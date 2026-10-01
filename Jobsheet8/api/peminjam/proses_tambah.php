<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$kode = trim($_POST['kode'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$nik = trim($_POST['nik'] ?? '');
$telepon = trim($_POST['telepon'] ?? '');
$email = trim($_POST['email'] ?? '');
$tier = trim($_POST['tier'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$agunan = $_POST['agunan'] ?? '0';
$jenisAgunan = trim($_POST['jenis_agunan'] ?? '');

$errors = [];
if ($kode === '') {
    $errors[] = "Kode peminjam wajib diisi.";
}
if ($nama === '') {
    $errors[] = "Nama lengkap wajib diisi.";
}
if ($nik === '') {
    $errors[] = "Nomor identitas wajib diisi.";
}
if ($telepon === '') {
    $errors[] = "Nomor telepon wajib diisi.";
}
if ($alamat === '') {
    $errors[] = "Alamat domisili wajib diisi.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

$inisial = strtoupper(substr($nama, 0, 2));

try {
    $stmt = $pdo->prepare(
        "INSERT INTO peminjam (kode, nama, inisial, telepon, nik, email, tier, alamat, perhiasan, id_perhiasan, jatuh_tempo, status, agunan, jenis_agunan)
         VALUES (:kode, :nama, :inisial, :telepon, :nik, :email, :tier, :alamat, :perhiasan, :id_perhiasan, :jatuh_tempo, :status, :agunan, :jenis_agunan)
         RETURNING id"
    );
    $stmt->execute([
        'kode' => $kode,
        'nama' => $nama,
        'inisial' => $inisial,
        'telepon' => $telepon,
        'nik' => $nik,
        'email' => $email,
        'tier' => $tier !== '' ? $tier : 'Verified VIP',
        'alamat' => $alamat,
        'perhiasan' => '-',
        'id_perhiasan' => '-',
        'jatuh_tempo' => '-',
        'status' => 'Bebas',
        'agunan' => (int) $agunan,
        'jenis_agunan' => $jenisAgunan !== '' ? $jenisAgunan : 'Belum ada agunan',
    ]);
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => "Kode peminjam \"$kode\" sudah digunakan."];
    header('Location: tambah.php');
    exit;
}

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data peminjam berhasil disimpan.'];
header('Location: list.php');
exit;