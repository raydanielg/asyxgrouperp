<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $tab = $request->get('tab', 'inbox');
        $search = $request->get('search');

        $inboxQuery = Message::with('sender')->where('recipient_id', $user->id)->latest();
        $sentQuery = Message::with('recipient')->where('sender_id', $user->id)->latest();

        if ($search) {
            $inboxQuery->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
            $sentQuery->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $inbox = $inboxQuery->paginate(15, ['*'], 'inbox_page');
        $sent = $sentQuery->paginate(15, ['*'], 'sent_page');

        return response()->json([
            'success' => true,
            'tab' => $tab,
            'inbox' => $inbox,
            'sent' => $sent,
            'unreadCount' => Message::where('recipient_id', $user->id)->where('status', 'unread')->count(),
            'inboxCount' => Message::where('recipient_id', $user->id)->count(),
            'sentCount' => Message::where('sender_id', $user->id)->count(),
            'users' => User::where('company_id', $user->company_id)->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'recipient_id' => [
                'required',
                Rule::exists('users', 'id')->where('company_id', $user->company_id),
            ],
            'subject' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'priority' => 'required|in:low,normal,high',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();
        $data['sender_id'] = auth()->id();
        $data['status'] = 'unread';
        $data['sent_at'] = now();

        $message = Message::create($data);
        $message->load('sender', 'recipient');

        return response()->json(['success' => true, 'message' => 'Message sent.', 'item' => $message]);
    }

    public function show(Message $message)
    {
        $user = auth()->user();
        if ($message->recipient_id !== $user->id && $message->sender_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if ($message->recipient_id === $user->id && $message->status === 'unread') {
            $message->update(['status' => 'read']);
        }

        $message->load('sender', 'recipient');
        return response()->json(['success' => true, 'item' => $message]);
    }

    public function destroy(Message $message)
    {
        $user = auth()->user();
        if ($message->recipient_id !== $user->id && $message->sender_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
        $message->delete();
        return response()->json(['success' => true, 'message' => 'Message deleted.']);
    }

    public function markStatus(Message $message, Request $request)
    {
        $user = auth()->user();
        if ($message->recipient_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
        $status = $request->validate(['status' => 'required|in:read,unread'])['status'];
        $message->update(['status' => $status]);
        return response()->json(['success' => true, 'message' => 'Status updated.', 'item' => $message]);
    }
}
