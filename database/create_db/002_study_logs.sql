DROP TABLE IF EXISTS study_logs;

CREATE TABLE study_logs (
    id CHAR(255) NOT NULL PRIMARY KEY DEFAULT (UUID()),
    user_id VARCHAR(255),
    category_id INT,
    unit_id INT,
    question_id INT,
    is_correct BOOLEAN,
    session_id INT,
    duration_seconds INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
