<?php

namespace App\Http\Controllers\Admin\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSupportController extends Controller
{
    // ── Index: List all support tickets ─────────────────────────
    public function index(Request $request)
    {
        $query = SupportTicket::with('user')
            ->withCount('replies');

        // Filters
        if ($status = $request->status) {
            $query->where('status', $status);
        }
        if ($priority = $request->priority) {
            $query->where('priority', $priority);
        }
        if ($source = $request->source) {
            $query->where('source', $source);
        }
        if ($search = trim($request->search ?? '')) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $tickets = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // KPI stats
        $stats = [
            'total'       => SupportTicket::count(),
            'open'        => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved'    => SupportTicket::whereIn('status', ['resolved', 'closed'])->count(),
        ];

        $admins = User::whereIn('role', ['admin', 'super_admin'])->select('id', 'name')->get();

        return view('admin.support.index', compact('tickets', 'stats', 'admins'));
    }

    // ── Show: Ticket detail + reply thread ──────────────────────
    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'replies.user', 'assignedTo']);

        // Mark as in_progress if it was open
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        $admins = User::whereIn('role', ['admin', 'super_admin'])->select('id', 'name')->get();

        return view('admin.support.show', compact('ticket', 'admins'));
    }

    // ── Reply to a ticket ───────────────────────────────────────
    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        SupportTicketReply::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => Auth::id(),
            'message'        => $request->message,
            'is_admin_reply' => true,
        ]);

        // Auto-move to in_progress when admin replies to an open ticket
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Reply sent to vendor successfully.');
    }

    // ── Update status ───────────────────────────────────────────
    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status'   => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        $data = ['status' => $request->status];

        if ($request->filled('priority')) {
            $data['priority'] = $request->priority;
        }

        if (in_array($request->status, ['resolved', 'closed']) && !$ticket->resolved_at) {
            $data['resolved_at'] = now();
        }

        $ticket->update($data);

        return back()->with('success', 'Ticket status updated to ' . ucfirst($request->status) . '.');
    }

    // ── Assign to admin ─────────────────────────────────────────
    public function assign(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket->update(['assigned_to' => $request->assigned_to]);

        return back()->with('success', 'Ticket assigned successfully.');
    }

    // ── Delete ticket ───────────────────────────────────────────
    public function destroy(SupportTicket $ticket)
    {
        $ticket->delete();
        return redirect()->route('admin.support.index')->with('success', 'Ticket deleted successfully.');
    }
}
