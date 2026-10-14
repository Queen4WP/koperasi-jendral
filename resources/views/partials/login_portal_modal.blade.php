<!-- MODAL SELECTOR PORTAL LOGIN (5 PERAN KOPIN) -->
<div id="loginPortalModal" class="login-modal-backdrop" aria-hidden="true" style="display: none;">
    <div class="login-modal-dialog">
        <!-- Header Modal -->
        <div class="login-modal-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="login-modal-icon-badge">
                    <i class="fa-solid fa-key"></i>
                </div>
                <div>
                    <span class="login-modal-tagline">PILIH PORTAL AKSES KOPIN</span>
                    <h2 class="login-modal-title">Masuk ke Sistem Koperasi</h2>
                </div>
            </div>
            <button type="button" class="login-modal-close" onclick="closeLoginModal()" aria-label="Tutup modal">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <p class="login-modal-desc">
            Silakan pilih portal sesuai peran Anda untuk dialihkan ke halaman login yang tepat:
        </p>

        <!-- Body Modal -->
        <div class="login-modal-body">
            
            <!-- KATEGORI 1: LAYANAN NASABAH & KARYAWAN -->
            <div class="portal-group-label">
                <i class="fa-solid fa-users" style="color: #059669;"></i> Portal Nasabah & Karyawan
            </div>
            <div class="portal-cards-grid grid-2">
                <!-- Card 1: Anggota Umum -->
                <div class="portal-card member-card">
                    <div class="portal-card-head">
                        <div class="portal-avatar member-avatar">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                        <div>
                            <span class="portal-badge badge-member">Masyarakat Umum</span>
                            <h3 class="portal-card-name">Anggota Koperasi</h3>
                        </div>
                    </div>
                    <p class="portal-card-text">
                        Akses mandiri cek simpanan pokok/wajib, pengajuan pinjaman online, dan status tagihan angsuran.
                    </p>
                    <div class="portal-card-actions">
                        <a href="{{ route('login') }}" class="btn-portal btn-portal-member">
                            <span>Masuk Portal Anggota</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <div class="demo-cred-box">
                            <span class="demo-cred-label">Akun Demo:</span>
                            <code>budi@gmail.com</code>
                            <button type="button" class="btn-copy-cred" onclick="copyCred('budi@gmail.com', 'password123', this)" title="Salin kredensial">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Karyawan Perusahaan -->
                <div class="portal-card karyawan-card">
                    <div class="portal-card-head">
                        <div class="portal-avatar karyawan-avatar">
                            <i class="fa-solid fa-id-badge"></i>
                        </div>
                        <div>
                            <span class="portal-badge badge-karyawan">Internal Staff</span>
                            <h3 class="portal-card-name">Karyawan KOPIN</h3>
                        </div>
                    </div>
                    <p class="portal-card-text">
                        Bebas simpanan pokok & wajib. Menabung simpanan sukarela dan bayar cicilan otomatis via potong gaji bulanan.
                    </p>
                    <div class="portal-card-actions">
                        <a href="{{ route('karyawan.login') }}" class="btn-portal btn-portal-karyawan">
                            <span>Masuk Portal Karyawan</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <div class="demo-cred-box">
                            <span class="demo-cred-label">Akun Demo:</span>
                            <code>karyawan@kopin.id</code>
                            <button type="button" class="btn-copy-cred" onclick="copyCred('karyawan@kopin.id', 'password123', this)" title="Salin kredensial">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KATEGORI 2: OPERASIONAL & MANAJEMEN KANTOR -->
            <div class="portal-group-label" style="margin-top: 24px;">
                <i class="fa-solid fa-building-shield" style="color: #4f46e5;"></i> Operasional Kantor & Manajemen
            </div>
            <div class="portal-cards-grid grid-3">
                <!-- Card 3: Kasir / Teller -->
                <div class="portal-card kasir-card">
                    <div class="portal-card-head">
                        <div class="portal-avatar kasir-avatar">
                            <i class="fa-solid fa-cash-register"></i>
                        </div>
                        <div>
                            <span class="portal-badge badge-kasir">Loket Frontliner</span>
                            <h3 class="portal-card-name">Kasir / Teller</h3>
                        </div>
                    </div>
                    <p class="portal-card-text">
                        Penerimaan tunai simpanan, bayar angsuran loket, penarikan BKK & serah terima fisik kas harian.
                    </p>
                    <div class="portal-card-actions">
                        <a href="{{ route('kasir.login') }}" class="btn-portal btn-portal-kasir">
                            <span>Masuk Kasir</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <div class="demo-cred-box">
                            <span class="demo-cred-label">Akun Demo:</span>
                            <code>kasir@kopin.id</code>
                            <button type="button" class="btn-copy-cred" onclick="copyCred('kasir@kopin.id', 'password123', this)" title="Salin kredensial">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Finance & Treasury -->
                <div class="portal-card finance-card">
                    <div class="portal-card-head">
                        <div class="portal-avatar finance-avatar">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <div>
                            <span class="portal-badge badge-finance">Treasury & Kas</span>
                            <h3 class="portal-card-name">Finance Officer</h3>
                        </div>
                    </div>
                    <p class="portal-card-text">
                        Verifikasi penerimaan fisik closing teller, alokasi cadangan modal 10% & manajemen likuiditas.
                    </p>
                    <div class="portal-card-actions">
                        <a href="{{ route('finance.login') }}" class="btn-portal btn-portal-finance">
                            <span>Masuk Finance</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <div class="demo-cred-box">
                            <span class="demo-cred-label">Akun Demo:</span>
                            <code>finance@kopin.id</code>
                            <button type="button" class="btn-copy-cred" onclick="copyCred('finance@kopin.id', 'password123', this)" title="Salin kredensial">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Administrator Sistem -->
                <div class="portal-card admin-card">
                    <div class="portal-card-head">
                        <div class="portal-avatar admin-avatar">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <span class="portal-badge badge-admin">Komite & Pengawas</span>
                            <h3 class="portal-card-name">Administrator</h3>
                        </div>
                    </div>
                    <p class="portal-card-text">
                        Approval komite pinjaman, verifikasi berkas anggota, kelola produk & pengawasan audit trail.
                    </p>
                    <div class="portal-card-actions">
                        <a href="{{ route('admin.login') }}" class="btn-portal btn-portal-admin">
                            <span>Masuk Admin</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <div class="demo-cred-box">
                            <span class="demo-cred-label">Akun Demo:</span>
                            <code>admin@kopin.id</code>
                            <button type="button" class="btn-copy-cred" onclick="copyCred('admin@kopin.id', 'password123', this)" title="Salin kredensial">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer Modal -->
        <div class="login-modal-footer">
            <div style="font-size: 13px; color: #64748B;">
                Belum terdaftar sebagai anggota KOPIN?
                <a href="{{ route('register') }}" style="color: #059669; font-weight: 700; text-decoration: none; margin-left: 4px;">
                    Daftar Keanggotaan Baru <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
                </a>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeLoginModal()">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- STYLES UNTUK MODAL SELECTOR PORTAL -->
<style>
.login-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.25s ease, visibility 0.25s ease;
}

.login-modal-backdrop.is-active {
    opacity: 1;
    visibility: visible;
}

.login-modal-dialog {
    background: #FFFFFF;
    border-radius: 20px;
    max-width: 880px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35);
    border: 1px solid #E2E8F0;
    padding: 28px;
    transform: scale(0.96) translateY(8px);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-sizing: border-box;
}

.login-modal-backdrop.is-active .login-modal-dialog {
    transform: scale(1) translateY(0);
}

.login-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 16px;
    border-bottom: 1px solid #F1F5F9;
}

.login-modal-icon-badge {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
}

.login-modal-tagline {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    color: #059669;
    display: block;
    margin-bottom: 2px;
}

.login-modal-title {
    font-size: 20px;
    font-weight: 800;
    color: #0F172A;
    margin: 0;
}

.login-modal-close {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #F1F5F9;
    border: none;
    color: #64748B;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.login-modal-close:hover {
    background: #E2E8F0;
    color: #0F172A;
}

.login-modal-desc {
    color: #64748B;
    font-size: 13px;
    margin: 14px 0 20px 0;
}

.portal-group-label {
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #475569;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.portal-cards-grid {
    display: grid;
    gap: 16px;
}

.portal-cards-grid.grid-2 {
    grid-template-columns: repeat(2, 1fr);
}

.portal-cards-grid.grid-3 {
    grid-template-columns: repeat(3, 1fr);
}

.portal-card {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 14px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.2s ease;
}

.portal-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    border-color: #CBD5E1;
}

.portal-card-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
}

.portal-avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.member-avatar { background: #ECFDF5; color: #059669; }
.karyawan-avatar { background: #EEF2FF; color: #4F46E5; }
.kasir-avatar { background: #FEF3C7; color: #D97706; }
.finance-avatar { background: #F3E8FF; color: #7C3AED; }
.admin-avatar { background: #F1F5F9; color: #334155; }

.portal-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    display: inline-block;
    margin-bottom: 2px;
}

.badge-member { background: #D1FAE5; color: #065F46; }
.badge-karyawan { background: #E0E7FF; color: #3730A3; }
.badge-kasir { background: #FDE68A; color: #92400E; }
.badge-finance { background: #EDE9FE; color: #5B21B6; }
.badge-admin { background: #E2E8F0; color: #1E293B; }

.portal-card-name {
    font-size: 15px;
    font-weight: 800;
    color: #0F172A;
    margin: 0;
}

.portal-card-text {
    font-size: 12px;
    color: #64748B;
    line-height: 1.5;
    margin: 0 0 14px 0;
    flex-grow: 1;
}

.portal-card-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-portal {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-portal-member { background: #059669; color: #FFFFFF; }
.btn-portal-member:hover { background: #047857; }

.btn-portal-karyawan { background: #4F46E5; color: #FFFFFF; }
.btn-portal-karyawan:hover { background: #4338CA; }

.btn-portal-kasir { background: #D97706; color: #FFFFFF; }
.btn-portal-kasir:hover { background: #B45309; }

.btn-portal-finance { background: #7C3AED; color: #FFFFFF; }
.btn-portal-finance:hover { background: #6D28D9; }

.btn-portal-admin { background: #1E293B; color: #FFFFFF; }
.btn-portal-admin:hover { background: #0F172A; }

.demo-cred-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #FFFFFF;
    border: 1px dashed #CBD5E1;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 11px;
}

.demo-cred-label {
    color: #94A3B8;
    font-size: 10px;
}

.demo-cred-box code {
    color: #334155;
    font-family: monospace;
    font-weight: 600;
    font-size: 11px;
}

.btn-copy-cred {
    background: none;
    border: none;
    color: #64748B;
    cursor: pointer;
    padding: 2px 4px;
    border-radius: 4px;
    font-size: 12px;
    transition: color 0.15s;
}

.btn-copy-cred:hover {
    color: #059669;
}

.login-modal-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 18px;
    margin-top: 20px;
    border-top: 1px solid #F1F5F9;
    flex-wrap: wrap;
    gap: 12px;
}

@media (max-width: 768px) {
    .portal-cards-grid.grid-2,
    .portal-cards-grid.grid-3 {
        grid-template-columns: 1fr;
    }
    .login-modal-dialog {
        padding: 20px;
    }
}
</style>

<!-- SCRIPT UNTUK MODAL SELECTOR & SALIN KREDENSIAL -->
<script>
    function openLoginModal() {
        const modal = document.getElementById('loginPortalModal');
        if (modal) {
            modal.style.display = 'flex';
            // force reflow
            void modal.offsetWidth;
            modal.classList.add('is-active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeLoginModal() {
        const modal = document.getElementById('loginPortalModal');
        if (modal) {
            modal.classList.remove('is-active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            setTimeout(() => {
                if (!modal.classList.contains('is-active')) {
                    modal.style.display = 'none';
                }
            }, 250);
        }
    }

    // Tutup saat klik di luar kotak dialog
    document.getElementById('loginPortalModal')?.addEventListener('click', function (e) {
        if (e.target === this) {
            closeLoginModal();
        }
    });

    // Tutup dengan tombol keyboard ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeLoginModal();
        }
    });

    // Fungsi salin kredensial cepat ke clipboard
    function copyCred(email, password, btnElement) {
        const text = `Email: ${email}\nPassword: ${password}`;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(email).then(() => {
                const originalHtml = btnElement.innerHTML;
                btnElement.innerHTML = '<i class="fa-solid fa-check" style="color: #059669;"></i>';
                btnElement.title = 'Email tersalin!';
                setTimeout(() => {
                    btnElement.innerHTML = originalHtml;
                    btnElement.title = 'Salin kredensial';
                }, 2000);
            });
        }
    }
</script>
