<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Silakan login terlebih dahulu untuk melihat riwayat peminjaman.'];
    $_SESSION['redirect_after_login'] = '../peminjam/riwayat.php';
    header('Location: ../auth/login.php');
    exit;
}
// Admin melihat semua data lewat Daftar Peminjam
if (($_SESSION['role'] ?? '') === 'admin') {
    header('Location: list.php');
    exit;
}

$page_title = "Riwayat Peminjaman";
$activePage = "riwayat";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Hanya pengajuan milik akun yang sedang login
$stmt = $pdo->prepare(
    "SELECT p.*, h.foto, h.kategori
     FROM peminjam p
     LEFT JOIN perhiasan h ON h.kode = p.id_perhiasan
     WHERE p.user_id = :u
     ORDER BY p.id DESC"
);
$stmt->execute(['u' => $_SESSION['user_id']]);
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($riwayat);
$aktif = $telat = $kembali = 0;
foreach ($riwayat as $r) {
    if ($r['status'] === 'Aktif') $aktif++;
    elseif ($r['status'] === 'Terlambat') $telat++;
    elseif ($r['status'] === 'Dikembalikan') $kembali++;
}
?>
    <div class="container">
        <div class="page-header">
            <div>
                <div class="page-eyebrow">🕘 Akun Saya · Riwayat</div>
                <h1>Riwayat Peminjaman</h1>
                <p>Daftar seluruh perhiasan yang pernah Anda ajukan beserta status dan jatuh temponya.</p>
            </div>
            <a href="tambah.php" class="btn btn-maroon">+ Ajukan Peminjaman Baru</a>
        </div>

        <div class="stat-grid" style="margin-bottom:1.6rem;">
            <div class="stat-card"><div class="stat-top"><span class="label">Total Pengajuan</span><div class="stat-icon-box">📋</div></div><div class="stat-value" style="font-size:1.5rem;"><?php echo $total; ?></div></div>
            <div class="stat-card"><div class="stat-top"><span class="label">Sedang Dipinjam</span><div class="stat-icon-box">🔑</div></div><div class="stat-value" style="font-size:1.5rem;"><?php echo $aktif; ?></div></div>
            <div class="stat-card warn"><div class="stat-top"><span class="label">Terlambat</span><div class="stat-icon-box">⚠️</div></div><div class="stat-value" style="font-size:1.5rem;"><?php echo $telat; ?></div></div>
            <div class="stat-card"><div class="stat-top"><span class="label">Dikembalikan</span><div class="stat-icon-box">✅</div></div><div class="stat-value" style="font-size:1.5rem;"><?php echo $kembali; ?></div></div>
        </div>

        <?php if ($flash): ?>
            <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
        <?php endif; ?>

        <div class="panel" style="padding:1.2rem;">
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr><th>Perhiasan Dipinjam</th><th>Kode Pengajuan</th><th>Jatuh Tempo</th><th>Status</th><th style="text-align:right;">Jaminan Agunan</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($riwayat)): ?>
                        <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--ink-mute);">Anda belum pernah meminjam perhiasan. <a href="../perhiasan/list.php" style="font-weight:700;">Lihat katalog</a></td></tr>
                        <?php else: foreach ($riwayat as $r):
                            $badge = $r['status'] === 'Aktif' ? 'badge-success'
                                : ($r['status'] === 'Terlambat' ? 'badge-danger' : 'badge-warning');
                        ?>
                        <tr>
                            <td>
                                <div class="klien-cell">
                                    <?php if (!empty($r['foto'])): ?>
                                    <img src="<?php echo $base; ?>assets/img/<?php echo htmlspecialchars($r['foto']); ?>" alt="" style="width:48px;height:48px;border-radius:10px;object-fit:cover;flex-shrink:0;">
                                    <?php endif; ?>
                                    <div>
                                        <div class="klien-name"><?php echo htmlspecialchars($r['perhiasan']); ?></div>
                                        <div class="klien-sub"><?php echo htmlspecialchars($r['id_perhiasan']); ?><?php echo !empty($r['kategori']) ? ' · ' . htmlspecialchars($r['kategori']) : ''; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($r['kode']); ?></td>
                            <td><?php echo htmlspecialchars($r['jatuh_tempo']); ?></td>
                            <td><span class="badge <?php echo $badge; ?>"><?php echo htmlspecialchars($r['status']); ?></span></td>
                            <td style="text-align:right;">
                                <div class="klien-name"><?php echo $r['agunan'] ? "Rp " . number_format((float) $r['agunan'], 0, ',', '.') : "-"; ?></div>
                                <div class="klien-sub"><?php echo htmlspecialchars($r['jenis_agunan']); ?></div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>