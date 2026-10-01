<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$kode = $_POST['kode'] ?? null;
if ($kode) {
    $cek = $pdo->prepare("SELECT id_perhiasan FROM peminjam WHERE kode = :kode");
    $cek->execute(['kode' => $kode]);
    $idPerhiasan = $cek->fetchColumn();

    $stmt = $pdo->prepare("DELETE FROM peminjam WHERE kode = :kode");
    $stmt->execute(['kode' => $kode]);

    // Perhiasan yang tadi dipinjam otomatis kembali tersedia
    if ($idPerhiasan) {
        sinkronPerhiasan($pdo, $idPerhiasan);
    }
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data peminjam berhasil dihapus.'];
}

header('Location: list.php');
exit;