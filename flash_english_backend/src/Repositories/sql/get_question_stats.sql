SELECT
    q.question_id,
    sl.category_no,
    sl.unit_no,
    sl.question_no,
    SUM(sl.is_correct = 1) AS correct_count,
    SUM(sl.is_correct = 0) AS wrong_count
FROM study_logs sl
JOIN questions q
    ON q.category_no = sl.category_no
   AND q.unit_no = sl.unit_no
   AND q.question_no = sl.question_no
WHERE sl.user_id = :user_id
GROUP BY
    q.question_id,
    sl.category_no,
    sl.unit_no,
    sl.question_no;