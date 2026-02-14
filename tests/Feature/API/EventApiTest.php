<?php

namespace Tests\Feature\Api;

use App\Domains\Auth\Models\User;
use App\Domains\ContentManagement\Models\Event;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventApiTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function v1_events_include_author_payload()
  {
    Event::query()->delete();

    $defaultTenant = Tenant::default() ?? Tenant::factory()->create([
      'slug' => config('tenants.default'),
      'url' => 'https://' . config('tenants.default'),
      'is_default' => true,
    ]);

    $author = User::factory()->create([
      'name' => 'Event Author',
      'email' => 'event.author@example.test',
    ]);

    Event::factory()->enabled()->create([
      'tenant_id' => $defaultTenant->id,
      'author_id' => $author->id,
      'created_by' => $author->id,
    ]);

    $response = $this->getJson('/api/events/v1');

    $response->assertOk();
    $response->assertJsonPath('data.0.author.name', 'Event Author');
    $response->assertJsonPath('data.0.author.email', 'event.author@example.test');
    $response->assertJsonPath('data.0.author.profile_url', '#');
  }
}