CREATE TABLE license_entitlements (
    id tinyint UNSIGNED NOT NULL,
    license_id varchar(100) NOT NULL,
    license_key_hash char(64) NOT NULL,
    installation_id char(36) NOT NULL,
    expires_at datetime NOT NULL,
    grace_ends_at datetime NOT NULL,
    suspended tinyint(1) NOT NULL DEFAULT 0,
    last_validated_at datetime DEFAULT NULL,
    last_known_good_at datetime DEFAULT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_license_entitlements_license_id (license_id),
    UNIQUE KEY uq_license_entitlements_installation_id (installation_id),
    CONSTRAINT chk_license_entitlements_singleton
        CHECK (id = 1),
    CONSTRAINT chk_license_entitlements_suspended
        CHECK (suspended IN (0, 1)),
    CONSTRAINT chk_license_entitlements_grace
        CHECK (grace_ends_at >= expires_at)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;