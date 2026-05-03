UPDATE unit_high_scores 
SET high_score = :high_score,
    achieved_at = :achieved_at
WHERE user_id = :user_id 
  AND category_no = :category_no 
  AND unit_no = :unit_no;
