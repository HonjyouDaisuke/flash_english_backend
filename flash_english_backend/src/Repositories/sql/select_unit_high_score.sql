SELECT high_score, achieved_at
FROM unit_high_scores
WHERE user_id = :user_id
  AND category_id = :category_id
  AND unit_id = :unit_id
LIMIT 1;