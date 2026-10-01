<?php
$page_title = "Tambah Peminjam";
$activePage = "peminjam-tambah";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
    <div class="container" style="max-width:900px;">
        <div class="page-eyebrow">👥 Manajemen Peminjam › Registrasi Nasabah Baru</div>
        <h1 style="margin-bottom:0.4rem;">Tambah Peminjam</h1>
        <p style="color:var(--ink-mute);margin-bottom:1.6rem;">Formulir Verifikasi Identitas & Kontrak Perjanjian Peminjam VIP.</p>

        <div class="notice-box">
            <div class="ic">ℹ️</div>
            <div><h4>Perhatian Otoritas Vault! <span class="tag-required">WAJIB DIPATUHI</span></h4><p>Pastikan dokumen identitas resmi (KTP/Paspor) dan verifikasi jaminan finansial telah ditinjau sebelum serah-terima fisik perhiasan.</p></div>
        </div>

        <?php if ($flash): ?>
            <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
        <?php endif; ?>

        <div class="panel">
            <form id="form-tambah" method="post" action="proses_tambah.php">
                <div class="form-step-block">
                    <div class="form-step-header"><h3>👤 Data Diri Peminjam</h3><span class="step-tag">Langkah 1/2</span></div>
                    <div class="form-grid">
                        <div>
                            <label class="field-label">🔲 Kode Peminjam <span class="req">*</span></label>
                            <input type="text" class="form-control" name="kode" placeholder="BR-007" required>
                        </div>
                        <div>
                            <label class="field-label">👤 Nama Lengkap Peminjam <span class="req">*</span></label>
                            <input type="text" class="form-control" name="nama" placeholder="Raden Ayu Clarissa Haryono" required>
                            <p class="field-hint">Wajib tepat sesuai nama resmi pada KTP/Paspor.</p>
                        </div>
                        <div>
                            <label class="field-label">🪪 Nomor Identitas (NIK/Paspor) <span class="req">*</span></label>
                            <input type="text" class="form-control" name="nik" placeholder="16 Digit NIK KTP" required>
                        </div>
                        <div>
                            <label class="field-label">📱 Nomor Telepon / WhatsApp <span class="req">*</span></label>
                            <input type="text" class="form-control" name="telepon" placeholder="+62 811 xxxx xxxx" required>
                        </div>
                        <div>
                            <label class="field-label">✉️ Alamat Email Korespondensi <span class="req">*</span></label>
                            <input type="email" class="form-control" name="email" placeholder="nama.klien@domainpribadi.com" required>
                        </div>
                        <div>
                            <label class="field-label">🏦 Klasifikasi Tier Klien</label>
                            <select class="form-control" name="tier">
                                <option value="VVIP Tier-1">VVIP Tier-1</option>
                                <option value="Corporate VIP">Corporate VIP</option>
                                <option value="Private Collector">Private Collector</option>
                                <option value="Verified VIP">Verified VIP</option>
                            </select>
                        </div>
                        <div class="full">
                            <label class="field-label">📍 Alamat Domisili Lengkap <span class="req">*</span></label>
                            <textarea class="form-control" name="alamat" rows="3" placeholder="Jl. Senopati Raya No. ..., RT/RW, Jakarta Selatan" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-step-block">
                    <div class="form-step-header"><h3>🗂️ Verifikasi & Jaminan</h3><span class="step-tag">Langkah 2/2</span></div>
                    <div class="form-grid">
                        <div class="upload-drop">
                            <div class="up-icon">🪪</div>
                            <strong>Klik atau Seret Berkas KTP</strong>
                            <span>Format PNG, JPG, PDF (Maks. 10MB) — unggah berkas belum aktif di jobsheet ini</span>
                        </div>
                        <div class="upload-drop">
                            <div class="up-icon">🏛️</div>
                            <strong>Klik atau Seret Bukti Jaminan</strong>
                            <span>Bank Statement, Surat Escrow, atau Polis Asuransi — unggah berkas belum aktif di jobsheet ini</span>
                        </div>
                        <div>
                            <label class="field-label">💰 Nilai Jaminan Agunan (IDR)</label>
                            <div class="currency-input"><span class="prefix">IDR</span><input type="number" class="form-control" name="agunan" min="0" placeholder="120000000"></div>
                        </div>
                        <div>
                            <label class="field-label">🏦 Jenis Jaminan</label>
                            <input type="text" class="form-control" name="jenis_agunan" placeholder="Bilyet Deposito BCA">
                        </div>
                        <div class="full" style="display:flex;align-items:flex-start;gap:0.6rem;">
                            <input type="checkbox" required style="margin-top:0.3rem;">
                            <span style="font-size:0.85rem;color:var(--ink-soft);">Saya mengonfirmasi bahwa seluruh dokumen nasabah telah diverifikasi keasliannya dan nasabah bersedia tunduk pada syarat & ketentuan peminjaman vault Permata Mini.</span>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn btn-outline">↺ Reset Formulir</button>
                    <div class="right-group">
                        <a href="list.php" class="btn btn-outline">Batalkan</a>
                        <button type="submit" class="btn btn-gold">👤 Simpan Data Peminjam</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

