<?php
/**
 * Halaman Beranda (Landing Page)
 * Sistem Rekomendasi Suplemen Fitness — Content-Based Filtering + TOPSIS
 */
?>

<section class="hero">
    <div class="container">
        <div class="hero__content">
            <div class="hero__badge">🔬 Content-Based Filtering + TOPSIS</div>
            <h1 class="hero__title">
                Temukan Suplemen Fitness <span>Terbaik</span> Untukmu
            </h1>
            <p class="hero__desc">
                Sistem Rekomendasi berbasis web yang membantu kamu memilih Whey Protein dan Mass Gainer 
                sesuai tujuan fitness-mu. Didukung data produk terdaftar <strong>BPOM RI</strong>.
            </p>
            <div class="hero__actions">
                <a href="<?= BASE_URL ?>?page=rekomendasi" class="btn btn--primary">🚀 Mulai Rekomendasi</a>
                <a href="#cara-kerja" class="btn btn--outline">📖 Cara Kerja</a>
            </div>
        </div>
    </div>
</section>

<section class="section" id="cara-kerja">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title">Bagaimana Sistem Ini Bekerja?</h2>
            <p class="section__desc">Empat langkah dalam pendekatan Content-Based Filtering + TOPSIS.</p>
        </div>

        <div class="features-grid">
            <div class="card">
                <div class="card__icon">👤</div>
                <h3 class="card__title">1. Buat Profil</h3>
                <p class="card__text">Tentukan tujuan fitness-mu (Bulking, Cutting, atau Maintenance). Sistem akan membuat <strong>User Profile</strong> sebagai dasar rekomendasi.</p>
            </div>
            <div class="card">
                <div class="card__icon">⚖️</div>
                <h3 class="card__title">2. Atur Preferensi</h3>
                <p class="card__text">Bobot kriteria otomatis disesuaikan dengan profilmu. Kamu tetap bisa menyesuaikan manual sesuai prioritas.</p>
            </div>
            <div class="card">
                <div class="card__icon">⚙️</div>
                <h3 class="card__title">3. Matching & Ranking</h3>
                <p class="card__text">Sistem mencocokkan profil preferensimu dengan atribut produk (<em>Content-Based Filtering</em>), lalu meranking menggunakan metode <strong>TOPSIS</strong>.</p>
            </div>
            <div class="card">
                <div class="card__icon">🏆</div>
                <h3 class="card__title">4. Lihat Rekomendasi</h3>
                <p class="card__text">Dapatkan daftar suplemen yang paling sesuai dengan kebutuhanmu, lengkap dengan skor dan detail perhitungan.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title">Kriteria Penilaian</h2>
            <p class="section__desc">5 variabel kuantitatif yang menjadi atribut produk (<em>Item Profile</em>) dalam Content-Based Filtering.</p>
        </div>

        <div class="features-grid">
            <div class="card">
                <div class="card__icon">💰</div>
                <h3 class="card__title">Harga Produk</h3>
                <p class="card__text">Harga per kemasan produk. Semakin murah semakin baik (kriteria <strong style="color:var(--danger)">Cost</strong>).</p>
            </div>
            <div class="card">
                <div class="card__icon">📊</div>
                <h3 class="card__title">Total Serving</h3>
                <p class="card__text">Jumlah takaran saji per kemasan. Semakin banyak semakin baik (kriteria <strong style="color:var(--success)">Benefit</strong>).</p>
            </div>
            <div class="card">
                <div class="card__icon">💪</div>
                <h3 class="card__title">Kandungan Protein</h3>
                <p class="card__text">Gram protein per serving. Semakin tinggi semakin baik (kriteria <strong style="color:var(--success)">Benefit</strong>).</p>
            </div>
            <div class="card">
                <div class="card__icon">🔥</div>
                <h3 class="card__title">Total Kalori</h3>
                <p class="card__text">Kalori per serving (kcal). Semakin tinggi semakin baik (kriteria <strong style="color:var(--success)">Benefit</strong>).</p>
            </div>
            <div class="card">
                <div class="card__icon">🥑</div>
                <h3 class="card__title">Lemak Total</h3>
                <p class="card__text">Gram lemak per serving. Semakin rendah semakin baik (kriteria <strong style="color:var(--danger)">Cost</strong>).</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container text-center">
        <div class="disclaimer">
            ⚠️ Sistem ini bersifat <strong>rekomendasi</strong> berdasarkan perhitungan matematis, bukan saran medis. 
            Konsultasikan dengan ahli gizi atau dokter untuk kebutuhan nutrisi khusus.
        </div>
        <a href="<?= BASE_URL ?>?page=rekomendasi" class="btn btn--primary">🚀 Coba Sekarang — Gratis!</a>
    </div>
</section>
