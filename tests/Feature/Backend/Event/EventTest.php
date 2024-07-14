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
        $this->loginAsEventEditor();
        $this->get('/dashboard/event/')->assertOk();
    }

    /** @test */
    public function an_event_editor_can_access_the_create_event_page()
    {
        $this->loginAsEventEditor();
        $this->get('/dashboard/event/create')->assertOk();
    }

    /** @test */
    public function an_event_editor_can_access_the_delete_event_page()
    {
        $this->loginAsEventEditor();
        $event = Event::factory()->create();
        $this->get('/dashboard/event/delete/' . $event->id)->assertOk();
    }

    /** @test */
   /* public function create_event_requires_validation()
    {
        $this->loginAsEventEditor();
        $response = $this->post('/dashboard/event');
        $response->assertSessionHasErrors(['title', 'description', 'image', 'link_url', 'link_caption']);
    }

    /** @test *//*
    public function update_event_requires_validation()
    {
        $this->loginAsEventEditor();
        $event = Event::factory()->create();

        $response = $this->put("/dashboard/event/{$event->id}", []);
        $response->assertSessionHasErrors(['title', 'description', 'image', 'link_url', 'link_caption']);
    }
*/
    /** @test */
    /** @test */
    /** @test */
    public function event_can_be_created()
    {
        $this->loginAsEventEditor();

        $response = $this->post('/dashboard/event/', [
            'title' => 'test event',
            'description' => 'Nostrum qui qui ut deserunt dolores quaerat. Est quos sed ea quo placeat maxime. Sequi temporibus alias atque assumenda facere modi deleniti. Recusandae autem quia officia iste laudantium veritatis aut.',
            'image' => 'sample-image.jpg',
            'author' => 'ishara kmll',
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
        $this->loginAsEventEditor();
        $event = Event::factory()->create();

        $updateData = [
            'title' => 'Updated Event',
            'description' => 'This is an updated event description.',
            'image' => 'sample-image.jpg',
            'author' => 'ishara',
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
        $this->loginAsEventEditor();
        $event = Event::factory()->create();
        $this->delete('/dashboard/event/' . $event->id);
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    /** @test */
    public function unauthorized_user_cannot_access_event_pages()
    {
        $event = Event::factory()->create();

        $this->get('/dashboard/event/')->assertRedirect('/login');
        $this->get('/dashboard/event/create')->assertRedirect('/login');
        $this->get('/dashboard/event/delete/' . $event->id)->assertRedirect('/login');
        $this->post('/dashboard/event')->assertRedirect('/login');
        $this->put("/dashboard/event/{$event->id}")->assertRedirect('/login');
        $this->delete('/dashboard/event/' . $event->id)->assertRedirect('/login');
    }
}
