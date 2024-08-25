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
                'title' => 'GSOC preparation mentoring program’21',
                'description' => 'Organized by the Department of Computer Engineering.',
                'image' => 'https://www.ce.pdn.ac.lk/events/images/gsoc-preparation-mentoring-program21.png',
                'user_id' => '4',
                'link_url' => 'https://www.ce.pdn.ac.lk/events/2021-05-07-gsoc-preparation-mentoring-program21.html',
                'link_caption' => 'GSOC preparation',
                'start_at' => '2021-04-01 20:00:00',
                'location' => 'Zoom',
            ],
            [
                'title' => 'Hackers’ Club Developer Series',
                'description' => 'An Online Webinar series organized by Hackers’ Club to introduce some of the tools that you must have up on your sleeve to be a successful Developer/Engineer in the world of Computing. And also a chance to master some of them with the Developer Resources & Materials shared by Hackers’ Club.
                                  This Developer Series mainly focuses on front-end web development, and back-end development, for implementing a multi-platform solution for the real world problems. The Developer Series will be an invaluable chance for you to start the journey of mastering the Web Development world.',
                'image' => 'https://www.ce.pdn.ac.lk/events/images/hackers-club-dev-series.png',
                'user_id' => '4',
                'link_url' => 'https://hackersuop.github.io/',
                'link_caption' => 'visit',
                'start_at' => '2023-11-03 17:00:00',
                'location' => 'Zoom',
            ],
            [
                'title' => 'VIVACES 2020',
                'description' => 'Online social gathering of Department of Computer Engineering, University of Peradeniya was held on Friday 12th of June, 2020 with the participation of the Students and Staff successfully.',
                'image' => 'https://www.ce.pdn.ac.lk/events/images/vivaces-2020.jpg',
                'user_id' => '4',
                'link_url' => 'https://www.ce.pdn.ac.lk/events/2020-06-18-vivaces.html',
                'link_caption' => 'VIVACES 2020',
                'start_at' => '2020-06-12 18:00:00',
                'location' => 'Department of Computer Engineering, UoP',
            ],
            [
                'title' => 'ESCAPE - 2020',
                'description' => 'EscaPe is the annual project symposium of the Department of Computer Engineering, University of Peradeniya. It will present the research projects of the undergraduates of the Department of Computer Engineering. ESCaPe 2020 is the 5th symposium that is organized by the department and this time the symposium is open for a broader audience and aims to build a platform for the undergraduates to present their research ideas to the industry and academic community.',
                'image' => 'https://www.ce.pdn.ac.lk/events/images/escape-2020.jpg',
                'user_id' => '4',
                'link_url' => 'http://aces.ce.pdn.ac.lk/escape20/',
                'link_caption' => 'visit',
                'start_at' => '2020-05-29 08:30:00',
                'location' => 'Zoom (Online)',
            ],

            // Add more events as needed
        ];

        foreach ($events as $item) {
            Event::create($item);
        }

        $this->enableForeignKeys();
    }
}
