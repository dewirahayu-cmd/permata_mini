<?php
$page_title = "Daftar Perhiasan";
$activePage = "perhiasan-list";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarPerhiasan = $pdo->query("SELECT * FROM perhiasan ORDER BY kode DESC")->fetchAll(PDO::FETCH_ASSOC);

$totalItem = count($daftarPerhiasan);
$tersediaCount = 0;
$dipinjamCount = 0;
$terlambatCount = 0;
foreach ($daftarPerhiasan as $p) {
    if ($p['status'] === 'Tersedia') $tersediaCount++;
    elseif ($p['status'] === 'Dipinjam') $dipinjamCount++;
    elseif ($p['status'] === 'Terlambat') $terlambatCount++;
}
?>
    <div class="container">
        <div class="page-header">
            <div>
                <div class="page-eyebrow">✔️ Permata Mini · Katalog Koleksi</div>
                <h1>Daftar Perhiasan</h1>
                <p>Katalog perhiasan mahakarya berlian, mutiara, safir, dan emas bersertifikasi resmi brankas Permata Mini.</p>
            </div>
            <a href="tambah.php" class="btn btn-maroon">+ Tambah Perhiasan Baru</a>
        </div>

        <div class="stat-grid" style="margin-bottom:1.6rem;">
            <div class="stat-card"><div class="stat-top"><span class="label">Total Koleksi</span><div class="stat-icon-box">💎</div></div><div class="stat-value" style="font-size:1.6rem;"><?php echo $totalItem; ?> Item</div></div>
            <div class="stat-card"><div class="stat-top"><span class="label">Tersedia</span><div class="stat-icon-box">🔓</div></div><div class="stat-value" style="font-size:1.6rem;"><?php echo $tersediaCount; ?> Item</div></div>
            <div class="stat-card"><div class="stat-top"><span class="label">Dipinjam</span><div class="stat-icon-box">🔑</div></div><div class="stat-value" style="font-size:1.6rem;"><?php echo $dipinjamCount; ?> Item</div></div>
            <div class="stat-card warn"><div class="stat-top"><span class="label">Terlambat</span><div class="stat-icon-box">🔔</div></div><div class="stat-value" style="font-size:1.6rem;"><?php echo $terlambatCount; ?> Item</div></div>
        </div>

        <?php if ($flash): ?>
            <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
        <?php endif; ?>

        <div class="toolbar">
            <div class="search-box"><span class="ic">🔍</span><input type="text" id="search-input" placeholder="Cari nama perhiasan, kode, atau material..."></div>
            <a href="list.php" class="btn btn-outline btn-sm">🔄 Muat Ulang</a>
        </div>

        <div class="product-grid" id="grid-perhiasan">
            <?php if (empty($daftarPerhiasan)): ?>
                <p style="text-align:center;padding:2rem;color:var(--ink-mute);grid-column:1/-1;">Belum ada data perhiasan. Silakan tambah lewat menu "Tambah Perhiasan".</p>
            <?php else: foreach ($daftarPerhiasan as $item):
                $badge = $item['status'] === 'Tersedia' ? 'badge-success' : ($item['status'] === 'Dipinjam' ? 'badge-warning' : 'badge-danger');
                $stokTxt = !empty($item['stok_total']) ? $item['stok'] . ' / ' . $item['stok_total'] : $item['stok'];
            ?>
            <div class="product-card">
                <div class="product-photo">
                    <img src="<?php echo $base; ?>assets/img/<?php echo htmlspecialchars($item['foto']); ?>" alt="<?php echo htmlspecialchars($item['nama']); ?>">
                    <span class="product-code-tag"><?php echo htmlspecialchars($item['kode']); ?></span>
                    <span class="product-status-tag badge <?php echo $badge; ?>"><?php echo htmlspecialchars($item['status']); ?></span>
                </div>
                <div class="product-body">
                    <div class="product-name"><?php echo htmlspecialchars($item['nama']); ?></div>
                    <div class="product-meta"><?php echo htmlspecialchars($item['material']); ?></div>
                    <div class="product-stock-row"><span>Ketersediaan</span><strong>Stok: <?php echo $stokTxt; ?></strong></div>
                    <div class="product-price-row">
                        <div><div class="product-price-label">Nilai Aset / Asuransi</div><div class="product-price">Rp <?php echo number_format((float) $item['nilai'], 0, ',', '.'); ?></div></div>
                        <div class="product-actions">
                            <button type="button" class="icon-btn">✏️</button>
                            <button type="button" class="icon-btn danger btn-hapus">🗑️</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>