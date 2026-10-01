<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Edit Perhiasan";
$activePage = "perhiasan-list";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$kode = $_GET['kode'] ?? null;
if (!$kode) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM perhiasan WHERE kode = :kode");
$stmt->execute(['kode' => $kode]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    header('Location: list.php');
    exit;
}

$kategoriOpsi = ['Kalung' => 'Kalung (Haute Collier)', 'Cincin' => 'Cincin (Solitaire & Halo)', 'Gelang' => 'Gelang & Bangle', 'Anting' => 'Anting (Earrings)', 'Bros' => 'Bros Kolektor', 'Tiara' => 'Tiara & Diadem'];
$stokTotal = !empty($item['stok_total']) ? (int) $item['stok_total'] : (int) $item['stok'];
?>
    <div class="container" style="max-width:900px;">
        <div class="page-eyebrow">Katalog Vault · Ubah Koleksi</div>
        <h1 style="margin-bottom:0.4rem;">Edit Perhiasan</h1>
        <p style="color:var(--ink-mute);margin-bottom:1.6rem;">Perbarui data koleksi <?php echo htmlspecialchars($item['nama']); ?>.</p>

        <?php if ($flash): ?>
            <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
        <?php endif; ?>

        <div class="panel">
            <form id="form-tambah" method="post" action="proses_edit.php">
                <div class="form-step-block">
                    <div class="form-step-header"><h3>💠 Identifikasi & Nomenklatur</h3></div>
                    <div class="form-grid">
                        <div>
                            <label class="field-label">🔲 Kode Perhiasan</label>
                            <input type="text" class="form-control" name="kode" value="<?php echo htmlspecialchars($item['kode']); ?>" readonly>
                            <p class="field-hint">Kode tidak dapat diubah karena dipakai pada data peminjam.</p>
                        </div>
                        <div>
                            <label class="field-label">💎 Nama Koleksi Perhiasan <span class="req">*</span></label>
                            <input type="text" class="form-control" name="nama" value="<?php echo htmlspecialchars($item['nama']); ?>" required>
                        </div>
                        <div>
                            <label class="field-label">🏷️ Kategori Perhiasan</label>
                            <select class="form-control" name="kategori">
                                <?php foreach ($kategoriOpsi as $value => $label): ?>
                                <option value="<?php echo $value; ?>" <?php echo $item['kategori'] === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="field-label">⚖️ Material & Kemurnian</label>
                            <input type="text" class="form-control" name="material" value="<?php echo htmlspecialchars($item['material']); ?>">
                        </div>
                        <div>
                            <label class="field-label">📦 Total Stok Koleksi <span class="req">*</span></label>
                            <input type="number" class="form-control" name="stok_total" min="0" value="<?php echo $stokTotal; ?>" required>
                        </div>
                        <div>
                            <label class="field-label">💰 Nilai Aset / Asuransi (IDR) <span class="req">*</span></label>
                            <div class="currency-input"><span class="prefix">IDR</span><input type="number" class="form-control" name="nilai" min="0" value="<?php echo (int) $item['nilai']; ?>" required></div>
                        </div>
                        <div>
                            <label class="field-label">🔓 Stok Tersedia</label>
                            <input type="text" class="form-control" value="<?php echo (int) $item['stok']; ?> / <?php echo $stokTotal; ?>" readonly>
                            <p class="field-hint">Dihitung otomatis dari data peminjam.</p>
                        </div>
                        <div>
                            <label class="field-label">📊 Status</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($item['status']); ?>" readonly>
                            <p class="field-hint">Mengikuti status peminjaman perhiasan ini.</p>
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
<?php include __DIR__ . '/../includes/footer.php'; ?>