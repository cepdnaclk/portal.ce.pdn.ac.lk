<?php

namespace Tests\Feature\Backend\TaxonomyList;

use App\Domains\Auth\Models\User;
use App\Domains\ContentManagement\Models\Article;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyFile;
use App\Domains\Taxonomy\Models\TaxonomyList;
use App\Domains\Taxonomy\Models\TaxonomyPage;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyListTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function admin_can_access_taxonomy_list_index_page(): void
  {
    $this->loginAsAdmin();

    $response = $this->get(route('dashboard.taxonomy-lists.index'));

    $response->assertOk();
  }

  /** @test */
  public function admin_can_access_taxonomy_list_creation_page(): void
  {
    $this->loginAsAdmin();

    $response = $this->get(route('dashboard.taxonomy-lists.create'));

    $response->assertOk();
  }

  /** @test */
  public function admin_can_access_taxonomy_list_edit_page(): void
  {
    $this->loginAsAdmin();
    $taxonomyList = TaxonomyList::factory()->create();

    $response = $this->get(route('dashboard.taxonomy-lists.edit', $taxonomyList));

    $response->assertOk();
  }

  /** @test */
  public function admin_can_access_taxonomy_list_manage_page(): void
  {
    $this->loginAsAdmin();
    $taxonomyList = TaxonomyList::factory()->create();

    $response = $this->get(route('dashboard.taxonomy-lists.manage', $taxonomyList));

    $response->assertOk();
  }

  /** @test */
  public function admin_can_view_taxonomy_list_page(): void
  {
    $this->loginAsAdmin();
    $taxonomy = Taxonomy::factory()->create(['name' => 'Sample Taxonomy']);
    $taxonomyList = TaxonomyList::factory()->create([
      'name' => 'Example List',
      'taxonomy_id' => $taxonomy->id,
      'data_type' => 'string',
      'items' => ['One', 'Two'],
    ]);

    $response = $this->get(route('dashboard.taxonomy-lists.view', $taxonomyList));

    $response->assertOk();
    $response->assertSee('Example List');
    $response->assertSee('Sample Taxonomy');
  }

  /** @test */
  public function admin_can_access_taxonomy_list_history_page(): void
  {
    $this->loginAsAdmin();
    $taxonomyList = TaxonomyList::factory()->create();

    $response = $this->get(route('dashboard.taxonomy-lists.history', $taxonomyList));

    $response->assertOk();
  }

  /** @test */
  public function admin_can_access_taxonomy_list_delete_confirmation_page(): void
  {
    $this->loginAsAdmin();
    $taxonomyList = TaxonomyList::factory()->create();

    $response = $this->get(route('dashboard.taxonomy-lists.delete', $taxonomyList));

    $response->assertOk();
  }

  /** @test */
  public function guest_cannot_access_taxonomy_list_routes(): void
  {
    $taxonomyList = TaxonomyList::factory()->create();

    $this->get(route('dashboard.taxonomy-lists.index'))
      ->assertRedirect('/login');

    $this->get(route('dashboard.taxonomy-lists.create'))
      ->assertRedirect('/login');

    $this->post(route('dashboard.taxonomy-lists.store'), [])
      ->assertRedirect('/login');

    $this->get(route('dashboard.taxonomy-lists.edit', $taxonomyList))
      ->assertRedirect('/login');

    $this->get(route('dashboard.taxonomy-lists.manage', $taxonomyList))
      ->assertRedirect('/login');

    $this->put(route('dashboard.taxonomy-lists.update', $taxonomyList), [])
      ->assertRedirect('/login');

    $this->put(route('dashboard.taxonomy-lists.update_list', $taxonomyList), [])
      ->assertRedirect('/login');

    $this->delete(route('dashboard.taxonomy-lists.destroy', $taxonomyList))
      ->assertRedirect('/login');
  }

  /** @test */
  public function non_admin_user_cannot_access_taxonomy_list_management_pages_and_actions(): void
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $taxonomyList = TaxonomyList::factory()->create();

    $this->get(route('dashboard.taxonomy-lists.index'))->assertStatus(302);
    $this->get(route('dashboard.taxonomy-lists.create'))->assertStatus(302);
    $this->post(route('dashboard.taxonomy-lists.store'), ['name' => 'Test', 'data_type' => 'string'])->assertStatus(302);
    $this->get(route('dashboard.taxonomy-lists.edit', $taxonomyList))->assertStatus(302);
    $this->get(route('dashboard.taxonomy-lists.manage', $taxonomyList))->assertStatus(302);
    $this->put(route('dashboard.taxonomy-lists.update', $taxonomyList), ['name' => 'Updated', 'data_type' => 'string'])->assertStatus(302);
    $this->put(route('dashboard.taxonomy-lists.update_list', $taxonomyList), ['items' => '[]'])->assertStatus(302);
    $this->delete(route('dashboard.taxonomy-lists.destroy', $taxonomyList))->assertStatus(302);
  }

  /** @test */
  public function admin_can_create_taxonomy_list(): void
  {
    $admin = $this->loginAsAdmin();
    $taxonomy = Taxonomy::factory()->create();

    $response = $this->post(route('dashboard.taxonomy-lists.store'), [
      'name' => 'Resources',
      'taxonomy_id' => $taxonomy->id,
      'data_type' => 'string',
    ]);

    $response->assertStatus(302);

    $taxonomyList = TaxonomyList::where('name', 'Resources')->first();
    $this->assertNotNull($taxonomyList);
    $response->assertRedirect(route('dashboard.taxonomy-lists.manage', $taxonomyList));

    $this->assertDatabaseHas('taxonomy_lists', [
      'name' => 'Resources',
      'taxonomy_id' => $taxonomy->id,
      'data_type' => 'string',
      'created_by' => $admin->id,
    ]);

    $this->assertSame([], $taxonomyList->items);
  }

  /** @test */
  public function taxonomy_list_store_validation_rules_are_enforced(): void
  {
    $this->loginAsAdmin();

    $response = $this->post(route('dashboard.taxonomy-lists.store'), []);
    $response->assertSessionHasErrors(['name', 'data_type']);

    TaxonomyList::factory()->create(['name' => 'Duplicated']);

    $response = $this->post(route('dashboard.taxonomy-lists.store'), [
      'name' => 'Duplicated',
      'data_type' => 'invalid-type',
    ]);
    $response->assertSessionHasErrors(['name', 'data_type']);

    $response = $this->post(route('dashboard.taxonomy-lists.store'), [
      'name' => 'Unique Name',
      'data_type' => 'string',
    ]);
    $response->assertSessionDoesntHaveErrors();
  }

  /** @test */
  public function admin_can_update_taxonomy_list_metadata(): void
  {
    $admin = $this->loginAsAdmin();
    $taxonomy = Taxonomy::factory()->create();
    $taxonomyList = TaxonomyList::factory()->create([
      'name' => 'Original Name',
      'taxonomy_id' => null,
      'data_type' => 'string',
    ]);

    $response = $this->put(route('dashboard.taxonomy-lists.update', $taxonomyList), [
      'name' => 'Updated Name',
      'taxonomy_id' => $taxonomy->id,
      'data_type' => 'email',
    ]);

    $response->assertRedirect(route('dashboard.taxonomy-lists.index'));

    $taxonomyList->refresh();
    $this->assertEquals('Updated Name', $taxonomyList->name);
    $this->assertEquals($taxonomy->id, $taxonomyList->taxonomy_id);
    $this->assertEquals('email', $taxonomyList->data_type);
    $this->assertEquals($admin->id, $taxonomyList->updated_by);
  }

  /** @test */
  public function cannot_change_data_type_when_items_exist(): void
  {
    $this->loginAsAdmin();
    $taxonomyList = TaxonomyList::factory()->create([
      'data_type' => 'string',
      'items' => ['First item'],
    ]);

    $response = $this->from(route('dashboard.taxonomy-lists.edit', $taxonomyList))
      ->put(route('dashboard.taxonomy-lists.update', $taxonomyList), [
        'name' => $taxonomyList->name,
        'taxonomy_id' => $taxonomyList->taxonomy_id,
        'data_type' => 'email',
      ]);

    $response->assertSessionHasErrors(['data_type']);

    $taxonomyList->refresh();
    $this->assertEquals('string', $taxonomyList->data_type);
  }

  /** @test */
  public function update_list_rejects_invalid_payloads_per_data_type(): void
  {
    $this->loginAsAdmin();

    $invalidCases = [
      'string' => ['items' => ['']],
      'date' => ['items' => ['not-a-date']],
      'url' => ['items' => ['not-a-url']],
      'email' => ['items' => ['not-an-email']],
      'file' => ['items' => [9999]],
      'page' => ['items' => [8888]],
    ];

    foreach ($invalidCases as $dataType => $case) {
      $taxonomyList = TaxonomyList::factory()->create([
        'data_type' => $dataType,
        'items' => [],
      ]);

      $response = $this->from(route('dashboard.taxonomy-lists.manage', $taxonomyList))
        ->put(route('dashboard.taxonomy-lists.update_list', $taxonomyList), [
          'items' => json_encode($case['items']),
        ]);

      $response->assertSessionHasErrors(['items'], sprintf('Failed asserting invalidation for [%s] data type.', $dataType));

      $taxonomyList->refresh();
      $this->assertSame([], $taxonomyList->items);
    }
  }

  /** @test */
  public function update_list_accepts_valid_items_and_normalises_them(): void
  {
    $admin = $this->loginAsAdmin();

    $taxonomy = Taxonomy::factory()->create();
    $file = TaxonomyFile::factory()->create(['taxonomy_id' => $taxonomy->id]);
    $page = TaxonomyPage::factory()->create(['taxonomy_id' => $taxonomy->id]);
    $tenant = Tenant::factory()->create();
    $article = Article::factory()->create(['tenant_id' => $tenant->id]);

    $validCases = [
      'string' => [
        'items' => ['  value '],
        'expected' => ['value'],
      ],
      'date' => [
        'items' => ['2024-06-01'],
        'expected' => ['2024-06-01'],
      ],
      'url' => [
        'items' => ['https://example.com/resource'],
        'expected' => ['https://example.com/resource'],
      ],
      'email' => [
        'items' => ['USER@example.com'],
        'expected' => ['user@example.com'],
      ],
      'file' => [
        'items' => [$file->id],
        'expected' => [$file->id],
      ],
      'page' => [
        'items' => [$page->id],
        'expected' => [$page->id],
      ],
      'article' => [
        'items' => [$article->id],
        'expected' => [$article->id],
      ],
    ];

    foreach ($validCases as $dataType => $case) {
      $taxonomyList = TaxonomyList::factory()->create([
        'data_type' => $dataType,
        'taxonomy_id' => $taxonomy->id,
        'items' => [],
      ]);

      $response = $this->put(route('dashboard.taxonomy-lists.update_list', $taxonomyList), [
        'items' => json_encode($case['items']),
      ]);

      $response->assertRedirect(route('dashboard.taxonomy-lists.index'));

      $taxonomyList->refresh();
      $this->assertSame($case['expected'], $taxonomyList->items, sprintf('Failed asserting normalisation for [%s] data type.', $dataType));
      $this->assertEquals($admin->id, $taxonomyList->updated_by);
    }
  }

  /** @test */
  public function update_list_rejects_malformed_json_payload(): void
  {
    $this->loginAsAdmin();
    $taxonomyList = TaxonomyList::factory()->create(['data_type' => 'string']);

    $response = $this->from(route('dashboard.taxonomy-lists.manage', $taxonomyList))
      ->put(route('dashboard.taxonomy-lists.update_list', $taxonomyList), [
        'items' => '{"not": "json"',
      ]);

    $response->assertSessionHasErrors(['items']);
  }

  /** @test */
  public function admin_can_delete_taxonomy_list(): void
  {
    $this->loginAsAdmin();
    $taxonomyList = TaxonomyList::factory()->create();

    $response = $this->delete(route('dashboard.taxonomy-lists.destroy', $taxonomyList));

    $response->assertRedirect(route('dashboard.taxonomy-lists.index'));
    $this->assertDatabaseMissing('taxonomy_lists', ['id' => $taxonomyList->id]);
  }
}
