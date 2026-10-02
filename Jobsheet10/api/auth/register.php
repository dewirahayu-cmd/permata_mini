<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$page_title = "Registrasi Akun";
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
                    <h1>Registrasi Akun</h1>
                    <p>Buat akun untuk mengajukan peminjaman perhiasan di Permata Mini.</p>
                </div>

                <?php if ($flash): ?>
                    <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
                <?php endif; ?>

                <form class="login-form" method="post" action="proses_register.php">
                    <div class="field-row">
                        <div class="field-top"><label class="field-label">Nama Lengkap</label></div>
                        <div class="input-icon-wrap">
                            <span class="ic-left">👤</span>
                            <input type="text" name="nama" class="form-control" placeholder="Nama lengkap Anda" maxlength="100" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field-top"><label class="field-label">Username</label></div>
                        <div class="input-icon-wrap">
                            <span class="ic-left">🪪</span>
                            <input type="text" name="username" class="form-control" placeholder="username Anda" pattern="[A-Za-z0-9_.\-]{3,30}" title="3-30 karakter: huruf, angka, titik, garis bawah, atau strip" autocomplete="username" required>
                        </div>
                        <p class="field-hint">3-30 karakter: huruf, angka, titik, garis bawah, atau strip.</p>
                    </div>
                    <div class="field-row">
                        <div class="field-top"><label class="field-label">Kata Sandi</label></div>
                        <div class="input-icon-wrap">
                            <span class="ic-left">🔒</span>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" minlength="6" maxlength="72" autocomplete="new-password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center;margin-top:0.8rem;">🔑 Daftar Akun</button>
                </form>

                <p class="login-footnote" style="margin-top:1.2rem;">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
            </div>
        </div>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>