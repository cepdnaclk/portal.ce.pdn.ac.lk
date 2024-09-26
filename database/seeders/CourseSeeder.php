<?php

namespace Database\Seeders;

use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use App\Domains\Course\Models\Course;

use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Semester 1 Courses
        Course::create([
            'code' => 'GP101',
            'name' => 'English I',
            'credits' => 3,
            'type' => 'Core',
            'semester_id' => 1,
            'academic_program' => 'undergraduate',
            'version' => 1,
            'content' => 'Language development, Communication through reading, Communication through listening, Communication through writing, Communication through speech',
            'objectives' => '',
            'ilos' => json_encode(['knowledge' => [], 'skills' => [], 'attitudes' => []]),
            'time_allocation' => json_encode(['lecture' => '20', 'assignment' => '50', 'tutorial' => '', 'practical' => '1']),
            'marks_allocation' => json_encode([
                'practicals' => '10',
                'project' => '',
                'mid_exam' => '30',
                'end_exam' => '60'
            ]),
            'references' => json_encode([]),
            'created_by' => '1',
            'updated_by' => '1',

        ]);

        Course::create([
            'code' => 'GP109',
            'name' => 'Materials Science',
            'credits' => 3,
            'type' => 'Core',
            'semester_id' => 1,
            'academic_program' => 'undergraduate',
            'version' => 1,
            'content' => 'Introduction to the structure and properties of engineering materials, Principles underlying structure-property relationships, Phase equilibrium, Structure and properties of cement and timber...',
            'objectives' => 'Introduce the structure and properties of Engineering Materials',
            'ilos' => json_encode([
                'knowledge' => ['Describe materials in major classes of engineering materials'],
                'skills' => ['Use Equilibrium Phase diagrams...'],
                'attitudes' => ['Appreciate structure-property relationships...']
            ]),
            'time_allocation' => json_encode(['lecture' => '38', 'assignment' => '1', 'tutorial' => '10', 'practical' => '1']),
            'marks_allocation' => json_encode([
                'practicals' => '10',
                'project' => '10',
                'mid_exam' => '30',
                'end_exam' => '50'
            ]),
            'references' => json_encode([
                'Engineering Materials 1...',
                'The Science and Engineering of Materials...'
            ]),
            'created_by' => '1',
            'updated_by' => '1',

        ]);

        Course::create([
            'code' => 'GP110',
            'name' => 'Engineering Mechanics',
            'credits' => 3,
            'type' => 'Core',
            'semester_id' => 1,
            'academic_program' => 'undergraduate',
            'version' => 1,
            'content' => 'Force systems, Analysis of simple structures, Work and energy methods, Inertial properties of plane and three-dimensional objects...',
            'objectives' => 'To introduce the state of rest or motion of bodies subjected to forces. Emphasis on applications to Engineering Designs.',
            'ilos' => json_encode([
                'knowledge' => ['Use scalar and vector methods for analyzing forces in structures.'],
                'skills' => ['Apply fundamental concepts of motion and identify parameters that define motion.'],
                'attitudes' => ['Use engineering mechanics for solving problems systematically.']
            ]),
            'time_allocation' => json_encode(['lecture' => '28', 'assignment' => '11', 'tutorial' => '', 'practical' => '12']),
            'marks_allocation' => json_encode([
                'practicals' => '10',
                'project' => '10',
                'mid_exam' => '20',
                'end_exam' => '60'
            ]),
            'references' => json_encode([
                'Hibbeler, R.C., Engineering Mechanics Statics and Dynamics...',
                'Douglas, J. F., Fluid Mechanics...'
            ]),
            'created_by' => '1',
            'updated_by' => '1',
        ]);

        Course::create([
            'code' => 'GP115',
            'name' => 'Calculus I',
            'credits' => 3,
            'type' => 'Core',
            'semester_id' => 1,
            'academic_program' => 'undergraduate',
            'version' => 1,
            'content' => 'Real number system, Functions of a single variable, 2-D coordinate geometry, 3-D Euclidean geometry, Complex numbers...',
            'objectives' => '',
            'ilos' => json_encode([
                'knowledge' => ['Analyze problems in limits, continuity, differentiability, and integration.'],
                'skills' => ['Compute derivatives of complex functions, identify conic sections, and solve problems.'],
                'attitudes' => ['Determine the convergence of sequences and series, and find power series expansions.']
            ]),
            'time_allocation' => json_encode(['lecture' => '36', 'assignment' => '18', 'tutorial' => '', 'practical' => '12']),
            'marks_allocation' => json_encode([
                'practicals' => '20',
                'project' => '',
                'mid_exam' => '30',
                'end_exam' => '50'
            ]),
            'references' => json_encode([
                'James Stewart, Calculus...',
                'Watson Fulks, Advanced Calculus...'
            ]),
            'created_by' => '1',
            'updated_by' => '1',
        ]);

        Course::create([
            'code' => 'GP112',
            'name' => 'Engineering Measurements',
            'credits' => 3,
            'type' => 'Core',
            'semester_id' => 1,
            'academic_program' => 'undergraduate',
            'version' => 1,
            'content' => 'Units and standards, Approximation errors and calibration, Measurement of physical parameters...',
            'objectives' => json_encode(['Understand different aspects of instrumentation and solve engineering problems through measurement and experimentation.']),
            'ilos' => json_encode([
                'knowledge' => ['Measure basic engineering quantities and present results using charts and tables.'],
                'skills' => ['Identify and minimize measurement errors, analyze time-dependent output of instruments.'],
                'attitudes' => ['Construct experiments to test hypotheses using statistical techniques.']
            ]),
            'time_allocation' => json_encode(['lecture' => '21', 'assignment' => '', 'tutorial' => '4', 'practical' => '40']),
            'marks_allocation' => json_encode([
                'practicals' => '40',
                'project' => '20',
                'mid_exam' => '',
                'end_exam' => '40'
            ]),
            'references' => json_encode([
                'Schofield, W., Engineering Surveying...',
                'Ghilani, Charles D., Elementary Surveying...'
            ]),
            'created_by' => '1',
            'updated_by' => '1',
        ]);

        Course::create([
            'code' => 'GP113',
            'name' => 'Fundamentals of Manufacture',
            'credits' => 3,
            'type' => 'Core',
            'semester_id' => 2,
            'academic_program' => 'undergraduate',
            'version' => 1,
            'content' => 'Introduction to manufacturing industry, Machining, Casting, Welding, Metal forming, Manufacturing systems...',
            'objectives' =>
            'Provide fundamental knowledge of manufacturing engineering and design.Enable students to evaluate and manufacture products while satisfying consumer requirements.',
            'ilos' => json_encode([
                'knowledge' => ['Understand the core principles of manufacturing processes.'],
                'skills' => ['Evaluate manufacturing systems for optimizing efficiency.'],
                'attitudes' => ['Apply safety measures in engineering manufacturing processes.']
            ]),
            'time_allocation' => json_encode(['lecture' => '20', 'assignment' => '36', 'tutorial' => '7', 'practical' => '40']),
            'marks_allocation' => json_encode([
                'practicals' => '30',
                'project' => '10',
                'mid_exam' => '20',
                'end_exam' => '40'
            ]),
            'references' => json_encode([
                'Shop Theory by Anderson and Tatro...',
                'Workshop Technology by W.A.J. Chapman...'
            ]),
            'created_by' => '1',
            'updated_by' => '1',
        ]);

        Course::create([
            'code' => 'CO221',
            'name' => 'Digital Design',
            'credits' => 3,
            'type' => 'Core',
            'semester_id' => 3,
            'academic_program' => 'undergraduate',
            'version' => 1,
            'content' => 'Introduction to digital logic, Number systems, Combinational logic circuits, Sequential logic circuits, Digital circuit design and implementation...',
            'objectives' =>
            'Introduce digital electronics with emphasis on practical design techniques for digital circuits.Teach how to design combinational and sequential circuits.',
            'ilos' => json_encode([
                'knowledge' => ['Perform Boolean manipulation and design digital circuits.'],
                'skills' => ['Design and implement basic combinational and sequential circuits.'],
                'attitudes' => ['Develop confidence in digital circuit design.']
            ]),
            'time_allocation' => json_encode(['lecture' => '30', 'assignment' => '14', 'tutorial' => '10']),
            'marks_allocation' => json_encode([

                'practicals' => '10',
                'project' => '',
                'mid_exam' => '30',
                'end_exam' => '60'
            ]),
            'references' => json_encode([
                'Digital Design by Morris Mano...',
                'Digital Design: A Systems Approach by William James Dally...'
            ]),
            'created_by' => '1',
            'updated_by' => '1',
        ]);
    }
}