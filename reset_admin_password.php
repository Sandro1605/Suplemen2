<?php
/**
 * Script Reset Password Admin
 * Gunakan untuk menyetel ulang password admin ke default
 * 
 * Username: admin
 * Password: admin123
 */

require_once __DIR__ . '/config/database.php';

echo "<div style='background:#1e293b;color:#94a3b8;padding:2rem;border-radius:8px;font-family:monospace;max-width:600px;margin:2rem auto;'>";
echo "<h2 style='color:#60a5fa;margin-bottom:1rem;'>Reset Password Admin</h2>";

try {
    $pdo = getConnection();
    
    // Hash password: admin123
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Update password
    $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE username = ?");
    $stmt->execute([$hashedPassword, 'admin']);
    
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:#86efac;'>✓ <strong>Berhasil!</strong></p>";
        echo "<p>Password admin telah direset ke:</p>";
        echo "<ul style='background:#0f172a;padding:1rem;border-radius:4px;'>";
        echo "<li><strong>Username:</strong> admin</li>";
        echo "<li><strong>Password:</strong> admin123</li>";
        echo "</ul>";
    } else {
        echo "<p style='color:#fca5a5;'>✗ User 'admin' tidak ditemukan</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:#fca5a5;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p style='margin-top:2rem;text-align:center;'>";
echo "<a href='?page=admin/login' style='color:#60a5fa;text-decoration:none;'>→ Ke Halaman Login</a>";
echo "</p>";
echo "</div>";
