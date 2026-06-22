@props(['profileOnclick' => "window.location.href='/profile'"])

<div class="top-bar">
    <div class="top-bar-content">
        <button class="menu-toggle" onclick="toggleSidebar()" title="Buka/Tutup Sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <div class="top-bar-right">
            <div class="user-info" onclick="{{ $profileOnclick }}" title="Pengaturan Profil">
                <span class="user-avatar-wrap"><i class="fas fa-user" id="userAvatarIcon"></i></span>
                <span class="user-name" id="userName">{{ session('full_name') ?? session('username') ?? 'User' }}</span>
            </div>
            <button class="dark-mode-toggle" id="darkModeToggle" title="Mode Gelap/Terang">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </div>
</div>
