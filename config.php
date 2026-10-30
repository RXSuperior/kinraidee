<?php
// === ต้องแก้ไขให้ตรงกับของคุณ ===
$host = 'localhost';

// ชื่อฐานข้อมูล มักจะเหมือนชื่อผู้ใช้เลย
$dbname = 's68160216';  

// ชื่อผู้ใช้ เหมือนกับรหัสนักศึกษา
$username = 's68160216';  

// รหัสผ่าน ใส่รหัสผ่าน Angsila จริงตรงนี้
$password = 'YS4sD3aE';  
// ===============================

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    die("เชื่อมต่อไม่ได้: " . $e->getMessage());
}
?>