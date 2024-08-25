<?php

namespace Tests\Feature\Backend\Event;

use App\Domains\Event\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_event_editor_can_access_the_list_event_page()
    {
        $this->loginAsEditor();
        $this->get('/dashboard/events/')->assertOk();
    }

    /** @test */
    public function an_event_editor_can_access_the_create_event_page()
    {
        $this->loginAsEditor();
        $this->get('/dashboard/events/create')->assertOk();
    }

    /** @test */
    public function an_event_editor_can_access_the_delete_event_page()
    {
        $this->loginAsEditor();
        $event = Event::factory()->create();
        $this->get('/dashboard/events/delete/' . $event->id)->assertOk();
    }

    /** @test */
    public function event_can_be_created()
    {
        $this->loginAsEditor();

        $response = $this->post('/dashboard/event/', [
            'title' => 'test event',
            'description' => 'Nostrum qui qui ut deserunt dolores quaerat. Est quos sed ea quo placeat maxime. Sequi temporibus alias atque assumenda facere modi deleniti. Recusandae autem quia officia iste laudantium veritatis aut.',
            'image' => 'sample-image.jpg',
            'user_id' => '4',
            'link_url' => 'http://runolfsdottir.biz/quia-provident-ut-ipsa-atque-et',
            'link_caption' => 'fugiat accusantium sit',
            'start_at' => '2024-07-06 18:04',
            'end_at' => '2024-07-12 18:04',
            'location' => 'zoom',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('events', [
            'title' => 'test event',
        ]);
    }

    /** @test */
    public function event_can_be_updated()
    {
        $this->loginAsEditor();
        $event = Event::factory()->create();

        $updateData = [
            'title' => 'Updated Event',
            'description' => 'This is an updated event description.',
            'image' => 'sample-image.jpg',
            'user_id' => '4',
            'link_url' => 'http://example.com',
            'link_caption' => 'eaque excepturi velit',
            'start_at' => '2024-07-06 18:04',
            'end_at' => '2024-07-12 18:04',
            'location' => 'zoom',
        ];

        $response = $this->put("/dashboard/event/{$event->id}", $updateData);
        $response->assertStatus(302);

        $this->assertDatabaseHas('events', [
            'title' => 'Updated Event',
        ]);
    }

    /** @test */
    public function event_can_be_deleted()
    {
        $this->loginAsEditor();
        $event = Event::factory()->create();
        $this->delete('/dashboard/event/' . $event->id);
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    /** @test */
    public function unauthorized_user_cannot_access_event_pages()
    {
        $event = Event::factory()->create();

        $this->get('/dashboard/events/')->assertRedirect('/login');
        $this->get('/dashboard/events/create')->assertRedirect('/login');
        $this->get('/dashboard/events/delete/' . $event->id)->assertRedirect('/login');
        $this->post('/dashboard/events/')->assertRedirect('/login');
        $this->put("/dashboard/events/{$event->id}")->assertRedirect('/login');
        $this->delete('/dashboard/events/' . $event->id)->assertRedirect('/login');
    }
}
