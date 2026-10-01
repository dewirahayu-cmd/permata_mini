
CREATE TABLE IF NOT EXISTS perhiasan (
    id SERIAL PRIMARY KEY,
    kode VARCHAR(50) NOT NULL UNIQUE,
    nama VARCHAR(255) NOT NULL,
    kategori VARCHAR(50),
    material VARCHAR(255),
    stok INTEGER NOT NULL DEFAULT 0,
    stok_total INTEGER NOT NULL DEFAULT 0,
    nilai BIGINT NOT NULL DEFAULT 0,
    status VARCHAR(30) NOT NULL DEFAULT 'Tersedia',
    sertifikat VARCHAR(50),
    foto VARCHAR(255),
    deskripsi TEXT
);

CREATE TABLE IF NOT EXISTS peminjam (
    id SERIAL PRIMARY KEY,
    kode VARCHAR(50) NOT NULL UNIQUE,
    nama VARCHAR(255) NOT NULL,
    inisial VARCHAR(5),
    telepon VARCHAR(30),
    nik VARCHAR(50),
    email VARCHAR(255),
    tier VARCHAR(50),
    alamat VARCHAR(255),
    perhiasan VARCHAR(255),
    id_perhiasan VARCHAR(50),
    jatuh_tempo VARCHAR(50),
    status VARCHAR(30) NOT NULL DEFAULT 'Aktif',
    agunan BIGINT NOT NULL DEFAULT 0,
    jenis_agunan VARCHAR(255)
);

INSERT INTO perhiasan (kode, nama, kategori, material, stok, stok_total, nilai, status, sertifikat, foto, deskripsi) VALUES
('P001', 'Kalung Sovereign', 'Kalung', 'Flowerish Rose Gold', 5, 5, 72500000, 'Tersedia', 'Antam', 'menu_3.jpg', ''),
('P002', 'Anting Colombia Mutiara Putri', 'Anting', 'Crown Tiara with Akoya Pearls & Diamonds', 0, 1, 220000000, 'Terlambat', 'HRD', 'menu_8.jpg', ''),
('P003', 'Anting Zamrud Kolombia Maharani', 'Anting', 'Colombian Emerald 6ct & Yellow Gold 18K', 1, 1, 175000000, 'Tersedia', 'GIA', 'menu_4.jpg', ''),
('P004', 'Anting Safir Royal Blue Burma', 'Anting', 'Natural Sapphire 4ct & Halo Diamonds', 2, 2, 118000000, 'Tersedia', 'SSEF', 'menu_6.jpg', ''),
('P005', 'Gelang Tennis Berlian Baguette', 'Gelang', 'White Gold 18K, 5.5ct Total Weight', 1, 3, 95000000, 'Dipinjam', 'GIA', 'menu_5.jpg', ''),
('P006', 'Cincin Taman Bunga Rose', 'Cincin', 'Floral Diamond Cluster & Rose Gold 18K', 3, 4, 62000000, 'Tersedia', 'HRD', 'menu_9.jpg', ''),
('P007', 'Cincin Berlian Solitaire Pavé', 'Cincin', 'Platinum 950, VVS1 Color D 2.5ct', 2, 2, 145000000, 'Tersedia', 'GIA', 'menu_7.jpg', ''),
('P008', 'Kalung Mutiara Royal Reine', 'Kalung', 'South Sea Pearl 14mm & White Gold 18K', 0, 1, 85000000, 'Dipinjam', 'GIA', 'menu_1.jpg', '')
ON CONFLICT (kode) DO NOTHING;

INSERT INTO peminjam (kode, nama, inisial, telepon, nik, email, tier, alamat, perhiasan, id_perhiasan, jatuh_tempo, status, agunan, jenis_agunan) VALUES
('BRW-009', 'Ny. Raden Ayu Kartika Dewi', 'KD', '+62 812-8877-2291', '-', '-', 'VVIP Tier-1', '-', 'Kalung Royal Rose Solitaire', 'J-SOL-018', '28 Okt 2025', 'Aktif', 120000000, 'Bilyet Deposito BCA'),
('BRW-014', 'Dra. Fiona Wijaya, M.B.A.', 'FW', '+62 811-9234-5510', '-', '-', 'Corporate VIP', '-', 'Tiara Emerald Grand Regency', 'J-EMR-004', '25 Okt 2025', 'Terlambat', 195000000, 'Sertifikat Hak Milik (SHM)'),
('BRW-003', 'Tn. Hendra Gunawan', 'HG', '+62 818-0909-1221', '-', '-', 'Private Collector', '-', 'Jam Saku Emas Antik Patek 1912', 'W-ANT-001', '31 Okt 2025', 'Aktif', 85000000, 'Safe Deposit Lock 04'),
('BRW-021', 'Dr. Amanda Prameswari, Sp.A.', 'AP', '+62 856-4301-8900', '-', '-', 'Verified VIP', '-', '-', '-', '-', 'Bebas', 0, 'Agunan Dirilis')
ON CONFLICT (kode) DO NOTHING;