<?php
/**
 * Konfigurasi Koneksi Database
 * SPK Rekomendasi Suplemen Fitness — Metode TOPSIS
 * 
 * Menggunakan PDO untuk koneksi aman ke MySQL (Laragon default)
 */

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'spk_suplemen');
define('DB_USER', 'root');
define('DB_PASS', '');       // Default Laragon: kosong
define('DB_CHARSET', 'utf8mb4');

/**
 * Membuat koneksi PDO ke database MySQL
 * @return PDO Instance koneksi database
 */
function getConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Tampilkan pesan error yang user-friendly
            die('<div style="font-family:sans-serif;padding:2rem;background:#1e293b;color:#f87171;border-radius:8px;margin:2rem;">
                <h2>⚠️ Koneksi Database Gagal</h2>
                <p>Pastikan MySQL sudah berjalan di Laragon dan database <code>spk_suplemen</code> sudah dibuat.</p>
                <p style="color:#94a3b8;font-size:0.85rem;">Detail: ' . htmlspecialchars($e->getMessage()) . '</p>
            </div>');
        }
    }
    
    return $pdo;
}

// Base URL untuk navigasi — auto-detect virtual host vs subfolder
$detectedBase = '/Suplemen/';
if (isset($_SERVER['HTTP_HOST'])) {
    $host = strtolower($_SERVER['HTTP_HOST']);
    // Laragon virtual host: suplemen.test → root is /
    if (strpos($host, 'suplemen.test') !== false || strpos($host, 'suplemen.local') !== false) {
        $detectedBase = '/';
    }
}
define('BASE_URL', $detectedBase);
?>
