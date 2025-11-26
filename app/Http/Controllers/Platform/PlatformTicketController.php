<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\PlatformTicket;
use App\Models\PlatformTicketReply;
use Illuminate\Http\Request;

class PlatformTicketController extends Controller
{
    public function index()
    {
        $tickets = PlatformTicket::with(['tenant', 'user', 'assignedTo'])->latest()->paginate(15);
        return view('platform.tickets.index', compact('tickets'));
    }

    public function show(PlatformTicket $ticket)
    {
        $ticket->load(['tenant', 'user', 'assignedTo', 'replies.user']);
        return view('platform.tickets.show', compact('ticket'));
    }

    public function update(Request $request, PlatformTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,waiting,resolved,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($validated['status'] === 'resolved' && $ticket->status !== 'resolved') {
            $validated['resolved_at'] = now();
        }

        $ticket->update($validated);

        return back()->with('success', 'Ticket updated successfully.');
    }

    public function reply(Request $request, PlatformTicket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal' => 'boolean',
        ]);

        PlatformTicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        return back()->with('success', 'Reply added successfully.');
    }

    public function destroy(PlatformTicket $ticket)
    {
        $ticket->delete();
        return redirect()->route('platform.tickets.index')->with('success', 'Ticket deleted successfully.');
    }
}
