<?php
// filepath: [confirmation.php](http://_vscodecontentref_/1)
require_once '../api/connection.php';

// === GET BOOKING ID ===
if (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);
} elseif (isset($_GET['order_id'])) {
    // Extract from PAY-{ID}-{timestamp}
    preg_match("/PAY-(\d+)-/", $_GET['order_id'], $m);
    $booking_id = $m[1] ?? 0;
} else {
    $booking_id = 0;
}

// === 1. FETCH BOOKING DATA ===
$stmt = $ineedthis->prepare("
    SELECT id, room_id, total_price, name, email, date, status, expires_at, gateway_order_id, payment_type
    FROM bookings
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    die("Booking not found.");
}

// === 2. FETCH ROOM DATA ===
$stmt = $ineedthis->prepare("
    SELECT nama_ruangan
    FROM rooms
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $booking['room_id']);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$room_name = $room['nama_ruangan'] ?? 'Unknown Room';
$stmt->close();
if (!$room) {
    die("Room not found.");
}

// === 3. FETCH BOOKED HOURS ===
$stmt = $ineedthis->prepare("
    SELECT hour 
    FROM booking_hours 
    WHERE booking_id = ?
    ORDER BY hour ASC
");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

$booked_hours = [];
while ($row = $result->fetch_assoc()) {
    $booked_hours[] = $row['hour'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - Tekape Workspace</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="header-orange">
        <div class="logo">
            <span class="logo-icon">TW</span>
            <span class="logo-text">Tekape Workspace</span>
        </div>
    </header>

    <div class="confirmation-container">
        <div class="confirmation-card">
            <h2>Detail Booking</h2>

            <div class="booking-info">
                <div class="info-label">D Booking</div>
                <div class="info-value"><?= htmlspecialchars($booking['gateway_order_id'] ?? '—'); ?></div>
            </div>

            <div class="booking-info">
                <div class="info-label">Nama Ruangan</div>
                <div class="info-value"><?php echo htmlspecialchars($room_name); ?></div>
            </div>

            <div class="booking-info">
                <div class="info-label">Tanggal</div>
                <div class="info-value"><?php echo htmlspecialchars($booking['date']); ?></div>
            </div>

            <div class="booking-info">
                <div class="info-label">Waktu</div>
                <div class="info-value"><?php echo htmlspecialchars(implode(' - ', $booked_hours)); ?></div>
            </div>

            <div class="booking-info">
                <div class="info-label">Durasi</div>
                <div class="info-value"><?php echo count($booked_hours); ?> Jam</div>
            </div>

            <div class="booking-info">
                <div class="info-label">Metode Pembayaran</div>
                <div class="info-value"><?= htmlspecialchars($booking['payment_type'] ?? '—')  ?></div>
            </div>

            <div class="payment-total">
                <span>Total Dibayar</span>
                <strong>Rp. <?php echo number_format($booking['total_price'], 0, ',', '.') ?></strong>
            </div>

            <div class="order-info">
                <h3>Informasi Pemesan</h3>
                <div class="info-row">
                    <span>Nama</span>
                    <strong><?= htmlspecialchars($booking['name']) ?></strong>
                </div>
                <div class="info-row">
                    <span>Email</span>
                    <strong><?= htmlspecialchars($booking['email']) ?></strong>
                </div>
            </div>

            <div class="action-buttons-confirmation">
                <button class="btn-outline" id="btnLihatInvoice">Lihat Invoice</button>
            </div>

            <div class="info-notice">
                <strong>Informasi Penting</strong>
                <p>Invoice telah dikirim ke email Anda. Silakan tunjukkan invoice atau ID booking saat check-in. Jika ada pertanyaan, hubungi kami di support@tekape.space</p>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('btnLihatInvoice').addEventListener('click', function() {
            window.location.href = 'invoice.php?booking_id=<?= $booking_id ?>';
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const bookingId = "<?php echo $booking['id']; ?>";

            fetch(`../api/_mailedInvoice.php?booking_id=` + bookingId)
                .then(r => r.text())
                .then(res => console.log(res))
                .catch(err => console.error(err));
        });
    </script>
    <footer>
        <p>© 2025 Tekape Workspace. All rights reserved.</p>
    </footer>
</body>

</html>