<?php
require_once 'connection.php';
require_once '_webhook.php';
require_once '_configuration.php';
$config = include __DIR__ . '/_configuration.php';
\Midtrans\Config::$serverKey = $config['midtrans']['server_key'];
\Midtrans\Config::$isProduction = $config['midtrans']['is_production'];
\Midtrans\Config::$isSanitized = true;

header('Content-Type: text/plain');

// Use SDK Notification to parse and validate signature
try {
    $notif = new \Midtrans\Notification();
} catch (Exception $e) {
    http_response_code(400);
    error_log("Midtrans notification init error: " . $e->getMessage());
    exit;
}

$order_id = $notif->order_id ?? null;
$transaction_status = $notif->transaction_status ?? null;
$gross_amount = isset($notif->gross_amount) ? (int)$notif->gross_amount : null;

if (!$order_id) {
    http_response_code(400);
    echo "no order_id";
    exit;
}

// find booking by gateway_order_id
$stmt = $ineedthis->prepare("SELECT id, total_price, status FROM bookings WHERE gateway_order_id = ? LIMIT 1");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    http_response_code(404);
    echo "booking not found";
    exit;
}

// idempotency
if ($booking['status'] === 'paid') {
    http_response_code(200);
    echo "already paid";
    exit;
}

// validate amount (optional but recommended)
if ($gross_amount !== null && (int)round($booking['total_price']) !== (int)$gross_amount) {
    http_response_code(400);
    error_log("amount mismatch for {$order_id}");
    echo "amount mismatch";
    exit;
}

// map midtrans status to booking status
$newStatus = null;
if (in_array($transaction_status, ['settlement', 'capture'])) {
    $newStatus = 'paid';
} elseif (in_array($transaction_status, ['deny', 'cancel', 'expire'])) {
    $newStatus = 'cancelled';
} elseif ($transaction_status === 'pending') {
    $newStatus = 'pending';
}

if ($newStatus) {
    $upd = $ineedthis->prepare("UPDATE bookings SET status = ?, updated_at = NOW() WHERE id = ?");
    $upd->bind_param("si", $newStatus, $booking['id']);
    $upd->execute();
    $upd->close();
}

http_response_code(200);
echo "OK";
exit;
