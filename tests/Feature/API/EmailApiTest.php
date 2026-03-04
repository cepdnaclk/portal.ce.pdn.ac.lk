<?php

namespace Tests\Feature\Api;

use App\Domains\Email\Models\ApiKey;
use App\Domains\Email\Models\EmailDeliveryLog;
use App\Domains\Email\Models\PortalApp;
use App\Jobs\SendEmailJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EmailApiTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function send_requires_api_key()
  {
    $response = $this->postJson('/api/email/v1/send', [
      'to' => ['user@example.test'],
      'subject' => 'Test',
      'body' => 'Hello',
    ]);

    $response->assertStatus(401);
  }

  /** @test */
  public function send_queues_email_and_logs_delivery()
  {
    Queue::fake();

    $portalApp = PortalApp::create([
      'name' => 'System A',
      'status' => PortalApp::STATUS_ACTIVE,
    ]);

    [, $plainKey] = ApiKey::issue($portalApp);

    $payload = [
      'to' => ['user@example.test'],
      'cc' => ['cc@example.test'],
      'bcc' => ['bcc@example.test'],
      'reply_to' => 'reply@example.test',
      'subject' => 'Test',
      'body' => 'Hello',
      'metadata' => ['reference_id' => 'ABC-123'],
    ];

    $response = $this->postJson('/api/email/v1/send', $payload, [
      'X-API-KEY' => $plainKey,
    ]);

    $response->assertOk();
    $this->assertDatabaseCount('email_delivery_logs', 1);

    Queue::assertPushed(SendEmailJob::class);

    $log = EmailDeliveryLog::first();
    $this->assertSame(EmailDeliveryLog::STATUS_QUEUED, $log->status);
    $this->assertSame(config('email-service.default_from') ?: 'no-reply@portal.ce.pdn.ac.lk', $log->from);
    $this->assertSame($portalApp->id, $log->portal_app_id);
    $this->assertSame($payload['to'], $log->to);
    $this->assertSame($payload['cc'], $log->cc);
    $this->assertSame($payload['bcc'], $log->bcc);
    $this->assertSame($payload['subject'], $log->subject);
    $this->assertSame($payload['metadata'], $log->metadata);

    $response->assertJsonPath('message_id', $log->id);
    $response->assertJsonPath('status', EmailDeliveryLog::STATUS_QUEUED);
  }

  /** @test */
  public function send_rejects_revoked_api_key()
  {
    $portalApp = PortalApp::create([
      'name' => 'System A',
      'status' => PortalApp::STATUS_ACTIVE,
    ]);

    [$apiKey, $plainKey] = ApiKey::issue($portalApp);
    $apiKey->forceFill(['revoked_at' => now()])->save();

    $response = $this->postJson('/api/email/v1/send', [
      'to' => ['user@example.test'],
      'subject' => 'Test',
      'body' => 'Hello',
    ], [
      'X-API-KEY' => $plainKey,
    ]);

    $response->assertStatus(401);
    $response->assertJsonPath('message', 'Unauthorized. API key was expired or revoked.');
  }

  /** @test */
  public function send_rejects_over_recipient_limit()
  {
    config(['email-service.max_recipients' => 2]);

    $portalApp = PortalApp::create([
      'name' => 'System A',
      'status' => PortalApp::STATUS_ACTIVE,
    ]);

    [, $plainKey] = ApiKey::issue($portalApp);

    $response = $this->postJson('/api/email/v1/send', [
      'to' => ['user-a@example.test', 'user-b@example.test'],
      'cc' => ['user-c@example.test'],
      'subject' => 'Test',
      'body' => 'Hello',
    ], [
      'X-API-KEY' => $plainKey,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('to');
  }

  /** @test */
  public function history_isolated_to_portal_app()
  {
    $portalAppA = PortalApp::create([
      'name' => 'System A',
      'status' => PortalApp::STATUS_ACTIVE,
    ]);

    $portalAppB = PortalApp::create([
      'name' => 'System B',
      'status' => PortalApp::STATUS_ACTIVE,
    ]);

    [, $keyA] = ApiKey::issue($portalAppA);
    $keyB = ApiKey::issue($portalAppB)[1];

    EmailDeliveryLog::create([
      'portal_app_id' => $portalAppA->id,
      'api_key_id' => ApiKey::where('portal_app_id', $portalAppA->id)->first()->id,
      'from' => config('email-service.default_from') ?: 'no-reply@portal.ce.pdn.ac.lk',
      'to' => ['user-a@example.test'],
      'subject' => 'A',
      'status' => EmailDeliveryLog::STATUS_SENT,
      'sent_at' => now(),
    ]);

    EmailDeliveryLog::create([
      'portal_app_id' => $portalAppB->id,
      'api_key_id' => ApiKey::where('portal_app_id', $portalAppB->id)->first()->id,
      'from' => config('email-service.default_from') ?: 'no-reply@portal.ce.pdn.ac.lk',
      'to' => ['user-b@example.test'],
      'subject' => 'B',
      'status' => EmailDeliveryLog::STATUS_SENT,
      'sent_at' => now(),
    ]);

    $response = $this->getJson('/api/email/v1/history', [
      'X-API-KEY' => $keyA,
    ]);

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.subject', 'A');
    $response->assertJsonPath('pagination.total', 1);

    $responseB = $this->getJson('/api/email/v1/history', [
      'X-API-KEY' => $keyB,
    ]);

    $responseB->assertOk();
    $responseB->assertJsonCount(1, 'data');
    $responseB->assertJsonPath('data.0.subject', 'B');
    $responseB->assertJsonPath('pagination.total', 1);
  }

  /** @test */
  public function history_can_filter_by_status_and_limit()
  {
    $portalApp = PortalApp::create([
      'name' => 'System A',
      'status' => PortalApp::STATUS_ACTIVE,
    ]);

    [, $plainKey] = ApiKey::issue($portalApp);
    $apiKeyId = ApiKey::where('portal_app_id', $portalApp->id)->first()->id;

    EmailDeliveryLog::create([
      'portal_app_id' => $portalApp->id,
      'api_key_id' => $apiKeyId,
      'from' => config('email-service.default_from') ?: 'no-reply@portal.ce.pdn.ac.lk',
      'to' => ['user-a@example.test'],
      'subject' => 'Sent A',
      'status' => EmailDeliveryLog::STATUS_SENT,
      'sent_at' => now(),
    ]);

    EmailDeliveryLog::create([
      'portal_app_id' => $portalApp->id,
      'api_key_id' => $apiKeyId,
      'from' => config('email-service.default_from') ?: 'no-reply@portal.ce.pdn.ac.lk',
      'to' => ['user-b@example.test'],
      'subject' => 'Sent B',
      'status' => EmailDeliveryLog::STATUS_SENT,
      'sent_at' => now(),
    ]);

    EmailDeliveryLog::create([
      'portal_app_id' => $portalApp->id,
      'api_key_id' => $apiKeyId,
      'from' => config('email-service.default_from') ?: 'no-reply@portal.ce.pdn.ac.lk',
      'to' => ['user-c@example.test'],
      'subject' => 'Failed',
      'status' => EmailDeliveryLog::STATUS_FAILED,
    ]);

    $response = $this->getJson('/api/email/v1/history?status=sent&limit=1', [
      'X-API-KEY' => $plainKey,
    ]);

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.status', EmailDeliveryLog::STATUS_SENT);
    $response->assertJsonPath('pagination.total', 2);
    $response->assertJsonPath('pagination.limit', 1);
  }
}