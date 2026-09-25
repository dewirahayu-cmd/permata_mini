<?php
session_start();

if (!isset($_SESSION['perhiasan'])) {
    $_SESSION['perhiasan'] = [
        ['kode' => 'P001', 'nama' => 'Kalung Sovereign', 'kategori' => 'Kalung', 'material' => 'Flowerish Rose Gold', 'stok' => 5, 'stok_total' => 5, 'nilai' => 72500000, 'status' => 'Tersedia', 'sertifikat' => 'Antam', 'foto' => 'menu_3.jpg', 'deskripsi' => ''],
        ['kode' => 'P002', 'nama' => 'Anting Colombia Mutiara Putri', 'kategori' => 'Anting', 'material' => 'Crown Tiara with Akoya Pearls & Diamonds', 'stok' => 0, 'stok_total' => 1, 'nilai' => 220000000, 'status' => 'Terlambat', 'sertifikat' => 'HRD', 'foto' => 'menu_8.jpg', 'deskripsi' => ''],
        ['kode' => 'P003', 'nama' => 'Anting Zamrud Kolombia Maharani', 'kategori' => 'Anting', 'material' => 'Colombian Emerald 6ct & Yellow Gold 18K', 'stok' => 1, 'stok_total' => 1, 'nilai' => 175000000, 'status' => 'Tersedia', 'sertifikat' => 'GIA', 'foto' => 'menu_4.jpg', 'deskripsi' => ''],
        ['kode' => 'P004', 'nama' => 'Anting Safir Royal Blue Burma', 'kategori' => 'Anting', 'material' => 'Natural Sapphire 4ct & Halo Diamonds', 'stok' => 2, 'stok_total' => 2, 'nilai' => 118000000, 'status' => 'Tersedia', 'sertifikat' => 'SSEF', 'foto' => 'menu_6.jpg', 'deskripsi' => ''],
        ['kode' => 'P005', 'nama' => 'Gelang Tennis Berlian Baguette', 'kategori' => 'Gelang', 'material' => 'White Gold 18K, 5.5ct Total Weight', 'stok' => 1, 'stok_total' => 3, 'nilai' => 95000000, 'status' => 'Dipinjam', 'sertifikat' => 'GIA', 'foto' => 'menu_5.jpg', 'deskripsi' => ''],
        ['kode' => 'P006', 'nama' => 'Cincin Taman Bunga Rose', 'kategori' => 'Cincin', 'material' => 'Floral Diamond Cluster & Rose Gold 18K', 'stok' => 3, 'stok_total' => 4, 'nilai' => 62000000, 'status' => 'Tersedia', 'sertifikat' => 'HRD', 'foto' => 'menu_9.jpg', 'deskripsi' => ''],
        ['kode' => 'P007', 'nama' => 'Cincin Berlian Solitaire Pavé', 'kategori' => 'Cincin', 'material' => 'Platinum 950, VVS1 Color D 2.5ct', 'stok' => 2, 'stok_total' => 2, 'nilai' => 145000000, 'status' => 'Tersedia', 'sertifikat' => 'GIA', 'foto' => 'menu_7.jpg', 'deskripsi' => ''],
        ['kode' => 'P008', 'nama' => 'Kalung Mutiara Royal Reine', 'kategori' => 'Kalung', 'material' => 'South Sea Pearl 14mm & White Gold 18K', 'stok' => 0, 'stok_total' => 1, 'nilai' => 85000000, 'status' => 'Dipinjam', 'sertifikat' => 'GIA', 'foto' => 'menu_1.jpg', 'deskripsi' => ''],
    ];
}

if (!isset($_SESSION['peminjam'])) {
    $_SESSION['peminjam'] = [
        ['kode' => 'BRW-009', 'nama' => 'Ny. Raden Ayu Kartika Dewi', 'inisial' => 'KD', 'telepon' => '+62 812-8877-2291', 'nik' => '-', 'email' => '-', 'tier' => 'VVIP Tier-1', 'alamat' => '-', 'perhiasan' => 'Kalung Royal Rose Solitaire', 'id_perhiasan' => 'J-SOL-018', 'jatuh_tempo' => '28 Okt 2025', 'status' => 'Aktif', 'agunan' => 120000000, 'jenis_agunan' => 'Bilyet Deposito BCA'],
        ['kode' => 'BRW-014', 'nama' => 'Dra. Fiona Wijaya, M.B.A.', 'inisial' => 'FW', 'telepon' => '+62 811-9234-5510', 'nik' => '-', 'email' => '-', 'tier' => 'Corporate VIP', 'alamat' => '-', 'perhiasan' => 'Tiara Emerald Grand Regency', 'id_perhiasan' => 'J-EMR-004', 'jatuh_tempo' => '25 Okt 2025', 'status' => 'Terlambat', 'agunan' => 195000000, 'jenis_agunan' => 'Sertifikat Hak Milik (SHM)'],
        ['kode' => 'BRW-003', 'nama' => 'Tn. Hendra Gunawan', 'inisial' => 'HG', 'telepon' => '+62 818-0909-1221', 'nik' => '-', 'email' => '-', 'tier' => 'Private Collector', 'alamat' => '-', 'perhiasan' => 'Jam Saku Emas Antik Patek 1912', 'id_perhiasan' => 'W-ANT-001', 'jatuh_tempo' => '31 Okt 2025', 'status' => 'Aktif', 'agunan' => 85000000, 'jenis_agunan' => 'Safe Deposit Lock 04'],
        ['kode' => 'BRW-021', 'nama' => 'Dr. Amanda Prameswari, Sp.A.', 'inisial' => 'AP', 'telepon' => '+62 856-4301-8900', 'nik' => '-', 'email' => '-', 'tier' => 'Verified VIP', 'alamat' => '-', 'perhiasan' => '-', 'id_perhiasan' => '-', 'jatuh_tempo' => '-', 'status' => 'Bebas', 'agunan' => 0, 'jenis_agunan' => 'Agunan Dirilis'],
    ];
}

$__jobsheetRoot = dirname(__DIR__);
$__scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
$__rel = ltrim(str_replace('\\', '/', substr($__scriptDir, strlen($__jobsheetRoot))), '/');
$base = $__rel === '' ? '' : str_repeat('../', substr_count($__rel, '/') + 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Permata Mini<?php echo isset($page_title) ? ' | ' . $page_title : ''; ?></title>
<link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
</head>
<body>
<header class="navbar">
    <div class="container nav-inner">
        <a class="brand-mark" href="<?php echo $base; ?>index.php">
            <span class="brand-icon"></span>
            <span class="brand-text"><span class="brand-name">Permata Mini</span><span class="brand-sub">Sistem Peminjaman Vault</span></span>
        </a>
        <button class="nav-toggle-btn" id="nav-toggle-btn"><span></span></button>
        <nav id="nav-menu">
            <ul>
                <li><a class="nav-link<?php echo (($activePage ?? '') === 'beranda') ? ' active' : ''; ?>" href="<?php echo $base; ?>index.php">Beranda</a></li>
                <li><a class="nav-link<?php echo (($activePage ?? '') === 'perhiasan-list') ? ' active' : ''; ?>" href="<?php echo $base; ?>perhiasan/list.php">Daftar Perhiasan</a></li>
                <li><a class="nav-link<?php echo (($activePage ?? '') === 'perhiasan-tambah') ? ' active' : ''; ?>" href="<?php echo $base; ?>perhiasan/tambah.php">Tambah Perhiasan</a></li>
                <li><a class="nav-link<?php echo (($activePage ?? '') === 'peminjam-list') ? ' active' : ''; ?>" href="<?php echo $base; ?>peminjam/list.php">Daftar Peminjam</a></li>
                <li><a class="nav-link<?php echo (($activePage ?? '') === 'peminjam-tambah') ? ' active' : ''; ?>" href="<?php echo $base; ?>peminjam/tambah.php">Tambah Peminjam</a></li>
            </ul>
        </nav>
        <div class="nav-right">
            <a class="vault-badge" href="<?php echo $base; ?>login.php">
                <span class="ic">🔑</span>
                <div class="txt"><b>VIP Vault Master</b><span>Hak Akses Penuh</span></div>
            </a>
            <div class="avatar-circle">👤</div>
        </div>
    </div>
</header>

<main>
