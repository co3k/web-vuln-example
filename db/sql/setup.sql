CREATE TABLE IF NOT EXISTS example_user (
    id INTEGER PRIMARY KEY,
    username TEXT,
    password TEXT
);

INSERT INTO example_user VALUES (1, 'guest', '8933b8284c4d7e0b5b7c4fb35cac5f1197451f38dceccf33c396db5f43c38ae85d07f46df20a9a460e9a75c9452de621e468f544a5783a938e9b17bb69e0535a');
INSERT INTO example_user VALUES (2, 'co3k', '5b11339dcc95fef60027f55248f94ff49b6be76b0930b760ec17a3e505d2a2b4341729dd08e58551a2ec881a590d9687076dc4e809f46fe5c1b7f7202099722e');

CREATE TABLE IF NOT EXISTS example_activity (
    id INTEGER PRIMARY KEY,
    user_id INTEGER,
    body TEXT,
    stamp TEXT,
    created_at TEXT
);
