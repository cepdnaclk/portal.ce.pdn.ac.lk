<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Semester\Models\Semester;

class SemesterSeeder extends Seeder
{
    public function run()
    {
        Semester::create([
            'title' => 'Semester 1',
            'version' => 1,
            'academic_program' => 'Undergraduate',
            'description' => 'First semester of the undergraduate program.',
            'url' => '/semester1',
            'created_by' => 1,
            'updated_by' => 1, // Assuming user with ID 1 exists
        ]);

        Semester::create([
            'title' => 'Semester 2',
            'version' => 1,
            'academic_program' => 'Undergraduate',
            'description' => 'Second semester of the undergraduate program.',
            'url' => '/semester2',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Semester 3',
            'version' => 1,
            'academic_program' => 'Undergraduate',
            'description' => 'Third semester of the undergraduate program.',
            'url' => '/semester3',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Semester 4',
            'version' => 1,
            'academic_program' => 'Undergraduate',
            'description' => 'Fourth semester of the undergraduate program.',
            'url' => '/semester4',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Short Semester',
            'version' => 1,
            'academic_program' => 'Undergraduate',
            'description' => 'During this semester, the students will follow a Guided Software Engineering project with General Elective courses of at least 9 credits. The semester is 7 weeks long.',
            'url' => '/short-semester',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Semester 5',
            'version' => 1,
            'academic_program' => 'Undergraduate',
            'description' => 'Fifth semester of the undergraduate program.',
            'url' => '/semester5',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Semester 6',
            'version' => 1,
            'academic_program' => 'Undergraduate',
            'description' => 'Sixth semester of the undergraduate program.',
            'url' => '/semester6',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Semester 7',
            'version' => 1,
            'academic_program' => 'Undergraduate',
            'description' => 'This is a short semester of 8 weeks, the students will start their final year projects and follow a few mandatory general elective courses.',
            'url' => '/semester7',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Semester 8',
            'version' => 1,
            'academic_program' => 'Undergraduate',
            'description' => 'Eighth semester of the undergraduate program.',
            'url' => '/semester8',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'General Electives',
            'version' => 1,
            'academic_program' => 'Undergraduate',
            'description' => 'General Electives',
            'url' => '/general-electives',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Technical Electives',
            'version' => 1,
            'academic_program' => 'Undergraduate',
            'description' => 'Technical Electives',
            'url' => '/technical-electives',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
