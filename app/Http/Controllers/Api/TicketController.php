<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HelpdeskTicket;
use App\Models\HelpdeskReply;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        return response()->json(HelpdeskTicket::with('category', 'assignedTo', 'replies')->latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:helpdesk_categories,id',
            'priority' => 'required|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        $data['ticket_number'] = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
        $data['status'] = 'open';
        $data['created_by'] = auth()->id();
        $ticket = HelpdeskTicket::create($data);
        return response()->json($ticket, 201);
    }

    public function show(HelpdeskTicket $ticket)
    {
        return response()->json($ticket->load('category', 'assignedTo', 'replies.user'));
    }

    public function update(Request $request, HelpdeskTicket $ticket)
    {
        $data = $request->validate([
            'status' => 'in:open,in_progress,resolved,closed',
            'priority' => 'in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        $ticket->update($data);
        return response()->json($ticket);
    }

    public function destroy(HelpdeskTicket $ticket)
    {
        $ticket->delete();
        return response()->json(['message' => 'Ticket deleted']);
    }

    public function reply(Request $request, HelpdeskTicket $ticket)
    {
        $data = $request->validate(['message' => 'required|string']);
        $reply = HelpdeskReply::create([
            'helpdesk_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $data['message'],
        ]);
        return response()->json($reply, 201);
    }
}
