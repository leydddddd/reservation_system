<?php
//1. Requirements
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
    SELECT id, room_id, total_price, name, email, phone, date, status, expires_at, gateway_order_id, payment_type
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

// === 2. FETCH BOOKED HOURS ===
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

// === 3. FETCH ROOM DATA ===
$stmt = $ineedthis->prepare("
    SELECT nama_ruangan, kapasitas, alamat, price
    FROM rooms
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $booking['room_id']);
$stmt->execute();

$room = $stmt->get_result()->fetch_assoc();
$room_name = $room['nama_ruangan'] ?? 'Unknown Room';
$room_unit_price = floatval($room['price'] ?? 0);
$stmt->close();
if (!$room) {
    die("Room not found.");
}

// === 4. CALCULATE PRICING DETAILS ===
$admin_fee = 5000;
$room_subtotal = count($booked_hours) * $room_unit_price;
$tax = 0.11 * $room_subtotal;
$admin_and_tax = $admin_fee + $tax;
$total_payment = round($room_subtotal + $admin_fee + $tax);


// === 5. DISPLAY INVOICE PAGE ===
$status_text = match ($booking['status']) {
    'paid' => 'Lunas',
    'pending' => 'Menunggu Pembayaran',
    'failed' => 'Gagal',
    default => ucfirst($booking['status']),
};
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Tekape Workspace</title>
    <link rel="stylesheet" href="style.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>

<body>
    <div class="invoice-container">
        <div class="invoice-header-bg">
            <div class="invoice-actions">
                <button class="btn-download" onclick="printThenDownload()">Download PDF</button>
            </div>
        </div>

        <div class="invoice-card">
            <div class="invoice-header">
                <div class="invoice-logo">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='70' font-size='60' fill='%23FF7F3E'%3E🏢%3C/text%3E%3C/svg%3E" alt="Tekape" width="60">
                    <div>
                        <h1>Tekape</h1>
                        <p>Pesan Ruangan dengan Mudah & Cepat</p>
                    </div>
                </div>
                <div class="invoice-info">
                    <h2>INVOICE</h2>
                    <p class="invoice-number" id="invoiceNumber">INV/<?= htmlspecialchars($booking['gateway_order_id'] ?? '—'); ?></p>
                    <div class="status-badge">
                        <div class="status-pill" id="statusPembayaran"><?= htmlspecialchars($status_text) ?></div>
                    </div>
                    <p class="invoice-date">Tanggal Invoice<br><strong id="tanggalInvoice"><?php echo htmlspecialchars($booking['date']); ?></strong></p>
                </div>
            </div>

            <div class="invoice-body">
                <div class="section-customer">
                    <h3>👤 Informasi Pelanggan</h3>
                    <div class="customer-details">
                        <div class="detail-item">
                            <span class="label">Nama</span>
                            <p id="custName"><?= htmlspecialchars($booking['name']) ?? "-" ?></p>
                        </div>
                        <div class="detail-item">
                            <span class="label">✉️ Email</span>
                            <p id="custEmail"><?= htmlspecialchars($booking['email']) ?? "-" ?></p>
                        </div>
                        <div class="detail-item">
                            <span class="label">📞 Telepon</span>
                            <p id="custPhone"><?= htmlspecialchars($booking['phone']) ?? "-" ?></p>
                        </div>
                        <!-- <div class="detail-item">
                            <span class="label">Perusahaan</span>
                            <p id="custCompany">PT.Peyelamat Bumi</p>
                        </div> -->
                    </div>
                </div>

                <div class="section-booking">
                    <h3>📝 Detail Pesanan</h3>
                    <div class="customer-details">
                        <div class="detail-item">
                            <span class="label">ID Booking</span>
                            <p id="bookingIdInv"><?= htmlspecialchars($booking['gateway_order_id'] ?? '—'); ?></p>
                        </div>
                        <div class="detail-item">
                            <span class="label">Ruangan</span>
                            <p id="roomName"><?= htmlspecialchars($room_name) ?></p>
                            <small>Kapasitas: <?= htmlspecialchars($room['kapasitas']) ?? "-" ?></small>
                        </div>
                        <div class="detail-item">
                            <span class="label">📍 Lokasi</span>
                            <p id="roomLocation"><?= htmlspecialchars($room['alamat']) ?? "-" ?></p>
                        </div>
                        <div class="detail-item">
                            <span class="label">📅 Tanggal Booking</span>
                            <p id="bookingDate"><?php echo htmlspecialchars($booking['date']); ?></p>
                        </div>
                        <div class="detail-item">
                            <span class="label">⏰ Waktu</span>
                            <p id="bookingTime"><?php echo htmlspecialchars(implode(' - ', $booked_hours)); ?></p>
                            <small>(<?php echo count($booked_hours); ?> Jam)</small>
                        </div>
                    </div>
                </div>

                <div class="section-price">
                    <h3>Rincian Biaya</h3>
                    <table class="price-table">
                        <!-- <thead>
                            <tr>
                                <th>Item</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: right;">Harga Satuan</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody id="priceTableBody">
                            <tr>
                                <td>Sewa Ruangan (3 Jam)</td>
                                <td style="text-align: center;">1</td>
                                <td style="text-align: right;">Rp 50.000</td>
                                <td style="text-align: right;">Rp 50.000</td>
                            </tr>
                            <tr>
                                <td>Proyektor & Sound System</td>
                                <td style="text-align: center;">1</td>
                                <td style="text-align: right;">Rp 30.000</td>
                                <td style="text-align: right;">Rp 30.000</td>
                            </tr>
                            <tr>
                                <td>Catering Snack (20 Pax)</td>
                                <td style="text-align: center;">20</td>
                                <td style="text-align: right;">Rp 15.000</td>
                                <td style="text-align: right;">Rp 300.000</td>
                            </tr>
                        </tbody> -->
                        <tfoot>
                            <!-- <tr class="subtotal-row">
                                <td colspan="3">Subtotal</td>
                                <td style="text-align: right;" id="subtotalAmount">Rp 380.000</td>
                            </tr> -->
                            <tr class="tax-row">
                                <td colspan="3">Biaya Admin</td>
                                <td style="text-align: right;" id="taxAmount">Rp <?php echo number_format($admin_fee, 0, ',', '.') ?></td>
                            </tr>
                            <tr class="tax-row">
                                <td colspan="3">Pajak (PPN 11%)</td>
                                <td style="text-align: right;" id="taxAmount">Rp <?php echo number_format($tax, 0, ',', '.') ?></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3"><strong>TOTAL BIAYA</strong></td>
                                <td style="text-align: right;"><strong id="totalAmount">Rp <?php echo number_format($booking['total_price'], 0, ',', '.') ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="section-notes">
                    <h4>Catatan</h4>
                    <p>Terima kasih atas kepercayaan Anda menggunakan layanan Tekape Workspace.<br>
                        Invoice ini merupakan bukti pembayaran yang sah.<br>
                        Harap simpan invoice ini untuk referensi Anda.</p>

                    <div class="contact-info">
                        <p><strong>Informasi Kontak</strong></p>
                        <p>Email: support@tekape.space<br>
                            Telepon: +62 21 1234 5678<br>
                            Website: www.tekape.space</p>
                    </div>
                </div>
            </div>

            <div class="invoice-footer">
                <p>© 2025 Tekape Workspace. All rights reserved.</p>
            </div>
        </div>
    </div>
    <script>
        function printThenDownload() {

            // 1. Trigger print
            window.print();

            // 2. After print dialog closes, trigger PDF download
            setTimeout(() => {
                const element = document.querySelector('.invoice-container');
                const opt = {
                    margin: 10,
                    filename: 'invoice.pdf',
                    html2canvas: {
                        scale: 2
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait'
                    }
                };
                html2pdf().set(opt).from(element).save();
            }, 500);
        }
    </script>
</body>

</html>