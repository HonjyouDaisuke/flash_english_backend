UPDATE unit_high_scores 
SET high_score = :high_score
WHERE user_id = :user_id 
  AND category_id = :category_id 
  AND unit_id = :unit_id;
