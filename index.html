<?php
/**
 * Sistem Rekomendasi Suplemen Fitness — Router Utama
 * Content-Based Filtering + TOPSIS
 * Semua request masuk melalui file ini dan di-route ke halaman yang sesuai.
 */

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load config & functions
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/auth_functions.php';
require_once __DIR__ . '/functions/produk_functions.php';
require_once __DIR__ . '/functions/topsis_logic.php';

// Ambil halaman yang diminta
$page = isset($_GET['page']) ? trim($_GET['page']) : 'home';

// Handle logout
if ($page === 'admin/logout') {
    logout(); // redirect ke login
    exit;
}

// Handle delete action
if ($page === 'admin/delete' && isset($_GET['id'])) {
    checkAuth();
    $pdo = getConnection();
    $result = deleteProduk($pdo, (int) $_GET['id']);
    $_SESSION['flash'] = $result;
    header('Location: ' . BASE_URL . '?page=admin/dashboard');
    exit;
}

// Daftar halaman yang valid
$validPages = [
    'home'             => 'pages/home.php',
    'rekomendasi'      => 'pages/rekomendasi.php',
    'admin/login'      => 'pages/admin/login.php',
    'admin/dashboard'  => 'pages/admin/dashboard.php',
    'admin/produk'     => 'pages/admin/form_produk.php',
    'admin/pengguna'   => 'pages/admin/pengguna.php',
];

// Cek apakah halaman valid
if (!array_key_exists($page, $validPages)) {
    $page = 'home';
}

// Login page: tanpa header/footer
if ($page === 'admin/login') {
    include __DIR__ . '/' . $validPages[$page];
    exit;
}

// Set page title
$pageTitles = [
    'home'             => '',
    'rekomendasi'      => 'Rekomendasi',
    'admin/dashboard'  => 'Dashboard Admin',
    'admin/produk'     => 'Kelola Produk',
    'admin/pengguna'   => 'Data Pengguna',
];
$pageTitle = isset($pageTitles[$page]) ? $pageTitles[$page] : 'SPK Suplemen';

// Render halaman dengan layout
include __DIR__ . '/includes/header.php';
include __DIR__ . '/' . $validPages[$page];
include __DIR__ . '/includes/footer.php';
?>
