<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'connection.php'; // must define $conn (mysqli)

// Receive input
$room_id = isset($_REQUEST['room_id']) ? intval($_REQUEST['room_id']) : 0;
$date    = isset($_REQUEST['date']) ? $_REQUEST['date'] : '';

if ($room_id === 0 || $date === '') {
    echo json_encode(['error' => 'Missing room_id or date']);
    exit;
}

// Statuses considered as "booked"
$statuses = ['paid'];
$placeholders = implode(',', array_fill(0, count($statuses), '?'));

$sql = "
    SELECT DISTINCT bh.hour
    FROM booking_hours bh
    JOIN bookings b ON b.id = bh.booking_id
    WHERE b.room_id = ?
      AND b.date = ?
      AND b.status IN ($placeholders)
    ORDER BY bh.hour
";

$stmt = $ineedthis->prepare($sql);

if (!$stmt) {
    echo json_encode(['error' => 'Prepare failed: ' . $ineedthis->error]);
    exit;
}

$types = 'is' . str_repeat('s', count($statuses));
$params = array_merge([$room_id, $date], $statuses);

// Bind parameters using references
$bindParams = [];
$bindParams[] = $types;

foreach ($params as $key => $value) {
    $bindParams[] = &$params[$key];
}

call_user_func_array([$stmt, 'bind_param'], $bindParams);

$stmt->execute();
$result = $stmt->get_result();

$hours = [];
while ($row = $result->fetch_assoc()) {
    $hours[] = $row['hour'];
}

echo json_encode(['booked' => $hours]);
exit;
