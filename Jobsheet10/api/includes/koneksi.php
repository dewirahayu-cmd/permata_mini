<?php
// Konfigurasi database.
// Di hosting (mis. Vercel) isi lewat Environment Variables: DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS.
// Nilai setelah "?:" hanyalah cadangan agar tetap jalan saat pengembangan.
$host = getenv('DB_HOST') ?: 'aws-0-ap-southeast-2.pooler.supabase.com'; // Dari Connection parameters Supabase
$port = getenv('DB_PORT') ?: '6543';
$db   = getenv('DB_NAME') ?: 'postgres';
$user = getenv('DB_USER') ?: 'postgres.aeyvrvkoltypcsumhalj';
$pass = getenv('DB_PASS') ?: 'p0stgr3sdewi';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db;sslmode=require", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Wajib untuk Supabase pooler (pgbouncer mode transaction, port 6543):
        // prepared statement asli tidak aman dipakai lewat pooler.
        PDO::ATTR_EMULATE_PREPARES => true,
    ]);
} catch (PDOException $e) {
    error_log('Koneksi database gagal: ' . $e->getMessage());
    http_response_code(500);
    die("Koneksi database gagal. Silakan coba beberapa saat lagi.");
}

// ===== Fungsi bantu (dipakai modul peminjam & perhiasan) =====

// Inisial dari nama: abaikan gelar (kata berakhiran titik) dan gelar belakang setelah koma.
function buatInisial($nama) {
    $nama = trim(explode(',', $nama)[0]);
    $kata = array_values(array_filter(preg_split('/\s+/', $nama), function ($k) {
        return $k !== '' && substr($k, -1) !== '.';
    }));
    if (empty($kata)) {
        return strtoupper(substr($nama, 0, 2));
    }
    $awal = strtoupper(substr($kata[0], 0, 1));
    $akhir = count($kata) > 1 ? strtoupper(substr(end($kata), 0, 1)) : strtoupper(substr($kata[0], 1, 1));
    return $awal . $akhir;
}

// Tanggal jatuh tempo (hari ini + N hari) dalam format "28 Okt 2025".
function tanggalJatuhTempo($hari) {
    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $d = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    $d->modify('+' . (int) $hari . ' days');
    return $d->format('j') . ' ' . $bulan[(int) $d->format('n') - 1] . ' ' . $d->format('Y');
}

// Hitung ulang stok & status perhiasan berdasarkan data peminjam yang sedang meminjamnya.
// Hanya status 'Aktif' dan 'Terlambat' yang dihitung sebagai "sedang dipinjam".
function sinkronPerhiasan(PDO $pdo, $kodePerhiasan) {
    if (!$kodePerhiasan || $kodePerhiasan === '-') {
        return;
    }
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) AS dipinjam, COUNT(*) FILTER (WHERE status = 'Terlambat') AS telat
         FROM peminjam WHERE id_perhiasan = :k AND status IN ('Aktif', 'Terlambat')"
    );
    $stmt->execute(['k' => $kodePerhiasan]);
    $hit = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT stok_total, stok FROM perhiasan WHERE kode = :k");
    $stmt->execute(['k' => $kodePerhiasan]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$p) {
        return;
    }

    $dipinjam = (int) $hit['dipinjam'];
    $total = max((int) $p['stok_total'], $dipinjam);
    $stok = $total - $dipinjam;
    $status = (int) $hit['telat'] > 0 ? 'Terlambat' : ($dipinjam > 0 ? 'Dipinjam' : 'Tersedia');

    $upd = $pdo->prepare("UPDATE perhiasan SET stok = :stok, stok_total = :total, status = :status WHERE kode = :k");
    $upd->execute(['stok' => $stok, 'total' => $total, 'status' => $status, 'k' => $kodePerhiasan]);
}

// Upload berkas (foto/KTP/bukti). Mengembalikan [namaFileBaru|null, pesanError|null].
// Tanpa file yang dipilih => [null, null] (upload bersifat opsional).
function uploadBerkas($field, $folderTujuan, $prefix, array $extBoleh, $maxByte = 2097152) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }
    $f = $_FILES[$field];
    if ($f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE || $f['size'] > $maxByte) {
        return [null, "Ukuran berkas terlalu besar (maks. " . round($maxByte / 1048576) . " MB)."];
    }
    if ($f['error'] !== UPLOAD_ERR_OK) {
        return [null, "Berkas gagal diunggah."];
    }
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $extBoleh, true)) {
        return [null, "Format berkas harus " . strtoupper(implode(', ', $extBoleh)) . "."];
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'], true)) {
        return [null, "Isi berkas tidak valid."];
    }
    if (!is_dir($folderTujuan) && !mkdir($folderTujuan, 0775, true)) {
        return [null, "Folder tujuan upload tidak bisa dibuat."];
    }
    $nama = preg_replace('/[^A-Za-z0-9_-]/', '', $prefix) . '_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], rtrim($folderTujuan, '/') . '/' . $nama)) {
        return [null, "Berkas gagal disimpan."];
    }
    return [$nama, null];
}
?>