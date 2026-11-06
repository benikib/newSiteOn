<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim((string) $request->query('q', ''));
        $contacts = collect();

        if ($search !== '') {
            $contacts = User::query()
                ->where('is_approved', true)
                ->whereKeyNot($user->id)
                ->where('name', 'like', "%{$search}%")
                ->orderBy('name')
                ->limit(20)
                ->get();
        }

        $conversationIds = $user->messagesSent()
            ->select('receiver_id as user_id')
            ->union($user->messagesReceived()->select('sender_id as user_id'))
            ->pluck('user_id');
        $conversations = User::query()
            ->whereIn('id', $conversationIds)
            ->where('is_approved', true)
            ->orderBy('name')
            ->get();

        $activeContact = null;
        $messages = collect();
        $contactId = $request->integer('with');

        if ($contactId) {
            abort_if($contactId === $user->id, 404);
            $activeContact = User::query()
                ->where('is_approved', true)
                ->findOrFail($contactId);

            $messages = Message::query()
                ->where(function ($query) use ($user, $activeContact) {
                    $query->where(function ($pair) use ($user, $activeContact) {
                        $pair->where('sender_id', $user->id)
                            ->where('receiver_id', $activeContact->id);
                    })->orWhere(function ($pair) use ($user, $activeContact) {
                        $pair->where('sender_id', $activeContact->id)
                            ->where('receiver_id', $user->id);
                    });
                })
                ->with('sender:id,name')
                ->latest('id')
                ->paginate(50)
                ->withQueryString();

            $unreadMessageIds = $messages->getCollection()
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->pluck('id');
            if ($unreadMessageIds->isNotEmpty()) {
                Message::whereKey($unreadMessageIds)->update(['read_at' => now()]);
            }

            $messages->setCollection($messages->getCollection()->reverse()->values());
        }

        return view('messages.index', compact(
            'contacts',
            'conversations',
            'activeContact',
            'messages',
            'search'
        ));
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        abort_if($user->id === $request->user()->id || ! $user->is_approved, 404);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $request->user()->messagesSent()->create([
            'receiver_id' => $user->id,
            'body' => $data['body'],
        ]);

        return redirect()->route('messages.index', ['with' => $user->id])
            ->with('success', 'Message envoyé.');
    }
}
