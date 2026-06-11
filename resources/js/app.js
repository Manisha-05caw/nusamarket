import './bootstrap';

// NusaRealtime — load hanya jika user sudah login (ada meta tag broadcast-driver)
if (document.querySelector('meta[name="broadcast-driver"]')) {
    import('./realtime.js').then(() => {
        // Inisialisasi setelah DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initRealtime);
        } else {
            initRealtime();
        }
    });
}

function initRealtime() {
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    if (!userId || typeof NusaRealtime === 'undefined') return;

    window._nusaRT = new NusaRealtime(userId);

    // Jika di halaman chat, subscribe ke conversation aktif
    const convId = document.querySelector('meta[name="active-conv"]')?.content;
    if (convId) {
        window._nusaRT.listenConversation(convId);
        window.addEventListener('beforeunload', () => {
            window._nusaRT.leaveConversation(convId);
        });
    }
}
