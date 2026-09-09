CREATE TABLE IF NOT EXISTS audit_log (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    entity VARCHAR(50) NOT NULL,
    entity_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    KEY idx_audit_log_user_id (user_id),
    KEY idx_audit_log_entity (entity),
    KEY idx_audit_log_entity_id (entity_id),
    KEY idx_audit_log_created_at (created_at),

    CONSTRAINT fk_audit_log_user
        FOREIGN KEY (user_id)
        REFERENCES users (id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_changes (
    id INT NOT NULL AUTO_INCREMENT,
    audit_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,

    PRIMARY KEY (id),

    KEY idx_audit_changes_audit_id (audit_id),

    CONSTRAINT fk_audit_changes_audit
        FOREIGN KEY (audit_id)
        REFERENCES audit_log (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

