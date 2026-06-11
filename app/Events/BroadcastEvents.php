<?php
// =============================================================
// app/Events/MessageSent.php
// Dipanggil saat pesan baru dikirim di chat
// =============================================================
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id'       => $this->message->sender_id,
            'sender_name'     => $this->message->sender->name,
            'sender_avatar'   => 'https://ui-avatars.com/api/?name=' . urlencode($this->message->sender->name) . '&background=2E75B6&color=fff&size=32',
            'content'         => $this->message->content,
            'type'            => $this->message->type,
            'media_url'       => $this->message->media_url,
            'created_at'      => $this->message->created_at->format('H:i'),
            'created_at_full' => $this->message->created_at->toIso8601String(),
        ];
    }
}


// =============================================================
// app/Events/NotificationCreated.php
// Dipanggil saat notifikasi baru dibuat untuk user
// =============================================================

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public \App\Models\Notification $notification) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->notification->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->notification->id,
            'type'       => $this->notification->type,
            'title'      => $this->notification->title,
            'body'       => $this->notification->body,
            'data'       => $this->notification->data,
            'created_at' => $this->notification->created_at->diffForHumans(),
        ];
    }
}


// =============================================================
// app/Events/OrderStatusUpdated.php
// Dipanggil saat status order berubah
// =============================================================

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public \App\Models\Order $order) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->order->buyer_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id'     => $this->order->id,
            'status'       => $this->order->status,
            'status_label' => $this->order->status_label,
        ];
    }
}


// =============================================================
// app/Events/UserOnlineStatus.php
// Presence channel — siapa saja yang sedang online
// =============================================================

class UserOnlineStatus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public \App\Models\User $user, public bool $online) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel('online-users')];
    }

    public function broadcastAs(): string
    {
        return $this->online ? 'user.online' : 'user.offline';
    }

    public function broadcastWith(): array
    {
        return ['user_id' => $this->user->id];
    }
}
