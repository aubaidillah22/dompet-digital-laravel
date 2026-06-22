@props(['active' => 'dashboard'])

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon"><i class="fas fa-wallet"></i></div>
            <div class="sidebar-brand-text">
                <h2>Dompet Digital</h2>
                <p>Kelola keuanganmu</p>
            </div>
        </div>
    </div>
    <nav>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="/dashboard" class="nav-link {{ $active === 'dashboard' ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item nav-divider">
                <span>Menu</span>
            </li>
            <li class="nav-item">
                <a href="/dompet" class="nav-link {{ $active === 'dompet' ? 'active' : '' }}">
                    <i class="fas fa-wallet nav-icon-wallet"></i>
                    <span class="nav-text">Dompet Utama</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/tabungan" class="nav-link {{ $active === 'tabungan' ? 'active' : '' }}">
                    <i class="fas fa-piggy-bank nav-icon-savings"></i>
                    <span class="nav-text">Tabungan</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/profile" class="nav-link {{ $active === 'profile' ? 'active' : '' }}">
                    <i class="fas fa-user-cog" id="sidebarAvatarIcon"></i> <span class="nav-text">Profil Saya</span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <button class="logout-btn" onclick="logout()">
            <i class="fas fa-sign-out-alt"></i>
            <span>Keluar</span>
        </button>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
