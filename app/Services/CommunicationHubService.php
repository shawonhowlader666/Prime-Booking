<?php

namespace App\Services;

use App\Models\CalendarEvent;
use App\Models\ChatMessage;
use App\Models\InternalEmail;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CommunicationHubService
{
    /**
     * Get staff contacts and recent conversation stream.
     */
    public function getChatData(int $hotelId, int $userId): array
    {
        $contacts = User::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->where('id', '!=', $userId)
            ->where('is_active', true)
            ->get();

        $activeContact = $contacts->first();

        $messages = ChatMessage::withoutGlobalScopes()
            ->with(['sender', 'receiver'])
            ->where('hotel_id', $hotelId)
            ->where(function ($q) use ($userId, $activeContact) {
                if ($activeContact) {
                    $q->where(fn($sub) => $sub->where('sender_id', $userId)->where('receiver_id', $activeContact->id))
                      ->orWhere(fn($sub) => $sub->where('sender_id', $activeContact->id)->where('receiver_id', $userId));
                } else {
                    $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
                }
            })
            ->latest()
            ->take(50)
            ->get()
            ->reverse();

        return [
            'contacts' => $contacts,
            'activeContact' => $activeContact,
            'messages' => $messages,
        ];
    }

    /**
     * Store new chat message.
     */
    public function sendChatMessage(int $hotelId, int $senderId, ?int $receiverId, string $message): ChatMessage
    {
        return ChatMessage::create([
            'hotel_id' => $hotelId,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    /**
     * Get inbox emails.
     */
    public function getInboxEmails(int $hotelId, ?string $folder = 'inbox'): Collection
    {
        return InternalEmail::withoutGlobalScopes()
            ->with('sender')
            ->where('hotel_id', $hotelId)
            ->when($folder === 'trash', fn($q) => $q->where('is_trash', true), fn($q) => $q->where('is_trash', false))
            ->latest()
            ->get();
    }

    /**
     * Store and send email.
     */
    public function sendEmail(int $hotelId, int $senderId, string $to, string $subject, string $body): InternalEmail
    {
        return InternalEmail::create([
            'hotel_id' => $hotelId,
            'sender_id' => $senderId,
            'recipient_email' => $to,
            'subject' => $subject,
            'body' => $body,
            'is_starred' => false,
            'is_trash' => false,
        ]);
    }

    /**
     * Get calendar events for property and user.
     */
    public function getCalendarEvents(int $hotelId, int $userId): Collection
    {
        return CalendarEvent::withoutGlobalScopes()
            ->with('user')
            ->where('hotel_id', $hotelId)
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Store calendar event.
     */
    public function createCalendarEvent(int $hotelId, int $userId, array $data): CalendarEvent
    {
        return CalendarEvent::create([
            'hotel_id' => $hotelId,
            'user_id' => $userId,
            'title' => $data['title'],
            'event_type' => $data['event_type'] ?? 'MEETING',
            'start_time' => $data['start_time'] ?? now(),
            'end_time' => $data['end_time'] ?? now()->addHour(),
            'description' => $data['description'] ?? null,
        ]);
    }
}
