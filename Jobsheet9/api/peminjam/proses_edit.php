<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$kode = trim($_POST['kode'] ?? '');
if ($kode === '') {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM peminjam WHERE kode = :kode");
$stmt->execute(['kode' => $kode]);
$lama = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$lama) {
    header('Location: list.php');
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$telepon = trim($_POST['telepon'] ?? '');
$email = trim($_POST['email'] ?? '');
$tier = trim($_POST['tier'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$idPerhiasan = trim($_POST['id_perhiasan'] ?? '');
$jatuhTempo = trim($_POST['jatuh_tempo'] ?? '');
$status = trim($_POST['status'] ?? 'Bebas');
$agunan = $_POST['agunan'] ?? '0';
$jenisAgunan = trim($_POST['jenis_agunan'] ?? '');

$errors = [];
if ($nama === '') $errors[] = "Nama wajib diisi.";
if ($telepon === '') $errors[] = "Telepon wajib diisi.";
if ($alamat === '') $errors[] = "Alamat wajib diisi.";
if (!in_array($status, ['Aktif', 'Terlambat', 'Bebas'], true)) $errors[] = "Status tidak valid.";
if ($agunan === '' || !is_numeric($agunan) || $agunan < 0) $agunan = 0;

$namaPerhiasan = '-';
if ($idPerhiasan === '' || $idPerhiasan === '-') {
    // Tidak meminjam apa pun -> harus Bebas
    $idPerhiasan = '-';
    $jatuhTempo = '-';
    if ($status !== 'Bebas') {
        $errors[] = "Status Aktif/Terlambat hanya untuk peminjam yang meminjam perhiasan. Isi kode perhiasan atau ubah status ke Bebas.";
    }
} else {
    if ($status === 'Bebas') {
        $errors[] = "Peminjam yang sedang meminjam perhiasan tidak boleh berstatus Bebas.";
    }
    if ($jatuhTempo === '' || $jatuhTempo === '-') {
        $errors[] = "Jadwal jatuh tempo wajib diisi jika meminjam perhiasan.";
    }
    $cek = $pdo->prepare("SELECT nama, stok FROM perhiasan WHERE kode = :k");
    $cek->execute(['k' => $idPerhiasan]);
    $p = $cek->fetch(PDO::FETCH_ASSOC);
    if (!$p) {
        $errors[] = "Kode perhiasan \"$idPerhiasan\" tidak ditemukan.";
    } elseif ($idPerhiasan !== $lama['id_perhiasan'] && (int) $p['stok'] <= 0) {
        $errors[] = "Perhiasan \"{$p['nama']}\" sedang tidak tersedia (stok habis).";
    } else {
        $namaPerhiasan = $p['nama'];
    }
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: edit.php?kode=' . urlencode($kode));
    exit;
}

// Inisial hanya dihitung ulang kalau nama berubah
$inisial = $nama !== $lama['nama'] ? buatInisial($nama) : $lama['inisial'];

try {
    $stmt = $pdo->prepare(
        "UPDATE peminjam SET nama = :nama, inisial = :inisial, telepon = :telepon, email = :email, tier = :tier,
         alamat = :alamat, perhiasan = :perhiasan, id_perhiasan = :id_perhiasan, jatuh_tempo = :jatuh_tempo,
         status = :status, agunan = :agunan, jenis_agunan = :jenis_agunan WHERE kode = :kode"
    );
    $stmt->execute([
        'nama' => $nama,
        'inisial' => $inisial,
        'telepon' => $telepon,
        'email' => $email,
        'tier' => $tier !== '' ? $tier : $lama['tier'],
        'alamat' => $alamat,
        'perhiasan' => $namaPerhiasan,
        'id_perhiasan' => $idPerhiasan,
        'jatuh_tempo' => $jatuhTempo,
        'status' => $status,
        'agunan' => (int) $agunan,
        'jenis_agunan' => $jenisAgunan !== '' ? $jenisAgunan : 'Belum ada agunan',
        'kode' => $kode,
    ]);
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => "Data peminjam \"$kode\" gagal diperbarui."];
    header('Location: edit.php?kode=' . urlencode($kode));
    exit;
}

// Perhiasan lama (jika diganti) dan baru sama-sama dihitung ulang stok & statusnya
sinkronPerhiasan($pdo, $lama['id_perhiasan']);
sinkronPerhiasan($pdo, $idPerhiasan);

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data peminjam berhasil diperbarui.'];
header('Location: list.php');
exit;