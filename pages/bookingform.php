<?php
include '../api/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['room_id'])) {
    header('Location: ../index.php');
    exit;
}

$room_id       = intval($_POST['room_id']);
$selected_date = isset($_POST['selected_date']) ? $_POST['selected_date'] : '';
$hours         = json_decode($_POST['hours'] ?? '[]', true);
$total_price   = floatval($_POST['total_price'] ?? 0);

// Calculate additional fees
$taxes = 0.11 * $total_price;
$admin_fee = 5000;
$final_price = $total_price + $taxes + $admin_fee;

$stmt = $ineedthis->prepare("SELECT nama_ruangan, alamat, kapasitas, ukuran FROM rooms WHERE id = ?");
if (!$stmt) {
    die('DB error');
}
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();
$stmt->close();

if (!$room) {
    die('Room not found.');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - Tekape Workspace</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <div class="logo">
            <span class="logo-icon">TW</span>
            <span class="logo-text">Tekape Workspace</span>
        </div>
    </header>

    <div class="progress-bar">
        <div class="progress-step active">
            <div class="step-circle">1</div>
            <div class="step-label">Booking</div>
        </div>
        <div class="progress-line"></div>
        <div class="progress-step">
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
            <div class="form-section">
                <h2 class="section-title">Form Pemesanan Ruangan</h2>
                <p class="section-subtitle">Lengkapi data di bawah untuk melanjutkan pemesanan</p>

                <div class="room-card">
                    <div class="room-icon">🏢</div>
                    <div class="room-info">
                        <h3><?php echo htmlspecialchars($room['nama_ruangan']); ?></h3>
                        <p class="room-location">📍 <?= htmlspecialchars($room['alamat']); ?></p>
                    </div>
                    <div class="room-details">
                        <div class="detail-row">
                            <span>Kapasitas</span>
                            <strong><?php echo htmlspecialchars($room['kapasitas']); ?></strong>
                        </div>
                        <div class="detail-row">
                            <span>Ukuran</span>
                            <strong><?php echo htmlspecialchars($room['ukuran']); ?></strong>
                        </div>
                    </div>
                </div>

                <form id="bookingForm" action="../api/_processForm.php" method="POST">

                    <!-- Hidden Inputs to carry data forward -->
                    <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room_id); ?>">
                    <input type="hidden" name="selected_date" value="<?php echo htmlspecialchars($selected_date); ?>">
                    <input type="hidden" name="hours" value='<?php echo htmlspecialchars(json_encode($hours)); ?>'>
                    <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($final_price); ?>">
                    <input type="hidden" name="room_price" value="<?php echo htmlspecialchars($total_price); ?>">

                    <div class="form-group-title">
                        <span class="icon">📋</span> Informasi Pemesan
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Lengkap <span class="required">*</span></label>
                            <input type="text" id="customer_name" name="customer_name" placeholder="Masukkan Nama Lengkap" required>
                        </div>
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" id="customer_email" name="customer_email" placeholder="nama@email.com" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nomor Telepon <span class="required">*</span></label>
                            <input type="tel" id="customer_phone" name="customer_phone" placeholder="08xxxxxxxxxx" required>
                        </div>
                        <div class="form-group">
                            <label>Organisasi/Perusahaan</label>
                            <input type="text" id="organisasi" name="organisasi" placeholder="Nama Organisasi (opsional)">
                        </div>
                    </div>

                    <!-- <div class="form-group-title">
                        <span class="icon">📅</span> Detail Pemesanan
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tanggal Booking</label>
                            <input type="date" id="tanggalBooking" value="<?php echo htmlspecialchars($selected_date); ?>" readonly required>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Peserta</label>
                            <input type="number" id="jumlahPeserta" name="jumlahPeserta" placeholder="Maks 10 orang" min="1" max="10">
                        </div>

                    </div>

                    <div class="form-group">
                        <label>Durasi Booking</label>
                        <input type="text" id="durasiBooking" value="<?php echo count($hours); ?> Jam" readonly>
                        <small>Durasi otomatis dihitung berdasarkan waktu yang telah dipilih</small>
                    </div>

                    <div class="form-group">
                        <label>Tujuan Penggunaan</label>
                        <select id="tujuanPenggunaan" name="tujuanPenggunaan">
                            <option value="">Pilih tujuan</option>
                            <option value="meeting">Meeting</option>
                            <option value="training">Training</option>
                            <option value="workshop">Workshop</option>
                            <option value="presentation">Presentation</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Catatan Tambahan</label>
                        <textarea id="catatanTambahan" name="catatanTambahan" placeholder="Tambahkan catatan khusus (opsional)" rows="4"></textarea>
                    </div> -->

                    <!-- Add a visible submit button as fallback for users without JS
                    <div class="form-actions" style="margin-top:1rem;">
                        <button type="submit" class="btn-primary">Lanjut Ke Pembayaran</button>
                    </div> -->
                </form>
            </div>
        </div>

        <div class="sidebar">
            <div class="summary-card">
                <h3>Ringkasan Biaya</h3>

                <div class="summary-item">
                    <span>Sewa Ruangan (<?php echo count($hours); ?> Jam)</span>
                    <strong><?php echo "Rp " . number_format($total_price, 0, ',', '.'); ?> </strong>
                </div>
                <div class="summary-item">
                    <span>Biaya admin</span>
                    <strong><?php echo "Rp " . number_format($admin_fee, 0, ',', '.'); ?></strong>
                </div>
                <div class="summary-item">
                    <span>PPN 11%</span>
                    <strong><?php echo "Rp " . number_format($taxes, 0, ',', '.'); ?></strong>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-total">
                    <span>Total Pembayaran</span>
                    <strong><?php echo "Rp " . number_format($final_price, 0, ',', '.'); ?></strong>
                </div>

                <div class="info-box">
                    <span class="icon">✓</span> Saya menyetujui Syarat & Ketentuan serta Kebijakan Privasi yang berlaku
                </div>

                <button type="button" class="btn-primary" id="btnLanjutPembayaran">
                    Lanjut Ke Pembayaran
                </button>

                <div class="transaction-notice">
                    🔒 Transaksi aman dan terenkripsi
                </div>
            </div>
        </div>
    </div>

    <script>
        // sidebar button submits the form
        document.getElementById('btnLanjutPembayaran').addEventListener('click', function() {
            document.getElementById('bookingForm').submit();
        });
    </script>
</body>

</html>