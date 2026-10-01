<?php
$page_title = "Daftar Perhiasan";
$activePage = "perhiasan-list";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$perPage = 8;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $hitung = $pdo->prepare("SELECT COUNT(*) FROM perhiasan WHERE nama ILIKE :kw OR kode ILIKE :kw");
    $hitung->execute(['kw' => '%' . $keyword . '%']);
    $totalRows = $hitung->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM perhiasan WHERE nama ILIKE :kw OR kode ILIKE :kw ORDER BY kode DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue('kw', '%' . $keyword . '%');
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM perhiasan")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM perhiasan ORDER BY kode DESC LIMIT :limit OFFSET :offset");
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$daftarPerhiasan = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));

$totalItem = (int) $pdo->query("SELECT COUNT(*) FROM perhiasan")->fetchColumn();
$tersediaCount = (int) $pdo->query("SELECT COUNT(*) FROM perhiasan WHERE status = 'Tersedia'")->fetchColumn();
$dipinjamCount = (int) $pdo->query("SELECT COUNT(*) FROM perhiasan WHERE status = 'Dipinjam'")->fetchColumn();
$terlambatCount = (int) $pdo->query("SELECT COUNT(*) FROM perhiasan WHERE status = 'Terlambat'")->fetchColumn();
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
            <form method="get" action="list.php" class="search-box" style="display:flex;gap:0.5rem;align-items:center;max-width:none;flex:1;">
                <span class="ic" style="position:static;">🔍</span>
                <input type="text" name="q" value="<?php echo $keyword; ?>" placeholder="Cari nama perhiasan atau kode...">
                <button type="submit" class="btn btn-maroon btn-sm">Cari</button>
            </form>
        </div>

        <div class="product-grid" id="grid-perhiasan">
            <?php if (empty($daftarPerhiasan)): ?>
                <p style="text-align:center;padding:2rem;color:var(--ink-mute);grid-column:1/-1;">Tidak ada data perhiasan yang cocok.</p>
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
                            <a href="edit.php?kode=<?php echo $item['kode']; ?>" class="icon-btn btn-edit" title="Edit">✏️</a>
                            <form class="form-hapus" method="post" action="hapus.php" style="display:inline;">
                                <input type="hidden" name="kode" value="<?php echo $item['kode']; ?>">
                                <button type="submit" class="icon-btn danger btn-hapus" title="Hapus">🗑️</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>

<?php if ($totalPages > 1): ?>
        <nav class="pagination" style="justify-content: center; gap: 10px;">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="list.php?page=<?php echo $i; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>"
               class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>