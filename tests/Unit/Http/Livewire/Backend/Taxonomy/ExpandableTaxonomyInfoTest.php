<?php

namespace Tests\Unit\Http\Livewire\Backend\Taxonomy;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Http\Livewire\Backend\Taxonomy\ExpandableTaxonomyInfo;
// use Illuminate\Foundation\Testing\RefreshDatabase; // Removed
use Livewire\Livewire;
use Tests\TestCase;

class ExpandableTaxonomyInfoTest extends TestCase
{
  // use RefreshDatabase; // Removed

  public function setUp(): void
  {
    // Don't call parent::setUp() to avoid global seeders from Tests\TestCase
    // Create the application instance as Laravel's BaseTestCase would.
    // The CreatesApplication trait is used in Tests\TestCase.
    $this->createApplication();

    // If any specific middleware needs to be disabled for these Livewire tests,
    // it could be done here, similar to how it's done in the parent TestCase, e.g.:
    // $this->withoutMiddleware(\App\Domains\Auth\Http\Middleware\TwoFactorAuthenticationStatus::class);
  }

  /** @test */
  public function component_renders_and_description_is_initially_hidden_when_description_exists()
  {
    $taxonomy = new Taxonomy([
      'id' => 1, // Dummy ID
      'name' => 'Test Taxonomy With Description',
      'description' => 'A test description.',
      // No need for created_by, updated_by, code, properties for this component's logic
    ]);

    Livewire::test(ExpandableTaxonomyInfo::class, ['taxonomy' => $taxonomy])
      ->assertSet('isExpanded', false)
      ->assertSee(__('Show Taxonomy Information'))
      ->assertDontSeeHtml($taxonomy->description);
  }

  /** @test */
  public function toggling_info_shows_and_hides_description_when_description_exists()
  {
    $taxonomy = new Taxonomy([
      'id' => 2, // Dummy ID
      'name' => 'Test Taxonomy For Toggling',
      'description' => 'A detailed test description.',
    ]);

    Livewire::test(ExpandableTaxonomyInfo::class, ['taxonomy' => $taxonomy])
      ->call('toggleInfo') // Show
      ->assertSet('isExpanded', true)
      ->assertSee('Hide Taxonomy Information') // Using raw string
      ->assertSeeHtml($taxonomy->description)
      ->call('toggleInfo') // Hide
      ->assertSet('isExpanded', false)
      ->assertSee('Show Taxonomy Information') // Using raw string
      ->assertDontSeeHtml($taxonomy->description);
  }

  /** @test */
  public function component_does_not_render_toggle_button_if_taxonomy_description_is_null()
  {
    $taxonomy = new Taxonomy([
      'id' => 3, // Dummy ID
      'name' => 'Test Taxonomy Null Description',
      'description' => null,
    ]);

    Livewire::test(ExpandableTaxonomyInfo::class, ['taxonomy' => $taxonomy])
      ->assertDontSee(__('Show Taxonomy Information'));
  }

  /** @test */
  public function component_does_not_render_toggle_button_if_taxonomy_description_is_empty_string()
  {
    $taxonomy = new Taxonomy([
      'id' => 4, // Dummy ID
      'name' => 'Test Taxonomy Empty Description',
      'description' => '',
    ]);

    Livewire::test(ExpandableTaxonomyInfo::class, ['taxonomy' => $taxonomy])
      ->assertDontSee(__('Show Taxonomy Information'));
  }
}