<?php
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0, private');
header('Pragma: no-cache');
header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

require_once '../api/connection.php';
$config = require_once '../api/_configuration.php';

// === 1. VALIDATE INPUT ===
if (!isset($_GET['booking_id'])) {
    header('Location: ../index.php');
    exit;
}

$booking_id = intval($_GET['booking_id']);

// === 2. FETCH BOOKING DATA ===
$stmt = $ineedthis->prepare("
    SELECT id, room_id, total_price, name, email, date, status, expires_at
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

// === 3. VALIDATE HOLD STATUS ===
$expiresTime = strtotime($booking['expires_at']);
$now = time();

if ($booking['status'] !== 'held' || $now > $expiresTime) {
    // release booking
    $upd = $ineedthis->prepare("UPDATE bookings SET status='cancelled' WHERE id=?");
    $upd->bind_param("i", $booking_id);
    $upd->execute();
    $upd->close();

    die("Hold expired. Please book again.");
}

// === 4. FETCH BOOKED HOURS ===
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

// === 5. FETCH ROOM DETAILS ===
$roomStmt = $ineedthis->prepare("
    SELECT nama_ruangan, price 
    FROM rooms 
    WHERE id = ? 
    LIMIT 1
");
$roomStmt->bind_param("i", $booking['room_id']);
$roomStmt->execute();
$room = $roomStmt->get_result()->fetch_assoc();
$roomStmt->close();

$room_name = $room['nama_ruangan'] ?? 'Unknown Room';
$room_unit_price = floatval($room['price'] ?? 0);

// === 6. CALCULATE PAYMENT DETAILS ===
$admin_fee = 5000;
$room_subtotal = count($booked_hours) * $room_unit_price;
$tax = round(0.11 * $room_subtotal);
$total_payment = round($room_subtotal + $admin_fee + $tax);

// authoritative fallback from DB
$final_amount = isset($booking['total_price']) ? floatval($booking['total_price']) : $total_payment;

// === 7. CALCULATE TIME LEFT FOR COUNTDOWN ===
$timeRemaining = max(0, $expiresTime - $now);
$minutesLeft = intdiv($timeRemaining, 60);
$secondsLeft = $timeRemaining % 60;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="<?= htmlspecialchars($config['midtrans']['client_key']); ?>"></script>
    <title>Payment - Tekape Workspace</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Hidden Input of booking_id -->
    <?php echo "<!-- LOADED FROM: " . __FILE__ . " -->"; ?>
    <?php echo "<script>console.log('booking_id PHP: " . $booking['id'] . "');</script>"; ?>

    <header>
        <div class="logo">
            <span class="logo-icon">TW</span>
            <span class="logo-text">Tekape</span>
        </div>
    </header>

    <div class="progress-bar">
        <div class="progress-step completed">
            <div class="step-circle">1</div>
            <div class="step-label">Booking</div>
        </div>
        <div class="progress-line completed"></div>
        <div class="progress-step active">
            <div class="step-circle">2</div>
            <div class="step-label">Payment</div>
        </div>
        <div class="progress-line"></div>
        <div class="progress-step">
            <div class="step-circle">3</div>
            <div class="step-label">Confirmation</div>
        </div>
    </div>

    <div class="container">
        <div class="main-content">
            <div class="payment-section">
                <h2 class="section-title">PEMBAYARAN</h2>
                <p class="section-subtitle">Pilih metode pembayaran dan selesaikan transaksi Anda</p>

                <div class="timer-box">
                    <span class="timer-icon">⏰</span>
                    <div>
                        <strong>Selesaikan Pembayaran dalam</strong>
                        <div class="countdown" id="countdown"><?php echo str_pad($minutesLeft, 2, '0', STR_PAD_LEFT) . ':' . str_pad($secondsLeft, 2, '0', STR_PAD_LEFT); ?></div>
                    </div>
                </div>

                <h3 class="payment-title">💳 PILIH METODE PEMBAYARAN</h3>

                <div class="payment-methods">
                    <label class="payment-option">
                        <input type="radio" name="paymentMethod" value="bank" checked>
                        <div class="payment-card">
                            <span class="payment-icon">🏛️</span>
                            <div class="payment-info">
                                <strong>Transfer Bank</strong>
                                <p>BCA, BNI, BRI, Mandiri</p>
                            </div>
                        </div>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="paymentMethod" value="va">
                        <div class="payment-card">
                            <span class="payment-icon">🏧</span>
                            <div class="payment-info">
                                <strong>Virtual Account</strong>
                                <p>Nomor VA akan digenerate otomatis</p>
                            </div>
                        </div>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="paymentMethod" value="ewallet">
                        <div class="payment-card">
                            <span class="payment-icon">💰</span>
                            <div class="payment-info">
                                <strong>E - Wallet</strong>
                                <p>GoPay, OVO, DANA, ShopeePay</p>
                            </div>
                        </div>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="paymentMethod" value="card">
                        <div class="payment-card">
                            <span class="payment-icon">💳</span>
                            <div class="payment-info">
                                <strong>Kartu Kredit/Debit</strong>
                                <p>Visa, Mastercard, JCB</p>
                            </div>
                        </div>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="paymentMethod" value="qris">
                        <div class="payment-card">
                            <span class="payment-icon">📷</span>
                            <div class="payment-info">
                                <strong>QRIS</strong>
                                <p>Scan QR untuk bayar</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="sidebar">
            <div class="summary-card">
                <h3>RINGKASAN PEMBAYARAN</h3>

                <div class="booking-summary">
                    <div class="summary-row">
                        <span>Ruangan</span>
                        <strong><?php echo htmlspecialchars($room_name); ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Tanggal</span>
                        <strong id="displayTanggal"><?php echo htmlspecialchars($booking['date']); ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Waktu</span>
                        <strong id="displayWaktu"><?php echo htmlspecialchars(implode(' - ', $booked_hours)); ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Durasi</span>
                        <strong id="displayDurasi"><?php echo count($booked_hours); ?> Jam</strong>
                    </div>
                </div>

                <div class="summary-item">
                    <span>Sewa Ruangan</span>
                    <strong>Rp <?php echo number_format($room_subtotal, 0, ',', '.'); ?></strong>
                </div>
                <div class="summary-item">
                    <span>Biaya Admin</span>
                    <strong>Rp <?php echo number_format($admin_fee, 0, ',', '.'); ?></strong>
                </div>
                <div class="summary-item">
                    <span>PPN (11%)</span>
                    <strong>Rp <?php echo number_format($tax, 0, ',', '.') ?> </strong>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-total">
                    <span>Total Pembayaran</span>
                    <strong class="total-amount">Rp. <?php echo number_format($booking['total_price'], 0, ',', '.') ?></strong>
                </div>

                <button type="button" class="btn-primary" id="btnPay">
                    BAYAR SEKARANG
                </button>

                <button type="button" class="btn-secondary" id="btnKembaliBooking">
                    ← KEMBALI KE BOOKING
                </button>

                <div class="info-box" style="margin-top: 20px;">
                    🔒 Pembayaran aman dan terenkripsi
                </div>
            </div>

            <div class="help-card">
                <h3>❓ Butuh Bantuan?</h3>
                <div class="help-item">
                    <span class="icon">✉️</span>
                    <span>support@tekapeworkspace.com</span>
                </div>
                <div class="help-item">
                    <span class="icon">📞</span>
                    <span>+62 812 3456 7890</span>
                </div>
                <div class="help-item">
                    <span class="icon">💬</span>
                    <span>Live Chat (09:00 - 17:00)</span>
                </div>
            </div>
        </div>
    </div>
    <script>
        // === COUNTDOWN TIMER ===
        let timeLeft = <?= $timeRemaining ?>;

        function updateCountdown() {
            if (timeLeft <= 0) {
                document.getElementById('countdown').innerText = "00:00";
                alert("Waktu pemesanan habis!");
                window.location.href = "../index.php";
                return;
            }

            let m = Math.floor(timeLeft / 60);
            let s = timeLeft % 60;

            document.getElementById('countdown').innerText =
                `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;

            timeLeft--;
        }
        setInterval(updateCountdown, 1000);
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const payBtn = document.getElementById("btnPay");

            if (!payBtn) {
                console.error("Button not found 404");
                return;
            }

            payBtn.addEventListener("click", function() {

                // get selected payment method
                const selected = document.querySelector('input[name="paymentMethod"]:checked');
                if (!selected) {
                    alert("Silakan pilih metode pembayaran.");
                    return;
                }

                const paymentMethod = selected.value;

                // booking_id from PHP
                const bookingId = "<?= $booking['id'] ?>";

                console.log("Sending:", {
                    booking_id: bookingId,
                    payment_method: paymentMethod
                });

                // disable button while loading
                payBtn.disabled = true;
                payBtn.innerHTML = "Processing...";

                console.log("Sending request:", {
                    booking_id: bookingId,
                    payment_method: paymentMethod
                });

                //fetch snap token from server


                fetch("../api/_snapToken.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            booking_id: bookingId,
                            payment_method: paymentMethod
                        })
                    })
                    .then(r => r.json())
                    .then(res => {
                        console.log("SnapToken response:", res);

                        if (!res.token) {
                            alert("Gagal mendapatkan token pembayaran: " + (res.error || "unknown error"));
                            payBtn.disabled = false;
                            payBtn.innerHTML = "Bayar Sekarang";
                            return;
                        }

                        // Open Snap popup
                        snap.pay(res.token, {

                            onSuccess: function(result) {
                                console.log("Success:", result);
                                window.location.href = "../pages/confirmation.php?order_id=" + res.order_id;
                            },

                            onPending: function(result) {
                                console.log("Pending:", result);
                                window.location.href = "/pages/payment_pending.php?order_id=" + res.order_id;
                            },

                            onError: function(result) {
                                console.log("Error:", result);
                                window.location.href = "/pages/payment_failed.php?order_id=" + res.order_id;
                            },

                            onClose: function() {
                                console.log("User closed the popup.");
                                payBtn.disabled = false;
                                payBtn.innerHTML = "Bayar Sekarang";
                            }

                        });

                    })
                    .catch(err => {
                        console.error("Error:", err);
                        alert("Terjadi kesalahan saat membuat token pembayaran.");
                        payBtn.disabled = false;
                        payBtn.innerHTML = "Bayar Sekarang";
                    });

            });

        });
    </script>
</body>

</html>