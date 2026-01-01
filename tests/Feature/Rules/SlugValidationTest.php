<?php

namespace Tests\Feature;

use App\Rules\Slug;
use Validator;
use Tests\TestCase;

class SlugValidationTest extends TestCase
{
  /** @test */
  public function test_it_accepts_valid_slugs(): void
  {
    $rule = ['slug' => [new Slug()]];
    $this->assertTrue(Validator::make(['slug' => 'hello-world'], $rule)->passes());
    $this->assertTrue(Validator::make(['slug' => 'foo-123-bar'], $rule)->passes());
  }

  /** @test */
  public function test_it_rejects_invalid_slugs(): void
  {
    $rule = ['slug' => [new Slug()]];
    $this->assertFalse(Validator::make(['slug' => 'Hello'], $rule)->passes());
    $this->assertFalse(Validator::make(['slug' => '--foo'], $rule)->passes());
    $this->assertFalse(Validator::make(['slug' => 'bar--'], $rule)->passes());
    $this->assertFalse(Validator::make(['slug' => 'foo--bar'], $rule)->passes());
    $this->assertFalse(Validator::make(['slug' => 'foo*'], $rule)->passes());
  }
}