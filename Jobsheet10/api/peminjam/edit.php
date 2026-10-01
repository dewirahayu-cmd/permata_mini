<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Edit Peminjam";
$activePage = "peminjam-list";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$kode = $_GET['kode'] ?? null;
if (!$kode) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM peminjam WHERE kode = :kode");
$stmt->execute(['kode' => $kode]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    header('Location: list.php');
    exit;
}

$tierOpsi = ['VVIP Tier-1', 'Corporate VIP', 'Private Collector', 'Verified VIP'];
$statusOpsi = ['Aktif', 'Terlambat', 'Dikembalikan', 'Bebas'];

$kodePerhiasan = $item['id_perhiasan'] === '-' ? '' : $item['id_perhiasan'];
$jatuhTempo = $item['jatuh_tempo'] === '-' ? '' : $item['jatuh_tempo'];

// Perhiasan yang bisa dipilih: yang stoknya ada, ditambah yang sedang dipinjam peminjam ini
$q = $pdo->prepare("SELECT kode, nama, stok FROM perhiasan WHERE stok > 0 OR kode = :cur ORDER BY kode");
$q->execute(['cur' => $kodePerhiasan]);
$daftarPerhiasan = $q->fetchAll(PDO::FETCH_ASSOC);
$mapPerhiasan = [];
foreach ($daftarPerhiasan as $p) {
    $mapPerhiasan[$p['kode']] = $p['nama'];
}
?>
    <div class="container" style="max-width:900px;">
        <div class="page-eyebrow">👥 Manajemen Peminjam › Ubah Data Nasabah</div>
        <h1 style="margin-bottom:0.4rem;">Edit Peminjam</h1>
        <p style="color:var(--ink-mute);margin-bottom:1.6rem;">Perbarui data <?php echo htmlspecialchars($item['nama']); ?>.</p>

        <?php if ($flash): ?>
            <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
        <?php endif; ?>

        <div class="panel">
            <form id="form-tambah" method="post" action="proses_edit.php">
                <div class="form-step-block">
                    <div class="form-step-header"><h3>👤 Data Diri Peminjam</h3></div>
                    <div class="form-grid">
                        <div>
                            <label class="field-label">🔲 Kode Peminjam</label>
                            <input type="text" class="form-control" name="kode" value="<?php echo htmlspecialchars($item['kode']); ?>" readonly>
                            <p class="field-hint">Kode tidak dapat diubah.</p>
                        </div>
                        <div>
                            <label class="field-label">👤 Nama Lengkap <span class="req">*</span></label>
                            <input type="text" class="form-control" name="nama" value="<?php echo htmlspecialchars($item['nama']); ?>" required>
                        </div>
                        <div>
                            <label class="field-label">🪪 Nomor Identitas (NIK/Paspor)</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($item['nik']); ?>" readonly>
                            <p class="field-hint">Identitas resmi tidak dapat diubah.</p>
                        </div>
                        <div>
                            <label class="field-label">📱 Nomor Telepon <span class="req">*</span></label>
                            <input type="text" class="form-control" name="telepon" value="<?php echo htmlspecialchars($item['telepon']); ?>" required>
                        </div>
                        <div>
                            <label class="field-label">✉️ Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($item['email']); ?>">
                        </div>
                        <div>
                            <label class="field-label">🏦 Tier</label>
                            <select class="form-control" name="tier">
                                <?php foreach ($tierOpsi as $t): ?>
                                <option value="<?php echo $t; ?>" <?php echo $item['tier'] === $t ? 'selected' : ''; ?>><?php echo $t; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="full">
                            <label class="field-label">📍 Alamat Domisili <span class="req">*</span></label>
                            <input type="text" class="form-control" name="alamat" value="<?php echo htmlspecialchars($item['alamat']); ?>" required>
                        </div>
                        <div>
                            <label class="field-label">🔲 Kode Perhiasan Dipinjam</label>
                            <input type="text" class="form-control" id="id_perhiasan" name="id_perhiasan" list="list-perhiasan" value="<?php echo htmlspecialchars($kodePerhiasan); ?>" placeholder="Kosongkan jika tidak meminjam" autocomplete="off">
                            <datalist id="list-perhiasan">
                                <?php foreach ($daftarPerhiasan as $p): ?>
                                <option value="<?php echo htmlspecialchars($p['kode']); ?>"><?php echo htmlspecialchars($p['nama']); ?> (stok <?php echo (int) $p['stok']; ?>)</option>
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div>
                            <label class="field-label">💎 Nama Perhiasan Dipinjam</label>
                            <input type="text" class="form-control" id="nama_perhiasan" value="<?php echo htmlspecialchars($item['perhiasan'] === '-' ? '' : $item['perhiasan']); ?>" placeholder="Terisi otomatis dari kode perhiasan" readonly>
                        </div>
                        <div>
                            <label class="field-label">📅 Jadwal Jatuh Tempo</label>
                            <input type="text" class="form-control" name="jatuh_tempo" value="<?php echo htmlspecialchars($jatuhTempo); ?>" placeholder="Contoh: 28 Okt 2025">
                        </div>
                        <div>
                            <label class="field-label">📊 Status Pinjaman</label>
                            <select class="form-control" id="status" name="status">
                                <?php foreach ($statusOpsi as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo $item['status'] === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="field-hint">Dikembalikan = perhiasan sudah kembali, riwayat tetap tersimpan. Bebas = belum pernah meminjam.</p>
                        </div>
                        <div>
                            <label class="field-label">💰 Nilai Agunan (IDR)</label>
                            <div class="currency-input"><span class="prefix">IDR</span><input type="number" class="form-control" name="agunan" min="0" value="<?php echo (int) $item['agunan']; ?>"></div>
                        </div>
                        <div>
                            <label class="field-label">📄 Jenis Agunan Direlase / Disimpan</label>
                            <input type="text" class="form-control" name="jenis_agunan" value="<?php echo htmlspecialchars($item['jenis_agunan']); ?>" placeholder="Contoh: Sertifikat Hak Milik (SHM) / Agunan Dirilis">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="list.php" class="btn btn-outline">← Batal & Kembali</a>
                    <button type="submit" class="btn btn-gold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
<script>
    const dataPerhiasan = <?php echo json_encode($mapPerhiasan, JSON_HEX_TAG | JSON_HEX_AMP); ?>;
    const inKode = document.getElementById('id_perhiasan');
    const inNama = document.getElementById('nama_perhiasan');
    const selStatus = document.getElementById('status');
    function isiNamaPerhiasan() {
        const k = inKode.value.trim();
        inNama.value = k === '' ? '' : (dataPerhiasan[k] || 'Kode tidak ditemukan / stok habis');
        if (k === '') {
            selStatus.value = 'Bebas';
        } else if (selStatus.value === 'Bebas') {
            selStatus.value = 'Aktif';
        }
    }
    inKode.addEventListener('input', isiNamaPerhiasan);
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>