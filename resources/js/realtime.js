// =============================================================
// resources/js/bootstrap.js
// Setup Laravel Echo dengan Reverb atau Pusher
// =============================================================

import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ─── Laravel Echo setup ───────────────────────────────────────
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Deteksi dari meta tag yang di-inject oleh Blade
const broadcastDriver = document.querySelector('meta[name="broadcast-driver"]')?.content ?? 'reverb';

if (broadcastDriver === 'pusher') {
    window.Echo = new Echo({
        broadcaster:  'pusher',
        key:          document.querySelector('meta[name="pusher-key"]')?.content,
        cluster:      document.querySelector('meta[name="pusher-cluster"]')?.content ?? 'ap1',
        forceTLS:     true,
    });
} else {
    // Laravel Reverb
    window.Echo = new Echo({
        broadcaster:     'reverb',
        key:             document.querySelector('meta[name="reverb-key"]')?.content,
        wsHost:          document.querySelector('meta[name="reverb-host"]')?.content ?? window.location.hostname,
        wsPort:          parseInt(document.querySelector('meta[name="reverb-port"]')?.content ?? '8080'),
        wssPort:         parseInt(document.querySelector('meta[name="reverb-port"]')?.content ?? '443'),
        forceTLS:        (document.querySelector('meta[name="reverb-scheme"]')?.content ?? 'http') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}


// =============================================================
// resources/js/realtime.js
// Module utama: notifikasi + chat realtime
// Import di layouts/app.blade.php via <script type="module">
// =============================================================

/**
 * NusaMarket Realtime Module
 * Mengelola:
 * 1. Notifikasi real-time (dropdown + badge)
 * 2. Chat real-time (pesan + unread count)
 * 3. Order status update
 */

class NusaRealtime {
    constructor(userId) {
        this.userId = userId;
        this.notifBadge    = document.getElementById('notif-badge');
        this.notifDropdown = document.getElementById('notif-list');
        this.chatBadges    = {};
        this.activeConvId  = null;

        this.listenUserChannel();
        this.listenPresence();
    }

    // ─── User private channel (notif + order update) ──────────
    listenUserChannel() {
        window.Echo.private(`user.${this.userId}`)

            // Notifikasi baru
            .listen('.notification.created', (data) => {
                this.addNotification(data);
                this.incrementNotifBadge();
                this.showToast(data.title, data.body, data.type);
            })

            // Order status berubah
            .listen('.order.status.updated', (data) => {
                this.updateOrderStatus(data);
            });
    }

    // ─── Subscribe ke conversation (dipanggil dari chat page) ──
    listenConversation(conversationId) {
        this.activeConvId = conversationId;

        window.Echo.private(`conversation.${conversationId}`)
            .listen('.message.sent', (data) => {
                // Jika bukan kita yang kirim, tampilkan pesan
                if (data.sender_id !== this.userId) {
                    this.appendMessage(data);
                    this.markConversationRead(conversationId);
                }
            });
    }

    leaveConversation(conversationId) {
        window.Echo.leave(`conversation.${conversationId}`);
        this.activeConvId = null;
    }

    // ─── Presence channel (online status) ─────────────────────
    listenPresence() {
        window.Echo.join('online-users')
            .here((users) => {
                users.forEach(u => this.setOnlineStatus(u.id, true));
            })
            .joining((user) => {
                this.setOnlineStatus(user.id, true);
            })
            .leaving((user) => {
                this.setOnlineStatus(user.id, false);
            });
    }

    // ─── DOM helpers ──────────────────────────────────────────

    addNotification(data) {
        if (!this.notifDropdown) return;

        const emptyEl = this.notifDropdown.querySelector('.notif-empty');
        if (emptyEl) emptyEl.remove();

        const item = document.createElement('a');
        item.className = 'dropdown-item fw-semibold bg-light-subtle notif-new';
        item.href = this.getNotifUrl(data);
        item.innerHTML = `
            <div class="small fw-semibold">${this.escHtml(data.title)}</div>
            <div class="text-muted" style="font-size:.75rem">${this.escHtml(data.body ?? '')}</div>
            <div class="text-muted" style="font-size:.72rem">${data.created_at}</div>
        `;

        // Insert di paling atas
        const firstItem = this.notifDropdown.querySelector('.dropdown-item');
        if (firstItem) {
            this.notifDropdown.insertBefore(item, firstItem);
        } else {
            this.notifDropdown.appendChild(item);
        }
    }

    incrementNotifBadge() {
        if (!this.notifBadge) return;
        const current = parseInt(this.notifBadge.textContent || '0');
        this.notifBadge.textContent = current + 1;
        this.notifBadge.style.display = 'inline';
    }

    appendMessage(data) {
        const box = document.getElementById('messagesBox');
        if (!box) return;

        const div = document.createElement('div');
        div.className = 'd-flex justify-content-start';
        div.innerHTML = `
            <img src="${data.sender_avatar}" class="rounded-circle align-self-end me-2 flex-shrink-0"
                 width="28" height="28">
            <div class="rounded-3 px-3 py-2 border bg-white" style="max-width:70%">
                ${data.type === 'image' && data.media_url
                    ? `<img src="${data.media_url}" class="img-fluid rounded" style="max-width:200px">`
                    : `<div style="font-size:.875rem">${this.escHtml(data.content)}</div>`
                }
                <div class="text-muted" style="font-size:.68rem;text-align:right;margin-top:2px">${data.created_at}</div>
            </div>
        `;

        box.appendChild(div);
        box.scrollTop = box.scrollHeight;

        // Update last message di sidebar
        this.updateConvSidebar(data.conversation_id, data.content || '[Gambar]');
    }

    updateConvSidebar(convId, lastMsg) {
        const convLink = document.querySelector(`[data-conv-id="${convId}"] .last-msg`);
        if (convLink) convLink.textContent = lastMsg;
    }

    updateOrderStatus(data) {
        // Update badge status di halaman orders jika ada
        const badge = document.querySelector(`[data-order-id="${data.order_id}"] .order-status-badge`);
        if (badge) {
            badge.textContent = data.status_label;
        }

        // Toast notifikasi
        this.showToast('Status Pesanan', data.status_label, 'order');
    }

    setOnlineStatus(userId, online) {
        document.querySelectorAll(`[data-user-id="${userId}"] .online-dot`).forEach(el => {
            el.style.background = online ? '#22c55e' : '#9ca3af';
            el.title = online ? 'Online' : 'Offline';
        });
    }

    showToast(title, body, type = 'info') {
        const icons = {
            new_message:  '💬',
            new_order:    '🛍️',
            order_shipped:'🚚',
            new_review:   '⭐',
            payment_success: '✅',
            store_approved:  '🎉',
            default:      '🔔',
        };

        const icon    = icons[type] ?? icons.default;
        const toastEl = document.getElementById('live-toast');

        if (!toastEl) {
            // Buat toast container jika belum ada
            const container = document.createElement('div');
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.id = 'toast-container';
            container.innerHTML = `
                <div id="live-toast" class="toast align-items-center text-bg-dark border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <span id="toast-icon"></span>
                            <strong id="toast-title" class="ms-1"></strong>
                            <div id="toast-body" class="text-white-50 small"></div>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            document.body.appendChild(container);
        }

        document.getElementById('toast-icon').textContent  = icon;
        document.getElementById('toast-title').textContent = title;
        document.getElementById('toast-body').textContent  = body ?? '';

        const toast = new bootstrap.Toast(document.getElementById('live-toast'), { delay: 5000 });
        toast.show();
    }

    getNotifUrl(data) {
        if (!data.data) return '#';
        if (data.data.order_id)        return `/orders/${data.data.order_id}`;
        if (data.data.conversation_id) return `/chat/${data.data.conversation_id}`;
        if (data.data.store_id)        return `/seller/store/edit`;
        return '#';
    }

    escHtml(str) {
        if (!str) return '';
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    markConversationRead(convId) {
        fetch(`/chat/${convId}/read`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
    }
}

// Export global
window.NusaRealtime = NusaRealtime;


// =============================================================
// resources/js/app.js
// Entry point Vite
// =============================================================

import './bootstrap';
import './realtime';
