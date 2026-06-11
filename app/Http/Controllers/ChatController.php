<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Notification;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = Conversation::where('buyer_id', Auth::id())
            ->with(['store','latestMessage'])
            ->orderByDesc('last_message_at')->get();
        $conv = null;
        return view('pages.chat.index', compact('conversations', 'conv'));
    }

    public function show(string $conversationId)
    {
        $conv = Conversation::where(fn($q) => $q->where('buyer_id', Auth::id())->orWhere('seller_id', Auth::id()))
            ->with(['messages.sender','store','buyer','seller','product'])
            ->findOrFail($conversationId);
        if ($conv->buyer_id === Auth::id()) $conv->update(['buyer_unread' => 0]);
        else $conv->update(['seller_unread' => 0]);
        $conversations = Conversation::where('buyer_id', Auth::id())
            ->orWhere('seller_id', Auth::id())
            ->with(['store','latestMessage'])
            ->orderByDesc('last_message_at')->get();
        return view('pages.chat.index', compact('conv', 'conversations'));
    }

    public function startOrShow(Request $request, string $storeId)
    {
        $store = Store::findOrFail($storeId);
        $conv  = Conversation::firstOrCreate(
            ['buyer_id' => Auth::id(), 'store_id' => $storeId],
            ['seller_id' => $store->owner_id, 'product_id' => $request->get('product')]
        );
        return redirect()->route('chat.show', $conv->id);
    }

    public function send(Request $request, string $conversationId)
    {
        $request->validate(['content' => 'required|string|max:2000']);
        $conv = Conversation::where(fn($q) => $q->where('buyer_id', Auth::id())->orWhere('seller_id', Auth::id()))
            ->findOrFail($conversationId);
        $conv->messages()->create([
            'sender_id' => Auth::id(),
            'content'   => $request->content,
            'type'      => 'text',
        ]);
        return $request->wantsJson() ? response()->json(['ok' => true]) : back();
    }

    public function markRead(string $conversationId)
    {
        $conv = Conversation::findOrFail($conversationId);
        if ($conv->buyer_id === Auth::id()) $conv->update(['buyer_unread' => 0]);
        else $conv->update(['seller_unread' => 0]);
        return response()->json(['ok' => true]);
    }

    public function sellerInbox()
    {
        $storeIds = Auth::user()->stores->pluck('id');
        $conversations = Conversation::whereIn('store_id', $storeIds)
            ->with(['buyer','latestMessage','store'])
            ->orderByDesc('last_message_at')->get();
        return view('pages.chat.seller', compact('conversations'));
    }
}
