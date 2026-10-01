<?php
$page_title = "Tambah Perhiasan";
$activePage = "perhiasan-tambah";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
    <div class="container" style="max-width:900px;">
        <div class="page-eyebrow">Katalog Vault · Tambah Koleksi Baru</div>
        <h1 style="margin-bottom:0.4rem;">Tambah Perhiasan</h1>
        <p style="color:var(--ink-mute);margin-bottom:1.6rem;">Registrasi Perhiasan Mewah ke dalam Sistem Vault Permata Mini.</p>

        <div class="notice-box">
            <div class="ic">💠</div>
            <div><h4>Registrasi Perhiasan Baru</h4><p>Pastikan seluruh data spesifikasi, valuasi, dan dokumentasi kuratorial telah lengkap dan terverifikasi sebelum disimpan.</p></div>
        </div>

        <?php if ($flash): ?>
            <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
        <?php endif; ?>

        <div class="panel">
            <form id="form-tambah" method="post" action="proses_tambah.php">
                <div class="form-step-block">
                    <div class="form-step-header"><h3>💠 Identifikasi & Nomenklatur</h3><span class="step-tag">Langkah 1/2</span></div>
                    <div class="form-grid">
                        <div>
                            <label class="field-label">🔲 Kode Perhiasan <span class="req">*</span></label>
                            <input type="text" class="form-control" name="kode" placeholder="P009-VLT" required>
                        </div>
                        <div>
                            <label class="field-label">💎 Nama Koleksi Perhiasan <span class="req">*</span></label>
                            <input type="text" class="form-control" name="nama" placeholder="Kalung Royal Belle Époque" required>
                        </div>
                        <div>
                            <label class="field-label">🏷️ Kategori Perhiasan</label>
                            <select class="form-control" name="kategori">
                                <option value="Kalung">Kalung (Haute Collier)</option>
                                <option value="Cincin">Cincin (Solitaire & Halo)</option>
                                <option value="Gelang">Gelang & Bangle</option>
                                <option value="Anting">Anting (Earrings)</option>
                                <option value="Bros">Bros Kolektor</option>
                                <option value="Tiara">Tiara & Diadem</option>
                            </select>
                        </div>
                        <div>
                            <label class="field-label">⚖️ Material & Kemurnian</label>
                            <input type="text" class="form-control" name="material" placeholder="Platinum 950 & Emas Putih 18K">
                        </div>
                        <div>
                            <label class="field-label">📦 Jumlah Stok <span class="req">*</span></label>
                            <input type="number" class="form-control" name="stok" min="0" placeholder="1" required>
                        </div>
                        <div>
                            <label class="field-label">💰 Nilai Aset / Asuransi (IDR) <span class="req">*</span></label>
                            <div class="currency-input"><span class="prefix">IDR</span><input type="number" class="form-control" name="nilai" min="0" placeholder="75000000" required></div>
                        </div>
                    </div>
                </div>

                <div class="form-step-block">
                    <div class="form-step-header"><h3>🖼️ Dokumentasi & Catatan Kuratorial</h3><span class="step-tag">Langkah 2/2</span></div>
                    <label class="field-label">📷 Unggah Foto Master Koleksi & Sertifikat</label>
                    <div class="upload-drop">
                        <div class="up-icon">☁️</div>
                        <strong>Tarik file citra beresolusi tinggi ke sini</strong>
                        <span>Mendukung PNG, JPG, PDF Sertifikat Lab (Maks. 25MB) — unggah berkas belum aktif di jobsheet ini</span>
                    </div>
                    <div style="margin-top:1.1rem;">
                        <label class="field-label">📝 Deskripsi Lengkap & Riwayat Kurasi</label>
                        <textarea class="form-control" name="deskripsi" rows="4" placeholder="Jelaskan karakteristik unik perhiasan, potongan batu permata, provenance..."></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="list.php" class="btn btn-outline">← Batal & Kembali</a>
                    <div class="right-group">
                        <button type="reset" class="btn btn-outline">Reset Formulir</button>
                        <button type="submit" class="btn btn-gold">Simpan ke Vault →</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
