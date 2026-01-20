<?php
session_start();
require_once '../../api/connection.php';

$error = '';

// Jika sudah login
if (isset($_SESSION['admin_id'])) {
    header('Location: DashboardAdmin.php');
    exit;
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        // Prepared statement aman
        $stmt = $ineedthis->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();

        if ($admin) {

            $dbPass = $admin['password'];
            $valid = false;

            // Jika bcrypt (selalu panjang 60)
            if (strlen($dbPass) >= 60 && str_starts_with($dbPass, '$2y$')) {
                if (password_verify($password, $dbPass)) {
                    $valid = true;
                }
            } else {
                // Fallback: plain text
                if ($password === $dbPass) {
                    $valid = true;
                }
            }

            if ($valid) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: DashboardAdmin.php');
                exit;
            } else {
                $error = "Username atau password salah.";
            }
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Admin | Tekape Workspace</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0f172a, #1f2937);
            color: #f9fafb;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            display: flex;
            width: 900px;
            max-width: 95%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }

        .left {
            flex: 1;
            background: url('../../res/banner1.jpg') center/cover no-repeat;
            position: relative;
        }

        .left::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.6), rgba(249, 115, 22, 0.6));
        }

        .left-content {
            position: relative;
            z-index: 1;
            padding: 30px;
            color: #f9fafb;
        }

        .left-content h1 {
            margin: 0 0 10px;
            font-size: 28px;
        }

        .left-content p {
            margin: 0;
            opacity: 0.9;
        }

        .right {
            flex: 1;
            background-color: #111827;
            padding: 40px 35px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            gap: 10px;
        }

        .logo img {
            width: 40px;
            height: 40px;
        }

        .logo span {
            font-weight: bold;
            font-size: 20px;
        }

        h2 {
            margin: 0 0 10px;
            font-size: 22px;
        }

        .subtitle {
            margin-bottom: 20px;
            font-size: 14px;
            color: #9ca3af;
        }

        .error {
            background-color: #b91c1c;
            color: #fee2e2;
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #374151;
            background-color: #020617;
            color: #f9fafb;
            font-size: 14px;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 1px rgba(249, 115, 22, 0.4);
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 20px;
            color: #9ca3af;
        }

        .options a {
            color: #f97316;
            text-decoration: none;
        }

        .login-button {
            width: 100%;
            padding: 10px 14px;
            border-radius: 999px;
            border: none;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .login-button:hover {
            filter: brightness(1.05);
        }

        .footer-text {
            margin-top: 10px;
            font-size: 11px;
            color: #6b7280;
            text-align: center;
        }

        @media (max-width: 768px) {
            .left {
                display: none;
            }

            .right {
                flex: 1;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="left">
            <div class="left-content">
                <h1>Tekape Workspace</h1>
                <p>Admin panel untuk mengelola booking, ruangan, dan pengguna.</p>
            </div>
        </div>
        <div class="right">
            <div class="logo">
                <img src="../../res/tkp_logo2.png" alt="Logo">
                <span>Tekape Admin</span>
            </div>
            <h2>Login Admin</h2>
            <p class="subtitle">Masuk menggunakan akun admin yang terdaftar.</p>

            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username Admin</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username">
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password">
                </div>

                <div class="options">
                    <label><input type="checkbox" name="remember"> Ingat saya</label>
                    <a href="#">Lupa password?</a>
                </div>

                <button type="submit" name="login" class="login-button">Login Sekarang!</button>
            </form>

            <p class="footer-text">© <?php echo date('Y'); ?> Tekape Workspace. All rights reserved.</p>
        </div>
    </div>
    <?php
    ob_end_flush();
    ?>
</body>

</html>