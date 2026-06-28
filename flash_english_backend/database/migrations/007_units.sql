DROP TABLE IF EXISTS units;

CREATE TABLE units (
    unit_id INTEGER PRIMARY KEY,
    category_no INTEGER NOT NULL,
    unit_no INTEGER NOT NULL,
    unit_name TEXT NOT NULL,
    unit_description TEXT NOT NULL,
    UNIQUE(category_no, unit_no),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
