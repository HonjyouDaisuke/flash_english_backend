DROP TABLE IF EXISTS categories;

CREATE TABLE categories (
    category_id INTEGER PRIMARY KEY,
    category_no INTEGER NOT NULL,
    category_name TEXT NOT NULL,
    category_description TEXT NOT NULL,
    UNIQUE(category_no),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
