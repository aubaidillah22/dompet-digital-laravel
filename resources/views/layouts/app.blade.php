<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dompet Digital')</title>
    <link rel="icon" type="image/svg+xml" href="{{ url('/favicon.svg') }}">
    <link rel="alternate icon" href="{{ url('/favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ url('/css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
    <script src="{{ url('/js/app-utils.js') }}"></script>
    <script src="{{ url('/js/spa.js') }}"></script>
    <script>
/* ── Avatar Presets (shared) ──────────── */
window.AVATARS = [
    { icon: 'fa-user', bg: '#3b82f6', label: 'Default' },
    { icon: 'fa-user-tie', bg: '#d4940a', label: 'Profesional' },
    { icon: 'fa-user-astronaut', bg: '#8b5cf6', label: 'Petualang' },
    { icon: 'fa-user-ninja', bg: '#1e293b', label: 'Ninja' },
    { icon: 'fa-user-graduate', bg: '#10b981', label: 'Pelajar' },
    { icon: 'fa-user-md', bg: '#ef4444', label: 'Dokter' },
    { icon: 'fa-user-gear', bg: '#f59e0b', label: 'Teknisi' },
    { icon: 'fa-user-secret', bg: '#6b7280', label: 'Agen' },
    { icon: 'fa-user', bg: '#ec4899', label: 'Cantik' },
    { icon: 'fa-user', bg: '#06b6d4', label: 'Santai' },
    { icon: 'fa-user', bg: '#e11d48', label: 'Ganteng' },
    /* ── Hewan ──── */
    { icon: 'fa-cat', bg: '#f97316', label: 'Kucing' },
    { icon: 'fa-dog', bg: '#d97706', label: 'Anjing' },
    { icon: 'fa-fish', bg: '#0ea5e9', label: 'Ikan' },
    { icon: 'fa-horse', bg: '#78350f', label: 'Kuda' },
    { icon: 'fa-frog', bg: '#22c55e', label: 'Katak' },
    { icon: 'fa-crow', bg: '#6b21a8', label: 'Gagak' },
    { icon: 'fa-dragon', bg: '#65a30d', label: 'Buaya' },
    { icon: 'fa-worm', bg: '#059669', label: 'Ular' },
];
window.renderUserAvatar = function(avatarIndex) {
    var avatar = window.AVATARS[avatarIndex] || window.AVATARS[0];
    // Update top bar avatar
    var iconEl = document.getElementById('userAvatarIcon');
    if (iconEl) {
        iconEl.className = 'fas ' + avatar.icon;
        iconEl.style.color = avatar.bg;
    }
    // Update sidebar Profil Saya icon
    var sidebarIcon = document.getElementById('sidebarAvatarIcon');
    if (sidebarIcon) {
        sidebarIcon.className = 'fas ' + avatar.icon;
        sidebarIcon.style.color = avatar.bg;
    }
};
</script>
    @stack('styles')
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>
