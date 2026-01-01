<?php

namespace Tests\Feature\Backend\Taxonomy;

use App\Domains\Auth\Models\User;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function test_admin_can_access_taxonomy_listing_page()
  {
    $this->loginAsAdmin();
    $response = $this->get(route('dashboard.taxonomy.index'));
    $response->assertOk();
  }

  /** @test */
  public function test_admin_can_access_taxonomy_creation_page()
  {
    $this->loginAsAdmin();
    $response = $this->get(route('dashboard.taxonomy.create'));
    $response->assertOk();
  }

  /** @test */
  public function test_admin_can_access_taxonomy_edit_page()
  {
    $this->loginAsAdmin();
    $taxonomy = Taxonomy::factory()->create();
    $response = $this->get(route('dashboard.taxonomy.edit', $taxonomy));
    $response->assertOk();
  }

  /** @test */
  public function test_admin_can_access_taxonomy_view_page()
  {
    $this->loginAsAdmin();
    $taxonomy = Taxonomy::factory()->create();
    $response = $this->get(route('dashboard.taxonomy.view', $taxonomy));
    $response->assertOk();
  }

  /** @test */
  public function test_admin_can_access_taxonomy_delete_confirmation_page()
  {
    $this->loginAsAdmin();
    $taxonomy = Taxonomy::factory()->create();
    // Assuming there's a specific route for delete confirmation,
    // often it's part of the edit view or a dedicated GET route.
    // If it's a delete form on view/edit page, this might need adjustment
    // based on actual routes. For now, let's assume a common pattern.
    // The provided routes list `taxonomy.delete` which is likely the confirmation.
    $response = $this->get(route('dashboard.taxonomy.delete', $taxonomy));
    $response->assertOk();
  }

  /** @test */
  public function test_taxonomy_store_validation()
  {
    $this->loginAsAdmin();

    // Test code is required
    $response = $this->post(route('dashboard.taxonomy.store'), ['name' => 'Test Name']);
    $response->assertSessionHasErrors(['code']);

    // Test name is required
    $response = $this->post(route('dashboard.taxonomy.store'), ['code' => 'test_code']);
    $response->assertSessionHasErrors(['name']);

    // Test code is unique
    Taxonomy::factory()->create(['code' => 'existing_code']);
    $response = $this->post(route('dashboard.taxonomy.store'), ['code' => 'existing_code', 'name' => 'Another Name']);
    $response->assertSessionHasErrors(['code']);

    // Test properties is a string (sending an array)
    $response = $this->post(route('dashboard.taxonomy.store'), [
      'code' => 'prop_test_array',
      'name' => 'Properties Test Array',
      'properties' => ['key' => 'value'] // Sending array directly
    ]);
    $response->assertSessionHasErrors(['properties']);

    $response = $this->post(route('dashboard.taxonomy.store'), [
      'code' => 'prop_test_json_string',
      'name' => 'Properties Test JSON String',
      'properties' => '{"key": "value", "another_key": "another_value"}' // Valid JSON string
    ]);
    $response->assertSessionDoesntHaveErrors(['properties']);


    $response = $this->post(route('dashboard.taxonomy.store'), [
      'code' => 'prop_test_malformed_json',
      'name' => 'Properties Test Malformed JSON',
      'properties' => '{"key": "value"' // Malformed JSON string
    ]);
    $response->assertSessionDoesntHaveErrors(['properties']);
  }

  /** @test */
  public function test_taxonomy_update_validation()
  {
    $this->loginAsAdmin();
    $taxonomy = Taxonomy::factory()->create();

    // Test code is required
    $response = $this->put(route('dashboard.taxonomy.update', $taxonomy), ['name' => 'Updated Name']);
    $response->assertSessionHasErrors(['code']);

    // Test name is required
    $response = $this->put(route('dashboard.taxonomy.update', $taxonomy), ['code' => 'updated_code']);
    $response->assertSessionHasErrors(['name']);

    // Test properties is a string (sending an array)
    $response = $this->put(route('dashboard.taxonomy.update', $taxonomy), [
      'code' => $taxonomy->code,
      'name' => $taxonomy->name,
      'properties' => ['key' => 'value'] // Sending array directly
    ]);
    $response->assertSessionHasErrors(['properties']);

    // Test properties is valid JSON (sending malformed JSON string) - see notes in store validation
    $response = $this->put(route('dashboard.taxonomy.update', $taxonomy), [
      'code' => $taxonomy->code,
      'name' => $taxonomy->name,
      'properties' => '{"key": "value"' // Malformed JSON string
    ]);
    // As per store validation, if only 'string' rule, this should not cause validation error.
    $response->assertSessionDoesntHaveErrors(['properties']);

    // Test with valid JSON string for properties
    $response = $this->put(route('dashboard.taxonomy.update', $taxonomy), [
      'code' => $taxonomy->code,
      'name' => $taxonomy->name,
      'properties' => '{"key": "new_value"}'
    ]);
    $response->assertSessionDoesntHaveErrors(['properties']);
  }

  /** @test */
  public function test_admin_can_create_taxonomy()
  {
    $this->loginAsAdmin();
    $adminUser = auth()->user();

    $propertiesArray = [
      ['code' => 'test_prop_code_1', 'name' => 'Test Property Name 1', 'data_type' => 'string', 'required' => true],
      ['code' => 'test_prop_code_2', 'name' => 'Test Property Name 2', 'data_type' => 'integer', 'required' => false],
    ];

    $data = [
      'code' => 'new_taxonomy_code',
      'name' => 'New Taxonomy Name',
      'description' => 'A detailed description for the new taxonomy.',
      'properties' => json_encode($propertiesArray),
      'visibility' => true,
    ];

    $response = $this->post(route('dashboard.taxonomy.store'), $data);

    $response->assertStatus(302);
    $response->assertRedirect(route('dashboard.taxonomy.index'));

    $this->assertDatabaseHas('taxonomies', [
      'code' => 'new_taxonomy_code',
      'name' => 'New Taxonomy Name',
      'description' => 'A detailed description for the new taxonomy.',
      'created_by' => $adminUser->id,
      'visibility' => true,
    ]);

    $createdTaxonomy = Taxonomy::where('code', 'new_taxonomy_code')->first();
    $this->assertNotNull($createdTaxonomy);
    $this->assertEquals($propertiesArray, $createdTaxonomy->properties, true);
    $this->assertTrue($createdTaxonomy->visibility);
  }

  /** @test */
  public function test_admin_can_view_taxonomy()
  {
    $this->loginAsAdmin();

    $taxonomy = Taxonomy::factory()->create();
    $taxonomyTerm = TaxonomyTerm::factory()->create(['taxonomy_id' => $taxonomy->id]);

    $response = $this->get(route('dashboard.taxonomy.view', $taxonomy));

    $response->assertStatus(200);
    $response->assertSee($taxonomy->name);
    $response->assertSee($taxonomy->code);

    // Assert that a property name is visible under terms
    $taxonomy_terms = $taxonomy->terms;
    if (!empty($taxonomy_terms) && isset($taxonomy_terms[0]['name'])) {
      $response->assertSee($taxonomy_terms[0]['name']);
    }
  }

  /** @test */
  public function test_admin_can_update_taxonomy()
  {
    $this->loginAsAdmin();
    $adminUser = auth()->user();

    $taxonomy = Taxonomy::factory()->create();

    $updatedPropertiesArray = [
      ['code' => 'updated_prop_code', 'name' => 'Updated Property Name', 'data_type' => 'boolean', 'required' => false],
    ];

    $updateData = [
      'code' => $taxonomy->code, // Keep original code, or update to a new unique one if desired
      'name' => 'Updated Taxonomy Name',
      'description' => 'An updated description for the taxonomy.',
      'properties' => json_encode($updatedPropertiesArray),
    ];

    $response = $this->put(route('dashboard.taxonomy.update', $taxonomy), $updateData);

    $response->assertStatus(302);
    $response->assertRedirect(route('dashboard.taxonomy.index'));

    $taxonomy->refresh();

    $this->assertEquals('Updated Taxonomy Name', $taxonomy->name);
    $this->assertEquals('An updated description for the taxonomy.', $taxonomy->description);
    $this->assertEquals($updatedPropertiesArray, $taxonomy->properties);
    $this->assertFalse($taxonomy->visibility);
    $this->assertEquals($adminUser->id, $taxonomy->updated_by);
  }

  /** @test */
  public function test_admin_can_delete_taxonomy_without_terms()
  {
    $this->loginAsAdmin();
    $taxonomy = Taxonomy::factory()->create();

    $response = $this->delete(route('dashboard.taxonomy.destroy', $taxonomy));

    $response->assertStatus(302);
    $response->assertRedirect(route('dashboard.taxonomy.index'));
    $this->assertDatabaseMissing('taxonomies', ['id' => $taxonomy->id]);
  }

  /** @test */
  public function test_admin_cannot_delete_taxonomy_with_terms()
  {
    $this->loginAsAdmin();
    $taxonomy = Taxonomy::factory()->create();
    TaxonomyTerm::factory()->create(['taxonomy_id' => $taxonomy->id]);

    $response = $this->delete(route('dashboard.taxonomy.destroy', $taxonomy));

    $response->assertStatus(302); // Should redirect, perhaps back to index or previous page
    $this->assertDatabaseHas('taxonomies', ['id' => $taxonomy->id]);
  }

  /** @test */
  public function test_guest_cannot_access_taxonomy_routes()
  {
    // Create page
    $response = $this->get(route('dashboard.taxonomy.create'));
    $response->assertStatus(302)->assertRedirect('/login');

    // Index page
    $response = $this->get(route('dashboard.taxonomy.index'));
    $response->assertStatus(302)->assertRedirect('/login');

    // Store action
    $response = $this->post(route('dashboard.taxonomy.store'), ['code' => 'test', 'name' => 'Test']);
    $response->assertStatus(302)->assertRedirect('/login');
  }

  /** @test */
  public function test_non_admin_user_cannot_access_taxonomy_management_pages_and_actions()
  {
    $user = User::factory()->create(); // This user is not an admin by default
    $this->actingAs($user);

    // Create page
    $response = $this->get(route('dashboard.taxonomy.create'));
    $response->assertStatus(302);
    $response->assertSessionHas('flash_danger', __('You do not have access to do that.'));

    // Index page
    $response = $this->get(route('dashboard.taxonomy.index'));
    $response->assertStatus(302);

    // Store action
    $response = $this->post(route('dashboard.taxonomy.store'), ['code' => 'test', 'name' => 'Test']);
    $response->assertStatus(302);

    // Actions requiring an existing Taxonomy
    $taxonomy = Taxonomy::factory()->create();

    // Edit page
    $response = $this->get(route('dashboard.taxonomy.edit', $taxonomy));
    $response->assertStatus(302);

    // Show page
    $response = $this->get(route('dashboard.taxonomy.view', $taxonomy));
    $response->assertStatus(302);

    // Update action
    $response = $this->put(route('dashboard.taxonomy.update', $taxonomy), ['code' => 'updated', 'name' => 'Updated']);
    $response->assertStatus(302);

    // Delete confirmation page
    $response = $this->get(route('dashboard.taxonomy.delete', $taxonomy));
    $response->assertStatus(302);

    // Destroy action
    $response = $this->delete(route('dashboard.taxonomy.destroy', $taxonomy));
    $response->assertStatus(302);
  }
}