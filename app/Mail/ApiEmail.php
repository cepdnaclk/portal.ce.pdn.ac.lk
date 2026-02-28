<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApiEmail extends Mailable
{
  use Queueable, SerializesModels;

  protected $body_text;
  protected $cc_addresses;
  protected $bcc_addresses;
  protected $reply_to_address;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($body_text, $subject = '', $cc = [], $bcc = [], $replyTo = null)
  {
    $this->body_text = $body_text;
    $this->cc_addresses = $cc;
    $this->bcc_addresses = $bcc;
    $this->reply_to_address = $replyTo;
    $this->subject($subject);
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $message = $this->markdown(
      config('email-service.default_template', 'emails.default'),
      ['body' => $this->body_text, 'subject' => $this->subject, 'support_email' => config('email-service.support_email')]
    )
      ->subject($this->subject);

    if (!empty($this->cc_addresses)) {
      $message->cc($this->cc_addresses);
    }

    if (!empty($this->bcc_addresses)) {
      $message->bcc($this->bcc_addresses);
    }

    if ($this->reply_to_address) {
      $message->replyTo($this->reply_to_address);
    }

    return $message;
  }
}