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
            <form id="form-tambah" method="post" action="proses_tambah.php" enctype="multipart/form-data">
                <div class="form-step-block">
                    <div class="form-step-header"><h3>💠 Identifikasi & Nomenklatur</h3><span class="step-tag">Langkah 1/2</span></div>
                    <div class="form-grid">
                        <div>
                            <label class="field-label">🔲 Kode Perhiasan <span class="req">*</span></label>
                            <input type="text" class="form-control" name="kode" placeholder="P009" required>
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
                    <label class="field-label">📷 Unggah Foto Master Koleksi</label>
                    <div class="upload-drop" data-file="foto">
                        <div class="up-icon">☁️</div>
                        <strong>Klik atau tarik foto perhiasan ke sini</strong>
                        <span>Mendukung PNG, JPG, WEBP (Maks. 2MB)</span>
                        <img id="preview-foto" alt="Pratinjau foto" style="display:none;max-height:180px;max-width:100%;margin-top:0.9rem;border-radius:12px;">
                        <input type="file" id="foto" name="foto" accept=".png,.jpg,.jpeg,.webp" style="display:none;">
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
<script>
    const formTambah = document.getElementById('form-tambah');
    const box = document.querySelector('.upload-drop[data-file]');
    const input = box.querySelector('input[type=file]');
    const info = box.querySelector('span');
    const preview = document.getElementById('preview-foto');
    const teksAwal = info.textContent;

    box.style.cursor = 'pointer';
    input.addEventListener('click', e => e.stopPropagation());
    box.addEventListener('click', () => input.click());
    input.addEventListener('change', () => {
        if (input.files.length) {
            info.textContent = '✔ ' + input.files[0].name;
            preview.src = URL.createObjectURL(input.files[0]);
            preview.style.display = 'inline-block';
        } else {
            info.textContent = teksAwal;
            preview.style.display = 'none';
        }
    });
    ['dragover', 'dragenter'].forEach(ev => box.addEventListener(ev, e => { e.preventDefault(); box.style.borderColor = '#c9a227'; }));
    ['dragleave', 'drop'].forEach(ev => box.addEventListener(ev, e => { e.preventDefault(); box.style.borderColor = ''; }));
    box.addEventListener('drop', e => {
        if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; input.dispatchEvent(new Event('change')); }
    });
    formTambah.addEventListener('reset', () => setTimeout(() => input.dispatchEvent(new Event('change')), 0));
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>