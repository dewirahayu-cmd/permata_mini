<?php
session_start();
$host = $_SERVER['HTTP_HOST'] ?? '';

if (strpos($host, 'vercel.app') !== false) {
    $base = '/';
} else {
    $__jobsheetRoot = dirname(__DIR__);
    $__scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
    $__rel = ltrim(str_replace('\\', '/', substr($__scriptDir, strlen($__jobsheetRoot))), '/');
    $base = $__rel === '' ? '' : str_repeat('../', substr_count($__rel, '/') + 1);
}
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