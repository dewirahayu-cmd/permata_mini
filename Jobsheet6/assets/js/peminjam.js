function esc(teks) {
    const d = document.createElement("div");
    d.textContent = teks;
    return d.innerHTML;
}

function kelasBadge(status) {
    if (status === "Terlambat") return "badge-danger";
    if (status === "Tersedia" || status === "Aktif") return "badge-success";
    return "badge-warning";
}

const AKSI = '<td class="text-center"><button type="button" class="icon-btn btn-edit" title="Edit">✏️</button> <button type="button" class="icon-btn danger btn-hapus" title="Hapus">🗑️</button></td>';

function buatBaris(item) {
    const late = item.status === "Terlambat" ? " late" : "";
    return '<td><div class="klien-cell"><div class="klien-avatar' + late + '">' + esc(item.inisial) + '</div><div><div class="klien-name">' + esc(item.nama) +
        '</div><div class="klien-sub">' + esc(item.kode) + " · " + esc(item.telepon) + "</div></div></div></td>" +
        '<td><span class="badge badge-warning">' + esc(item.tier) + "</span></td>" +
        '<td><div class="klien-name" style="font-weight:600;">' + esc(item.perhiasan) + '</div><div class="klien-sub">' + esc(item.id_perhiasan) + "</div></td>" +
        '<td><span class="badge ' + kelasBadge(item.status) + '">' + esc(item.status) + '</span><br><span class="klien-sub">Tempo: ' + esc(item.jatuh_tempo) + "</span></td>" +
        '<td style="text-align:right;"><div class="klien-name">' + esc(item.agunan_rp) + '</div><div class="klien-sub">' + esc(item.jenis_agunan) + "</div></td>";
}

async function muatDaftarPeminjam() {
    const tbody = document.querySelector(".table-wrap table tbody");
    const loading = document.getElementById("loading-indicator");
    if (!tbody) return;

    loading.style.display = "block";
    tbody.innerHTML = "";
    try {
        await new Promise((resolve) => setTimeout(resolve, 600)); // simulasi delay jaringan
        const res = await fetch("../data/peminjam.json");
        if (!res.ok) throw new Error("Gagal mengambil data (status " + res.status + ")");
        const daftar = await res.json();
        daftar.forEach(function (item) {
            const tr = document.createElement("tr");
            tr.innerHTML = buatBaris(item) + AKSI;
            tbody.appendChild(tr);
        });
    } catch (err) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--danger-text);font-weight:700;">Gagal memuat data: ' + esc(err.message) + "</td></tr>";
    } finally {
        loading.style.display = "none";
    }
}

document.addEventListener("DOMContentLoaded", muatDaftarPeminjam);
