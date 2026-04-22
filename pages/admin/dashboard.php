<?php
/**
 * Dashboard Admin — CRUD Produk Suplemen
 */
checkAuth();
$pdo = getConnection();

// Flash message
$flash = null;
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$totalProduk   = countProduk($pdo);
$totalWhey     = countProduk($pdo, 'Whey Protein');
$totalGainer   = countProduk($pdo, 'Mass Gainer');
$totalProfiles = countUserProfiles($pdo);
$produkList    = getAllProduk($pdo);
?>

<div class="admin-page">
    <div class="container">
        <div class="admin-header">
            <div>
                <h1 class="admin-header__title">📋 Dashboard Admin</h1>
                <p class="admin-header__subtitle">Selamat datang, <?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?>!</p>
            </div>
            <div class="admin-toolbar">
                <input type="text" id="adminSearch" class="search-input" placeholder="🔍 Cari produk...">
                <a href="<?= BASE_URL ?>?page=admin/produk" class="btn btn--primary btn--sm">+ Tambah Produk</a>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert--<?= $flash['success'] ? 'success' : 'error' ?>">
                <?= $flash['success'] ? '✅' : '⚠️' ?> <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-card__number"><?= $totalProduk ?></div>
                <div class="stat-card__label">Total Produk</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__number"><?= $totalWhey ?></div>
                <div class="stat-card__label">Whey Protein</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__number"><?= $totalGainer ?></div>
                <div class="stat-card__label">Mass Gainer</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__number"><?= $totalProfiles ?></div>
                <div class="stat-card__label">User Profiles</div>
            </div>
        </div>

        <!-- Tabel Produk -->
        <div class="table-wrap">
            <table class="table" id="produkTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Produk</th>
                        <th>Merek</th>
                        <th>Kategori</th>
                        <th>No. BPOM</th>
                        <th>Harga</th>
                        <th>Serving</th>
                        <th>Protein</th>
                        <th>Kalori</th>
                        <th>Lemak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produkList)): ?>
                        <tr><td colspan="11" class="text-center" style="padding:2rem;color:var(--text-muted);">Belum ada data produk.</td></tr>
                    <?php else: ?>
                        <?php foreach ($produkList as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= htmlspecialchars($p['nama_produk']) ?></strong></td>
                            <td><?= htmlspecialchars($p['merek']) ?></td>
                            <td>
                                <span class="kategori-badge kategori-badge--<?= $p['kategori']==='Whey Protein'?'whey':'gainer' ?>">
                                    <?= $p['kategori'] ?>
                                </span>
                            </td>
                            <td><small><?= htmlspecialchars($p['no_bpom']) ?></small></td>
                            <td class="harga-col">Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                            <td><?= $p['serving'] ?></td>
                            <td><?= $p['protein'] ?>g</td>
                            <td><?= number_format($p['kalori'], 0) ?></td>
                            <td><?= $p['lemak'] ?>g</td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?= BASE_URL ?>?page=admin/produk&id=<?= $p['id'] ?>" class="btn btn--outline btn--sm">✏️</a>
                                    <button type="button" class="btn btn--danger btn--sm" onclick="showDeleteModal(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nama_produk'])) ?>')">🗑️</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h3 class="modal__title">🗑️ Hapus Produk</h3>
        <p class="modal__text">Apakah kamu yakin ingin menghapus <strong id="deleteProductName"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal__actions">
            <button type="button" class="btn btn--outline btn--sm" onclick="hideDeleteModal()">Batal</button>
            <a href="#" id="deleteForm" class="btn btn--danger btn--sm">Ya, Hapus</a>
        </div>
    </div>
</div>
