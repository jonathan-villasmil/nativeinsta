<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /** List all conversations for the logged-in user */
    public function index()
    {
        $user = auth()->user();

        $conversations = Conversation::with(['userOne', 'userTwo'])
            ->where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->latest('last_message_at')
            ->get()
            ->filter(fn ($c) => $c->messages()->exists()); // Only show started convos

        return view('messages.index', compact('conversations'));
    }

    /** Open or start a conversation with a specific user */
    public function show(User $user)
    {
        abort_if(auth()->id() === $user->id, 403);

        $conversation = Conversation::between(auth()->id(), $user->id);
        $messages = $conversation->messages()->with('sender')->get();

        // Mark all incoming messages as read
        $conversation->messages()
            ->where('sender_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.show', compact('conversation', 'messages', 'user'));
    }

    /** Send a message in a conversation */
    public function store(Request $request, User $user)
    {
        abort_if(auth()->id() === $user->id, 403);

        $request->validate(['body' => 'required|string|max:1000']);

        $conversation = Conversation::between(auth()->id(), $user->id);

        $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'body'      => $request->body,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // ── DM notification ──────────────────────────────────────
        // Use updateOrCreate so the notification is refreshed (read_at reset)
        // every time a new message arrives, re-activating the bell even if
        // the recipient had already read a previous notification from this sender.
        if ($user->id !== auth()->id()) {
            $user->appNotifications()->updateOrCreate(
                [
                    'actor_id'        => auth()->id(),
                    'type'            => 'message',
                    'notifiable_id'   => $conversation->id,
                    'notifiable_type' => \App\Models\Conversation::class,
                ],
                [
                    'read_at' => null,   // mark unread again on new message
                ]
            );
        }

        return back();
    }
}
