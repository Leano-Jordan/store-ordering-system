CREATE TABLE user_sessions (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    login_at DATETIME NOT NULL,
    last_activity_at DATETIME NOT NULL,
    logout_at DATETIME NULL,
    status ENUM('ACTIVE', 'LOGGED_OUT', 'TIMED_OUT', 'TERMINATED')
        NOT NULL DEFAULT 'ACTIVE',

    PRIMARY KEY (id),

    KEY idx_user_sessions_user_id (user_id),
    KEY idx_user_sessions_status (status),
    KEY idx_user_sessions_last_activity (last_activity_at),

    CONSTRAINT fk_user_sessions_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB
    DEFAULT CHARSET=utf8mb4
    COLLATE=utf8mb4_general_ci;

