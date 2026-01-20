<?php
header("Content-Type: application/json");

require_once __DIR__ . '/connection.php';
$config = require __DIR__ . '/_configuration.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Midtrans config
\Midtrans\Config::$serverKey = $config['midtrans']['server_key'];
\Midtrans\Config::$isProduction = $config['midtrans']['is_production'];
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Helper
function jsonErr($msg, $code = 400)
{
    http_response_code($code);
    echo json_encode(["error" => $msg]);
    exit;
}

// Read JSON body safely
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Debug logs
error_log("RAW: " . $raw);
error_log("PARSED: " . print_r($data, true));

// Validate request
if (!is_array($data)) jsonErr("Invalid JSON");

if (empty($data['booking_id'])) jsonErr("Missing booking_id");
if (empty($data['payment_method'])) jsonErr("Missing payment_method");

// Now SAFE
$booking_id = intval($data['booking_id']);
$payment_method = strtolower($data['payment_method']);

// optional amount from client (ignored, only server-side calc used)
$clientAmount = isset($data['amount']) ? intval($data['amount']) : null;

if ($booking_id <= 0) jsonErr('Missing or invalid booking_id');
if ($payment_method === '') jsonErr('Missing payment_method');

// prepare normalized method string for switch()
$method = $payment_method;

// fetch booking and ensure it's in held/pending state
$stmt = $ineedthis->prepare("SELECT id, room_id, total_price, name, email, date, status, expires_at FROM bookings WHERE id = ? LIMIT 1");
if (!$stmt) jsonErr('DB prepare failed (booking)');
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) jsonErr('Booking not found', 404);

// check hold expiry/status
$expiresTime = strtotime($booking['expires_at']);
$now = time();
if ($booking['status'] !== 'held' || ($expiresTime && $now > $expiresTime)) {
    // mark cancelled if expired
    if ($booking['status'] !== 'cancelled') {
        $u = $ineedthis->prepare("UPDATE bookings SET status='cancelled' WHERE id=?");
        if ($u) {
            $u->bind_param('i', $booking_id);
            $u->execute();
            $u->close();
        }
    }
    jsonErr('Booking expired or not held', 400);
}

// compute authoritative amount server-side (you may change fees here)
$roomStmt = $ineedthis->prepare("SELECT price FROM rooms WHERE id = ? LIMIT 1");
$roomPrice = 0;
if ($roomStmt) {
    $roomStmt->bind_param('i', $booking['room_id']);
    $roomStmt->execute();
    $room = $roomStmt->get_result()->fetch_assoc();
    $roomPrice = floatval($room['price'] ?? 0.0);
    $roomStmt->close();
}

// fetch booked hours count
$hstmt = $ineedthis->prepare("SELECT COUNT(*) AS cnt FROM booking_hours WHERE booking_id = ?");
$hoursCount = 0;
if ($hstmt) {
    $hstmt->bind_param('i', $booking_id);
    $hstmt->execute();
    $r = $hstmt->get_result()->fetch_assoc();
    $hoursCount = intval($r['cnt'] ?? 0);
    $hstmt->close();
}

// server-side calculation: subtotal, admin, tax, total
$admin_fee = 5000;
$room_subtotal = $hoursCount * $roomPrice;
$tax = round(0.11 * $room_subtotal);
$total_amount = (int) round($room_subtotal + $admin_fee + $tax);

// if DB already contains total_price, prefer it (but still int)
if (!empty($booking['total_price'])) {
    $total_amount = (int) round(floatval($booking['total_price']));
}

// create gateway order id and store it (for later callback matching)
$orderId = "PAY-{$booking_id}-" . time();

$upd = $ineedthis->prepare("UPDATE bookings SET gateway_order_id = ? WHERE id = ?");
if ($upd) {
    $upd->bind_param('si', $orderId, $booking_id);
    $upd->execute();
    $upd->close();
}

// prepare item description (hours list)
$hstmt2 = $ineedthis->prepare("SELECT hour FROM booking_hours WHERE booking_id = ? ORDER BY hour ASC");
$hourList = [];
if ($hstmt2) {
    $hstmt2->bind_param('i', $booking_id);
    $hstmt2->execute();
    $all = $hstmt2->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach ($all as $r) $hourList[] = $r['hour'];
    $hstmt2->close();
}
$itemName = "Booking Room ({$booking['date']}) " . (count($hourList) ? implode(', ', $hourList) : '');

// build base params
$params = [
    'transaction_details' => [
        'order_id' => $orderId,
        'gross_amount' => $total_amount
    ],
    'item_details' => [
        [
            'id' => "room_" . intval($booking['room_id']),
            'price' => $total_amount,
            'quantity' => 1,
            'name' => $itemName
        ]
    ],
    'customer_details' => [
        'first_name' => $booking['name'] ?? 'Guest',
        'email' => $booking['email'] ?? 'guest@example.com'
    ]
];

// map your frontend method -> Midtrans settings
$method = strtolower($payment_method);
switch ($method) {
    case 'bank':
    case 'bank_transfer':
        // let Snap present bank options, or force a specific bank:
        // to restrict to specific bank use 'enabled_payments' + 'bank_transfer' key
        // Example: enable only bank_transfer
        $params['enabled_payments'] = ['bank_transfer'];
        break;

    case 'va':
        // Virtual account: you can let snap choose or you can request specific bank
        // Example: allow bank_transfer only (will show VA options)
        $params['enabled_payments'] = ['bank_transfer'];
        break;

    case 'ewallet':
    case 'gopay':
        // enable typical e-wallets; snap will show available ones in sandbox
        $params['enabled_payments'] = ['gopay', 'shopeepay'];
        break;

    case 'card':
    case 'credit_card':
        $params['enabled_payments'] = ['credit_card'];
        // enforce 3ds already enabled in config
        break;

    case 'qris':
        // enable qris
        $params['enabled_payments'] = ['qris'];
        break;

    default:
        jsonErr('Invalid payment method');
}

// optional: if you want Snap to only show a single payment option more strictly,
// map a frontend value to a more precise payload. Example for forcing BCA VA:
/*
if ($method === 'va_bca') {
    $params['enabled_payments'] = ['bank_transfer'];
    $params['bank_transfer'] = ['bank' => 'bca'];
}
*/

// call Midtrans Snap
try {
    $token = \Midtrans\Snap::getSnapToken($params);
    echo json_encode(['token' => $token, 'order_id' => $orderId, 'amount' => $total_amount]);
    exit;
} catch (Exception $e) {
    error_log('Midtrans Snap token error: ' . $e->getMessage());
    jsonErr('Failed to get snap token: ' . $e->getMessage(), 500);
}
