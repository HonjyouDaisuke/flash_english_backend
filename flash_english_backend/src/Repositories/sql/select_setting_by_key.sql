SELECT
    setting_key,
    value,
    updated_at
FROM user_settings
WHERE user_id = :user_id
AND setting_key = :setting_key