<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    // Ganti ID session setelah login (mencegah session fixation)
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];

    // Kembali ke halaman yang tadi dituju (mis. form pengajuan), atau ke beranda
    $tujuan = $_SESSION['redirect_after_login'] ?? '../index.php';
    unset($_SESSION['redirect_after_login']);
    header('Location: ' . $tujuan);
    exit;
}

// Pesan error sengaja tidak spesifik (username/password) — mencegah
// orang menebak username valid lewat perbedaan pesan error.
$_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username atau password salah.'];
header('Location: login.php');
exit;