<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$page_title = "Masuk";
$activePage = "login";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-card-topbar"></div>
            <div class="login-body">
                <div class="login-head">
                    <span class="badge-shield">🛡️ Portal Akun Permata Mini</span>
                    <h1>Masuk Akun</h1>
                    <p>Masuk untuk mengajukan peminjaman perhiasan dan melihat riwayat peminjaman Anda.</p>
                </div>

                <div class="notice-box" style="margin-bottom:1.2rem;">
                    <div class="ic">🎓</div>
                    <div>
                        <h4>Akun Demo Admin</h4>
                        <p>Username: <b>admin</b> &nbsp;·&nbsp; Password: <b>admin</b></p>
                        <p style="margin-top:0.3rem;"><a href="#" id="isi-admin" style="font-weight:700;">Isi otomatis →</a></p>
                    </div>
                </div>

                <?php if ($flash): ?>
                    <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
                <?php endif; ?>

                <form class="login-form" method="post" action="proses_login.php">
                    <div class="field-row">
                        <div class="field-top"><label class="field-label">Username</label></div>
                        <div class="input-icon-wrap">
                            <span class="ic-left">🪪</span>
                            <input type="text" name="username" class="form-control" placeholder="admin atau username Anda" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field-top"><label class="field-label">Kata Sandi</label></div>
                        <div class="input-icon-wrap">
                            <span class="ic-left">🔒</span>
                            <input type="password" name="password" class="form-control" placeholder="••••••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center;margin-top:0.6rem;">🔑 Masuk ke Sistem Vault</button>
                </form>

                <p class="login-footnote" style="margin-top:1.4rem;">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
        </div>
    </div>
    <p class="login-footnote">⚖️ Akses diawasi secara biometrik dan tercatat pada buku besar audit digital Permata Mini.</p>
<script>
    document.getElementById('isi-admin').addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector('input[name=username]').value = 'admin';
        document.querySelector('input[name=password]').value = 'admin';
    });
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>