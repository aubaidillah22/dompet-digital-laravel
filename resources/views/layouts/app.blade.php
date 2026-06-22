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
    <style>
        body {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        .app-footer {
            text-align: center;
            padding: 20px 20px 18px;
            font-size: 0.72rem;
            letter-spacing: 0.3px;
            color: rgba(100,100,110,0.5);
            border-top: 1px solid rgba(100,100,110,0.1);
            margin-top: 32px;
            transition: color 0.3s, border-color 0.3s;
            flex-shrink: 0;
        }
        body.dark-mode .app-footer {
            color: rgba(255,255,255,0.3);
            border-top-color: rgba(255,255,255,0.05);
        }
        .app-footer .footer-heart {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .app-footer .footer-heart i {
            font-size: 0.65rem;
            animation: footerPulse 2s ease-in-out infinite;
        }
        @keyframes footerPulse {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.25); opacity: 1; }
        }
        
        /* ── Shared Animations (all user pages) ── */
        @keyframes gentleFlow {
            0% { background-position: 0% 0%; }
            50% { background-position: 100% 100%; }
            100% { background-position: 0% 0%; }
        }
        @keyframes sidebarBorderGlow {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.7; }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes countPop {
            0% { transform: scale(1); }
            50% { transform: scale(1.06); opacity: 0.85; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes splashSpin {
            to { transform: rotate(360deg); }
        }
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(40px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes toastOut {
            from { opacity: 1; }
            to { opacity: 0; transform: translateY(-10px); }
        }
        .fade-in {
            animation: fadeInUp 0.5s ease both;
        }
        .fade-in:nth-child(1) { animation-delay: 0.05s; }
        .fade-in:nth-child(2) { animation-delay: 0.1s; }
        .fade-in:nth-child(3) { animation-delay: 0.15s; }
        .fade-in:nth-child(4) { animation-delay: 0.2s; }
        .fade-in:nth-child(5) { animation-delay: 0.25s; }
        .fade-in:nth-child(6) { animation-delay: 0.3s; }
        .fade-in:nth-child(7) { animation-delay: 0.35s; }
        .fade-in:nth-child(8) { animation-delay: 0.4s; }
        .stat-value.count-up { animation: countPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .skeleton-loader {
            animation: shimmer 1.5s ease-in-out infinite;
            background: linear-gradient(90deg, rgba(200,200,210,0.15) 25%, rgba(200,200,210,0.3) 50%, rgba(200,200,210,0.15) 75%);
            background-size: 200% 100%; border-radius: 6px;
        }
        body.dark-mode .skeleton-loader {
            background: linear-gradient(90deg, rgba(255,255,255,0.04) 25%, rgba(255,255,255,0.12) 50%, rgba(255,255,255,0.04) 75%);
            background-size: 200% 100%;
        }
        .toast {
            position: fixed; top: 20px; right: 20px; padding: 12px 20px; border-radius: 12px;
            color: #fff; font-size: 0.85rem; font-weight: 500; z-index: 9999;
            display: flex; align-items: center; gap: 8px;
            backdrop-filter: blur(12px); animation: toastIn 0.3s ease, toastOut 0.3s ease 2.7s forwards;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }
        .toast-success { background: rgba(16,185,129,0.9); }
        .toast-error { background: rgba(239,68,68,0.9); }
        .toast-info { background: rgba(59,130,246,0.9); }

        /* ── Custom Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(212,148,10,0.3), rgba(224,112,32,0.3));
            border-radius: 40px;
            transition: background 0.3s;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, rgba(212,148,10,0.5), rgba(224,112,32,0.5));
        }
        body.dark-mode ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(240,180,41,0.25), rgba(255,140,66,0.25));
        }
        body.dark-mode ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, rgba(240,180,41,0.45), rgba(255,140,66,0.45));
        }
        * { scrollbar-width: thin; scrollbar-color: rgba(212,148,10,0.3) transparent; }
        body.dark-mode * { scrollbar-color: rgba(240,180,41,0.25) transparent; }
    </style>
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>
