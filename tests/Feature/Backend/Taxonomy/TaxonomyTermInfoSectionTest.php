<?php

namespace Tests\Feature\Backend\Taxonomy;

use App\Domains\Auth\Models\User;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyTermInfoSectionTest extends TestCase
{
    use RefreshDatabase;

    protected ?User $adminUser;
    protected Taxonomy $taxonomy;

    public function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->admin()->create();

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
        $this->loginAsAdmin();
        $response = $this->get(route('dashboard.taxonomy.terms.create', $this->taxonomy));

        $response->assertOk();
        $response->assertSeeLivewire('backend.expandable-info-card');
        $response->assertSee(__('Info'));
    }

    /** @test */
    public function info_section_appears_on_taxonomy_term_edit_page()
    {
        $this->loginAsAdmin();
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
        $response->assertSeeLivewire('backend.expandable-info-card');
        $response->assertSee(__('Info'));
    }


    /** @test */
    public function info_section_is_not_present_if_taxonomy_has_no_description_on_create_page()
    {
        $this->loginAsAdmin();
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
        $response->assertDontSeeLivewire('backend.expandable-info-card');;
        $response->assertDontSee(__('Info'));
    }

    /** @test */
    public function info_section_is_not_present_if_taxonomy_has_no_description_on_edit_page()
    {
        $this->loginAsAdmin();
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
        $response->assertDontSeeLivewire('backend.expandable-info-card');
        $response->assertDontSee(__('Info'));
    }
}
