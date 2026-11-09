-- ============================================================
-- KinRaiDee (กินไรดี) — Database Schema
-- ใช้กับ MySQL 5.7+ / MariaDB 10.2+
-- วิธีใช้: นำไฟล์นี้ไป import ใน phpMyAdmin หรือรันใน MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS kinraidee DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kinraidee;

-- ============================================================
-- 1. ตารางหมวดหมู่ร้านอาหาร
-- ============================================================
CREATE TABLE IF NOT EXISTS TBL_CATEGORY (
    category_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. ตารางผู้ดูแลระบบ (แอดมิน)
--    เก็บรหัสผ่านแบบ hash ด้วย password_hash() ใน PHP
-- ============================================================
CREATE TABLE IF NOT EXISTS TBL_ADMIN (
    admin_id      INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. ตารางร้านอาหาร (ตารางหลัก)
--    lat/lng เก็บเป็น DECIMAL(10,7) — คำนวณระยะทางด้วย
--    ST_Distance_Sphere(POINT(lng, lat), POINT(user_lng, user_lat))
-- ============================================================
CREATE TABLE IF NOT EXISTS TBL_RESTAURANTS (
    restaurant_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_id     INT NOT NULL,
    restaurant_name VARCHAR(255) NOT NULL,
    country         VARCHAR(100) DEFAULT 'ไทย',
    atmosphere      ENUM('แอร์','พัดลม','ทั้งคู่') DEFAULT 'ทั้งคู่',
    budget_max      DECIMAL(10,2) NOT NULL DEFAULT 0,
    address         TEXT,
    phone           VARCHAR(20) DEFAULT NULL,          -- เบอร์โทรร้าน
    social          VARCHAR(255) DEFAULT NULL,         -- ลิงค์ Facebook / IG / Line
    map_url         VARCHAR(500) DEFAULT NULL,         -- ลิงค์ Google Maps
    lat             DECIMAL(10,7) NOT NULL,            -- ละติจูด (ดึงจากลิงค์ Maps อัตโนมัติ)
    lng             DECIMAL(10,7) NOT NULL,            -- ลองจิจูด
    open_time       TIME NOT NULL DEFAULT '08:00',
    close_time      TIME NOT NULL DEFAULT '20:00',
    closed_day      SET('จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์','อาทิตย์') DEFAULT NULL,
    is_open         TINYINT(1) DEFAULT 1,              -- 1=เปิดปกติ, 0=ปิดฉุกเฉิน
    is_approved     TINYINT(1) DEFAULT 0,              -- 0=รออนุมัติ, 1=อนุมัติแล้ว
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES TBL_CATEGORY(category_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ดัชนีช่วยเรียงลำดับและค้นหาเร็วขึ้น
CREATE INDEX idx_restaurant_approved ON TBL_RESTAURANTS(is_approved);
CREATE INDEX idx_restaurant_open     ON TBL_RESTAURANTS(is_open);
CREATE INDEX idx_restaurant_cat      ON TBL_RESTAURANTS(category_id);
CREATE INDEX idx_restaurant_location ON TBL_RESTAURANTS(lat, lng);

-- ============================================================
-- 4. ตารางกลุ่มโหวต (Group Voting)
-- ============================================================
CREATE TABLE IF NOT EXISTS TBL_VOTE_GROUP (
    group_id   INT AUTO_INCREMENT PRIMARY KEY,
    share_code CHAR(4) NOT NULL UNIQUE,                -- โค้ดแชร์ 4 ตัวอักษร เช่น KR7X
    is_closed  TINYINT(1) DEFAULT 0,                   -- 0=เปิดโหวต, 1=ปิด/ประกาศผลแล้ว
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. ตารางตัวเลือกร้านในแต่ละกลุ่มโหวต
-- ============================================================
CREATE TABLE IF NOT EXISTS TBL_VOTE_OPTION (
    option_id     INT AUTO_INCREMENT PRIMARY KEY,
    group_id      INT NOT NULL,
    restaurant_id INT NOT NULL,
    FOREIGN KEY (group_id)      REFERENCES TBL_VOTE_GROUP(group_id)      ON DELETE CASCADE,
    FOREIGN KEY (restaurant_id) REFERENCES TBL_RESTAURANTS(restaurant_id) ON DELETE CASCADE,
    UNIQUE KEY unique_option (group_id, restaurant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. ตารางบันทึกการโหวต (ป้องกันโหวตซ้ำด้วย voter_token)
-- ============================================================
CREATE TABLE IF NOT EXISTS TBL_VOTE_RECORD (
    vote_id     INT AUTO_INCREMENT PRIMARY KEY,
    group_id    INT NOT NULL,
    option_id   INT NOT NULL,
    voter_token VARCHAR(64) NOT NULL,                  -- token จากเบราว์เซอร์ (ไม่ระบุตัวตน)
    voted_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id)  REFERENCES TBL_VOTE_GROUP(group_id)  ON DELETE CASCADE,
    FOREIGN KEY (option_id) REFERENCES TBL_VOTE_OPTION(option_id) ON DELETE CASCADE,
    UNIQUE KEY unique_voter (group_id, voter_token)    -- 1 คนโหวตได้ 1 ครั้งต่อห้อง
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ข้อมูลตัวอย่าง (Sample Data)
-- ============================================================

-- หมวดหมู่
INSERT INTO TBL_CATEGORY (category_name) VALUES
('อาหารตามสั่ง'),('ก๋วยเตี๋ยว'),('ส้มตำ/อีสาน'),('ญี่ปุ่น'),
('จีน'),('ฮาลาล'),('คาเฟ่/ของหวาน'),('หมูกระทะ');

-- แอดมินตัวอย่าง (รหัสผ่าน: admin123)
-- ค่า hash นี้สร้างด้วย password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO TBL_ADMIN (username, password_hash) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ร้านอาหารตัวอย่าง (ตำแหน่งใกล้มหาวิทยาลัยบูรพา บางแสน)
INSERT INTO TBL_RESTAURANTS
(category_id, restaurant_name, country, atmosphere, budget_max, address, phone, social, map_url, lat, lng, open_time, close_time, closed_day, is_open, is_approved)
VALUES
(2, 'ก๋วยเตี๋ยวไก่เจ้สม', 'ไทย', 'พัดลม', 60, 'ถนนสุขุมวิท บางแสน', '081-234-5678', 'fb.com/kaisom',
 'https://www.google.com/maps/search/?api=1&query=13.2880,100.9250', 13.2880, 100.9250, '08:00', '17:00', '', 1, 1),

(3, 'ส้มตำริมทาง', 'ไทย', 'พัดลม', 80, 'แยกบางแสน', '089-999-8888', 'ig.com/somtumrimthang',
 'https://www.google.com/maps/search/?api=1&query=13.2890,100.9260', 13.2890, 100.9260, '10:00', '21:00', 'จันทร์', 1, 1),

(1, 'ข้าวแกงปักษ์ใต้', 'ไทย', 'แอร์', 120, 'ตรงข้ามมหาวิทยาลัยบูรพา', NULL, NULL,
 'https://www.google.com/maps/search/?api=1&query=13.2900,100.9280', 13.2900, 100.9280, '07:00', '19:00', '', 0, 1),

(4, 'ซูชิมาซา', 'ญี่ปุ่น', 'แอร์', 250, 'ห้างสรรพสินค้าบางแสน', '038-123-456', 'fb.com/sushimasa',
 'https://www.google.com/maps/search/?api=1&query=13.2860,100.9230', 13.2860, 100.9230, '11:00', '22:00', '', 1, 1),

(1, 'ผัดไทยเจ๊นง', 'ไทย', 'ทั้งคู่', 70, 'ซอยบางแสน 5', NULL, NULL,
 'https://www.google.com/maps/search/?api=1&query=13.2855,100.9220', 13.2855, 100.9220, '09:00', '20:00', 'พุธ', 1, 1),

(5, 'ติ่มซำฮองมง', 'จีน', 'แอร์', 180, 'ถนนเพชรเกษม', '038-000-111', NULL,
 'https://www.google.com/maps/search/?api=1&query=13.2910,100.9300', 13.2910, 100.9300, '10:00', '20:00', 'อังคาร', 1, 1),

(6, 'ข้าวมันไก่ฮาลาล', 'ฮาลาล', 'พัดลม', 50, 'ตลาดบางแสน', NULL, NULL,
 'https://www.google.com/maps/search/?api=1&query=13.2870,100.9242', 13.2870, 100.9242, '08:00', '18:00', '', 1, 1),

(7, 'กาแฟริมทะเล', 'ไทย', 'แอร์', 150, 'ชายหาดบางแสน', '081-555-6666', 'ig.com/caferimthale',
 'https://www.google.com/maps/search/?api=1&query=13.2840,100.9200', 13.2840, 100.9200, '09:00', '22:00', '', 1, 1),

(8, 'หมูกระทะเจ้าเก่า', 'ไทย', 'พัดลม', 200, 'แยกไฟแดงบางแสน', NULL, NULL,
 'https://www.google.com/maps/search/?api=1&query=13.2920,100.9320', 13.2920, 100.9320, '16:00', '23:00', '', 1, 1),

(2, 'บะหมี่เกี๊ยวเจ้โต', 'จีน', 'พัดลม', 55, 'ตรงข้ามโรงเรียน', NULL, NULL,
 'https://www.google.com/maps/search/?api=1&query=13.2885,100.9255', 13.2885, 100.9255, '07:00', '16:00', 'เสาร์', 1, 1);

-- ============================================================
-- ตัวอย่าง Query ใช้งานจริง (อ้างอิง)
-- ============================================================

-- 1) สุ่มร้านตามเงื่อนไข + คำนวณระยะทางด้วย ST_Distance_Sphere
--    (แทนที่ :user_lat, :user_lng ด้วยค่าจาก GPS ของผู้ใช้)
-- SELECT r.*, c.category_name,
--        ST_Distance_Sphere(POINT(r.lng, r.lat), POINT(:user_lng, :user_lat)) AS distance_m
-- FROM TBL_RESTAURANTS r
-- JOIN TBL_CATEGORY c ON r.category_id = c.category_id
-- WHERE r.is_approved = 1
--   AND r.is_open = 1
--   AND r.budget_max <= :budget
--   AND (:country = '' OR r.country = :country)
--   AND (:atmos = '' OR r.atmosphere = :atmos OR r.atmosphere = 'ทั้งคู่')
--   AND (:cat_id = 0 OR r.category_id = :cat_id)
--   AND ST_Distance_Sphere(POINT(r.lng, r.lat), POINT(:user_lng, :user_lat)) <= :max_dist
--   AND CURTIME() BETWEEN r.open_time AND r.close_time
--   AND FIND_IN_SET(DAYNAME(CURDATE()), r.closed_day) = 0
-- ORDER BY RAND() LIMIT 1;

-- 2) แสดงร้านเรียงระยะทางใกล้สุด (หน้าเลือกเอง)
-- SELECT r.*, ST_Distance_Sphere(POINT(r.lng, r.lat), POINT(:user_lng, :user_lat)) AS distance_m
-- FROM TBL_RESTAURANTS r WHERE r.is_approved = 1
-- ORDER BY distance_m ASC;

-- 3) นับคะแนนโหวตในห้อง (กราฟเรียลไทม์)
-- SELECT o.option_id, r.restaurant_name, COUNT(v.vote_id) AS vote_count
-- FROM TBL_VOTE_OPTION o
-- JOIN TBL_RESTAURANTS r ON o.restaurant_id = r.restaurant_id
-- LEFT JOIN TBL_VOTE_RECORD v ON v.option_id = o.option_id
-- WHERE o.group_id = :group_id
-- GROUP BY o.option_id ORDER BY vote_count DESC;
