DROP TABLE IF EXISTS questions;

CREATE TABLE questions (
    question_id INTEGER PRIMARY KEY,
    category_no INTEGER NOT NULL,
    unit_no INTEGER NOT NULL,
    question_no INTEGER NOT NULL,
    UNIQUE(category_no, unit_no, question_no),
    
    japanese TEXT NOT NULL,
    english TEXT NOT NULL,

    japanese_audio TEXT,
    english_audio TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);