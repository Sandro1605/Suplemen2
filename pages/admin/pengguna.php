<?php
/**
 * Halaman Admin — Data & Analitik Pengguna (Content-Based Filtering)
 */
checkAuth();
$pdo = getConnection();

// Ambil data
$userProfiles = getAllUserProfiles($pdo);
$stats = getUserProfileStats($pdo);

// Hitung persentase untuk chart/tampilan
$total = $stats['Total'] > 0 ? $stats['Total'] : 1; // hindari div by zero
$pctBulking = round(($stats['Bulking'] / $total) * 100);
$pctCutting = round(($stats['Cutting'] / $total) * 100);
$pctMaintenance = round(($stats['Maintenance'] / $total) * 100);
?>

<div class="admin-page">
    <div class="container">
        <div class="admin-header">
            <div>
                <h1 class="admin-header__title">👥 Data Pengguna & Analitik</h1>
                <p class="admin-header__subtitle">Analisis profil preferensi pengguna dari Content-Based Filtering.</p>
            </div>
            <div class="admin-toolbar">
                <input type="text" id="userSearch" class="search-input" placeholder="🔍 Cari nama pengguna...">
            </div>
        </div>

        <!-- Analytics Cards -->
        <div class="stats-row" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
            <div class="stat-card" style="border-top: 4px solid var(--accent);">
                <div class="stat-card__number"><?= $stats['Total'] ?></div>
                <div class="stat-card__label">Total Tes Rekomendasi</div>
            </div>
            <div class="stat-card" style="border-top: 4px solid var(--warning);">
                <div class="stat-card__number" style="color:var(--warning); background:none;-webkit-text-fill-color:var(--warning);"><?= $pctBulking ?>%</div>
                <div class="stat-card__label">Fokus Bulking (<?= $stats['Bulking'] ?> user)</div>
            </div>
            <div class="stat-card" style="border-top: 4px solid var(--danger);">
                <div class="stat-card__number" style="color:var(--danger); background:none;-webkit-text-fill-color:var(--danger);"><?= $pctCutting ?>%</div>
                <div class="stat-card__label">Fokus Cutting (<?= $stats['Cutting'] ?> user)</div>
            </div>
            <div class="stat-card" style="border-top: 4px solid var(--success);">
                <div class="stat-card__number" style="color:var(--success); background:none;-webkit-text-fill-color:var(--success);"><?= $pctMaintenance ?>%</div>
                <div class="stat-card__label">Fokus Maintenance (<?= $stats['Maintenance'] ?> user)</div>
            </div>
        </div>

        <!-- Visual Bar Chart Sederhana -->
        <?php if ($stats['Total'] > 0): ?>
        <div class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h3 style="margin-bottom: 1rem; font-size: 1rem;">Distribusi Tujuan Fitness</h3>
            <div style="display: flex; height: 24px; border-radius: 12px; overflow: hidden; background: var(--border);">
                <div style="width: <?= $pctBulking ?>%; background: var(--warning); display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:bold; color:#000;" title="Bulking: <?= $pctBulking ?>%"><?= $pctBulking > 5 ? $pctBulking.'%' : '' ?></div>
                <div style="width: <?= $pctCutting ?>%; background: var(--danger); display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:bold; color:#fff;" title="Cutting: <?= $pctCutting ?>%"><?= $pctCutting > 5 ? $pctCutting.'%' : '' ?></div>
                <div style="width: <?= $pctMaintenance ?>%; background: var(--success); display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:bold; color:#fff;" title="Maintenance: <?= $pctMaintenance ?>%"><?= $pctMaintenance > 5 ? $pctMaintenance.'%' : '' ?></div>
            </div>
            <div style="display: flex; gap: 1.5rem; margin-top: 0.75rem; font-size: 0.8rem; color: var(--text-secondary); justify-content: center;">
                <span style="display:flex; align-items:center; gap:0.25rem;"><span style="width:10px;height:10px;border-radius:50%;background:var(--warning);"></span> Bulking</span>
                <span style="display:flex; align-items:center; gap:0.25rem;"><span style="width:10px;height:10px;border-radius:50%;background:var(--danger);"></span> Cutting</span>
                <span style="display:flex; align-items:center; gap:0.25rem;"><span style="width:10px;height:10px;border-radius:50%;background:var(--success);"></span> Maintenance</span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tabel Histori Pengguna -->
        <h3 style="margin-bottom: 1rem;">Riwayat Tes Sistem</h3>
        <div class="table-wrap">
            <table class="table" id="userTable">
                <thead>
                    <tr>
                        <th>Waktu (WIB)</th>
                        <th>Nama Pengguna</th>
                        <th>Tujuan Fitness</th>
                        <th>Filter Kategori</th>
                        <th style="text-align:center;">Bobot (Harga, Serv, Prot, Kal, Lemak)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($userProfiles)): ?>
                        <tr>
                            <td colspan="5" class="text-center" style="padding: 2rem; color: var(--text-muted);">Belum ada data pengguna.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($userProfiles as $user): 
                            $date = new DateTime($user['created_at']);
                            $tujuanClass = '';
                            if ($user['tujuan'] === 'Bulking') $tujuanClass = 'color: var(--warning);';
                            if ($user['tujuan'] === 'Cutting') $tujuanClass = 'color: var(--danger);';
                            if ($user['tujuan'] === 'Maintenance') $tujuanClass = 'color: var(--success);';
                        ?>
                            <tr>
                                <td style="color: var(--text-secondary); font-size: 0.85rem;"><?= $date->format('d M Y, H:i') ?></td>
                                <td><strong><?= htmlspecialchars($user['nama']) ?></strong></td>
                                <td><span style="font-weight:600; <?= $tujuanClass ?>"><?= htmlspecialchars($user['tujuan']) ?></span></td>
                                <td><?= htmlspecialchars($user['kategori']) ?></td>
                                <td style="text-align:center; font-family: monospace; font-size: 0.85rem; color: var(--text-muted);">
                                    [<?= $user['bobot_c1'] ?>, <?= $user['bobot_c2'] ?>, <?= $user['bobot_c3'] ?>, <?= $user['bobot_c4'] ?>, <?= $user['bobot_c5'] ?>]
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fitur Pencarian untuk tabel pengguna
    const searchInput = document.getElementById('userSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#userTable tbody tr').forEach(row => {
                if(row.children.length === 1) return; // skip empty message row
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
        });
    }
});
</script>
