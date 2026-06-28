SELECT
    question_id,
    category_no,
    unit_no,
    question_no as number,
    japanese,
    english,
    japanese_audio,
    english_audio
FROM questions
ORDER BY category_no, unit_no, question_no;
