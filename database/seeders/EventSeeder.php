<?php

namespace Database\Seeders;

use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;
use App\Domains\Event\Models\Event;

class EventSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->disableForeignKeys();
        $this->truncateMultiple([
            'events',
        ]);
        // Create some sample event
        $events = [
            [
                "id" => 3,
                "title" => "ESCAPE - 2020",
                "description" => '<p><span style="color: rgb(33, 37, 41);">EscaPe is the annual project symposium of the Department of Computer Engineering, University of Peradeniya. It will present the research projects of the undergraduates of the Department of Computer Engineering. ESCaPe 2020 is the 5th symposium that is organized by the department and this time the symposium is open for a broader audience and aims to build a platform for the undergraduates to present their research ideas to the industry and academic community.</span></p><p><span style="color: rgb(33, 37, 41);">Further details of the event are at: </span><a href="http://aces.ce.pdn.ac.lk/escape20/" target="_blank" style="color: rgb(33, 37, 41);">http://aces.ce.pdn.ac.lk/escape20/</a></p>',
                "url" => "escape-2020",
                'event_type' => ['0', '1'],  
                "published_at" => "2024-08-27",
                "image" => "1724778446.jpg",
                "link_url" => "https://aces.ce.pdn.ac.lk/escape/2020/",
                "link_caption" => "Further details of the event",
                "start_at" => "2024-08-27 08:30:00",
                "end_at" => "2024-08-27 14:30:00",
                "location" => "Zoom Webinar",
                "enabled" => 1,
                "created_by" => 1,
                "created_at" => "2024-08-27 22:16:29",
                "updated_at" => "2024-08-27 22:38:31"
            ],
            [
                "id" => 4,
                "title" => "VIVACES 2020",
                "description" => '<p><span style="color: rgb(33, 37, 41);">Online social gathering of Department of Computer Engineering, University of Peradeniya was held on Friday 12th of June, 2020 with the participation of the Students and Staff successfully.</span></p>',
                "url" => "VIVACES-2020",
                'event_type' => ['1','2'],  

                "published_at" => "2024-08-27",
                "image" => "1724778453.jpg",
                "link_url" => null,
                "link_caption" => null,
                "start_at" => "2020-06-12 18:00:00",
                "end_at" => null,
                "location" => "Zoom (Online)",
                "enabled" => 1,
                "created_by" => 1,
                "created_at" => "2024-08-27 22:18:42",
                "updated_at" => "2024-08-27 22:41:47"
            ],
            [
                "id" => 5,
                "title" => "Hackers’ Club Developer Series",
                "description" => '<p>An Online Webinar series organized by Hackers’ Club to introduce some of the tools that you must have up on your sleeve to be a successful Developer/Engineer in the world of Computing. And also a chance to master some of them with the Developer Resources &amp; Materials shared by Hackers’ Club.</p><p>This Developer Series mainly focuses on front-end web development, and back-end development, for implementing a multi-platform solution for the real world problems. The Developer Series will be an invaluable chance for you to start the journey of mastering the Web Development world.</p><p><span style="color: rgb(33, 37, 41);">Series Timeline:</span></p><ul><li>Introduction to Git – Nov 03</li><li>Project collaboration with “GitHub” – Nov 17</li><li>Introduction to Node.js – Nov 24</li><li>MongoDB Express REST API with Node.js – Dec 01</li><li>Introduction to React – Dec 09</li></ul><p>For more information, please contact hackersclub@eng.pdn.ac.lk or visit <a href="https://hackersuop.github.io/" target="_blank">https://hackersuop.github.io</a></p>',
                "url" => "hackers-club-dev-series",
                'event_type' => ['0'],  

                "published_at" => "2024-08-27",
                "image" => "1724778465.png",
                "link_url" => null,
                "link_caption" => null,
                "start_at" => "2020-11-03 00:00:00",
                "end_at" => "2020-12-09 00:00:00",
                "location" => "Zoom (Online)",
                "enabled" => 1,
                "created_by" => 1,
                "created_at" => "2024-08-27 22:20:30",
                "updated_at" => "2024-08-27 22:37:45"
            ],
            [
                "id" => 6,
                "title" => "GSOC preparation mentoring program’21",
                "description" => '<p>Organized by the Department of Computer Engineering.</p>',
                "url" => "gsoc-preparation-mentoring-program21",
                'event_type' => ['0', '2'],  

                "published_at" => "2024-08-27",
                "image" => "1724778473.png",
                "link_url" => null,
                "link_caption" => null,
                "start_at" => "2021-04-01 14:00:00",
                "end_at" => null,
                "location" => "Zoom (Online)",
                "enabled" => 1,
                "created_by" => 1,
                "created_at" => "2024-08-27 22:23:44",
                "updated_at" => "2024-08-27 22:37:53"
            ]
        ];

        foreach ($events as $item) {
            Event::create($item);
        }

        $this->enableForeignKeys();
    }
}