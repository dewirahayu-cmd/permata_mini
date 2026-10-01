</main>

<?php if (!empty($show_full_footer)): ?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <div class="footer-brand"><span class="brand-icon"></span><span>Permata Mini</span></div>
            <p class="footer-tagline">Sistem Peminjaman Perhiasan Eksklusif & Tata Kelola Vault</p>
            <p class="footer-desc">Platform terverifikasi untuk tata kelola aset perhiasan murni, kurasi berlian bersertifikasi, dan otorisasi peminjaman bernilai tinggi.</p>
        </div>
        <div class="footer-col">
            <h4>Jam Layanan</h4>
            <div class="footer-row"><b>Senin - Jumat</b>09:00 - 18:00 WIB</div>
            <div class="footer-row"><b>Sabtu</b>09:00 - 15:00 WIB</div>
            <div class="footer-row"><b>Minggu & Libur</b>Tutup</div>
        </div>
        <div class="footer-col">
            <h4>Kontak Kami</h4>
            <div class="footer-contact">📍 Grand Hyatt Arcade Level 3, Jakarta Pusat</div>
            <div class="footer-contact">✉️ concierge@permata-mini.id</div>
            <div class="footer-contact">📞 +62 21 390 8822</div>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>Copyright © 2026 Permata Mini. Hak Cipta Dilindungi.</span>
        <div><a href="#">Kebijakan Privasi</a><a href="#">Syarat & Ketentuan</a></div>
    </div>
</footer>
<?php else: ?>
<footer class="site-footer"><div class="container footer-bottom"><span>Copyright © 2026 Permata Mini.</span></div></footer>
<?php endif; ?>

<script src="<?php echo $base; ?>assets/js/app.js"></script>
<?php if (!empty($extra_scripts)): foreach ($extra_scripts as $src): ?>
<script src="<?php echo $src; ?>"></script>
<?php endforeach;
endif; ?>
</body>
</html>
