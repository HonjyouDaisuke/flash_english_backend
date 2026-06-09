SELECT
    setting_key,
    value,
    updated_at
FROM user_settings
WHERE user_id = :user_id
ORDER BY setting_key;
