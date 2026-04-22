<?php
/**
 * Fungsi CRUD Produk Suplemen
 * SPK Rekomendasi Suplemen Fitness — Metode TOPSIS
 */

/**
 * Ambil semua produk, opsional filter berdasarkan kategori
 * 
 * @param PDO         $pdo
 * @param string|null $kategori  'Whey Protein', 'Mass Gainer', atau null untuk semua
 * @return array
 */
function getAllProduk($pdo, $kategori = null) {
    if ($kategori && $kategori !== 'semua') {
        $stmt = $pdo->prepare("SELECT * FROM produk WHERE kategori = :kategori ORDER BY nama_produk ASC");
        $stmt->execute([':kategori' => $kategori]);
    } else {
        $stmt = $pdo->query("SELECT * FROM produk ORDER BY nama_produk ASC");
    }
    return $stmt->fetchAll();
}

/**
 * Ambil satu produk berdasarkan ID
 * 
 * @param PDO $pdo
 * @param int $id
 * @return array|false
 */
function getProdukById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => (int) $id]);
    return $stmt->fetch();
}

/**
 * Tambah produk baru
 * 
 * @param PDO   $pdo
 * @param array $data  Associative array berisi field-field produk
 * @return array ['success' => bool, 'message' => string]
 */
function createProduk($pdo, $data) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO produk (nama_produk, merek, kategori, no_bpom, harga, serving, protein, kalori, lemak)
            VALUES (:nama_produk, :merek, :kategori, :no_bpom, :harga, :serving, :protein, :kalori, :lemak)
        ");
        $stmt->execute([
            ':nama_produk' => trim($data['nama_produk']),
            ':merek'       => trim($data['merek']),
            ':kategori'    => $data['kategori'],
            ':no_bpom'     => trim($data['no_bpom']),
            ':harga'       => (float) $data['harga'],
            ':serving'     => (int) $data['serving'],
            ':protein'     => (float) $data['protein'],
            ':kalori'      => (float) $data['kalori'],
            ':lemak'       => (float) $data['lemak'],
        ]);
        return ['success' => true, 'message' => 'Produk berhasil ditambahkan!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Gagal menambahkan produk: ' . $e->getMessage()];
    }
}

/**
 * Update produk berdasarkan ID
 * 
 * @param PDO   $pdo
 * @param int   $id
 * @param array $data
 * @return array ['success' => bool, 'message' => string]
 */
function updateProduk($pdo, $id, $data) {
    try {
        $stmt = $pdo->prepare("
            UPDATE produk SET
                nama_produk = :nama_produk,
                merek       = :merek,
                kategori    = :kategori,
                no_bpom     = :no_bpom,
                harga       = :harga,
                serving     = :serving,
                protein     = :protein,
                kalori      = :kalori,
                lemak       = :lemak
            WHERE id = :id
        ");
        $stmt->execute([
            ':id'          => (int) $id,
            ':nama_produk' => trim($data['nama_produk']),
            ':merek'       => trim($data['merek']),
            ':kategori'    => $data['kategori'],
            ':no_bpom'     => trim($data['no_bpom']),
            ':harga'       => (float) $data['harga'],
            ':serving'     => (int) $data['serving'],
            ':protein'     => (float) $data['protein'],
            ':kalori'      => (float) $data['kalori'],
            ':lemak'       => (float) $data['lemak'],
        ]);
        return ['success' => true, 'message' => 'Produk berhasil diperbarui!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Gagal memperbarui produk: ' . $e->getMessage()];
    }
}

/**
 * Hapus produk berdasarkan ID
 * 
 * @param PDO $pdo
 * @param int $id
 * @return array ['success' => bool, 'message' => string]
 */
function deleteProduk($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM produk WHERE id = :id");
        $stmt->execute([':id' => (int) $id]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Produk berhasil dihapus!'];
        }
        return ['success' => false, 'message' => 'Produk tidak ditemukan.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Gagal menghapus produk: ' . $e->getMessage()];
    }
}

/**
 * Ambil semua data kriteria
 * 
 * @param PDO $pdo
 * @return array
 */
function getKriteria($pdo) {
    $stmt = $pdo->query("SELECT * FROM kriteria ORDER BY id ASC");
    return $stmt->fetchAll();
}

/**
 * Hitung total produk, opsional per kategori
 * 
 * @param PDO         $pdo
 * @param string|null $kategori
 * @return int
 */
function countProduk($pdo, $kategori = null) {
    if ($kategori) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM produk WHERE kategori = :kategori");
        $stmt->execute([':kategori' => $kategori]);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM produk");
    }
    $result = $stmt->fetch();
    return (int) $result['total'];
}

/**
 * Format data produk dari database ke format input TopsisEngine
 * 
 * TopsisEngine memerlukan format:
 * [
 *     'P1' => [
 *         'nama_produk' => '...',
 *         'nilai' => ['C1' => harga, 'C2' => serving, 'C3' => protein, 'C4' => kalori, 'C5' => lemak]
 *     ],
 *     ...
 * ]
 * 
 * @param array $produkList  Hasil query dari getAllProduk()
 * @return array Format yang siap diproses TopsisEngine
 */
function prepareDataForTopsis($produkList) {
    $data = [];
    foreach ($produkList as $produk) {
        $key = 'P' . $produk['id'];
        $data[$key] = [
            'nama_produk' => $produk['nama_produk'],
            'nilai' => [
                'C1' => (float) $produk['harga'],
                'C2' => (int)   $produk['serving'],
                'C3' => (float) $produk['protein'],
                'C4' => (float) $produk['kalori'],
                'C5' => (float) $produk['lemak'],
            ]
        ];
    }
    return $data;
}

/**
 * Format data kriteria dari database + bobot user ke format input TopsisEngine
 * 
 * TopsisEngine memerlukan format:
 * [
 *     'C1' => ['nama' => '...', 'sifat' => 'cost', 'bobot' => 5],
 *     ...
 * ]
 * 
 * @param array $kriteriaList  Hasil query dari getKriteria()
 * @param array $bobotUser     Bobot dari input user ['C1' => 3, 'C2' => 5, ...]
 * @return array Format yang siap diproses TopsisEngine
 */
function prepareKriteriaForTopsis($kriteriaList, $bobotUser) {
    $data = [];
    foreach ($kriteriaList as $kriteria) {
        $id = $kriteria['id'];
        $data[$id] = [
            'nama'  => $kriteria['nama'],
            'sifat' => $kriteria['sifat'],
            'bobot' => isset($bobotUser[$id]) ? (int) $bobotUser[$id] : 3, // Default bobot = 3
        ];
    }
    return $data;
}

/**
 * Cari produk berdasarkan keyword (nama atau merek)
 * 
 * @param PDO    $pdo
 * @param string $keyword
 * @return array
 */
function searchProduk($pdo, $keyword) {
    $stmt = $pdo->prepare("
        SELECT * FROM produk 
        WHERE nama_produk LIKE :keyword OR merek LIKE :keyword2
        ORDER BY nama_produk ASC
    ");
    $search = '%' . trim($keyword) . '%';
    $stmt->execute([':keyword' => $search, ':keyword2' => $search]);
    return $stmt->fetchAll();
}

// ============================================================
// CONTENT-BASED FILTERING — User Profile Functions
// ============================================================

/**
 * Mendapatkan preset bobot berdasarkan tujuan fitness pengguna.
 * Ini merupakan inti dari User Profiling dalam Content-Based Filtering.
 * 
 * @param string $tujuan  'Bulking', 'Cutting', atau 'Maintenance'
 * @return array Bobot preset ['C1'=>..., 'C2'=>..., ...]
 */
function getPresetBobot($tujuan) {
    $presets = [
        'Bulking' => [
            'C1' => 2,  // Harga: kurang penting (fokus nutrisi)
            'C2' => 4,  // Serving: penting (butuh banyak asupan)
            'C3' => 4,  // Protein: penting (bangun otot)
            'C4' => 5,  // Kalori: sangat penting (surplus kalori)
            'C5' => 2,  // Lemak: kurang penting (boleh tinggi)
        ],
        'Cutting' => [
            'C1' => 3,  // Harga: cukup penting
            'C2' => 3,  // Serving: cukup penting
            'C3' => 5,  // Protein: sangat penting (jaga otot)
            'C4' => 2,  // Kalori: kurang penting (defisit kalori)
            'C5' => 5,  // Lemak: sangat penting (rendah lemak)
        ],
        'Maintenance' => [
            'C1' => 3,  // Harga: cukup penting
            'C2' => 3,  // Serving: cukup penting
            'C3' => 3,  // Protein: cukup penting
            'C4' => 3,  // Kalori: cukup penting
            'C5' => 3,  // Lemak: cukup penting
        ],
    ];

    return isset($presets[$tujuan]) ? $presets[$tujuan] : $presets['Maintenance'];
}

/**
 * Simpan profil preferensi pengguna ke database (User Profile CBF)
 * 
 * @param PDO   $pdo
 * @param array $data  ['nama', 'tujuan', 'kategori', 'bobot_c1'..'bobot_c5']
 * @return array ['success' => bool, 'message' => string, 'id' => int|null]
 */
function saveUserProfile($pdo, $data) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO user_profiles (nama, tujuan, kategori, bobot_c1, bobot_c2, bobot_c3, bobot_c4, bobot_c5)
            VALUES (:nama, :tujuan, :kategori, :c1, :c2, :c3, :c4, :c5)
        ");
        $stmt->execute([
            ':nama'     => trim($data['nama'] ?? 'Anonim'),
            ':tujuan'   => $data['tujuan'] ?? 'Maintenance',
            ':kategori' => $data['kategori'] ?? 'Semua',
            ':c1'       => (int)($data['bobot_c1'] ?? 3),
            ':c2'       => (int)($data['bobot_c2'] ?? 3),
            ':c3'       => (int)($data['bobot_c3'] ?? 3),
            ':c4'       => (int)($data['bobot_c4'] ?? 3),
            ':c5'       => (int)($data['bobot_c5'] ?? 3),
        ]);
        return ['success' => true, 'message' => 'Profil tersimpan.', 'id' => $pdo->lastInsertId()];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Gagal menyimpan profil.', 'id' => null];
    }
}

/**
 * Hitung total profil pengguna yang tersimpan
 * 
 * @param PDO $pdo
 * @return int
 */
function countUserProfiles($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM user_profiles");
    $result = $stmt->fetch();
    return (int) $result['total'];
}

/**
 * Ambil semua data profil pengguna untuk tabel admin
 * 
 * @param PDO $pdo
 * @return array
 */
function getAllUserProfiles($pdo) {
    $stmt = $pdo->query("SELECT * FROM user_profiles ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

/**
 * Dapatkan statistik tujuan fitness dari profil pengguna
 * 
 * @param PDO $pdo
 * @return array
 */
function getUserProfileStats($pdo) {
    $stmt = $pdo->query("SELECT tujuan, COUNT(*) as jumlah FROM user_profiles GROUP BY tujuan");
    $data = $stmt->fetchAll();
    
    $stats = [
        'Bulking' => 0,
        'Cutting' => 0,
        'Maintenance' => 0,
        'Total' => 0
    ];
    
    foreach ($data as $row) {
        $stats[$row['tujuan']] = (int)$row['jumlah'];
        $stats['Total'] += (int)$row['jumlah'];
    }
    
    return $stats;
}
?>
