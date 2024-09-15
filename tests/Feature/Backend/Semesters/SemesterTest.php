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
