<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use Carbon\Carbon;

use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;

class TaxonomySeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Fixed data structures used in the Portal site will be defined in here, and need to get seeded into the Database for proper functioning the site
        $taxonomies = [
            'intranet' => [
                'code' => 'intranet',
                'name' => 'CE Intranet',
                'description' => 'This manages the intranet links shown in https://www.ce.pdn.ac.lk/intranet',
                'properties' => [
                    ['code' => 'link', 'name' => 'Link', 'data_type' => 'url']
                ],
                'terms' => [] // Update the list of terms as needed
            ],
            'students' => [
                'code' => 'students',
                'name' => 'Students',
                'description' => 'This taxonomy manages the list of student batches in the Department',
                'properties' => [
                    ['code' => 'start_date', 'name' => 'Academic Start Date', 'data_type' => 'date'],
                    ['code' => 'end_date', 'name' => 'Academic End Date', 'data_type' => 'date'],
                    ['code' => 'profiles_page', 'name' => 'Profiles Page', 'data_type' => 'url'],
                    ['code' => 'notes', 'name' => 'Notes', 'data_type' => 'string']
                ],
                'terms' => [] // Update the list of terms as needed
            ],
            'courses' => [
                'code' => 'courses',
                'name' => 'Course',
                'description' => 'Course types offered by the Department, with related metadata',
                'properties' => [
                    ["code" => "key", "name" => "Key", "data_type" => "string"],
                    ["code" => "value", "name" => "Value", "data_type" => "string"]
                ],
                'terms' => [
                    [
                        'code' => 'course_types',
                        'name' => 'Course Types',
                        'parent_id' => NULL,
                        'metadata' => '',
                        'terms' => [
                            [
                                'code' => 'course_type_foundation',
                                'name' => 'Foundation',
                                'parent_id' => '24',
                                'metadata' => '[{"code": "key", "value": "Foundation"}, {"code": "value", "value": "Foundation"}]',
                            ],
                            [
                                'code' => 'course_type_core',
                                'name' => 'Core',
                                'parent_id' => '24',
                                'metadata' => '[{"code": "key", "value": "Core"}, {"code": "value", "value": "Core"}]',
                            ],
                            [
                                'code' => 'course_type_ge',
                                'name' => 'General Electives',
                                'parent_id' => '24',
                                'metadata' => '[{"code": "key", "value": "GE"}, {"code": "value", "value": "General Electives"}]',
                            ],
                            [
                                'code' => 'course_type_te',
                                'name' => 'Technical Electives',
                                'parent_id' => '24',
                                'metadata' => '[{"code": "key", "value": "TE"}, {"code": "value", "value": "Technical Electives"}]',
                            ],

                        ]
                    ],
                    [
                        'code' => 'academic_program',
                        'name' => 'Academic Program',
                        'parent_id' => NULL,
                        'metadata' => '',
                        'terms' => [
                            [
                                'code' => 'academic_program_undergraduate',
                                'name' => 'Undergraduate',
                                'metadata' => '[{"code": "key", "value": "undergraduate"}, {"code": "value", "value": "Undergraduate"}]',
                                'terms' => [
                                    [
                                        'code' => 'ug_curriculum_v0',
                                        'name' => 'Old Curriculum',
                                        'metadata' => '[{"code": "key", "value": "0"}, {"code": "value", "value": "Old Curriculum"}]',
                                    ],
                                    [
                                        'code' => 'ug_curriculum_v1',
                                        'name' => 'Current Curriculum',
                                        'metadata' => '[{"code": "key", "value": "1"}, {"code": "value", "value": "Current Curriculum"}]',

                                    ],
                                    ['code' => 'ug_curriculum_v2', 'name' => 'Curriculum - Effective from E22', 'taxonomy_id' => '3', 'parent_id' => '30', 'metadata' => '[{"code": "key", "value": "2"}, {"code": "value", "value": "Curriculum - Effective from E22"}]', 'created_by' => '6', 'updated_by' => '6', 'created_at' => '2024-12-08 18:07:20', 'updated_at' => '2024-12-08 18:07:47'],
                                ]
                            ],
                            [
                                'code' => 'academic_program_postgraduate',
                                'name' => 'Postgraduate',
                                'metadata' => '[{"code": "key", "value": "postgraduate"}, {"code": "value", "value": "Postgraduate"}]',
                                'terms' => [
                                    [
                                        'code' => 'pg_curriculum_v1',
                                        'name' => 'Current Curriculum - PG',
                                        'metadata' => '[{"code": "key", "value": "3"}, {"code": "value", "value": "Current Curriculum - PG"}]',
                                    ],
                                ]
                            ],

                        ]
                    ]
                ]
            ],
            'staff' => [
                'code' => 'staff',
                'name' => 'Department Staff',
                'description' => 'This list will be used to keep the details of the department staff, and will be integrated with the people.ce.pdn.ac.lk',
                'properties' => [
                    ["code" => "designation", "name" => "Designation", "data_type" => "string"],
                    ["code" => "email", "name" => "Email", "data_type" => "email"],
                    ["code" => "telephone", "name" => "Telephone", "data_type" => "string"],
                    ["code" => "profile_image", "name" => "Profile Image", "data_type" => "file"],
                    ["code" => "url_linkedin", "name" => "Linkedin Profile", "data_type" => "url"],
                    ["code" => "url_profile", "name" => "Additional Profile (Student)", "data_type" => "url"],
                    ["code" => "joined_date", "name" => "Joined Date", "data_type" => "date"],
                    ["code" => "leave_date", "name" => "Leave Date", "data_type" => "date"],
                ],
                'terms' => [
                    [
                        'code' => 'academic-staff',
                        'name' => 'Academic Staff',
                        'parent_id' => NULL,
                        'metadata' => '',
                    ],
                    [
                        'code' => 'temporary-academic-staff',
                        'name' => 'Temporary Academic Staff',
                        'parent_id' => NULL,
                        'metadata' => '',
                    ],
                    [
                        'code' => 'academic-support-staff',
                        'name' => 'Academic Support Staff',
                        'parent_id' => NULL,
                        'metadata' => ''
                    ]

                ],
            ],
            'lists' => [
                'code' => 'lists',
                'name' => 'Lists',
                'description' => 'This will manage the key-value list of contents used by the portal.ce.pdn.ac.lk, integrated over other places',
                'properties' => [
                    ["code" => "key", "name" => "Key", "data_type" => "integer"],
                    ["code" => "value", "name" => "Value", "data_type" => "string"],
                    ["code" => "description", "name" => "Description", "data_type" => "string"],
                ],
                'terms' => [
                    // Events
                    [
                        'code' => 'events',
                        'name' => 'Events',
                        'metadata' => '[{"code": "key", "value": null}, {"code": "value", "value": null}, {"code": "description", "value": "This list will manage the types of Events hosted by the department, and the list is available under Content Management > Events > Event Type"}]',
                        'terms' => [
                            [
                                'code' => 'event',
                                'name' => 'Event',
                                'metadata' => '[{"code": "key", "value": "0"}, {"code": "value", "value": "Event"}, {"code": "description", "value": "Department organized event"}]',
                            ],
                            [
                                'code' => 'seminar',
                                'name' => 'Seminar',
                                'metadata' => '[{"code": "key", "value": "1"}, {"code": "value", "value": "Seminar"}, {"code": "description", "value": "Seminar hosted by the Department"}]',
                            ],
                            [
                                'code' => 'aces',
                                'name' => 'ACES',
                                'metadata' => '[{"code": "key", "value": "2"}, {"code": "value", "value": "ACES Event"}, {"code": "description", "value": "Events hosted by the ACES team"}]',
                            ],
                            [
                                'code' => 'hackers',
                                'name' => 'Hackers',
                                'metadata' => '[{"code": "key", "value": "3"}, {"code": "value", "value": "Hackers Club"}, {"code": "description", "value": "The events hosted by the Hackers\' Club "}]',
                            ],
                        ],
                    ],
                    // Course ILOs
                    [
                        'code' => 'course_ilos',
                        'name' => 'Course ILOs list',
                        'metadata' => '[{"code": "key", "value": null}, {"code": "value", "value": null}, {"code": "description", "value": "List of ILO Types used in the Academic Program > Courses"}]',
                        'terms' => [
                            [
                                'code' => 'general',
                                'name' => 'General',
                                'metadata' => '[{"code": "key", "value": "1"}, {"code": "value", "value": "General"}]',
                            ],
                            [
                                'code' => 'knowledge',
                                'name' => 'Knowledge',
                                'metadata' => '[{"code": "key", "value": "2"}, {"code": "value", "value": "Knowledge"}]',
                            ],
                            [
                                'code' => 'skills',
                                'name' => 'Skills',
                                'metadata' => '[{"code": "key", "value": "3"}, {"code": "value", "value": "Skills"}]',
                            ],
                            [
                                'code' => 'attitudes',
                                'name' => 'Attitudes',
                                'metadata' => '[{"code": "key", "value": "4"}, {"code": "value", "value": "Attitudes"}]',
                            ],
                        ],
                    ],
                    // Marks Allocations
                    [
                        'code' => 'mark_allocations',
                        'name' => 'Marks Allocation',
                        'metadata' => '[{"code": "key", "value": null}, {"code": "value", "value": null}, {"code": "description", "value": "List of Mark Allocation Types used in the Academic Program > Courses"}]',
                        'terms' => [
                            [
                                'code' => 'practicals',
                                'name' => 'Practicals',
                                'metadata' => '',
                            ],
                            [
                                'code' => 'quizzes',
                                'name' => 'Quizzes',
                                'metadata' => '',
                            ],
                            [
                                'code' => 'assignments',
                                'name' => 'Assignments',
                                'metadata' => '',
                            ],
                            [
                                'code' => 'tutorials',
                                'name' => 'Tutorials',
                                'metadata' => '',
                            ],
                            [
                                'code' => 'projects',
                                'name' => 'Projects',
                                'metadata' => '',
                            ],
                            [
                                'code' => 'participation',
                                'name' => 'Participation',
                                'metadata' => '',
                            ],
                            [
                                'code' => 'mid_exam',
                                'name' => 'Mid Exam',
                                'metadata' => '',
                            ],
                            [
                                'code' => 'end_exam',
                                'name' => 'End Exam',
                                'metadata' => '',
                            ],
                        ]
                    ],
                    // Time Allocations
                    [
                        'code' => 'time_allocations',
                        'name' => 'Time Allocations',
                        'metadata' => '[{"code": "key", "value": null}, {"code": "value", "value": null}, {"code": "description", "value": "List of Time Allocation Types used in the Academic Program > Courses"}]',
                        'terms' => [
                            [
                                'code' => 'lecture',
                                'name' => 'Lectures',
                                'metadata' => '',
                            ],
                            [
                                'code' => 'tutorial',
                                'name' => 'Tutorials',
                                'metadata' => '',
                            ],
                            [
                                'code' => 'practical',
                                'name' => 'Practicals',
                                'metadata' => '',
                            ],
                            [
                                'code' => 'design',
                                'name' => 'Design',
                                'metadata' => '',
                            ],
                            [
                                'code' => 'assignment',
                                'name' => 'Assignments',
                                'metadata' => '',
                            ],
                            [
                                'code' => 'independent_learning',
                                'name' => 'Independent Learning',
                                'metadata' => '',
                            ],
                        ]
                    ]
                ]
            ]
        ];

        // Truncate the taxonomies and terms tables before seeding
        $this->truncateMultiple(['taxonomies', 'taxonomy_terms']);

        // Create taxonomies and their terms
        foreach ($taxonomies as $key => $taxonomy) {
            print("\nCreating taxonomy: {$taxonomy['code']}\n");

            $taxonomyRecord = Taxonomy::firstOrCreate([
                'code' => $taxonomy['code'],
                'name' => $taxonomy['name'],
                'description' => $taxonomy['description'],
                'properties' => $taxonomy['properties'],
            ]);

            if (isset($taxonomy['terms']) && sizeof($taxonomy['terms']) > 0) {
                print("Creating taxonomy terms\n");
                $this->createTaxonomyTerms($taxonomyRecord, $taxonomy['terms']);
            }
        }


        $this->enableForeignKeys();
    }

    /**
     * Create taxonomy terms for a given taxonomy.
     *
     * @param Taxonomy $taxonomy
     * @param array $terms
     */

    private function createTaxonomyTerms($taxonomy, $terms, $parent_id = null, $level = 0)
    {
        if (is_array($terms) && ! empty($terms)) {
            foreach ($terms as $term) {
                print(str_repeat('  ', $level + 1) . "{$term['code']}\n");
                $taxonomyTerm = TaxonomyTerm::firstOrCreate([
                    'code' => $term['code'],
                    'name' => $term['name'],
                    'taxonomy_id' => $taxonomy->id,
                    'parent_id' => $parent_id,
                    'metadata' => isset($term['metadata']) ? json_decode($term['metadata'], true) : null,
                ]);

                // If the term has sub-terms, recursively create them
                if (isset($term['terms'])) {
                    $this->createTaxonomyTerms($taxonomy, $term['terms'], $taxonomyTerm->id, $level + 1);
                }
            }
        }
    }
}