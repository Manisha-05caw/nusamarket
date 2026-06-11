<?php
// =============================================================
// routes/channels.php
// Otorisasi siapa yang boleh subscribe ke channel private/presence
// =============================================================

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;
use App\Models\User;

// ─── Private: user channel (notifikasi & order update) ───────
Broadcast::channel('user.{userId}', function (User $user, string $userId) {
    return $user->id === $userId;
});

// ─── Private: conversation channel (chat) ─────────────────────
Broadcast::channel('conversation.{conversationId}', function (User $user, string $conversationId) {
    $conv = Conversation::find($conversationId);
    if (!$conv) return false;

    // Hanya buyer atau seller dari conversation ini boleh subscribe
    return in_array($user->id, [$conv->buyer_id, $conv->seller_id]);
});

// ─── Presence: online users ───────────────────────────────────
Broadcast::channel('online-users', function (User $user) {
    return [
        'id'     => $user->id,
        'name'   => $user->name,
        'avatar' => $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name),
    ];
});
