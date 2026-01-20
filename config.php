<?php
// config.php
// GANTI $dbUser dan $dbPass sesuai MySQL kamu

$host   = 'localhost';
$dbName = 'tekape_workspace';
$dbUser = 'root';      // default XAMPP
$dbPass = '';          // default XAMPP kosong, ganti kalau kamu pakai password

$dsn = "mysql:host=$host;dbname=$dbName;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}
