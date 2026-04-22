<?php

class TopsisEngine {
    private $alternatifs;
    private $kriteria;

    /**
     * Konstruktor Kelas TOPSIS
     */
    public function __construct($alternatifs, $kriteria) {
        $this->alternatifs = $alternatifs;
        $this->kriteria = $kriteria;
    }

    /**
     * Fungsi utama untuk menjalankan seluruh proses hitung TOPSIS
     */
    public function hitung() {
        // Cek jika data kosong untuk mencegah error
        if (empty($this->alternatifs) || empty($this->kriteria)) {
            return ['error' => 'Data alternatif atau kriteria tidak boleh kosong.'];
        }

        // =========================================================
        // LANGKAH 1: Normalisasi Bobot Kriteria (W)
        // Memastikan total seluruh bobot bernilai 1 (100%)

        $total_bobot = array_sum(array_column($this->kriteria, 'bobot'));
        $W = [];
        foreach ($this->kriteria as $id_kriteria => $k) {
            // Jika total_bobot 0 (hindari division by zero), set bobot 0
            $W[$id_kriteria] = ($total_bobot != 0) ? ($k['bobot'] / $total_bobot) : 0;
        }

        // =========================================================
        // LANGKAH 2: Mencari Nilai Pembagi (Akar Kuadrat)
        // Rumus: sqrt(sum(x^2)) untuk setiap kolom kriteria
        
        $pembagi = [];
        foreach ($this->kriteria as $id_kriteria => $k) {
            $sum_kuadrat = 0;
            foreach ($this->alternatifs as $id_alt => $alt) {
                // Kuadratkan nilai dan jumlahkan
                $nilai_asli = isset($alt['nilai'][$id_kriteria]) ? $alt['nilai'][$id_kriteria] : 0;
                $sum_kuadrat += pow($nilai_asli, 2);
            }
            $pembagi[$id_kriteria] = sqrt($sum_kuadrat);
        }

        // =========================================================
        // LANGKAH 3: Matriks Ternormalisasi (R) & Terbobot (Y)
        
        $R = []; // Matriks Normalisasi
        $Y = []; // Matriks Normalisasi Terbobot

        foreach ($this->alternatifs as $id_alt => $alt) {
            foreach ($this->kriteria as $id_kriteria => $k) {
                $nilai_asli = isset($alt['nilai'][$id_kriteria]) ? $alt['nilai'][$id_kriteria] : 0;
                
                // Hitung R (Cegah pembagian dengan nol)
                $nilai_r = ($pembagi[$id_kriteria] != 0) ? ($nilai_asli / $pembagi[$id_kriteria]) : 0;
                $R[$id_alt][$id_kriteria] = $nilai_r;
                
                // Hitung Y
                $nilai_y = $nilai_r * $W[$id_kriteria];
                $Y[$id_alt][$id_kriteria] = $nilai_y;
            }
        }

        // =========================================================
        // LANGKAH 4: Solusi Ideal Positif (A+) dan Negatif (A-)
        
        $A_plus = [];
        $A_min = [];

        foreach ($this->kriteria as $id_kriteria => $k) {
            // Ambil semua nilai pada kolom kriteria tertentu dari matriks Y
            $kolom_y = array_column($Y, $id_kriteria);
            
            $max_val = max($kolom_y);
            $min_val = min($kolom_y);

            // Cek sifat kriteria (Benefit atau Cost)
            if (strtolower($k['sifat']) === 'benefit') {
                $A_plus[$id_kriteria] = $max_val; // Benefit: A+ adalah Max
                $A_min[$id_kriteria]  = $min_val; // Benefit: A- adalah Min
            } else { // Jika sifatnya Cost
                $A_plus[$id_kriteria] = $min_val; // Cost: A+ adalah Min
                $A_min[$id_kriteria]  = $max_val; // Cost: A- adalah Max
            }
        }

        // =========================================================
        // LANGKAH 5 & 6: Jarak Solusi (D+, D-) & Nilai Preferensi (V)
       
        $D_plus = [];
        $D_min = [];
        $V = [];
        $hasil_ranking = [];

        foreach ($this->alternatifs as $id_alt => $alt) {
            $sum_d_plus = 0;
            $sum_d_min = 0;

            foreach ($this->kriteria as $id_kriteria => $k) {
                $sum_d_plus += pow($Y[$id_alt][$id_kriteria] - $A_plus[$id_kriteria], 2);
                $sum_d_min  += pow($Y[$id_alt][$id_kriteria] - $A_min[$id_kriteria], 2);
            }

            // Hasil Jarak
            $D_plus[$id_alt] = sqrt($sum_d_plus);
            $D_min[$id_alt]  = sqrt($sum_d_min);

            // Menghitung Nilai Preferensi (V)
            $total_d = $D_plus[$id_alt] + $D_min[$id_alt];
            $nilai_v = ($total_d != 0) ? ($D_min[$id_alt] / $total_d) : 0;
            $V[$id_alt] = $nilai_v;

            // Simpan ke array akhir untuk diurutkan
            $hasil_ranking[] = [
                'id_produk'   => $id_alt,
                'nama_produk' => $alt['nama_produk'],
                'nilai_v'     => round($nilai_v, 5) // Dibulatkan 5 angka di belakang koma
            ];
        }

        // =========================================================
        // LANGKAH 7: Mengurutkan (Ranking) dari V terbesar ke terkecil
        
        usort($hasil_ranking, function($a, $b) {
            // Gunakan operator spaceship (<=>) untuk membandingkan nilai desimal
            return $b['nilai_v'] <=> $a['nilai_v'];
        });

        // Kembalikan semua data sebagai bentuk laporan lengkap
        return [
            'status'        => 'success',
            'bobot_W'       => $W,
            'matriks_R'     => $R,
            'matriks_Y'     => $Y,
            'ideal_positif' => $A_plus,
            'ideal_negatif' => $A_min,
            'jarak_positif' => $D_plus,
            'jarak_negatif' => $D_min,
            'ranking_akhir' => $hasil_ranking
        ];
    }
}
?>