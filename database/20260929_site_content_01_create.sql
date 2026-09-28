-- MariaDB 5.5.50 / MySQL 5.5. No existing tables are altered.
SET NAMES utf8mb4;
CREATE TABLE IF NOT EXISTS site_content_texts (
    page_key VARCHAR(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    content_key VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    text_value TEXT NOT NULL,
    revision INT UNSIGNED NOT NULL DEFAULT 1,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (page_key, content_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS site_content_images (
    slot_key VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    storage_key VARCHAR(255) NOT NULL,
    alt_text VARCHAR(500) NOT NULL,
    revision INT UNSIGNED NOT NULL DEFAULT 1,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (slot_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
