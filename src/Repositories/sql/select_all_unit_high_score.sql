SELECT category_id, unit_id, high_score, achieved_at
FROM unit_high_scores
WHERE user_id = :user_id
  AND category_id = :category_id
ORDER BY unit_id ASC