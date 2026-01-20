<?php
header("Content-Type: application/json");

// Allow only POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(200);
    echo json_encode(["message" => "Method ignored"]);
    exit;
}

// Load Midtrans config
$config = require_once "_configuration.php";
$serverKey = $config['midtrans']['server_key'];

// Read raw payload once
$raw = file_get_contents("php://input");
file_put_contents(
    __DIR__ . "/callback_debug.log",
    "=== CALLBACK RECEIVED === " . date("Y-m-d H:i:s") . "\n$raw\n\n",
    FILE_APPEND
);

$data = json_decode($raw, true);

// Validate JSON
if (!$data) {
    http_response_code(200);
    echo json_encode(["message" => "Invalid JSON"]);
    exit;
}

// Validate required fields
if (!isset($data['order_id'], $data['status_code'], $data['gross_amount'], $data['signature_key'])) {
    http_response_code(200);
    echo json_encode(["message" => "Missing fields"]);
    exit;
}

$order_id     = $data['order_id'];
$status_code  = $data['status_code'];
$gross_amount = $data['gross_amount'];
$signature    = $data['signature_key'];

// Compute signature
$expected = hash("sha512", $order_id . $status_code . $gross_amount . $serverKey);

if ($signature !== $expected) {
    http_response_code(200);
    echo json_encode(["message" => "Signature mismatch"]);
    exit;
}

// DB connection
require_once __DIR__ . "/connection.php";

// Determine payment status
$mid_status  = $data['transaction_status'];
$payment_type = $data['payment_type'] ?? "unknown";

$mapped = match ($mid_status) {
    "capture", "settlement" => "paid",
    "pending"               => "pending",
    "deny", "cancel"        => "cancelled",
    "expire"                => "expired",
    default                 => "unknown",
};

// Extract booking_id from PAY-{ID}-{timestamp}
preg_match("/PAY-(\d+)-/", $order_id, $m);
$booking_id = $m[1] ?? null;

if (!$booking_id) {
    http_response_code(200);
    echo json_encode(["message" => "booking_id not found"]);
    exit;
}

// Update DB
$stmt = $ineedthis->prepare("
    UPDATE bookings 
    SET status = ?, payment_type = ?
    WHERE id = ?
");
$ok = $stmt->execute([$mapped, $payment_type, $booking_id]);

http_response_code(200);

if ($ok) {
    echo json_encode(["success" => true, "status" => $mapped]);
} else {
    echo json_encode(["message" => "DB update failed"]);
}
