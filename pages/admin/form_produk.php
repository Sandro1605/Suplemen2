<?php
/**
 * Form Tambah / Edit Produk Suplemen
 */
checkAuth();
$pdo = getConnection();

$isEdit = false;
$produk = [
    'nama_produk' => '', 'merek' => '', 'kategori' => 'Whey Protein',
    'no_bpom' => '', 'harga' => '', 'serving' => '', 'protein' => '',
    'kalori' => '', 'lemak' => ''
];

// Jika mode edit (ada parameter id)
if (isset($_GET['id']) && (int)$_GET['id'] > 0) {
    $isEdit = true;
    $data = getProdukById($pdo, (int)$_GET['id']);
    if ($data) {
        $produk = $data;
    } else {
        $_SESSION['flash'] = ['success' => false, 'message' => 'Produk tidak ditemukan.'];
        header('Location: ' . BASE_URL . '?page=admin/dashboard');
        exit;
    }
}

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'nama_produk' => $_POST['nama_produk'] ?? '',
        'merek'       => $_POST['merek'] ?? '',
        'kategori'    => $_POST['kategori'] ?? 'Whey Protein',
        'no_bpom'     => $_POST['no_bpom'] ?? '',
        'harga'       => $_POST['harga'] ?? 0,
        'serving'     => $_POST['serving'] ?? 0,
        'protein'     => $_POST['protein'] ?? 0,
        'kalori'      => $_POST['kalori'] ?? 0,
        'lemak'       => $_POST['lemak'] ?? 0,
    ];

    if ($isEdit) {
        $result = updateProduk($pdo, (int)$_GET['id'], $formData);
    } else {
        $result = createProduk($pdo, $formData);
    }

    $_SESSION['flash'] = $result;
    header('Location: ' . BASE_URL . '?page=admin/dashboard');
    exit;
}
?>

<div class="form-page">
    <div class="container">
        <div class="form-card">
            <h1 class="form-card__title"><?= $isEdit ? '✏️ Edit Produk' : '➕ Tambah Produk Baru' ?></h1>
            <p class="form-card__desc"><?= $isEdit ? 'Perbarui data produk suplemen.' : 'Masukkan informasi produk suplemen baru.' ?></p>

            <form method="POST" id="formProduk" action="<?= BASE_URL ?>?page=admin/produk<?= $isEdit ? '&id='.$produk['id'] : '' ?>">
                <div class="form-grid">
                    <div class="form-group form-group--full">
                        <label for="nama_produk">Nama Produk <span class="required">*</span></label>
                        <input type="text" id="nama_produk" name="nama_produk" class="form-control" 
                               value="<?= htmlspecialchars($produk['nama_produk']) ?>" placeholder="Contoh: Gold Standard 100% Whey 5 Lbs" required>
                    </div>

                    <div class="form-group">
                        <label for="merek">Merek <span class="required">*</span></label>
                        <input type="text" id="merek" name="merek" class="form-control" 
                               value="<?= htmlspecialchars($produk['merek']) ?>" placeholder="Contoh: Optimum Nutrition" required>
                    </div>

                    <div class="form-group">
                        <label for="kategori">Kategori <span class="required">*</span></label>
                        <div class="select-wrap">
                            <select id="kategori" name="kategori" class="form-control" required>
                                <option value="Whey Protein" <?= $produk['kategori']==='Whey Protein'?'selected':'' ?>>Whey Protein</option>
                                <option value="Mass Gainer" <?= $produk['kategori']==='Mass Gainer'?'selected':'' ?>>Mass Gainer</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group form-group--full">
                        <label for="no_bpom">No. Registrasi BPOM <span class="required">*</span></label>
                        <input type="text" id="no_bpom" name="no_bpom" class="form-control" 
                               value="<?= htmlspecialchars($produk['no_bpom']) ?>" placeholder="Contoh: ML 867009001156" required>
                    </div>

                    <div class="form-group">
                        <label for="harga">Harga per Kemasan (Rp) <span class="required">*</span></label>
                        <input type="number" id="harga" name="harga" class="form-control" 
                               value="<?= $produk['harga'] ?>" placeholder="Contoh: 1350000" min="0" step="1000" required>
                    </div>

                    <div class="form-group">
                        <label for="serving">Total Serving <span class="required">*</span></label>
                        <input type="number" id="serving" name="serving" class="form-control" 
                               value="<?= $produk['serving'] ?>" placeholder="Contoh: 73" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="protein">Protein per Serving (gram) <span class="required">*</span></label>
                        <input type="number" id="protein" name="protein" class="form-control" 
                               value="<?= $produk['protein'] ?>" placeholder="Contoh: 24" min="0" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="kalori">Kalori per Serving (kcal) <span class="required">*</span></label>
                        <input type="number" id="kalori" name="kalori" class="form-control" 
                               value="<?= $produk['kalori'] ?>" placeholder="Contoh: 120" min="0" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="lemak">Lemak Total per Serving (gram) <span class="required">*</span></label>
                        <input type="number" id="lemak" name="lemak" class="form-control" 
                               value="<?= $produk['lemak'] ?>" placeholder="Contoh: 1.5" min="0" step="0.01" required>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?= BASE_URL ?>?page=admin/dashboard" class="btn btn--outline">Batal</a>
                    <button type="submit" class="btn btn--primary"><?= $isEdit ? '💾 Simpan Perubahan' : '➕ Tambah Produk' ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
