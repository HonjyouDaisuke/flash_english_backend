INSERT INTO user_settings (
    user_id,
    setting_key,
    value,
    updated_at
)
VALUES (
    :user_id,
    :setting_key,
    :value,
    NOW()
)
ON DUPLICATE KEY UPDATE
    value = VALUES(value),
    updated_at = NOW();
    