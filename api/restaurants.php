<?php
/**
 * API: ดึงร้านอาหารที่อนุมัติแล้ว เรียงตามระยะทางใกล้สุด
 * Method: GET
 * Params: lat, lng (ตำแหน่งผู้ใช้), cat (หมวดหมู่, optional)
 * Response: JSON [{restaurant_id, restaurant_name, category_name, country, atmosphere,
 *           budget_max, address, phone, social, map_url, lat, lng, open_time, close_time,
 *           closed_day, is_open, distance_m}, ...]
 */
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$user_lat = isset($_GET['lat']) ? (float)$_GET['lat'] : 13.2875;
$user_lng = isset($_GET['lng']) ? (float)$_GET['lng'] : 100.9244;
$cat = isset($_GET['cat']) ? trim($_GET['cat']) : '';

$sql = "SELECT r.*, c.category_name,
        ST_Distance_Sphere(POINT(r.lng, r.lat), POINT(?, ?)) AS distance_m
        FROM TBL_RESTAURANTS r
        JOIN TBL_CATEGORY c ON r.category_id = c.category_id
        WHERE r.is_approved = 1";
$params = [$user_lng, $user_lat];

if ($cat) {
    $sql .= " AND c.category_name = ?";
    $params[] = $cat;
}
$sql .= " ORDER BY distance_m ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
