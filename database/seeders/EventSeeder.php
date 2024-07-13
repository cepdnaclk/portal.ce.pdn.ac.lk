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
        /*  [
                'title' => 'Eius quia blanditiis architecto exercitationem.',
                'description' => 'Nostrum qui qui ut deserunt dolores quaerat. Est quos sed ea quo placeat maxime. Sequi temporibus alias atque assumenda facere modi deleniti. Recusandae autem quia officia iste laudantium veritatis aut.',
                'image' => 'sample-image.jpg',
                'author' => 'Dr. Anna Murazik',
                'link_url' => 'https:\/\/www.murazik.org\/aut-et-quibusdam-molestias-consectetur-consequatur',
                'link_caption' => 'fugiat accusantium sit',
                'start_at' => now()->subWeek(),
                'end_at' => now()->subDay(),
                'location' => 'zoom',
            ],

            [
            'title' => 'event1',
            'description' => 'Nostrum qui qui ut deserunt dolores quaerat. Est quos sed ea quo placeat maxime. Sequi temporibus alias atque assumenda facere modi deleniti. Recusandae autem quia officia iste laudantium veritatis aut.',
            'image' => 'sample-image.jpg',
            'author' => 'ishara',
            'link_url' => 'https:\/\/www.murazik.org\/aut-et-quibusdam-molestias-consectetur-consequatur',
            'link_caption' => 'fugiat accusantium sit',
            'start_at' => now()->subWeek(),
            'end_at' => now()->subDay(),
            'location' => 'zoom',
            ]
*/
            // Add more events as needed
        ];

        foreach ($events as $item) {
            Event::create($item);
        }

        $this->enableForeignKeys();
    }
}
