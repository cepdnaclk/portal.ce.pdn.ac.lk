<?php

namespace Tests\Feature\Backend\Semesters;

use App\Domains\Semester\Models\Semester;
use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SemesterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_course_manager_can_access_the_list_semesters_page()
    {
        $this->loginAsCourseManager();
        $this->get('/dashboard/semesters/')->assertOk();
    }

    /** @test */
    public function a_course_manager_can_access_the_create_semester_page()
    {
        $this->loginAsCourseManager();
        $this->get('/dashboard/semesters/create')->assertOk();
    }

    /** @test */
    public function a_course_manager_can_access_the_delete_semester_page()
    {
        $this->loginAsCourseManager();
        $semester = Semester::factory()->create();
        $this->get('/dashboard/semesters/delete/' . $semester->id)->assertOk();
    }

    /** @test */
    public function semester_can_be_created()
    {
        $this->loginAsCourseManager();
        $response = $this->post('/dashboard/semesters', [
            'title' => 'Test Semester 1',
            'version' => 1,
            'academic_program' => 'Undergraduate',
            'description' => 'Description of Semester 1',
            'url' => '/semester-1',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('semesters', [
            'title' => 'Test Semester 1',
        ]);
    }
    /** @test */
    public function semester_can_be_updated()
    {
        $this->loginAsCourseManager();
        $semester = Semester::factory()->create();

        $updateData = [
            'title' => 'Test Semester 2',
            'version' => 2,
            'academic_program' => 'Postgraduate',
            'description' => 'Updated description',
            'url' => '/semester-2',
        ];

        $response = $this->put("/dashboard/semesters/{$semester->id}", $updateData);
        $response->assertStatus(302);

        $this->assertDatabaseHas('semesters', [
            'title' => 'Test Semester 2',
        ]);
    }

    
    /** @test */
    public function semester_can_be_deleted()
    {
        $this->loginAsCourseManager();
        $semester = Semester::factory()->create();
        $this->delete('/dashboard/semesters/' . $semester->id);
        $this->assertDatabaseMissing('semesters', ['id' => $semester->id]);
    }

     /** @test */
     public function semester_url_must_be_unique()
     {
         $this->loginAsCourseManager();
 
         
         Semester::factory()->create([
             'url' => '/unique-url'
         ]);
 
         
         $response = $this->post('/dashboard/semesters', [
             'title' => 'Test Semester 2',
             'version' => 1,
             'academic_program' => 'Undergraduate',
             'description' => 'Description of Semester 2',
             'url' => '/unique-url',
         ]);
 
         $response->assertSessionHasErrors('url');
     }
 
     /** @test */
     public function semester_must_have_valid_academic_program()
     {
         $this->loginAsCourseManager();
 
       
         $response = $this->post('/dashboard/semesters', [
             'title' => 'Test Semester 3',
             'version' => 1,
             'academic_program' => 'InvalidProgram',  
             'description' => 'Description of Semester 3',
             'url' => '/valid-url',
         ]);
 
       
         $response->assertSessionHasErrors('academic_program');
     }
 
     /** @test */
     public function semester_can_be_updated_with_unique_url()
     {
         $this->loginAsCourseManager();
 
         
         $semester = Semester::factory()->create([
             'url' => '/old-url'
         ]);
 
         Semester::factory()->create([
             'url' => '/existing-url'
         ]);
 
         $response = $this->put("/dashboard/semesters/{$semester->id}", [
             'title' => 'Updated Semester',
             'version' => 1,
             'academic_program' => 'Undergraduate',
             'description' => 'Updated description',
             'url' => '/existing-url', 
         ]);
 
         
         $response->assertSessionHasErrors('url');
     }

    /** @test */
    public function unauthorized_user_cannot_access_semester_pages()
    {
        $semester = Semester::factory()->create();

        $this->get('/dashboard/semesters/')->assertRedirect('/login');
        $this->get('/dashboard/semesters/create')->assertRedirect('/login');
        $this->get('/dashboard/semesters/delete/' . $semester->id)->assertRedirect('/login');
        $this->post('/dashboard/semesters')->assertRedirect('/login');
        $this->put("/dashboard/semesters/{$semester->id}")->assertRedirect('/login');
        $this->delete('/dashboard/semesters/' . $semester->id)->assertRedirect('/login');
    }
}
