USE if0_40045927_movie_app;

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

-- user_movies (combines watchlist + rating)
CREATE TABLE IF NOT EXISTS user_movies (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    movie_id INT(11) NOT NULL,
    in_watchlist TINYINT(1) DEFAULT 0,
    rating TINYINT(4) DEFAULT NULL,
    added_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, movie_id),
    KEY movie_id (movie_id),
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Create watched_movies table for tracking watched status per user and movie
CREATE TABLE IF NOT EXISTS watched_movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    watched_status TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY user_movie_unique (user_id, movie_id)
);
-- User reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    review TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(movie_id),
    INDEX(user_id)
);
-- Table for storing password reset tokens
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    INDEX(user_id),
    INDEX(token)
);

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
    ),
    (
        10,
        'The Shawshank Redemption',
        'https://imgs.search.brave.com/Ht6ORiehNzc4NS62k3uFxvw7rxIGPIvV6t8x92KcShY/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly91cGxv/YWQud2lraW1lZGlh/Lm9yZy93aWtpcGVk/aWEvZW4vOC84MS9T/aGF3c2hhbmtSZWRl/bXB0aW9uTW92aWVQ/b3N0ZXIuanBn',
        'Drama',
        'A banker convicted of uxoricide forms a friendship over a quarter century with a hardened convict, while maintaining his innocence and trying to remain hopeful through simple compassion.\r\n\r\nDirector\r\nFrank Darabont\r\nWriters\r\nStephen KingFrank Darabont\r\nStars\r\nTim RobbinsMorgan FreemanBob Gunton',
        '1994',
        '2025-09-28 07:46:39'
    ),
    (
        11,
        'The Dark Knight ',
        'https://imgs.search.brave.com/hHMCjDq0nUWuULcYeQSIRT5pJXpf0O8yVZ8BS-o0LRU/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly91cGxv/YWQud2lraW1lZGlh/Lm9yZy93aWtpcGVk/aWEvZW4vMS8xYy9U/aGVfRGFya19Lbmln/aHRfJTI4MjAwOF9m/aWxtJTI5LmpwZw',
        'Action',
        'When a menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman, James Gordon and Harvey Dent must work together to put an end to the madness.',
        '2008',
        '2025-09-28 14:27:40'
    ),
    (
        12,
        'Inception',
        'https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_QL75_UX100_CR0,0,100,148_.jpg',
        'Action',
        'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O., but his tragic past may doom the project and his team to disaster.\r\n\r\nDirector\r\nChristopher Nolan\r\nWriter\r\nChristopher Nolan\r\nStars\r\nLeonardo DiCaprioJoseph Gordon-LevittElliot Page\r\n',
        '2010',
        '2025-09-28 14:29:00'
    ),
    (
        13,
        'John Wick',
        'https://imgs.search.brave.com/KyZcdRx69aW7txN-LfTBMmF45heZr0ob_c09ESEd4aE/rs:fit:200:200:1:0/g:ce/aHR0cHM6Ly9tLm1l/ZGlhLWFtYXpvbi5j/b20vaW1hZ2VzL00v/TVY1Qk1UVTJOakEx/T0Rnek1GNUJNbDVC/YW5CblhrRnRaVGd3/TVRNMk1USTRNakVA/Ll9WMV8uanBn',
        'Admin',
        'John Wick is a former hitman grieving the loss of his true love. When his home is broken into, robbed, and his dog killed, he is forced to return to action to exact revenge.\r\n\r\nDirector\r\nChad Stahelski\r\nWriter\r\nDerek Kolstad\r\nStars\r\nKeanu ReevesMichael NyqvistAlfie Allen\r\n',
        '2014',
        '2025-09-28 14:30:15'
    ),
    (
        14,
        'Mad Max: Fury Road',
        'https://imgs.search.brave.com/PReWoS1OalJ2oO0kQLUOvK1gMVCXbTIrpzEL4Z5w5dY/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly9pcnMu/d3d3Lndhcm5lcmJy/b3MuY29tL2tleWFy/dC1qcGVnL21hZF9t/YXhfZnVyeV9yb2Fk/X3dodl9rZXlhcnQu/anBn',
        'Action',
        'In a post-apocalyptic wasteland, a woman rebels against a tyrannical ruler in search for her homeland with the aid of a group of female prisoners, a psychotic worshipper and a drifter named Max.\r\n\r\nDirector\r\nGeorge Miller\r\nWriters\r\nGeorge MillerBrendan McCarthyNick Lathouris\r\nStars\r\nTom HardyCharlize TheronNicholas Hoult',
        '2015',
        '2025-09-28 16:05:06'
    ),
    (
        15,
        'Mission Impossible: Fallout',
        'https://imgs.search.brave.com/46SwBWnZkU4F6mLm03UFSNCBNjH_GL5gqnbxyBpqe_U/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly91cGxv/YWQud2lraW1lZGlh/Lm9yZy93aWtpcGVk/aWEvZW4vdGh1bWIv/Zi9mZi9NSV8lRTIl/ODAlOTNfRmFsbG91/dC5qcGcvNTEycHgt/TUlfJUUyJTgwJTkz/X0ZhbGxvdXQuanBn',
        'Action',
        'A group of terrorists plans to detonate three plutonium cores for a simultaneous nuclear attack on different cities. Ethan Hunt, along with his IMF team, sets out to stop the carnage.\r\n\r\nDirector\r\nChristopher McQuarrie\r\nWriters\r\nBruce GellerChristopher McQuarrie\r\nStars\r\nTom CruiseHenry CavillVing Rhames',
        '2018',
        '2025-09-28 16:07:08'
    ),
    (
        16,
        'Gladiator',
        'https://m.media-amazon.com/images/M/MV5BYWQ4YmNjYjEtOWE1Zi00Y2U4LWI4NTAtMTU0MjkxNWQ1ZmJiXkEyXkFqcGc@._V1_QL75_UX100_CR0,0,100,148_.jpg',
        'Action',
        'A former Roman General sets out to exact vengeance against the corrupt emperor who murdered his family and sent him into slavery.\r\n\r\nDirector\r\nRidley Scott\r\nWriters\r\nDavid FranzoniJohn LoganWilliam Nicholson\r\nStars\r\nRussell CroweJoaquin PhoenixConnie Nielsen',
        '2000',
        '2025-09-28 16:09:12'
    ),
    (
        17,
        'The Avengers',
        'https://imgs.search.brave.com/uA-09LZWgn5lHGtazsmmy7YxNBK7EBdDAI0bzhNbXEE/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly9jZG4u/bWFydmVsLmNvbS9j/b250ZW50LzF4L2F2/ZW5nZXJzYWdlb2Z1/bHRyb25fbG9iX2Ny/ZF8wMy5qcGc',
        'Action',
        'Earth\'s mightiest heroes must come together and learn to fight as a team if they are going to stop the mischievous Loki and his alien army from enslaving humanity.\r\n\r\nDirector\r\nJoss Whedon\r\nWriters\r\nJoss WhedonZak Penn\r\nStars\r\nRobert Downey Jr.Chris EvansScarlett Johansson\r\n',
        '2012',
        '2025-09-28 16:10:48'
    ),
    (
        18,
        'Sholay ',
        'https://imgs.search.brave.com/UlgT8ekdTidg5Vp_9sJgn5ph8p_iN2A4lGYImonVYY0/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly91cGxv/YWQud2lraW1lZGlh/Lm9yZy93aWtpcGVk/aWEvZW4vdGh1bWIv/NS81Mi9TaG9sYXkt/cG9zdGVyLmpwZy81/MTJweC1TaG9sYXkt/cG9zdGVyLmpwZw',
        'Action',
        'After his family is murdered by a notorious and ruthless bandit, a former police officer enlists the services of two outlaws to capture the bandit.\r\n\r\nDirector\r\nRamesh Sippy\r\nWriters\r\nJaved AkhtarSalim Khan\r\nStars\r\nSanjeev KumarDharmendraAmitabh Bachchan',
        '1975',
        '2025-09-28 16:12:09'
    ),
    (
        19,
        'Ghayal (1990)',
        'https://imgs.search.brave.com/F3nwgWcasDfibzwGWv0U1yth3jODgkQmVRbxHxCRlA8/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly91cGxv/YWQud2lraW1lZGlh/Lm9yZy93aWtpcGVk/aWEvZW4vdGh1bWIv/OS85NS9HaGF5YWwl/MkNfMTk5MF9maWxt/LmpwZy81MTJweC1H/aGF5YWwlMkNfMTk5/MF9maWxtLmpwZw',
        'Action',
        'Ajay is framed by Balwant Rai for his brother\'s murder and sent to prison. Later, he befriends three convicts and sets out to seek revenge against Balwant Rai.\r\n\r\nDirector\r\nRajkumar Santoshi\r\nWriters\r\nNasir AdibLateef BinnyVijay Deveshwar\r\nStars\r\nSunny DeolAmrish PuriMeenakshi Sheshadri',
        '1990',
        '2025-09-28 16:14:30'
    ),
    (
        20,
        'Dhoom 2',
        'https://imgs.search.brave.com/bnn8KidJYnHkxQUuwCGTIu-Fll3thcCXJyvvFksNp08/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly91cGxv/YWQud2lraW1lZGlh/Lm9yZy93aWtpcGVk/aWEvZW4vdGh1bWIv/MS8xMy9EaG9vbV8y/XyUyODIwMDZfZmls/bSUyOV9wb3N0ZXIu/anBnLzUxMnB4LURo/b29tXzJfJTI4MjAw/Nl9maWxtJTI5X3Bv/c3Rlci5qcGc',
        'Action',
        'In the Namib Desert, an elusive international thief known only as “Mr. A” skydives onto a moving train carrying Queen Elizabeth II. Disguising himself as the Queen, he steals her crown, overpowers the guards, and escapes. In response, ACP Jai Dixit and SI Ali Akbar Khan are assigned to the case, working alongside Shonali Bose, a special officer and Jai’s former colleague.\r\n\r\nJai begins analyzing Mr. A’s heists and concludes that the next target will be a rare diamond housed in one of two major museums in Mumbai. While guarding one, he discovers a fake artifact and realizes the actual heist is occurring at the second museum. There, Mr. A—disguised first as a statue and then a security guard—successfully steals the diamond.',
        '2006',
        '2025-09-28 16:17:32'
    ),
    (
        21,
        'Ghajini ',
        'https://imgs.search.brave.com/2_xYahuAaqFvKNJqZ5EOqfwKKCajVG7vCxbQ-y6FKj8/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly91cGxv/YWQud2lraW1lZGlh/Lm9yZy93aWtpcGVk/aWEvZW4vdGh1bWIv/OS85Ny9HaGFqaW5p/X0hpbmRpLmpwZy81/MTJweC1HaGFqaW5p/X0hpbmRpLmpwZw',
        'Action',
        'Sunita, a medical student, is investigating the anterograde amnesia case of Sanjay Singhania, the chairman of Air Voice, an infamous telecommunications company. Sunita does her investigation against her professor Dr. Debkumar Mitra\'s wishes as Sanjay is under criminal investigation. Sanjay, who loses his memory every 15 minutes, uses a system of photographs, notes, and tattoos on his body to recover his memory and remember his mission of avenging the murder of his fiancée Kalpana Shetty, who was killed by Ghajini Dharmatma, a kingpin and a notable socialite in Mumbai.\r\n\r\nMeanwhile, Inspector Arjun Yadav of Mumbai Police investigates a recent murder committed by Sanjay of one of Ghajini\'s men at his house. He tracks Sanjay to his apartment and sneaks inside, knocking him unconscious and tying him to the chair he was sitting on. While searching his apartment, he finds a diary in his drawer and begins to read it. Arjun learns that Sanjay, a successful entrepreneur and second generation businessman, met Kalpana, a struggling model after planning to install an advertising billboard for his company above her apartment. When his agents approach Kalpana about it, her boss misinterprets it as a romantic advance and encourages her to accept it. It is later revealed that the company already found out that not only the apartment was on rent but she also didn\'t pay the rent for two months. However, Kalpana decides to pose as Sanjay\'s girlfriend, after seeing that the false story elevates her influence at work',
        '2008',
        '2025-09-28 16:18:39'
    ),
    (
        22,
        'War',
        'https://imgs.search.brave.com/e8bknI37X70dx_rE9vgpOQTAfDAce7UBKrCVfuMIWEM/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly91cGxv/YWQud2lraW1lZGlh/Lm9yZy93aWtpcGVk/aWEvZW4vNi82Zi9X/YXJfb2ZmaWNpYWxf/cG9zdGVyLmpwZw',
        'Action',
        'n New Delhi, Major Kabir Dhaliwal is assigned to assassinate an Iraqi terrorist named Farid Haqqani, but instead kills veteran RAW agent V. K. Naidu declaring himself rogue. Defence Minister Sherna Patel and Colonel Sunil Luthra assign Kabir\'s former student Captain Khalid Rahmani to eliminate him.\r\n\r\nIn a flashback, Khalid Rahmani yearns to join Kabir\'s squad, but is initially rejected due to Khalid\'s father Major Abdul Rahmani betraying the army in a previous mission, leaving Kabir with two gunshot wounds and his partner dead. After being persuaded by Luthra, Kabir is openly hostile towards Khalid and reluctantly agrees to let Khalid join his team. The squad consists of agents Saurabh, Prateek, Muthu and Aditi.',
        '2019',
        '2025-09-28 16:19:38'
    ),
    (
        23,
        'Pathaan ',
        'https://imgs.search.brave.com/DN_aiUPs1EUO50ZRNodZGW_csTqfC-oDxRx2fqvRocE/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly91cGxv/YWQud2lraW1lZGlh/Lm9yZy93aWtpcGVk/aWEvaGkvdGh1bWIv/Ny83ZS8lRTAlQTQl/QUElRTAlQTQlQTAl/RTAlQTQlQkUlRTAl/QTQlQThfJUUwJUE0/JUFCJUUwJUE0JUJG/JUUwJUE0JUIyJUUw/JUE1JThEJUUwJUE0/JUFFXyVFMCVBNCU5/NSVFMCVBNCVCRV8l/RTAlQTQlQUElRTAl/QTUlOEIlRTAlQTQl/QjglRTAlQTUlOEQl/RTAlQTQlOUYlRTAl/QTQlQjAuanBlZy81/MTJweC0lRTAlQTQl/QUElRTAlQTQlQTAl/RTAlQTQlQkUlRTAl/QTQlQThfJUUwJUE0/JUFCJUUwJUE0JUJG/JUUwJUE0JUIyJUUw/JUE1JThEJUUwJUE0/JUFFXyVFMCVBNCU5/NSVFMCVBNCVCRV8l/RTAlQTQlQUElRTAl/QTUlOEIlRTAlQTQl/QjglRTAlQTUlOEQl/RTAlQTQlOUYlRTAl/QTQlQjAuanBlZw',
        'Action',
        'Good Movie !',
        '2023',
        '2025-09-28 16:21:47'
    ),
    (
        24,
        'Baahubali: The Beginning ',
        'https://upload.wikimedia.org/wikipedia/en/5/5f/Baahubali_The_Beginning_poster.jpg',
        'Action',
        'An elderly woman emerges from a cave underneath a mountain, carrying an infant. While trying to cross a raging river, she is swept away by the current. Facing imminent death, she holds the baby aloft and prays to Lord Shiva to spare the baby, Mahendra Baahubali, for the sake of Mahishmati Kingdom. The child is saved by the people of the Amburi tribe, and their chieftain\'s wife Sanga adopts him, naming him Siva.\r\n\r\nSiva grows up to be an ambitious and mischievous child, obsessed with ascending the mountain despite his mother\'s pleas, but fails every time. As a young man, he exhibits superhuman strength while lifting a lingam of Lord Shiva and placing it at the foot of the mountain. As he does so, a mask falls from above. Realising it belongs to a woman, Siva is determined to find its owner, and finally succeeds in climbing the mountain. Upon reaching the top, he encounters Avantika, to whom the mask belongs, and follows her. He learns that she is a member of a resistance group dedicated to overthrowing the tyrannical King Bhallaladeva of Mahishmati, and rescuing the captive princess Devasena.',
        '2015',
        '2025-09-28 16:23:36'
    ),
    (
        25,
        'Baahubali: The Conclusion ',
        'https://upload.wikimedia.org/wikipedia/en/9/93/Baahubali_2_The_Conclusion_poster.jpg',
        'Action',
        'he plot of the film connects with the predecessor. For more information on the predecessor\'s plot, see Baahubali: The Beginning.\r\nKattappa continues Amarendra Baahubali\'s tale. After his victory over the Kalakeyas, Baahubali is declared heir apparent with Bhallaladeva as his commander. Sivagami sends him on a tour of the kingdom in disguise with Kattappa, while she selects a suitable queen for him. During the journey, Baahubali witnesses Princess Devasena of Kuntala bravely repelling attackers, leading to an admiration for her. In disguise, he approaches her and is accepted into the royal palace of Kuntala as a guard.\r\n\r\nBhallaladeva views Devasena\'s portrait and Sivagami assures their marriage, sending an emissary to Kuntala. Devasena rejects the proposal, and Bijjaladeva tricks Sivagami into ordering Baahubali to capture Devasena. In Kuntala, Baahubali and Kattappa defend the palace from an attack alongside Devasena and her cousin Kumara Varma. Kattappa then reveals Baahubali as the future King of Mahishmati, and Baahubali proposes Devasena for marriage, who agrees. As the misunderstanding is brought to light, Sivagami orders Baahubali to choose between the crown and Devasena, who then chooses Devasena. As a result, Bhallaladeva is crowned King, with Baahubali as his commander, though Baahubali retains his fame with the people.',
        '2017',
        '2025-09-28 16:24:29'
    ),
    (
        26,
        'Vikram ',
        'https://imgs.search.brave.com/fzIWobyvNR6chJ5KKx86oQrEKVQWCM4zl5VrcJP4vjQ/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly9tZWRp/YS50aGVtb3ZpZWRi/Lm9yZy90L3AvdzUz/M19hbmRfaDMwMF9i/ZXN0djIvZGtJWDRk/U011VnFqZnJQR3Vu/QkpVUjdLM0xRLmpw/Zw',
        'Action',
        'Amar, the leader of a black ops squad, is called upon by Police Commissioner Jose to bring to justice a group of masked vigilantes responsible for the murder of Stephen Raj, ACP Prabhanjan and his foster father, Karnan. The gang killed Stephen after being arrested and subsequently released following his imprisonment for assisting criminals Adaikalam and Anbu. Amar leads the investigation by digging into Karnan\'s life, as he finds the murder out of place since Karnan was an ordinary man while the other two were high-ranking NCB officials.\r\n\r\nAmar discovers Karnan\'s addiction to alcohol and prostitutes while overprotecting his foster grandson. He found two drug containers missing that Sandhanam, who runs a larger syndicate than Adaikalam, called Vettai Vagaiyara, is hunting. The other two containers were part of a shipment to Rolex, Sandhanam\'s boss, who promised to help form his government if he received the drugs. If not, Rolex would kill Sandhanam and his family. Amar discovers Karnan\'s addictions are cover for a covert operation he was running.',
        '2022',
        '2025-09-28 16:25:33'
    ),
    (
        27,
        'K.G.F: Chapter 1 ',
        'https://imgs.search.brave.com/zhhhLUImDR1c7G63O28cByGOCc0kkTUQV2M_WfyCmMo/rs:fit:200:200:1:0/g:ce/aHR0cHM6Ly9tLm1l/ZGlhLWFtYXpvbi5j/b20vaW1hZ2VzL1Mv/cHYtdGFyZ2V0LWlt/YWdlcy9mMjM5Mzdk/NTA0MDNlOTE5ZDc0/MDEyMjE4ZjZmYzIy/ZDc5ZTkwNWUyNmM5/N2IxOTZmY2QzYTNk/MGU1NmE5NTQxLmpw/Zw',
        'Action',
        'Journalist Anand Ingalagi\'s book El Dorado, which detailed the events at the Kolar Gold Fields (KGF) between 1951 and 2018, was banned by the Indian government, but a television news channel procures a copy and interviews him.\r\n\r\nIn 1951, Raja "Rocky" Krishnappa Bairya was born to a poor, underage girl named Shanti. On the same day, government officials, accompanied by Suryavardhan, arrived to investigate a strange yellow rock discovered by workers digging a well in southern Mysore State. Upon realizing that the rock was gold ore, Suryavardhan, a powerful don and politician of the time, killed the officials and leased the land for 99 years under the pretext of running a limestone mine called Narachi, while secretly establishing K.G.F and his crime syndicate. To manage the empire, Suryavardhan had appointed five associates: Adheera, his ruthless younger brother, who headed the security at K.G.F; Kamal, son of Suryavardhan\'s late associate Bhargav, who looks after gold refinery in Varca; Rajendra Desai, who oversees the transportation of gold bars coming from the refinery; Andrews, who oversees the gold smuggling in the Western Coast; and Guru Pandian, president of the incumbent DYSS party who gave political influence to Suryavardhan. Suryavardhan suffers a stroke and appoints his elder son Garuda as the future heir of K.G.F, expecting Adheera to serve as his son\'s aide. His associates, now eyeing the riches of K.G.F, plan to assassinate Garuda. Adheera is presumed dead after Garuda bombs his car in retaliation for Adheera\'s unsuccessful assassination attempt.',
        '2018',
        '2025-09-28 16:27:35'
    ),
    (
        28,
        'K.G.F: Chapter 2',
        'https://upload.wikimedia.org/wikipedia/en/d/d0/K.G.F_Chapter_2.jpg',
        'Action',
        'After detailing the events in KGF: Chapter 1, Anand Ingalagi suffers a stroke and his son, Vijayendra Ingalagi takes over to narrate the rest of the story.\r\n\r\nRocky kills heir apparent Virat and takes over the Kolar Gold Fields, keeping Reena hostage to ensure the cooperation of Guru Pandian, Andrews, Kamal and Rajendra Desai. But when Rocky shows his arrogance and teases Reena, Kamal gets enraged and threatens to kill Rocky, which angeres Rocky and he shoots Kamal on the spot. After this, Rocky issues orders to start work in eight hidden mines, while Vanaram, who was captured by Rocky, decides to help him.\r\n\r\nMeanwhile, Adheera resurfaces and kills all guards at an outpost. In a ruse to bring Rocky to Adheera, Andrews kills Desai to lure Reena outside KGF, and John abducts Reena as per Adheera\'s order. While trying to save Reena, Adheera shoots Rocky but spares his life, while his men roadblock all gold exports from KGF. Later, Shetty ties up with other subordinates of Andrews across India\'s western coast and exterminates Rocky\'s allies with Inayat Khalil\'s newfound support. Rocky vacates the mansion with Reena with excessive gold and currency from the treasury. While chaos ensues among people of KGF, Vanaram deduces that Rocky is headed for a throat clamp.',
        '2022',
        '2025-09-28 16:28:28'
    ),
    (
        29,
        'RRR ',
        'https://translate.google.com/website?sl=en&tl=hi&hl=hi&client=srp&u=https://upload.wikimedia.org/wikipedia/en/d/d7/RRR_Poster.jpg',
        'Action',
        'During the British Raj in 1920, the tyrannical Governor Scott Buxton and his wife Catherine visit a forest in Adilabad, where they abduct Malli, an artistically talented young girl from the Gond tribe. The tribe\'s guardian Komaram Bheem embarks for Delhi to rescue her, disguising himself as a Muslim named Akhtar. The Nizamate of Hyderabad warns Buxton\'s office of the impending danger and advises them to return the child. Undeterred, Catherine enlists Alluri Sitarama Raju, an ambitious Indian Imperial Police officer, to quell the threat, promising a promotion should he capture Bheem alive or a bounty otherwise. Ram and his uncle Venkateswarulu attend a pro-independence gathering in disguise, where Bheem\'s aide Lachhu attempts to recruit them into Bheem\'s plot. On the way to Bheem\'s hideout, Lachhu discerns Ram\'s identity and flees. Shortly afterward, Ram and Bheem witness a child getting trapped by a train wreck and work together to rescue him. Unaware of their opposing allegiances, they form a close friendship.\r\n\r\nWith Ram\'s help, Bheem courts an Englishwoman named Jenny, discovering that she is staying with the Buxtons. Following a party where Ram and Bheem out-dance the pompous English attendees, Jenny invites Bheem to her residence, where he finds Malli and promises to free her soon after. Meanwhile, Ram locates Lachhu and apprehends him. While being interrogated, Lachhu sets a banded krait onto Ram and warns him of his imminent death and that the antidote is only known to the Gonds. Bheem finds Ram, saves his life, and divulges his tribal identity and mission, unaware of Ram\'s true identity. That night, at an event to honour Governor Buxton, Bheem\'s men barge into his residence with a truck filled with wild animals; they maul Buxton\'s guards, creating havoc among the guests. Ram arrives and fights Bheem; Bheem is forced to stand down when Buxton holds Malli at gunpoint. Bheem is arrested and Ram is promoted.',
        '2022',
        '2025-09-28 16:29:35'
    ),
    (
        30,
        'Indiana Jones: Raiders of the Lost Ark',
        'https://imgs.search.brave.com/xbrfHwZK807p7uKur8NrB6svtWBJ1TAbLe67ZKeuHHo/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly91cGxv/YWQud2lraW1lZGlh/Lm9yZy93aWtpcGVk/aWEvZW4vdGh1bWIv/YS9hNi9SYWlkZXJz/X29mX3RoZV9Mb3N0/X0Fya19UaGVhdHJp/Y2FsX1Bvc3Rlci5q/cGcvNTEycHgtUmFp/ZGVyc19vZl90aGVf/TG9zdF9BcmtfVGhl/YXRyaWNhbF9Qb3N0/ZXIuanBn',
        'Adventure',
        'In 1936, American archaeologist Indiana Jones recovers a Golden Idol from a booby-trapped Peruvian temple. Rival archaeologist René Belloq corners him and steals the idol; Jones escapes in a waiting float plane.\r\n\r\nAfter returning to the United States, Jones is briefed by two Army Intelligence agents that Nazi German forces are excavating at Tanis, Egypt, and one of their telegrams mentions Jones\'s former mentor Abner Ravenwood. He deduces that the Nazis are seeking the Ark of the Covenant, which Adolf Hitler believes will make their army invincible. The agents recruit Jones to recover the Ark first.\r\n\r\nAt a bar in Nepal, Jones reunites with Abner\'s daughter Marion, with whom Jones once had an illicit relationship, and learns that Abner is dead. The bar is set ablaze during a scuffle with Gestapo agent Arnold Toht, who arrives to take a medallion from Marion. Toht attempts to recover the medallion from the flames, but only burns its image into his hand. Jones and Marion safely take the medallion and escape.\r\n\r\nTraveling to Cairo, the pair meet Jones\'s friend Sallah. He reveals Belloq is assisting the Nazis, who have fashioned an incomplete replica medallion from the burns on Toht\'s hand. Nazi soldiers and mercenaries attack Jones, and Marion is seemingly killed, leaving Jones despondent. An imam deciphers the medallion for Jones, revealing that one side bears a warning against disturbing the Ark, and the other bears the complete measurements for the "staff of Ra", an item which, when combined with the medallion, is used to locate the Ark. Jones and Sallah realize that the Nazis are digging in the wrong location, infiltrate the Nazi dig site, and use the medallion and the correctly sized staff of Ra to locate the Well of Souls, the Ark\'s resting place. The pair recover the Ark, a golden, intricately decorated chest, but Belloq and the Nazis discover them and seize it. Jones and Marion, whom Belloq has been holding captive, are sealed inside the well, but the pair escape and Jones captures a truck carrying the Ark.',
        '1981',
        '2025-09-28 16:30:35'
    ),
    (
        31,
        'Jurassic Park',
        'https://upload.wikimedia.org/wikipedia/en/e/e7/Jurassic_Park_poster.jpg',
        'Adventure',
        'Industrialist John Hammond has created Jurassic Park, a theme park of de-extincted dinosaurs, on the tropical island Isla Nublar, off the coast of Costa Rica. After a Velociraptor kills a dinosaur handler, the park\'s investors, represented by lawyer Donald Gennaro, threaten to pull funding unless experts certify the island\'s safety. Gennaro invites chaotician Ian Malcolm, and Hammond invites paleontologist Alan Grant and paleobotanist Ellie Sattler. Upon arrival, the group is shocked to see living Brachiosaurus and Parasaurolophus. At the park\'s visitor center, the group learns that the cloning was accomplished by extracting dinosaur DNA from prehistoric mosquitoes preserved in amber. DNA from frogs, among other animals, was used to fill in gaps in the dinosaurs\' genome.\r\n\r\nTo prevent breeding, the dinosaurs were made female by direct chromosome manipulation. The group witnesses the hatching of a baby Velociraptor and visits the raptor enclosure. During lunch, the group debates the ethics of cloning and the park\'s creation. Malcolm warns of the implications of genetic engineering while Grant and Sattler express uncertainty over the ability of humans and dinosaurs to coexist. Hammond\'s grandchildren, 12-year-old Lex and 9-year-old Tim, join the others for a park tour in two self-driving electric Ford Explorer tour vehicles, while Hammond oversees them from the control room. Most of the dinosaurs fail to appear, and the group encounters a sick Triceratops. The tour is cut short as a tropical storm approaches. The park employees leave for the mainland on a boat while the visitors return to their railed-electric tour vehicles, except Sattler, who stays behind with the park\'s veterinarian, Dr. Harding, to study the Triceratops.',
        '1993',
        '2025-09-28 17:21:31'
    ),
    (
        32,
        'The Lord of the Rings: The Fellowship of the Ring ',
        'https://upload.wikimedia.org/wikipedia/en/f/fb/Lord_Rings_Fellowship_Ring.jpg',
        'Adventure',
        'In the Second Age of Middle-earth, the lords of Elves, Dwarves, and Men each receive Rings of Power. Unbeknownst to them, the Dark Lord Sauron forges the One Ring in Mount Doom, imbuing it with his power to control the other Rings and conquer Middle-earth. A final alliance of Men and Elves battles Sauron\'s forces in Mordor. Isildur of Gondor severs Sauron\'s finger, vanquishing him and returning him to spirit form, marking the beginning of the Third Age. The Ring corrupts Isildur, who takes it and is later killed by Orcs. The Ring is lost in a river for 2,500 years until it is found by Gollum, who has possessed it for over four centuries. The Ring abandons Gollum and is found by a hobbit named Bilbo Baggins.\r\n\r\nSixty years later, Bilbo celebrates his 111th birthday in the Shire with his old friend, Gandalf the Grey. He leaves the Shire for one last adventure, passing on his inheritance, including the Ring, to his nephew Frodo. Gandalf investigates the Ring, learns its true nature, and discovers that Gollum revealed two words during interrogation: "Shire" and "Baggins." Gandalf warns Frodo to leave the Shire. As Frodo departs with his gardener friend, Samwise Gamgee, Gandalf heads to Isengard to seek counsel from his friend, the powerful wizard Saruman. Saruman reveals Sauron has dispatched his nine Nazgûl servants to retrieve the Ring. Gandalf immediately attempts to flee to warn Frodo, but is imprisoned by Saruman who has allied himself with Sauron, communicating with him via a palantír.',
        '2001',
        '2025-09-28 17:22:31'
    ),
    (
        33,
        'Pirates of the Caribbean: The Curse of the Black Pearl',
        'https://upload.wikimedia.org/wikipedia/en/8/89/Pirates_of_the_Caribbean_-_The_Curse_of_the_Black_Pearl.png',
        'Adventure',
        'In the early 18th century, Governor Weatherby Swann and his daughter, Elizabeth, sail aboard HMS Dauntless captained by Lieutenant Norrington and his crew. They encounter a shipwreck and rescue a boy named Will Turner. Elizabeth notices a gold medallion around Will\'s neck and takes it while he is unconscious, before seeing a ghostly ship sailing away. Eight years later in Port Royal, Jamaica, Captain Norrington is being promoted to commodore while Will works as a blacksmith, and pirate captain Jack Sparrow arrives in Port Royal seeking a ship. Norrington proposes to Elizabeth atop a cliff, but she faints and falls into the ocean, causing the medallion she is carrying to emit a pulse. Jack rescues Elizabeth and discovers the medallion. Governor Swann orders Jack\'s execution after he is identified as a pirate, but Jack flees into Will\'s smithy, where he is caught after Will duels him to a stalemate.\r\n\r\nThat night, Port Royal is attacked by the pirate crew of the Black Pearl, who are in search of the medallion. They take Elizabeth hostage aboard the ship to meet Captain Barbossa after she identifies herself as "Elizabeth Turner". Barbossa explains that the medallion is one of 882 cursed gold pieces used to bribe Hernán Cortés to stop his slaughter of the Aztecs. After finding and stealing the cursed gold at Isla de Muerta, the crew became cursed undead zombies who cannot feel pleasure or pain. To lift the curse, the crew has returned all the gold with an offering of blood from each member, but one medallion belonging to "Bootstrap" Bill Turner, a crew member thrown overboard after the theft, is missing. Believing Elizabeth to be Bootstrap\'s child, Barbossa intends to use her blood for the ritual.',
        '2003',
        '2025-09-28 17:23:26'
    ),
    (
        34,
        'Avatar',
        'https://upload.wikimedia.org/wikipedia/en/d/d6/Avatar_%282009_film%29_poster.jpg',
        'Adventure',
        'In 2154, Earth suffers from resource exhaustion and ecological collapse. The Resources Development Administration (RDA) mines the valuable mineral unobtanium on Pandora, a lush habitable moon orbiting a gas giant in the Alpha Centauri star system. Pandora, whose atmosphere is inhospitable to humans, is inhabited by the Na\'vi, 10-foot-tall (3.0 m), blue-skinned, sapient humanoids that live in harmony with nature.\r\n\r\nTo explore Pandora, genetically matched human scientists control Na\'vi-human hybrids called "avatars", allowing them to breathe'
    )
ON DUPLICATE KEY UPDATE
    title = VALUES(title);
