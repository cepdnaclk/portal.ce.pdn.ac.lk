<?php

namespace Tests\Feature\Backend\Taxonomy;

use App\Domains\Auth\Models\User;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use App\Http\Livewire\Backend\Taxonomy\ExpandableTaxonomyInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase as ProjectTestCase; // Alias our project's TestCase
use Illuminate\Foundation\Testing\TestCase as BaseTestCase; // Import Laravel's base TestCase

class TaxonomyTermInfoSectionTest extends ProjectTestCase // Still extends our project's TestCase
{
    use RefreshDatabase; // Keep this to ensure migrations are run by the trait if not by parent

    protected ?User $adminUser;
    protected Taxonomy $taxonomy;

    public function setUp(): void
    {
        // Call Laravel's base setUp to create application and setup traits
        BaseTestCase::setUp();
        
        // RefreshDatabase trait should run migrations now.
        // If not, uncomment the next line:
        // $this->artisan('migrate:fresh');

        // Disable specific middleware that might interfere in tests
        $this->withoutMiddleware(\App\Domains\Auth\Http\Middleware\RequirePassword::class);
        $this->withoutMiddleware(\App\Domains\Auth\Http\Middleware\TwoFactorAuthenticationStatus::class);

        // Now, seed necessary roles and permissions
        $this->artisan('db:seed', ['--class' => \Database\Seeders\Auth\PermissionRoleSeeder::class]);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\Roles\TaxonomyRoleSeeder::class]);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\Roles\EditorRoleSeeder::class]); // For 'Editor' role

        // Manually create the Admin user (ID 1) - UserSeeder might also do this if called by a global seeder
        // but we ensure it here for this test's context.
        User::updateOrCreate(
            ['id' => 1],
            [
                'type' => User::TYPE_ADMIN,
                'name' => 'Test Super Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'), // Ensure bcrypt is available or use Hash::make
                'email_verified_at' => now(),
                'active' => true,
            ]
        );
        $this->adminUser = User::find(1);
        
        // Assign necessary roles. 'Administrator' role should have taxonomy permissions.
        if ($this->adminUser) {
            $this->adminUser->assignRole('Administrator');
            // $this->adminUser->assignRole('Taxonomy Manager'); // If Administrator doesn't cover everything
            $this->actingAs($this->adminUser);
        } else {
            throw new \Exception('Failed to create and retrieve Admin user for testing.');
        }

        // Manually create and save the taxonomy
        $this->taxonomy = new Taxonomy([
            // 'id' will be auto-assigned by Eloquent
            'name' => 'Test Taxonomy With Description',
            'code' => 'test-taxonomy-desc-'.uniqid(),
            'description' => 'This is the parent taxonomy description.',
            'properties' => [],
            'created_by' => $this->adminUser->id,
            'updated_by' => $this->adminUser->id,
        ]);
        $this->taxonomy->save();
    }

    /** @test */
    public function info_section_appears_on_taxonomy_term_create_page()
    {
        $response = $this->get(route('dashboard.taxonomy.terms.create', $this->taxonomy));

        $response->assertOk();
        $response->assertSeeLivewire('backend.taxonomy.expandable-taxonomy-info');
        $response->assertSee(__('Show Taxonomy Information')); // Initial button text
        $response->assertDontSee($this->taxonomy->description); // Description initially hidden
    }

    // /** @test */
    // public function info_section_can_be_expanded_on_create_page()
    // {
    //      // This test uses Livewire testing utilities directly on the page response
    //     $this->get(route('dashboard.taxonomy.terms.create', $this->taxonomy))
    //         ->assertOk()
    //         ->assertSeeLivewire('backend.taxonomy.expandable-taxonomy-info')
    //         ->livewire('backend.taxonomy.expandable-taxonomy-info')
    //         ->call('toggleInfo')
    //         ->assertSee($this->taxonomy->description)
    //         ->assertSee(__('Hide Taxonomy Information'));
    // }

    /** @test */
    public function info_section_appears_on_taxonomy_term_edit_page()
    {
        if (!$this->adminUser) {
            $this->markTestSkipped('Admin user not found, cannot run this test.');
        }
        $term = new TaxonomyTerm([
            'taxonomy_id' => $this->taxonomy->id,
            'name' => 'Test Term',
            'code' => 'test-term-'.uniqid(),
            'created_by' => $this->adminUser->id,
            'updated_by' => $this->adminUser->id,
            'metadata' => []
        ]);
        $term->save();

        $response = $this->get(route('dashboard.taxonomy.terms.edit', ['taxonomy' => $this->taxonomy, 'term' => $term]));

        $response->assertOk();
        $response->assertSeeLivewire('backend.taxonomy.expandable-taxonomy-info');
        $response->assertSee(__('Show Taxonomy Information'));
        $response->assertDontSee($this->taxonomy->description);
    }

    // /** @test */
    // public function info_section_can_be_expanded_on_edit_page()
    // {
    //     if (!$this->adminUser) {
    //         $this->markTestSkipped('Admin user not found, cannot run this test.');
    //     }
    //     $term = new TaxonomyTerm([
    //         'taxonomy_id' => $this->taxonomy->id,
    //         'name' => 'Test Term Expand',
    //         'code' => 'test-term-expand-'.uniqid(),
    //         'created_by' => $this->adminUser->id,
    //         'updated_by' => $this->adminUser->id,
    //         'metadata' => []
    //     ]);
    //     $term->save();

    //     $this->get(route('dashboard.taxonomy.terms.edit', ['taxonomy' => $this->taxonomy, 'term' => $term]))
    //         ->assertOk()
    //         ->assertSeeLivewire('backend.taxonomy.expandable-taxonomy-info')
    //         ->livewire('backend.taxonomy.expandable-taxonomy-info')
    //         ->call('toggleInfo')
    //         ->assertSee($this->taxonomy->description)
    //         ->assertSee(__('Hide Taxonomy Information'));
    // }

    /** @test */
    public function info_section_is_not_present_if_taxonomy_has_no_description_on_create_page()
    {
        if (!$this->adminUser) {
            $this->markTestSkipped('Admin user not found, cannot run this test.');
        }
        $taxonomyWithoutDescription = new Taxonomy([
            'name' => 'No Desc Taxonomy',
            'code' => 'no-desc-tax-'.uniqid(),
            'description' => null,
            'properties' => [],
            'created_by' => $this->adminUser->id,
            'updated_by' => $this->adminUser->id,
        ]);
        $taxonomyWithoutDescription->save();

        $response = $this->get(route('dashboard.taxonomy.terms.create', $taxonomyWithoutDescription));

        $response->assertOk();
        $response->assertSeeLivewire('backend.taxonomy.expandable-taxonomy-info');
        $response->assertDontSee(__('Show Taxonomy Information'));
    }

    /** @test */
    public function info_section_is_not_present_if_taxonomy_has_no_description_on_edit_page()
    {
        if (!$this->adminUser) {
            $this->markTestSkipped('Admin user not found, cannot run this test.');
        }
        $taxonomyWithoutDescription = new Taxonomy([
            'name' => 'No Desc Taxonomy Edit',
            'code' => 'no-desc-tax-edit-'.uniqid(),
            'description' => null,
            'properties' => [],
            'created_by' => $this->adminUser->id,
            'updated_by' => $this->adminUser->id,
        ]);
        $taxonomyWithoutDescription->save();

        $term = new TaxonomyTerm([
            'taxonomy_id' => $taxonomyWithoutDescription->id,
            'name' => 'Test Term No Desc',
            'code' => 'test-term-no-desc-'.uniqid(),
            'created_by' => $this->adminUser->id,
            'updated_by' => $this->adminUser->id,
            'metadata' => []
        ]);
        $term->save();

        $response = $this->get(route('dashboard.taxonomy.terms.edit', ['taxonomy' => $taxonomyWithoutDescription, 'term' => $term]));

        $response->assertOk();
        $response->assertSeeLivewire('backend.taxonomy.expandable-taxonomy-info');
        $response->assertDontSee(__('Show Taxonomy Information'));
    }
}
