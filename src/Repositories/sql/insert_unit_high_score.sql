INSERT INTO unit_high_scores 
(
  user_id,
  category_id,
  unit_id,
  high_score,
  achieved_at
) 
VALUES 
(
  :user_id,
  :category_id,
  :unit_id,
  :high_score,
  :achieved_at
);
