<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Belum login -> ke halaman login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Sudah login tapi bukan admin -> kembali ke beranda
if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}