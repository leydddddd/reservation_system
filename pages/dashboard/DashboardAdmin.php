<?php
session_start();
require_once '../../config.php';

// Wajib login
if (!isset($_SESSION['admin_id'])) {
    header('Location: LoginAdmin.php');
    exit;
}

// --- Query data dashboard ---
// Total Bookings
$stmt = $pdo->query("SELECT COUNT(*) AS total_bookings FROM bookings");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totalBookings = (int) ($row['total_bookings'] ?? 0);

// Active Rooms
$stmt = $pdo->query("SELECT COUNT(*) AS total_rooms FROM rooms");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$activeRooms = (int) ($row['total_rooms'] ?? 0);

// Total Users
$stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totalUsers = (int) ($row['total_users'] ?? 0);

// Revenue
$stmt = $pdo->query("SELECT COALESCE(SUM(total_price), 0) AS revenue FROM bookings WHERE status = 'paid'");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$revenue = (float) ($row['revenue'] ?? 0);


// Nama admin
$adminName = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | Tekape Workspace</title>
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: radial-gradient(circle at top left, #1f2937, #020617);
            color: #e5e7eb;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            background: rgba(15, 23, 42, 0.95);
            border-bottom: 1px solid #1f2937;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-left img {
            width: 32px;
            height: 32px;
        }

        .topbar-title {
            font-weight: 600;
        }

        .topbar-subtitle {
            font-size: 11px;
            color: #9ca3af;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            position: relative;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: linear-gradient(135deg, #f97316, #fb923c);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #111827;
            cursor: pointer;
        }

        /* Tombol Toggle Mode */
        .theme-toggle {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: 0.25s;
        }

        .theme-toggle:hover {
            background: #4b5563;
        }

        /* Dropdown Logout */
        .profile-dropdown {
            position: absolute;
            top: 45px;
            right: 0;
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 10px;
            width: 140px;
            display: none;
            flex-direction: column;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.45);
            z-index: 20;
            overflow: hidden;
        }

        .profile-dropdown a {
            padding: 12px;
            color: #e5e7eb;
            text-decoration: none;
            font-size: 13px;
            border-bottom: 1px solid #374151;
        }

        .profile-dropdown a:hover {
            background: #ef4444;
            color: #fff;
        }

        .main {
            padding: 24px;
            max-width: 1100px;
            margin: 0 auto;
        }

        h1 {
            font-size: 22px;
            margin: 0 0 6px;
        }

        .breadcrumb {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 24px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .card {
            background: radial-gradient(circle at top left, #111827, #020617);
            border-radius: 16px;
            padding: 16px 18px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.7);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card .label {
            font-size: 13px;
            color: #9ca3af;
            margin-bottom: 4px;
        }

        .card .value {
            font-size: 22px;
            font-weight: 600;
        }

        .card .chip {
            margin-top: 4px;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 999px;
            background: rgba(22, 163, 74, 0.12);
            color: #4ade80;
        }

        .card-icon {
            font-size: 26px;
            opacity: 0.8;
        }

        .panel {
            margin-top: 10px;
            background: rgba(15, 23, 42, 0.9);
            border-radius: 16px;
            padding: 16px 18px;
            border: 1px solid rgba(75, 85, 99, 0.6);
        }

        .panel h2 {
            margin: 0 0 10px;
            font-size: 16px;
        }

        .panel p {
            margin: 0;
            font-size: 13px;
            color: #9ca3af;
        }

        /* ==================== LIGHT MODE ==================== */

        .light-mode {
            background: #f3f4f6 !important;
            color: #111 !important;
        }

        .light-mode .topbar {
            background: white !important;
            color: #111 !important;
            border-bottom: 1px solid #d1d5db !important;
        }

        .light-mode .card,
        .light-mode .panel {
            background: #ffffff !important;
            color: #111 !important;
            border-color: #d1d5db !important;
        }

        .light-mode .breadcrumb {
            color: #6b7280 !important;
        }

        .light-mode .profile-dropdown {
            background: white !important;
            border-color: #d1d5db !important;
        }

        .light-mode .profile-dropdown a {
            color: #111 !important;
        }

        .light-mode .theme-toggle {
            background: #e5e7eb !important;
        }

        .light-mode .theme-toggle:hover {
            background: #d1d5db !important;
        }
    </style>
</head>

<body>

    <div class="topbar">
        <div class="topbar-left">
            <img src="../../res/tkp_logo2.png" alt="Logo">
            <div>
                <div class="topbar-title">Tekape Admin</div>
                <div class="topbar-subtitle">Workspace Management Dashboard</div>
            </div>
        </div>

        <div class="topbar-right">
            <span>Halo, <?php echo htmlspecialchars($adminName); ?></span>

            <!-- Toggle Theme -->
            <div class="theme-toggle" id="themeToggle">🌙</div>

            <!-- Avatar -->
            <div class="avatar" id="avatarMenuBtn">
                <?php echo strtoupper(substr($adminName, 0, 1)); ?>
            </div>

            <!-- Dropdown Logout -->
            <div class="profile-dropdown" id="profileDropdown">
                <a href="Logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="main">
        <h1>Overview</h1>
        <div class="breadcrumb">Dashboard &gt; Summary</div>

        <div class="cards">
            <div class="card">
                <div>
                    <div class="label">Total Bookings</div>
                    <div class="value"><?php echo number_format($totalBookings); ?></div>
                    <div class="chip">📅 Semua transaksi booking</div>
                </div>
                <div class="card-icon">📘</div>
            </div>

            <div class="card">
                <div>
                    <div class="label">Active Rooms</div>
                    <div class="value"><?php echo number_format($activeRooms); ?></div>
                    <div class="chip">🏢 Ruangan terdaftar</div>
                </div>
                <div class="card-icon">🏬</div>
            </div>

            <div class="card">
                <div>
                    <div class="label">Total Users</div>
                    <div class="value"><?php echo number_format($totalUsers); ?></div>
                    <div class="chip">👥 Pengguna terdaftar</div>
                </div>
                <div class="card-icon">👤</div>
            </div>

            <div class="card">
                <div>
                    <div class="label">Revenue</div>
                    <div class="value">
                        Rp <?php echo number_format($revenue, 0, ',', '.'); ?>
                    </div>
                    <div class="chip">💰 Booking confirmed</div>
                </div>
                <div class="card-icon">💰</div>
            </div>
        </div>

        <div class="panel">
            <h2>Info Singkat</h2>
            <p>
                Data di atas diambil dari database
                <strong>tekape_workspace</strong>
                (tabel <code>bookings</code>, <code>rooms</code>, <code>users</code>).
            </p>
        </div>
    </div>

    <script>
        // Toggle dropdown logout
        const avatarBtn = document.getElementById('avatarMenuBtn');
        const dropdown = document.getElementById('profileDropdown');

        avatarBtn.addEventListener('click', () => {
            dropdown.style.display = dropdown.style.display === 'flex' ? 'none' : 'flex';
        });

        document.addEventListener('click', (e) => {
            if (!avatarBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        // === DARK / LIGHT MODE TOGGLE ===
        const themeToggle = document.getElementById('themeToggle');

        // Cek preferensi
        if (localStorage.getItem('theme') === 'light') {
            document.body.classList.add('light-mode');
            themeToggle.textContent = "☀️";
        }

        // Klik toggle
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('light-mode');

            if (document.body.classList.contains('light-mode')) {
                themeToggle.textContent = "☀️";
                localStorage.setItem('theme', 'light');
            } else {
                themeToggle.textContent = "🌙";
                localStorage.setItem('theme', 'dark');
            }
        });
    </script>

</body>

</html>