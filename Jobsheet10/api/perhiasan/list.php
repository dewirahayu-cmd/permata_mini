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

// Filter kategori (hanya nilai yang dikenal yang diterima)
$kategoriOpsi = ['Kalung', 'Cincin', 'Gelang', 'Anting', 'Bros', 'Tiara'];
$kategori = $_GET['kategori'] ?? '';
if (!in_array($kategori, $kategoriOpsi, true)) {
    $kategori = '';
}

$where = [];
$params = [];
if ($keyword !== '') {
    $where[] = "(nama ILIKE :kw OR kode ILIKE :kw)";
    $params['kw'] = '%' . $keyword . '%';
}
if ($kategori !== '') {
    $where[] = "kategori = :kat";
    $params['kat'] = $kategori;
}
$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$hitung = $pdo->prepare("SELECT COUNT(*) FROM perhiasan" . $sqlWhere);
$hitung->execute($params);
$totalRows = (int) $hitung->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM perhiasan" . $sqlWhere . " ORDER BY kode DESC LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
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

// Pembuat URL yang mempertahankan filter kategori & pencarian
$buatUrl = function ($pg, $kat) use ($keyword) {
    $q = [];
    if ($kat !== '') $q['kategori'] = $kat;
    if ($keyword !== '') $q['q'] = $keyword;
    if ($pg > 1) $q['page'] = $pg;
    return 'list.php' . ($q ? '?' . http_build_query($q) : '');
};

// $isAdmin, $isUser, $sudahLogin berasal dari header.php.
// Katalog terbuka untuk semua orang. Tombol kelola data hanya untuk admin.
?>
    <div class="container">
        <div class="page-header">
            <div>
                <div class="page-eyebrow">✔️ Permata Mini · Katalog Koleksi</div>
                <h1>Daftar Perhiasan</h1>
                <p>Katalog perhiasan mahakarya berlian, mutiara, safir, dan emas bersertifikasi resmi brankas Permata Mini.</p>
            </div>
            <?php if ($isAdmin): ?>
                <a href="tambah.php" class="btn btn-maroon">+ Tambah Perhiasan Baru</a>
            <?php endif; ?>
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

        <?php if (!$sudahLogin): ?>
            <p class="flash" style="background:var(--pink-bg-2);color:var(--maroon-900);">👁️ Anda melihat katalog sebagai Tamu. Untuk meminjam perhiasan, silakan <a href="<?php echo $base; ?>auth/login.php" style="font-weight:700;">Login</a> atau <a href="<?php echo $base; ?>auth/register.php" style="font-weight:700;">Daftar Akun</a> terlebih dahulu.</p>
        <?php elseif ($isUser): ?>
            <p class="flash" style="background:var(--pink-bg-2);color:var(--maroon-900);">💎 Ingin meminjam perhiasan? <a href="<?php echo $base; ?>peminjam/tambah.php" style="font-weight:700;">Ajukan peminjaman</a> atau lihat <a href="<?php echo $base; ?>peminjam/riwayat.php" style="font-weight:700;">riwayat peminjaman Anda</a>.</p>
        <?php endif; ?>

        <div class="toolbar">
            <form method="get" action="list.php" class="search-box" style="display:flex;gap:0.5rem;align-items:center;max-width:none;flex:1;">
                <span class="ic" style="position:static;">🔍</span>
                <?php if ($kategori !== ''): ?>
                    <input type="hidden" name="kategori" value="<?php echo htmlspecialchars($kategori); ?>">
                <?php endif; ?>
                <input type="text" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Cari nama perhiasan atau kode...">
                <button type="submit" class="btn btn-maroon btn-sm">Cari</button>
            </form>
        </div>

        <div class="filter-tabs" style="margin-bottom:1.4rem;">
            <a class="filter-tab<?php echo $kategori === '' ? ' active' : ''; ?>" style="display:inline-block;" href="<?php echo $buatUrl(1, ''); ?>">Semua</a>
            <?php foreach ($kategoriOpsi as $k): ?>
                <a class="filter-tab<?php echo $kategori === $k ? ' active' : ''; ?>" style="display:inline-block;" href="<?php echo $buatUrl(1, $k); ?>"><?php echo $k; ?></a>
            <?php endforeach; ?>
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
                        <?php if ($isAdmin): ?>
                        <div class="product-actions">
                            <a href="edit.php?kode=<?php echo urlencode($item['kode']); ?>" class="icon-btn btn-edit" title="Edit">✏️</a>
                            <form class="form-hapus" method="post" action="hapus.php" style="display:inline;">
                                <input type="hidden" name="kode" value="<?php echo htmlspecialchars($item['kode']); ?>">
                                <button type="submit" class="icon-btn danger btn-hapus" title="Hapus">🗑️</button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <nav class="pagination" style="justify-content: center; gap: 10px;">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?php echo $buatUrl($i, $kategori); ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>