document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initValidasiForm();
    initTableFilter();
    initStatCards();
});

function initNavToggle() {
    const btn = document.getElementById("nav-toggle-btn");
    const nav = document.getElementById("nav-menu");
    if (!btn || !nav) return;
    btn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
        btn.classList.toggle("is-active");
    });
}

/* Kartu Ringkasan */
function initStatCards() {
    document.querySelectorAll(".stat-card").forEach(function (card) {
        card.addEventListener("click", function () {
            card.classList.toggle("stat-active");
        });
    });
}

function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;
        form.querySelectorAll("[required]").forEach(function (field) {
            hapusError(field);
            if (field.value.trim() === "") {
                tampilkanError(field, "Kolom ini wajib diisi.");
                valid = false;
            }
        });
        if (!valid) e.preventDefault();
    });

    form.querySelectorAll("input, textarea, select").forEach(function (field) {
        field.addEventListener("input", function () { hapusError(field); });
    });
}

function tampilkanError(field, pesan) {
    hapusError(field);
    const el = document.createElement("span");
    el.className = "error";
    el.textContent = pesan;
    field.classList.add("is-invalid");
    field.insertAdjacentElement("afterend", el);
}

function hapusError(field) {
    field.classList.remove("is-invalid");
    const next = field.nextElementSibling;
    if (next && next.classList.contains("error")) next.remove();
}

function initTableFilter() {
    const input = document.getElementById("search-input");
    if (!input) return;
    const tbody = document.querySelector(".data-table tbody, .product-grid");
    if (!tbody) return;

    input.addEventListener("keyup", function () {
        const kata = input.value.trim().toLowerCase();
        const items = tbody.tagName === "TBODY" ? tbody.querySelectorAll("tr") : tbody.querySelectorAll(".product-card");
        items.forEach(function (el) {
            el.style.display = el.textContent.toLowerCase().includes(kata) ? "" : "none";
        });
    });
}

/* Fungsi fetch generik dipakai perhiasan.js & peminjam.js */
async function muatData(config) {
    const { url, containerSelector, colspan, renderItem, mode, delayMs } = config;
    const container = document.querySelector(containerSelector);
    const loading = document.getElementById("loading-indicator");
    if (!container) return;

    if (loading) loading.style.display = "block";
    container.innerHTML = "";

    try {
        await new Promise((resolve) => setTimeout(resolve, delayMs || 800));
        const res = await fetch(url);
        if (!res.ok) throw new Error("Gagal mengambil data (status " + res.status + ")");
        const data = await res.json();

        if (data.length === 0) {
            container.innerHTML = mode === "table"
                ? `<tr><td colspan="${colspan}" style="text-align:center;padding:2rem;color:var(--ink-mute);">Belum ada data.</td></tr>`
                : `<p style="text-align:center;padding:2rem;color:var(--ink-mute);">Belum ada data.</p>`;
            return;
        }

        data.forEach(function (item) {
            if (mode === "table") {
                const tr = document.createElement("tr");
                tr.innerHTML = renderItem(item);
                container.appendChild(tr);
            } else {
                const div = document.createElement("div");
                div.className = "product-card";
                div.innerHTML = renderItem(item);
                container.appendChild(div);
            }
        });
        initTableFilter();
    } catch (err) {
        container.innerHTML = mode === "table"
            ? `<tr><td colspan="${colspan}" style="text-align:center;padding:2rem;color:var(--danger-text);font-weight:700;">Gagal memuat data: ${err.message}</td></tr>`
            : `<p style="text-align:center;padding:2rem;color:var(--danger-text);font-weight:700;">Gagal memuat data: ${err.message}</p>`;
    } finally {
        if (loading) loading.style.display = "none";
    }
}

document.addEventListener("click", function (e) {
    const btnHapus = e.target.closest(".btn-hapus");
    if (btnHapus) {
        const row = btnHapus.closest("tr") || btnHapus.closest(".product-card");
        const nama = row ? (row.querySelector(".klien-name, .product-name")?.textContent.trim() || "data ini") : "data ini";
        if (confirm(`Yakin ingin menghapus "${nama}"?`)) {
            row.classList.add("fade-out");
            setTimeout(() => row.remove(), 400);
        }
    }
    const btnReload = e.target.closest(".btn-reload");
    if (btnReload && window.__reloadHandler) window.__reloadHandler();
});