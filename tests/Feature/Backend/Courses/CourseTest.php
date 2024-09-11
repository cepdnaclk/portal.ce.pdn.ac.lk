<?php

namespace Tests\Feature\Backend\Courses;

use App\Domains\Course\Models\Course;
use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_course_manager_can_access_the_list_courses_page()
    {
        $this->loginAsCourseManager();
        $this->get('/dashboard/courses/')->assertOk();
    }

    /** @test */
    public function a_course_manager_can_access_the_create_course_page()
    {
        $this->loginAsCourseManager();
        $this->get('/dashboard/courses/create')->assertOk();
    }

    /** @test */
    public function a_course_manager_can_access_the_delete_course_page()
    {
        $this->loginAsCourseManager();
        $course = Course::factory()->create();
        $this->get('/dashboard/courses/delete/' . $course->id)->assertOk();
    }


    /** @test */
    public function unauthorized_user_cannot_access_course_pages()
    {
        $course = Course::factory()->create();

        $this->get('/dashboard/courses/')->assertRedirect('/login');
        $this->get('/dashboard/courses/create')->assertRedirect('/login');
        $this->get('/dashboard/courses/delete/' . $course->id)->assertRedirect('/login');
        $this->post('/dashboard/courses')->assertRedirect('/login');
        $this->put("/dashboard/courses/{$course->id}")->assertRedirect('/login');
        $this->delete('/dashboard/courses/' . $course->id)->assertRedirect('/login');
    }
}
