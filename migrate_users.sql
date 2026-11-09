-- KinRaiDee — ตารางผู้ใช้ (สมาชิกทั่วไป + แอดมิน แยกสิทธิ์ด้วยคอลัมน์ role)
-- ไม่ต้องรันเองก็ได้: api/auth.php จะสร้างตารางนี้ให้อัตโนมัติเมื่อถูกเรียกครั้งแรก
CREATE TABLE IF NOT EXISTS TBL_USER (
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(30)  NOT NULL UNIQUE,
    display_name  VARCHAR(60)  NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตั้งให้บัญชีใดเป็นแอดมิน (ทำครั้งเดียว หลังสมัครบัญชีนั้นแล้ว):
-- UPDATE TBL_USER SET role = 'admin' WHERE username = 'ชื่อผู้ใช้ของคุณ';
-- (บัญชี admin เดิมในตาราง TBL_ADMIN ยังล็อกอินได้ตามปกติ และได้สิทธิ์แอดมินเหมือนกัน)
