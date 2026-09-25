<?php
$page_title = "Daftar Peminjam";
$activePage = "peminjam-list";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Jobsheet 8: ganti $_SESSION['peminjam'] menjadi SELECT * FROM peminjam
$daftarPeminjam = $pdo->query("SELECT * FROM peminjam ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$totalKlien = count($daftarPeminjam);
$aktifCount = 0;
$terlambatCount = 0;
$totalAgunan = 0;
foreach ($daftarPeminjam as $pm) {
    if ($pm['status'] === 'Aktif') $aktifCount++;
    elseif ($pm['status'] === 'Terlambat') $terlambatCount++;
    $totalAgunan += (int) ($pm['agunan'] ?? 0);
}
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
            <div class="search-box"><span class="ic">🔍</span><input type="text" id="search-input" placeholder="Cari nama klien, kode, atau telepon..."></div>
            <a href="list.php" class="btn btn-outline btn-sm">🔄 Muat Ulang</a>
        </div>

        <div class="panel" style="padding:1.2rem;">
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr><th>Klien / Nasabah</th><th>Klasifikasi</th><th>Perhiasan Dipinjam</th><th>Jadwal & Status</th><th style="text-align:right;">Jaminan Agunan</th><th>Aksi</th></tr>
                    </thead>
                    <tbody id="body-peminjam">
                        <?php if (empty($daftarPeminjam)): ?>
                        <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--ink-mute);">Belum ada data peminjam. Silakan tambah lewat menu "Tambah Peminjam".</td></tr>
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
                                <button type="button" class="icon-btn">👁️</button>
                                <button type="button" class="icon-btn danger btn-hapus">🗑️</button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>