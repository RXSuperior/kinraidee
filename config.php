<?php
/**
 * KinRaiDee — ไฟล์ตั้งค่าเชื่อมต่อฐานข้อมูล (config.php)
 * ใช้ PDO เชื่อมต่อ MySQL — ปลอดภัยจาก SQL Injection เมื่อใช้ Prepared Statement
 *
 * วิธีใช้: ใส่ที่ด้านบนของไฟล์ PHP ทุกไฟล์ที่ต้องติดต่อฐานข้อมูล:
 *   require_once 'config.php';
 */

// ========== ตั้งค่าฐานข้อมูล (แก้ไขตรงนี้ให้ตรงกับเซิร์ฟเวอร์จริง) ==========
define('DB_HOST', 'localhost');        // ที่อยู่เซิร์ฟเวอร์ MySQL (ปกติคือ localhost)
define('DB_NAME', 's68160216');        // ชื่อฐานข้อมูล (ตรงกับไฟล์ kinraidee_schema.sql)
define('DB_USER', 's68160216');             // ชื่อผู้ใช้ MySQL (XAMPP ปกติคือ root)
define('DB_PASS', 'YS4sD3aE');                 // รหัสผ่าน MySQL (XAMPP ปกติเว้นว่าง)
define('DB_CHARSET', 'utf8mb4');       // ชุดอักขระ รองรับภาษาไทยและอีโมจิ

// ========== ตั้งค่าเว็บไซต์ ==========
define('SITE_NAME', 'KinRaiDee');
define('SITE_URL', 'http://localhost/kinraidee');  // URL จริงเมื่อขึ้นโฮสต์ ให้แก้ตรงนี้
define('ADMIN_USERNAME', 'admin');                  // ชื่อผู้ใช้แอดมินเริ่มต้น

// ========== Longdo Map API key (ใช้กับ import_longdo.php) — สมัครที่ map.longdo.com/console ==========
define('LONGDO_KEY', '489d4095cf00941c391012cdeb06abc1');   // วางคีย์ระหว่างเครื่องหมาย ' ' นี้

// ========== ตั้งค่า GPS / ระยะทาง (หน่วย: เมตร) ==========
define('DEFAULT_RADIUS', 2000);        // รัศมีค้นหาเริ่มต้น 2 กม.
define('MAX_RADIUS', 5000);            // รัศมีสูงสุด 5 กม.

// ========== เชื่อมต่อฐานข้อมูลด้วย PDO ==========
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // แสดง error เมื่อ query ผิด
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // ดึงข้อมูลมาเป็น array แบบ key=>value
        PDO::ATTR_EMULATE_PREPARES   => false,                    // ใช้ Prepared Statement จริง ป้องกัน SQL Injection
    ]);
} catch (PDOException $e) {
    // ถ้าเชื่อมต่อไม่ได้ แสดงข้อความ (โหมดพัฒนา) — เมื่อขึ้นจริงให้บันทึกลง log แทน
    http_response_code(500);
    die("ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $e->getMessage() .
        "<br>กรุณาตรวจสอบค่าในไฟล์ config.php (DB_HOST, DB_NAME, DB_USER, DB_PASS)");
}

// ========== ฟังก์ชันช่วยเหลือ (ใช้ได้ทั่วทุกไฟล์) ==========

/**
 * ดึงพิกัด lat,lng จากลิงค์ Google Maps (ทุกรูปแบบ)
 * ใช้ตอนผู้ใช้วางลิงค์ในฟอร์มเพิ่มร้าน
 */
function parseMapUrl($url) {
    if (!$url) return null;
    // 1) !3d..!4d.. = พิกัดหมุดจริงของร้าน (แม่นที่สุด) ต้องเช็กก่อน
    if (preg_match('/!3d(-?\d+\.?\d*)!4d(-?\d+\.?\d*)/', $url, $m))
        return ['lat' => (float)$m[1], 'lng' => (float)$m[2]];
    // 2) q= / query= / ll=
    if (preg_match('/[?&](?:q|query|ll)=(-?\d+\.?\d*),(-?\d+\.?\d*)/', $url, $m))
        return ['lat' => (float)$m[1], 'lng' => (float)$m[2]];
    // 3) พิกัดล้วน เช่น 13.2875,100.9244
    if (preg_match('/^\s*(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)\s*$/', $url, $m))
        return ['lat' => (float)$m[1], 'lng' => (float)$m[2]];
    // 4) @lat,lng = จุดกึ่งกลางหน้าจอแผนที่ ไม่ใช่ตำแหน่งหมุด ใช้เป็นทางเลือกสุดท้ายเท่านั้น
    if (preg_match('/@(-?\d+\.?\d*),(-?\d+\.?\d*)/', $url, $m))
        return ['lat' => (float)$m[1], 'lng' => (float)$m[2]];
    return null;
}

/**
 * ลิงก์สั้น (maps.app.goo.gl / goo.gl/maps) ไม่มีพิกัดในตัว
 * ฟังก์ชันนี้ตามไปดู URL ปลายทางแล้วคืนค่าเป็นลิงก์ยาว (ถ้าตามไม่ได้ คืนลิงก์เดิม)
 */
function resolveShortUrl($url) {
    if (!preg_match('~^https?://(maps\.app\.goo\.gl|goo\.gl/maps)~i', $url)) return $url;
    if (!function_exists('curl_init')) return $url;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; KinRaiDee)',
    ]);
    curl_exec($ch);
    $final = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    return $final ?: $url;
}

/**
 * สร้างโค้ดแชร์ 4 ตัวอักษรสำหรับห้องโหวต
 */
function generateShareCode($pdo) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    do {
        $code = '';
        for ($i = 0; $i < 4; $i++) $code .= $chars[random_int(0, strlen($chars) - 1)];
        $stmt = $pdo->prepare("SELECT group_id FROM TBL_VOTE_GROUP WHERE share_code = ?");
        $stmt->execute([$code]);
    } while ($stmt->fetch()); // สุ่มใหม่ถ้าโค้ดซ้ำ
    return $code;
}

/**
 * ตรวจสอบว่าร้านเปิดอยู่หรือไม่ (ตรงเวลา + ไม่ใช่วันหยุด + ไม่ปิดฉุกเฉิน)
 */
function isRestaurantOpen($r) {
    if ((int)$r['is_open'] !== 1) return false;
    $day_map = ['Sunday'=>'อาทิตย์','Monday'=>'จันทร์','Tuesday'=>'อังคาร','Wednesday'=>'พุธ',
                'Thursday'=>'พฤหัสบดี','Friday'=>'ศุกร์','Saturday'=>'เสาร์'];
    $today = $day_map[date('l')];
    $closed = explode(',', $r['closed_day'] ?? '');
    if (in_array($today, $closed)) return false;
    $cur = date('H') * 60 + date('i');
    [$oh, $om] = explode(':', $r['open_time']);
    [$ch, $cm] = explode(':', $r['close_time']);
    $open = (int)$oh * 60 + (int)$om;
    $close = (int)$ch * 60 + (int)$cm;
    if ($close < $open) return $cur >= $open || $cur <= $close; // เปิดข้ามเที่ยงคืน
    return $cur >= $open && $cur <= $close;
}

// เริ่ม session สำหรับแอดมินและ voter_token
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
