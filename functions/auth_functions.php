<?php
/**
 * Fungsi Autentikasi Admin
 * SPK Rekomendasi Suplemen Fitness
 * 
 * Login/logout sederhana berbasis session PHP
 */

// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Login admin dengan username dan password
 * 
 * @param PDO    $pdo      Instance koneksi database
 * @param string $username Username admin
 * @param string $password Password plain text
 * @return array ['success' => bool, 'message' => string]
 */
function loginAdmin($pdo, $username, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Set session
            $_SESSION['admin_id']       = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nama']     = $admin['nama'];
            $_SESSION['admin_logged_in'] = true;

            return ['success' => true, 'message' => 'Login berhasil!'];
        }

        return ['success' => false, 'message' => 'Username atau password salah.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Terjadi kesalahan sistem.'];
    }
}

/**
 * Cek apakah admin sudah login
 * Redirect ke halaman login jika belum
 * 
 * @return bool
 */
function checkAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: ' . BASE_URL . '?page=admin/login');
        exit;
    }
    return true;
}

/**
 * Cek status login tanpa redirect
 * 
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Logout admin — hapus session
 */
function logout() {
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    header('Location: ' . BASE_URL . '?page=admin/login');
    exit;
}
?>
