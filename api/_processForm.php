<?php
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Clear any output buffer
while (ob_get_level() > 0) {
    ob_end_clean();
}
require_once 'connection.php'; // must provide $ineedthis (mysqli)

function bad($msg)
{
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

// Input parsing + basic validation
$room_id       = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
$selected_date = isset($_POST['selected_date']) ? $_POST['selected_date'] : '';
$hoursJSON     = isset($_POST['hours']) ? $_POST['hours'] : '[]';
$final_price   = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0.0;
$customer_name  = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
$customer_email = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : '';
$customer_phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';

// minimal checks
if ($room_id <= 0) bad('Invalid room_id');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) bad('Invalid date format');
$hours = json_decode($hoursJSON, true);
if (!is_array($hours) || count($hours) === 0) bad('No hours provided');
if ($final_price < 0) bad('Invalid total price');
if ($customer_name === '' || $customer_email === '' || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    bad('Invalid customer info');
}

// normalize hours to "HH:00"
$normalizedHours = [];
foreach ($hours as $h) {
    $h = trim($h);
    if ($h === '') continue;
    if (preg_match('/^\d{1,2}$/', $h)) {
        $hh = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
    } elseif (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $h)) {
        $parts = explode(':', $h);
        $hh = str_pad($parts[0], 2, '0', STR_PAD_LEFT) . ':00';
    } else {
        bad('Invalid hour format: ' . $h);
    }
    $normalizedHours[] = $hh;
}
$normalizedHours = array_values(array_unique($normalizedHours));
if (count($normalizedHours) === 0) bad('No valid hours');

$ineedthis->begin_transaction();

try {
    // 1) Check availability for each requested hour
    $statuses = ['held', 'pending', 'booked', 'active'];
    // Create placeholders for SQL IN clause (e.g., "?, ?, ?, ?")
    $placeholders = implode(',', array_fill(0, count($statuses), '?'));
    $checkSql = "
        SELECT COUNT(*) AS cnt
        FROM booking_hours bh
        JOIN bookings b ON b.id = bh.booking_id
        WHERE b.room_id = ?
          AND b.date = ?
          AND b.status IN ($placeholders)
          AND bh.hour = ?
    ";
    // Check each requested hour for availability
    $checkStmt = $ineedthis->prepare($checkSql);
    if (!$checkStmt) throw new Exception('Prepare failed (availability check): ' . $ineedthis->error);
    foreach ($normalizedHours as $hour) {
        // Build bind params: room_id (i) + selected_date (s) + statuses (s x 4) + hour (s)
        $bindParams = array_merge([$room_id, $selected_date], $statuses, [$hour]);
        // types: i (room_id) + s (selected_date) + s (held) + s (pending) + s (booked) + s (active) + s (hour)
        // That's 1 + 1 + 4 + 1 = 7 total, so: 'i' + 6 's's = 'issssss'
        $bindTypes = 'i' . str_repeat('s', count($statuses) + 2); // 'issssss'

        $refs = [];
        foreach ($bindParams as $k => $v) $refs[$k] = &$bindParams[$k];

        // ✅ FIXED: use call_user_func_array instead of spread operator
        $bound = call_user_func_array([$checkStmt, 'bind_param'], array_merge([$bindTypes], $refs));
        if (!$bound) throw new Exception('Bind failed (availability check): ' . $checkStmt->error);
        if (!$checkStmt->execute()) throw new Exception('Execute failed (availability check): ' . $checkStmt->error);

        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();
        if ($row['cnt'] > 0) {
            $checkStmt->close();
            bad('One or more selected hours are not available');
        }
    }
    $checkStmt->close();

    // 2) Insert booking with 'held' status (expires in 15 minutes)
    $expiresAt = date('Y-m-d H:i:s', time() + 15 * 60); // 15 min hold
    $insertBookingSql = "INSERT INTO bookings (room_id, date, name, email, phone, total_price, status, expires_at, created_at) VALUES (?, ?, ?, ?, ?, ?, 'held', ?, NOW())";
    $typesIns = "issssds";
    $insStmt = $ineedthis->prepare($insertBookingSql);
    if (!$insStmt) {
        throw new Exception('Prepare failed (insert booking): ' . $ineedthis->error);
    }
    if (!$insStmt->bind_param($typesIns, $room_id, $selected_date, $customer_name, $customer_email, $customer_phone, $final_price, $expiresAt)) {
        throw new Exception('Bind failed (insert booking): ' . $insStmt->error);
    }
    if (!$insStmt->execute()) {
        throw new Exception('Execute failed (insert booking): ' . $insStmt->error);
    }
    $booking_id = $ineedthis->insert_id;
    $insStmt->close();

    // 3) Insert booking_hours rows (one row per hour)
    $insertHourSql = "INSERT INTO booking_hours (booking_id, hour) VALUES (?, ?)";
    $insHourStmt = $ineedthis->prepare($insertHourSql);
    if (!$insHourStmt) throw new Exception('Prepare failed (insert hour): ' . $ineedthis->error);

    foreach ($normalizedHours as $hour) {
        if (!$insHourStmt->bind_param("is", $booking_id, $hour)) {
            throw new Exception('Bind failed (insert hour): ' . $insHourStmt->error);
        }
        if (!$insHourStmt->execute()) {
            throw new Exception('Execute failed (insert hour): ' . $insHourStmt->error);
        }
    }
    $insHourStmt->close(); // close before commit

    $ineedthis->commit();

    // debug log and force absolute redirect (temporary)
    $redirectId = intval($booking_id);
    file_put_contents(__DIR__ . '/_processForm_log.txt', date('c') . " created booking_id=" . $redirectId . PHP_EOL, FILE_APPEND);

    // build absolute URL to avoid relative-path ambiguity
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = dirname($_SERVER['REQUEST_URI'], 2); // move up to /web
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $host . $base . '/pages/payment.php?booking_id=' . $redirectId;

    header('Location: ' . $url, true, 302);
    exit;
} catch (Exception $e) {
    $ineedthis->rollback();
    // Always return JSON for consistency
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
};
