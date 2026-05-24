-- ============================================================
--  BookLoop Database
--  ICT312 Advanced Web Information Systems
--  Drop & recreate for a fresh install
-- ============================================================

DROP DATABASE IF EXISTS bookloop_db;
CREATE DATABASE bookloop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bookloop_db;

-- ── Users ────────────────────────────────────────────────────
CREATE TABLE users (
    user_id       INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(30)      NOT NULL UNIQUE,
    email         VARCHAR(100)     NOT NULL UNIQUE,
    password_hash VARCHAR(255)     NOT NULL,
    first_name    VARCHAR(50)      NOT NULL,
    last_name     VARCHAR(50)      NOT NULL,
    location      VARCHAR(100)     DEFAULT NULL,
    bio           TEXT             DEFAULT NULL,
    avatar        VARCHAR(500)     DEFAULT NULL,
    is_admin      TINYINT(1)       NOT NULL DEFAULT 0,
    is_active     TINYINT(1)       NOT NULL DEFAULT 1,
    created_at    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login    DATETIME         DEFAULT NULL
) ENGINE=InnoDB;

-- ── Books ─────────────────────────────────────────────────────
CREATE TABLE books (
    book_id       INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED     NOT NULL,
    title         VARCHAR(200)     NOT NULL,
    author        VARCHAR(150)     NOT NULL,
    genre         ENUM('Fiction','Non-Fiction','Science Fiction','Mystery',
                       'Fantasy','Romance','Biography','History',
                       'Science','Self-Help','Children','Other') NOT NULL,
    description   TEXT             DEFAULT NULL,
    condition_status ENUM('New','Like New','Good','Fair','Poor') NOT NULL DEFAULT 'Good',
    cover_image   VARCHAR(500)     DEFAULT NULL,
    is_available  TINYINT(1)       NOT NULL DEFAULT 1,
    views         INT UNSIGNED     NOT NULL DEFAULT 0,
    listed_at     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_genre     (genre),
    INDEX idx_available (is_available),
    INDEX idx_user      (user_id),
    FULLTEXT idx_search (title, author)
) ENGINE=InnoDB;

-- ── Exchange Requests ─────────────────────────────────────────
CREATE TABLE exchange_requests (
    request_id    INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    book_id       INT UNSIGNED     NOT NULL,
    requester_id  INT UNSIGNED     NOT NULL,
    owner_id      INT UNSIGNED     NOT NULL,
    message       TEXT             DEFAULT NULL,
    status        ENUM('pending','accepted','declined','completed','cancelled')
                                   NOT NULL DEFAULT 'pending',
    created_at    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME         DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id)      REFERENCES books(book_id)   ON DELETE CASCADE,
    FOREIGN KEY (requester_id) REFERENCES users(user_id)   ON DELETE CASCADE,
    FOREIGN KEY (owner_id)     REFERENCES users(user_id)   ON DELETE CASCADE,
    INDEX idx_requester (requester_id),
    INDEX idx_owner     (owner_id),
    INDEX idx_status    (status)
) ENGINE=InnoDB;

-- ── Reviews ───────────────────────────────────────────────────
CREATE TABLE reviews (
    review_id     INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    reviewer_id   INT UNSIGNED     NOT NULL,
    reviewee_id   INT UNSIGNED     NOT NULL,
    request_id    INT UNSIGNED     NOT NULL,
    rating        TINYINT UNSIGNED NOT NULL,
    comment       TEXT             DEFAULT NULL,
    created_at    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5),
    FOREIGN KEY (reviewer_id) REFERENCES users(user_id)            ON DELETE CASCADE,
    FOREIGN KEY (reviewee_id) REFERENCES users(user_id)            ON DELETE CASCADE,
    FOREIGN KEY (request_id)  REFERENCES exchange_requests(request_id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (reviewer_id, request_id)
) ENGINE=InnoDB;

-- ── Notifications ─────────────────────────────────────────────
CREATE TABLE notifications (
    notif_id      INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED     NOT NULL,
    type          VARCHAR(30)      NOT NULL,
    title         VARCHAR(200)     NOT NULL,
    message       TEXT             DEFAULT NULL,
    link          VARCHAR(300)     DEFAULT NULL,
    is_read       TINYINT(1)       NOT NULL DEFAULT 0,
    created_at    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_unread (user_id, is_read)
) ENGINE=InnoDB;

-- Passwords:  admin → Admin@1234  |  others → Test@1234

INSERT INTO users (username, email, password_hash, first_name, last_name, location, bio, is_admin) VALUES
('admin', 'admin@bookloop.com',
 '$2y$12$/5N3bkZeNTxP95J0eYOPa.cEpUnSyq8eScvMioeJ8pvF4RxlKw2aC',
 'Admin', 'User', 'Sydney, NSW', 'BookLoop platform administrator.', 1);

INSERT INTO users (username, email, password_hash, first_name, last_name, location, bio) VALUES
('Nirmal_hero', 'niraml@example.com',
 '$2y$12$wv2Y6DXPmRomobHUccyv8ui3/xl5R0LxMplU7uuJvBZct4LBFLrqm',
 'Nirmal', 'Basnet', 'Melbourne, VIC', 'Avid reader. Love literary fiction and sci-fi.'),
('bishal_books', 'bishal@example.com',
 '$2y$12$wv2Y6DXPmRomobHUccyv8ui3/xl5R0LxMplU7uuJvBZct4LBFLrqm',
 'Bishal', 'Jha', 'Brisbane, QLD', 'Mystery and thriller fan.'),
('Neymar_lit', 'neymar@example.com',
 '$2y$12$wv2Y6DXPmRomobHUccyv8ui3/xl5R0LxMplU7uuJvBZct4LBFLrqm',
 'Neymar', 'JR', 'Perth, WA', 'History and biography enthusiast.');

INSERT INTO books (user_id, title, author, genre, description, condition_status, cover_image) VALUES
(2, 'Dune', 'Frank Herbert', 'Science Fiction',
 'Set on the desert planet Arrakis, Dune tells the story of young Paul Atreides whose family assumes control of the planet — the only source of the most valuable substance in the universe.',
 'Like New', 'https://picsum.photos/seed/dune-bl/300/450'),
(2, 'The Great Gatsby', 'F. Scott Fitzgerald', 'Fiction',
 'A story of the fabulously wealthy Jay Gatsby and his obsessive love for Daisy Buchanan, set against the backdrop of jazz-age America.',
 'Good', 'https://picsum.photos/seed/gatsby-bl/300/450'),
(2, 'Sapiens', 'Yuval Noah Harari', 'Non-Fiction',
 'A sweeping narrative of human history, from the Stone Age through the 21st century, examining how biology and history have defined us.',
 'Like New', 'https://picsum.photos/seed/sapiens-bl/300/450'),
(3, 'Gone Girl', 'Gillian Flynn', 'Mystery',
 'On their fifth wedding anniversary, Amy Dunne disappears. The police suspect her husband Nick. A twisting psychological thriller.',
 'Good', 'https://picsum.photos/seed/gonegirl-bl/300/450'),
(3, 'The Name of the Wind', 'Patrick Rothfuss', 'Fantasy',
 'The tale of Kvothe — from his childhood in a troupe of traveling players, to his brazen bid to enter a legendary school of magic.',
 'New', 'https://picsum.photos/seed/namewind-bl/300/450'),
(3, 'Atomic Habits', 'James Clear', 'Self-Help',
 'Tiny changes, remarkable results. A proven framework for building good habits and breaking bad ones, backed by science.',
 'Like New', 'https://picsum.photos/seed/atomichab-bl/300/450'),
(4, 'The Hobbit', 'J.R.R. Tolkien', 'Fantasy',
 'Bilbo Baggins is swept into an epic quest to reclaim the lost Dwarf Kingdom of Erebor from the fearsome dragon Smaug.',
 'Fair', 'https://picsum.photos/seed/hobbit-bl/300/450'),
(4, 'To Kill a Mockingbird', 'Harper Lee', 'Fiction',
 'A Pulitzer Prize-winning story of racial injustice and moral growth, told through the eyes of young Scout Finch in the American South.',
 'Good', 'https://picsum.photos/seed/mockingbrd-bl/300/450'),
(4, 'A Brief History of Time', 'Stephen Hawking', 'Science',
 'Hawking attempts to explain cosmology — the Big Bang, black holes, light cones and superstring theory — for a general audience.',
 'Good', 'https://picsum.photos/seed/hawking-bl/300/450'),
(2, '1984', 'George Orwell', 'Fiction',
 'A dystopian novel set in a totalitarian society under the constant surveillance of Big Brother. One of the most influential books of the 20th century.',
 'Like New', 'https://picsum.photos/seed/1984orw-bl/300/450'),
(3, 'The Alchemist', 'Paulo Coelho', 'Fiction',
 'Santiago, an Andalusian shepherd boy, dreams of finding a worldly treasure. A philosophical novel about following your dreams.',
 'Good', 'https://picsum.photos/seed/alchemst-bl/300/450'),
(4, 'Educated', 'Tara Westover', 'Biography',
 'A memoir about a young woman kept out of school who leaves her survivalist family and goes on to earn a PhD from Cambridge University.',
 'New', 'https://picsum.photos/seed/educated-bl/300/450');

INSERT INTO exchange_requests (book_id, requester_id, owner_id, message, status) VALUES
(4, 2, 3, 'Hi! I would love to borrow Gone Girl. I have some great sci-fi you might enjoy in return!', 'pending');
