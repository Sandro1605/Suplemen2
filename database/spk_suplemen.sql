-- ============================================================
-- SPK Rekomendasi Suplemen Fitness — Metode TOPSIS
-- Database Schema & Seed Data
-- ============================================================

CREATE DATABASE IF NOT EXISTS spk_suplemen
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE spk_suplemen;

-- ============================================================
-- TABEL 1: KRITERIA
-- 5 kriteria tetap yang digunakan dalam perhitungan TOPSIS
-- ============================================================
CREATE TABLE IF NOT EXISTS kriteria (
    id      VARCHAR(5) PRIMARY KEY,
    nama    VARCHAR(100) NOT NULL,
    sifat   ENUM('cost', 'benefit') NOT NULL,
    satuan  VARCHAR(30) DEFAULT NULL
) ENGINE=InnoDB;

INSERT INTO kriteria (id, nama, sifat, satuan) VALUES
('C1', 'Harga Produk',      'cost',    'Rp'),
('C2', 'Total Serving',     'benefit', 'serving'),
('C3', 'Kandungan Protein', 'benefit', 'gram'),
('C4', 'Total Kalori',      'benefit', 'kcal'),
('C5', 'Lemak Total',       'cost',    'gram');

-- ============================================================
-- TABEL 2: PRODUK SUPLEMEN
-- Data produk Whey Protein & Mass Gainer terdaftar BPOM
-- ============================================================
CREATE TABLE IF NOT EXISTS produk (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(255) NOT NULL,
    merek       VARCHAR(150) NOT NULL,
    kategori    ENUM('Whey Protein', 'Mass Gainer') NOT NULL,
    no_bpom     VARCHAR(50) NOT NULL,
    harga       DECIMAL(12,2) NOT NULL COMMENT 'Harga per kemasan (Rp)',
    serving     INT NOT NULL COMMENT 'Jumlah total takaran saji per kemasan',
    protein     DECIMAL(6,2) NOT NULL COMMENT 'Kandungan protein per serving (gram)',
    kalori      DECIMAL(8,2) NOT NULL COMMENT 'Total kalori per serving (kcal)',
    lemak       DECIMAL(6,2) NOT NULL COMMENT 'Lemak total per serving (gram)',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seed Data: Whey Protein (5 produk)
INSERT INTO produk (nama_produk, merek, kategori, no_bpom, harga, serving, protein, kalori, lemak) VALUES
('Gold Standard 100% Whey 5 Lbs',   'Optimum Nutrition', 'Whey Protein', 'ML 867009001156', 1350000, 73, 24.00, 120.00, 1.00),
('Pro Whey 100 900g',               'Muscle First',      'Whey Protein', 'ML 867009002188', 390000,  30, 24.00, 130.00, 2.00),
('Whey Protein 1 Lbs',              'Evolene',           'Whey Protein', 'MD 867031001062', 360000,  15, 25.00, 120.00, 1.50),
('ISO Whey Protein 5 Lbs',          'BPI Sports',        'Whey Protein', 'ML 867009003045', 1200000, 68, 25.00, 130.00, 1.00),
('100% Pure Whey Protein 2 Lbs',    'L-Men',             'Whey Protein', 'MD 867009001234', 450000,  27, 22.00, 125.00, 2.50);

-- Seed Data: Mass Gainer (5 produk)
INSERT INTO produk (nama_produk, merek, kategori, no_bpom, harga, serving, protein, kalori, lemak) VALUES
('Serious Mass 12 Lbs',             'Optimum Nutrition', 'Mass Gainer',  'ML 867009001289', 1500000, 16, 50.00, 1250.00, 4.50),
('Gain Mass 225g',                  'L-Men',             'Mass Gainer',  'MD 867009001567', 55000,   5,  15.00, 150.00,  1.50),
('M1 Gold Pro Gainer 6 Lbs',        'Muscle First',      'Mass Gainer',  'ML 867009002195', 650000,  19, 55.00, 700.00,  8.00),
('Massiv Gainer 12 Lbs',            'FITlife',           'Mass Gainer',  'MD 867031002078', 900000,  24, 30.00, 620.00,  5.00),
('Evomass 2 Lbs',                   'Evolene',           'Mass Gainer',  'MD 867031001085', 280000,  12, 27.00, 380.00,  3.50);

-- ============================================================
-- TABEL 3: ADMIN
-- Akun administrator untuk mengelola data produk
-- Password default: admin123 (di-hash dengan PASSWORD_DEFAULT)
-- ============================================================
CREATE TABLE IF NOT EXISTS admin (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    username  VARCHAR(50) NOT NULL UNIQUE,
    password  VARCHAR(255) NOT NULL,
    nama      VARCHAR(100) DEFAULT 'Administrator',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Password: admin123 (bcrypt hash)
INSERT INTO admin (username, password, nama) VALUES
('admin', '$2y$10$3LjZy2tflP7PLbHXEUNY1eIWufHdaT6VMCECbJg8ChIgStvCY3DUW', 'Administrator');

-- ============================================================
-- TABEL 4: USER PROFILES (Content-Based Filtering)
-- Menyimpan profil preferensi pengguna sebagai User Profile
-- dalam framework Content-Based Filtering
-- ============================================================
CREATE TABLE IF NOT EXISTS user_profiles (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nama         VARCHAR(100) NOT NULL,
    tujuan       ENUM('Bulking', 'Cutting', 'Maintenance') NOT NULL,
    kategori     ENUM('Whey Protein', 'Mass Gainer', 'Semua') DEFAULT 'Semua',
    bobot_c1     TINYINT NOT NULL DEFAULT 3 COMMENT 'Bobot Harga',
    bobot_c2     TINYINT NOT NULL DEFAULT 3 COMMENT 'Bobot Serving',
    bobot_c3     TINYINT NOT NULL DEFAULT 3 COMMENT 'Bobot Protein',
    bobot_c4     TINYINT NOT NULL DEFAULT 3 COMMENT 'Bobot Kalori',
    bobot_c5     TINYINT NOT NULL DEFAULT 3 COMMENT 'Bobot Lemak',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
