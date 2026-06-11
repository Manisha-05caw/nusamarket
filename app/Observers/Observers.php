<?php
// =============================================================
// app/Observers/NotificationObserver.php
// =============================================================
namespace App\Observers;

use App\Events\NotificationCreated;
use App\Models\Notification;

class NotificationObserver
{
    public function created(Notification $notification): void
    {
        // Broadcast real-time ke user channel
        broadcast(new NotificationCreated($notification));
    }
}


// =============================================================
// app/Observers/OrderObserver.php
// =============================================================

namespace App\Observers;

use App\Events\OrderStatusUpdated;
use App\Models\Order;

class OrderObserver
{
    public function updated(Order $order): void
    {
        // Broadcast hanya jika status berubah
        if ($order->isDirty('status')) {
            broadcast(new OrderStatusUpdated($order));
        }
    }
}
