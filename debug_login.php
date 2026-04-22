<?php
/**
 * Script Debug Login
 * Membantu mengidentifikasi masalah autentikasi
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/auth_functions.php';

echo "<pre style='background:#1e293b;color:#94a3b8;padding:1rem;border-radius:8px;font-family:monospace;'>";
echo "=== DEBUG LOGIN SYSTEM ===\n\n";

// 1. Cek koneksi database
echo "1. KONEKSI DATABASE:\n";
try {
    $pdo = getConnection();
    echo "   ✓ Koneksi berhasil\n\n";
} catch (Exception $e) {
    echo "   ✗ Koneksi gagal: " . $e->getMessage() . "\n\n";
    exit;
}

// 2. Cek tabel admin ada
echo "2. TABEL ADMIN:\n";
$stmt = $pdo->query("SELECT COUNT(*) FROM admin");
$count = $stmt->fetchColumn();
echo "   Total admin: $count\n\n";

// 3. Tampilkan data admin (tanpa password)
echo "3. DATA ADMIN (USERNAME ONLY):\n";
$stmt = $pdo->query("SELECT id, username, nama FROM admin");
$admins = $stmt->fetchAll();
foreach ($admins as $admin) {
    echo "   - ID: {$admin['id']}, Username: {$admin['username']}, Nama: {$admin['nama']}\n";
}
echo "\n";

// 4. Test password hash
echo "4. PASSWORD HASH TEST:\n";
$testPassword = 'admin123';
$correctHash = '$2y$10$3LjZy2tflP7PLbHXEUNY1eIWufHdaT6VMCECbJg8ChIgStvCY3DUW';
$isMatch = password_verify($testPassword, $correctHash);
echo "   Password 'admin123' matches expected hash: " . ($isMatch ? "✓ YA" : "✗ TIDAK") . "\n\n";

// 5. Ambil hash yang ada di database
echo "5. HASH DI DATABASE:\n";
$stmt = $pdo->prepare("SELECT username, password FROM admin WHERE username = ?");
$stmt->execute(['admin']);
$admin = $stmt->fetch();
if ($admin) {
    echo "   Username: {$admin['username']}\n";
    echo "   Hash: {$admin['password']}\n";
    $isMatch = password_verify($testPassword, $admin['password']);
    echo "   Password 'admin123' matches: " . ($isMatch ? "✓ YA" : "✗ TIDAK") . "\n\n";
} else {
    echo "   ✗ User 'admin' tidak ditemukan\n\n";
}

// 6. Coba login
echo "6. SIMULASI LOGIN:\n";
$result = loginAdmin($pdo, 'admin', 'admin123');
echo "   Result: " . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "=== END DEBUG ===\n";
echo "</pre>";
echo "<p style='text-align:center;margin-top:2rem;'><a href='?page=admin/login' style='color:#3b82f6;'>← Kembali ke Login</a></p>";
