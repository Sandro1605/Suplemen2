<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Rekomendasi Suplemen Fitness menggunakan metode Content-Based Filtering dan TOPSIS. Temukan Whey Protein dan Mass Gainer terbaik sesuai kebutuhanmu.">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?>Sistem Rekomendasi Suplemen Fitness</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <!-- ============ NAVBAR ============ -->
    <nav class="navbar" id="mainNavbar">
        <div class="container navbar__inner">
            <a href="<?= BASE_URL ?>" class="navbar__brand">
                <span class="navbar__icon">💪</span>
                <span class="navbar__title">Reko<span class="text-accent">Suplemen</span></span>
            </a>

            <button class="navbar__toggle" id="navToggle" aria-label="Toggle navigation">
                <span class="navbar__toggle-bar"></span>
                <span class="navbar__toggle-bar"></span>
                <span class="navbar__toggle-bar"></span>
            </button>

            <ul class="navbar__menu" id="navMenu">
                <li><a href="<?= BASE_URL ?>" class="navbar__link <?= (!isset($_GET['page']) || $_GET['page'] === 'home') ? 'active' : '' ?>">Beranda</a></li>
                <li><a href="<?= BASE_URL ?>?page=rekomendasi" class="navbar__link <?= (isset($_GET['page']) && $_GET['page'] === 'rekomendasi') ? 'active' : '' ?>">Rekomendasi</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="<?= BASE_URL ?>?page=admin/dashboard" class="navbar__link <?= (isset($_GET['page']) && in_array($_GET['page'], ['admin/dashboard', 'admin/produk'])) ? 'active' : '' ?>">Produk</a></li>
                    <li><a href="<?= BASE_URL ?>?page=admin/pengguna" class="navbar__link <?= (isset($_GET['page']) && $_GET['page'] === 'admin/pengguna') ? 'active' : '' ?>">Data Pengguna</a></li>
                    <li><a href="<?= BASE_URL ?>?page=admin/logout" class="navbar__link navbar__link--logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>?page=admin/login" class="navbar__link <?= (isset($_GET['page']) && $_GET['page'] === 'admin/login') ? 'active' : '' ?>">Admin</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- ============ MAIN CONTENT ============ -->
    <main class="main-content">
