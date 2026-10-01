<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$kode = trim($_POST['kode'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$kategori = trim($_POST['kategori'] ?? '');
$material = trim($_POST['material'] ?? '');
$stok = $_POST['stok'] ?? '';
$nilai = $_POST['nilai'] ?? '';
$deskripsi = trim($_POST['deskripsi'] ?? '');

$errors = [];
if ($kode === '') {
    $errors[] = "Kode perhiasan wajib diisi.";
}
if ($nama === '') {
    $errors[] = "Nama koleksi wajib diisi.";
}
if (!is_numeric($stok) || $stok < 0) {
    $errors[] = "Jumlah stok harus berupa angka dan tidak boleh negatif.";
}
if (!is_numeric($nilai) || $nilai <= 0) {
    $errors[] = "Nilai aset harus berupa angka lebih dari 0.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO perhiasan (kode, nama, kategori, material, stok, stok_total, nilai, status, sertifikat, foto, deskripsi)
         VALUES (:kode, :nama, :kategori, :material, :stok, :stok_total, :nilai, :status, :sertifikat, :foto, :deskripsi)
         RETURNING id"
    );
    $stmt->execute([
        'kode' => $kode,
        'nama' => $nama,
        'kategori' => $kategori !== '' ? $kategori : 'Lainnya',
        'material' => $material,
        'stok' => (int) $stok,
        'stok_total' => (int) $stok,
        'nilai' => (int) $nilai,
        'status' => 'Tersedia',
        'sertifikat' => '-',
        'foto' => 'menu_1.jpg',
        'deskripsi' => $deskripsi,
    ]);
} catch (PDOException $e) {

    $_SESSION['flash'] = ['type' => 'error', 'pesan' => "Kode perhiasan \"$kode\" sudah digunakan."];
    header('Location: tambah.php');
    exit;
}

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Perhiasan berhasil ditambahkan ke vault.'];
header('Location: list.php');
exit;