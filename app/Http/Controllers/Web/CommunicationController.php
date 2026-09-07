<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\CommunicationHubService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunicationController extends Controller
{
    public function __construct(
        protected CommunicationHubService $commService
    ) {}

    private function getHotelId(): int
    {
        return auth()->user()?->hotel_id ?? Hotel::value('id') ?? 1;
    }

    public function chat(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $userId = auth()->id() ?? 1;
        $chatData = $this->commService->getChatData($hotelId, $userId);

        return view('pages.chat', $chatData);
    }

    public function sendChatMessage(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $userId = auth()->id() ?? 1;

        $validated = $request->validate([
            'receiver_id' => 'nullable|integer',
            'message' => 'required|string|max:1000',
        ]);

        $this->commService->sendChatMessage($hotelId, $userId, $validated['receiver_id'] ?? null, $validated['message']);

        return redirect()->back()->with('success', 'Message sent successfully.');
    }

    public function inbox(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $folder = $request->get('folder', 'inbox');
        $emails = $this->commService->getInboxEmails($hotelId, $folder);

        return view('pages.inbox', compact('emails', 'folder'));
    }

    public function sendEmail(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $userId = auth()->id() ?? 1;

        $validated = $request->validate([
            'to' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $this->commService->sendEmail($hotelId, $userId, $validated['to'], $validated['subject'], $validated['body']);

        return redirect()->back()->with('success', 'Email dispatched successfully.');
    }

    public function calendar(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $userId = auth()->id() ?? 1;
        $events = $this->commService->getCalendarEvents($hotelId, $userId);

        return view('pages.calendar', compact('events'));
    }

    public function createCalendarEvent(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();
        $userId = auth()->id() ?? 1;

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'event_type' => 'nullable|string|max:50',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $this->commService->createCalendarEvent($hotelId, $userId, $validated);

        return redirect()->back()->with('success', 'Event scheduled successfully.');
    }

    public function blog(): View
    {
        return view('pages.blog');
    }

    public function filemanager(): View
    {
        return view('pages.filemanager');
    }
}
