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

    $response = $this->postJson('/api/email/v1/send', [
      'to' => ['user@example.test'],
      'subject' => 'Test',
      'body' => 'Hello',
      'metadata' => ['reference_id' => 'ABC-123'],
    ], [
      'X-API-KEY' => $plainKey,
    ]);

    $response->assertOk();
    $this->assertDatabaseCount('email_delivery_logs', 1);

    Queue::assertPushed(SendEmailJob::class);

    $log = EmailDeliveryLog::first();
    $this->assertSame('queued', $log->status);
    $this->assertSame(config('email-service.default_from') ?: 'no-reply@portal.ce.pdn.ac.lk', $log->from);
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

    $responseB = $this->getJson('/api/email/v1/history', [
      'X-API-KEY' => $keyB,
    ]);

    $responseB->assertOk();
    $responseB->assertJsonCount(1, 'data');
    $responseB->assertJsonPath('data.0.subject', 'B');
  }
}
