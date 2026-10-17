// app.js - Jobsheet 5: interaktivitas DOM & event Permata Mini.
// Tiap fungsi diawali guard clause (if (!elemen) return) supaya satu file aman di semua halaman.

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initStatCards();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
});

// 1. Menu hamburger: klik tombol -> menu buka/tutup, ikon berubah jadi X
function initNavToggle() {
    const btn = document.getElementById("nav-toggle-btn");
    const nav = document.getElementById("nav-menu");
    if (!btn || !nav) return;
    btn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
        btn.classList.toggle("is-active");
    });
}

// 2. Kartu ringkasan: klik -> bentuk & warna berubah (toggle kelas stat-active)
function initStatCards() {
    document.querySelectorAll(".stat-card").forEach(function (card) {
        card.addEventListener("click", function () {
            card.classList.toggle("stat-active");
        });
    });
}

// 3. Tombol Hapus: konfirmasi dulu, kalau OK baris dihapus dari tampilan
function initHapusConfirm() {
    document.querySelectorAll(".btn-hapus").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const row = btn.closest("tr");
            const nama = row.querySelector(".klien-name").textContent;
            if (confirm('Yakin ingin menghapus "' + nama + '"?')) {
                row.remove();
            }
        });
    });
}

// 4. Filter tabel: ketik di kolom cari -> baris yang tidak cocok disembunyikan
function initTableFilter() {
    const input = document.getElementById("search-input");
    if (!input) return;
    input.addEventListener("keyup", function () {
        const kata = input.value.trim().toLowerCase();
        document.querySelectorAll("tbody tr").forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(kata) ? "" : "none";
        });
    });
}

// 5. Validasi form (client-side)
function tampilkanError(input, pesan) {
    hapusError(input);
    const span = document.createElement("span");
    span.className = "error";
    span.textContent = pesan;
    input.classList.add("is-invalid");
    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    input.classList.remove("is-invalid");
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error")) next.remove();
}

function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;

        form.querySelectorAll("[required]").forEach(function (input) {
            hapusError(input);
            if (input.value.trim() === "") {
                tampilkanError(input, "Kolom ini wajib diisi.");
                valid = false;
            }
        });

        const stok = form.querySelector("[name='stok']");
        if (stok && stok.value.trim() !== "" && !(parseInt(stok.value, 10) >= 0)) {
            tampilkanError(stok, "Stok tidak boleh negatif.");
            valid = false;
        }
        const nilai = form.querySelector("[name='nilai']");
        if (nilai && nilai.value.trim() !== "" && !(parseInt(nilai.value, 10) > 0)) {
            tampilkanError(nilai, "Nilai aset harus lebih dari 0.");
            valid = false;
        }

        // Format kode: P001 (perhiasan) atau BRW-001 (peminjam)
        const kode = form.querySelector("[name='kode']");
        if (kode && kode.value.trim() !== "") {
            const formPeminjam = !!form.querySelector("[name='telepon']");
            const pola = formPeminjam ? /^BRW-\d{3}$/ : /^P\d{3}$/;
            if (!pola.test(kode.value.trim())) {
                tampilkanError(kode, "Format kode tidak sesuai (contoh: " + (formPeminjam ? "BRW-022" : "P009") + ").");
                valid = false;
            }
        }

        e.preventDefault();
        if (valid) {
            alert("Data valid. (Simulasi: belum dikirim ke server. Penyimpanan ke database ada di Jobsheet 7 dst.)");
        }
    });

    form.querySelectorAll("input, select, textarea").forEach(function (input) {
        input.addEventListener("input", function () { hapusError(input); });
    });
}
