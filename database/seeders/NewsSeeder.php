<?php

namespace Database\Seeders;

use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;
use App\Domains\News\Models\News;

class NewsSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->disableForeignKeys();
        $this->truncateMultiple([
            'news',
        ]);
        // Create some sample news items
        $news = [
            // [
            //     'title' => 'Eius quia blanditiis architecto exercitationem.',
            //     'description' => 'Nostrum qui qui ut deserunt dolores quaerat. Est quos sed ea quo placeat maxime. Sequi temporibus alias atque assumenda facere modi deleniti. Recusandae autem quia officia iste laudantium veritatis aut.',
            //     'image' => 'sample-image.jpg',
            //     'author' => 'Dr. Anna Murazik',
            //     'link_url' => 'https:\/\/www.murazik.org\/aut-et-quibusdam-molestias-consectetur-consequatur',
            //     'link_caption' => 'fugiat accusantium sit',
            // ],

            // Add more news items as needed
        ];

        foreach ($news as $item) {
            News::create($item);
        }

        $this->enableForeignKeys();
    }
}
