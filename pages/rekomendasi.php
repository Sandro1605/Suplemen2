<?php
/**
 * Halaman Rekomendasi — Content-Based Filtering + TOPSIS
 * 
 * Alur:
 * 1. User mengisi profil (nama, tujuan fitness) → User Profile (CBF)
 * 2. Sistem menyesuaikan bobot berdasarkan profil → Preference Weighting
 * 3. User dapat menyesuaikan bobot secara manual
 * 4. TOPSIS menghitung ranking → Recommendation Output
 */
$pdo = getConnection();
$kriteriaList = getKriteria($pdo);
$hasResult = false;
$ranking = [];
$hasilTopsis = [];
$produkMap = [];
$userTujuan = '';
$userName = '';

// Proses form jika di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = isset($_POST['user_nama']) ? trim($_POST['user_nama']) : 'Anonim';
    $userTujuan = isset($_POST['user_tujuan']) ? $_POST['user_tujuan'] : 'Maintenance';
    
    $bobotUser = [
        'C1' => isset($_POST['bobot_C1']) ? (int)$_POST['bobot_C1'] : 3,
        'C2' => isset($_POST['bobot_C2']) ? (int)$_POST['bobot_C2'] : 3,
        'C3' => isset($_POST['bobot_C3']) ? (int)$_POST['bobot_C3'] : 3,
        'C4' => isset($_POST['bobot_C4']) ? (int)$_POST['bobot_C4'] : 3,
        'C5' => isset($_POST['bobot_C5']) ? (int)$_POST['bobot_C5'] : 3,
    ];
    $filterKategori = isset($_POST['filter_kategori']) ? $_POST['filter_kategori'] : 'semua';

    // Simpan profil pengguna ke database (User Profile - CBF)
    saveUserProfile($pdo, [
        'nama'     => $userName,
        'tujuan'   => $userTujuan,
        'kategori' => $filterKategori === 'semua' ? 'Semua' : $filterKategori,
        'bobot_c1' => $bobotUser['C1'],
        'bobot_c2' => $bobotUser['C2'],
        'bobot_c3' => $bobotUser['C3'],
        'bobot_c4' => $bobotUser['C4'],
        'bobot_c5' => $bobotUser['C5'],
    ]);

    // Ambil produk dari DB (Item Profile - CBF)
    $produkList = getAllProduk($pdo, $filterKategori);

    if (count($produkList) >= 2) {
        foreach ($produkList as $p) { $produkMap[$p['id']] = $p; }

        // Prepare data for TOPSIS (Matching & Ranking - CBF)
        $dataAlternatif = prepareDataForTopsis($produkList);
        $dataKriteria   = prepareKriteriaForTopsis($kriteriaList, $bobotUser);

        // Run TOPSIS Engine
        $topsis = new TopsisEngine($dataAlternatif, $dataKriteria);
        $hasilTopsis = $topsis->hitung();

        if (isset($hasilTopsis['status']) && $hasilTopsis['status'] === 'success') {
            $ranking = $hasilTopsis['ranking_akhir'];
            $hasResult = true;
        }
    } else {
        $errorMsg = 'Minimal 2 produk diperlukan untuk menghasilkan rekomendasi.';
    }
} else {
    $bobotUser = ['C1'=>3,'C2'=>3,'C3'=>3,'C4'=>3,'C5'=>3];
    $filterKategori = 'semua';
}

$kriteriaInfo = [
    'C1' => ['label'=>'Harga Produk','sifat'=>'cost','icon'=>'💰'],
    'C2' => ['label'=>'Total Serving','sifat'=>'benefit','icon'=>'📊'],
    'C3' => ['label'=>'Kandungan Protein','sifat'=>'benefit','icon'=>'💪'],
    'C4' => ['label'=>'Total Kalori','sifat'=>'benefit','icon'=>'🔥'],
    'C5' => ['label'=>'Lemak Total','sifat'=>'cost','icon'=>'🥑'],
];

// Preset bobot sebagai JSON untuk JavaScript
$presetBobotJson = json_encode([
    'Bulking'     => getPresetBobot('Bulking'),
    'Cutting'     => getPresetBobot('Cutting'),
    'Maintenance' => getPresetBobot('Maintenance'),
]);
?>

<div class="reko-page">
    <div class="container">
        <div class="reko-page__header">
            <h1 class="reko-page__title">🎯 Sistem Rekomendasi Suplemen</h1>
            <p class="reko-page__desc">Isi profil fitness-mu, atur preferensi, dan dapatkan rekomendasi suplemen terbaik.</p>
        </div>

        <div class="disclaimer">⚠️ Sistem ini bersifat <strong>rekomendasi</strong> berdasarkan metode Content-Based Filtering + TOPSIS, bukan saran medis.</div>

        <?php if (isset($errorMsg)): ?>
            <div class="alert alert--error">⚠️ <?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <div class="reko-layout">
            <!-- SIDEBAR: User Profile + Bobot Form -->
            <div class="reko-sidebar">
                <form method="POST" action="<?= BASE_URL ?>?page=rekomendasi" class="bobot-form" id="bobotForm">
                    
                    <!-- STEP 1: User Profile (Content-Based Filtering) -->
                    <div class="profile-section">
                        <h2 class="bobot-form__title">👤 Profil Pengguna</h2>
                        <p class="bobot-form__desc">Langkah 1: Beritahu kami tentang tujuan fitness-mu</p>

                        <div class="form-group" style="margin-bottom:1rem;">
                            <label for="user_nama" style="font-weight:600;font-size:0.88rem;margin-bottom:0.4rem;display:block;color:var(--text-secondary);">Nama</label>
                            <input type="text" id="user_nama" name="user_nama" class="form-control" 
                                   value="<?= htmlspecialchars($userName) ?>" placeholder="Nama kamu (opsional)">
                        </div>

                        <div class="tujuan-cards" id="tujuanCards">
                            <label class="tujuan-card <?= $userTujuan === 'Bulking' ? 'active' : '' ?>">
                                <input type="radio" name="user_tujuan" value="Bulking" <?= $userTujuan === 'Bulking' ? 'checked' : '' ?>>
                                <span class="tujuan-card__icon">💪</span>
                                <span class="tujuan-card__title">Bulking</span>
                                <span class="tujuan-card__desc">Menambah massa otot & berat badan</span>
                            </label>
                            <label class="tujuan-card <?= $userTujuan === 'Cutting' ? 'active' : '' ?>">
                                <input type="radio" name="user_tujuan" value="Cutting" <?= $userTujuan === 'Cutting' ? 'checked' : '' ?>>
                                <span class="tujuan-card__icon">🔥</span>
                                <span class="tujuan-card__title">Cutting</span>
                                <span class="tujuan-card__desc">Menurunkan lemak, jaga otot</span>
                            </label>
                            <label class="tujuan-card <?= $userTujuan === 'Maintenance' ? 'active' : '' ?>">
                                <input type="radio" name="user_tujuan" value="Maintenance" <?= $userTujuan === 'Maintenance' ? 'checked' : '' ?>>
                                <span class="tujuan-card__icon">⚖️</span>
                                <span class="tujuan-card__title">Maintenance</span>
                                <span class="tujuan-card__desc">Menjaga kondisi saat ini</span>
                            </label>
                        </div>
                    </div>

                    <hr style="border:none;border-top:1px solid var(--border);margin:1.5rem 0;">

                    <!-- STEP 2: Preference Weighting -->
                    <h2 class="bobot-form__title">⚖️ Bobot Preferensi</h2>
                    <p class="bobot-form__desc">Langkah 2: Sesuaikan bobot (otomatis dari profil, bisa diubah manual)</p>

                    <?php foreach ($kriteriaInfo as $id => $info): ?>
                    <div class="bobot-group">
                        <div class="bobot-group__header">
                            <span class="bobot-group__label"><?= $info['icon'] ?> <?= $info['label'] ?></span>
                            <span class="bobot-group__badge bobot-group__badge--<?= $info['sifat'] ?>"><?= ucfirst($info['sifat']) ?></span>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <input type="range" name="bobot_<?= $id ?>" min="1" max="5" value="<?= $bobotUser[$id] ?>"
                                   class="range-slider" data-value-target="val_<?= $id ?>" id="slider_<?= $id ?>" style="flex:1;">
                            <span class="bobot-group__value" id="val_<?= $id ?>"><?= $bobotUser[$id] ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="filter-group">
                        <label>📦 Filter Kategori</label>
                        <div class="select-wrap">
                            <select name="filter_kategori">
                                <option value="semua" <?= $filterKategori==='semua'?'selected':'' ?>>Semua Kategori</option>
                                <option value="Whey Protein" <?= $filterKategori==='Whey Protein'?'selected':'' ?>>Whey Protein</option>
                                <option value="Mass Gainer" <?= $filterKategori==='Mass Gainer'?'selected':'' ?>>Mass Gainer</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn--primary btn--block">🚀 Dapatkan Rekomendasi</button>
                </form>
            </div>

            <!-- MAIN: Results -->
            <div class="results-panel">
                <?php if ($hasResult): ?>
                    <div class="results-panel__header">
                        <div>
                            <h2 class="results-panel__title">🏆 Hasil Rekomendasi</h2>
                            <?php if ($userName && $userTujuan): ?>
                                <p class="results-panel__subtitle" style="color:var(--text-secondary);font-size:0.9rem;margin-top:0.25rem;">
                                    Rekomendasi untuk <strong style="color:var(--accent)"><?= htmlspecialchars($userName ?: 'Anonim') ?></strong> 
                                    dengan tujuan <strong style="color:var(--accent)"><?= htmlspecialchars($userTujuan) ?></strong>
                                </p>
                            <?php endif; ?>
                        </div>
                        <span class="results-panel__count"><?= count($ranking) ?> produk dianalisis</span>
                    </div>

                    <!-- Methodology Badge -->
                    <div class="method-badge">
                        <span class="method-badge__item">📋 Content-Based Filtering</span>
                        <span class="method-badge__separator">+</span>
                        <span class="method-badge__item">📐 TOPSIS Ranking</span>
                    </div>

                    <div class="table-wrap">
                        <table class="table" id="hasilTable">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Serving</th>
                                    <th>Protein</th>
                                    <th>Kalori</th>
                                    <th>Lemak</th>
                                    <th>Skor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ranking as $i => $r):
                                    $rank = $i + 1;
                                    $pid = str_replace('P', '', $r['id_produk']);
                                    $produk = isset($produkMap[$pid]) ? $produkMap[$pid] : null;
                                    $rankClass = $rank <= 3 ? "rank-$rank" : '';
                                ?>
                                <tr class="<?= $rankClass ?>">
                                    <td>
                                        <?php if ($rank <= 3): ?>
                                            <span class="rank-badge rank-badge--<?= $rank ?>"><?= $rank ?></span>
                                        <?php else: ?>
                                            <?= $rank ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($r['nama_produk']) ?></strong>
                                        <?php if ($produk): ?>
                                            <br><small style="color:var(--text-muted)"><?= htmlspecialchars($produk['merek']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($produk): ?>
                                            <span class="kategori-badge kategori-badge--<?= $produk['kategori']==='Whey Protein'?'whey':'gainer' ?>">
                                                <?= $produk['kategori'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="harga-col"><?= $produk ? 'Rp '.number_format($produk['harga'],0,',','.') : '-' ?></td>
                                    <td><?= $produk ? $produk['serving'] : '-' ?></td>
                                    <td><?= $produk ? $produk['protein'].'g' : '-' ?></td>
                                    <td><?= $produk ? number_format($produk['kalori'],0).' kcal' : '-' ?></td>
                                    <td><?= $produk ? $produk['lemak'].'g' : '-' ?></td>
                                    <td class="nilai-v"><?= $r['nilai_v'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Detail Perhitungan TOPSIS -->
                    <div class="detail-topsis">
                        <button class="detail-toggle" type="button">
                            <span>📐 Detail Langkah Perhitungan TOPSIS</span>
                            <span class="detail-toggle__arrow">▼</span>
                        </button>
                        <div class="detail-content">
                            <div class="detail-content__inner">
                                <div class="detail-section">
                                    <h4>Bobot Ternormalisasi (W)</h4>
                                    <div class="table-wrap"><table class="table">
                                        <thead><tr><?php foreach ($hasilTopsis['bobot_W'] as $id => $w): ?><th><?= $id ?></th><?php endforeach; ?></tr></thead>
                                        <tbody><tr><?php foreach ($hasilTopsis['bobot_W'] as $w): ?><td><?= round($w, 5) ?></td><?php endforeach; ?></tr></tbody>
                                    </table></div>
                                </div>
                                <div class="detail-section">
                                    <h4>Matriks Ternormalisasi (R)</h4>
                                    <div class="table-wrap"><table class="table">
                                        <thead><tr><th>Alternatif</th><?php foreach (array_keys($hasilTopsis['bobot_W']) as $c): ?><th><?= $c ?></th><?php endforeach; ?></tr></thead>
                                        <tbody><?php foreach ($hasilTopsis['matriks_R'] as $pid => $vals): ?>
                                            <tr><td><?= $pid ?></td><?php foreach ($vals as $v): ?><td><?= round($v, 5) ?></td><?php endforeach; ?></tr>
                                        <?php endforeach; ?></tbody>
                                    </table></div>
                                </div>
                                <div class="detail-section">
                                    <h4>Matriks Terbobot (Y)</h4>
                                    <div class="table-wrap"><table class="table">
                                        <thead><tr><th>Alternatif</th><?php foreach (array_keys($hasilTopsis['bobot_W']) as $c): ?><th><?= $c ?></th><?php endforeach; ?></tr></thead>
                                        <tbody><?php foreach ($hasilTopsis['matriks_Y'] as $pid => $vals): ?>
                                            <tr><td><?= $pid ?></td><?php foreach ($vals as $v): ?><td><?= round($v, 5) ?></td><?php endforeach; ?></tr>
                                        <?php endforeach; ?></tbody>
                                    </table></div>
                                </div>
                                <div class="detail-section">
                                    <h4>Solusi Ideal Positif (A⁺) & Negatif (A⁻)</h4>
                                    <div class="table-wrap"><table class="table">
                                        <thead><tr><th>Solusi</th><?php foreach (array_keys($hasilTopsis['bobot_W']) as $c): ?><th><?= $c ?></th><?php endforeach; ?></tr></thead>
                                        <tbody>
                                            <tr><td><strong>A⁺</strong></td><?php foreach ($hasilTopsis['ideal_positif'] as $v): ?><td><?= round($v, 5) ?></td><?php endforeach; ?></tr>
                                            <tr><td><strong>A⁻</strong></td><?php foreach ($hasilTopsis['ideal_negatif'] as $v): ?><td><?= round($v, 5) ?></td><?php endforeach; ?></tr>
                                        </tbody>
                                    </table></div>
                                </div>
                                <div class="detail-section">
                                    <h4>Jarak Solusi (D⁺, D⁻) & Nilai Preferensi (V)</h4>
                                    <div class="table-wrap"><table class="table">
                                        <thead><tr><th>Alternatif</th><th>D⁺</th><th>D⁻</th><th>Nilai V</th></tr></thead>
                                        <tbody><?php foreach ($hasilTopsis['jarak_positif'] as $pid => $dp): ?>
                                            <tr>
                                                <td><?= $pid ?></td>
                                                <td><?= round($dp, 5) ?></td>
                                                <td><?= round($hasilTopsis['jarak_negatif'][$pid], 5) ?></td>
                                                <td class="nilai-v"><?= round($hasilTopsis['jarak_negatif'][$pid] / ($dp + $hasilTopsis['jarak_negatif'][$pid]), 5) ?></td>
                                            </tr>
                                        <?php endforeach; ?></tbody>
                                    </table></div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state__icon">📊</div>
                        <p class="empty-state__text">Isi profil dan atur bobot preferensi di sebelah kiri, lalu klik <strong>"Dapatkan Rekomendasi"</strong> untuk melihat hasil.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Preset bobot data for JavaScript -->
<script>window.PRESET_BOBOT = <?= $presetBobotJson ?>;</script>
