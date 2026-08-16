CREATE INDEX idx_study_logs_user_question_stats
ON study_logs (
    user_id,
    category_no,
    unit_no,
    question_no,
    is_correct
);