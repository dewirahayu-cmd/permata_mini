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
$idPerhiasan = trim($_POST['id_perhiasan'] ?? '');
$jatuhTempo = trim($_POST['jatuh_tempo'] ?? '');

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
if ($agunan === '' || !is_numeric($agunan) || $agunan < 0) {
    $agunan = 0;
}

// Perhiasan yang dipinjam (opsional). Nama diambil dari database, bukan dari input form.
$namaPerhiasan = '-';
$status = 'Bebas';
if ($idPerhiasan !== '') {
    $cek = $pdo->prepare("SELECT nama, stok FROM perhiasan WHERE kode = :k");
    $cek->execute(['k' => $idPerhiasan]);
    $p = $cek->fetch(PDO::FETCH_ASSOC);
    if (!$p) {
        $errors[] = "Kode perhiasan \"$idPerhiasan\" tidak ditemukan.";
    } elseif ((int) $p['stok'] <= 0) {
        $errors[] = "Perhiasan \"{$p['nama']}\" sedang tidak tersedia (stok habis).";
    } else {
        $namaPerhiasan = $p['nama'];
        $status = 'Aktif';
    }
    if ($jatuhTempo === '' || $jatuhTempo === '-') {
        $errors[] = "Jadwal jatuh tempo wajib diisi jika meminjam perhiasan.";
    }
} else {
    $idPerhiasan = '-';
    $jatuhTempo = '-';
}

// Upload berkas KTP & bukti jaminan (opsional)
$folderUpload = __DIR__ . '/../assets/uploads/peminjam';
$ekstensi = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
$fileKtp = null;
$fileJaminan = null;
if (empty($errors)) {
    list($fileKtp, $errKtp) = uploadBerkas('berkas_ktp', $folderUpload, 'ktp_' . $kode, $ekstensi);
    if ($errKtp) $errors[] = "Berkas KTP: " . $errKtp;
    list($fileJaminan, $errJaminan) = uploadBerkas('berkas_jaminan', $folderUpload, 'jaminan_' . $kode, $ekstensi);
    if ($errJaminan) $errors[] = "Bukti jaminan: " . $errJaminan;
}

if (!empty($errors)) {
    // Hapus berkas yang sempat tersimpan bila ada error lain
    if ($fileKtp) @unlink($folderUpload . '/' . $fileKtp);
    if ($fileJaminan) @unlink($folderUpload . '/' . $fileJaminan);
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO peminjam (kode, nama, inisial, telepon, nik, email, tier, alamat, perhiasan, id_perhiasan, jatuh_tempo, status, agunan, jenis_agunan, foto_ktp, bukti_jaminan)
         VALUES (:kode, :nama, :inisial, :telepon, :nik, :email, :tier, :alamat, :perhiasan, :id_perhiasan, :jatuh_tempo, :status, :agunan, :jenis_agunan, :foto_ktp, :bukti_jaminan)
         RETURNING kode"
    );
    $stmt->execute([
        'kode' => $kode,
        'nama' => $nama,
        'inisial' => buatInisial($nama),
        'telepon' => $telepon,
        'nik' => $nik,
        'email' => $email,
        'tier' => $tier !== '' ? $tier : 'Verified VIP',
        'alamat' => $alamat,
        'perhiasan' => $namaPerhiasan,
        'id_perhiasan' => $idPerhiasan,
        'jatuh_tempo' => $jatuhTempo,
        'status' => $status,
        'agunan' => (int) $agunan,
        'jenis_agunan' => $jenisAgunan !== '' ? $jenisAgunan : 'Belum ada agunan',
        'foto_ktp' => $fileKtp ?: '-',
        'bukti_jaminan' => $fileJaminan ?: '-',
    ]);
} catch (PDOException $e) {
    if ($fileKtp) @unlink($folderUpload . '/' . $fileKtp);
    if ($fileJaminan) @unlink($folderUpload . '/' . $fileJaminan);
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => "Kode peminjam \"$kode\" sudah digunakan."];
    header('Location: tambah.php');
    exit;
}

sinkronPerhiasan($pdo, $idPerhiasan);

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data peminjam berhasil disimpan.'];
header('Location: list.php');
exit;