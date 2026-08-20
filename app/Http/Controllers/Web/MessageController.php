<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Property;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $company = config('company');
        $userId  = auth()->id();

        $messages = $userId
            ? Message::where('sender_id', $userId)
                ->orWhere('receiver_id', $userId)
                ->with(['sender', 'receiver', 'property'])
                ->latest()
                ->paginate(15)
            : collect();

        return view('pages.messages', compact('company', 'messages'));
    }

    public function store(Request $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            return back()->with('error', 'Please sign in to send a message.');
        }

        $validated = $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'subject'     => 'nullable|string|max:255',
            'message'     => 'required|string|min:5',
        ]);

        $receiverId = null;
        if (!empty($validated['property_id'])) {
            $property = Property::find($validated['property_id']);
            $receiverId = $property?->vendor_id;
        }

        Message::create([
            'sender_id'   => $userId,
            'receiver_id' => $receiverId,
            'property_id' => $validated['property_id'] ?? null,
            'subject'     => $validated['subject'] ?? 'Guest Inquiry',
            'message'     => $validated['message'],
            'is_read'     => false,
        ]);

        return back()->with('success', 'Your message has been sent successfully to the property host!');
    }
}
