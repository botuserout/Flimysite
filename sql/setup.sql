CREATE DATABASE IF NOT EXISTS movie_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE movie_app;

-- users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- movies
CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    poster_url VARCHAR(1000),
    genre VARCHAR(100),
    description TEXT,
    release_year YEAR NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ratings (one rating per user per movie)
CREATE TABLE IF NOT EXISTS ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, movie_id),
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies (id) ON DELETE CASCADE
);

-- watchlist
CREATE TABLE IF NOT EXISTS watchlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, movie_id),
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies (id) ON DELETE CASCADE
);

-- contact/feedback
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120),
    email VARCHAR(120),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- seed an admin and some movies
INSERT INTO users (username, email, password_hash, is_admin)
VALUES ('admin', 'admin@example.com', '$2y$10$replace_with_hash', 1)
ON DUPLICATE KEY UPDATE email=VALUES(email);

-- Note: replace password hash above. Better: create user via signup or generate hash with PHP.
INSERT INTO
    movies (
        title,
        poster_url,
        genre,
        description,
        release_year
    )
VALUES (
        'The Stardust Voyage',
        'https://imgs.search.brave.com/EZpxDE0Dh4iQKJc68uJox_i7p6WDEAfTVEkVfu99IiM/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly91cGxv/YWQud2lraW1lZGlh/Lm9yZy93aWtpcGVk/aWEvZW4vNi82Zi9T/dGFyZHVzdF9wcm9t/b19wb3N0ZXIuanBn',
        'Sci-Fi',
        'A spaceship crew discovers...',
        2022
    ),
    (
        'Midnight Melody',
        'https://m.media-amazon.com/images/M/MV5BOTRkZDYyM2YtNjU2MS00OGMxLTk0NzgtMDdiNTk0N2JkMDRkXkEyXkFqcGc@._V1_QL75_UX133.5_.jpg',
        'Drama',
        'An emotional journey...',
        2019
    ),
    (
        'Action Riot',
        'https://imgs.search.brave.com/Y8X13GgCZQtkQNnCpx23hdT8YwuZX6pNpqoA43868pI/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly9tLm1l/ZGlhLWFtYXpvbi5j/b20vaW1hZ2VzL00v/TVY1QlptWm1aVFZr/TldJdE0yTXhZUzAw/TVdKakxXSmtOalF0/WkRSbE56WXlZakps/WlRBMlhrRXlYa0Zx/Y0djQC5qcGc',
        'Action',
        'Non-stop thrills...',
        2021
    );