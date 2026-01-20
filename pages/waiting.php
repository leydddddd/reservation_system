<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Pembayaran - Tekape Workspace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="waiting-container">
        <div class="waiting-card">
            <div class="waiting-header">
                <div class="avatar-icon">👨</div>
                <h2>Menunggu Pembayaran</h2>
                <p>Silakan selesaikan pembayaran Anda</p>
                <div class="booking-id">Booking ID: <strong id="bookingId">VFBP-2025-001234</strong></div>
            </div>

            <div class="timer-box-large">
                <p>Waktu Mulai Pembayaran</p>
                <div class="countdown-large" id="countdownLarge">23:59:42</div>
            </div>

            <div class="booking-details">
                <h3>📋 Detail Pemesanan</h3>
                
                <div class="detail-row">
                    <span>Nama Pemesan</span>
                    <strong id="displayNama">Ahmad Fauzl</strong>
                </div>
                <div class="detail-row">
                    <span>Email</span>
                    <strong id="displayEmail">ahmad.fauzl@email.com</strong>
                </div>
                <div class="detail-row">
                    <span>No. Telepon</span>
                    <strong id="displayTelepon">+62 812-3456-7890</strong>
                </div>
                <div class="detail-row">
                    <span>Ruangan</span>
                    <strong id="displayRuangan" class="link">Meeting Room A</strong>
                </div>
                <div class="detail-row">
                    <span>Lokasi</span>
                    <strong id="displayLokasi">Jl. Lodaya No. 13, Bandung</strong>
                </div>
                <div class="detail-row">
                    <span>Tanggal Booking</span>
                    <strong id="displayTanggalDetail">10 November 2025</strong>
                </div>
                <div class="detail-row">
                    <span>Waktu</span>
                    <strong id="displayWaktuDetail">09:00 - 12:00 (3 Jam)</strong>
                </div>
                <div class="detail-row">
                    <span>Kapasitas</span>
                    <strong id="displayKapasitas">6 Orang</strong>
                </div>

                <div class="price-summary">
                    <div class="price-item">
                        <span>Harga per Jam</span>
                        <strong>Rp 0</strong>
                    </div>
                    <div class="price-item">
                        <span>Durasi</span>
                        <strong>3 Jam</strong>
                    </div>
                    <div class="price-item">
                        <span>Layanan Lainnya</span>
                        <strong>Rp 0</strong>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="price-item total">
                        <span>Total Pembayaran</span>
                        <strong>Rp2151,000</strong>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn-primary" id="btnLanjutPembayaran">
                        ✓ Lanjut Pembayaran
                    </button>
                    <button class="btn-danger" id="btnBatalkanPesanan">
                        ✕ Batalkan Pesanan
                    </button>
                </div>

                <div class="warning-box">
                    <span class="icon">⚠️</span>
                    <p><strong>Penting:</strong> Harap selesaikan pembayaran sebelum waktu habis. Jika tidak, reservasi Anda akan otomatis dibatalkan dan ruangan akan tersedia untuk orang lain.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>