<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Trigger: refresh rating produk & toko setelah review INSERT
        DB::unprepared("
            CREATE TRIGGER trg_refresh_rating_insert
            AFTER INSERT ON reviews
            FOR EACH ROW
            BEGIN
                UPDATE products SET
                    rating_avg    = (SELECT ROUND(AVG((rating_product + rating_delivery + rating_service) / 3), 2) FROM reviews WHERE product_id = NEW.product_id),
                    total_reviews = (SELECT COUNT(*) FROM reviews WHERE product_id = NEW.product_id)
                WHERE id = NEW.product_id;

                UPDATE stores SET
                    rating_avg    = (SELECT ROUND(AVG((rating_product + rating_delivery + rating_service) / 3), 2) FROM reviews WHERE store_id = NEW.store_id),
                    total_reviews = (SELECT COUNT(*) FROM reviews WHERE store_id = NEW.store_id)
                WHERE id = NEW.store_id;
            END
        ");

        // Trigger: refresh rating produk & toko setelah review UPDATE
        DB::unprepared("
            CREATE TRIGGER trg_refresh_rating_update
            AFTER UPDATE ON reviews
            FOR EACH ROW
            BEGIN
                UPDATE products SET
                    rating_avg    = (SELECT ROUND(AVG((rating_product + rating_delivery + rating_service) / 3), 2) FROM reviews WHERE product_id = NEW.product_id),
                    total_reviews = (SELECT COUNT(*) FROM reviews WHERE product_id = NEW.product_id)
                WHERE id = NEW.product_id;

                UPDATE stores SET
                    rating_avg    = (SELECT ROUND(AVG((rating_product + rating_delivery + rating_service) / 3), 2) FROM reviews WHERE store_id = NEW.store_id),
                    total_reviews = (SELECT COUNT(*) FROM reviews WHERE store_id = NEW.store_id)
                WHERE id = NEW.store_id;
            END
        ");

        // Trigger: kurangi stok variant saat order item dibuat
        DB::unprepared("
            CREATE TRIGGER trg_decrement_stock
            BEFORE INSERT ON order_items
            FOR EACH ROW
            BEGIN
                DECLARE current_stock INT;
                SELECT stock INTO current_stock
                FROM product_variants
                WHERE id = NEW.variant_id FOR UPDATE;

                IF current_stock < NEW.quantity THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Stok tidak mencukupi';
                END IF;

                UPDATE product_variants
                SET stock = stock - NEW.quantity
                WHERE id = NEW.variant_id;
            END
        ");

        // Trigger: update last_message_at & unread count setelah pesan baru
        DB::unprepared("
            CREATE TRIGGER trg_message_after_insert
            AFTER INSERT ON messages
            FOR EACH ROW
            BEGIN
                UPDATE conversations SET
                    last_message_at = NEW.created_at,
                    buyer_unread  = buyer_unread  + IF(buyer_id  != NEW.sender_id, 1, 0),
                    seller_unread = seller_unread + IF(seller_id != NEW.sender_id, 1, 0)
                WHERE id = NEW.conversation_id;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_refresh_rating_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_refresh_rating_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_decrement_stock');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_message_after_insert');
    }
};
