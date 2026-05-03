SELECT high_score, achieved_at
FROM unit_high_scores
WHERE user_id = :user_id
  AND category_no = :category_no
  AND unit_no = :unit_no
LIMIT 1;