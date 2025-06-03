<?php

namespace Tests\Feature\Backend\Taxonomy;

use App\Domains\Auth\Models\User;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use App\Http\Livewire\Backend\Taxonomy\ExpandableTaxonomyInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase as ProjectTestCase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class TaxonomyTermInfoSectionTest extends ProjectTestCase
{
    use RefreshDatabase;

    protected ?User $adminUser;
    protected Taxonomy $taxonomy;

    public function setUp(): void
    {
        BaseTestCase::setUp();

        // Manually create and save the taxonomy
        $this->taxonomy = new Taxonomy([
            'name' => 'Test Taxonomy With Description',
            'code' => 'test-taxonomy-desc-' . uniqid(),
            'description' => 'This is the parent taxonomy description.',
            'properties' => [],
            'created_by' => $this->adminUser->id,
            'updated_by' => $this->adminUser->id,
        ]);
        $this->taxonomy->save();

        $this->actingAs($user = User::factory()->admin()->create());
    }

    /** @test */
    public function info_section_appears_on_taxonomy_term_create_page()
    {
        $response = $this->get(route('dashboard.taxonomy.terms.create', $this->taxonomy));

        $response->assertOk();
        $response->assertSeeLivewire('backend.taxonomy.expandable-taxonomy-info');
        $response->assertSee(__('Show Taxonomy Information'));
        $response->assertDontSee($this->taxonomy->description);
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
            'code' => 'test-term-' . uniqid(),
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
            'code' => 'no-desc-tax-' . uniqid(),
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
            'code' => 'no-desc-tax-edit-' . uniqid(),
            'description' => null,
            'properties' => [],
            'created_by' => $this->adminUser->id,
            'updated_by' => $this->adminUser->id,
        ]);
        $taxonomyWithoutDescription->save();

        $term = new TaxonomyTerm([
            'taxonomy_id' => $taxonomyWithoutDescription->id,
            'name' => 'Test Term No Desc',
            'code' => 'test-term-no-desc-' . uniqid(),
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