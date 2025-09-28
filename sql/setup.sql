CREATE DATABASE IF NOT EXISTS movie_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE movie_app;

-- users
CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- movies
CREATE TABLE IF NOT EXISTS movies (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    poster_url VARCHAR(1000) DEFAULT NULL,
    genre VARCHAR(100) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    release_year YEAR(4) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ratings
CREATE TABLE IF NOT EXISTS ratings (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    movie_id INT(11) NOT NULL,
    rating TINYINT(4) NOT NULL CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, movie_id),
    KEY movie_id (movie_id),
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- watchlist
CREATE TABLE IF NOT EXISTS watchlist (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    movie_id INT(11) NOT NULL,
    added_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, movie_id),
    KEY movie_id (movie_id),
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- feedback
CREATE TABLE IF NOT EXISTS feedback (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) DEFAULT NULL,
    email VARCHAR(120) DEFAULT NULL,
    message TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Seed users
INSERT INTO
    users (
        id,
        username,
        email,
        password_hash,
        is_admin,
        created_at
    )
VALUES (
        2,
        'admin',
        'admin@moviehaven.com',
        '$2y$10$PhZKWbviLK5eQ9yrr81V5uPOGgKXrpjS4F.aqemZLoMdRCQrsgIrm',
        1,
        '2025-09-28 10:54:33'
    ),
    (
        3,
        'Rakesh',
        'expander@gmail.com',
        '$2y$10$VuqJ5tawbLHB/E2ajApaHOD6xa3G7of5fJ2ehqP8im70GHu/mMkf6',
        0,
        '2025-09-28 11:16:41'
    )
ON DUPLICATE KEY UPDATE
    email = VALUES(email);

-- Seed movies
INSERT INTO
    movies (
        id,
        title,
        poster_url,
        genre,
        description,
        release_year,
        created_at
    )
VALUES (
        10,
        'The Shawshank Redemption',
        'https://th.bing.com/th/id/OIP.7uUItEV-tOvglPkIxQ6JCQHaLH?w=108&h=108&c=1&bgcl=f05f9c&r=0&o=7&dpr=1.5&pid=ImgRC&rm=3',
        'Drama',
        'A banker convicted of uxoricide forms a friendship over a quarter century with a hardened convict, while maintaining his innocence and trying to remain hopeful through simple compassion.\r\n\r\nDirector\r\nFrank Darabont\r\nWriters\r\nStephen KingFrank Darabont\r\nStars\r\nTim RobbinsMorgan FreemanBob Gunton',
        '1994',
        '2025-09-28 13:16:39'
    )
ON DUPLICATE KEY UPDATE
    title = VALUES(title);

-- You can add more seed data for ratings, watchlist, and feedback if needed.