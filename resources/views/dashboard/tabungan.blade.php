@extends('layouts.app')

@section('title', 'Tabungan - Dompet Digital')

@push('styles')
<style>
/* ── Tabungan page: green theme overrides ── */
.main-content {
    --accent-gold: #059669;
    --accent-orange: #10b981;
}
body.dark-mode .main-content {
    --accent-gold: #34d399;
    --accent-orange: #6ee7b7;
}

/* Sidebar overrides for tabungan (green active) */
.sidebar .nav-link.active {
    background: linear-gradient(90deg, rgba(52,211,153,0.18), rgba(16,185,129,0.06)) !important;
    color: #059669 !important;
}
.sidebar .nav-link.active::before {
    background: linear-gradient(180deg, #059669, #10b981) !important;
    box-shadow: 0 0 8px rgba(52,211,153,0.3) !important;
}
.sidebar .nav-link.active i { color: #059669 !important; }

/* Card hover (green border) */
.card:hover { border-color: rgba(52,211,153,0.25); }

/* Search bar overrides */
.search-bar:focus-within { box-shadow: 0 0 0 3px rgba(52,211,153,0.15); }

/* Button overrides */
.btn-outline:hover { background: rgba(52,211,153,0.15); border-color: var(--accent-gold); color: var(--accent-gold); }
.btn-gold { background: linear-gradient(90deg, var(--accent-gold), var(--accent-orange)); color: #1e1a0c; }
.btn-gold:hover { transform: scale(1.02); opacity: 0.95; }

/* Form control overrides */
.form-control:focus { box-shadow: 0 0 0 3px rgba(52,211,153,0.15); }

/* Chart card */
.chart-card-savings::before { background: linear-gradient(90deg, #059669, #10b981 60%, transparent); }

/* Hero section (savings/green theme) */
.hero-section {
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 28px;
    padding: 32px;
    background: linear-gradient(135deg, rgba(52,211,153,0.08), rgba(16,185,129,0.03));
    border: 1px solid rgba(52,211,153,0.15);
    border-radius: 28px;
    position: relative;
    overflow: hidden;
}
.hero-section::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #059669, #10b981, #059669);
}
.hero-icon {
    width: 72px; height: 72px;
    border-radius: 24px;
    background: linear-gradient(135deg, #059669, #10b981);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem; color: #1e1a0c;
    flex-shrink: 0;
    box-shadow: 0 8px 24px rgba(52,211,153,0.3);
}
.hero-body { flex: 1; }
.hero-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 2px; }
.hero-amount { font-size: 2.4rem; font-weight: 900; letter-spacing: -0.03em; color: #059669; line-height: 1.2; }
body.dark-mode .hero-amount { color: #34d399; }
.hero-sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }
.hero-glow {
    position: absolute; width: 300px; height: 300px;
    border-radius: 50%; right: -60px; top: -60px;
    background: radial-gradient(circle, rgba(52,211,153,0.1) 0%, transparent 70%);
    pointer-events: none;
}

/* Stats grid */
.stats-grid { grid-template-columns: repeat(3, 1fr); }
.stats-grid-4 { grid-template-columns: repeat(4, 1fr); }

/* Stat card overrides (green) */
.stat-icon-wrap {
    background: linear-gradient(135deg, rgba(52,211,153,0.2), rgba(52,211,153,0.06));
    color: #059669;
    border: 1px solid rgba(52,211,153,0.2);
}
.stat-balance { background: linear-gradient(135deg, var(--card-bg) 60%, rgba(52,211,153,0.04)) !important; }
body.dark-mode .stat-balance { background: linear-gradient(135deg, rgba(255,255,255,0.06) 50%, rgba(52,211,153,0.06)) !important; }
body.dark-mode .stat-balance .stat-icon-wrap { background: linear-gradient(135deg, rgba(52,211,153,0.18), rgba(52,211,153,0.04)); border-color: rgba(52,211,153,0.15); }
.stat-glow-balance { background: var(--accent-gold); }
.stat-value { font-size: 1.3rem; }

/* Sidebar ::after uses main-content vars */
.sidebar::after {
    opacity: 0.5;
    animation: sidebarBorderGlow 4s ease-in-out infinite;
}

/* Floating modal green theme */
.modal-floating-card { box-shadow: 0 32px 80px rgba(0,0,0,0.35), 0 0 0 1px rgba(16,185,129,0.06); }
.floating-accent-bar {
    background: linear-gradient(90deg, #059669, #10b981, #34d399, #10b981, #059669);
    background-size: 200% 100%;
    animation: accentBarShimmer 3s ease-in-out infinite;
}
.floating-icon-wrap {
    background: linear-gradient(135deg, #059669, #10b981);
    box-shadow: 0 4px 16px rgba(16,185,129,0.35);
    animation: iconPulse 2.5s ease-in-out infinite;
}
@keyframes iconPulse {
    0%, 100% { box-shadow: 0 4px 16px rgba(16,185,129,0.35); }
    50% { box-shadow: 0 4px 28px rgba(16,185,129,0.6); }
}
.floating-label i { color: #10b981; }
.floating-input:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
}
.floating-input-glow { background: linear-gradient(90deg, transparent, #10b981, transparent); }
.floating-currency-prefix { color: #10b981; }
.floating-btn-primary {
    background: linear-gradient(90deg, #059669, #10b981);
    box-shadow: 0 4px 14px rgba(16,185,129,0.3);
}
.floating-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16,185,129,0.4);
}
.floating-btn-secondary:hover {
    background: rgba(16,185,129,0.08);
    border-color: rgba(16,185,129,0.3);
    color: #10b981;
}
.particle.p1 { background: radial-gradient(circle, #10b981, transparent); }
.particle.p2 { background: radial-gradient(circle, #059669, transparent); }
.particle.p3 { background: radial-gradient(circle, #34d399, transparent); }
.particle.p4 { background: radial-gradient(circle, #10b981, transparent); }

/* Compact variant for target modal */
.modal-floating-card.compact { max-width: 420px; }
.modal-floating-card.compact .floating-header { padding: 20px 24px 12px; }
.modal-floating-card.compact form { padding: 0 24px 20px; }
.modal-floating-card.compact .floating-icon-wrap { width: 40px; height: 40px; font-size: 1.1rem; border-radius: 13px; }
.modal-floating-card.compact .floating-title { font-size: 1.05rem; }
.modal-floating-card.compact .floating-field { margin-bottom: 14px; }

/* Pagination green overrides */
.page-btn:hover:not(.disabled):not(.active) { background: rgba(52,211,153,0.15); border-color: var(--accent-gold); color: var(--accent-gold); }

/* Goal card green fill */
.goal-bar-fill { background: linear-gradient(90deg, #059669, #10b981); }

/* Responsive */
@media (max-width: 1024px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .stats-grid, .stats-grid-4 { grid-template-columns: 1fr; }
    .hero-section { flex-direction: column; text-align: center; padding: 24px; gap: 16px; }
    .hero-amount { font-size: 1.8rem; }
}
@media (max-width: 480px) {
    .hero-section { padding: 18px; }
    .hero-icon { width: 56px; height: 56px; font-size: 1.4rem; border-radius: 18px; }
    .hero-amount { font-size: 1.5rem; }
    .floating-header { padding: 18px 18px 12px; }
    .modal-floating-card.compact .floating-header { padding: 16px 18px 10px; }
    .modal-floating-card.compact form { padding: 0 18px 18px; }
    .floating-title { font-size: 1rem; }
    .floating-icon-wrap { width: 40px; height: 40px; font-size: 1.1rem; border-radius: 13px; }
    .floating-input { padding: 9px 14px; font-size: 0.82rem; border-radius: 12px; }
    .floating-amount-input { padding-left: 36px; font-size: 1rem; }
    .floating-btn { padding: 10px 16px; font-size: 0.8rem; border-radius: 12px; }
    .modal-floating-card { border-radius: 22px; }
}

/* Modal form padding (tabungan override) */
.modal form { padding: 0 28px 24px; }
</style>
@endpush

@section('content')
<div class="app-container">
@include('layouts.sidebar', ['active' => 'tabungan'])

    <main class="main-content">
@include('layouts.topbar', ['profileOnclick' => 'showProfilePage()'])

        <!-- Hero -->
        <div class="hero-section fade-in">
            <div class="hero-icon"><i class="fas fa-piggy-bank"></i></div>
            <div class="hero-body">
                <div class="hero-label">Saldo Tabungan</div>
                <div class="hero-amount" id="savingsBalance">Rp 0</div>
                <div class="hero-sub">Simpan uang untuk masa depanmu</div>
            </div>
            <div class="hero-glow"></div>
        </div>

        <!-- Stats -->
        <div class="stats-grid stats-grid-4">
            <div class="card stat-card stat-income fade-in">
                <div class="stat-icon-wrap"><i class="fas fa-coins"></i></div>
                <div class="stat-body">
                    <div class="stat-label">Total Ditabung</div>
                    <div class="stat-value stat-text-income" id="totalSaved">Rp 0</div>
                </div>
                <div class="stat-glow stat-glow-income"></div>
            </div>
            <div class="card stat-card stat-count fade-in">
                <div class="stat-icon-wrap"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-body">
                    <div class="stat-label">Bulan Ini</div>
                    <div class="stat-value" id="monthlySaved">Rp 0</div>
                </div>
                <div class="stat-glow stat-glow-count"></div>
            </div>
            <div class="card stat-card stat-balance fade-in">
                <div class="stat-icon-wrap"><i class="fas fa-chart-line"></i></div>
                <div class="stat-body">
                    <div class="stat-label">Rata-rata / Bulan</div>
                    <div class="stat-value" id="avgMonthly">Rp 0</div>
                </div>
                <div class="stat-glow stat-glow-balance"></div>
            </div>
            <div class="card stat-card stat-expense fade-in">
                <div class="stat-icon-wrap"><i class="fas fa-arrow-right-arrow-left"></i></div>
                <div class="stat-body">
                    <div class="stat-label">Total Transaksi</div>
                    <div class="stat-value stat-text-count" id="totalTxCount">0</div>
                </div>
                <div class="stat-glow stat-glow-expense"></div>
            </div>
        </div>

        <!-- Savings Goal -->
        <div class="card chart-card chart-card-savings fade-in" style="margin-bottom: 24px; padding: 20px 24px;">
            <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-bullseye" style="color: var(--accent-gold); font-size: 0.95rem;"></i>
                            <span style="font-weight: 700; font-size: 0.85rem; color: var(--text-primary);">Progress Tabungan</span>
                        </div>
                        <span style="font-size: 0.8rem; color: var(--text-muted);" id="goalProgressText">Rp 0 / Rp 10.000.000</span>
                    </div>
                    <div style="
                        height: 8px; background: var(--glass-bg); border-radius: 40px;
                        overflow: hidden; border: 1px solid var(--glass-border);
                    ">
                        <div id="goalProgressBar" style="
                            height: 100%; width: 0%;
                            background: linear-gradient(90deg, #059669, #10b981);
                            border-radius: 40px;
                            transition: width 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
                        "></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 4px;">
                        <span style="font-size: 0.65rem; color: var(--text-muted);" id="goalPctText">0% tercapai</span>
                        <span style="font-size: 0.65rem; color: var(--text-muted); cursor: pointer;" id="goalTargetText" onclick="openTargetModal()" title="Klik untuk ubah target">
                            <i class="fas fa-pencil-alt" style="font-size:0.55rem;margin-right:3px;opacity:0.6;"></i> Target: Rp 10.000.000
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Savings Growth Chart -->
        <div class="card chart-card chart-card-savings fade-in" style="margin-bottom: 24px; padding: 20px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--glass-border);">
                <div style="
                    width: 40px; height: 40px; border-radius: 12px;
                    background: linear-gradient(135deg, rgba(52,211,153,0.2), rgba(52,211,153,0.06));
                    display: flex; align-items: center; justify-content: center;
                    color: #059669; font-size: 1rem;
                    border: 1px solid rgba(52,211,153,0.2); flex-shrink: 0;
                ">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary);">Grafik Pertumbuhan Tabungan</div>
                    <div style="font-size: 0.7rem; color: var(--text-muted);">Saldo tabungan per bulan</div>
                </div>
            </div>
            <div style="display: flex; justify-content: center; align-items: center; min-height: 240px; position: relative;">
                <canvas id="savingsGrowthChart" style="max-height: 260px; max-width: 100%;"></canvas>
            </div>
        </div>

        <!-- Transfer CTA -->
        <div style="margin-bottom: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
            <button class="btn btn-gold" onclick="openTransferModal()">
                <i class="fas fa-arrow-up"></i> Topup Saldo dari Dompet
            </button>
            <button class="btn btn-outline" onclick="refreshData()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>

        <!-- Riwayat -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid var(--glass-border);">
                <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.85rem; color: var(--text-primary);">
                    <i class="fas fa-history" style="color: var(--accent-gold);"></i> Riwayat Tabungan
                </div>
            </div>
            <div class="search-bar">
                <i class="fas fa-search search-bar-icon"></i>
                <input type="text" class="search-bar-input" id="savingsSearch" placeholder="Cari transaksi tabungan..." autocomplete="off">
                <button class="search-bar-clear" id="savingsSearchClearBtn" style="display:none;" onclick="clearSavingsSearch()"><i class="fas fa-times"></i></button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="savingsTable">
                        <tr><td colspan="4" style="text-align: center; padding: 32px;">
                            <div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Memuat data...</p></div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
            <div class="search-no-result" id="savingsSearchNoResult">
                <i class="fas fa-search"></i>
                <p>Tidak ada transaksi yang cocok</p>
            </div>
            <div id="savingsPagination"></div>
        </div>

        <!-- Modal Target — Premium Floating -->
<div id="targetModal" class="modal modal-floating">
    <div class="modal-floating-overlay"></div>
    <div class="modal-floating-card compact" id="floatingTargetCard">
        <!-- Decorative particles -->
        <div class="modal-particles">
            <div class="particle p1"></div>
            <div class="particle p2"></div>
            <div class="particle p3"></div>
            <div class="particle p4"></div>
        </div>
        <!-- Gradient top bar -->
        <div class="floating-accent-bar"></div>
        
        <!-- Header -->
        <div class="floating-header">
            <div class="floating-icon-wrap">
                <i class="fas fa-bullseye"></i>
            </div>
            <div>
                <h3 class="floating-title">Ubah Target Tabungan</h3>
                <p class="floating-subtitle">Tetapkan target yang ingin kamu capai</p>
            </div>
            <button type="button" class="floating-close" onclick="closeTargetModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="targetForm">
            @csrf
            <!-- Nominal Target -->
            <div class="floating-field" style="--i:1;">
                <label class="floating-label">
                    <i class="fas fa-coins"></i> Target Tabungan (Rp)
                </label>
                <div class="floating-amount-wrap">
                    <span class="floating-currency-prefix">Rp</span>
                    <input type="text" class="floating-input floating-amount-input rupiah-input" id="targetAmount" required placeholder="0">
                </div>
            </div>

            <!-- Actions -->
            <div class="floating-actions" style="--i:2;">
                <button type="button" class="floating-btn floating-btn-primary" onclick="saveTarget()" id="targetBtn">
                    <i class="fas fa-check"></i>
                    <span>Simpan Target</span>
                </button>
                <button type="button" class="floating-btn floating-btn-secondary" onclick="closeTargetModal()">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

        <!-- Modal Transfer/Topup — Premium Floating -->
<div id="transferModal" class="modal modal-floating">
    <div class="modal-floating-overlay"></div>
    <div class="modal-floating-card" id="floatingTransferCard">
        <!-- Decorative particles -->
        <div class="modal-particles">
            <div class="particle p1"></div>
            <div class="particle p2"></div>
            <div class="particle p3"></div>
            <div class="particle p4"></div>
        </div>
        <!-- Gradient top bar -->
        <div class="floating-accent-bar"></div>
        
        <!-- Header -->
        <div class="floating-header">
            <div class="floating-icon-wrap">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div>
                <h3 class="floating-title">Topup Saldo ke Tabungan</h3>
                <p class="floating-subtitle">Pindahkan saldo dari Dompet Utama ke Tabungan</p>
            </div>
            <button type="button" class="floating-close" onclick="closeTransferModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="transferForm">
            @csrf
            <input type="hidden" id="transferFromWallet">
            <input type="hidden" id="transferToWallet">
            
            <!-- Dari Wallet -->
            <div class="floating-field" style="--i:1;">
                <label class="floating-label">
                    <i class="fas fa-wallet"></i> Dari Wallet
                </label>
                <p style="color: var(--text-primary); font-weight: 600; font-size: 0.88rem; padding: 6px 0;" id="transferFromLabel">Dompet Utama</p>
            </div>

            <!-- Ke Wallet -->
            <div class="floating-field" style="--i:2;">
                <label class="floating-label">
                    <i class="fas fa-piggy-bank"></i> Ke Wallet
                </label>
                <div class="floating-amount-wrap" style="padding: 6px 0;">
                    <span style="font-size: 0.88rem; font-weight: 700; color: #10b981;">Tabungan</span>
                </div>
            </div>

            <!-- Nominal -->
            <div class="floating-field" style="--i:3;">
                <label class="floating-label">
                    <i class="fas fa-money-bill-wave"></i> Nominal (Rp)
                </label>
                <div class="floating-amount-wrap">
                    <span class="floating-currency-prefix">Rp</span>
                    <input type="text" class="floating-input floating-amount-input rupiah-input" id="transferAmount" required placeholder="0">
                </div>
            </div>

            <!-- Catatan -->
            <div class="floating-field" style="--i:4;">
                <label class="floating-label">
                    <i class="fas fa-pen"></i> Catatan <span class="floating-optional">(opsional)</span>
                </label>
                <input type="text" class="floating-input" id="transferNote" placeholder="Catatan transfer" value="Menabung">
            </div>

            <!-- Actions -->
            <div class="floating-actions" style="--i:5;">
                <button type="button" class="floating-btn floating-btn-primary" onclick="doTransfer()" id="transferBtn">
                    <i class="fas fa-paper-plane"></i>
                    <span>Topup Saldo ke Tabungan</span>
                </button>
                <button type="button" class="floating-btn floating-btn-secondary" onclick="closeTransferModal()">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
    
@include('layouts.profile-form-inline')
        <footer class="app-footer">
            <span>&copy; 2026.</span>
            <span class="footer-heart">&nbsp;Made with <i class="fas fa-heart"></i> and coffee, dedicated to be useful.</span>
        </footer>
    </main>
</div>


@push('scripts')
<script>
window.DOMpetConfig = {
    apiBase: '{{ url("/api") }}',
    logoutUrl: '{{ url("/logout") }}',
};

// Ã¢â€â‚¬Ã¢â€â‚¬ State Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
var currentUser = null;
var savingsWalletId = null;
var mainWalletId = null;
var wallets = [];
var savingsTarget = 10000000;
var pagination = { currentPage: 1, perPage: 10, total: 0, totalPages: 0 };
var API_BASE = window.DOMpetConfig.apiBase;

// Ã¢â€â‚¬Ã¢â€â‚¬ Utilities Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(amount);
}

function escapeHtml(text) {
    if (!text) return ''; const div = document.createElement('div'); div.textContent = text; return div.innerHTML;
}

function parseCurrencyText(text) {
    if (!text) return 0; return parseInt(text.replace(/[^0-9]/g, '')) || 0;
}

function parseRupiahToNumber(s) {
    return parseInt(s.replace(/[^0-9]/g, '')) || 0;
}

function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

function showToast(message, type = 'success') {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: type, title: message, toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, background: '#1a1a2e', color: '#fff' });
    } else { console.log(`[${type}] ${message}`); }
}

function autoFormatRupiah(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value) { value = parseInt(value).toLocaleString('id-ID'); input.value = value; }
}

// Ã¢â€â‚¬Ã¢â€â‚¬ API Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
async function apiRequest(endpoint, method = 'GET', data = null, options = {}) {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta || !csrfMeta.content) throw new Error('CSRF token tidak ditemukan.');
    const fetchOpts = {
        method, credentials: 'include',
        headers: { 'X-CSRF-TOKEN': csrfMeta.content, 'Accept': 'application/json' }
    };
    if (data && (method === 'POST' || method === 'PUT' || method === 'DELETE')) {
        const fd = new URLSearchParams();
        for (const k in data) fd.append(k, data[k]);
        if (method === 'PUT' || method === 'DELETE') { fd.append('_method', method); fetchOpts.method = 'POST'; }
        fetchOpts.body = fd.toString(); fetchOpts.headers['Content-Type'] = 'application/x-www-form-urlencoded';
    }
    try {
        const res = await fetch(`${API_BASE}/${endpoint}`, fetchOpts);
        if (res.status === 401) { localStorage.removeItem('user'); window.location.href = '/login'; throw new Error('Unauthorized'); }
        if (!res.headers.get('content-type')?.includes('application/json')) throw new Error('Non-JSON response');
        const result = await res.json();
        if (!res.ok) throw new Error(result.error || result.message || 'Request failed');
        return result;
    } catch (error) {
        console.error('API Error:', error);
        if (!options.silent) showToast(error.message, 'error');
        throw error;
    }
}

async function checkAuth() {
    try {
        const data = await apiRequest('auth/check', 'GET', null, { silent: true });
        if (!data.authenticated) { localStorage.removeItem('user'); window.location.href = '/login'; return null; }
        localStorage.setItem('user', JSON.stringify(data.user)); return data.user;
    } catch (e) { localStorage.removeItem('user'); window.location.href = '/login'; return null; }
}

async function logout() {
    try {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        await fetch(window.DOMpetConfig.logoutUrl, { method: 'POST', credentials: 'include', headers: { 'X-CSRF-TOKEN': csrfMeta?.content || '' } });
    } catch (e) {}
    localStorage.removeItem('user');
    window.location.href = '/login';
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Core Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
function showSkeleton() {
    const tbody = document.getElementById('savingsTable');
    if (!tbody) return;
    tbody.innerHTML = Array(4).fill('').map(() => `
        <tr class="skeleton-row">
            <td><div class="skeleton-cell skeleton-loader w-60"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-55"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-40"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-70"></div></td>
        </tr>
    `).join('');
}

async function loadData(page = 1) {
    showSkeleton();
    try {
        let endpoint = `transactions?page=${page}&limit=${pagination.perPage}`;
        if (savingsWalletId) endpoint += `&wallet_id=${savingsWalletId}`;
        const data = await apiRequest(endpoint);
        if (data.pagination) {
            pagination.currentPage = data.pagination.current_page || 1;
            pagination.perPage = data.pagination.per_page || 10;
            pagination.total = data.pagination.total || 0;
            pagination.totalPages = data.pagination.total_pages || 0;
        }
        allSavingsTransactions = data.transactions;
        renderTable(allSavingsTransactions);
        renderPagination();
        updateStats(data.summary);
        // Reset search on data reload
        var searchInput = document.getElementById('savingsSearch');
        if (searchInput && searchInput.value) {
            searchInput.value = '';
            filterSavingsTable('');
        }
        await loadMonthly();
    } catch (e) { console.error('Load error:', e); }
}

async function loadMonthly() {
    try {
        const now = new Date();
        const year = now.getFullYear();
        let endpoint = `transactions/monthly?year=${year}`;
        if (savingsWalletId) endpoint += `&wallet_id=${savingsWalletId}`;
        const data = await apiRequest(endpoint, 'GET', null, { silent: true });
        if (data.monthly && data.monthly.length > 0) {
            const thisMonth = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
            const current = data.monthly.find(m => m.month === thisMonth);
            if (current) {
                document.getElementById('monthlySaved').textContent = formatCurrency(parseFloat(current.total_income || 0));
            }
        }
    } catch (e) { /* silent */ }
}

function renderTable(transactions) {
    const tbody = document.getElementById('savingsTable');
    if (!tbody) return;
    if (transactions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4"><div class="empty-state"><i class="fas fa-piggy-bank"></i><p>Belum ada tabungan. Mulai Topup Saldo dari Dompet!</p></div></td></tr>';
        return;
    }
    tbody.innerHTML = transactions.map(t => `
        <tr class="fade-in">
            <td>${formatDate(t.transaction_date)}</td>
            <td><span class="${t.type === 'income' ? 'badge-income' : 'badge-expense'}">${escapeHtml(t.category)}</span></td>
            <td class="${t.type === 'income' ? 'text-success' : 'text-danger'}">${t.type === 'income' ? '+' : '-'}${formatCurrency(t.amount)}</td>
            <td>${escapeHtml(t.note || '-')}</td>
        </tr>
    `).join('');
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Search Filter Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
var filteredSavings = [];
var allSavingsTransactions = [];

function filterSavingsTable(query) {
    const tbody = document.getElementById('savingsTable');
    const noResult = document.getElementById('savingsSearchNoResult');
    const clearBtn = document.getElementById('savingsSearchClearBtn');
    if (!tbody) return;

    if (!query) {
        renderTable(allSavingsTransactions);
        if (noResult) noResult.style.display = 'none';
        if (clearBtn) clearBtn.style.display = 'none';
        return;
    }

    const q = query.toLowerCase();
    filteredSavings = allSavingsTransactions.filter(t => {
        const searchText = (
            (t.transaction_date || '') + ' ' +
            (t.category || '') + ' ' +
            (t.amount ? t.amount.toString() : '') + ' ' +
            (t.note || '') + ' ' +
            formatCurrency(t.amount || 0).replace(/[^a-zA-Z0-9]/g, '')
        ).toLowerCase();
        const cleanQ = q.replace(/[^a-zA-Z0-9]/g, '');
        return searchText.replace(/[^a-zA-Z0-9]/g, '').includes(cleanQ) || searchText.includes(q);
    });

    renderTable(filteredSavings);

    if (noResult) {
        noResult.style.display = filteredSavings.length === 0 ? 'block' : 'none';
    }
    if (clearBtn) {
        clearBtn.style.display = query ? 'block' : 'none';
    }
}

function setupSavingsSearch() {
    const input = document.getElementById('savingsSearch');
    const clearBtn = document.getElementById('savingsSearchClearBtn');
    if (!input) return;

    input.addEventListener('input', function() {
        filterSavingsTable(this.value);
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            filterSavingsTable('');
            this.blur();
        }
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            input.value = '';
            filterSavingsTable('');
            input.focus();
        });
    }
}

function clearSavingsSearch() {
    const input = document.getElementById('savingsSearch');
    if (input) {
        input.value = '';
        filterSavingsTable('');
        input.focus();
    }
}

function animateValue(element, start, end, duration = 600, isCurrency = true) {
    if (!element) return;
    const startTime = performance.now();
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const current = Math.round(start + (end - start) * eased);
        if (isCurrency) {
            element.textContent = formatCurrency(current);
        } else {
            element.textContent = current;
        }
        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            element.classList.remove('count-up');
            void element.offsetWidth;
            element.classList.add('count-up');
        }
    }
    requestAnimationFrame(update);
}

function parseCurrencyText(text) {
    if (!text) return 0;
    return parseInt(text.replace(/[^0-9]/g, '')) || 0;
}

function updateStats(summary) {
    if (!summary) return;
    const totalSaved = summary.total_income || 0;
    const totalTx = summary.total_transactions || 0;

    // Animate values with count-up effect
    const totalSavedEl = document.getElementById('totalSaved');
    const totalTxEl = document.getElementById('totalTxCount');
    const avgMonthlyEl = document.getElementById('avgMonthly');

    const prevSaved = parseCurrencyText(totalSavedEl?.textContent);
    const prevTx = parseCurrencyText(totalTxEl?.textContent);
    const prevAvg = parseCurrencyText(avgMonthlyEl?.textContent);

    animateValue(totalSavedEl, prevSaved, totalSaved, 600, true);
    animateValue(totalTxEl, prevTx, totalTx, 500, false);

    // Average per month Ã¢â‚¬â€ count unique months from cached monthly data
    const uniqueMonths = monthlyDataCache.filter(m => (m.total_income > 0 || m.total_expense > 0)).length;
    const monthsActive = Math.max(1, uniqueMonths || 1);
    const avg = Math.round(totalSaved / monthsActive);
    animateValue(avgMonthlyEl, prevAvg, avg, 600, true);

    updateGoalDisplay(totalSaved);
}

function renderPagination() {
    const container = document.getElementById('savingsPagination');
    if (!container) return;
    const { currentPage, totalPages, total, perPage } = pagination;
    if (total === 0) { container.innerHTML = ''; return; }
    const start = ((currentPage - 1) * perPage) + 1;
    const end = Math.min(currentPage * perPage, total);
    const sizeOpts = [5, 10, 25, 50].map(n => `<option value="${n}" ${n === perPage ? 'selected' : ''}>${n}</option>`).join('');
    let btns = '';
    const maxV = 5;
    let sP = Math.max(1, currentPage - Math.floor(maxV / 2));
    let eP = Math.min(totalPages, sP + maxV - 1);
    if (eP - sP < maxV - 1) sP = Math.max(1, eP - maxV + 1);
    if (sP > 1) { btns += `<button class="page-btn" onclick="goPage(1)">1</button>`; if (sP > 2) btns += '<span class="page-ellipsis">...</span>'; }
    for (let i = sP; i <= eP; i++) btns += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="goPage(${i})">${i}</button>`;
    if (eP < totalPages) { if (eP < totalPages - 1) btns += '<span class="page-ellipsis">...</span>'; btns += `<button class="page-btn" onclick="goPage(${totalPages})">${totalPages}</button>`; }
    container.innerHTML = `
        <div class="pagination-wrapper">
            <div class="pagination-left">
                <label class="page-size-label">Tampilkan</label>
                <select class="page-size-select" onchange="changeSize(this.value)">${sizeOpts}</select>
                <span class="page-size-label">data per halaman</span>
                <span class="pagination-info">&mdash; Menampilkan ${start}-${end} dari ${total} transaksi</span>
            </div>
            <div class="pagination-right">
                <button class="page-btn ${currentPage === 1 ? 'disabled' : ''}" onclick="goPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}><i class="fas fa-chevron-left"></i></button>
                ${btns}
                <button class="page-btn ${currentPage === totalPages ? 'disabled' : ''}" onclick="goPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>`;
}

function goPage(p) { if (p < 1 || p > pagination.totalPages) return; loadData(p); }
function changeSize(s) { pagination.perPage = parseInt(s); loadData(1); }

// Ã¢â€â‚¬Ã¢â€â‚¬ Chart Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
var savingsChart = null;
var monthlyDataCache = [];

async function loadSavingsChart() {
    try {
        const now = new Date();
        let endpoint = 'transactions/monthly';
        if (savingsWalletId) endpoint += '?wallet_id=' + savingsWalletId;
        const data = await apiRequest(endpoint, 'GET', null, { silent: true });
        monthlyDataCache = data.monthly || [];
        renderSavingsChart(monthlyDataCache);
    } catch (e) { /* silent */ }
}

function renderSavingsChart(monthlyData) {
    const canvas = document.getElementById('savingsGrowthChart');
    if (!canvas) return;
    if (savingsChart) savingsChart.destroy();

    // Sort by month ascending
    monthlyData.sort((a, b) => a.month.localeCompare(b.month));

    // Compute cumulative balance
    let cumulative = 0;
    const labels = [];
    const balances = [];
    monthlyData.forEach(m => {
        if (m.total_income > 0 || m.total_expense > 0) {
            cumulative += parseFloat(m.total_income || 0) - parseFloat(m.total_expense || 0);
            const [y, mth] = m.month.split('-');
            const names = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
            labels.push(names[parseInt(mth, 10) - 1] + ' ' + y);
            balances.push(cumulative);
        }
    });

    if (labels.length === 0) {
        canvas.parentElement.innerHTML = '<div class="empty-state"><i class="fas fa-chart-line"></i><p>Belum ada data tabungan untuk ditampilkan</p></div>';
        return;
    }

    const isDark = document.body.classList.contains('dark-mode');
    const textColor = isDark ? 'rgba(255,255,255,0.65)' : 'rgba(71,85,105,0.8)';
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';

    if (typeof Chart === 'undefined') return;
    savingsChart = new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Saldo Tabungan',
                data: balances,
                fill: true,
                backgroundColor: isDark
                    ? 'rgba(52,211,153,0.15)'
                    : 'rgba(5,150,105,0.1)',
                borderColor: '#10b981',
                borderWidth: 3,
                pointBackgroundColor: '#10b981',
                pointBorderColor: isDark ? '#1e1b3a' : '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8,
                tension: 0.35,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? 'rgba(30,27,58,0.95)' : 'rgba(255,255,255,0.95)',
                    titleColor: isDark ? 'rgba(255,255,255,0.9)' : '#1e293b',
                    bodyColor: isDark ? 'rgba(255,255,255,0.75)' : '#475569',
                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
                    borderWidth: 1,
                    borderRadius: 12,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return 'Saldo: ' + formatCurrency(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: textColor, font: { size: 10, weight: '600' } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: {
                        color: textColor,
                        font: { size: 10 },
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                            return 'Rp ' + value;
                        }
                    }
                }
            },
            animation: { duration: 800, easing: 'easeOutQuart' }
        }
    });
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Wallets Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
async function loadWallets() {
    try {
        const data = await apiRequest('wallets');
        wallets = data.wallets;
        const savings = wallets.find(w => w.type === 'savings');
        const main = wallets.find(w => w.type === 'main');
        if (savings) {
            savingsWalletId = savings.id;
            savingsTarget = savings.savings_target || 10000000;
            document.getElementById('savingsBalance').textContent = formatCurrency(savings.balance);
        }
        if (main) mainWalletId = main.id;
        setupTransferModal();
        updateGoalDisplay();
    } catch (e) { console.error('Load wallets error:', e); }
}

function updateGoalDisplay(totalSaved) {
    const saved = totalSaved !== undefined ? totalSaved : 0;
    const pct = Math.min(100, savingsTarget > 0 ? (saved / savingsTarget) * 100 : 0);
    document.getElementById('goalProgressText').textContent = formatCurrency(saved) + ' / ' + formatCurrency(savingsTarget);
    document.getElementById('goalProgressBar').style.width = pct + '%';
    document.getElementById('goalPctText').textContent = pct.toFixed(1) + '% tercapai';
    document.getElementById('goalTargetText').innerHTML = '<i class="fas fa-pencil-alt" style="font-size:0.55rem;margin-right:3px;opacity:0.6;"></i> Target: ' + formatCurrency(savingsTarget);
}

function openTargetModal() {
    const el = document.getElementById('targetModal');
    const card = document.getElementById('floatingTargetCard');
    if (card) card.classList.remove('closing');
    el.classList.add('show');
    document.getElementById('targetAmount').value = savingsTarget.toLocaleString('id-ID');
    // Re-trigger stagger animations
    setTimeout(() => {
        const fields = card.querySelectorAll('.floating-field, .floating-actions');
        fields.forEach(f => {
            f.style.animation = 'none';
            void f.offsetWidth;
            f.style.animation = '';
        });
    }, 50);
}

function closeTargetModal() {
    const el = document.getElementById('targetModal');
    const card = document.getElementById('floatingTargetCard');
    if (card) {
        card.classList.add('closing');
        setTimeout(() => {
            el.classList.remove('show');
            card.classList.remove('closing');
        }, 280);
    } else {
        el.classList.remove('show');
    }
}

async function saveTarget() {
    const amount = parseRupiahToNumber(document.getElementById('targetAmount').value);
    const btn = document.getElementById('targetBtn');
    if (amount <= 0) { showToast('Target harus lebih dari 0', 'error'); return; }
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...'; btn.disabled = true;
    try {
        const data = await apiRequest('wallets/savings-target', 'PUT', { savings_target: amount });
        savingsTarget = data.savings_target || amount;
        showToast('Target tabungan berhasil diperbarui!', 'success');
        closeTargetModal();
        updateGoalDisplay();
    } catch (e) { console.error(e); }
    finally { btn.innerHTML = orig; btn.disabled = false; }
}

function setupTransferModal() {
    const fromInput = document.getElementById('transferFromWallet');
    const toInput = document.getElementById('transferToWallet');
    if (fromInput) fromInput.value = mainWalletId || '';
    if (toInput) toInput.value = savingsWalletId || '';
    // Init rupiah formatting
    document.querySelectorAll('.rupiah-input').forEach(input => {
        input.addEventListener('input', function() { autoFormatRupiah(this); });
    });
}

async function doTransfer() {
    const amount = parseRupiahToNumber(document.getElementById('transferAmount').value);
    const note = document.getElementById('transferNote').value.trim();
    const btn = document.getElementById('transferBtn');
    if (amount <= 0) { showToast('Nominal harus lebih dari 0', 'error'); return; }
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mentransfer...'; btn.disabled = true;
    try {
        await apiRequest('wallets/transfer', 'POST', {
            from_wallet_id: mainWalletId, to_wallet_id: savingsWalletId,
            amount: amount, note: note || 'Menabung',
        });
        showToast('Berhasil Topup Saldo ke Tabungan!', 'success');
        closeTransferModal();
        document.getElementById('transferForm')?.reset();
        document.getElementById('transferNote').value = 'Menabung';
        // Reload
        await loadWallets();
        await loadData(1);
        await loadSavingsChart();
    } catch (e) { console.error(e); }
    finally { btn.innerHTML = orig; btn.disabled = false; }
}

function openTransferModal() {
    const el = document.getElementById('transferModal');
    const card = document.getElementById('floatingTransferCard');
    if (card) card.classList.remove('closing');
    el.classList.add('show');
    // Re-trigger stagger animations
    setTimeout(() => {
        const fields = card.querySelectorAll('.floating-field, .floating-actions');
        fields.forEach(f => {
            f.style.animation = 'none';
            void f.offsetWidth;
            f.style.animation = '';
        });
    }, 50);
}

function closeTransferModal() {
    const el = document.getElementById('transferModal');
    const card = document.getElementById('floatingTransferCard');
    if (card) {
        card.classList.add('closing');
        setTimeout(() => {
            el.classList.remove('show');
            card.classList.remove('closing');
        }, 280);
    } else {
        el.classList.remove('show');
    }
}

function refreshData() {
    loadWallets();
    loadData(1);
    loadSavingsChart();
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Sidebar Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
function toggleSidebar() {
    const s = document.getElementById('sidebar');
    const o = document.getElementById('sidebarOverlay');
    if (!s) return;
    if (window.innerWidth <= 768) { s.classList.toggle('open'); if (o) o.classList.toggle('show', s.classList.contains('open')); }
    else { s.classList.toggle('collapsed'); }
}
function closeSidebar() {
    document.getElementById('sidebar')?.classList.remove('open');
    document.getElementById('sidebarOverlay')?.classList.remove('show');
}
document.addEventListener('click', function(e) {
    if (window.innerWidth > 768) return;
    const s = document.getElementById('sidebar'); const t = document.querySelector('.menu-toggle');
    if (s?.classList.contains('open') && !s.contains(e.target) && !t?.contains(e.target)) closeSidebar();
});

// Ã¢â€â‚¬Ã¢â€â‚¬ Dark Mode Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
function loadThemePreference() {
    const t = localStorage.getItem('darkMode');
    const d = t !== 'disabled';
    document.body.classList.toggle('dark-mode', d);
    const b = document.getElementById('darkModeToggle');
    if (b) b.innerHTML = d ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
}
function toggleDarkMode() {
    const d = !document.body.classList.contains('dark-mode');
    document.body.classList.toggle('dark-mode', d);
    localStorage.setItem('darkMode', d ? 'enabled' : 'disabled');
    const b = document.getElementById('darkModeToggle');
    if (b) b.innerHTML = d ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Profile Modal Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
function openProfileModal() {
    showProfilePage();
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Init Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
(function() {
    async function initTabungan() {
        loadThemePreference();
        document.getElementById('darkModeToggle')?.addEventListener('click', toggleDarkMode);

        currentUser = await checkAuth();
        if (!currentUser) return;

        document.getElementById('userName').textContent = currentUser.full_name || currentUser.username;
        renderUserAvatar(currentUser.avatar || 0);
        await loadWallets();
        await loadSavingsChart();
        await loadData(1);
        setupSavingsSearch();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTabungan);
    } else {
        // DOMContentLoaded already fired Ã¢â‚¬â€ direct init (SPA navigation)
        initTabungan();
    }
})();
</script>

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
