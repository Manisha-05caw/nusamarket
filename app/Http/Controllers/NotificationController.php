<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(20);
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);
        return view('pages.notifications.index', compact('notifications'));
    }

    public function markRead(string $id)
    {
        $notif = Auth::user()->notifications()->findOrFail($id);
        $notif->update(['is_read' => true, 'read_at' => now()]);
        $data = $notif->data ?? [];
        if (!empty($data['order_id']))        return redirect()->route('orders.show', $data['order_id']);
        if (!empty($data['conversation_id'])) return redirect()->route('chat.show', $data['conversation_id']);
        return back();
    }

    public function readAll()
    {
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);
        return back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }
}
