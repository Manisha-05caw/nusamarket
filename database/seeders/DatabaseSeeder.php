<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            UserSeeder::class,
            StoreSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            ReviewSeeder::class,
            ChatSeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}


// =============================================================
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Admin
            [
                'id'       => Str::uuid(),
                'name'     => 'Admin NusaMarket',
                'email'    => 'admin@nusamarket.id',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'is_verified' => true,
                'is_active'   => true,
            ],
            // Sellers
            [
                'id'       => Str::uuid(),
                'name'     => 'Dewi Rahayu',
                'email'    => 'dewi@seller.com',
                'password' => Hash::make('password'),
                'role'     => 'seller',
                'phone'    => '081234567890',
                'is_verified' => true,
                'is_active'   => true,
            ],
            [
                'id'       => Str::uuid(),
                'name'     => 'Budi Santoso',
                'email'    => 'budi@seller.com',
                'password' => Hash::make('password'),
                'role'     => 'seller',
                'phone'    => '082345678901',
                'is_verified' => true,
                'is_active'   => true,
            ],
            // Buyers
            [
                'id'       => Str::uuid(),
                'name'     => 'Raka Pratama',
                'email'    => 'raka@buyer.com',
                'password' => Hash::make('password'),
                'role'     => 'buyer',
                'phone'    => '083456789012',
                'is_verified' => true,
                'is_active'   => true,
            ],
            [
                'id'       => Str::uuid(),
                'name'     => 'Sari Indah',
                'email'    => 'sari@buyer.com',
                'password' => Hash::make('password'),
                'role'     => 'buyer',
                'phone'    => '084567890123',
                'is_verified' => true,
                'is_active'   => true,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert(array_merge($user, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Addresses for buyers
        $buyerIds = DB::table('users')->where('role', 'buyer')->pluck('id');
        foreach ($buyerIds as $userId) {
            DB::table('addresses')->insert([
                'id'          => Str::uuid(),
                'user_id'     => $userId,
                'label'       => 'Rumah',
                'recipient'   => DB::table('users')->where('id', $userId)->value('name'),
                'phone'       => '08' . rand(100000000, 999999999),
                'address_line'=> 'Jl. Sudirman No. ' . rand(1, 100),
                'city'        => collect(['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta'])->random(),
                'province'    => 'Jawa Barat',
                'postal_code' => (string) rand(10000, 99999),
                'is_default'  => true,
                'created_at'  => now(),
            ]);
        }
    }
}


// =============================================================
class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = DB::table('users')->where('role', 'seller')->get();

        $stores = [
            [
                'name'        => 'Batik Java Official',
                'slug'        => 'batik-java-official',
                'description' => 'Toko batik premium langsung dari pengrajin Jogja. Kualitas terjamin, motif otentik.',
                'city'        => 'Yogyakarta',
                'province'    => 'DI Yogyakarta',
                'rating_avg'  => 4.8,
                'total_sales' => 1250,
                'status'      => 'active',
            ],
            [
                'name'        => 'TechHub Store',
                'slug'        => 'techhub-store',
                'description' => 'Elektronik & aksesoris terpercaya. Bergaransi resmi, pengiriman aman.',
                'city'        => 'Jakarta',
                'province'    => 'DKI Jakarta',
                'rating_avg'  => 4.6,
                'total_sales' => 3400,
                'status'      => 'active',
            ],
        ];

        foreach ($stores as $i => $store) {
            $storeId = Str::uuid();
            DB::table('stores')->insert(array_merge($store, [
                'id'           => $storeId,
                'owner_id'     => $sellers[$i]->id,
                'total_reviews'=> rand(200, 800),
                'created_at'   => now()->subMonths(rand(3, 12)),
                'updated_at'   => now(),
            ]));

            // Init seller balance
            DB::table('seller_balances')->insert([
                'id'          => Str::uuid(),
                'store_id'    => $storeId,
                'available'   => rand(500000, 5000000),
                'pending'     => rand(100000, 500000),
                'total_earned'=> rand(5000000, 50000000),
                'updated_at'  => now(),
            ]);
        }
    }
}


// =============================================================
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $stores = DB::table('stores')->get()->keyBy('slug');

        $products = [
            // Batik Java
            ['store' => 'batik-java-official', 'name' => 'Kemeja Batik Pria Motif Parang', 'category' => 'fashion_pria',   'price' => 185000, 'sold' => 423],
            ['store' => 'batik-java-official', 'name' => 'Dress Batik Wanita Modern',      'category' => 'fashion_wanita', 'price' => 220000, 'sold' => 312],
            ['store' => 'batik-java-official', 'name' => 'Kain Batik Tulis Jogja 2m',      'category' => 'fashion_wanita', 'price' => 350000, 'sold' => 187],
            ['store' => 'batik-java-official', 'name' => 'Set Batik Couple Kondangan',     'category' => 'fashion_wanita', 'price' => 450000, 'sold' => 256],
            // TechHub
            ['store' => 'techhub-store', 'name' => 'Earphone TWS Bluetooth 5.3',          'category' => 'elektronik', 'price' => 129000, 'sold' => 1203],
            ['store' => 'techhub-store', 'name' => 'Power Bank 20000mAh Fast Charge',     'category' => 'elektronik', 'price' => 249000, 'sold' => 890],
            ['store' => 'techhub-store', 'name' => 'Kabel Data USB-C 3A 1m',              'category' => 'elektronik', 'price' => 35000,  'sold' => 2341],
            ['store' => 'techhub-store', 'name' => 'Stand HP Lipat Adjustable',           'category' => 'elektronik', 'price' => 55000,  'sold' => 765],
        ];

        foreach ($products as $p) {
            $productId = Str::uuid();
            $storeId   = $stores[$p['store']]->id;
            $slug      = Str::slug($p['name']);

            DB::table('products')->insert([
                'id'               => $productId,
                'store_id'         => $storeId,
                'name'             => $p['name'],
                'slug'             => $slug,
                'description'      => "Deskripsi lengkap untuk {$p['name']}. Produk berkualitas tinggi dengan bahan pilihan.",
                'category'         => $p['category'],
                'base_price'       => $p['price'],
                'discount_percent' => collect([0, 0, 10, 15, 20])->random(),
                'weight_gram'      => rand(100, 500),
                'rating_avg'       => round(rand(42, 50) / 10, 1),
                'total_reviews'    => rand(50, 500),
                'total_sold'       => $p['sold'],
                'is_active'        => true,
                'created_at'       => now()->subDays(rand(10, 180)),
                'updated_at'       => now(),
            ]);

            // Variants (ukuran & warna untuk fashion, satu default untuk elektronik)
            $variants = str_contains($p['category'], 'fashion')
                ? [
                    ['size' => 'S',  'color' => 'Hitam'],
                    ['size' => 'M',  'color' => 'Hitam'],
                    ['size' => 'L',  'color' => 'Hitam'],
                    ['size' => 'XL', 'color' => 'Hitam'],
                    ['size' => 'M',  'color' => 'Biru'],
                    ['size' => 'L',  'color' => 'Biru'],
                  ]
                : [
                    ['size' => null, 'color' => null],
                  ];

            foreach ($variants as $v) {
                DB::table('product_variants')->insert([
                    'id'         => Str::uuid(),
                    'product_id' => $productId,
                    'size'       => $v['size'],
                    'color'      => $v['color'],
                    'sku'        => strtoupper(Str::random(8)),
                    'price'      => $p['price'],
                    'stock'      => rand(10, 100),
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Images (placeholder)
            for ($img = 0; $img < 3; $img++) {
                DB::table('product_images')->insert([
                    'id'         => Str::uuid(),
                    'product_id' => $productId,
                    'url'        => "https://picsum.photos/seed/{$slug}-{$img}/400/400",
                    'alt_text'   => $p['name'],
                    'sort_order' => $img,
                    'created_at' => now(),
                ]);
            }
        }
    }
}


// =============================================================
class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $buyers   = DB::table('users')->where('role', 'buyer')->get();
        $variants = DB::table('product_variants')->get();

        foreach ($buyers as $buyer) {
            $address = DB::table('addresses')->where('user_id', $buyer->id)->first();

            for ($o = 0; $o < 3; $o++) {
                $variant   = $variants->random();
                $product   = DB::table('products')->where('id', $variant->product_id)->first();
                $store     = DB::table('stores')->where('id', $product->store_id)->first();
                $qty       = rand(1, 3);
                $unitPrice = $variant->price;
                $subtotal  = $unitPrice * $qty;
                $shipping  = 15000;
                $fee       = round($subtotal * 0.02);
                $total     = $subtotal + $shipping + $fee;
                $status    = collect(['completed', 'delivered', 'shipped', 'processing'])->random();

                $orderId = Str::uuid();
                DB::table('orders')->insert([
                    'id'               => $orderId,
                    'buyer_id'         => $buyer->id,
                    'status'           => $status,
                    'subtotal'         => $subtotal,
                    'shipping_cost'    => $shipping,
                    'platform_fee'     => $fee,
                    'total_amount'     => $total,
                    'shipping_address' => json_encode([
                        'recipient'    => $buyer->name,
                        'phone'        => $address->phone ?? '08123456789',
                        'address_line' => $address->address_line ?? 'Jl. Test',
                        'city'         => $address->city ?? 'Jakarta',
                        'province'     => $address->province ?? 'DKI Jakarta',
                        'postal_code'  => $address->postal_code ?? '12345',
                    ]),
                    'courier'          => collect(['JNE', 'J&T', 'SiCepat'])->random(),
                    'courier_service'  => 'REG',
                    'tracking_number'  => strtoupper(Str::random(12)),
                    'paid_at'          => now()->subDays(rand(1, 30)),
                    'completed_at'     => $status === 'completed' ? now()->subDays(rand(1, 10)) : null,
                    'created_at'       => now()->subDays(rand(5, 60)),
                    'updated_at'       => now(),
                ]);

                // Temporarily disable stock trigger for seeding
                DB::table('order_items')->insert([
                    'id'           => Str::uuid(),
                    'order_id'     => $orderId,
                    'variant_id'   => $variant->id,
                    'store_id'     => $store->id,
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'variant_info' => json_encode([
                        'size'  => $variant->size,
                        'color' => $variant->color,
                    ]),
                    'quantity'     => $qty,
                    'unit_price'   => $unitPrice,
                    'subtotal'     => $subtotal,
                    'item_status'  => $status === 'completed' ? 'delivered' : 'processing',
                    'created_at'   => now()->subDays(rand(5, 60)),
                ]);

                // Payment
                DB::table('payments')->insert([
                    'id'              => Str::uuid(),
                    'order_id'        => $orderId,
                    'method'          => collect(['bank_transfer', 'gopay', 'qris', 'ovo'])->random(),
                    'gateway'         => 'midtrans',
                    'gateway_ref'     => 'INV-' . strtoupper(Str::random(10)),
                    'gateway_payload' => json_encode(['snap_token' => Str::random(32)]),
                    'amount'          => $total,
                    'status'          => 'paid',
                    'paid_at'         => now()->subDays(rand(1, 30)),
                    'created_at'      => now()->subDays(rand(5, 60)),
                    'updated_at'      => now(),
                ]);
            }
        }
    }
}


// =============================================================
class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $completedItems = DB::table('order_items')
            ->where('item_status', 'delivered')
            ->get();

        $comments = [
            'Produknya bagus banget, sesuai foto! Pengiriman juga cepat. Recommended!',
            'Kualitas oke, harga sepadan. Penjual ramah dan responsif.',
            'Barang sudah sampai dengan selamat, packaging rapi.',
            'Mantap! Akan repeat order lagi.',
            'Sesuai deskripsi, warna dan ukuran pas.',
            'Pengiriman cepat, produk original. Puas!',
        ];

        foreach ($completedItems as $item) {
            $order = DB::table('orders')->where('id', $item->order_id)->first();
            $product = DB::table('products')->where('id', $item->product_id)->first();

            DB::table('reviews')->insert([
                'id'              => Str::uuid(),
                'order_item_id'   => $item->id,
                'buyer_id'        => $order->buyer_id,
                'product_id'      => $item->product_id,
                'store_id'        => $item->store_id,
                'rating_product'  => rand(4, 5),
                'rating_delivery' => rand(4, 5),
                'rating_service'  => rand(4, 5),
                'comment'         => collect($comments)->random(),
                'seller_reply'    => rand(0, 1) ? 'Terima kasih atas ulasannya! Semoga suka dengan produknya ya 😊' : null,
                'replied_at'      => rand(0, 1) ? now()->subDays(rand(1, 5)) : null,
                'created_at'      => now()->subDays(rand(1, 20)),
                'updated_at'      => now(),
            ]);
        }
    }
}


// =============================================================
class ChatSeeder extends Seeder
{
    public function run(): void
    {
        $buyers  = DB::table('users')->where('role', 'buyer')->get();
        $stores  = DB::table('stores')->get();

        foreach ($buyers as $buyer) {
            $store  = $stores->random();
            $seller = DB::table('users')->where('id', $store->owner_id)->first();

            $convId = Str::uuid();
            DB::table('conversations')->insert([
                'id'              => $convId,
                'buyer_id'        => $buyer->id,
                'seller_id'       => $seller->id,
                'store_id'        => $store->id,
                'last_message_at' => now()->subMinutes(rand(10, 1440)),
                'buyer_unread'    => rand(0, 3),
                'seller_unread'   => rand(0, 2),
                'created_at'      => now()->subDays(rand(1, 30)),
            ]);

            $chatMessages = [
                [$buyer->id,  "Halo kak, apakah produk ini masih tersedia?"],
                [$seller->id, "Halo! Masih ada stok kok, mau pesan yang ukuran apa?"],
                [$buyer->id,  "Saya mau ukuran M warna hitam. Bisa COD?"],
                [$seller->id, "Kami pakai pengiriman ekspedisi ya kak, tidak COD. Tapi pengiriman cepat 1-2 hari 😊"],
                [$buyer->id,  "Oke siap, saya order sekarang ya!"],
            ];

            foreach ($chatMessages as $msg) {
                DB::table('messages')->insert([
                    'id'              => Str::uuid(),
                    'conversation_id' => $convId,
                    'sender_id'       => $msg[0],
                    'content'         => $msg[1],
                    'type'            => 'text',
                    'is_read'         => true,
                    'read_at'         => now()->subMinutes(rand(5, 60)),
                    'created_at'      => now()->subMinutes(rand(10, 1440)),
                ]);
            }
        }
    }
}
