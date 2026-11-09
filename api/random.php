<?php
/**
 * API: สุ่มร้าน — ดึงชุดเดียวกับหน้าเลือกเองทุกร้าน
 * Method: GET
 */
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$user_lat = isset($_GET['lat']) ? (float)$_GET['lat'] : 13.2875;
$user_lng = isset($_GET['lng']) ? (float)$_GET['lng'] : 100.9244;
$cat      = trim($_GET['cat'] ?? '');
$budget   = isset($_GET['budget_max']) ? (float)$_GET['budget_max'] : 9999;
$country  = trim($_GET['country'] ?? '');
$atmos    = trim($_GET['atmosphere'] ?? '');
$max_dist = isset($_GET['max_dist']) ? (float)$_GET['max_dist'] : 99999;

// ✅ เงื่อนไขตรงกับหน้าเลือกเองเป๊ะๆ
$sql = "SELECT r.*, c.category_name,
        ST_Distance_Sphere(POINT(r.lng, r.lat), POINT(?, ?)) AS distance_m
        FROM TBL_RESTAURANTS r
        JOIN TBL_CATEGORY c ON r.category_id = c.category_id
        WHERE r.is_approved = 1
          AND r.is_open = 1
          AND r.budget_max <= ?
          AND ST_Distance_Sphere(POINT(r.lng, r.lat), POINT(?, ?)) <= ?";

$params = [$user_lng, $user_lat, $budget, $user_lng, $user_lat, $max_dist];

// ข้ามร้านที่เว็บลองแล้วแต่ระยะขับรถจริงเกินกำหนด (ส่งมาเป็น id คั่นด้วย ,)
$ex = array_values(array_filter(array_map('intval', explode(',', $_GET['exclude'] ?? ''))));
if ($ex) { $sql .= " AND r.restaurant_id NOT IN (" . implode(',', array_fill(0, count($ex), '?')) . ")"; foreach ($ex as $e) $params[] = $e; }

if ($cat)     { $sql .= " AND c.category_name = ?"; $params[] = $cat; }
if ($country) { $sql .= " AND r.country = ?";       $params[] = $country; }
if ($atmos)   { $sql .= " AND (r.atmosphere = ? OR r.atmosphere = 'ทั้งคู่')"; $params[] = $atmos; }

$sql .= " ORDER BY RAND() LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$shop = $stmt->fetch();

if ($shop) {
    echo json_encode(['success' => true, 'restaurant' => $shop], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่พบร้านที่ตรงเงื่อนไข'], JSON_UNESCAPED_UNICODE);
}
