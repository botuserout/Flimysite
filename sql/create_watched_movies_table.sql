-- Create watched_movies table for tracking watched status per user and movie
CREATE TABLE IF NOT EXISTS watched_movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    watched_status TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY user_movie_unique (user_id, movie_id)
);
