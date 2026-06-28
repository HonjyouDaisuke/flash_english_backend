SELECT category_no, unit_no, high_score, achieved_at
FROM unit_high_scores
WHERE user_id = :user_id
ORDER BY category_no, unit_no ASC;
