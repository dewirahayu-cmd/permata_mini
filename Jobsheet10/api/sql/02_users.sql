-- Jobsheet 10: tabel users untuk autentikasi (admin & user)

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user'
);

-- Pendaftaran publik selalu menjadi 'user'
ALTER TABLE users ALTER COLUMN role SET DEFAULT 'user';

-- Akun admin demo -> username: admin | password: admin
-- Password disimpan sebagai HASH bcrypt (bukan teks biasa), karena login memakai password_verify().
-- Bagian DO UPDATE memperbaiki akun admin lama yang passwordnya masih teks biasa.
INSERT INTO users (nama, username, password, role)
VALUES ('Admin Permata', 'admin', '$2y$10$PwE0wudx3NptxOvJ7GuNt.Y11i/aRynEFrISI3rvhlSGHXYYc4sru', 'admin')
ON CONFLICT (username) DO UPDATE
  SET nama = EXCLUDED.nama,
      password = EXCLUDED.password,
      role = 'admin';

-- Hanya akun 'admin' yang boleh berrole admin
UPDATE users SET role = 'user' WHERE username <> 'admin' AND role = 'admin';

-- Relasi pengajuan peminjam -> akun pemiliknya (untuk halaman Riwayat Saya)
DO $$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'peminjam_user_id_fkey') THEN
    ALTER TABLE peminjam
      ADD CONSTRAINT peminjam_user_id_fkey
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
  END IF;
END $$;

-- Keamanan Supabase: aktifkan RLS agar tabel tidak bisa dibaca lewat API publik.
-- Aplikasi PHP tetap berjalan normal karena terhubung langsung memakai user 'postgres'
-- (yang melewati RLS), bukan lewat API Supabase.
ALTER TABLE users ENABLE ROW LEVEL SECURITY;
ALTER TABLE perhiasan ENABLE ROW LEVEL SECURITY;
ALTER TABLE peminjam ENABLE ROW LEVEL SECURITY;

-- Pemeriksaan: kolom awal_password untuk admin harus berawalan $2y$
SELECT username, role, LEFT(password, 4) AS awal_password FROM users ORDER BY id;