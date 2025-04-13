<?php

namespace Tests\Feature\Backend\Event;

use App\Domains\Auth\Models\User;
use App\Domains\Event\Models\Event;
use App\Http\Resources\EventResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class EventTest.
 */
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
        $user = $this->loginAsEditor();

        $response = $this->post('/dashboard/events/', [
            'title' => 'test event',
            'description' => 'Nostrum qui qui ut deserunt dolores quaerat. Est quos sed ea quo placeat maxime. Sequi temporibus alias atque assumenda facere modi deleniti. Recusandae autem quia officia iste laudantium veritatis aut.',
            'event_type' => ['0', '1'],
            'image' => 'sample-image.jpg',
            'created_by' => $user->id,
            'link_url' => 'http://runolfsdottir.biz/quia-provident-ut-ipsa-atque-et',
            'link_caption' => 'fugiat accusantium sit',
            'start_at' => '2024-10-06T18:04',
            'end_at' => '2024-07-12T18:04',
            'url' => 'https://ce.pdn.ac.lk/news/2004-10-10',
            'published_at' => '2024-10-10',
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
        $user = $this->loginAsEditor();
        $event = Event::factory()->create();

        $updateData = [
            'title' => 'Updated Event',
            'description' => 'This is an updated event description.',
            'event_type' => ['0', '2'],

            'image' => 'sample-image.jpg',
            'created_by' => $user->id,
            'link_url' => 'http://example.com',
            'link_caption' => 'eaque excepturi velit',
            'start_at' => '2024-09-06T18:04',
            'end_at' => '2024-07-12T18:04',
            'location' => 'zoom',
            'url' => 'www.uniqueurl1.com',
            'published_at' => '2024-01-10',
        ];

        $response = $this->put("/dashboard/events/{$event->id}", $updateData);
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
        $this->delete('/dashboard/events/' . $event->id);
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

    // Event Resource
    /** @test */
    public function test_event_resource_transforms_data_correctly()
    {
        // Create a user and an event
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'created_by' => $user->id,
            'event_type' => [1, 2],
        ]);

        // Mock the event type map
        Event::shouldReceive('eventTypeMap')
            ->andReturn([
                1 => 'Conference',
                2 => 'Workshop',
            ]);

        // Create the resource
        $resource = new EventResource($event);

        // Transform the resource into an array
        $data = $resource->toArray(request());

        // Assert the transformed data
        $this->assertEquals($event->id, $data['id']);
        $this->assertEquals($event->title, $data['title']);
        $this->assertEquals($event->description, $data['description']);
        $this->assertEquals($user->name, $data['author']);
        $this->assertEquals(URL::to($event->thumbURL()), $data['image']);
        $this->assertEquals($event->start_at, $data['start_at']);
        $this->assertEquals($event->end_at, $data['end_at']);
        $this->assertEquals(['Conference', 'Workshop'], $data['event_type']);
        $this->assertEquals($event->location, $data['location']);
        $this->assertEquals($event->link_url, $data['link_url']);
        $this->assertEquals($event->link_caption, $data['link_caption']);
        $this->assertEquals($event->published_at, $data['published_at']);
        $this->assertEquals($event->updated_at, $data['updated_at']);
    }
}
