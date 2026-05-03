DROP TABLE IF EXISTS unit_high_scores;

CREATE TABLE unit_high_scores (
    id CHAR(255) NOT NULL PRIMARY KEY DEFAULT (UUID()),
    user_id VARCHAR(255),
    category_no INT,
    unit_no INT,
    high_score INT NOT NULL DEFAULT 0,
    achieved_at VARCHAR(50),
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_user_unit (user_id, category_no, unit_no)
);
