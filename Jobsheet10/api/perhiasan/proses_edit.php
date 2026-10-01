<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';


$kode = trim($_POST['kode'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$kategori = trim($_POST['kategori'] ?? '');
$material = trim($_POST['material'] ?? '');
$stokTotal = $_POST['stok_total'] ?? '';
$nilai = $_POST['nilai'] ?? '';

if ($kode === '') {
    header('Location: list.php');
    exit;
}

$errors = [];
if ($nama === '') $errors[] = "Nama koleksi wajib diisi.";
if (!is_numeric($stokTotal) || $stokTotal < 0) $errors[] = "Total stok harus angka dan tidak boleh negatif.";
if (!is_numeric($nilai) || $nilai <= 0) $errors[] = "Nilai aset harus lebih dari 0.";

// Total stok tidak boleh lebih kecil dari jumlah yang sedang dipinjam
if (empty($errors)) {
    $hit = $pdo->prepare("SELECT COUNT(*) FROM peminjam WHERE id_perhiasan = :k AND status IN ('Aktif', 'Terlambat')");
    $hit->execute(['k' => $kode]);
    $dipinjam = (int) $hit->fetchColumn();
    if ((int) $stokTotal < $dipinjam) {
        $errors[] = "Total stok tidak boleh kurang dari jumlah yang sedang dipinjam ($dipinjam).";
    }
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: edit.php?kode=' . urlencode($kode));
    exit;
}

try {
    $stmt = $pdo->prepare(
        "UPDATE perhiasan SET nama = :nama, kategori = :kategori, material = :material,
         stok_total = :stok_total, nilai = :nilai WHERE kode = :kode"
    );
    $stmt->execute([
        'nama' => $nama,
        'kategori' => $kategori,
        'material' => $material,
        'stok_total' => (int) $stokTotal,
        'nilai' => (int) $nilai,
        'kode' => $kode,
    ]);

    // Nama perhiasan di data peminjam ikut diperbarui
    $upd = $pdo->prepare("UPDATE peminjam SET perhiasan = :nama WHERE id_perhiasan = :kode");
    $upd->execute(['nama' => $nama, 'kode' => $kode]);
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => "Data perhiasan \"$kode\" gagal diperbarui."];
    header('Location: edit.php?kode=' . urlencode($kode));
    exit;
}

// Stok tersedia & status dihitung ulang dari total stok yang baru
sinkronPerhiasan($pdo, $kode);

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Perhiasan berhasil diperbarui.'];
header('Location: list.php');
exit;