<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Tamu harus login/daftar dulu sebelum mengajukan peminjaman
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Silakan login atau daftar akun terlebih dahulu untuk mengajukan peminjaman.'];
    $_SESSION['redirect_after_login'] = '../peminjam/tambah.php';
    header('Location: ../auth/login.php');
    exit;
}
$isAdmin = (($_SESSION['role'] ?? '') === 'admin');

$page_title = $isAdmin ? "Tambah Peminjam" : "Ajukan Peminjaman";
$activePage = "peminjam-tambah";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Data diri terisi otomatis dari pengajuan user sebelumnya (admin selalu mulai kosong)
$isi = ['nama' => '', 'nik' => '', 'telepon' => '', 'email' => '', 'alamat' => ''];
if (!$isAdmin) {
    $isi['nama'] = $_SESSION['nama'];
    $q = $pdo->prepare("SELECT nama, nik, telepon, email, alamat FROM peminjam WHERE user_id = :u ORDER BY kode DESC LIMIT 1");
    $q->execute(['u' => $_SESSION['user_id']]);
    $terakhir = $q->fetch(PDO::FETCH_ASSOC);
    if ($terakhir) {
        $isi = $terakhir;
    }
}

// Hanya perhiasan yang stoknya masih ada yang bisa dipinjam
$daftarPerhiasan = $pdo->query("SELECT kode, nama, stok FROM perhiasan WHERE stok > 0 ORDER BY kode")->fetchAll(PDO::FETCH_ASSOC);
$mapPerhiasan = [];
foreach ($daftarPerhiasan as $p) {
    $mapPerhiasan[$p['kode']] = $p['nama'];
}
?>
    <div class="container" style="max-width:900px;">
        <?php if ($isAdmin): ?>
        <div class="page-eyebrow">👥 Manajemen Peminjam › Registrasi Nasabah Baru</div>
        <h1 style="margin-bottom:0.4rem;">Tambah Peminjam</h1>
        <p style="color:var(--ink-mute);margin-bottom:1.6rem;">Formulir Verifikasi Identitas & Kontrak Perjanjian Peminjam VIP.</p>

        <div class="notice-box">
            <div class="ic">ℹ️</div>
            <div><h4>Perhatian Otoritas Vault! <span class="tag-required">WAJIB DIPATUHI</span></h4><p>Pastikan dokumen identitas resmi (KTP/Paspor) dan verifikasi jaminan finansial telah ditinjau sebelum serah-terima fisik perhiasan.</p></div>
        </div>
        <?php else: ?>
        <div class="page-eyebrow">💎 Layanan Peminjaman › Pengajuan Baru</div>
        <h1 style="margin-bottom:0.4rem;">Ajukan Peminjaman</h1>
        <p style="color:var(--ink-mute);margin-bottom:1.6rem;">Lengkapi data diri dan pilih perhiasan yang ingin Anda pinjam.</p>

        <div class="notice-box">
            <div class="ic">ℹ️</div>
            <div><h4>Informasi Pengajuan</h4><p>Data Anda akan diverifikasi oleh petugas vault sebelum perhiasan diserahkan. Seluruh pengajuan Anda dapat dilihat di menu <b>Riwayat Saya</b>.</p></div>
        </div>
        <?php endif; ?>

        <?php if ($flash): ?>
            <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
        <?php endif; ?>

        <div class="panel">
            <form id="form-tambah" method="post" action="proses_tambah.php" enctype="multipart/form-data">
                <div class="form-step-block">
                    <div class="form-step-header"><h3>👤 Data Diri Peminjam</h3><span class="step-tag">Langkah 1/2</span></div>
                    <div class="form-grid">
                        <?php if ($isAdmin): ?>
                        <div>
                            <label class="field-label">🔲 Kode Peminjam</label>
                            <input type="text" class="form-control" name="kode" placeholder="BRW-007">
                            <p class="field-hint">Kosongkan untuk dibuat otomatis (BRW-xxx).</p>
                        </div>
                        <?php endif; ?>
                        <div>
                            <label class="field-label">👤 Nama Lengkap Peminjam <span class="req">*</span></label>
                            <input type="text" class="form-control" name="nama" value="<?php echo htmlspecialchars($isi['nama']); ?>" placeholder="Raden Ayu Clarissa Haryono" required>
                            <p class="field-hint">Wajib tepat sesuai nama resmi pada KTP/Paspor.</p>
                        </div>
                        <div>
                            <label class="field-label">🪪 Nomor Identitas (NIK/Paspor) <span class="req">*</span></label>
                            <input type="text" class="form-control" name="nik" value="<?php echo htmlspecialchars($isi['nik']); ?>" placeholder="16 Digit NIK KTP" required>
                        </div>
                        <div>
                            <label class="field-label">📱 Nomor Telepon / WhatsApp <span class="req">*</span></label>
                            <input type="text" class="form-control" name="telepon" value="<?php echo htmlspecialchars($isi['telepon']); ?>" placeholder="+62 811 xxxx xxxx" required>
                        </div>
                        <div>
                            <label class="field-label">✉️ Alamat Email Korespondensi <span class="req">*</span></label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($isi['email']); ?>" placeholder="nama.klien@domainpribadi.com" required>
                        </div>
                        <?php if ($isAdmin): ?>
                        <div>
                            <label class="field-label">🏦 Klasifikasi Tier Klien</label>
                            <select class="form-control" name="tier">
                                <option value="VVIP Tier-1">VVIP Tier-1</option>
                                <option value="Corporate VIP">Corporate VIP</option>
                                <option value="Private Collector">Private Collector</option>
                                <option value="Verified VIP" selected>Verified VIP</option>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="full">
                            <label class="field-label">📍 Alamat Domisili Lengkap <span class="req">*</span></label>
                            <textarea class="form-control" name="alamat" rows="3" placeholder="Jl. Senopati Raya No. ..., RT/RW, Jakarta Selatan" required><?php echo htmlspecialchars($isi['alamat']); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-step-block">
                    <div class="form-step-header"><h3>🗂️ Verifikasi & Jaminan</h3><span class="step-tag">Langkah 2/2</span></div>
                    <div class="form-grid">
                        <div class="upload-drop" data-file="berkas_ktp">
                            <div class="up-icon">🪪</div>
                            <strong>Klik atau Seret Berkas KTP</strong>
                            <span>Format PNG, JPG, WEBP, PDF (Maks. 2MB)</span>
                            <input type="file" id="berkas_ktp" name="berkas_ktp" accept=".png,.jpg,.jpeg,.webp,.pdf" style="display:none;">
                        </div>
                        <div class="upload-drop" data-file="berkas_jaminan">
                            <div class="up-icon">🏛️</div>
                            <strong>Klik atau Seret Bukti Jaminan</strong>
                            <span>Bank Statement, Surat Escrow, atau Polis Asuransi (Maks. 2MB)</span>
                            <input type="file" id="berkas_jaminan" name="berkas_jaminan" accept=".png,.jpg,.jpeg,.webp,.pdf" style="display:none;">
                        </div>
                        <div>
                            <label class="field-label">💰 Nilai Jaminan Agunan (IDR)</label>
                            <div class="currency-input"><span class="prefix">IDR</span><input type="number" class="form-control" name="agunan" min="0" placeholder="120000000"></div>
                        </div>
                        <div>
                            <label class="field-label">🏦 Jenis Jaminan</label>
                            <input type="text" class="form-control" name="jenis_agunan" placeholder="Bilyet Deposito BCA">
                        </div>
                        <div>
                            <label class="field-label">🔲 Kode Perhiasan Dipinjam <?php if (!$isAdmin): ?><span class="req">*</span><?php endif; ?></label>
                            <input type="text" class="form-control" id="id_perhiasan" name="id_perhiasan" list="list-perhiasan" placeholder="<?php echo $isAdmin ? 'P008 (kosongkan jika belum meminjam)' : 'Contoh: P008'; ?>" autocomplete="off" <?php echo $isAdmin ? '' : 'required'; ?>>
                            <datalist id="list-perhiasan">
                                <?php foreach ($daftarPerhiasan as $p): ?>
                                <option value="<?php echo htmlspecialchars($p['kode']); ?>"><?php echo htmlspecialchars($p['nama']); ?> (stok <?php echo (int) $p['stok']; ?>)</option>
                                <?php endforeach; ?>
                            </datalist>
                            <p class="field-hint">Pilih dari daftar perhiasan yang stoknya tersedia.</p>
                        </div>
                        <div>
                            <label class="field-label">💎 Nama Perhiasan Dipinjam</label>
                            <input type="text" class="form-control" id="nama_perhiasan" placeholder="Terisi otomatis dari kode perhiasan" readonly>
                            <p class="field-hint">Otomatis mengikuti kode perhiasan.</p>
                        </div>
                        <div>
                            <?php if ($isAdmin): ?>
                            <label class="field-label">📅 Jadwal Jatuh Tempo</label>
                            <input type="text" class="form-control" name="jatuh_tempo" placeholder="Contoh: 28 Okt 2025">
                            <p class="field-hint">Wajib diisi jika meminjam perhiasan.</p>
                            <?php else: ?>
                            <label class="field-label">📅 Lama Peminjaman</label>
                            <select class="form-control" name="lama_hari">
                                <option value="3">3 hari</option>
                                <option value="7" selected>7 hari</option>
                                <option value="14">14 hari</option>
                                <option value="30">30 hari</option>
                            </select>
                            <p class="field-hint">Jatuh tempo dihitung otomatis sejak hari pengajuan.</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="field-label">📊 Status Pinjaman</label>
                            <input type="text" class="form-control" id="status_tampil" value="Bebas" readonly>
                            <p class="field-hint">Otomatis: Aktif jika meminjam, Bebas jika tidak.</p>
                        </div>
                        <div class="full" style="display:flex;align-items:flex-start;gap:0.6rem;">
                            <input type="checkbox" required style="margin-top:0.3rem;">
                            <span style="font-size:0.85rem;color:var(--ink-soft);">
                                <?php if ($isAdmin): ?>
                                Saya mengonfirmasi bahwa seluruh dokumen nasabah telah diverifikasi keasliannya dan nasabah bersedia tunduk pada syarat & ketentuan peminjaman vault Permata Mini.
                                <?php else: ?>
                                Saya menyatakan data dan dokumen yang saya berikan adalah benar, serta bersedia tunduk pada syarat & ketentuan peminjaman vault Permata Mini.
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn btn-outline">↺ Reset Formulir</button>
                    <div class="right-group">
                        <a href="<?php echo $isAdmin ? 'list.php' : '../perhiasan/list.php'; ?>" class="btn btn-outline">Batalkan</a>
                        <button type="submit" class="btn btn-gold"><?php echo $isAdmin ? '👤 Simpan Data Peminjam' : '📨 Kirim Pengajuan'; ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script>
    const dataPerhiasan = <?php echo json_encode($mapPerhiasan, JSON_HEX_TAG | JSON_HEX_AMP); ?>;
    const inKode = document.getElementById('id_perhiasan');
    const inNama = document.getElementById('nama_perhiasan');
    const inStatus = document.getElementById('status_tampil');
    function isiNamaPerhiasan() {
        const k = inKode.value.trim();
        inNama.value = k === '' ? '' : (dataPerhiasan[k] || 'Kode tidak ditemukan / stok habis');
        inStatus.value = (k !== '' && dataPerhiasan[k]) ? 'Aktif' : 'Bebas';
    }
    inKode.addEventListener('input', isiNamaPerhiasan);

    const formTambah = document.getElementById('form-tambah');
    formTambah.addEventListener('reset', () => setTimeout(isiNamaPerhiasan, 0));

    // Area upload: klik atau seret berkas
    document.querySelectorAll('.upload-drop[data-file]').forEach(box => {
        const input = box.querySelector('input[type=file]');
        const info = box.querySelector('span');
        const teksAwal = info.textContent;
        box.style.cursor = 'pointer';
        input.addEventListener('click', e => e.stopPropagation());
        box.addEventListener('click', () => input.click());
        input.addEventListener('change', () => {
            info.textContent = input.files.length ? '✔ ' + input.files[0].name : teksAwal;
        });
        ['dragover', 'dragenter'].forEach(ev => box.addEventListener(ev, e => { e.preventDefault(); box.style.borderColor = '#c9a227'; }));
        ['dragleave', 'drop'].forEach(ev => box.addEventListener(ev, e => { e.preventDefault(); box.style.borderColor = ''; }));
        box.addEventListener('drop', e => {
            if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; input.dispatchEvent(new Event('change')); }
        });
        formTambah.addEventListener('reset', () => setTimeout(() => input.dispatchEvent(new Event('change')), 0));
    });
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>