-- =============================================================
-- NusaMarket — MySQL Schema v1.0
-- Kompatibel dengan Laravel & MySQL 8.0+
-- Jalankan: mysql -u root -p nusamarket < nusamarket_mysql.sql
-- =============================================================

CREATE DATABASE IF NOT EXISTS nusamarket CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nusamarket;

-- =============================================================
-- USERS
-- =============================================================

CREATE TABLE users (
    id              CHAR(36)        NOT NULL PRIMARY KEY,
    name            VARCHAR(120)    NOT NULL,
    email           VARCHAR(255)    NOT NULL UNIQUE,
    password        TEXT            DEFAULT NULL,
    phone           VARCHAR(20)     DEFAULT NULL,
    avatar_url      TEXT            DEFAULT NULL,
    role            ENUM('buyer','seller','admin') NOT NULL DEFAULT 'buyer',
    is_verified     TINYINT(1)      NOT NULL DEFAULT 0,
    is_active       TINYINT(1)      NOT NULL DEFAULT 1,
    remember_token  VARCHAR(100)    DEFAULT NULL,
    last_login_at   TIMESTAMP       NULL,
    email_verified_at TIMESTAMP     NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_email (email),
    INDEX idx_users_role  (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- STORES
-- =============================================================

CREATE TABLE stores (
    id              CHAR(36)        NOT NULL PRIMARY KEY,
    owner_id        CHAR(36)        NOT NULL,
    name            VARCHAR(120)    NOT NULL,
    slug            VARCHAR(120)    NOT NULL UNIQUE,
    description     TEXT            DEFAULT NULL,
    logo_url        TEXT            DEFAULT NULL,
    banner_url      TEXT            DEFAULT NULL,
    city            VARCHAR(100)    DEFAULT NULL,
    province        VARCHAR(100)    DEFAULT NULL,
    rating_avg      DECIMAL(3,2)    NOT NULL DEFAULT 0.00,
    total_reviews   INT UNSIGNED    NOT NULL DEFAULT 0,
    total_sales     INT UNSIGNED    NOT NULL DEFAULT 0,
    status          ENUM('active','inactive','suspended','pending_review') NOT NULL DEFAULT 'pending_review',
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_stores_owner FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_stores_owner  (owner_id),
    INDEX idx_stores_status (status),
    INDEX idx_stores_rating (rating_avg)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- PRODUCTS
-- =============================================================

CREATE TABLE products (
    id              CHAR(36)        NOT NULL PRIMARY KEY,
    store_id        CHAR(36)        NOT NULL,
    name            VARCHAR(255)    NOT NULL,
    slug            VARCHAR(255)    NOT NULL,
    description     TEXT            DEFAULT NULL,
    category        ENUM(
        'fashion_wanita','fashion_pria','elektronik',
        'rumah_dapur','kecantikan','olahraga',
        'otomotif','mainan','buku','lainnya'
    ) NOT NULL DEFAULT 'lainnya',
    base_price      DECIMAL(14,2)   NOT NULL,
    discount_percent TINYINT UNSIGNED NOT NULL DEFAULT 0,
    weight_gram     INT UNSIGNED    NOT NULL DEFAULT 0,
    rating_avg      DECIMAL(3,2)    NOT NULL DEFAULT 0.00,
    total_reviews   INT UNSIGNED    NOT NULL DEFAULT 0,
    total_sold      INT UNSIGNED    NOT NULL DEFAULT 0,
    is_active       TINYINT(1)      NOT NULL DEFAULT 1,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    UNIQUE KEY uq_product_store_slug (store_id, slug),
    INDEX idx_products_category (category),
    INDEX idx_products_active   (is_active),
    INDEX idx_products_rating   (rating_avg),
    FULLTEXT INDEX ft_products_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- PRODUCT VARIANTS
-- =============================================================

CREATE TABLE product_variants (
    id          CHAR(36)        NOT NULL PRIMARY KEY,
    product_id  CHAR(36)        NOT NULL,
    size        VARCHAR(50)     DEFAULT NULL,
    color       VARCHAR(50)     DEFAULT NULL,
    sku         VARCHAR(100)    DEFAULT NULL,
    price       DECIMAL(14,2)   NOT NULL,
    stock       INT UNSIGNED    NOT NULL DEFAULT 0,
    is_active   TINYINT(1)      NOT NULL DEFAULT 1,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_variants_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY uq_variant_size_color (product_id, size, color),
    INDEX idx_variants_sku (sku)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- PRODUCT IMAGES
-- =============================================================

CREATE TABLE product_images (
    id          CHAR(36)        NOT NULL PRIMARY KEY,
    product_id  CHAR(36)        NOT NULL,
    url         TEXT            NOT NULL,
    alt_text    VARCHAR(255)    DEFAULT NULL,
    sort_order  TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pimages_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_pimages_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- ADDRESSES
-- =============================================================

CREATE TABLE addresses (
    id           CHAR(36)        NOT NULL PRIMARY KEY,
    user_id      CHAR(36)        NOT NULL,
    label        VARCHAR(60)     NOT NULL DEFAULT 'Rumah',
    recipient    VARCHAR(120)    NOT NULL,
    phone        VARCHAR(20)     NOT NULL,
    address_line TEXT            NOT NULL,
    city         VARCHAR(100)    NOT NULL,
    province     VARCHAR(100)    NOT NULL,
    postal_code  VARCHAR(10)     NOT NULL,
    is_default   TINYINT(1)      NOT NULL DEFAULT 0,
    created_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_addresses_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_addresses_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- ORDERS
-- =============================================================

CREATE TABLE orders (
    id               CHAR(36)        NOT NULL PRIMARY KEY,
    buyer_id         CHAR(36)        NOT NULL,
    status           ENUM(
        'pending_payment','paid','processing',
        'shipped','delivered','completed',
        'cancelled','refunded'
    ) NOT NULL DEFAULT 'pending_payment',
    subtotal         DECIMAL(14,2)   NOT NULL DEFAULT 0,
    shipping_cost    DECIMAL(14,2)   NOT NULL DEFAULT 0,
    platform_fee     DECIMAL(14,2)   NOT NULL DEFAULT 0,
    total_amount     DECIMAL(14,2)   NOT NULL DEFAULT 0,
    shipping_address JSON            NOT NULL,
    courier          VARCHAR(50)     DEFAULT NULL,
    courier_service  VARCHAR(50)     DEFAULT NULL,
    tracking_number  VARCHAR(100)    DEFAULT NULL,
    notes            TEXT            DEFAULT NULL,
    paid_at          TIMESTAMP       NULL,
    completed_at     TIMESTAMP       NULL,
    created_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_buyer FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_orders_buyer   (buyer_id),
    INDEX idx_orders_status  (status),
    INDEX idx_orders_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- ORDER ITEMS
-- =============================================================

CREATE TABLE order_items (
    id           CHAR(36)        NOT NULL PRIMARY KEY,
    order_id     CHAR(36)        NOT NULL,
    variant_id   CHAR(36)        NOT NULL,
    store_id     CHAR(36)        NOT NULL,
    product_id   CHAR(36)        NOT NULL,
    product_name VARCHAR(255)    NOT NULL,
    variant_info JSON            NOT NULL,
    quantity     SMALLINT UNSIGNED NOT NULL,
    unit_price   DECIMAL(14,2)   NOT NULL,
    subtotal     DECIMAL(14,2)   NOT NULL,
    item_status  ENUM('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
    created_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_oitems_order   FOREIGN KEY (order_id)   REFERENCES orders(id)           ON DELETE CASCADE,
    CONSTRAINT fk_oitems_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE RESTRICT,
    CONSTRAINT fk_oitems_store   FOREIGN KEY (store_id)   REFERENCES stores(id)           ON DELETE RESTRICT,
    CONSTRAINT fk_oitems_product FOREIGN KEY (product_id) REFERENCES products(id)         ON DELETE RESTRICT,
    INDEX idx_oitems_order   (order_id),
    INDEX idx_oitems_store   (store_id),
    INDEX idx_oitems_variant (variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- PAYMENTS
-- =============================================================

CREATE TABLE payments (
    id               CHAR(36)        NOT NULL PRIMARY KEY,
    order_id         CHAR(36)        NOT NULL UNIQUE,
    method           ENUM('bank_transfer','gopay','ovo','dana','qris','credit_card','debit_card') NOT NULL,
    gateway          VARCHAR(50)     NOT NULL DEFAULT 'midtrans',
    gateway_ref      VARCHAR(255)    DEFAULT NULL,
    gateway_payload  JSON            DEFAULT NULL,
    amount           DECIMAL(14,2)   NOT NULL,
    status           ENUM('pending','paid','failed','expired','refunded') NOT NULL DEFAULT 'pending',
    paid_at          TIMESTAMP       NULL,
    expired_at       TIMESTAMP       NULL,
    created_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE RESTRICT,
    INDEX idx_payments_status      (status),
    INDEX idx_payments_gateway_ref (gateway_ref)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- SELLER BALANCES
-- =============================================================

CREATE TABLE seller_balances (
    id           CHAR(36)        NOT NULL PRIMARY KEY,
    store_id     CHAR(36)        NOT NULL UNIQUE,
    available    DECIMAL(14,2)   NOT NULL DEFAULT 0.00,
    pending      DECIMAL(14,2)   NOT NULL DEFAULT 0.00,
    total_earned DECIMAL(14,2)   NOT NULL DEFAULT 0.00,
    updated_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_balances_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE balance_transactions (
    id            CHAR(36)        NOT NULL PRIMARY KEY,
    store_id      CHAR(36)        NOT NULL,
    order_id      CHAR(36)        DEFAULT NULL,
    type          VARCHAR(30)     NOT NULL COMMENT 'credit_sale | debit_withdrawal | debit_refund',
    amount        DECIMAL(14,2)   NOT NULL,
    balance_after DECIMAL(14,2)   NOT NULL,
    description   TEXT            DEFAULT NULL,
    created_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_baltx_store FOREIGN KEY (store_id) REFERENCES stores(id)  ON DELETE CASCADE,
    CONSTRAINT fk_baltx_order FOREIGN KEY (order_id) REFERENCES orders(id)  ON DELETE SET NULL,
    INDEX idx_baltx_store (store_id),
    INDEX idx_baltx_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- REVIEWS
-- =============================================================

CREATE TABLE reviews (
    id              CHAR(36)        NOT NULL PRIMARY KEY,
    order_item_id   CHAR(36)        NOT NULL UNIQUE,
    buyer_id        CHAR(36)        NOT NULL,
    product_id      CHAR(36)        NOT NULL,
    store_id        CHAR(36)        NOT NULL,
    rating_product  TINYINT UNSIGNED NOT NULL,
    rating_delivery TINYINT UNSIGNED NOT NULL,
    rating_service  TINYINT UNSIGNED NOT NULL,
    comment         TEXT            DEFAULT NULL,
    seller_reply    TEXT            DEFAULT NULL,
    replied_at      TIMESTAMP       NULL,
    is_flagged      TINYINT(1)      NOT NULL DEFAULT 0,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_oitem   FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_buyer   FOREIGN KEY (buyer_id)      REFERENCES users(id)       ON DELETE CASCADE,
    CONSTRAINT fk_reviews_product FOREIGN KEY (product_id)    REFERENCES products(id)    ON DELETE CASCADE,
    CONSTRAINT fk_reviews_store   FOREIGN KEY (store_id)      REFERENCES stores(id)      ON DELETE CASCADE,
    CONSTRAINT chk_rating_product  CHECK (rating_product  BETWEEN 1 AND 5),
    CONSTRAINT chk_rating_delivery CHECK (rating_delivery BETWEEN 1 AND 5),
    CONSTRAINT chk_rating_service  CHECK (rating_service  BETWEEN 1 AND 5),
    INDEX idx_reviews_product (product_id),
    INDEX idx_reviews_store   (store_id),
    INDEX idx_reviews_buyer   (buyer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE review_images (
    id          CHAR(36)        NOT NULL PRIMARY KEY,
    review_id   CHAR(36)        NOT NULL,
    url         TEXT            NOT NULL,
    sort_order  TINYINT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT fk_rimages_review FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    INDEX idx_rimages_review (review_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- CONVERSATIONS & MESSAGES (CHAT)
-- =============================================================

CREATE TABLE conversations (
    id              CHAR(36)        NOT NULL PRIMARY KEY,
    buyer_id        CHAR(36)        NOT NULL,
    seller_id       CHAR(36)        NOT NULL,
    store_id        CHAR(36)        NOT NULL,
    product_id      CHAR(36)        DEFAULT NULL,
    last_message_at TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    buyer_unread    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    seller_unread   SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_conv_buyer  FOREIGN KEY (buyer_id)  REFERENCES users(id)    ON DELETE CASCADE,
    CONSTRAINT fk_conv_seller FOREIGN KEY (seller_id) REFERENCES users(id)    ON DELETE CASCADE,
    CONSTRAINT fk_conv_store  FOREIGN KEY (store_id)  REFERENCES stores(id)   ON DELETE CASCADE,
    CONSTRAINT fk_conv_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    UNIQUE KEY uq_conv_buyer_store (buyer_id, store_id),
    INDEX idx_conv_buyer    (buyer_id),
    INDEX idx_conv_seller   (seller_id),
    INDEX idx_conv_store    (store_id),
    INDEX idx_conv_last_msg (last_message_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE messages (
    id              CHAR(36)        NOT NULL PRIMARY KEY,
    conversation_id CHAR(36)        NOT NULL,
    sender_id       CHAR(36)        NOT NULL,
    content         TEXT            DEFAULT NULL,
    type            ENUM('text','image','system') NOT NULL DEFAULT 'text',
    media_url       TEXT            DEFAULT NULL,
    is_read         TINYINT(1)      NOT NULL DEFAULT 0,
    read_at         TIMESTAMP       NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_msg_conv   FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    CONSTRAINT fk_msg_sender FOREIGN KEY (sender_id)       REFERENCES users(id)         ON DELETE CASCADE,
    INDEX idx_msg_conv    (conversation_id),
    INDEX idx_msg_sender  (sender_id),
    INDEX idx_msg_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- CART
-- =============================================================

CREATE TABLE carts (
    id          CHAR(36)        NOT NULL PRIMARY KEY,
    user_id     CHAR(36)        NOT NULL UNIQUE,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_carts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cart_items (
    id          CHAR(36)        NOT NULL PRIMARY KEY,
    cart_id     CHAR(36)        NOT NULL,
    variant_id  CHAR(36)        NOT NULL,
    quantity    SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    added_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_citems_cart    FOREIGN KEY (cart_id)   REFERENCES carts(id)            ON DELETE CASCADE,
    CONSTRAINT fk_citems_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE,
    UNIQUE KEY uq_cart_variant (cart_id, variant_id),
    INDEX idx_citems_cart (cart_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- WISHLISTS
-- =============================================================

CREATE TABLE wishlists (
    id          CHAR(36)        NOT NULL PRIMARY KEY,
    user_id     CHAR(36)        NOT NULL,
    product_id  CHAR(36)        NOT NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_wl_user    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    CONSTRAINT fk_wl_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY uq_wishlist (user_id, product_id),
    INDEX idx_wl_user    (user_id),
    INDEX idx_wl_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- NOTIFICATIONS
-- =============================================================

CREATE TABLE notifications (
    id          CHAR(36)        NOT NULL PRIMARY KEY,
    user_id     CHAR(36)        NOT NULL,
    type        VARCHAR(60)     NOT NULL,
    title       VARCHAR(255)    NOT NULL,
    body        TEXT            DEFAULT NULL,
    data        JSON            DEFAULT NULL,
    is_read     TINYINT(1)      NOT NULL DEFAULT 0,
    read_at     TIMESTAMP       NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notif_user   (user_id),
    INDEX idx_notif_unread (user_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- TRIGGERS: Otomatis update rating produk & toko saat review baru
-- =============================================================

DELIMITER $$

CREATE TRIGGER trg_refresh_rating_after_insert
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
END$$

CREATE TRIGGER trg_refresh_rating_after_update
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
END$$

-- =============================================================
-- TRIGGER: Kurangi stok saat order item dibuat
-- =============================================================

CREATE TRIGGER trg_decrement_stock
BEFORE INSERT ON order_items
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;
    SELECT stock INTO current_stock FROM product_variants WHERE id = NEW.variant_id FOR UPDATE;
    IF current_stock < NEW.quantity THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak mencukupi';
    END IF;
    UPDATE product_variants SET stock = stock - NEW.quantity WHERE id = NEW.variant_id;
END$$

-- =============================================================
-- TRIGGER: Update last_message_at & unread count di conversations
-- =============================================================

CREATE TRIGGER trg_message_after_insert
AFTER INSERT ON messages
FOR EACH ROW
BEGIN
    UPDATE conversations SET
        last_message_at = NEW.created_at,
        buyer_unread  = buyer_unread  + IF((SELECT buyer_id FROM conversations WHERE id = NEW.conversation_id) != NEW.sender_id, 1, 0),
        seller_unread = seller_unread + IF((SELECT seller_id FROM conversations WHERE id = NEW.conversation_id) != NEW.sender_id, 1, 0)
    WHERE id = NEW.conversation_id;
END$$

DELIMITER ;

-- =============================================================
-- SEED: Data awal
-- =============================================================

INSERT INTO users (id, name, email, password, role, is_verified, is_active)
VALUES (
    UUID(),
    'Admin NusaMarket',
    'admin@nusamarket.id',
    '$2y$12$placeholder_hashed_password',
    'admin',
    1,
    1
);

-- =============================================================
-- END OF SCHEMA
-- =============================================================
