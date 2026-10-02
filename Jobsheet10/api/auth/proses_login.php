<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

// Hanya menerima POST dari form login
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username dan password wajib diisi.'];
    header('Location: login.php');
    exit;
}

try {
    // Username tidak membedakan huruf besar/kecil
    $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(username) = LOWER(:username) ORDER BY id LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Login error: ' . $e->getMessage());
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Terjadi gangguan pada server. Silakan coba lagi.'];
    header('Location: login.php');
    exit;
}

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