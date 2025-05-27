<?php

namespace Tests\Feature\Backend\Taxonomy;

use App\Domains\Auth\Models\User;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm; // Added as per instruction, though might be used later
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
        $response = $this->get(route('dashboard.taxonomy.show', $taxonomy));
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

        // Test properties is valid JSON (sending malformed JSON string)
        // The validation rule is 'string', so a malformed string might pass this,
        // but the controller's json_decode would fail.
        // However, if 'json' rule is added to validation, this would be caught by validator.
        // For now, let's assume 'string' validation means it should be a string.
        // The factory produces valid JSON. If we send a non-JSON string, it should be fine by 'string' rule.
        // If we send a malformed JSON string, it's still a string.
        // The actual test for "valid JSON" would typically be if 'json' validation rule is present.
        // Let's test if a non-string still fails as per previous test, and a valid JSON string passes.
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
        // Depending on how strict the 'string' validation is, and if there's a 'json' rule,
        // this might or might not have an error for 'properties'.
        // If only 'string', it's a valid string. If 'json' rule, it would fail.
        // Given the subtask, we are testing 'properties' => 'string'.
        // A malformed JSON is still a string. So no validation error here for 'properties'.
        // The error would likely occur at the controller's json_decode stage, not validation.
        // So, we assert no validation error for 'properties' for this specific test.
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

        // Test code is unique (ignoring self)
        $otherTaxonomy = Taxonomy::factory()->create(['code' => 'other_code']);
        $response = $this->put(route('dashboard.taxonomy.update', $taxonomy), [
            'code' => 'other_code',
            'name' => 'Updated Name for Other Code'
        ]);
        $response->assertSessionHasErrors(['code']);

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
        ];

        $response = $this->post(route('dashboard.taxonomy.store'), $data);

        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard.taxonomy.index'));

        $this->assertDatabaseHas('taxonomies', [
            'code' => 'new_taxonomy_code',
            'name' => 'New Taxonomy Name',
            'description' => 'A detailed description for the new taxonomy.',
            'created_by' => $adminUser->id,
            // Properties are checked separately below due to JSON nature
        ]);

        $createdTaxonomy = Taxonomy::where('code', 'new_taxonomy_code')->first();
        $this->assertNotNull($createdTaxonomy);
        $this->assertEquals($propertiesArray, json_decode($createdTaxonomy->properties, true));
    }

    /** @test */
    public function test_admin_can_view_taxonomy()
    {
        $this->loginAsAdmin();

        $taxonomy = Taxonomy::factory()->create(); // Factory creates properties

        $response = $this->get(route('dashboard.taxonomy.show', $taxonomy));

        $response->assertStatus(200);
        $response->assertSee($taxonomy->name);
        $response->assertSee($taxonomy->code);

        // Assert that a property name is visible
        // The factory default properties are:
        // json_encode([
        //     ['code' => 'property_code_1', 'name' => 'Property Name 1', 'data_type' => 'string', 'required' => true],
        //     ['code' => 'property_code_2', 'name' => 'Property Name 2', 'data_type' => 'integer', 'required' => false],
        // ])
        // So, we expect to see "Property Name 1" or "Property Name 2" if the view renders them.
        $properties = json_decode($taxonomy->properties, true);
        if (!empty($properties) && isset($properties[0]['name'])) {
            $response->assertSee($properties[0]['name']);
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
        $this->assertEquals($updatedPropertiesArray, json_decode($taxonomy->properties, true));
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
        $response->assertSessionHas('success', 'Taxonomy was deleted successfully.'); // Or whatever the actual message is
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
        // The exact error message might vary based on implementation
        // $response->assertSessionHas('error', 'Cannot delete taxonomy with associated terms.');
        // For now, just checking if an error key exists, as the message can be configured.
        $response->assertSessionHas('error');
    }

    /** @test */
    public function test_guest_cannot_access_taxonomy_routes()
    {
        // Create page
        $response = $this->get(route('dashboard.taxonomy.create'));
        $response->assertStatus(302)->assertRedirect(route('login'));

        // Index page
        $response = $this->get(route('dashboard.taxonomy.index'));
        $response->assertStatus(302)->assertRedirect(route('login'));

        // Store action
        $response = $this->post(route('dashboard.taxonomy.store'), ['code' => 'test', 'name' => 'Test']);
        $response->assertStatus(302)->assertRedirect(route('login'));
    }

    /** @test */
    public function test_non_admin_user_cannot_access_taxonomy_management_pages_and_actions()
    {
        $user = User::factory()->create(); // This user is not an admin by default
        $this->actingAs($user);

        // Create page
        $response = $this->get(route('dashboard.taxonomy.create'));
        $response->assertStatus(403);

        // Index page
        $response = $this->get(route('dashboard.taxonomy.index'));
        $response->assertStatus(403);

        // Store action
        $response = $this->post(route('dashboard.taxonomy.store'), ['code' => 'test', 'name' => 'Test']);
        $response->assertStatus(403);

        // Actions requiring an existing Taxonomy
        $taxonomy = Taxonomy::factory()->create();

        // Edit page
        $response = $this->get(route('dashboard.taxonomy.edit', $taxonomy));
        $response->assertStatus(403);

        // Show page
        $response = $this->get(route('dashboard.taxonomy.show', $taxonomy));
        $response->assertStatus(403);

        // Update action
        $response = $this->put(route('dashboard.taxonomy.update', $taxonomy), ['code' => 'updated', 'name' => 'Updated']);
        $response->assertStatus(403);

        // Delete confirmation page
        $response = $this->get(route('dashboard.taxonomy.delete', $taxonomy));
        $response->assertStatus(403);

        // Destroy action
        $response = $this->delete(route('dashboard.taxonomy.destroy', $taxonomy));
        $response->assertStatus(403);
    }
}
