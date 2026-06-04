DROP TABLE IF EXISTS user_settings;

CREATE TABLE user_settings (
    user_id VARCHAR(36) NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    value TEXT NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (
        user_id,
        setting_key
    )
);