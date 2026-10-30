-- สร้างฐานข้อมูล (ถ้ายังไม่มี)
-- CREATE DATABASE IF NOT EXISTS kinraidee CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE kinraidee;

-- 1. หมวดหมู่ร้าน
CREATE TABLE TBL_CATEGORY (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. ข้อมูลร้านอาหาร
CREATE TABLE TBL_RESTAURANTS (
    restaurant_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    restaurant_name VARCHAR(200) NOT NULL,
    address VARCHAR(255),
    latitude DECIMAL(10,7),
    longitude DECIMAL(10,7),
    open_time TIME DEFAULT '08:00:00',
    close_time TIME DEFAULT '22:00:00',
    closed_day VARCHAR(100),
    is_open TINYINT(1) DEFAULT 1,
    budget_min INT DEFAULT 0,
    budget_max INT DEFAULT 999,
    cuisine_origin VARCHAR(50),
    atmosphere ENUM('แอร์','พัดลม','ทั้งคู่') DEFAULT 'ทั้งคู่',
    submitted_by VARCHAR(50),
    status ENUM('pending','active') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES TBL_CATEGORY(category_id)
);

-- 3. แอดมิน
CREATE TABLE TBL_ADMIN (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. เซสชันโหวต
CREATE TABLE TBL_VOTE_SESSIONS (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    session_code VARCHAR(8) NOT NULL UNIQUE,
    session_name VARCHAR(100),
    created_by VARCHAR(50),
    status ENUM('open','closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. คะแนนโหวต
CREATE TABLE TBL_VOTES (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    voter_token VARCHAR(64) NOT NULL,
    restaurant_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES TBL_VOTE_SESSIONS(session_id) ON DELETE CASCADE,
    FOREIGN KEY (restaurant_id) REFERENCES TBL_RESTAURANTS(restaurant_id) ON DELETE CASCADE,
    UNIQUE KEY one_vote_per_person (session_id, voter_token)
);

-- ใส่ข้อมูลตัวอย่าง: หมวดหมู่
INSERT INTO TBL_CATEGORY (category_name) VALUES
('อาหารตามสั่ง'),
('ก๋วยเตี๋ยว'),
('ส้มตำ'),
('ข้าวแกง'),
('ปิ้งย่าง'),
('ฮาลาล');

-- ใส่ข้อมูลตัวอย่าง: ร้านอาหาร
INSERT INTO TBL_RESTAURANTS 
(category_id, restaurant_name, address, latitude, longitude, budget_min, budget_max, cuisine_origin, atmosphere, is_open, status) VALUES
(1, 'ร้านเจ๊ไฝ', 'ซอยสุขุมวิท 11 แยก 2', 13.7563000, 100.5018000, 40, 80, 'ไทย', 'พัดลม', 1, 'active'),
(2, 'ราเมนซางโตะ', 'สยามสแควร์ ซอย 3', 13.7450000, 100.5340000, 150, 300, 'ญี่ปุ่น', 'แอร์', 1, 'active'),
(3, 'ส้มตำเจ๊แดง', 'ตลาดนางเลิ้ง', 13.7620000, 100.5100000, 40, 90, 'ไทย', 'ทั้งคู่', 1, 'active'),
(6, 'ข้าวมันไก่ฮาลาล', 'อารีย์ ซอย 1', 13.7800000, 100.5400000, 40, 70, 'อาหรับ (ฮาลาล)', 'แอร์', 1, 'active');

-- ใส่ข้อมูลแอดมินตัวอย่าง: admin / 1234
INSERT INTO TBL_ADMIN (username, password_hash) VALUES
('admin', '$2y$10$EixZaYb4UuUuKq7GqR8wPeWb4H0u9xXzYqK7sT5aP3bD9eF8gH7iJ');
-- รหัสผ่าน 1234 เข้ารหัสเรียบร้อยแล้ว