<?php
$page_title = "Beranda";
$activePage = "beranda";
$show_full_footer = true;
include __DIR__ . '/includes/header.php';

$daftarPerhiasan = $_SESSION['perhiasan'] ?? [];
$daftarPeminjam  = $_SESSION['peminjam'] ?? [];

$totalKoleksi = count($daftarPerhiasan);
$totalPeminjam = count($daftarPeminjam);

$aktivitasAktif = 0;
foreach ($daftarPerhiasan as $p) {
    if ($p['status'] === 'Dipinjam') $aktivitasAktif++;
}

$perhatianVault = 0;
foreach ($daftarPerhiasan as $p) {
    if ($p['status'] === 'Terlambat') $perhatianVault++;
}
foreach ($daftarPeminjam as $pm) {
    if ($pm['status'] === 'Terlambat') $perhatianVault++;
}
?>
    <div class="container">
        <section class="hero">
            <div class="hero-grid">
                <div>
                    <span class="hero-badge">‹ Koleksi Eksklusif & Tata Kelola Vault</span>
                    <h1>Selamat Datang di Sistem Peminjaman Perhiasan Mini</h1>
                    <p>Aplikasi modern untuk mengelola peminjaman koleksi mahakarya berlian, emas murni, dan mutiara bernilai tinggi dengan standar kurasi kelas dunia.</p>
                    <div class="hero-actions">
                        <a href="perhiasan/list.php" class="btn btn-gold">💎 Lihat Katalog Koleksi</a>
                        <a href="peminjam/tambah.php" class="btn" style="background:rgba(59,13,24,0.5);color:#d7c1c3;border:1px solid rgba(212,175,55,0.3);">👤 Registrasi Peminjaman Baru</a>
                    </div>
                </div>
                <div class="hero-photo-card">
                    <div class="photo-wrap">
                        <img src="<?php echo $base; ?>assets\img\foto_login.jpg" alt="Royal Pearl & Marquise Diamond Collier">
                        <div class="hero-photo-caption">
                            <span class="tag">Curated High Jewel</span>
                            <h3>Royal Pearl & Marquise Diamond Collier</h3>
                        </div>
                    </div>
                    <div class="hero-photo-foot"><span>★ Mahakarya Utama</span><span>Vault Ref. PM-001</span></div>
                </div>
            </div>
        </section>

        <div class="section-title-row">
            <h2>📊 Ringkasan Hari Ini</h2>
            <span class="sync-note">Data Langsung dari Sesi Aktif</span>
        </div>
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-top"><span class="label">Total Koleksi</span><div class="stat-icon-box">💎</div></div>
                <div><div class="stat-value"><?php echo $totalKoleksi; ?></div><div class="stat-sub">📦 Tercatat di vault saat ini</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-top"><span class="label">Peminjam</span><div class="stat-icon-box">👥</div></div>
                <div><div class="stat-value"><?php echo $totalPeminjam; ?></div><div class="stat-sub">✔️ Nasabah VIP & Prioritas</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-top"><span class="label">Aktivitas</span><div class="stat-icon-box">🔑</div></div>
                <div><div class="stat-value"><?php echo $aktivitasAktif; ?></div><div class="stat-sub">⏳ Dalam masa sewa aktif</div></div>
            </div>
            <div class="stat-card warn">
                <div class="stat-top"><span class="label">Perhatian Vault</span><div class="stat-icon-box">🔔</div></div>
                <div><div class="stat-value"><?php echo $perhatianVault; ?></div><div class="stat-sub">‼️ Memerlukan konfirmasi</div></div>
            </div>
        </div>

        <div class="info-panel">
            <span class="hero-badge" style="background:var(--pink-bg-2);color:var(--warn-text);border:none;">🛡️ Standar Kepercayaan & Integritas</span>
            <h2 style="margin-top:0.8rem;">Tentang Peminjaman Perhiasan Mini</h2>
            <p>Permata Mini hadir sebagai platform concierge terpercaya untuk tata kelola peminjaman perhiasan mewah secara aman, transparan, dan terasuransi penuh.</p>
            <div class="info-grid">
                <div class="info-item"><div class="ic-box">✅</div><div><h4>Status Tersedia <span class="pill-status ok">TERSEDIA</span></h4><p>Perhiasan tersimpan aman di vault dengan sertifikat otentisitas GIA.</p></div></div>
                <div class="info-item"><div class="ic-box">🔒</div><div><h4>Status Dipinjam <span class="pill-status warn">DIPINJAM</span></h4><p>Tercatat resmi dalam kontrak legal pinjam-pakai dengan asuransi komprehensif.</p></div></div>
                <div class="info-item"><div class="ic-box">⚠️</div><div><h4>Status Terlambat <span class="pill-status bad">TERLAMBAT</span></h4><p>Sistem pengingat otomatis via WhatsApp Concierge.</p></div></div>
                <div class="info-item"><div class="ic-box">🧼</div><div><h4>Layanan Kurasi & Inspeksi</h4><p>Pembersihan ultrasonik dan evaluasi mikroskopik berkala.</p></div></div>
            </div>
        </div>
    </div>
<?php include __DIR__ . '/includes/footer.php'; ?>
