-- =====================================================
-- Library database: tblBookType, tblAuthor, tblBook, tblBookAuthor
-- =====================================================

CREATE DATABASE IF NOT EXISTS my_lap_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE my_lap_db;

-- ---------- 1. CREATE TABLES ----------

CREATE TABLE IF NOT EXISTS tblBookType (
    BookTypeID   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    BookTypeName VARCHAR(100) NOT NULL,
    created_at   TIMESTAMP NULL,
    updated_at   TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS tblAuthor (
    AuthorID   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    AuthorName VARCHAR(100) NOT NULL,
    Gender     VARCHAR(10)  NULL,
    DOB        DATE         NULL,
    POB        VARCHAR(255) NULL,
    Address    VARCHAR(255) NULL,
    Phone      VARCHAR(20)  NULL,
    Email      VARCHAR(100) NULL,
    Photo      VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS tblBook (
    BookID      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    BookTitle   VARCHAR(255) NOT NULL,
    BookTypeID  BIGINT UNSIGNED NOT NULL,
    PublishDate DATE         NULL,
    NumOfPages  INT          NULL,
    NumOfCopies INT          NOT NULL DEFAULT 0,
    Edition     VARCHAR(50)  NULL,
    Publisher   VARCHAR(100) NULL,
    BookSource  VARCHAR(100) NULL,
    Remark      TEXT         NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL,
    CONSTRAINT fk_book_booktype FOREIGN KEY (BookTypeID)
        REFERENCES tblBookType (BookTypeID)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS tblBookAuthor (
    BookID     BIGINT UNSIGNED NOT NULL,
    AuthorID   BIGINT UNSIGNED NOT NULL,
    AuthorDate DATE NULL,
    Remark     TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (BookID, AuthorID),
    CONSTRAINT fk_bookauthor_book FOREIGN KEY (BookID)
        REFERENCES tblBook (BookID) ON DELETE CASCADE,
    CONSTRAINT fk_bookauthor_author FOREIGN KEY (AuthorID)
        REFERENCES tblAuthor (AuthorID) ON DELETE CASCADE
);

-- ---------- 2. INSERT (sample data) ----------

INSERT INTO tblBookType (BookTypeName, created_at, updated_at) VALUES
    ('Programming', NOW(), NOW()),
    ('Novel', NOW(), NOW());

INSERT INTO tblAuthor (AuthorName, Gender, DOB, POB, Address, Phone, Email, created_at, updated_at) VALUES
    ('Sok Dara', 'Male', '1990-05-01', 'Phnom Penh', 'Phnom Penh', '012345678', 'dara@gmail.com', NOW(), NOW()),
    ('Chan Sreymom', 'Female', '1995-08-12', 'Siem Reap', 'Siem Reap', '098765432', 'sreymom@gmail.com', NOW(), NOW());

INSERT INTO tblBook (BookTitle, BookTypeID, PublishDate, NumOfPages, NumOfCopies, Edition, Publisher, BookSource, created_at, updated_at) VALUES
    ('Learn Laravel', 1, '2024-01-10', 300, 5, '1st', 'ABC Press', 'Donation', NOW(), NOW()),
    ('Flutter Basics', 1, '2025-03-20', 250, 3, '2nd', 'XYZ Press', 'Purchase', NOW(), NOW());

INSERT INTO tblBookAuthor (BookID, AuthorID, AuthorDate, Remark, created_at, updated_at) VALUES
    (1, 1, '2024-01-01', 'Main author', NOW(), NOW()),
    (2, 1, '2025-03-01', 'Co-author', NOW(), NOW()),
    (2, 2, '2025-03-01', 'Main author', NOW(), NOW());

-- ---------- 3. SELECT ----------

SELECT * FROM tblBookType;
SELECT * FROM tblAuthor;
SELECT * FROM tblBook;
SELECT * FROM tblBookAuthor;

-- Books with their type and authors
SELECT b.BookID, b.BookTitle, t.BookTypeName, a.AuthorName, ba.Remark
FROM tblBook b
JOIN tblBookType t        ON t.BookTypeID = b.BookTypeID
LEFT JOIN tblBookAuthor ba ON ba.BookID = b.BookID
LEFT JOIN tblAuthor a      ON a.AuthorID = ba.AuthorID
ORDER BY b.BookID;

-- ---------- 4. UPDATE ----------

UPDATE tblBook       SET BookTitle = 'Learn Laravel 12', updated_at = NOW() WHERE BookID = 1;
UPDATE tblAuthor     SET Phone = '011223344', updated_at = NOW()          WHERE AuthorID = 1;
UPDATE tblBookType   SET BookTypeName = 'IT', updated_at = NOW()          WHERE BookTypeID = 1;
UPDATE tblBookAuthor SET Remark = 'Updated', updated_at = NOW()           WHERE BookID = 1 AND AuthorID = 1;

-- ---------- 5. DELETE (child tables first) ----------

DELETE FROM tblBookAuthor WHERE BookID = 2 AND AuthorID = 2;
DELETE FROM tblBook       WHERE BookID = 2;
DELETE FROM tblAuthor     WHERE AuthorID = 2;
DELETE FROM tblBookType   WHERE BookTypeID = 2;
