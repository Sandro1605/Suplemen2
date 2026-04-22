<?php
/**
 * Halaman Login Admin
 * Rendered WITHOUT header/footer (standalone layout)
 */
if (session_status() === PHP_SESSION_NONE) session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ' . BASE_URL . '?page=admin/dashboard');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getConnection();
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $result = loginAdmin($pdo, $username, $password);
    if ($result['success']) {
        header('Location: ' . BASE_URL . '?page=admin/dashboard');
        exit;
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — SPK Suplemen Fitness</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-card__logo"><span>🔐</span></div>
            <h1 class="login-card__title">Admin Login</h1>
            <p class="login-card__desc">Masuk untuk mengelola data produk suplemen.</p>

            <?php if ($error): ?>
                <div class="alert alert--error">⚠️ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>?page=admin/login">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn--primary btn--block" style="margin-top:0.5rem;">Masuk</button>
            </form>

            <p style="text-align:center;margin-top:1.5rem;">
                <a href="<?= BASE_URL ?>" style="font-size:0.85rem;color:var(--text-muted);">← Kembali ke Beranda</a>
            </p>
        </div>
    </div>
</body>
</html>
