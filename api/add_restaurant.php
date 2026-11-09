<?php
/**
 * API: เพิ่มร้านอาหารใหม่ — ต้องล็อกอิน (สมาชิกทั่วไป = รออนุมัติ, แอดมิน = อนุมัติทันที)
 * Method: POST (JSON หรือ form-data)
 * Params: name, category_id (หรือ category_name), country, atmosphere, budget_max,
 *         address, phone, social, map_url, lat, lng, open_time, close_time, closed_day (array หรือ CSV)
 * Response: JSON {success: true/false, message: "...", restaurant_id: ...}
 */
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

// ต้องล็อกอินก่อนเพิ่มร้าน (ถ้าอยากให้ทุกคนเพิ่มได้ ให้ลบ 4 บรรทัดนี้)
if (empty($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อนเพิ่มร้าน'], JSON_UNESCAPED_UNICODE);
    exit;
}
$auto_approve = ($_SESSION['role'] === 'admin') ? 1 : 0;

// รับข้อมูล (รองรับทั้ง JSON และ form-data)
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$name = trim($input['name'] ?? '');
$budget = (float)($input['budget_max'] ?? 0);
$map_url = trim($input['map_url'] ?? '');
$lat = isset($input['lat']) ? (float)$input['lat'] : null;
$lng = isset($input['lng']) ? (float)$input['lng'] : null;

// ถ้าไม่ได้ส่ง lat/lng มา ให้ดึงจาก map_url เอง
if (($lat === null || $lng === null) && $map_url) {
    $coord = parseMapUrl(resolveShortUrl($map_url));
    if ($coord) { $lat = $coord['lat']; $lng = $coord['lng']; }
}

if (!$name || !$budget || $lat === null || $lng === null) {
    echo json_encode(['success' => false, 'message' => 'กรอกชื่อร้าน งบประมาณ และพิกัด (หรือลิงค์ Google Maps) ให้ครบ']);
    exit;
}

// หา category_id จากชื่อหมวดหมู่
$cat_name = trim($input['category_name'] ?? $input['cat'] ?? 'อาหารตามสั่ง');
$stmt = $pdo->prepare("SELECT category_id FROM TBL_CATEGORY WHERE category_name = ?");
$stmt->execute([$cat_name]);
$cat = $stmt->fetch();
$category_id = $cat ? (int)$cat['category_id'] : 1;

// จัดการวันหยุด
$closed = $input['closed_day'] ?? [];
if (is_array($closed)) $closed_day = implode(',', $closed);
else $closed_day = trim($closed);

try {
    $stmt = $pdo->prepare("INSERT INTO TBL_RESTAURANTS
        (category_id, restaurant_name, country, atmosphere, budget_max, address,
         phone, social, map_url, lat, lng, open_time, close_time, closed_day, is_open, is_approved)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,?)");
    $stmt->execute([
        $category_id,
        $name,
        $input['country'] ?? 'ไทย',
        $input['atmosphere'] ?? 'ทั้งคู่',
        $budget,
        $input['address'] ?? '',
        $input['phone'] ?? '',
        $input['social'] ?? '',
        $map_url,
        $lat, $lng,
        $input['open_time'] ?? '08:00',
        $input['close_time'] ?? '20:00',
        $closed_day,
        $auto_approve
    ]);
    echo json_encode(['success' => true, 'message' => $auto_approve ? 'เพิ่มร้านเรียบร้อย แสดงผลได้ทันที' : 'ส่งร้านเรียบร้อย รอแอดมินอนุมัติ', 'restaurant_id' => $pdo->lastInsertId()], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'บันทึกไม่สำเร็จ: ' . $e->getMessage()]);
}
