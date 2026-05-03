INSERT INTO unit_high_scores 
(
  user_id,
  category_no,
  unit_no,
  high_score,
  achieved_at
) 
VALUES 
(
  :user_id,
  :category_no,
  :unit_no,
  :high_score,
  :achieved_at
);
