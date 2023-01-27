CREATE TABLE categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    name TEXT,
    slug TEXT
);

CREATE TABLE states (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    name VARCHAR(2)
);

CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    name VARCHAR(100),
    email VARCHAR(100),
    state VARCHAR(100) REFERENCES states(id),
    password VARCHAR(100),
    token VARCHAR(100)
);

CREATE TABLE ads (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    status BOOLEAN ,
    user_id INTEGER REFERENCES users(id),
    state INTEGER REFERENCES states(id),
    title VARCHAR(100),
    category INTEGER REFERENCES categories(id),
    price INTEGER,
    price_negotiable BOOLEAN ,
    description VARCHAR(100),
    img VARCHAR(100),
    views INTEGER
);