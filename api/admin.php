<?php
/**
 * API: แอดมิน — ล็อกอิน / ดูร้านรออนุมัติ / อนุมัติ / ลบ / จัดการหมวดหมู่
 * Method: POST (JSON)
 * Action: login | pending_list | approve | delete | add_cat | del_cat | user_list | set_role | logout
 * สิทธิ์: ต้องเป็น role = admin (ล็อกอินผ่าน auth.php หรือบัญชีแอดมินเดิม)
 * Response: JSON {success, ...}
 */
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;
$action = $input['action'] ?? '';

// ---------- ล็อกอิน ----------
if ($action === 'login') {
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM TBL_ADMIN WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['role'] = 'admin'; $_SESSION['username'] = $admin['username']; $_SESSION['display_name'] = $admin['username']; $_SESSION['user_id'] = 0;
        echo json_encode(['success' => true, 'message' => 'เข้าสู่ระบบสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
    }
    exit;
}

// ---------- ตรวจสอบสิทธิ์แอดมิน (ทุก action ยกเว้น login) ----------
if (empty($_SESSION['admin_id'])) {
    $msg = empty($_SESSION['role']) ? 'กรุณาเข้าสู่ระบบก่อน' : 'ต้องเป็นแอดมินเท่านั้น';
    echo json_encode(['success' => false, 'message' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}
// บัญชีจากตาราง TBL_USER: ตรวจ role ในฐานข้อมูลซ้ำทุกครั้ง (ถูกถอดสิทธิ์แล้วต้องใช้ไม่ได้ทันที)
if (!empty($_SESSION['user_id'])) {
    $s = $pdo->prepare("SELECT role FROM TBL_USER WHERE user_id = ?");
    $s->execute([$_SESSION['user_id']]);
    $r = $s->fetch();
    if (!$r || $r['role'] !== 'admin') {
        unset($_SESSION['admin_id']); $_SESSION['role'] = $r ? $r['role'] : 'user';
        echo json_encode(['success' => false, 'message' => 'ต้องเป็นแอดมินเท่านั้น'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

switch ($action) {
    // ดูร้านรออนุมัติ
    case 'pending_list':
        $stmt = $pdo->query("SELECT r.*, c.category_name FROM TBL_RESTAURANTS r
                             JOIN TBL_CATEGORY c ON r.category_id = c.category_id
                             WHERE r.is_approved = 0 ORDER BY r.restaurant_id DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()], JSON_UNESCAPED_UNICODE);
        break;

    // อนุมัติร้าน
    case 'approve':
        $id = (int)($input['restaurant_id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE TBL_RESTAURANTS SET is_approved = 1 WHERE restaurant_id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'อนุมัติเรียบร้อย']);
        break;

    // ลบร้าน
    case 'delete':
        $id = (int)($input['restaurant_id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM TBL_RESTAURANTS WHERE restaurant_id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'ลบเรียบร้อย']);
        break;

    // เพิ่มหมวดหมู่
    case 'add_cat':
        $name = trim($input['category_name'] ?? '');
        if (!$name) { echo json_encode(['success' => false, 'message' => 'ใส่ชื่อหมวดหมู่']); break; }
        try {
            $stmt = $pdo->prepare("INSERT INTO TBL_CATEGORY (category_name) VALUES (?)");
            $stmt->execute([$name]);
            echo json_encode(['success' => true, 'message' => 'เพิ่มหมวดหมู่เรียบร้อย']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'มีหมวดหมู่นี้แล้ว']);
        }
        break;

    // ลบหมวดหมู่
    case 'del_cat':
        $name = trim($input['category_name'] ?? '');
        $stmt = $pdo->prepare("DELETE FROM TBL_CATEGORY WHERE category_name = ?");
        $stmt->execute([$name]);
        echo json_encode(['success' => true, 'message' => 'ลบหมวดหมู่เรียบร้อย']);
        break;

    // รายชื่อผู้ใช้ทั้งหมด (ให้แอดมินกำหนดสิทธิ์)
    case 'user_list':
        try {
            $stmt = $pdo->query("SELECT user_id, username, display_name, role, created_at FROM TBL_USER ORDER BY role DESC, created_at DESC");
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll(), 'me' => (int)($_SESSION['user_id'] ?? 0)], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            echo json_encode(['success' => true, 'data' => [], 'me' => 0]);
        }
        break;

    // ตั้งสิทธิ์ผู้ใช้ (user <-> admin) — ห้ามเปลี่ยนสิทธิ์ตัวเอง
    case 'set_role':
        $uid = (int)($input['user_id'] ?? 0);
        $role = ($input['role'] ?? '') === 'admin' ? 'admin' : 'user';
        if ($uid === (int)($_SESSION['user_id'] ?? 0)) {
            echo json_encode(['success' => false, 'message' => 'เปลี่ยนสิทธิ์ตัวเองไม่ได้'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $stmt = $pdo->prepare("UPDATE TBL_USER SET role = ? WHERE user_id = ?");
        $stmt->execute([$role, $uid]);
        echo json_encode(['success' => true, 'message' => $role === 'admin' ? 'ตั้งเป็นแอดมินแล้ว' : 'ปรับเป็นผู้ใช้ทั่วไปแล้ว'], JSON_UNESCAPED_UNICODE);
        break;

    // ออกจากระบบ
    case 'logout':
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'ออกจากระบบแล้ว']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'ไม่รู้จัก action: ' . $action]);
}
