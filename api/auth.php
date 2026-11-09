<?php
/**
 * API: บัญชีผู้ใช้ — สมัคร / ล็อกอิน / ออกจากระบบ / ดูสถานะ
 * Method: POST (JSON)
 * Action: register | login | logout | me
 * สิทธิ์ (role): 'user' = สมาชิกทั่วไป, 'admin' = แอดมิน (ตั้งได้โดยแอดมินเท่านั้น ผ่าน admin.php)
 * รองรับบัญชีแอดมินเดิมในตาราง TBL_ADMIN (ล็อกอินแล้วได้ role = admin)
 */
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;
$action = $input['action'] ?? '';

function out($arr) { echo json_encode($arr, JSON_UNESCAPED_UNICODE); exit; }

// สร้างตารางผู้ใช้อัตโนมัติ (ถ้ายังไม่มี)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS TBL_USER (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL UNIQUE,
        display_name VARCHAR(60) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('user','admin') NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (PDOException $e) {
    out(['success' => false, 'message' => 'ยังไม่มีตาราง TBL_USER — กรุณารันไฟล์ migrate_users.sql ใน phpMyAdmin']);
}

// เซ็ตข้อมูลหลังล็อกอินสำเร็จ
function startSession($id, $username, $display, $role, $legacyAdmin = false) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $legacyAdmin ? 0 : (int)$id;
    $_SESSION['username'] = $username;
    $_SESSION['display_name'] = $display;
    $_SESSION['role'] = $role;
    $_SESSION['login_fails'] = 0;
    // admin.php และ import_*.php เช็กสิทธิ์จาก admin_id
    if ($role === 'admin') { $_SESSION['admin_id'] = $legacyAdmin ? $id : -$id; $_SESSION['admin_username'] = $username; }
    else { unset($_SESSION['admin_id'], $_SESSION['admin_username']); }
}

function userJson() {
    return ['logged_in' => true, 'username' => $_SESSION['username'], 'display_name' => $_SESSION['display_name'], 'role' => $_SESSION['role']];
}

switch ($action) {
    case 'me':
        if (empty($_SESSION['role'])) out(['success' => true, 'logged_in' => false]);
        // ตรวจ role จากฐานข้อมูลซ้ำ เผื่อแอดมินถูกปรับสิทธิ์ระหว่างที่ล็อกอินอยู่
        if (!empty($_SESSION['user_id'])) {
            $s = $pdo->prepare("SELECT username, display_name, role FROM TBL_USER WHERE user_id = ?");
            $s->execute([$_SESSION['user_id']]);
            $u = $s->fetch();
            if (!$u) { session_destroy(); out(['success' => true, 'logged_in' => false]); }
            $_SESSION['role'] = $u['role'];
            $_SESSION['display_name'] = $u['display_name'];
            if ($u['role'] === 'admin') $_SESSION['admin_id'] = -$_SESSION['user_id'];
            else unset($_SESSION['admin_id']);
        }
        out(['success' => true] + userJson());

    case 'register':
        $username = trim($input['username'] ?? '');
        $display  = trim($input['display_name'] ?? '');
        $password = (string)($input['password'] ?? '');
        if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $username))
            out(['success' => false, 'message' => 'ชื่อผู้ใช้ใช้ได้เฉพาะ A-Z, 0-9 และ _ ยาว 3-20 ตัว']);
        if (strlen($password) < 6 || strlen($password) > 72)
            out(['success' => false, 'message' => 'รหัสผ่านต้องยาว 6-72 ตัวอักษร']);
        if ($display === '') $display = $username;
        if (mb_strlen($display) > 40) out(['success' => false, 'message' => 'ชื่อที่แสดงยาวเกิน 40 ตัวอักษร']);
        // ห้ามซ้ำกับแอดมินเดิม
        $s = $pdo->prepare("SELECT 1 FROM TBL_ADMIN WHERE username = ?"); $s->execute([$username]);
        if ($s->fetch()) out(['success' => false, 'message' => 'ชื่อผู้ใช้นี้ถูกใช้แล้ว']);
        try {
            $s = $pdo->prepare("INSERT INTO TBL_USER (username, display_name, password_hash, role) VALUES (?,?,?,'user')");
            $s->execute([$username, $display, password_hash($password, PASSWORD_DEFAULT)]);
        } catch (PDOException $e) {
            out(['success' => false, 'message' => 'ชื่อผู้ใช้นี้ถูกใช้แล้ว']);
        }
        startSession($pdo->lastInsertId(), $username, $display, 'user');
        out(['success' => true, 'message' => 'สมัครสมาชิกสำเร็จ'] + userJson());

    case 'login':
        // หน่วงเวลาเมื่อใส่ผิดหลายครั้ง (กันเดารหัส)
        $fails = (int)($_SESSION['login_fails'] ?? 0);
        if ($fails >= 5) { sleep(min(5, $fails - 4)); }
        $username = trim($input['username'] ?? '');
        $password = (string)($input['password'] ?? '');
        if ($username === '' || $password === '') out(['success' => false, 'message' => 'กรอกชื่อผู้ใช้และรหัสผ่าน']);

        $s = $pdo->prepare("SELECT * FROM TBL_USER WHERE username = ?"); $s->execute([$username]);
        $u = $s->fetch();
        if ($u && password_verify($password, $u['password_hash'])) {
            startSession($u['user_id'], $u['username'], $u['display_name'], $u['role']);
            out(['success' => true, 'message' => 'เข้าสู่ระบบสำเร็จ'] + userJson());
        }
        // บัญชีแอดมินเดิม (TBL_ADMIN)
        $s = $pdo->prepare("SELECT * FROM TBL_ADMIN WHERE username = ?"); $s->execute([$username]);
        $a = $s->fetch();
        if ($a && password_verify($password, $a['password_hash'])) {
            // ย้ายบัญชีแอดมินเดิมเข้า TBL_USER (role = admin) เพื่อให้มี user_id จริง ใช้กับห้องโหวตได้
            $pdo->prepare("INSERT INTO TBL_USER (username, display_name, password_hash, role) VALUES (?,?,?,'admin')
                           ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), role = 'admin'")
                ->execute([$a['username'], $a['username'], $a['password_hash']]);
            $s = $pdo->prepare("SELECT * FROM TBL_USER WHERE username = ?"); $s->execute([$a['username']]);
            $u = $s->fetch();
            startSession($u['user_id'], $u['username'], $u['display_name'], 'admin');
            out(['success' => true, 'message' => 'เข้าสู่ระบบสำเร็จ'] + userJson());
        }
        $_SESSION['login_fails'] = $fails + 1;
        out(['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);

    case 'logout':
        $_SESSION = [];
        session_destroy();
        out(['success' => true, 'message' => 'ออกจากระบบแล้ว']);

    default:
        out(['success' => false, 'message' => 'ไม่รู้จัก action: ' . $action]);
}
