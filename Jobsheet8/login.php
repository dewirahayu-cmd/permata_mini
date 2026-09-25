<?php
$page_title = "Masuk Admin";
$activePage = "login";
include __DIR__ . '/includes/header.php';
?>

<main>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-card-topbar"></div>
            <div class="login-body">
                <div class="login-head">
                    <span class="badge-shield">🛡️ Portal Administrator Permata Mini</span>
                    <h1>Masuk Akun Admin</h1>
                    <p>Silakan masukkan kredensial administrator untuk mengelola sirkulasi perhiasan dan data peminjam.</p>
                </div>
                
                <!-- Tag form sudah disiapkan untuk bisa memproses data PHP nantinya -->
                <form class="login-form" method="post" action="proses_login.php">
                    <div class="field-row">
                        <div class="field-top"><label class="field-label">ID Staf / Email Admin</label></div>
                        <div class="input-icon-wrap">
                            <span class="ic-left">🪪</span>
                            <input type="text" name="username" class="form-control" placeholder="admin@permata-mini.id" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field-top"><label class="field-label">Kata Sandi</label><a href="#">Lupa Kata Sandi?</a></div>
                        <div class="input-icon-wrap">
                            <span class="ic-left">🔒</span>
                            <input type="password" name="password" class="form-control" placeholder="••••••••••••" required>
                            <button type="button" class="toggle-eye">👁️</button>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field-top"><label class="field-label">PIN Otorisasi Brankas</label><span style="font-size:0.72rem;color:var(--ink-mute);">6 DIGIT</span></div>
                        <div class="pin-grid">
                            <input type="text" maxlength="1"><input type="text" maxlength="1"><input type="text" maxlength="1">
                            <input type="text" maxlength="1"><input type="text" maxlength="1"><input type="text" maxlength="1">
                        </div>
                    </div>
                    <div class="remember-row">
                        <input type="checkbox" name="remember"><span>Ingat Sesi Perangkat Ini (Maks. 8 Jam)</span>
                    </div>
                    
                    <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center;">🔑 Masuk ke Sistem Vault</button>
                    
                    <div class="login-secondary">
                        <button type="button">🔐 Kunci Fisik / FIDO2</button>
                        <a href="#">🎧 Bantuan IT Vault</a>
                    </div>
                </form>

                <div class="login-trust">🔒 Koneksi Terenkripsi TLS 1.3 · Audit Keamanan Perbankan Privat</div>
            </div>
        </div>
    </div>
    <p class="login-footnote">⚖️ Akses diawasi secara biometrik dan tercatat pada buku besar audit digital Permata Mini.</p>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>