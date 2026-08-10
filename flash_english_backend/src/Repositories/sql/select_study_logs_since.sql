SELECT * FROM study_logs
WHERE user_id = :user_id
AND created_at >= :since
ORDER BY created_at ASC;
