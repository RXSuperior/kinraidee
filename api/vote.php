<?php
/**
 * API: ระบบโหวตทีม (ต้องล็อกอิน) — มีหัวห้อง / สมาชิก / โหวตแบบซ่อนผลจนกว่าจะเฉลย
 * Method: POST (JSON)
 * Action: list | create | join | state | cast | kick | announce | my_rooms
 *
 * กติกา
 *  - คนสร้างห้อง = หัวห้อง: เลือกร้าน (create), เฉลยผล (announce), เชิญคนที่ไม่โหวตออก (kick)
 *  - คนที่ใส่โค้ดเข้าห้อง = สมาชิก โหวตได้คนละ 1 ร้าน (หัวห้องโหวตด้วยได้)
 *  - ก่อนเฉลย: ทุกคนเห็นแค่ว่า "ใครโหวตแล้ว/ยัง" แต่ไม่เห็นว่าโหวตร้านไหน และไม่เห็นคะแนน
 *  - หัวห้องกดเฉลยได้ก็ต่อเมื่อสมาชิกทุกคนโหวตครบ
 * ตารางที่ต้องเพิ่ม (คอลัมน์ owner_user_id, winner_option_id และตาราง TBL_VOTE_MEMBER) สร้างให้อัตโนมัติ
 */
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;
$action = $input['action'] ?? '';

function out($arr) { echo json_encode($arr, JSON_UNESCAPED_UNICODE); exit; }
function fail($msg) { out(['success' => false, 'message' => $msg]); }

// ---------- ต้องล็อกอิน ----------
$uid = (int)($_SESSION['user_id'] ?? 0);
if (!$uid) fail('กรุณาเข้าสู่ระบบก่อนใช้งานห้องโหวต');
$token = 'u' . $uid;   // ใช้ user_id เป็น voter_token (กันโหวตซ้ำ + รู้ว่าใครโหวตแล้ว)

// ---------- เตรียมตาราง/คอลัมน์ใหม่ (รันซ้ำได้) ----------
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS TBL_VOTE_MEMBER (
        group_id INT NOT NULL,
        user_id  INT NOT NULL,
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (group_id, user_id),
        FOREIGN KEY (group_id) REFERENCES TBL_VOTE_GROUP(group_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    foreach (['owner_user_id' => 'INT NULL', 'winner_option_id' => 'INT NULL'] as $col => $def) {
        $c = $pdo->query("SHOW COLUMNS FROM TBL_VOTE_GROUP LIKE '$col'")->fetch();
        if (!$c) $pdo->exec("ALTER TABLE TBL_VOTE_GROUP ADD COLUMN $col $def");
    }
} catch (PDOException $e) {
    fail('เตรียมตารางโหวตไม่สำเร็จ (สิทธิ์ MySQL ไม่พอ): ' . $e->getMessage());
}

function getGroupByCode($pdo, $code) {
    $s = $pdo->prepare("SELECT * FROM TBL_VOTE_GROUP WHERE share_code = ?");
    $s->execute([strtoupper(trim($code))]);
    return $s->fetch();
}
function getGroupById($pdo, $id) {
    $s = $pdo->prepare("SELECT * FROM TBL_VOTE_GROUP WHERE group_id = ?");
    $s->execute([(int)$id]);
    return $s->fetch();
}
function isMember($pdo, $gid, $uid) {
    $s = $pdo->prepare("SELECT 1 FROM TBL_VOTE_MEMBER WHERE group_id = ? AND user_id = ?");
    $s->execute([$gid, $uid]);
    return (bool)$s->fetch();
}

// สถานะห้อง: ส่งให้ทุกคนในห้อง — ก่อนเฉลยไม่มีคะแนนและไม่บอกว่าใครโหวตร้านไหน
function roomState($pdo, $g, $uid) {
    $gid = (int)$g['group_id'];
    $closed = (bool)$g['is_closed'];

    $s = $pdo->prepare("SELECT o.option_id, r.restaurant_name, r.budget_max, r.map_url, c.category_name
                        FROM TBL_VOTE_OPTION o
                        JOIN TBL_RESTAURANTS r ON o.restaurant_id = r.restaurant_id
                        JOIN TBL_CATEGORY c ON r.category_id = c.category_id
                        WHERE o.group_id = ? ORDER BY r.restaurant_name");
    $s->execute([$gid]);
    $options = $s->fetchAll();

    $s = $pdo->prepare("SELECT m.user_id, u.display_name, u.username,
                               (SELECT v.option_id FROM TBL_VOTE_RECORD v WHERE v.group_id = m.group_id AND v.voter_token = CONCAT('u', m.user_id)) AS opt
                        FROM TBL_VOTE_MEMBER m JOIN TBL_USER u ON u.user_id = m.user_id
                        WHERE m.group_id = ? ORDER BY m.joined_at");
    $s->execute([$gid]);
    $members = []; $voted = 0; $myOpt = null;
    foreach ($s->fetchAll() as $m) {
        $has = $m['opt'] !== null;
        if ($has) $voted++;
        if ((int)$m['user_id'] === $uid) $myOpt = $has ? (int)$m['opt'] : null;
        $row = [
            'user_id' => (int)$m['user_id'], 'name' => $m['display_name'],
            'is_owner' => (int)$m['user_id'] === (int)$g['owner_user_id'],
            'is_me' => (int)$m['user_id'] === $uid,
            'voted' => $has,
        ];
        if ($closed && $has) $row['option_id'] = (int)$m['opt'];   // เปิดเผยเฉพาะหลังเฉลย
        $members[] = $row;
    }
    $total = count($members);
    $isOwner = (int)$g['owner_user_id'] === $uid;

    $state = [
        'success' => true, 'group_id' => (int)$g['group_id'], 'code' => $g['share_code'],
        'is_closed' => $closed, 'is_owner' => $isOwner,
        'options' => $options, 'members' => $members,
        'voted_count' => $voted, 'total' => $total,
        'my_option_id' => $myOpt, 'already_voted' => $myOpt !== null,
        'can_announce' => $isOwner && !$closed && $total > 0 && $voted === $total,
    ];
    if ($closed) {
        $counts = [];
        foreach ($members as $m) if (isset($m['option_id'])) $counts[$m['option_id']] = ($counts[$m['option_id']] ?? 0) + 1;
        foreach ($state['options'] as &$o) $o['vote_count'] = $counts[(int)$o['option_id']] ?? 0;
        unset($o);
        usort($state['options'], function ($a, $b) { return $b['vote_count'] <=> $a['vote_count']; });
        $state['winner_option_id'] = $g['winner_option_id'] !== null ? (int)$g['winner_option_id'] : null;
    }
    return $state;
}

switch ($action) {
    // ---------- รายชื่อร้านให้หัวห้องเลือก ----------
    case 'list':
        $stmt = $pdo->query("SELECT r.restaurant_id, r.restaurant_name, r.budget_max, c.category_name
                             FROM TBL_RESTAURANTS r
                             JOIN TBL_CATEGORY c ON r.category_id = c.category_id
                             WHERE r.is_approved = 1 AND r.is_open = 1
                             ORDER BY c.category_name, r.restaurant_name");
        out(['success' => true, 'restaurants' => $stmt->fetchAll()]);

    // ---------- สร้างห้อง: ผู้สร้างเป็นหัวห้อง เลือกร้านกี่ร้านก็ได้ (≥ 2) ----------
    case 'create':
        $ids = $input['restaurant_ids'] ?? [];
        if (!is_array($ids)) $ids = [];
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (count($ids) < 2) fail('เลือกร้านอย่างน้อย 2 ร้านเพื่อให้เพื่อนโหวต');
        if (count($ids) > 100) fail('เลือกได้ไม่เกิน 100 ร้านต่อห้อง');
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $s = $pdo->prepare("SELECT restaurant_id FROM TBL_RESTAURANTS WHERE is_approved = 1 AND restaurant_id IN ($ph)");
        $s->execute($ids);
        $opts = $s->fetchAll();
        if (count($opts) < 2) fail('ร้านที่เลือกใช้ไม่ได้ กรุณาเลือกใหม่');

        $code = generateShareCode($pdo);
        $pdo->beginTransaction();
        $pdo->prepare("INSERT INTO TBL_VOTE_GROUP (share_code, owner_user_id) VALUES (?, ?)")->execute([$code, $uid]);
        $gid = (int)$pdo->lastInsertId();
        $ins = $pdo->prepare("INSERT INTO TBL_VOTE_OPTION (group_id, restaurant_id) VALUES (?, ?)");
        foreach ($opts as $o) $ins->execute([$gid, $o['restaurant_id']]);
        $pdo->prepare("INSERT INTO TBL_VOTE_MEMBER (group_id, user_id) VALUES (?, ?)")->execute([$gid, $uid]);
        $pdo->commit();
        out(['success' => true, 'code' => $code, 'group_id' => $gid, 'count' => count($opts)]);

    // ---------- เข้าห้อง / ดูสถานะห้อง (เรียกซ้ำได้ ใช้รีเฟรชสถานะด้วย) ----------
    case 'join':
        $g = getGroupByCode($pdo, $input['code'] ?? '');
        if (!$g) fail('ไม่พบห้องโหวต ' . strtoupper(trim($input['code'] ?? '')));
        if (!isMember($pdo, $g['group_id'], $uid)) {
            if ($g['is_closed']) fail('ห้องนี้เฉลยผลแล้ว เข้าร่วมไม่ได้');
            $pdo->prepare("INSERT IGNORE INTO TBL_VOTE_MEMBER (group_id, user_id) VALUES (?, ?)")->execute([$g['group_id'], $uid]);
        }
        out(roomState($pdo, $g, $uid));

    // ---------- ดูสถานะห้องเฉยๆ (ใช้ดึงข้อมูลซ้ำ ไม่เพิ่มสมาชิก — ถ้าถูกเชิญออกจะแจ้งกลับ) ----------
    case 'state':
        $g = getGroupById($pdo, $input['group_id'] ?? 0);
        if (!$g) fail('ไม่พบห้องโหวต');
        if (!isMember($pdo, $g['group_id'], $uid)) out(['success' => false, 'removed' => true, 'message' => 'คุณไม่ได้อยู่ในห้องนี้แล้ว']);
        out(roomState($pdo, $g, $uid));

    // ---------- โหวต (เฉพาะสมาชิก คนละ 1 ครั้ง) ----------
    case 'cast':
        $g = getGroupById($pdo, $input['group_id'] ?? 0);
        if (!$g) fail('ไม่พบห้องโหวต');
        if ($g['is_closed']) fail('ห้องโหวตปิดแล้ว');
        if (!isMember($pdo, $g['group_id'], $uid)) fail('คุณไม่ได้อยู่ในห้องนี้');
        $opt = (int)($input['option_id'] ?? 0);
        $s = $pdo->prepare("SELECT 1 FROM TBL_VOTE_OPTION WHERE option_id = ? AND group_id = ?");
        $s->execute([$opt, $g['group_id']]);
        if (!$s->fetch()) fail('ไม่มีร้านนี้ในห้อง');
        // เปลี่ยนใจได้จนกว่าหัวห้องจะเฉลย: ลบโหวตเดิมแล้วบันทึกใหม่
        $pdo->beginTransaction();
        try {
            $pdo->prepare("DELETE FROM TBL_VOTE_RECORD WHERE group_id = ? AND voter_token = ?")->execute([$g['group_id'], $token]);
            $pdo->prepare("INSERT INTO TBL_VOTE_RECORD (group_id, option_id, voter_token) VALUES (?,?,?)")->execute([$g['group_id'], $opt, $token]);
            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            fail('บันทึกโหวตไม่สำเร็จ ลองใหม่อีกครั้ง');
        }
        out(['success' => true, 'message' => 'บันทึกโหวตเรียบร้อย']);

    // ---------- หัวห้องเชิญสมาชิกออก (กรณีมีคนไม่ยอมโหวตจนเฉลยไม่ได้) ----------
    case 'kick':
        $g = getGroupById($pdo, $input['group_id'] ?? 0);
        if (!$g) fail('ไม่พบห้องโหวต');
        if ((int)$g['owner_user_id'] !== $uid) fail('เฉพาะหัวห้องเท่านั้น');
        if ($g['is_closed']) fail('ห้องโหวตปิดแล้ว');
        $target = (int)($input['user_id'] ?? 0);
        if ($target === $uid) fail('เชิญตัวเองออกไม่ได้');
        $pdo->prepare("DELETE FROM TBL_VOTE_MEMBER WHERE group_id = ? AND user_id = ?")->execute([$g['group_id'], $target]);
        $pdo->prepare("DELETE FROM TBL_VOTE_RECORD WHERE group_id = ? AND voter_token = ?")->execute([$g['group_id'], 'u' . $target]);
        out(['success' => true, 'message' => 'เชิญออกจากห้องแล้ว']);

    // ---------- เฉลยผล: หัวห้องเท่านั้น และต้องโหวตครบทุกคน ----------
    case 'announce':
        $g = getGroupById($pdo, $input['group_id'] ?? 0);
        if (!$g) fail('ไม่พบห้องโหวต');
        if ((int)$g['owner_user_id'] !== $uid) fail('เฉพาะหัวห้องเท่านั้นที่เฉลยผลได้');
        if (!$g['is_closed']) {
            $st = roomState($pdo, $g, $uid);
            if ($st['voted_count'] < $st['total']) fail('ยังโหวตไม่ครบ (' . $st['voted_count'] . '/' . $st['total'] . ' คน)');
            // นับคะแนน หาผู้ชนะ (คะแนนเท่ากัน → สุ่มจากร้านที่เสมอกัน)
            $s = $pdo->prepare("SELECT o.option_id, COUNT(v.vote_id) AS c
                                FROM TBL_VOTE_OPTION o LEFT JOIN TBL_VOTE_RECORD v ON v.option_id = o.option_id
                                WHERE o.group_id = ? GROUP BY o.option_id ORDER BY c DESC");
            $s->execute([$g['group_id']]);
            $rows = $s->fetchAll();
            $max = (int)$rows[0]['c'];
            $top = array_values(array_filter($rows, function ($r) use ($max) { return (int)$r['c'] === $max; }));
            $winner = $top[array_rand($top)]['option_id'];
            $pdo->prepare("UPDATE TBL_VOTE_GROUP SET is_closed = 1, winner_option_id = ? WHERE group_id = ?")->execute([$winner, $g['group_id']]);
            $g = getGroupById($pdo, $g['group_id']);
            $tie = count($top) > 1;
        }
        $st = roomState($pdo, $g, $uid);
        $st['note'] = !empty($tie) ? 'มีร้านที่ได้คะแนนเท่ากัน ระบบสุ่มจากร้านที่เสมอกัน' : '';
        out($st);

    // ---------- ห้องที่ฉันอยู่ ----------
    case 'my_rooms':
        $s = $pdo->prepare("SELECT g.share_code, g.is_closed, g.created_at, (g.owner_user_id = ?) AS is_owner,
                                   (SELECT COUNT(*) FROM TBL_VOTE_MEMBER x WHERE x.group_id = g.group_id) AS members
                            FROM TBL_VOTE_MEMBER m JOIN TBL_VOTE_GROUP g ON g.group_id = m.group_id
                            WHERE m.user_id = ? ORDER BY g.created_at DESC LIMIT 15");
        $s->execute([$uid, $uid]);
        out(['success' => true, 'rooms' => $s->fetchAll()]);

    default:
        fail('ไม่รู้จัก action: ' . $action);
}
