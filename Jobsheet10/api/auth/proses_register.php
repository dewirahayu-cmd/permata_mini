<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];
if ($nama === '') $errors[] = "Nama wajib diisi.";
if (strlen($nama) > 100) $errors[] = "Nama maksimal 100 karakter.";
if (!preg_match('/^[A-Za-z0-9_.\-]{3,30}$/', $username)) $errors[] = "Username 3-30 karakter: huruf, angka, titik, garis bawah, atau strip.";
if (strlen($password) < 6) $errors[] = "Password minimal 6 karakter.";
if (strlen($password) > 72) $errors[] = "Password maksimal 72 karakter.";

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: register.php');
    exit;
}

try {
    // Pengecekan tidak membedakan huruf besar/kecil (mencegah "Admin" meniru "admin")
    $cek = $pdo->prepare("SELECT id FROM users WHERE LOWER(username) = LOWER(:username)");
    $cek->execute(['username' => $username]);
    if ($cek->fetch()) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username sudah digunakan.'];
        header('Location: register.php');
        exit;
    }

    // Registrasi publik SELALU menghasilkan role 'user'.
    // Role 'admin' hanya boleh diberikan manual oleh pemilik sistem lewat database.
    $stmt = $pdo->prepare(
        "INSERT INTO users (nama, username, password, role) VALUES (:nama, :username, :password, 'user')"
    );
    $stmt->execute([
        'nama' => $nama,
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);
} catch (PDOException $e) {
    // 23505 = pelanggaran UNIQUE (dua orang mendaftar dengan username sama bersamaan)
    if ($e->getCode() === '23505') {
        $pesan = 'Username sudah digunakan.';
    } else {
        error_log('Register error: ' . $e->getMessage());
        $pesan = 'Terjadi gangguan pada server. Silakan coba lagi.';
    }
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => $pesan];
    header('Location: register.php');
    exit;
}

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Registrasi berhasil, silakan login.'];
header('Location: login.php');
exit;