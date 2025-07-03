<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;


class TaxonomyTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get taxonomy IDs
        $intranetId = Taxonomy::where('code', 'intranet')->first()->id;
        $studentsId = Taxonomy::where('code', 'students')->first()->id;

        // Intranet taxonomy terms
        $intranetTerms = [
            'students' => [
                'code' => 'students',
                'name' => 'For Students',
                'metadata' => [['code' => 'link', 'value' => null]],
                'children' => [
                    [
                        'code' => 'academic_calendar',
                        'name' => 'Academic Calendar',
                        'metadata' => [['code' => 'link', 'value' => 'https://docs.google.com/document/d/e/2PACX-1vR-EkodNirStWpMfHr1pZcivrPJ_usJRJV2-36o0aa8F6VHgwbr0xZVswd8x5fk3RZN0uLGZILSjsdW/pub']]
                    ],
                    [
                        'code' => 'timetables',
                        'name' => 'Timetables',
                        'metadata' => [['code' => 'link', 'value' => 'https://docs.google.com/document/d/e/2PACX-1vRp4MEjiFOZuvkPFd3_emXj6pPTxM91RF6Ilhn5CX7fRaj_dG6hDKa17ykQ5thIFpmzLLUC78bxbkn1/pub']]
                    ],
                    [
                        'code' => 'examination_schedules',
                        'name' => 'Examination Schedules',
                        'metadata' => [['code' => 'link', 'value' => 'https://docs.google.com/document/d/e/2PACX-1vRULmmwk1-6KQQVYkBWmIJNOliC-H1O2DJGPqlfI2vyHgzjo9IHS7AoeVPW9RoxnQn8_cLqOmkXrkFy/pub']]
                    ],
                    [
                        'code' => 'advisor_advisee',
                        'name' => 'Advisor-Advisee',
                        'metadata' => [['code' => 'link', 'value' => 'https://docs.google.com/spreadsheets/d/1r0A0kh6xtSxPt3wkcKYsHQLoWKG_XNYEyvAtDBPM-Xs/edit#gid=652951330']]
                    ]
                ]
            ],
            'staff' => [
                'code' => 'staff',
                'name' => 'For Staff',
                'metadata' => [['code' => 'link', 'value' => null]],
                'children' => [
                    [
                        'code' => 'exam_claim',
                        'name' => 'Exam Claim Application',
                        'metadata' => [['code' => 'link', 'value' => 'https://docs.google.com/document/u/2/d/e/2PACX-1vRJh5v40ChLsmR1iAfFGMEnjtzs4nef19JI7OymUMCBca6ybFiUtk43EqDG3I26rUJz3xyOODECHZ-I/pub?urp=gmail_link']]
                    ],
                    [
                        'code' => 'examination_progress',
                        'name' => 'Examination Progress',
                        'metadata' => [['code' => 'link', 'value' => 'https://docs.google.com/document/d/1n0l0YJwfeVv9cCpcq2N1ptIoDCEE339CjWqCaOKYC0I/edit']]
                    ],
                    [
                        'code' => 'work_allocation',
                        'name' => 'Work Allocation',
                        'metadata' => [['code' => 'link', 'value' => 'https://docs.google.com/document/d/e/2PACX-1vQw6ubwr36kpNVleJQaanrM6c2yXTh6eF79BHw37bkqzgTXzMv4NKPdOTY4XoWfqkaduQLRAjQohFR6/pub']]
                    ],
                    [
                        'code' => 'department_meeting_minutes',
                        'name' => 'Department Meeting Minutes',
                        'metadata' => [['code' => 'link', 'value' => 'https://docs.google.com/document/d/1YG9-amXUAgHCmqfWU85pg35yPpeG5eBO-YHPADem7N8/edit?usp=sharing']]
                    ]
                ]
            ]
        ];

        // Students taxonomy terms
        $studentsTerms = [
            'undergraduate' => [
                'code' => 'undergraduate',
                'name' => 'Undergraduate Students',
                'metadata' => $this->createStudentMetadata('https://people.ce.pdn.ac.lk/'),
                'children' => [
                    [
                        'code' => 'e20',
                        'name' => 'E20',
                        'metadata' => $this->createStudentMetadata('https://people.ce.pdn.ac.lk/students/e20/', null, '2022-05-30')
                    ],
                    [
                        'code' => 'e19',
                        'name' => 'E19',
                        'metadata' => $this->createStudentMetadata('https://people.ce.pdn.ac.lk/students/e19/')
                    ],
                    [
                        'code' => 'e18',
                        'name' => 'E18',
                        'metadata' => $this->createStudentMetadata('https://people.ce.pdn.ac.lk/students/e18/')
                    ]
                ]
            ],
            'postgraduate_students' => [
                'code' => 'postgraduate_students',
                'name' => 'Postgraduate Students',
                'metadata' => $this->createStudentMetadata('https://people.ce.pdn.ac.lk/students/postgraduate/')
            ],
            'undergraduate_alumni' => [
                'code' => 'undergraduate_alumni',
                'name' => 'Alumni Students',
                'metadata' => $this->createStudentMetadata('https://people.ce.pdn.ac.lk/alumni/', 'Only undergraduate batches'),
                'children' => [
                    [
                        'code' => 'e17',
                        'name' => 'E17',
                        'metadata' => $this->createStudentMetadata('https://people.ce.pdn.ac.lk/students/e17/')
                    ],
                    [
                        'code' => 'e16',
                        'name' => 'E16',
                        'metadata' => $this->createStudentMetadata('https://people.ce.pdn.ac.lk/students/e16/')
                    ],
                    [
                        'code' => 'e15',
                        'name' => 'E15',
                        'metadata' => $this->createStudentMetadata('https://people.ce.pdn.ac.lk/students/e15/', null, '2016-11-16', '2021-09-03')
                    ],
                    [
                        'code' => 'e14',
                        'name' => 'E14',
                        'metadata' => $this->createStudentMetadata('https://people.ce.pdn.ac.lk/students/e14/')
                    ],
                    [
                        'code' => 'e13',
                        'name' => 'E13',
                        'metadata' => $this->createStudentMetadata('https://people.ce.pdn.ac.lk/students/e13/')
                    ],
                    [
                        'code' => 'e12',
                        'name' => 'E12',
                        'metadata' => $this->createStudentMetadata('https://people.ce.pdn.ac.lk/students/e12/')
                    ],
                    [
                        'code' => 'e11',
                        'name' => 'E11',
                        'metadata' => $this->createStudentMetadata('https://people.ce.pdn.ac.lk/students/e11/')
                    ]
                ]
            ]
        ];

        $this->createTermsWithChildren($intranetTerms, $intranetId);
        $this->createTermsWithChildren($studentsTerms, $studentsId);
    }

    /**
     * Helper function to create student metadata
     */
    private function createStudentMetadata($profilesPage, $notes = null, $startDate = null, $endDate = null)
    {
        return [
            ['code' => 'start_date', 'value' => $startDate],
            ['code' => 'end_date', 'value' => $endDate],
            ['code' => 'profiles_page', 'value' => $profilesPage],
            ['code' => 'notes', 'value' => $notes]
        ];
    }

    /**
     * Helper function to create terms with their children
     */
    private function createTermsWithChildren($terms, $taxonomyId, $parentId = null)
    {
        foreach ($terms as $term) {
            $createdTerm = TaxonomyTerm::firstOrCreate([
                'code' => $term['code'],
                'name' => $term['name'],
                'taxonomy_id' => $taxonomyId,
                'parent_id' => $parentId,
                'metadata' => $term['metadata'],
                'created_by' => 1,
                'updated_by' => 1
            ]);

            if (isset($term['children'])) {
                foreach ($term['children'] as $child) {
                    TaxonomyTerm::firstOrCreate([
                        'code' => $child['code'],
                        'name' => $child['name'],
                        'taxonomy_id' => $taxonomyId,
                        'parent_id' => $createdTerm->id,
                        'metadata' => $child['metadata'],
                        'created_by' => 1,
                        'updated_by' => 1
                    ]);
                }
            }
        }
    }
}
