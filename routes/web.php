<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboard;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\StoreController as SellerStoreController;
use App\Http\Controllers\Seller\BalanceController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;

// ================================================================
// PUBLIC ROUTES
// ================================================================

Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products',           [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search',    [ProductController::class, 'search'])->name('products.search');
Route::get('/products/{slug}',    [ProductController::class, 'show'])->name('products.show');

// Stores
Route::get('/stores/{slug}',      [StoreController::class, 'show'])->name('stores.show');

// Flash Sale
Route::get('/flash-sale',         [HomeController::class, 'flashSale'])->name('flash-sale.index');

// ================================================================
// AUTH ROUTES
// ================================================================

Route::middleware('guest')->group(function () {
    Route::get('/login',          [LoginController::class, 'showForm'])->name('login');
    Route::post('/login',         [LoginController::class, 'login']);
    Route::get('/register',       [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register',      [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ================================================================
// AUTHENTICATED ROUTES (Buyer & Seller)
// ================================================================

Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile',            [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile',            [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',   [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Addresses
    Route::get('/addresses',          [ProfileController::class, 'addresses'])->name('addresses.index');
    Route::post('/addresses',         [ProfileController::class, 'storeAddress'])->name('addresses.store');
    Route::put('/addresses/{id}',     [ProfileController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/addresses/{id}',  [ProfileController::class, 'destroyAddress'])->name('addresses.destroy');
    Route::post('/addresses/{id}/default', [ProfileController::class, 'setDefault'])->name('addresses.default');

    // Cart
    Route::get('/cart',               [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add',          [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{itemId}',      [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{itemId}',   [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart',            [CartController::class, 'clear'])->name('cart.clear');

    // Checkout
    Route::get('/checkout',           [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/direct',   [CheckoutController::class, 'direct'])->name('checkout.direct');
    Route::post('/checkout',          [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/{orderId}/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/{orderId}/pay',    [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::get('/checkout/success/{orderId}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Orders
    Route::get('/orders',             [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}',        [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel',[OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{id}/confirm-received', [OrderController::class, 'confirmReceived'])->name('orders.confirm-received');

    // Reviews
    Route::get('/orders/{orderId}/review/{itemId}', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/orders/{orderId}/review/{itemId}',[ReviewController::class, 'store'])->name('reviews.store');

    // Chat
    Route::get('/chat',               [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversationId}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{storeId}',    [ChatController::class, 'startOrShow'])->name('chat.store');
    Route::post('/chat/{conversationId}/send', [ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/{conversationId}/read', [ChatController::class, 'markRead'])->name('chat.read');

    // Wishlist
    Route::get('/wishlist',           [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{productId}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Notifications
    Route::get('/notifications',      [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all',  [NotificationController::class, 'readAll'])->name('notifications.read-all');

    // ================================================================
    // SELLER ROUTES
    // ================================================================

    Route::prefix('seller')->name('seller.')->middleware('seller')->group(function () {

        // Dashboard
        Route::get('/',               [SellerDashboard::class, 'index'])->name('dashboard');

        // Store setup
        Route::get('/store/setup',    [SellerStoreController::class, 'setup'])->name('store.setup');
        Route::post('/store/setup',   [SellerStoreController::class, 'storeSetup'])->name('store.setup.store');
        Route::get('/store/edit',     [SellerStoreController::class, 'edit'])->name('store.edit');
        Route::put('/store',          [SellerStoreController::class, 'update'])->name('store.update');

        // Products
        Route::get('/products',       [SellerProductController::class, 'index'])->name('products.index');
        Route::get('/products/create',[SellerProductController::class, 'create'])->name('products.create');
        Route::post('/products',      [SellerProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [SellerProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}',  [SellerProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [SellerProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{id}/toggle', [SellerProductController::class, 'toggle'])->name('products.toggle');

        // Orders
        Route::get('/orders',         [SellerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}',    [SellerOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/process', [SellerOrderController::class, 'process'])->name('orders.process');
        Route::post('/orders/{id}/ship',    [SellerOrderController::class, 'ship'])->name('orders.ship');

        // Balance
        Route::get('/balance',        [BalanceController::class, 'index'])->name('balance.index');
        Route::post('/balance/withdraw', [BalanceController::class, 'withdraw'])->name('balance.withdraw');

        // Chat (seller side)
        Route::get('/chat',           [ChatController::class, 'sellerInbox'])->name('chat.index');
    });
});

// ================================================================
// ADMIN ROUTES
// ================================================================

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/',               [AdminDashboard::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users',          [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}',     [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/toggle', [AdminUserController::class, 'toggle'])->name('users.toggle');

    // Stores
    Route::get('/stores',         [AdminStoreController::class, 'index'])->name('stores.index');
    Route::post('/stores/{id}/approve',  [AdminStoreController::class, 'approve'])->name('stores.approve');
    Route::post('/stores/{id}/suspend',  [AdminStoreController::class, 'suspend'])->name('stores.suspend');

    // Products
    Route::get('/products',       [AdminProductController::class, 'index'])->name('products.index');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{id}/toggle', [AdminProductController::class, 'toggle'])->name('products.toggle');
});

// ================================================================
// PAYMENT WEBHOOK (Midtrans)
// ================================================================

Route::post('/webhook/midtrans', [CheckoutController::class, 'webhook'])
    ->name('webhook.midtrans')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
