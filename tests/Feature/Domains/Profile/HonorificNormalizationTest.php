<?php

namespace Tests\Feature\Domains\Profile;

use App\Domains\Profile\Models\UserProfile;
use Tests\TestCase;

class HonorificNormalizationTest extends TestCase
{
  /** @test */
  public function it_normalizes_honorifics_to_option_keys(): void
  {
    $this->assertSame('Mr.', UserProfile::normalizeHonorific('Mr'));
    $this->assertSame('Miss', UserProfile::normalizeHonorific('Miss.'));
    $this->assertSame('Dr.', UserProfile::normalizeHonorific(' dr '));
    $this->assertNull(UserProfile::normalizeHonorific(''));
    $this->assertNull(UserProfile::normalizeHonorific(null));

    foreach (array_keys(UserProfile::HONORIFIC_OPTIONS) as $option) {
      $this->assertSame($option, UserProfile::normalizeHonorific($option));
    }
  }
}
