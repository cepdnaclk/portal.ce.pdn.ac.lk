<?php

namespace Tests\Feature\Backend\Courses;

use App\Domains\Course\Models\Course;
use App\Domains\Semester\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Class CourseTest.
 */
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
    public function a_course_can_be_created_via_livewire()
    {
        $this->loginAsCourseManager();

        $semester = Semester::factory()->create();

        Livewire::test(\App\Http\Livewire\Backend\CreateCourses::class)
            ->set('academicProgram', 'undergraduate')
            ->set('semester', (string) $semester->id)
            ->set('version', '1')
            ->set('type', 'Core')
            ->set('code', 'CS101')
            ->set('name', 'Introduction to Computer Science')
            ->set('credits', 3)
            ->set('content', 'Basic concepts of computer science.')
            ->set('objectives', 'Learn the basics of computer science')
            ->set('time_allocation', ['lecture' => 3, 'tutorial' => 1, 'practical' => 1])
            ->set('marks_allocation', ['practicals' => 20, 'mid_exam' => 30, 'end_exam' => 50])
            ->set('ilos', ['knowledge' => ['Understand basic algorithms'], 'skills' => ['Implement basic programs']])
            ->set('references', ['Introduction to Algorithms'])
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('courses', [
            'code' => 'CS101',
            'name' => 'Introduction to Computer Science',
        ]);
    }

    /** @test */
    public function a_course_can_be_updated_via_livewire()
    {
        $this->loginAsCourseManager();
        $course = Course::factory()->create();

        Livewire::test(\App\Http\Livewire\Backend\CreateCourses::class)
            ->set('academicProgram', $course->academic_program)
            ->set('semester', (string) $course->semester_id)
            ->set('version', (string) $course->version)
            ->set('type', $course->type)
            ->set('code', 'CS102')  
            ->set('name', 'Advanced Computer Science')
            ->set('credits', 3)
            ->set('content', 'Advanced topics in computer science.')
            ->set('objectives', 'Learn advanced topics')
            ->set('time_allocation', ['lecture' => 3, 'tutorial' => 1, 'practical' => 2])
            ->set('marks_allocation', ['practicals' => 30, 'mid_exam' => 20, 'end_exam' => 50])
            ->set('ilos', ['knowledge' => ['Understand advanced algorithms']])
            ->set('references', ['Advanced Algorithms'])
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('courses', [
            'code' => 'CS102',
            'name' => 'Advanced Computer Science',
        ]);
    }

    /** @test */
    public function a_course_can_be_deleted()
    {
        $this->loginAsCourseManager();
        $course = Course::factory()->create();

        $response = $this->delete('/dashboard/courses/' . $course->id);

        $response->assertRedirect('/dashboard/courses');
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    /** @test */
    public function unauthorized_user_cannot_access_course_pages()
    {
        $course = Course::factory()->create();

        $this->get('/dashboard/courses/')->assertRedirect('/login');
        $this->get('/dashboard/courses/create')->assertRedirect('/login');
        $this->post('/dashboard/courses')->assertRedirect('/login');
        $this->put("/dashboard/courses/{$course->id}")->assertRedirect('/login');
        $this->delete('/dashboard/courses/' . $course->id)->assertRedirect('/login');
    }

    /** @test */
    public function store_course_requires_valid_data()
    {
        $this->loginAsCourseManager();

        Livewire::test(\App\Http\Livewire\Backend\CreateCourses::class)
            ->set('academicProgram', '')
            ->set('semester', '')
            ->set('version', '')
            ->set('type', '')
            ->set('code', '')
            ->set('name', '')
            ->set('credits', '')
            ->call('submit')
            ->assertHasErrors(['academicProgram', 'semester', 'version', 'type', 'code', 'name', 'credits']);
    }

    /** @test */
    public function update_course_requires_valid_data()
    {
        $this->loginAsCourseManager();
        $course = Course::factory()->create();

        Livewire::test(\App\Http\Livewire\Backend\CreateCourses::class)
            ->set('academicProgram', '')
            ->set('semester', '')
            ->set('version', '')
            ->set('type', '')
            ->set('code', '')
            ->set('name', '')
            ->set('credits', '')
            ->call('submit')
            ->assertHasErrors(['academicProgram', 'semester', 'version', 'type', 'code', 'name', 'credits']);
    }
}
