/**
 * Sistem Rekomendasi Suplemen Fitness — Main JavaScript
 * Content-Based Filtering + TOPSIS
 */
document.addEventListener('DOMContentLoaded', function() {

    // === Navbar scroll effect ===
    const navbar = document.getElementById('mainNavbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });
    }

    // === Mobile nav toggle ===
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => navMenu.classList.toggle('show'));
        document.addEventListener('click', (e) => {
            if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('show');
            }
        });
    }

    // === Range slider realtime value ===
    document.querySelectorAll('.range-slider').forEach(slider => {
        const valueEl = document.getElementById(slider.dataset.valueTarget);
        if (valueEl) {
            const update = () => { valueEl.textContent = slider.value; };
            slider.addEventListener('input', update);
            update();
        }
    });

    // === Content-Based Filtering: Preset Bobot berdasarkan Tujuan Fitness ===
    const tujuanCards = document.getElementById('tujuanCards');
    if (tujuanCards && window.PRESET_BOBOT) {
        const radioButtons = tujuanCards.querySelectorAll('input[name="user_tujuan"]');
        const allCards = tujuanCards.querySelectorAll('.tujuan-card');

        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                const tujuan = this.value;
                const preset = window.PRESET_BOBOT[tujuan];
                
                // Update active card styling
                allCards.forEach(card => card.classList.remove('active'));
                this.closest('.tujuan-card').classList.add('active');

                // Apply preset bobot to sliders with smooth animation
                if (preset) {
                    Object.keys(preset).forEach(key => {
                        const slider = document.getElementById('slider_' + key);
                        const valueEl = document.getElementById('val_' + key);
                        if (slider && valueEl) {
                            slider.value = preset[key];
                            valueEl.textContent = preset[key];
                            // Visual feedback animation
                            valueEl.style.transform = 'scale(1.3)';
                            valueEl.style.transition = 'transform 0.3s';
                            setTimeout(() => { valueEl.style.transform = 'scale(1)'; }, 300);
                        }
                    });
                }
            });
        });
    }

    // === Detail TOPSIS accordion ===
    document.querySelectorAll('.detail-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('active');
            const content = btn.nextElementSibling;
            if (content) content.classList.toggle('show');
        });
    });

    // === Delete confirmation modal ===
    window.showDeleteModal = function(id, name) {
        const overlay = document.getElementById('deleteModal');
        if (!overlay) return;
        document.getElementById('deleteProductName').textContent = name;
        document.getElementById('deleteForm').href = '?page=admin/delete&id=' + id;
        overlay.classList.add('show');
    };
    window.hideDeleteModal = function() {
        const overlay = document.getElementById('deleteModal');
        if (overlay) overlay.classList.remove('show');
    };

    // === Admin search filter ===
    const searchInput = document.getElementById('adminSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#produkTable tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
        });
    }

    // === Form validation ===
    const produkForm = document.getElementById('formProduk');
    if (produkForm) {
        produkForm.addEventListener('submit', function(e) {
            let valid = true;
            this.querySelectorAll('[required]').forEach(input => {
                if (!input.value.trim()) {
                    input.style.borderColor = 'var(--danger)';
                    valid = false;
                } else {
                    input.style.borderColor = '';
                }
            });
            if (!valid) { e.preventDefault(); alert('Mohon lengkapi semua field yang wajib diisi.'); }
        });
    }

    // === Auto-hide alerts ===
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-8px)';
            setTimeout(() => alert.remove(), 300);
        }, 4000);
    });
});
