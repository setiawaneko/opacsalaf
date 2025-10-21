CREATE DATABASE perpustakaan_assalafiyyah;
USE perpustakaan_assalafiyyah;

CREATE TABLE kategori (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE buku (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(255) NOT NULL,
    penulis VARCHAR(200) NOT NULL,
    penerbit VARCHAR(200),
    tahun_terbit YEAR,
    isbn VARCHAR(20),
    kategori_id INT,
    cover_image VARCHAR(255),
    file_pdf VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    jumlah_halaman INT,
    bahasa VARCHAR(50) DEFAULT 'Indonesia',
    status ENUM('Tersedia', 'Dipinjam', 'Dalam Perbaikan') DEFAULT 'Tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
);

CREATE TABLE anggota (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_anggota VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(200) NOT NULL,
    email VARCHAR(100),
    telepon VARCHAR(15),
    alamat TEXT,
    jenis_kelamin ENUM('L', 'P'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE peminjaman (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_peminjaman VARCHAR(20) UNIQUE NOT NULL,
    buku_id INT,
    anggota_id INT,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE,
    tanggal_harus_kembali DATE NOT NULL,
    status ENUM('Dipinjam', 'Dikembalikan', 'Terlambat') DEFAULT 'Dipinjam',
    denda DECIMAL(10,2) DEFAULT 0,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE CASCADE,
    FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON DELETE CASCADE
);

-- Insert sample categories
INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Aqidah', 'Buku-buku tentang ilmu tauhid dan aqidah Islam'),
('Fiqh', 'Buku-buku tentang hukum dan fiqh Islam'),
('Tasawuf', 'Buku-buku tentang spiritualitas dan tasawuf'),
('Hadits', 'Buku-buku kumpulan hadits dan syarahnya'),
('Sejarah Islam', 'Buku-buku sejarah peradaban Islam'),
('Bahasa Arab', 'Buku-buku pembelajaran bahasa Arab');
('Pendidikan', 'Buku-buku pendidikan umum');
('Teknologi', 'Buku-buku pembelajaran teknologi');
('Kesehatan', 'Buku-buku pembelajaran kesehatan medis');
('Fiksi', 'Buku-buku fiksi');
('Non - Fiksi', 'Buku-buku non fiksi');