<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use App\Domains\Tenant\Models\Tenant;
use RuntimeException;
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
                  [
                    'code' => 'ug_curriculum_v2',
                    'name' => 'Curriculum - Effective from E22',
                    'metadata' => '[{"code": "key", "value": "2"}, {"code": "value", "value": "Curriculum - Effective from E22"}]',
                  ],
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
                'metadata' => '[{"code": "key", "value": "3"}, {"code": "value", "value": "Hackers Club"}, {"code": "description", "value": "The events hosted by the Hackers\' Club"}]',
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
        ],
      ],
      'project-categories' => [
        'code' => 'project-categories',
        'name' => 'Project Categories',
        'description' => 'This will manage the list of Project Categories shown in the https://projects.ce.pdn.ac.lk/.\n\ncourse_code: Course code, or list of related codes (comma separated)\nfilter: A regex filter that defines the repository naming convention\nnaming_convention: A representative way of naming the projects\ncontact: Contact Person / Course Coordinator email\nfurther_details: A page with additional information\nactive: Boolean flag to indicate project category is active or not.',
        'visibility' => '1',
        'properties' => [
          ["code" => "course_code", "name" => "Course Code", "data_type" => "string"],
          ["code" => "cover_image", "name" => "Cover Image", "data_type" => "file"],
          ["code" => "thumbnail_image", "name" => "Thumbnail Image", "data_type" => "file"],
          ["code" => "description", "name" => "Description", "data_type" => "string"],
          ["code" => "repo_template", "name" => "GitHub Repo Template URL", "data_type" => "url"],
          ["code" => "filter", "name" => "Regex Filter", "data_type" => "string"],
          ["code" => "naming_convention", "name" => "Naming Convention for Repositories", "data_type" => "string"],
          ["code" => "contact", "name" => "Contact Person", "data_type" => "email"],
          ["code" => "further_details", "name" => "Further Details", "data_type" => "page"],
          ["code" => "active", "name" => "Active Category", "data_type" => "boolean"],
        ],
        'terms' => [
          [
            'code' => 'course_project',
            'name' => 'Course',
          ],
          [
            'code' => 'general_project',
            'name' => 'General',
            'terms' => [
              [
                'code' => '2yp',
                'name' => 'Software Systems Design Project',
                'metadata' => '[{"code": "course_code", "value": "CO227, CO2060"}, {"code": "description", "value": "Software systems designed and developed by second year Computer Engineering Students as part of coursework"}, {"code": "repo_template", "value": "https://github.com/cepdnaclk/eYY-CO2060-project-template"}, {"code": "filter", "value": "^e(\\\\d{2})-{co227|co2060}-(.+)$"}, {"code": "naming_convention", "value": "https://github.com/cepdnaclk/eYY-co2060-TITLE"}, {"code": "contact", "value": null}, {"code": "further_details", "value": null}, {"code": "active", "value": true}]'
              ],
              [
                'code' => '3yp',
                'name' => 'Cyber-Physical Systems Projects',
                'metadata' => '[{"code": "course_code", "value": "CO3060"}, {"code": "description", "value": "Cyber-Physical Systems designed and implemented by 3rd year Computer Engineering Students as part of coursework. These projects contain modern embedded hardware and software, cloud-deployed web back-end/front-end software and modern networking and communication for integration"}, {"code": "repo_template", "value": "https://github.com/cepdnaclk/eYY-3yp-project-template"}, {"code": "filter", "value": "^e(\\\\d{2})-3yp-(.+)$"}, {"code": "naming_convention", "value": "https://github.com/cepdnaclk/eYY-3yp-TITLE"}, {"code": "contact", "value": "isurun@eng.pdn.ac.lk"}, {"code": "further_details", "value": "25"}, {"code": "active", "value": true}]'
              ],
              [
                'code' => '4yp',
                'name' => 'Computer Engineering Research Project',
                'metadata' => '[{"code": "course_code", "value": "CO4060, CO421, CO425"}, {"code": "description", "value": "Final Year Research Project for Computer Engineering Undergraduates"}, {"code": "repo_template", "value": "https://github.com/cepdnaclk/eYY-4yp-project-template/"}, {"code": "filter", "value": "^e(\\\\d{2})-4yp-(.+)$"}, {"code": "naming_convention", "value": "https://github.com/cepdnaclk/eYY-4yp-TITLE"}, {"code": "contact", "value": null}, {"code": "further_details", "value": null}, {"code": "active", "value": true}]'
              ]
            ]
          ],
        ]
      ]
    ];

    // Truncate the taxonomies and terms tables before seeding
    $this->truncateMultiple(['taxonomies', 'taxonomy_terms']);

    $defaultTenantId = Tenant::defaultId() ?? Tenant::query()->value('id');

    if (! $defaultTenantId) {
      throw new RuntimeException('Default tenant not found. Please ensure at least one tenant exists before running TaxonomySeeder.');
    }

    // Create taxonomies and their terms
    foreach ($taxonomies as $key => $taxonomy) {

      // print("\nCreating taxonomy: {$taxonomy['code']}\n");

      $taxonomyRecord = Taxonomy::firstOrCreate([
        'code' => $taxonomy['code'],
        'name' => $taxonomy['name'],
        'description' => $taxonomy['description'],
        'properties' => $taxonomy['properties'],
        'tenant_id' => $defaultTenantId,
      ]);

      if (isset($taxonomy['terms']) && sizeof($taxonomy['terms']) > 0) {
        // print("Creating taxonomy terms\n");
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
        // print(str_repeat('  ', $level + 1) . "{$term['code']}\n");
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
