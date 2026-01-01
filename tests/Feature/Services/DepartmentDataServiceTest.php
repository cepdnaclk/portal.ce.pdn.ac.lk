<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Services\DepartmentDataService;
use Illuminate\Support\Facades\Http;

class DepartmentDataServiceTest extends TestCase
{
  /** @test */
  public function can_check_about_the_existence_of_an_user_email_()
  {
    Http::fake([
      '*' => Http::response([
        [
          'emails' => ['faculty' => ['name' => 'nuwanjaliyagoda', 'domain' => 'eng.pdn.ac.lk']],
          'email' => 'staff1@eng.pdn.ac.lk'
        ]
      ])
    ]);

    $api = new DepartmentDataService();
    $this->assertTrue($api->isInternalEmail('nuwanjaliyagoda@eng.pdn.ac.lk'));
    $this->assertFalse($api->isInternalEmail('user@example.com'));
  }
}