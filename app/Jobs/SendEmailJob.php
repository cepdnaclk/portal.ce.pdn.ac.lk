<?php

namespace App\Jobs;

use App\Domains\Email\Models\EmailDeliveryLog;
use App\Mail\ApiEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Throwable;

class SendEmailJob implements ShouldQueue
{
  use Dispatchable;
  use InteractsWithQueue;
  use Queueable;
  use SerializesModels;

  protected string $logId;
  protected array $payload;

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(string $logId, array $payload)
  {
    $this->logId = $logId;
    $this->payload = $payload;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $log = EmailDeliveryLog::find($this->logId);
    if (!$log) {
      return;
    }

    $providerMessageId = null;

    try {
      $to = $this->payload['to'] ?? [];
      $cc = $this->payload['cc'] ?? [];
      $bcc = $this->payload['bcc'] ?? [];
      $replyTo = $this->payload['reply_to'] ?? null;
      $subject = $this->payload['subject'] ?? '';
      $body = $this->payload['body'] ?? null;
      $from = $this->payload['from'] ?? (config('email-service.default_from') ?: 'no-reply@portal.ce.pdn.ac.lk');

      // TODO allow markdown formatted body through 'body_markdown' field and use it if available instead of 'body'.
      // Only one option should be used at a time, to be validated by the Validation layer.

      Mail::to($to)
        ->send(new ApiEmail($body, $subject, $cc, $bcc, $replyTo), [], function ($message) use ($to, $replyTo, $subject, $from, &$providerMessageId) {
          $message->from($from)->subject($subject)->to($to);

          if (!empty($cc)) {
            $message->cc($cc);
          }

          if (!empty($bcc)) {
            $message->bcc($bcc);
          }

          if ($replyTo) {
            $message->replyTo($replyTo);
          }

          $providerMessageId = $message->getId();
        });

      $log->forceFill([
        'status' => EmailDeliveryLog::STATUS_SENT,
        'sent_at' => now(),
        'provider_message_id' => $providerMessageId,
      ])->save();
    } catch (Throwable $exception) {
      $log->forceFill([
        'status' => EmailDeliveryLog::STATUS_FAILED,
        'failure_reason' => $exception->getMessage(),
      ])->save();

      Log::error('Email send failed', [
        'message_id' => $log->id,
        'error' => $exception->getMessage(),
      ]);

      throw $exception;
    }
  }
}