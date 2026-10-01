<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Daftar Peminjam";
$activePage = "peminjam-list";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$perPage = 6;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $hitung = $pdo->prepare("SELECT COUNT(*) FROM peminjam WHERE nama ILIKE :kw OR kode ILIKE :kw OR telepon ILIKE :kw");
    $hitung->execute(['kw' => '%' . $keyword . '%']);
    $totalRows = $hitung->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM peminjam WHERE nama ILIKE :kw OR kode ILIKE :kw OR telepon ILIKE :kw ORDER BY kode DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue('kw', '%' . $keyword . '%');
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM peminjam")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM peminjam ORDER BY kode DESC LIMIT :limit OFFSET :offset");
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$daftarPeminjam = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));

$totalKlien = (int) $pdo->query("SELECT COUNT(*) FROM peminjam")->fetchColumn();
$aktifCount = (int) $pdo->query("SELECT COUNT(*) FROM peminjam WHERE status = 'Aktif'")->fetchColumn();
$terlambatCount = (int) $pdo->query("SELECT COUNT(*) FROM peminjam WHERE status = 'Terlambat'")->fetchColumn();
$totalAgunan = (int) $pdo->query("SELECT COALESCE(SUM(agunan),0) FROM peminjam")->fetchColumn();
$valuasiJt = round($totalAgunan / 1000000);
?>
    <div class="container">
        <div class="page-header">
            <div>
                <div class="page-eyebrow">⚜️ Manajemen Relasi & Klien</div>
                <h1>Daftar Peminjam</h1>
                <p>Direktori klien VIP, status peminjaman aktif, jaminan agunan terikat, dan riwayat sirkulasi perhiasan.</p>
            </div>
            <a href="tambah.php" class="btn btn-maroon">+ Tambah Peminjam Baru</a>
        </div>

        <div class="stat-grid" style="margin-bottom:1.6rem;">
            <div class="stat-card"><div class="stat-top"><span class="label">Total Klien</span><div class="stat-icon-box">👥</div></div><div class="stat-value" style="font-size:1.5rem;"><?php echo $totalKlien; ?></div></div>
            <div class="stat-card"><div class="stat-top"><span class="label">Aktif</span><div class="stat-icon-box">🔑</div></div><div class="stat-value" style="font-size:1.5rem;"><?php echo $aktifCount; ?></div></div>
            <div class="stat-card warn"><div class="stat-top"><span class="label">Terlambat</span><div class="stat-icon-box">⚠️</div></div><div class="stat-value" style="font-size:1.5rem;"><?php echo $terlambatCount; ?></div></div>
            <div class="stat-card"><div class="stat-top"><span class="label">Valuasi Dilindungi</span><div class="stat-icon-box">🔒</div></div><div class="stat-value" style="font-size:1.3rem;">Rp <?php echo $valuasiJt; ?> Jt</div></div>
        </div>

        <?php if ($flash): ?>
            <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
        <?php endif; ?>

        <div class="toolbar">
            <form method="get" action="list.php" class="search-box" style="display:flex;gap:0.5rem;align-items:center;max-width:none;flex:1;">
                <span class="ic" style="position:static;">🔍</span>
                <input type="text" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Cari nama klien, kode, atau telepon...">
                <button type="submit" class="btn btn-maroon btn-sm">Cari</button>
            </form>
        </div>

        <div class="panel" style="padding:1.2rem;">
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr><th>Klien / Nasabah</th><th>Klasifikasi</th><th>Perhiasan Dipinjam</th><th>Jadwal & Status</th><th style="text-align:right;">Jaminan Agunan</th><th>Aksi</th></tr>
                    </thead>
                    <tbody id="body-peminjam">
                        <?php if (empty($daftarPeminjam)): ?>
                        <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--ink-mute);">Tidak ada data yang cocok.</td></tr>
                        <?php else: foreach ($daftarPeminjam as $item):
                            $badge = $item['status'] === 'Aktif' ? 'badge-success'
                                : ($item['status'] === 'Terlambat' ? 'badge-danger' : 'badge-warning');
                            $avatarClass = $item['status'] === 'Terlambat' ? 'klien-avatar late' : 'klien-avatar';
                        ?>
                        <tr>
                            <td>
                                <div class="klien-cell">
                                    <div class="<?php echo $avatarClass; ?>"><?php echo htmlspecialchars($item['inisial']); ?></div>
                                    <div>
                                        <div class="klien-name"><?php echo htmlspecialchars($item['nama']); ?></div>
                                        <div class="klien-sub"><?php echo htmlspecialchars($item['kode']); ?> · <?php echo htmlspecialchars($item['telepon']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge badge-warning"><?php echo htmlspecialchars($item['tier']); ?></span></td>
                            <td>
                                <div class="klien-name" style="font-weight:600;"><?php echo htmlspecialchars($item['perhiasan']); ?></div>
                                <div class="klien-sub"><?php echo htmlspecialchars($item['id_perhiasan']); ?></div>
                            </td>
                            <td>
                                <span class="badge <?php echo $badge; ?>"><?php echo htmlspecialchars($item['status']); ?></span><br>
                                <span class="klien-sub">Tempo: <?php echo htmlspecialchars($item['jatuh_tempo']); ?></span>
                            </td>
                            <td style="text-align:right;">
                                <div class="klien-name"><?php echo $item['agunan'] ? "Rp " . number_format((float) $item['agunan'], 0, ',', '.') : "-"; ?></div>
                                <div class="klien-sub"><?php echo htmlspecialchars($item['jenis_agunan']); ?></div>
                            </td>
                            <td class="text-center">
                                <a href="edit.php?kode=<?php echo urlencode($item['kode']); ?>" class="icon-btn btn-edit" title="Edit">✏️</a>
                                <form class="form-hapus" method="post" action="hapus.php" style="display:inline;">
                                    <input type="hidden" name="kode" value="<?php echo htmlspecialchars($item['kode']); ?>">
                                    <button type="submit" class="icon-btn danger btn-hapus" title="Hapus">🗑️</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
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