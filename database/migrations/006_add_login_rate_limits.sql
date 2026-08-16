CREATE TABLE login_rate_limits (
    username_hash CHAR(64) NOT NULL,
    window_started_at DATETIME NOT NULL, 
    failed_attempts INT UNSIGNED NOT NULL DEFAULT 0, 
    locked_until DATETIME NULL,
    PRIMARY KEY (username_hash)
) ENGINE=InnoDB;