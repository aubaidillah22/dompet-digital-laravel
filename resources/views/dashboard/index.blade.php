@extends('layouts.app')
@section('title', 'Dashboard - Dompet Digital')

@push('styles')
<style>
/* ── Dashboard-specific styles ──────────── */
.stats-grid-6 { grid-template-columns: repeat(3, 1fr); }

/* Category list */
.category-list { display: flex; flex-direction: column; gap: 8px; }
.category-item { display: flex; align-items: center; gap: 10px; }
.category-dot { width: 10px; height: 10px; border-radius: 4px; flex-shrink: 0; }
.category-name { flex: 1; font-size: 0.78rem; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.category-amount { font-size: 0.78rem; font-weight: 600; color: var(--text-primary); white-space: nowrap; }
.category-bar-wrap { height: 4px; background: var(--glass-bg); border-radius: 4px; overflow: hidden; margin-top: 2px; }
.category-bar { height: 100%; border-radius: 4px; transition: width 0.6s ease; }

/* Hero hover states & colors */
.hero-card-main:hover { border-color: rgba(240,180,41,0.3); box-shadow: 0 8px 24px rgba(240,180,41,0.12); }
.hero-card-savings:hover { border-color: rgba(16,185,129,0.3); box-shadow: 0 8px 24px rgba(16,185,129,0.12); }
.hero-card-main .hero-amount { color: var(--accent-gold); }
.hero-card-savings .hero-amount { color: #10b981; }
body.dark-mode .hero-card-main .hero-amount { color: #f0b429; }
body.dark-mode .hero-card-savings .hero-amount { color: #34d399; }
.hero-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; display: flex; align-items: center; gap: 6px; }
.hero-card-main .hero-glow { background: radial-gradient(circle, rgba(240,180,41,0.1) 0%, transparent 70%); }
.hero-card-savings .hero-glow { background: radial-gradient(circle, rgba(16,185,129,0.1) 0%, transparent 70%); }

/* Goal extras */
.goal-amount { font-size: 0.8rem; color: var(--text-muted); }
.goal-footer { display: flex; justify-content: space-between; margin-top: 4px; }
.goal-pct { font-size: 0.65rem; color: var(--text-muted); }
.goal-target { font-size: 0.65rem; color: var(--text-muted); }

/* Extra stat colors */
.stat-text-month { color: #8b5cf6; }
.stat-text-ratio { color: #f59e0b; }
.stat-text-avg { color: #ec4899; }
body.dark-mode .stat-text-savings { color: #34d399; }
.stat-period i { font-size: 0.55rem; opacity: 0.6; }

/* Responsive overrides */
@media (max-width: 1024px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .stats-grid-6 { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .stats-grid, .stats-grid-6 { grid-template-columns: 1fr; }
}
@media (max-width: 480px) {
    .stat-glow { display: none; }
}
</style>

<script>
function showProfilePage() {
    window.location.href = '/profile';
}

function loadUserProfile() {
    if (!currentUser) return;
    var el = document.getElementById('profileFullNameUser');
    if (el) el.value = currentUser.full_name || '';
    el = document.getElementById('profileUsernameUser');
    if (el) el.value = currentUser.username || '';
    ['currentPasswordUser','newPasswordUser','confirmPasswordUser'].forEach(function(id) {
        var e = document.getElementById(id);
        if (e) e.value = '';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var profileForm = document.getElementById('profileFormUser');
    if (profileForm) {
        profileForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            var fullName = document.getElementById('profileFullNameUser');
            if (!fullName || !fullName.value.trim()) { showToast('Nama lengkap wajib diisi', 'error'); return; }
            var btn = document.getElementById('saveProfileBtnUser');
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...'; }
            try {
                var data = { full_name: fullName.value.trim() };
                var ue = document.getElementById('profileUsernameUser');
                if (ue && ue.value) data.username = ue.value;
                await apiRequest('user/profile', 'PUT', data);
                if (currentUser) currentUser.full_name = fullName.value.trim();
                var ne = document.getElementById('userName');
                if (ne) ne.textContent = fullName.value.trim();
                var sne = document.getElementById('sidebarUserName');
                if (sne) sne.textContent = fullName.value.trim();
                showToast('Profil berhasil diperbarui', 'success');
            } catch (error) { console.error(error); }
            finally { if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan'; } }
        });
    }
    var passwordForm = document.getElementById('passwordFormUser');
    if (passwordForm) {
        passwordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            var cp = document.getElementById('currentPasswordUser');
            var np = document.getElementById('newPasswordUser');
            var conp = document.getElementById('confirmPasswordUser');
            if (!cp.value) { showToast('Password lama wajib diisi', 'error'); return; }
            if (!np.value || np.value.length < 8) { showToast('Password baru minimal 8 karakter', 'error'); return; }
            if (np.value !== conp.value) { showToast('Konfirmasi password tidak cocok', 'error'); return; }
            var btn = document.getElementById('savePasswordBtnUser');
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...'; }
            try {
                await apiRequest('user/password', 'PUT', { current_password: cp.value, new_password: np.value });
                showToast('Password berhasil diubah', 'success');
                cp.value = ''; np.value = ''; conp.value = '';
            } catch (error) { console.error(error); }
            finally { if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-key"></i> Ganti Password'; } }
        });
    }
});
</script>@endpush

@section('content')
<!-- Splash Screen -->
<div id="splashScreen" style="position:fixed;inset:0;z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;background:radial-gradient(ellipse at center,#1a1635 0%,#0f0c29 100%);transition:opacity 0.6s ease,visibility 0.6s ease;">
    <div style="position:relative;width:80px;height:80px;margin-bottom:28px;">
        <div style="position:absolute;inset:0;border-radius:50%;border:3px solid rgba(240,180,41,0.15);border-top-color:#f0b429;animation:splashSpin 1s cubic-bezier(0.6,0,0.4,1) infinite;"></div>
        <div style="position:absolute;inset:4px;border-radius:50%;border:3px solid transparent;border-bottom-color:rgba(255,140,66,0.4);animation:splashSpin 0.8s cubic-bezier(0.6,0,0.4,1) infinite reverse;"></div>
        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;"><i class="fas fa-wallet" style="font-size:28px;color:#f0b429;filter:drop-shadow(0 0 8px rgba(240,180,41,0.4));"></i></div>
    </div>
    <h2 style="font-size:1.4rem;font-weight:700;margin-bottom:6px;background:linear-gradient(90deg,#f0b429,#ff8c42);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Dompet Digital</h2>
    <p style="color:rgba(255,255,255,0.5);font-size:0.85rem;letter-spacing:1px;"><span id="splashDots">Memuat</span></p>
</div>

<script>
var dotCount=0;
window.splashDotInterval=setInterval(()=>{
    dotCount=(dotCount+1)%4;
    const el=document.getElementById('splashDots');
    if(el)el.textContent='Memuat'+'.'.repeat(dotCount);
},500);
</script>

<div class="app-container">
@include('layouts.sidebar', ['active' => 'dashboard'])

    <main class="main-content">
@include('layouts.topbar')

        <!-- Page Header -->
        <div style="margin-bottom:24px;">
            <h1 style="font-size:1.5rem;font-weight:900;letter-spacing:-0.03em;color:var(--text-primary);">
                <i class="fas fa-tachometer-alt" style="color:var(--accent-gold);margin-right:8px;"></i> Dashboard Keuangan
            </h1>
            <p style="font-size:0.82rem;color:var(--text-muted);margin-top:4px;">
                <i class="fas fa-circle" style="font-size:0.4rem;color:var(--accent-gold);vertical-align:middle;margin-right:6px;"></i>
                Ringkasan lengkap Dompet Utama &amp; Tabungan
            </p>
        </div>

        <!-- Ã¢â€â‚¬Ã¢â€â‚¬ Dual Wallet Hero Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ -->
        <div class="hero-dual">
            <div class="hero-card hero-card-main">
                <div class="hero-glow"></div>
                <div class="hero-icon"><i class="fas fa-wallet"></i></div>
                <div class="hero-body">
                    <div class="hero-label"><i class="fas fa-star" style="font-size:0.5rem;margin-right:3px;"></i> Dompet Utama</div>
                    <div class="hero-amount" id="heroMainBalance">Rp 0</div>
                    <div class="hero-sub"><i class="fas fa-circle" style="font-size:0.4rem;color:var(--accent-gold);"></i> Transaksi sehari-hari</div>
                </div>
            </div>
            <div class="hero-card hero-card-savings">
                <div class="hero-glow"></div>
                <div class="hero-icon"><i class="fas fa-piggy-bank"></i></div>
                <div class="hero-body">
                    <div class="hero-label"><i class="fas fa-star" style="font-size:0.5rem;margin-right:3px;"></i> Tabungan</div>
                    <div class="hero-amount" id="heroSavingsBalance">Rp 0</div>
                    <div class="hero-sub"><i class="fas fa-circle" style="font-size:0.4rem;color:#10b981;"></i> Simpanan masa depan</div>
                </div>
            </div>
        </div>

        <!-- Ã¢â€â‚¬Ã¢â€â‚¬ Main Stats Grid (8 cards) Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ -->
        <div class="stats-grid">
            <div class="card stat-card stat-income fade-in">
                <div class="stat-icon-wrap"><i class="fas fa-circle-down"></i></div>
                <div class="stat-body">
                    <div class="stat-label">Total Pemasukan</div>
                    <div class="stat-value stat-text-income" id="totalIncome">Rp 0</div>
                    <div class="stat-period" id="periodIncome"><i class="fas fa-infinity"></i> Semua waktu</div>
                </div>
                <div class="stat-glow stat-glow-income"></div>
            </div>
            <div class="card stat-card stat-expense fade-in">
                <div class="stat-icon-wrap"><i class="fas fa-circle-up"></i></div>
                <div class="stat-body">
                    <div class="stat-label">Total Pengeluaran</div>
                    <div class="stat-value stat-text-expense" id="totalExpense">Rp 0</div>
                    <div class="stat-period" id="periodExpense"><i class="fas fa-infinity"></i> Semua waktu</div>
                </div>
                <div class="stat-glow stat-glow-expense"></div>
            </div>
            <div class="card stat-card stat-balance fade-in">
                <div class="stat-icon-wrap"><i class="fas fa-wallet"></i></div>
                <div class="stat-body">
                    <div class="stat-label">Saldo</div>
                    <div class="stat-value stat-text-balance" id="balance">Rp 0</div>
                    <div class="stat-period" id="periodBalance"><i class="fas fa-infinity"></i> Semua waktu</div>
                </div>
                <div class="stat-glow stat-glow-balance"></div>
            </div>
            <div class="card stat-card stat-count fade-in">
                <div class="stat-icon-wrap"><i class="fas fa-arrow-right-arrow-left"></i></div>
                <div class="stat-body">
                    <div class="stat-label">Total Transaksi</div>
                    <div class="stat-value stat-text-count" id="totalTransactions">0</div>
                    <div class="stat-period" id="periodCount"><i class="fas fa-infinity"></i> Semua waktu</div>
                </div>
                <div class="stat-glow stat-glow-count"></div>
            </div>
        </div>

        <!-- Ã¢â€â‚¬Ã¢â€â‚¬ Second Stats Row (monthly + ratio) Ã¢â€â‚¬Ã¢â€â‚¬ -->
        <div class="stats-grid">
            <div class="card stat-card stat-month fade-in">
                <div class="stat-icon-wrap"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-body">
                    <div class="stat-label">Pemasukan Bulan Ini</div>
                    <div class="stat-value stat-value-sm stat-text-month" id="monthlyIncome">Rp 0</div>
                </div>
            </div>
            <div class="card stat-card stat-expense fade-in">
                <div class="stat-icon-wrap"><i class="fas fa-calendar-times"></i></div>
                <div class="stat-body">
                    <div class="stat-label">Pengeluaran Bulan Ini</div>
                    <div class="stat-value stat-value-sm stat-text-expense" id="monthlyExpense">Rp 0</div>
                </div>
            </div>
            <div class="card stat-card stat-ratio fade-in">
                <div class="stat-icon-wrap"><i class="fas fa-percentage"></i></div>
                <div class="stat-body">
                    <div class="stat-label">Rasio Pengeluaran</div>
                    <div class="stat-value stat-value-sm stat-text-ratio" id="expenseRatio">0%</div>
                    <div class="stat-period">dari total pemasukan</div>
                </div>
            </div>
            <div class="card stat-card stat-avg fade-in">
                <div class="stat-icon-wrap"><i class="fas fa-chart-line"></i></div>
                <div class="stat-body">
                    <div class="stat-label">Rata-rata / Bulan</div>
                    <div class="stat-value stat-value-sm stat-text-avg" id="avgMonthly">Rp 0</div>
                </div>
            </div>
        </div>

        <!-- Ã¢â€â‚¬Ã¢â€â‚¬ Kategori Charts Row (Pemasukan &amp; Pengeluaran) Ã¢â€â‚¬Ã¢â€â‚¬ -->
        <div class="grid-2">
            <div class="card chart-card chart-card-income fade-in">
                <div class="chart-card-header">
                    <div class="chart-card-icon"><i class="fas fa-chart-doughnut"></i></div>
                    <div>
                        <div class="chart-card-title">Pemasukan per Kategori</div>
                        <div class="chart-card-subtitle">Top pemasukan</div>
                    </div>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="incomeDoughnutChart"></canvas>
                </div>
            </div>
            <div class="card chart-card chart-card-expense fade-in">
                <div class="chart-card-header">
                    <div class="chart-card-icon"><i class="fas fa-chart-doughnut"></i></div>
                    <div>
                        <div class="chart-card-title">Pengeluaran per Kategori</div>
                        <div class="chart-card-subtitle">Top pengeluaran</div>
                    </div>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="expensePieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Ã¢â€â‚¬Ã¢â€â‚¬ Tren &amp; Tabungan Charts Row Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ -->
        <div class="grid-2">
            <div class="card chart-card chart-card-trend fade-in">
                <div class="chart-card-header">
                    <div class="chart-card-icon"><i class="fas fa-chart-bar"></i></div>
                    <div>
                        <div class="chart-card-title">Tren Pemasukan &amp; Pengeluaran</div>
                        <div class="chart-card-subtitle">12 bulan terakhir</div>
                    </div>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
            <div class="card chart-card chart-card-savings fade-in">
                <div class="chart-card-header">
                    <div class="chart-card-icon"><i class="fas fa-piggy-bank"></i></div>
                    <div>
                        <div class="chart-card-title">Pertumbuhan Tabungan</div>
                        <div class="chart-card-subtitle">Akumulasi per bulan</div>
                    </div>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="savingsGrowthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Ã¢â€â‚¬Ã¢â€â‚¬ Savings Goal Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ -->
        <div class="goal-card fade-in">
            <div class="goal-header">
                <div class="goal-title">
                    <i class="fas fa-bullseye" style="color:var(--accent-gold);font-size:0.9rem;"></i>
                    Progress Tabungan
                </div>
                <span class="goal-amount" id="goalProgressText">Rp 0 / Rp 10.000.000</span>
            </div>
            <div class="goal-bar">
                <div class="goal-bar-fill" id="goalProgressBar" style="width:0%;"></div>
            </div>
            <div class="goal-footer">
                <span class="goal-pct" id="goalPctText">0% tercapai</span>
                <span class="goal-target">Target: Rp 10.000.000</span>
            </div>
        </div>

        <!-- Ã¢â€â‚¬Ã¢â€â‚¬ Quick Actions Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ -->
        <div class="quick-actions">
            <a href="/dompet" class="btn btn-primary">
                <i class="fas fa-wallet"></i> Kelola Dompet Utama
            </a>
            <a href="/tabungan" class="btn btn-green">
                <i class="fas fa-piggy-bank"></i> Kelola Tabungan
            </a>
            <button class="btn btn-outline" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>

        <!-- Ã¢â€â‚¬Ã¢â€â‚¬ Recent Transactions Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ -->
        <div class="card fade-in">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;padding-bottom:10px;border-bottom:1px solid var(--glass-border);">
                <div style="display:flex;align-items:center;gap:8px;font-weight:700;font-size:0.85rem;color:var(--text-primary);">
                    <i class="fas fa-clock-rotate" style="color:var(--accent-gold);"></i> Transaksi Terbaru
                </div>
                <span style="font-size:0.68rem;color:var(--text-muted);">10 transaksi terakhir</span>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Kategori</th>
                            <th>Wallet</th>
                            <th>Nominal</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="recentTransactions">
                        <tr><td colspan="6" style="text-align:center;padding:32px;">
                            <div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Memuat data...</p></div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <footer class="app-footer">
            <span>&copy; 2026.</span>
            <span class="footer-heart">&nbsp;Made with <i class="fas fa-heart"></i> and coffee, dedicated to be useful.</span>
        </footer>
    </main>
</div>

<!-- Modal Pengaturan Profil -->
<div id="profileModal" class="modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);backdrop-filter:blur(8px);z-index:1000;align-items:center;justify-content:center;">
    <div class="modal-content" style="background:var(--modal-bg);backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:24px;padding:28px;max-width:500px;width:90%;">
        <h3 style="margin-bottom:20px;color:var(--accent-gold);"><i class="fas fa-user-cog"></i> Pengaturan Profil</h3>
        <div style="margin-top:16px;">
            <h4 style="color:var(--text-primary);font-size:0.95rem;font-weight:700;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--glass-border);">
                <i class="fas fa-id-card"></i> Data Profil
            </h4>
            <form id="profileForm">
                @csrf
                <div class="form-group"><label>Nama Lengkap</label><input type="text" class="form-control" id="profileFullName" required placeholder="Masukkan nama lengkap"></div>
                <div class="form-group"><label>Username</label><input type="text" class="form-control" id="profileUsername" required minlength="3" placeholder="Minimal 3 karakter"></div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;" id="saveProfileBtn"><i class="fas fa-save"></i> Simpan Profil</button>
            </form>
        </div>
        <div style="margin-top:20px;">
            <h4 style="color:var(--text-primary);font-size:0.95rem;font-weight:700;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--glass-border);">
                <i class="fas fa-lock"></i> Ganti Password
            </h4>
            <form id="passwordForm">
                @csrf
                <div class="form-group"><label>Password Lama</label><input type="password" class="form-control" id="currentPassword" required placeholder="Masukkan password lama"></div>
                <div class="form-group"><label>Password Baru</label><input type="password" class="form-control" id="newPassword" required minlength="8" placeholder="Minimal 8 karakter"></div>
                <div class="form-group"><label>Konfirmasi Password Baru</label><input type="password" class="form-control" id="confirmPassword" required placeholder="Ulangi password baru"></div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;" id="savePasswordBtn"><i class="fas fa-key"></i> Ganti Password</button>
            </form>
        </div>
        <div style="margin-top:16px;">
            <button class="btn btn-outline" onclick="closeModal('profileModal')" style="width:100%;justify-content:center;"><i class="fas fa-times"></i> Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.DOMpetConfig = {
    apiBase: '{{ url("/api") }}',
    logoutUrl: '{{ url("/logout") }}',
};
</script>
<script src="{{ url('/js/dashboard.js') }}"></script>
@endpush
