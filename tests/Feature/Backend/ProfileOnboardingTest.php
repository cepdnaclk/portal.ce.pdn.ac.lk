<?php

namespace Tests\Feature\Backend;

use App\Domains\Auth\Models\User;
use App\Domains\Profile\Models\UserProfile;
use App\Http\Livewire\Backend\ProfileWizard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileOnboardingTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function the_banner_is_shown_for_an_incomplete_profile()
  {
    $user = User::factory()->user()->create();
    UserProfile::factory()->create(['email' => $user->email, 'user_id' => $user->id, 'full_name' => null]);
    $this->actingAs($user);

    $this->get(route('dashboard.home'))
      ->assertOk()
      ->assertSee('please complete your profile')
      ->assertSee(route('dashboard.profiles.onboarding'));
  }

  /** @test */
  public function the_banner_is_hidden_once_the_profile_is_complete()
  {
    $user = User::factory()->user()->create();
    UserProfile::factory()->create([
      'email' => $user->email,
      'user_id' => $user->id,
      'full_name' => 'Jane Doe',
      'name_with_initials' => 'J. Doe',
      'preferred_short_name' => 'Jane',
      'preferred_long_name' => 'Jane Doe',
      'honorific' => 'Ms',
      'location' => 'Peradeniya',
    ]);
    $this->actingAs($user);

    $this->get(route('dashboard.home'))
      ->assertOk()
      ->assertDontSee('please complete your profile');
  }

  /** @test */
  public function the_wizard_page_loads_for_any_authenticated_user()
  {
    $this->actingAs(User::factory()->user()->create());

    $this->get(route('dashboard.profiles.onboarding'))->assertOk();
  }

  /** @test */
  public function the_wizard_updates_the_profile_and_reports_completeness()
  {
    $user = User::factory()->user()->create();
    $this->actingAs($user);

    Livewire::test(ProfileWizard::class)
      ->set('fields.full_name', 'Jane Doe')
      ->set('fields.name_with_initials', 'J. Doe')
      ->set('fields.preferred_short_name', 'Jane')
      ->set('fields.preferred_long_name', 'Jane Doe')
      ->set('fields.honorific', 'Ms')
      ->call('next')
      ->assertSet('step', 2)
      ->set('fields.location', 'Peradeniya')
      ->call('next')
      ->assertSet('step', 3)
      ->set('links.github', 'https://github.com/jane')
      ->call('finish')
      ->assertSet('step', 4);

    $profile = $user->refresh()->profile;
    $this->assertEquals('Jane Doe', $profile->full_name);
    $this->assertEquals('Peradeniya', $profile->location);
    $this->assertEquals('https://github.com/jane', $profile->links->firstWhere('type', 'github')->url);
    $this->assertTrue($profile->isComplete());
  }

  /** @test */
  public function the_wizard_rejects_an_invalid_link()
  {
    $this->actingAs(User::factory()->user()->create());

    Livewire::test(ProfileWizard::class)
      ->set('step', 3)
      ->set('links.github', 'not-a-url')
      ->call('finish')
      ->assertHasErrors('links.github');
  }
}