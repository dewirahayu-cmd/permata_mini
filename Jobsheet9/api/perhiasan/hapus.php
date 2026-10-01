<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

// Sengaja hanya menerima POST — mencegah delete terpicu tanpa sengaja
// lewat link/preview crawler.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$kode = $_POST['kode'] ?? null;
if ($kode) {
    // Perhiasan yang masih dipinjam tidak boleh dihapus
    $cek = $pdo->prepare("SELECT COUNT(*) FROM peminjam WHERE id_perhiasan = :kode AND status IN ('Aktif', 'Terlambat')");
    $cek->execute(['kode' => $kode]);
    if ((int) $cek->fetchColumn() > 0) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Perhiasan masih dipinjam, tidak bisa dihapus.'];
    } else {
        $stmt = $pdo->prepare("DELETE FROM perhiasan WHERE kode = :kode");
        $stmt->execute(['kode' => $kode]);
        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Perhiasan berhasil dihapus.'];
    }
}

header('Location: list.php');
exit;