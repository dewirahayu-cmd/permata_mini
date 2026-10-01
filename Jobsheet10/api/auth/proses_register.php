<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];
if ($nama === '') $errors[] = "Nama wajib diisi.";
if ($username === '') $errors[] = "Username wajib diisi.";
if (strlen($password) < 6) $errors[] = "Password minimal 6 karakter.";

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: register.php');
    exit;
}

$cek = $pdo->prepare("SELECT id FROM users WHERE username = :username");
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

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Registrasi berhasil, silakan login.'];
header('Location: login.php');
exit;