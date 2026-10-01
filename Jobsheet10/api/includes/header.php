<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$sudahLogin = isset($_SESSION['user_id']);
$isAdmin = $sudahLogin && (($_SESSION['role'] ?? '') === 'admin');
$isUser = $sudahLogin && !$isAdmin;

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
                <?php if ($isAdmin): ?>
                <li><a class="nav-link<?php echo (($activePage ?? '') === 'perhiasan-tambah') ? ' active' : ''; ?>" href="<?php echo $base; ?>perhiasan/tambah.php">Tambah Perhiasan</a></li>
                <li><a class="nav-link<?php echo (($activePage ?? '') === 'peminjam-list') ? ' active' : ''; ?>" href="<?php echo $base; ?>peminjam/list.php">Daftar Peminjam</a></li>
                <li><a class="nav-link<?php echo (($activePage ?? '') === 'peminjam-tambah') ? ' active' : ''; ?>" href="<?php echo $base; ?>peminjam/tambah.php">Tambah Peminjam</a></li>
                <?php else: ?>
                <li><a class="nav-link<?php echo (($activePage ?? '') === 'peminjam-tambah') ? ' active' : ''; ?>" href="<?php echo $base; ?>peminjam/tambah.php">Ajukan Peminjaman</a></li>
                <?php endif; ?>
                <?php if ($isUser): ?>
                <li><a class="nav-link<?php echo (($activePage ?? '') === 'riwayat') ? ' active' : ''; ?>" href="<?php echo $base; ?>peminjam/riwayat.php">Riwayat Saya</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="nav-right">
            <?php if ($sudahLogin): ?>
                <div class="vault-badge" style="cursor:default;">
                    <span class="ic">🔑</span>
                    <div class="txt"><b><?php echo htmlspecialchars($_SESSION['nama']); ?></b><span><?php echo $isAdmin ? 'Hak Akses Admin' : 'Akun Pengguna'; ?></span></div>
                </div>
                <a href="<?php echo $base; ?>auth/logout.php" class="btn btn-outline btn-sm">Logout</a>
            <?php else: ?>
                <a class="vault-badge" href="<?php echo $base; ?>auth/login.php">
                    <span class="ic">🔑</span>
                    <div class="txt"><b>Login</b><span>Admin & Pengguna</span></div>
                </a>
                <a href="<?php echo $base; ?>auth/register.php" class="btn btn-outline btn-sm">Daftar</a>
            <?php endif; ?>
            <div class="avatar-circle">👤</div>
        </div>
    </div>
</header>

<main>