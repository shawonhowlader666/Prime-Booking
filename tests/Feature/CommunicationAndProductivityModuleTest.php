<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\ChatMessage;
use App\Models\Hotel;
use App\Models\InternalEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunicationAndProductivityModuleTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;
    private User $user1;
    private User $user2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Communications Grand Hotel',
            'slug' => 'communications-grand-hotel',
            'code' => 'CGH-01',
        ]);

        $this->user1 = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Chief Concierge',
            'email' => 'concierge@cgh.com',
            'role' => 'SUPERADMIN',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->user2 = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Front Desk Manager',
            'email' => 'frontdesk@cgh.com',
            'role' => 'MANAGER',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    public function test_can_view_chat_and_send_messages(): void
    {
        $response = $this->actingAs($this->user1)->get('/chat.html');
        $response->assertStatus(200);
        $response->assertSee('Chat');
        $response->assertSee('Front Desk Manager');

        $sendResponse = $this->actingAs($this->user1)->post('/chat/send', [
            'receiver_id' => $this->user2->id,
            'message' => 'VIP guest arriving in Room 101 today.',
        ]);

        $sendResponse->assertStatus(302);
        $this->assertDatabaseHas('chat_messages', [
            'hotel_id' => $this->hotel->id,
            'sender_id' => $this->user1->id,
            'receiver_id' => $this->user2->id,
            'message' => 'VIP guest arriving in Room 101 today.',
        ]);
    }

    public function test_can_view_inbox_and_send_internal_email(): void
    {
        $response = $this->actingAs($this->user1)->get('/inbox.html');
        $response->assertStatus(200);
        $response->assertSee('Inbox');

        $sendResponse = $this->actingAs($this->user1)->post('/inbox/send', [
            'to' => 'reservations@cgh.com',
            'subject' => 'Group Booking Confirmation #7890',
            'body' => 'Please confirm booking folio details for the delegation.',
        ]);

        $sendResponse->assertStatus(302);
        $this->assertDatabaseHas('internal_emails', [
            'hotel_id' => $this->hotel->id,
            'sender_id' => $this->user1->id,
            'recipient_email' => 'reservations@cgh.com',
            'subject' => 'Group Booking Confirmation #7890',
        ]);
    }

    public function test_can_view_calendar_and_schedule_events(): void
    {
        $response = $this->actingAs($this->user1)->get('/calendar.html');
        $response->assertStatus(200);
        $response->assertSee('My Calendar');

        $eventResponse = $this->actingAs($this->user1)->post('/calendar/events', [
            'title' => 'Executive General Meeting',
            'event_type' => 'MEETING',
            'start_time' => now()->addDays(2)->toDateTimeString(),
            'description' => 'Quarterly occupancy and revenue review.',
        ]);

        $eventResponse->assertStatus(302);
        $this->assertDatabaseHas('calendar_events', [
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->user1->id,
            'title' => 'Executive General Meeting',
        ]);
    }

    public function test_can_view_blog_and_filemanager(): void
    {
        $blogResponse = $this->actingAs($this->user1)->get('/blog.html');
        $blogResponse->assertStatus(200);
        $blogResponse->assertSee('Blog List');

        $fmResponse = $this->actingAs($this->user1)->get('/filemanager.html');
        $fmResponse->assertStatus(200);
        $fmResponse->assertSee('File manager');
    }
}
