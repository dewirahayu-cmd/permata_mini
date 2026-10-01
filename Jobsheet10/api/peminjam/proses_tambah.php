<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Hanya user login (atau admin) yang boleh mengajukan peminjaman
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Silakan login atau daftar akun terlebih dahulu untuk mengajukan peminjaman.'];
    header('Location: ../auth/login.php');
    exit;
}
require __DIR__ . '/../includes/koneksi.php';

$isAdmin = (($_SESSION['role'] ?? '') === 'admin');
// Pengajuan user ditautkan ke akunnya (untuk riwayat). Data buatan admin tidak terikat akun.
$userId = $isAdmin ? null : (int) $_SESSION['user_id'];

// Kode & tier hanya boleh ditentukan admin. User: kode otomatis, tier otomatis.
$kode = $isAdmin ? trim($_POST['kode'] ?? '') : '';
$tier = $isAdmin ? trim($_POST['tier'] ?? '') : '';

$nama = trim($_POST['nama'] ?? '');
$nik = trim($_POST['nik'] ?? '');
$telepon = trim($_POST['telepon'] ?? '');
$email = trim($_POST['email'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$agunan = $_POST['agunan'] ?? '0';
$jenisAgunan = trim($_POST['jenis_agunan'] ?? '');
$idPerhiasan = trim($_POST['id_perhiasan'] ?? '');
$jatuhTempo = trim($_POST['jatuh_tempo'] ?? '');

$errors = [];
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

// User biasa wajib memilih perhiasan (pengajuan tanpa perhiasan tidak ada artinya)
if (!$isAdmin && $idPerhiasan === '') {
    $errors[] = "Pilih perhiasan yang ingin dipinjam.";
}

// Aturan khusus user: tidak boleh punya pinjaman terlambat & tidak boleh meminjam perhiasan yang sama dua kali
if (!$isAdmin) {
    $c = $pdo->prepare("SELECT COUNT(*) FROM peminjam WHERE user_id = :u AND status = 'Terlambat'");
    $c->execute(['u' => $userId]);
    if ((int) $c->fetchColumn() > 0) {
        $errors[] = "Anda masih memiliki pinjaman yang terlambat. Selesaikan terlebih dahulu sebelum meminjam lagi.";
    }
    if ($idPerhiasan !== '') {
        $c = $pdo->prepare("SELECT COUNT(*) FROM peminjam WHERE user_id = :u AND id_perhiasan = :k AND status IN ('Aktif', 'Terlambat')");
        $c->execute(['u' => $userId, 'k' => $idPerhiasan]);
        if ((int) $c->fetchColumn() > 0) {
            $errors[] = "Anda masih meminjam perhiasan ini.";
        }
    }
}

// Perhiasan yang dipinjam. Nama diambil dari database, bukan dari input form.
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

    if ($isAdmin) {
        if ($jatuhTempo === '' || $jatuhTempo === '-') {
            $errors[] = "Jadwal jatuh tempo wajib diisi jika meminjam perhiasan.";
        }
    } else {
        // Jatuh tempo user dihitung sistem dari lama peminjaman yang dipilih
        $lama = (int) ($_POST['lama_hari'] ?? 7);
        if (!in_array($lama, [3, 7, 14, 30], true)) {
            $lama = 7;
        }
        $jatuhTempo = tanggalJatuhTempo($lama);
    }
} else {
    $idPerhiasan = '-';
    $jatuhTempo = '-';
}

// Buat kode otomatis (BRW-001, BRW-002, ...) bila kosong
if (empty($errors) && $kode === '') {
    $max = $pdo->query("SELECT COALESCE(MAX(CAST(SUBSTRING(kode FROM '[0-9]+$') AS INTEGER)), 0) FROM peminjam WHERE kode LIKE 'BRW-%'")->fetchColumn();
    $kode = sprintf('BRW-%03d', ((int) $max) + 1);
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
        "INSERT INTO peminjam (kode, nama, inisial, telepon, nik, email, tier, alamat, perhiasan, id_perhiasan, jatuh_tempo, status, agunan, jenis_agunan, foto_ktp, bukti_jaminan, user_id)
         VALUES (:kode, :nama, :inisial, :telepon, :nik, :email, :tier, :alamat, :perhiasan, :id_perhiasan, :jatuh_tempo, :status, :agunan, :jenis_agunan, :foto_ktp, :bukti_jaminan, :user_id)
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
        'user_id' => $userId,
    ]);
} catch (PDOException $e) {
    if ($fileKtp) @unlink($folderUpload . '/' . $fileKtp);
    if ($fileJaminan) @unlink($folderUpload . '/' . $fileJaminan);
    $pesan = $isAdmin
        ? "Kode peminjam \"$kode\" sudah digunakan."
        : "Pengajuan gagal diproses, silakan coba lagi.";
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => $pesan];
    header('Location: tambah.php');
    exit;
}

sinkronPerhiasan($pdo, $idPerhiasan);

if ($isAdmin) {
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data peminjam berhasil disimpan.'];
    header('Location: list.php');
} else {
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => "Pengajuan berhasil dikirim dengan kode $kode. Jatuh tempo: $jatuhTempo. Petugas vault akan menghubungi Anda untuk verifikasi."];
    header('Location: riwayat.php');
}
exit;