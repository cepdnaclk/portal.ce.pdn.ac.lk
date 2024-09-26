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
            'academic_program' => 'undergraduate',
            'description' => 'First semester of the undergraduate program.',
            'url' => 'semester-1',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Semester 2',
            'version' => 1,
            'academic_program' => 'undergraduate',
            'description' => 'Second semester of the undergraduate program.',
            'url' => 'semester-2',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Semester 3',
            'version' => 1,
            'academic_program' => 'undergraduate',
            'description' => 'Third semester of the undergraduate program.',
            'url' => 'semester-3',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Semester 4',
            'version' => 1,
            'academic_program' => 'undergraduate',
            'description' => 'Fourth semester of the undergraduate program.',
            'url' => 'semester-4',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Short Semester',
            'version' => 1,
            'academic_program' => 'undergraduate',
            'description' => 'During this semester, the students will follow a Guided Software Engineering project with General Elective courses of at least 9 credits. The semester is 7 weeks long.',
            'url' => 'short-semester',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Semester 5',
            'version' => 1,
            'academic_program' => 'undergraduate',
            'description' => 'Fifth semester of the undergraduate program.',
            'url' => 'semester-5',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Semester 6',
            'version' => 1,
            'academic_program' => 'undergraduate',
            'description' => 'Sixth semester of the undergraduate program.',
            'url' => 'semester-6',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Semester 7',
            'version' => 1,
            'academic_program' => 'undergraduate',
            'description' => 'This is a short semester of 8 weeks, the students will start their final year projects and follow a few mandatory general elective courses.',
            'url' => 'semester-7',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Semester 8',
            'version' => 1,
            'academic_program' => 'undergraduate',
            'description' => 'Eighth semester of the undergraduate program.',
            'url' => 'semester-8',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'General Electives',
            'version' => 1,
            'academic_program' => 'undergraduate',
            'description' => 'General Electives',
            'url' => 'general-electives',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Semester::create([
            'title' => 'Technical Electives',
            'version' => 1,
            'academic_program' => 'undergraduate',
            'description' => 'Technical Electives',
            'url' => 'technical-electives',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}