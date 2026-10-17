# Wireframe & User Flow — Permata Mini (Jobsheet 4)

Dokumen rancangan untuk fitur yang belum dikoding. HTML/CSS tetap sama dengan Jobsheet 3A.

## 1. Aktor

| Aktor | Hak akses |
|---|---|
| **Tamu** | Hanya melihat Beranda dan Daftar Perhiasan (katalog). |
| **User** | Login, mengajukan peminjaman, melihat Riwayat Saya. |
| **Admin** | Login, CRUD perhiasan & peminjam, memproses pengembalian. |

## 2. Wireframe (ASCII)

Konvensi: `+--+` kotak, `[______]` input, `[ Teks ]` tombol.

### 2.1 Login
```
+--------------------------------------------+
| Permata Mini      Beranda  Perhiasan  Daftar|
+--------------------------------------------+
|        +------------------------------+    |
|        |        Masuk Akun            |    |
|        | Username  [________________] |    |
|        | Password  [________________] |    |
|        |        [ Masuk ]             |    |
|        | Belum punya akun? Daftar     |    |
|        +------------------------------+    |
+--------------------------------------------+
| (c) 2026 Permata Mini                      |
+--------------------------------------------+
```

### 2.2 Dashboard Admin
```
+--------------------------------------------------------+
| Permata Mini  Beranda Perhiasan Peminjam Pengembalian  |
|                               Admin Permata [ Logout ] |
+--------------------------------------------------------+
| +------------+ +------------+ +------------+           |
| |Total Koleksi| |Sedang Pinjam| |Terlambat  |           |
| |     8      | |     3      | |     1      |           |
| +------------+ +------------+ +------------+           |
| Aksi Cepat: [ + Perhiasan ] [ + Peminjam ] [ Kembalikan ]|
| Transaksi Terbaru                                      |
| +-------+------------------+------------+-----------+  |
| | Kode  | Peminjam         | Perhiasan  | Status    |  |
| | BRW-009| Kartika Dewi    | P008       | Aktif     |  |
| | BRW-014| Fiona Wijaya    | P002       | Terlambat |  |
| +-------+------------------+------------+-----------+  |
+--------------------------------------------------------+
```

### 2.3 Form Pengajuan Peminjaman (User)
```
+------------------------------------------+
| Ajukan Peminjaman                        |
| Nama      [______________]  Telepon [____]|
| Email     [______________]               |
| Perhiasan [ P-- pilih (stok > 0) v ]     |
| Lama      [ 7 hari v ]                   |
| [x] Data yang saya berikan benar         |
|        [ Batal ]   [ Kirim Pengajuan ]   |
+------------------------------------------+
```

### 2.4 Form Pengembalian (Admin)
```
+------------------------------------------+
| Pengembalian Perhiasan                   |
| Transaksi [ BRW-009 - Kartika Dewi v ]   |
| Perhiasan : Kalung Mutiara Royal Reine   |
| Jatuh tempo: 28 Okt 2025                 |
| Kondisi   [ Baik v ]                     |
|        [ Batal ]   [ Simpan Pengembalian]|
+------------------------------------------+
```

### 2.5 Riwayat Saya (User)
```
+------------------------------------------------------+
| Riwayat Peminjaman              [ + Ajukan Baru ]    |
| [Total 3] [Dipinjam 1] [Terlambat 0] [Kembali 2]     |
| +------------+---------+------------+-----------+    |
| | Perhiasan  | Kode    | Jatuh Tempo| Status    |    |
| +------------+---------+------------+-----------+    |
+------------------------------------------------------+
```

## 3. User Flow

### 3.1 Peminjaman
```
Mulai -> Buka Daftar Perhiasan
  -> Sudah login?
       Tidak -> Halaman Login -> kredensial benar?
                   Tidak -> pesan "Username atau password salah" -> kembali ke Login
                   Ya    -> lanjut
       Ya    -> lanjut
  -> Form Pengajuan Peminjaman
  -> Punya pinjaman Terlambat?
       Ya    -> tolak: "Selesaikan pinjaman terlambat dahulu" -> Riwayat Saya
       Tidak -> pilih perhiasan
  -> Stok perhiasan > 0?
       Tidak -> tolak: "Stok habis" -> pilih perhiasan lain
       Ya    -> Simpan -> status Aktif, stok berkurang 1 -> Riwayat Saya
```

### 3.2 Pengembalian
```
Mulai -> Login Admin -> Dashboard -> Form Pengembalian
  -> Pilih transaksi (status Aktif/Terlambat)
  -> Ada transaksi aktif?
       Tidak -> tampilkan "Tidak ada pinjaman aktif" -> selesai
       Ya    -> Simpan -> status Dikembalikan, stok bertambah 1
  -> Terlambat?
       Ya    -> catat keterlambatan di riwayat
       Tidak -> selesai
```

## 4. Edge Case

- Perhiasan stok 0 tidak boleh dipilih di form peminjaman.
- User dengan pinjaman Terlambat tidak boleh meminjam lagi.
- User tidak boleh meminjam perhiasan yang sama dua kali sekaligus.
- Perhiasan yang masih dipinjam tidak boleh dihapus.
- Username sudah dipakai saat registrasi.
- Tamu yang membuka halaman user/admin diarahkan ke Login.

## 5. Konsistensi dengan Desain yang Sudah Berjalan

- Warna maroon `#3b0d18` dan emas `#d4af37` mengikuti `assets/css/style.css`.
- Navbar, kartu statistik (grid 3 kolom), tabel zebra, dan badge status (Tersedia/Aktif hijau, Dipinjam kuning, Terlambat merah) dipakai ulang.
- Breakpoint 768px dan 480px tetap; menu hamburger tampil di layar kecil.

## 6. Infografis

Bila dosen meminta, siapkan `Infografis.png` berisi ringkasan aktor, dua user flow, dan edge case di atas.
