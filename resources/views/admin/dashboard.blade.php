@extends('layouts.app')

@section('title', 'Admin Panel - Dompet Digital')

@push('styles')
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
:root {
    --white: #ffffff; --glass-bg: rgba(255,255,255,0.7);
    --glass-border: rgba(0,0,0,0.1); --accent-gold: #d4940a;
    --accent-orange: #e07020; --danger: #ef4444; --success: #10b981;
    --text-primary: #1e293b; --text-secondary: #475569;
    --text-muted: rgba(71,85,105,0.7); --card-bg: rgba(255,255,255,0.85);
    --body-bg: linear-gradient(135deg, #eef2ff, #e8ecf8, #f5f0ff);
    --sidebar-bg: rgba(255,255,255,0.92); --input-bg: rgba(255,255,255,0.9);
    --input-border: rgba(0,0,0,0.15); --modal-bg: rgba(255,255,255,0.97);
    --card-shadow: 0 2px 12px rgba(0,0,0,0.04);
    --card-shadow-hover: 0 8px 32px rgba(0,0,0,0.08);
}
body.dark-mode {
    --white: #1e1b3a; --glass-bg: rgba(255,255,255,0.06);
    --glass-border: rgba(255,255,255,0.1); --accent-gold: #f0b429;
    --accent-orange: #ff8c42; --text-primary: #ffffff;
    --text-secondary: rgba(255,255,255,0.85); --text-muted: rgba(255,255,255,0.5);
    --card-bg: rgba(255,255,255,0.06); --body-bg: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
    --sidebar-bg: rgba(15,12,41,0.7); --input-bg: rgba(255,255,255,0.08);
    --input-border: rgba(255,255,255,0.15); --modal-bg: rgba(30,26,44,0.95);
    --card-shadow: 0 2px 12px rgba(0,0,0,0.2);
    --card-shadow-hover: 0 8px 32px rgba(0,0,0,0.3);
}
body {
    font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif; height: 100vh; overflow: hidden;
    background: var(--body-bg); background-size: 200% 200%;
    animation: gentleFlow 12s ease infinite; color: var(--text-primary);
    transition: background 0.3s ease, color 0.3s ease;
}
.app-container { display: flex; flex: 1; min-height: 0; }

/* ── Admin Sidebar ─────────────────────── */
.sidebar {
    width: 260px; background: var(--sidebar-bg); backdrop-filter: blur(20px);
    border-right: 1px solid var(--glass-border);
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), left 0.3s ease;
    z-index: 100; display: flex; flex-direction: column; overflow: hidden; flex-shrink: 0;
    position: sticky; top: 0; height: 100vh; align-self: flex-start;
}
.sidebar::before {
    content: ''; position: absolute; inset: 0;
    background: radial-gradient(ellipse 140% 80% at 50% -20%, rgba(240,180,41,0.08) 0%, transparent 70%);
    pointer-events: none;
}
.sidebar::after {
    content: ''; position: absolute; top: 0; right: -1px; width: 2px; height: 100%;
    background: linear-gradient(180deg, transparent 0%, var(--accent-gold) 20%, var(--accent-orange) 50%, var(--accent-gold) 80%, transparent 100%);
    opacity: 0.5; pointer-events: none;
    animation: sidebarBorderGlow 4s ease-in-out infinite;
}
.sidebar.collapsed { width: 60px; }
.sidebar.collapsed .sidebar-header h2 span,
.sidebar.collapsed .sidebar-header p,
.sidebar.collapsed .nav-text { display: none; }
.sidebar.collapsed .sidebar-header { padding: 16px 0; text-align: center; }
.sidebar.collapsed .sidebar-brand-icon { margin: 0 auto; }
.sidebar.collapsed .nav-link { justify-content: center; padding: 12px; }
.sidebar.collapsed .nav-link i { font-size: 1.15rem; margin: 0; }
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 99; backdrop-filter: blur(2px); }
.sidebar-overlay.show { display: block; }

.sidebar-header {
    padding: 20px 18px 16px;
    border-bottom: 1px solid var(--glass-border);
    position: relative; overflow: hidden; flex-shrink: 0;
}
.sidebar-header::after {
    content: ''; position: absolute; bottom: -1px; left: 10%; right: 10%; height: 1px;
    background: linear-gradient(90deg, transparent, var(--accent-gold), transparent);
    opacity: 0.3;
}
.sidebar-brand {
    display: flex; align-items: center; gap: 12px;
    position: relative; z-index: 1;
}
.sidebar-brand-icon {
    width: 38px; height: 38px; border-radius: 12px;
    background: linear-gradient(135deg, var(--accent-gold), var(--accent-orange));
    display: flex; align-items: center; justify-content: center;
    font-size: 1.05rem; color: #1e1a0c;
    flex-shrink: 0;
    box-shadow: 0 3px 10px rgba(240,180,41,0.25);
}
.sidebar-brand-icon i { margin: 0 !important; }
.sidebar-brand-text h2 {
    font-size: 1rem; font-weight: 800; margin: 0; line-height: 1.3;
    background: linear-gradient(90deg, var(--accent-gold), var(--accent-orange));
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.sidebar-brand-text p {
    font-size: 0.6rem; color: var(--text-muted); margin: 0; letter-spacing: 0.3px;
}

.nav-menu {
    list-style: none; padding: 12px 10px; flex: 1;
    display: flex; flex-direction: column; gap: 2px;
    position: relative; z-index: 1; overflow-y: auto;
}
.nav-item { width: 100%; }
.nav-link {
    display: flex; align-items: center; gap: 10px; padding: 9px 14px;
    border-radius: 10px; text-decoration: none; color: var(--text-secondary);
    font-weight: 500; font-size: 0.83rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}
.nav-link i { width: 20px; font-size: 0.95rem; transition: all 0.25s ease; text-align: center; flex-shrink: 0; }
.nav-link:hover {
    background: rgba(240,180,41,0.08);
    color: var(--accent-gold);
}
.nav-link:hover i { transform: scale(1.15); }
.nav-link.active {
    background: linear-gradient(90deg, rgba(240,180,41,0.15), rgba(255,140,66,0.05));
    color: var(--accent-gold);
    font-weight: 600;
}
.nav-link.active::before {
    content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%);
    width: 3px; height: 45%; border-radius: 0 3px 3px 0;
    background: linear-gradient(180deg, var(--accent-gold), var(--accent-orange));
    box-shadow: 0 0 8px rgba(240,180,41,0.3);
}
.nav-link.active i { transform: scale(1.1); }
.nav-link.nav-logout { margin-top: auto; }

.sidebar-footer {
    padding: 10px 14px 14px;
    border-top: 1px solid var(--glass-border);
    position: relative; z-index: 1;
    display: flex; flex-shrink: 0;
}
.sidebar-footer .logout-btn {
    display: flex; align-items: center; gap: 10px; width: 100%;
    padding: 8px 10px; border-radius: 10px; border: none;
    background: none; color: var(--text-muted); font-size: 0.78rem;
    cursor: pointer; transition: all 0.2s;
}
.sidebar-footer .logout-btn:hover {
    background: rgba(239,68,68,0.08);
    color: #ef4444;
}
.sidebar-footer .logout-btn i { width: 18px; font-size: 0.85rem; text-align: center; }

/* ── Main Content ──────────────────────── */
.main-content { flex: 1; padding: 20px 28px; overflow-y: auto; }

/* ── Stats Grid ────────────────────────── */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
.stat-card {
    position: relative; overflow: hidden;
    display: flex; align-items: center; gap: 14px;
    padding: 18px 20px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: var(--card-bg); backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border); border-radius: 24px;
    box-shadow: var(--card-shadow);
}
.stat-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    opacity: 0.6; transition: opacity 0.4s ease, height 0.3s ease; z-index: 2;
}
.stat-card:hover::before { opacity: 1; height: 4px; }
.stat-card:hover { transform: translateY(-4px); box-shadow: var(--card-shadow-hover); }
.stat-card-users::before { background: linear-gradient(90deg, #3b82f6, #60a5fa 60%, transparent); }
.stat-card-tx::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa 60%, transparent); }
.stat-card-income::before { background: linear-gradient(90deg, #10b981, #34d399 60%, transparent); }
.stat-card-expense::before { background: linear-gradient(90deg, #ef4444, #f87171 60%, transparent); }
.stat-card-main::before { background: linear-gradient(90deg, var(--accent-gold), var(--accent-orange) 60%, transparent); }
.stat-card-savings::before { background: linear-gradient(90deg, #10b981, #06b6d4 60%, transparent); }

.stat-card-users   { background: linear-gradient(135deg, var(--card-bg) 60%, rgba(59,130,246,0.04)) !important; }
.stat-card-tx      { background: linear-gradient(135deg, var(--card-bg) 60%, rgba(139,92,246,0.04)) !important; }
.stat-card-income  { background: linear-gradient(135deg, var(--card-bg) 60%, rgba(16,185,129,0.04)) !important; }
.stat-card-expense { background: linear-gradient(135deg, var(--card-bg) 60%, rgba(239,68,68,0.04)) !important; }
.stat-card-main    { background: linear-gradient(135deg, var(--card-bg) 60%, rgba(240,180,41,0.04)) !important; }
.stat-card-savings { background: linear-gradient(135deg, var(--card-bg) 60%, rgba(16,185,129,0.04)) !important; }

body.dark-mode .stat-card-users   { background: linear-gradient(135deg, rgba(255,255,255,0.06) 50%, rgba(59,130,246,0.06)) !important; }
body.dark-mode .stat-card-tx      { background: linear-gradient(135deg, rgba(255,255,255,0.06) 50%, rgba(139,92,246,0.06)) !important; }
body.dark-mode .stat-card-income  { background: linear-gradient(135deg, rgba(255,255,255,0.06) 50%, rgba(16,185,129,0.06)) !important; }
body.dark-mode .stat-card-expense { background: linear-gradient(135deg, rgba(255,255,255,0.06) 50%, rgba(239,68,68,0.06)) !important; }
body.dark-mode .stat-card-main    { background: linear-gradient(135deg, rgba(255,255,255,0.06) 50%, rgba(240,180,41,0.06)) !important; }
body.dark-mode .stat-card-savings { background: linear-gradient(135deg, rgba(255,255,255,0.06) 50%, rgba(16,185,129,0.06)) !important; }
body.dark-mode .stat-card:hover { box-shadow: 0 10px 32px rgba(0,0,0,0.5); background: rgba(255,255,255,0.07) !important; }

.stat-icon-wrap {
    flex-shrink: 0; width: 44px; height: 44px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); position: relative; z-index: 1;
}
.stat-card-users .stat-icon-wrap   { background: linear-gradient(135deg, rgba(59,130,246,0.2), rgba(59,130,246,0.06)); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2); }
.stat-card-tx .stat-icon-wrap      { background: linear-gradient(135deg, rgba(139,92,246,0.2), rgba(139,92,246,0.06)); color: #8b5cf6; border: 1px solid rgba(139,92,246,0.2); }
.stat-card-income .stat-icon-wrap  { background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(16,185,129,0.06)); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
.stat-card-expense .stat-icon-wrap { background: linear-gradient(135deg, rgba(239,68,68,0.2), rgba(239,68,68,0.06)); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }
.stat-card-main .stat-icon-wrap    { background: linear-gradient(135deg, rgba(240,180,41,0.2), rgba(240,180,41,0.06)); color: var(--accent-gold); border: 1px solid rgba(240,180,41,0.2); }
.stat-card-savings .stat-icon-wrap { background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(16,185,129,0.06)); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
.stat-card:hover .stat-icon-wrap { transform: scale(1.12) rotate(-5deg); }

body.dark-mode .stat-card-users .stat-icon-wrap   { background: linear-gradient(135deg, rgba(59,130,246,0.18), rgba(59,130,246,0.04)); border-color: rgba(59,130,246,0.15); }
body.dark-mode .stat-card-tx .stat-icon-wrap      { background: linear-gradient(135deg, rgba(139,92,246,0.18), rgba(139,92,246,0.04)); border-color: rgba(139,92,246,0.15); }
body.dark-mode .stat-card-income .stat-icon-wrap  { background: linear-gradient(135deg, rgba(16,185,129,0.18), rgba(16,185,129,0.04)); border-color: rgba(16,185,129,0.15); }
body.dark-mode .stat-card-expense .stat-icon-wrap { background: linear-gradient(135deg, rgba(239,68,68,0.18), rgba(239,68,68,0.04)); border-color: rgba(239,68,68,0.15); }
body.dark-mode .stat-card-main .stat-icon-wrap    { background: linear-gradient(135deg, rgba(240,180,41,0.18), rgba(240,180,41,0.04)); border-color: rgba(240,180,41,0.15); }
body.dark-mode .stat-card-savings .stat-icon-wrap { background: linear-gradient(135deg, rgba(16,185,129,0.18), rgba(16,185,129,0.04)); border-color: rgba(16,185,129,0.15); }

.stat-body { flex: 1; min-width: 0; position: relative; z-index: 1; }
.stat-label { font-size: 0.7rem; font-weight: 600; color: var(--text-muted); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 1px; display: flex; align-items: center; gap: 6px; }
.stat-value { font-size: 1.3rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1.3; transition: all 0.3s; color: var(--text-primary); }
.stat-value-sm { font-size: 1rem; }
.stat-text-income { color: #10b981; }
.stat-text-expense { color: #ef4444; }
.stat-text-gold { background: linear-gradient(90deg, var(--accent-gold), var(--accent-orange)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.stat-text-green { color: #10b981; }
body.dark-mode .stat-value { color: rgba(255,255,255,0.93); }

.stat-glow {
    position: absolute; width: 140px; height: 140px; border-radius: 50%;
    right: -40px; bottom: -40px; opacity: 0; transition: opacity 0.6s ease, transform 0.6s ease;
    pointer-events: none; filter: blur(50px);
}
.stat-card:hover .stat-glow { opacity: 0.12; transform: scale(1.3); }
.stat-glow-users { background: #3b82f6; }
.stat-glow-tx { background: #8b5cf6; }
.stat-glow-income { background: #10b981; }
.stat-glow-expense { background: #ef4444; }
.stat-glow-main { background: var(--accent-gold); }
.stat-glow-savings { background: #10b981; }
body.dark-mode .stat-card:hover .stat-glow { opacity: 0.3; filter: blur(60px); }

/* ── Section Header ────────────────────── */
.section-header {
    position: relative;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--glass-border);
}
.section-header::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 60px;
    height: 2px;
    background: linear-gradient(90deg, var(--accent-gold), var(--accent-orange));
    border-radius: 2px;
}
.section-header h2 {
    font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em;
    color: var(--text-primary);
}
.section-header h2 i { color: var(--accent-gold); margin-right: 8px; }
.section-header p { color: var(--text-muted); font-size: 0.82rem; margin-top: 4px; }

/* ── Chart Cards ────────────────────────── */
.chart-card {
    background: var(--card-bg); backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 24px; padding: 18px 20px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: var(--card-shadow);
    position: relative; overflow: hidden;
}
.chart-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    opacity: 0.6; transition: opacity 0.4s ease; z-index: 2;
}
.chart-card:hover::before { opacity: 1; }
.chart-card:hover { transform: translateY(-4px); box-shadow: var(--card-shadow-hover); }
body.dark-mode .chart-card:hover { background: rgba(255,255,255,0.07); }

.chart-card-income::before { background: linear-gradient(90deg, #10b981, #34d399 60%, transparent); }
.chart-card-expense::before { background: linear-gradient(90deg, #ef4444, #f87171 60%, transparent); }
.chart-card-wallet::before { background: linear-gradient(90deg, var(--accent-gold), var(--accent-orange) 60%, transparent); }

.chart-card-header {
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 14px; padding-bottom: 10px;
    border-bottom: 1px solid var(--glass-border);
}
.chart-card-icon {
    width: 40px; height: 40px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}
.chart-card-income .chart-card-icon { background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(16,185,129,0.06)); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
.chart-card-expense .chart-card-icon { background: linear-gradient(135deg, rgba(239,68,68,0.2), rgba(239,68,68,0.06)); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }
.chart-card-wallet .chart-card-icon { background: linear-gradient(135deg, rgba(240,180,41,0.2), rgba(240,180,41,0.06)); color: var(--accent-gold); border: 1px solid rgba(240,180,41,0.2); }
body.dark-mode .chart-card-income .chart-card-icon { background: linear-gradient(135deg, rgba(16,185,129,0.18), rgba(16,185,129,0.04)); border-color: rgba(16,185,129,0.15); }
body.dark-mode .chart-card-expense .chart-card-icon { background: linear-gradient(135deg, rgba(239,68,68,0.18), rgba(239,68,68,0.04)); border-color: rgba(239,68,68,0.15); }
body.dark-mode .chart-card-wallet .chart-card-icon { background: linear-gradient(135deg, rgba(240,180,41,0.18), rgba(240,180,41,0.04)); border-color: rgba(240,180,41,0.15); }

.chart-card-title { font-size: 0.85rem; font-weight: 700; color: var(--text-primary); line-height: 1.2; }
.chart-card-subtitle { font-size: 0.7rem; color: var(--text-muted); font-weight: 500; }
.chart-canvas-wrap { min-height: 200px; position: relative; display: flex; justify-content: center; align-items: center; }
.chart-canvas-wrap canvas { max-width: 100%; max-height: 260px; }

.chart-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
.charts-section { margin-bottom: 28px; }

/* ── Table ─────────────────────────────── */
.table-container { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px 12px; text-align: left; border-bottom: 1px solid var(--glass-border); }
th { color: var(--text-muted); font-weight: 600; font-size: 0.72rem; letter-spacing: 0.5px; text-transform: uppercase; }
td { color: var(--text-secondary); font-size: 0.78rem; transition: background 0.2s; }
tr:hover td { background: rgba(240,180,41,0.03); }

.badge-role { padding: 3px 10px; border-radius: 40px; font-size: 0.68rem; font-weight: 600; display: inline-block; }
.badge-admin { background: rgba(240,180,41,0.2); color: #f0b429; }
.badge-user { background: rgba(59,130,246,0.15); color: #60a5fa; }

/* ── Premium User Search ───────────────── */
.filter-bar-card {
    margin-bottom: 20px;
    background: var(--card-bg); backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border); border-radius: 20px;
    padding: 16px 20px; transition: all 0.3s; position: relative; overflow: hidden;
}
.filter-bar-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, transparent, var(--accent-gold), var(--accent-orange), transparent);
    opacity: 0.6;
}
.filter-bar-card:hover { border-color: rgba(240,180,41,0.25); }
.filter-bar-inner {
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 12px;
}
.filter-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

.search-bar {
    display: flex; align-items: center; gap: 8px;
    padding: 6px 14px; min-width: 200px; flex: 1; max-width: 280px;
    background: var(--input-bg); border: 1px solid var(--input-border);
    border-radius: 40px; transition: all 0.2s;
}
.search-bar:focus-within { border-color: var(--accent-gold); box-shadow: 0 0 0 3px rgba(240,180,41,0.15); }
.search-bar-icon { color: var(--text-muted); font-size: 0.8rem; opacity: 0.6; flex-shrink: 0; }
.search-bar-input { flex: 1; border: none; background: transparent; color: var(--text-primary); font-size: 0.82rem; outline: none; padding: 6px 0; min-width: 0; }
.search-bar-input::placeholder { color: var(--text-muted); opacity: 0.6; }

.btn {
    padding: 8px 16px; border-radius: 40px; font-weight: 600; font-size: 0.78rem;
    cursor: pointer; transition: all 0.2s; border: none;
    display: inline-flex; align-items: center; gap: 7px;
    text-decoration: none;
}
.btn-primary { background: linear-gradient(90deg, var(--accent-gold), var(--accent-orange)); color: #1e1a0c; }
.btn-primary:hover { transform: scale(1.02); opacity: 0.95; }
.btn-success { background: linear-gradient(90deg, #059669, #10b981); color: white; }
.btn-success:hover { transform: scale(1.02); opacity: 0.95; }
.btn-danger { background: rgba(239,68,68,0.2); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); }
.btn-danger:hover { background: rgba(239,68,68,0.4); }
.btn-outline { background: transparent; border: 1px solid var(--glass-border); color: var(--text-secondary); }
.btn-outline:hover { background: rgba(240,180,41,0.15); border-color: var(--accent-gold); color: var(--accent-gold); }
.btn-secondary { background: var(--glass-bg); color: var(--text-primary); border: 1px solid var(--glass-border); }
.btn-secondary:hover { background: rgba(240,180,41,0.2); border-color: var(--accent-gold); }
.btn-sm { padding: 5px 10px; font-size: 0.68rem; }

/* ── Form Controls ─────────────────────── */
.form-control {
    padding: 8px 14px; background: var(--input-bg); border: 1px solid var(--input-border);
    border-radius: 40px; color: var(--text-primary); font-size: 0.82rem; outline: none;
    transition: all 0.2s; width: 100%;
}
.form-control:focus { border-color: var(--accent-gold); box-shadow: 0 0 0 3px rgba(240,180,41,0.15); }
select option { background: var(--white); color: var(--text-primary); }
.form-group { margin-bottom: 14px; }
.form-group label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 5px; }
.form-group label i { margin-right: 4px; color: var(--accent-gold); font-size: 0.7rem; }

/* ── Premium Floating Modal ────────────── */
.modal-floating {
    display: none;
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    z-index: 1000;
    align-items: center; justify-content: center;
}
.modal-floating.show { display: flex; }
.modal-floating-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    animation: overlayFadeIn 0.35s ease both;
    z-index: -1;
}
.modal-floating-card {
    position: relative;
    background: var(--modal-bg);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid var(--glass-border);
    border-radius: 28px;
    padding: 0;
    max-width: 500px;
    width: 92%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 32px 80px rgba(0,0,0,0.35);
    animation: modalFloatIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
    transform-origin: center bottom;
}
.modal-floating-card.closing {
    animation: modalFloatOut 0.3s cubic-bezier(0.55, 0, 1, 0.45) both;
}
.modal-floating-card.wide { max-width: 820px; }
.modal-floating-card::-webkit-scrollbar { width: 4px; }
.modal-floating-card::-webkit-scrollbar-track { background: transparent; }
.modal-floating-card::-webkit-scrollbar-thumb { background: var(--glass-border); border-radius: 4px; }

/* Particles */
.modal-particles {
    position: absolute; inset: 0; overflow: hidden; pointer-events: none;
    border-radius: 28px; z-index: 0;
}
.particle {
    position: absolute; border-radius: 50%;
    animation: particleFloat 6s ease-in-out infinite;
    opacity: 0.15;
}
.particle.p1 { width: 80px; height: 80px; background: radial-gradient(circle, #f0b429, transparent); top: -20px; right: 20px; animation-delay: 0s; }
.particle.p2 { width: 50px; height: 50px; background: radial-gradient(circle, #ff8c42, transparent); bottom: 40px; left: -10px; animation-delay: 1.5s; }
.particle.p3 { width: 60px; height: 60px; background: radial-gradient(circle, #d4940a, transparent); top: 50%; left: 30px; animation-delay: 3s; }
.particle.p4 { width: 40px; height: 40px; background: radial-gradient(circle, #10b981, transparent); bottom: 10px; right: 60px; animation-delay: 4.5s; }
body.dark-mode .particle { opacity: 0.2; }

.floating-accent-bar {
    position: relative; z-index: 1;
    height: 3px; width: 100%;
    background: linear-gradient(90deg, #d4940a, #f0b429, #ff8c42, #f0b429, #d4940a);
    background-size: 200% 100%;
    animation: accentBarShimmer 3s ease-in-out infinite;
    border-radius: 28px 28px 0 0;
}
.floating-header {
    position: relative; z-index: 1;
    display: flex; align-items: flex-start; gap: 14px;
    padding: 24px 28px 16px;
}
.floating-icon-wrap {
    width: 48px; height: 48px; border-radius: 16px; flex-shrink: 0;
    background: linear-gradient(135deg, #d4940a, #e07020);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: #1e1a0c;
    box-shadow: 0 4px 16px rgba(240,180,41,0.35);
    animation: iconPulse 2.5s ease-in-out infinite;
}
.floating-title { font-size: 1.1rem; font-weight: 800; color: var(--text-primary); margin: 2px 0 2px; line-height: 1.2; }
.floating-subtitle { font-size: 0.72rem; color: var(--text-muted); font-weight: 500; margin: 0; }
.floating-close {
    margin-left: auto; flex-shrink: 0;
    width: 34px; height: 34px; border-radius: 12px;
    border: 1px solid var(--glass-border); background: var(--glass-bg); color: var(--text-muted);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem; transition: all 0.2s;
}
.floating-close:hover { background: rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.3); color: #ef4444; transform: rotate(90deg); }
.floating-body { position: relative; z-index: 1; padding: 0 28px 24px; }
.floating-field { animation: fieldSlideUp 0.4s cubic-bezier(0.22, 1, 0.36, 1) both; animation-delay: calc(var(--i, 1) * 0.07s); margin-bottom: 16px; }
.floating-input {
    width: 100%; padding: 10px 16px;
    background: var(--input-bg); border: 1px solid var(--input-border);
    border-radius: 14px; color: var(--text-primary);
    font-size: 0.85rem; outline: none;
    transition: all 0.25s;
}
.floating-input:focus { border-color: var(--accent-gold); box-shadow: 0 0 0 3px rgba(240,180,41,0.12); }
.floating-input::placeholder { color: var(--text-muted); opacity: 0.5; }
.floating-select { appearance: none; -webkit-appearance: none; padding-right: 36px; cursor: pointer; }
.floating-select option { background: var(--white); color: var(--text-primary); }
.floating-select-wrap { position: relative; }
.floating-select-arrow { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.7rem; pointer-events: none; }
.floating-actions {
    display: flex; gap: 10px; margin-top: 20px; padding-top: 16px;
    border-top: 1px solid var(--glass-border);
    animation: fieldSlideUp 0.4s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: calc(var(--i, 1) * 0.07s);
}
.floating-btn {
    padding: 11px 20px; border-radius: 14px; font-weight: 700; font-size: 0.82rem;
    cursor: pointer; transition: all 0.25s; border: none;
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    text-decoration: none;
}
.floating-btn-primary {
    flex: 1;
    background: linear-gradient(90deg, #d4940a, #e07020);
    color: #1e1a0c;
    box-shadow: 0 4px 14px rgba(240,180,41,0.3);
    position: relative; overflow: hidden;
}
.floating-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(240,180,41,0.4); }
.floating-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }
.floating-btn-secondary {
    background: transparent; border: 1px solid var(--glass-border);
    color: var(--text-muted); min-width: 80px;
}
.floating-btn-secondary:hover { background: rgba(240,180,41,0.08); border-color: rgba(240,180,41,0.3); color: var(--accent-gold); }

/* Modal TX filter */
.tx-filter-bar {
    display: flex; flex-wrap: wrap; gap: 8px;
    margin-bottom: 14px; padding: 10px 14px;
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    border-radius: 14px; align-items: center;
}
.tx-filter-label {
    font-size: 0.72rem; color: var(--text-muted); font-weight: 600;
    display: flex; align-items: center; gap: 4px; margin-right: 2px;
}
.tx-filter-input {
    padding: 6px 10px; background: var(--input-bg); border: 1px solid var(--input-border);
    border-radius: 40px; color: var(--text-primary); font-size: 0.75rem;
    outline: none; transition: all 0.2s; width: auto;
}
.tx-filter-input:focus { border-color: var(--accent-gold); }
.tx-filter-input.w-day { width: 60px; }
.tx-filter-input.w-month { width: 100px; }
.tx-filter-input.w-year { width: 80px; }

/* ── Pagination ────────────────────────── */
.pagination-wrapper {
    display: flex; justify-content: space-between; align-items: center;
    padding: 14px 4px 4px; flex-wrap: wrap; gap: 12px;
}
.pagination-left { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.pagination-right { display: flex; align-items: center; gap: 4px; }
.page-size-label { font-size: 0.78rem; color: var(--text-secondary); }
.page-size-select {
    padding: 4px 10px; border: 1px solid var(--glass-border); border-radius: 8px;
    background: var(--glass-bg); color: var(--text-primary); font-size: 0.78rem; cursor: pointer;
    outline: none; width: auto;
}
.page-size-select option { background: var(--white); color: var(--text-primary); }
.pagination-info { font-size: 0.78rem; color: var(--text-muted); }
.page-btn {
    min-width: 32px; height: 32px; padding: 0 7px; border: 1px solid var(--glass-border);
    background: var(--glass-bg); color: var(--text-secondary); border-radius: 8px;
    cursor: pointer; font-size: 0.78rem; font-weight: 600; transition: all 0.2s;
    display: inline-flex; align-items: center; justify-content: center;
}
.page-btn:hover:not(.disabled):not(.active) { background: rgba(240,180,41,0.15); border-color: var(--accent-gold); color: var(--accent-gold); }
.page-btn.active { background: linear-gradient(90deg, var(--accent-gold), var(--accent-orange)); color: #1e1a0c; border-color: transparent; }
.page-btn.disabled { opacity: 0.35; cursor: not-allowed; }
.page-ellipsis { padding: 0 4px; color: var(--text-muted); font-weight: 600; }

/* ── Empty State ───────────────────────── */
.empty-state { text-align: center; padding: 40px; color: var(--text-muted); }
.empty-state i { font-size: 2rem; margin-bottom: 10px; display: block; }

/* ── Responsive ────────────────────────── */
@media (max-width: 1024px) {
    .chart-grid { grid-template-columns: 1fr; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .sidebar { position: fixed; left: -260px; top: 0; height: 100%; width: 260px; }
    .sidebar.collapsed { width: 260px; left: -260px; }
    .sidebar.open { left: 0; }
    .stats-grid { grid-template-columns: 1fr; }
    .main-content { padding: 16px; }
    .filter-bar-inner { flex-direction: column; align-items: stretch; }
    .search-bar { max-width: 100%; }
    .filter-actions { flex-wrap: wrap; }
    .chart-grid { gap: 12px; }
}
@media (max-width: 480px) {
    .main-content { padding: 10px; }
    .stat-card { padding: 12px 14px; gap: 10px; }
    .stat-icon-wrap { width: 32px; height: 32px; font-size: 0.8rem; border-radius: 9px; }
    .stat-value { font-size: 1.05rem; }
    .card { padding: 14px; border-radius: 18px; }
    th, td { padding: 8px 6px; font-size: 0.7rem; }
    .floating-header { padding: 18px 18px 12px; }
    .floating-body { padding: 0 18px 18px; }
    .floating-title { font-size: 1rem; }
    .floating-icon-wrap { width: 40px; height: 40px; font-size: 1.1rem; border-radius: 13px; }
    .floating-input { padding: 9px 14px; font-size: 0.8rem; border-radius: 12px; }
    .floating-btn { padding: 10px 16px; font-size: 0.78rem; border-radius: 12px; }
    .modal-floating-card { border-radius: 22px; }
    .section-header h2 { font-size: 1.2rem; }
    .tx-filter-bar { flex-direction: column; align-items: stretch; }
    .tx-filter-input.w-day, .tx-filter-input.w-month, .tx-filter-input.w-year { width: 100%; }
    table th:nth-child(1), table td:nth-child(1) { display: none; }
    table th:nth-child(9), table td:nth-child(9) { display: none; }
    .pagination-wrapper { flex-direction: column; align-items: center; }
    .pagination-left { justify-content: center; }
    .pagination-right { flex-wrap: wrap; justify-content: center; }
    .page-btn { min-width: 28px; height: 28px; font-size: 0.68rem; padding: 0 5px; }
}
</style>
@endpush

@section('content')
<!-- Splash Screen -->
<div id="splashScreen" style="position:fixed;inset:0;z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;background:radial-gradient(ellipse at center,#1a1635 0%,#0f0c29 100%);transition:opacity 0.6s ease,visibility 0.6s ease;">
    <div style="position:relative;width:80px;height:80px;margin-bottom:28px;">
        <div style="position:absolute;inset:0;border-radius:50%;border:3px solid rgba(240,180,41,0.15);border-top-color:#f0b429;animation:splashSpin 1s cubic-bezier(0.6,0,0.4,1) infinite;"></div>
        <div style="position:absolute;inset:4px;border-radius:50%;border:3px solid transparent;border-bottom-color:rgba(255,140,66,0.4);animation:splashSpin 0.8s cubic-bezier(0.6,0,0.4,1) infinite reverse;"></div>
        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;"><i class="fas fa-crown" style="font-size:26px;color:#f0b429;filter:drop-shadow(0 0 8px rgba(240,180,41,0.4));"></i></div>
    </div>
    <h2 style="font-size:1.4rem;font-weight:700;margin-bottom:6px;background:linear-gradient(90deg,#f0b429,#ff8c42);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Dompet Digital</h2>
    <p style="color:rgba(255,255,255,0.5);font-size:0.85rem;letter-spacing:1px;"><span id="splashDots">Memuat</span></p>
</div>

<style>@keyframes splashSpin { to { transform: rotate(360deg); } }</style>
<script>
let dotCount = 0;
window.splashDotInterval = setInterval(() => {
    dotCount = (dotCount + 1) % 4;
    const el = document.getElementById('splashDots');
    if (el) el.textContent = 'Memuat' + '.'.repeat(dotCount);
}, 500);
</script>

<div class="app-container">
    <!-- Admin Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon"><i class="fas fa-crown"></i></div>
                <div class="sidebar-brand-text">
                    <h2><span>Admin Panel</span></h2>
                    <p>Manajemen Dompet Digital</p>
                </div>
            </div>
        </div>
        <nav>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link active" onclick="showSection('dashboard')">
                        <i class="fas fa-tachometer-alt"></i> <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="showSection('users')">
                        <i class="fas fa-users"></i> <span class="nav-text">Manajemen User</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="showSection('profile')">
                        <i class="fas fa-user-cog"></i> <span class="nav-text">Profil Saya</span>
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

    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="top-bar-content">
                <button class="menu-toggle" onclick="toggleSidebar()" title="Buka/Tutup Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="top-bar-right">
                    <div class="user-info" onclick="showSection('profile')" title="Pengaturan Profil">
                        <i class="fas fa-user-shield"></i>
                        <span id="adminName">Admin</span>
                    </div>
                    <button class="dark-mode-toggle" id="darkModeToggle" title="Mode Gelap/Terang">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ── Dashboard Section ────────── -->
        <div id="dashboardSection">
            <div class="section-header">
                <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
                <p>Overview seluruh data platform</p>
            </div>
            <div class="stats-grid">
                <div class="stat-card stat-card-users fade-in">
                    <div class="stat-icon-wrap"><i class="fas fa-users"></i></div>
                    <div class="stat-body">
                        <div class="stat-label"><i class="fas fa-circle" style="font-size:0.4rem;color:#3b82f6;"></i> Total Pengguna</div>
                        <div class="stat-value" id="totalUsers">0</div>
                    </div>
                    <div class="stat-glow stat-glow-users"></div>
                </div>
                <div class="stat-card stat-card-tx fade-in">
                    <div class="stat-icon-wrap"><i class="fas fa-exchange-alt"></i></div>
                    <div class="stat-body">
                        <div class="stat-label"><i class="fas fa-circle" style="font-size:0.4rem;color:#8b5cf6;"></i> Total Transaksi</div>
                        <div class="stat-value" id="totalTransactions">0</div>
                    </div>
                    <div class="stat-glow stat-glow-tx"></div>
                </div>
                <div class="stat-card stat-card-income fade-in">
                    <div class="stat-icon-wrap"><i class="fas fa-circle-down"></i></div>
                    <div class="stat-body">
                        <div class="stat-label"><i class="fas fa-circle" style="font-size:0.4rem;color:#10b981;"></i> Total Pemasukan</div>
                        <div class="stat-value stat-text-income" id="totalIncome">Rp 0</div>
                    </div>
                    <div class="stat-glow stat-glow-income"></div>
                </div>
                <div class="stat-card stat-card-expense fade-in">
                    <div class="stat-icon-wrap"><i class="fas fa-circle-up"></i></div>
                    <div class="stat-body">
                        <div class="stat-label"><i class="fas fa-circle" style="font-size:0.4rem;color:#ef4444;"></i> Total Pengeluaran</div>
                        <div class="stat-value stat-text-expense" id="totalExpense">Rp 0</div>
                    </div>
                    <div class="stat-glow stat-glow-expense"></div>
                </div>
                <div class="stat-card stat-card-main fade-in">
                    <div class="stat-icon-wrap"><i class="fas fa-wallet"></i></div>
                    <div class="stat-body">
                        <div class="stat-label"><i class="fas fa-circle" style="font-size:0.4rem;color:var(--accent-gold);"></i> Saldo Dompet Utama</div>
                        <div class="stat-value stat-text-gold" id="totalMainBalance">Rp 0</div>
                    </div>
                    <div class="stat-glow stat-glow-main"></div>
                </div>
                <div class="stat-card stat-card-savings fade-in">
                    <div class="stat-icon-wrap"><i class="fas fa-piggy-bank"></i></div>
                    <div class="stat-body">
                        <div class="stat-label"><i class="fas fa-circle" style="font-size:0.4rem;color:#10b981;"></i> Saldo Tabungan</div>
                        <div class="stat-value stat-text-green" id="totalSavingsBalance">Rp 0</div>
                    </div>
                    <div class="stat-glow stat-glow-savings"></div>
                </div>
            </div>

            <div class="charts-section">
                <div class="chart-grid">
                    <div class="chart-card chart-card-income">
                        <div class="chart-card-header">
                            <div class="chart-card-icon"><i class="fas fa-chart-bar"></i></div>
                            <div>
                                <div class="chart-card-title">Grafik Pemasukan per User</div>
                                <div class="chart-card-subtitle">Urut dari yang terbesar</div>
                            </div>
                        </div>
                        <div class="chart-canvas-wrap"><canvas id="incomeBarChart"></canvas></div>
                    </div>
                    <div class="chart-card chart-card-expense">
                        <div class="chart-card-header">
                            <div class="chart-card-icon"><i class="fas fa-chart-bar"></i></div>
                            <div>
                                <div class="chart-card-title">Grafik Pengeluaran per User</div>
                                <div class="chart-card-subtitle">Urut dari yang terbesar</div>
                            </div>
                        </div>
                        <div class="chart-canvas-wrap"><canvas id="expenseBarChart"></canvas></div>
                    </div>
                </div>
                <div class="chart-card chart-card-wallet">
                    <div class="chart-card-header">
                        <div class="chart-card-icon"><i class="fas fa-wallet"></i></div>
                        <div>
                            <div class="chart-card-title">Saldo Dompet per User</div>
                            <div class="chart-card-subtitle">Dompet Utama vs Tabungan</div>
                        </div>
                    </div>
                    <div class="chart-canvas-wrap"><canvas id="walletBarChart"></canvas></div>
                </div>
            </div>
        </div>

        <!-- ── Users Section ────────────── -->
        <div id="usersSection" style="display: none;">
            <div class="section-header">
                <h2><i class="fas fa-users"></i> Manajemen User</h2>
                <p>Kelola semua pengguna platform</p>
            </div>

            <div class="filter-bar-card">
                <div class="filter-bar-inner">
                    <div class="filter-actions">
                        <button class="btn btn-success" onclick="openFloatingModal('addUserModal')"><i class="fas fa-user-plus"></i> Tambah User</button>
                        <button class="btn btn-outline" onclick="loadUsers()"><i class="fas fa-sync-alt"></i> Refresh</button>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <div class="search-bar">
                            <i class="fas fa-search search-bar-icon"></i>
                            <input type="text" class="search-bar-input" id="searchUser" placeholder="Cari user..." onkeyup="filterUsers()">
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="exportUsersToExcel()" title="Export Excel"><i class="fas fa-file-excel"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="exportUsersToPDF()" title="Export PDF"><i class="fas fa-file-pdf"></i></button>
                    </div>
                </div>
            </div>

            <div class="card" style="background:var(--card-bg);backdrop-filter:blur(12px);border:1px solid var(--glass-border);border-radius:24px;padding:0;overflow:hidden;">
                <div class="table-container" style="padding:4px 0;">
                    <table>
                        <thead>
                            <tr><th>ID</th><th>Username</th><th>Nama Lengkap</th><th>Role</th><th>Transaksi</th><th>Pemasukan</th><th>Pengeluaran</th><th>Dompet</th><th>Tabungan</th><th>Saldo</th><th>Bergabung</th><th>Aksi</th></tr>
                        </thead>
                        <tbody id="usersTable">
                            <tr><td colspan="12" style="text-align:center;padding:32px;"><div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Memuat data...</p></div></td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="userPaginationContainer" style="padding:0 16px 12px;"></div>
            </div>
        </div>

        <!-- ── Profile Section ──────────── -->
        <div id="profileSection" style="display: none;">
            <div class="section-header">
                <h2><i class="fas fa-user-cog"></i> Pengaturan Profil</h2>
                <p>Kelola data profil Anda</p>
            </div>
            <div class="card" style="max-width:600px;">
                <form id="profileForm">
                    @csrf
                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> Nama Lengkap <span style="color:var(--danger)">*</span></label>
                        <input type="text" class="form-control" id="profileFullName" required placeholder="Masukkan nama lengkap">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Username</label>
                        <input type="text" class="form-control" id="profileUsername" disabled style="color:var(--text-muted);">
                    </div>
                    <button type="submit" class="btn btn-primary" id="saveProfileBtn" style="width:100%;justify-content:center;"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </form>
                <div class="divider" style="margin:24px 0;"></div>
                <h4 style="color:var(--text-secondary);margin-bottom:16px;display:flex;align-items:center;gap:8px;"><i class="fas fa-lock" style="color:var(--accent-gold);"></i> Ganti Password</h4>
                <form id="passwordForm">
                    @csrf
                    <div class="form-group">
                        <label><i class="fas fa-key"></i> Password Lama <span style="color:var(--danger)">*</span></label>
                        <input type="password" class="form-control" id="currentPassword" required placeholder="Masukkan password lama">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password Baru <span style="color:var(--danger)">*</span></label>
                        <input type="password" class="form-control" id="newPassword" required minlength="8" placeholder="Minimal 8 karakter">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-check-circle"></i> Konfirmasi Password Baru <span style="color:var(--danger)">*</span></label>
                        <input type="password" class="form-control" id="confirmPassword" required placeholder="Ulangi password baru">
                    </div>
                    <button type="submit" class="btn btn-primary" id="savePasswordBtn" style="width:100%;justify-content:center;"><i class="fas fa-key"></i> Ganti Password</button>
                </form>
            </div>
        </div>

        <footer class="app-footer">
            <span>&copy; 2026.</span>
            <span class="footer-heart">&nbsp;Made with <i class="fas fa-heart"></i> and coffee, dedicated to be useful.</span>
        </footer>
    </main>
</div>

<!-- ── Modal Tambah User — Premium Floating ── -->
<div id="addUserModal" class="modal-floating">
    <div class="modal-floating-overlay"></div>
    <div class="modal-floating-card" id="floatingAddCard">
        <div class="modal-particles">
            <div class="particle p1"></div>
            <div class="particle p2"></div>
            <div class="particle p3"></div>
            <div class="particle p4"></div>
        </div>
        <div class="floating-accent-bar"></div>
        <div class="floating-header">
            <div class="floating-icon-wrap"><i class="fas fa-user-plus"></i></div>
            <div>
                <h3 class="floating-title">Tambah User Baru</h3>
                <p class="floating-subtitle">Buat akun baru untuk pengguna</p>
            </div>
            <button type="button" class="floating-close" onclick="closeFloatingModal('addUserModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="floating-body">
            <form id="addUserForm">
                @csrf
                <div class="floating-field" style="--i:1;">
                    <label class="floating-label"><i class="fas fa-id-card"></i> Nama Lengkap</label>
                    <input type="text" class="floating-input" id="addFullName" required placeholder="Masukkan nama lengkap">
                </div>
                <div class="floating-field" style="--i:2;">
                    <label class="floating-label"><i class="fas fa-user"></i> Username</label>
                    <input type="text" class="floating-input" id="addUsername" required minlength="3" placeholder="Minimal 3 karakter">
                </div>
                <div class="floating-field" style="--i:3;">
                    <label class="floating-label"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" class="floating-input" id="addPassword" required minlength="8" placeholder="Minimal 8 karakter">
                </div>
                <div class="floating-field" style="--i:4;">
                    <label class="floating-label"><i class="fas fa-tag"></i> Role</label>
                    <div class="floating-select-wrap">
                        <select class="floating-input floating-select" id="addRole">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                        <i class="fas fa-chevron-down floating-select-arrow"></i>
                    </div>
                </div>
                <div class="floating-actions" style="--i:5;">
                    <button type="button" class="floating-btn floating-btn-primary" onclick="saveNewUser()" id="addUserBtn">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button type="button" class="floating-btn floating-btn-secondary" onclick="closeFloatingModal('addUserModal')">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal Edit User — Premium Floating ── -->
<div id="editUserModal" class="modal-floating">
    <div class="modal-floating-overlay"></div>
    <div class="modal-floating-card" id="floatingEditCard">
        <div class="modal-particles">
            <div class="particle p1"></div>
            <div class="particle p2"></div>
            <div class="particle p3"></div>
            <div class="particle p4"></div>
        </div>
        <div class="floating-accent-bar"></div>
        <div class="floating-header">
            <div class="floating-icon-wrap"><i class="fas fa-user-edit"></i></div>
            <div>
                <h3 class="floating-title">Edit User</h3>
                <p class="floating-subtitle">Ubah data pengguna</p>
            </div>
            <button type="button" class="floating-close" onclick="closeFloatingModal('editUserModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="floating-body">
            <form id="editUserForm">
                @csrf
                <input type="hidden" id="editUserId">
                <div class="floating-field" style="--i:1;">
                    <label class="floating-label"><i class="fas fa-id-card"></i> Nama Lengkap</label>
                    <input type="text" class="floating-input" id="editFullName" required>
                </div>
                <div class="floating-field" style="--i:2;">
                    <label class="floating-label"><i class="fas fa-tag"></i> Role</label>
                    <div class="floating-select-wrap">
                        <select class="floating-input floating-select" id="editRole">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                        <i class="fas fa-chevron-down floating-select-arrow"></i>
                    </div>
                </div>
                <div class="floating-field" style="--i:3;">
                    <label class="floating-label"><i class="fas fa-lock"></i> Password Baru <span class="floating-optional">(kosongkan jika tidak diubah)</span></label>
                    <input type="password" class="floating-input" id="editPassword" placeholder="Minimal 8 karakter" minlength="8">
                </div>
                <div class="floating-actions" style="--i:4;">
                    <button type="button" class="floating-btn floating-btn-primary" onclick="saveUserEdit()">
                        <i class="fas fa-check"></i> Simpan
                    </button>
                    <button type="button" class="floating-btn floating-btn-secondary" onclick="closeFloatingModal('editUserModal')">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal Transaksi User — Premium Floating ── -->
<div id="userTransactionsModal" class="modal-floating">
    <div class="modal-floating-overlay"></div>
    <div class="modal-floating-card wide" id="floatingTxCard">
        <div class="modal-particles">
            <div class="particle p1"></div>
            <div class="particle p2"></div>
            <div class="particle p3"></div>
            <div class="particle p4"></div>
        </div>
        <div class="floating-accent-bar"></div>
        <div class="floating-header">
            <div class="floating-icon-wrap"><i class="fas fa-history"></i></div>
            <div>
                <h3 class="floating-title" id="userTxTitle">Transaksi User</h3>
                <p class="floating-subtitle">Riwayat transaksi pengguna</p>
            </div>
            <button type="button" class="floating-close" onclick="closeFloatingModal('userTransactionsModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="floating-body">
            <div class="tx-filter-bar">
                <span class="tx-filter-label"><i class="fas fa-filter"></i> Filter</span>
                <input type="number" id="txFilterDay" class="tx-filter-input w-day" placeholder="Hari" min="1" max="31" onchange="applyUserTxFilter(1)">
                <select id="txFilterMonth" class="tx-filter-input w-month" onchange="applyUserTxFilter(1)">
                    <option value="">Bulan</option>
                    <option value="1">Jan</option><option value="2">Feb</option><option value="3">Mar</option>
                    <option value="4">Apr</option><option value="5">Mei</option><option value="6">Jun</option>
                    <option value="7">Jul</option><option value="8">Agu</option><option value="9">Sep</option>
                    <option value="10">Okt</option><option value="11">Nov</option><option value="12">Des</option>
                </select>
                <input type="number" id="txFilterYear" class="tx-filter-input w-year" placeholder="Tahun" min="2000" max="2100" onchange="applyUserTxFilter(1)">
                <button class="btn btn-sm btn-secondary" onclick="resetUserTxFilter()" title="Reset"><i class="fas fa-undo"></i></button>
            </div>
            <div id="userTransactionsContent" class="table-container"></div>
            <div id="userTxPagination" class="pagination-bar" style="display:none;"></div>
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
<script src="{{ url('/js/admin.js') }}"></script>
@endpush
