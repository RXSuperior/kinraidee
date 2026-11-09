<?php
/**
 * API: ดึงหมวดหมู่ทั้งหมด
 * Method: GET
 * Response: JSON [{category_id, category_name}, ...]
 */
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$stmt = $pdo->query("SELECT category_id, category_name FROM TBL_CATEGORY ORDER BY category_id");
echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
