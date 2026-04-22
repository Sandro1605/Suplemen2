    </main>

    <!-- ============ FOOTER ============ -->
    <footer class="footer">
        <div class="container">
            <div class="footer__inner">
                <div class="footer__brand">
                    <span class="footer__logo">💪 Reko<span class="text-accent">Suplemen</span></span>
                    <p class="footer__desc">Sistem Rekomendasi Suplemen Fitness berbasis web menggunakan metode Content-Based Filtering dan TOPSIS.</p>
                </div>
                <div class="footer__links">
                    <h4>Navigasi</h4>
                    <a href="<?= BASE_URL ?>">Beranda</a>
                    <a href="<?= BASE_URL ?>?page=rekomendasi">Rekomendasi</a>
                </div>
                <div class="footer__info">
                    <h4>Informasi</h4>
                    <p>Data produk berdasarkan suplemen terdaftar <strong>BPOM RI</strong>.</p>
                    <p class="footer__disclaimer">⚠️ Sistem ini bersifat <strong>rekomendasi</strong>, bukan saran medis. Konsultasikan dengan ahli gizi untuk kebutuhan khusus.</p>
                </div>
            </div>
            <div class="footer__bottom">
                <p>&copy; <?= date('Y') ?> Sistem Rekomendasi Suplemen Fitness — Content-Based Filtering + TOPSIS. Dibuat untuk keperluan akademik.</p>
            </div>
        </div>
    </footer>

    <!-- Main JavaScript -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
