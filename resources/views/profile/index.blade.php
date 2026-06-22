@extends('layouts.app')

@section('title', 'Profil Saya - Dompet Digital')

@push('styles')
<style>
/* ── Profile page specific styles ──────── */

/* Profile Hero */
.profile-hero {
    display: flex;
    align-items: center;
    gap: 28px;
    margin-bottom: 32px;
    padding: 32px 36px;
    background: linear-gradient(135deg, rgba(240,180,41,0.08), rgba(255,140,66,0.03));
    border: 1px solid rgba(240,180,41,0.15);
    border-radius: 28px;
    position: relative;
}
.profile-hero .avatar-wrap {
    position: relative;
    flex-shrink: 0;
}
.profile-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #d4940a, #e07020, #d4940a);
}
.profile-avatar {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #d4940a, #e07020);
    display: flex; align-items: center; justify-content: center;
    font-size: 2.2rem; color: #1e1a0c;
    flex-shrink: 0;
    box-shadow: 0 8px 24px rgba(240,180,41,0.3);
    position: relative;
    transition: all 0.3s ease;
}
.profile-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 32px rgba(240,180,41,0.45);
}
.profile-avatar .avatar-badge {
    position: absolute; bottom: -2px; right: -2px;
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--success);
    border: 3px solid var(--white);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.7rem; color: #fff;
}
.avatar-edit-overlay {
    position: absolute; inset: 0; border-radius: 50%;
    background: rgba(0,0,0,0.4);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity 0.3s ease;
    color: #fff; font-size: 1.4rem;
    pointer-events: none;
    cursor: pointer;
}
.profile-avatar:hover .avatar-edit-overlay { opacity: 1; }
.profile-hero-body { flex: 1; }
.profile-hero-title { font-size: 1.6rem; font-weight: 900; letter-spacing: -0.03em; color: var(--text-primary); }
.profile-hero-title i { color: var(--accent-gold); margin-right: 8px; }
.profile-hero-sub { font-size: 0.82rem; color: var(--text-muted); margin-top: 4px; }
.profile-hero-glow {
    position: absolute; width: 300px; height: 300px;
    border-radius: 50%; right: -60px; top: -60px;
    background: radial-gradient(circle, rgba(240,180,41,0.1) 0%, transparent 70%);
    pointer-events: none;
}

/* Form Cards */
.form-card {
    background: var(--card-bg);
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 24px;
    padding: 28px 32px;
    transition: all 0.3s;
    margin-bottom: 24px;
}
.form-card:hover { border-color: rgba(240,180,41,0.25); }
.form-card-header {
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 24px; padding-bottom: 16px;
    border-bottom: 1px solid var(--glass-border);
}
.form-card-icon {
    width: 44px; height: 44px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.form-card-icon.profile-icon { background: linear-gradient(135deg, rgba(240,180,41,0.2), rgba(240,180,41,0.06)); color: var(--accent-gold); border: 1px solid rgba(240,180,41,0.2); }
.form-card-icon.lock-icon { background: linear-gradient(135deg, rgba(59,130,246,0.2), rgba(59,130,246,0.06)); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2); }
.form-card-title { font-size: 1rem; font-weight: 700; color: var(--text-primary); line-height: 1.2; }
.form-card-subtitle { font-size: 0.72rem; color: var(--text-muted); font-weight: 500; }
.form-card-body { max-width: 520px; }

/* Form overrides */
.form-group label {
    display: block; font-size: 0.78rem; font-weight: 600;
    color: var(--text-secondary); margin-bottom: 6px;
}
.form-control {
    padding: 11px 18px;
}
.btn {
    padding: 11px 24px; font-size: 0.85rem;
}
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(240,180,41,0.25); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }
.btn-outline:hover { transform: translateY(-1px); }
.divider {
    height: 1px; background: var(--glass-border); margin: 24px 0;
}

/* Quote Card */
.quote-card {
    position: relative;
    margin-bottom: 28px;
    padding: 24px 32px;
    border-radius: 20px;
    background: linear-gradient(135deg, rgba(240,180,41,0.1), rgba(255,140,66,0.05));
    background-size: 200% 200%;
    border: 1px solid rgba(240,180,41,0.18);
    overflow: hidden;
    transition: all 0.35s cubic-bezier(0.22, 1, 0.36, 1);
    animation: quoteCardEntry 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
}
@keyframes quoteCardEntry {
    0% { opacity: 0; transform: translateY(24px) scale(0.97); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
.quote-card:hover {
    border-color: rgba(240,180,41,0.35);
    box-shadow: 0 8px 32px rgba(240,180,41,0.08);
    background-position: 100% 100%;
}
.quote-card::before {
    content: '\f10d'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
    position: absolute; top: -6px; left: -4px; font-size: 4.5rem;
    color: var(--accent-gold); opacity: 0.06; pointer-events: none;
    line-height: 1;
    animation: quoteMarkFloat 6s ease-in-out infinite;
}
.quote-card::after {
    content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%;
    background: linear-gradient(180deg, var(--accent-gold), var(--accent-orange));
    border-radius: 0 2px 2px 0;
    animation: accentBarPulse 3s ease-in-out infinite;
}
.quote-card-inner {
    display: flex; align-items: center; gap: 16px;
    position: relative; z-index: 1;
}
.quote-icon {
    flex-shrink: 0;
    width: 44px; height: 44px; border-radius: 14px;
    background: linear-gradient(135deg, var(--accent-gold), var(--accent-orange));
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; color: #1e1a0c;
    box-shadow: 0 4px 14px rgba(240,180,41,0.25);
    animation: quoteIconPulse 2.5s ease-in-out infinite;
}
@keyframes quoteIconPulse {
    0%, 100% { box-shadow: 0 4px 14px rgba(240,180,41,0.25); transform: scale(1); }
    50% { box-shadow: 0 4px 24px rgba(240,180,41,0.45); transform: scale(1.05); }
}
.quote-content { flex: 1; min-width: 0; }
.quote-text {
    font-size: 1.25rem; font-weight: 600; line-height: 1.6;
    color: var(--text-primary); margin: 0;
    transition: all 0.2s;
    word-wrap: break-word;
    min-height: 1.5em;
}
.quote-text .editable-hint {
    font-weight: 400; font-size: 0.78rem; color: var(--text-muted);
    opacity: 0; transition: opacity 0.25s;
}
.quote-card:hover .quote-text .editable-hint { opacity: 1; }
.quote-text-input {
    display: none;
    width: 100%; padding: 8px 14px;
    background: var(--input-bg); border: 2px solid var(--accent-gold);
    border-radius: 12px; color: var(--text-primary);
    font-size: 0.92rem; font-weight: 500;
    outline: none; line-height: 1.5;
    transition: box-shadow 0.2s;
}
.quote-text-input:focus { box-shadow: 0 0 0 3px rgba(240,180,41,0.15); }
.quote-text-input::placeholder { color: var(--text-muted); opacity: 0.5; }

.quote-actions {
    display: flex; gap: 6px; flex-shrink: 0;
    opacity: 0; transition: opacity 0.25s;
}
.quote-card:hover .quote-actions { opacity: 1; }
.quote-btn {
    width: 34px; height: 34px; border-radius: 10px;
    border: 1px solid var(--glass-border);
    background: var(--glass-bg); color: var(--text-muted);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem; transition: all 0.2s;
}
.quote-btn:hover { border-color: var(--accent-gold); color: var(--accent-gold); background: rgba(240,180,41,0.08); }
.quote-btn.save-btn:hover { border-color: var(--success); color: var(--success); background: rgba(16,185,129,0.08); }
.quote-btn.cancel-btn:hover { border-color: var(--danger); color: var(--danger); background: rgba(239,68,68,0.08); }

.quote-glow {
    position: absolute; width: 200px; height: 200px;
    border-radius: 50%; right: -40px; bottom: -60px;
    background: radial-gradient(circle, rgba(240,180,41,0.08) 0%, transparent 70%);
    pointer-events: none;
    animation: glowDrift 8s ease-in-out infinite;
}
@keyframes glowDrift {
    0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.4; }
    33% { transform: translate(-20px, -15px) scale(1.2); opacity: 0.7; }
    66% { transform: translate(10px, -8px) scale(0.9); opacity: 0.3; }
}

.quote-shimmer {
    position: absolute; top: 0; left: -100%; width: 60%; height: 1px;
    background: linear-gradient(90deg, transparent, rgba(240,180,41,0.3), transparent);
    animation: shimmerSweep 4s ease-in-out infinite;
    pointer-events: none;
}
.quote-shimmer-2 {
    position: absolute; bottom: 0; right: -100%; width: 40%; height: 1px;
    background: linear-gradient(270deg, transparent, rgba(240,180,41,0.15), transparent);
    animation: shimmerSweep2 5s ease-in-out infinite 1s;
    pointer-events: none;
}

.quote-dot {
    position: absolute; border-radius: 50%;
    pointer-events: none; z-index: 0;
    opacity: 0.12;
}
.quote-dot.d1 {
    width: 12px; height: 12px; background: var(--accent-gold);
    top: 20%; right: 15%;
    animation: dotFloat1 7s ease-in-out infinite;
}
.quote-dot.d2 {
    width: 8px; height: 8px; background: var(--accent-orange);
    bottom: 25%; right: 35%;
    animation: dotFloat2 9s ease-in-out infinite 1s;
}
.quote-dot.d3 {
    width: 6px; height: 6px; background: var(--accent-gold);
    top: 40%; left: 20%;
    animation: dotFloat3 6s ease-in-out infinite 0.5s;
}

.quote-typing-cursor {
    display: inline-block;
    width: 2px; height: 1.1em;
    background: var(--accent-gold);
    margin-left: 2px;
    vertical-align: text-bottom;
    animation: cursorBlink 0.8s step-end infinite;
}

.quote-card.editing .quote-text { display: none; }
.quote-card.editing .quote-text-input { display: block; }

/* Avatar Picker */
.avatar-picker-item {
    width: 100%; aspect-ratio: 1; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; color: #fff; cursor: pointer;
    border: 3px solid transparent; transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.avatar-picker-item:hover {
    transform: scale(1.12);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}
.avatar-picker-item.selected {
    border-color: var(--accent-gold);
    box-shadow: 0 0 0 3px var(--accent-gold), 0 6px 20px rgba(240,180,41,0.3);
    transform: scale(1.08);
}
.avatar-picker-item .check-badge {
    position: absolute; bottom: -2px; right: -2px;
    width: 22px; height: 22px; border-radius: 50%;
    background: var(--accent-gold);
    border: 2px solid var(--white);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.55rem; color: #1e1a0c;
    opacity: 0; transform: scale(0.5); transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.avatar-picker-item.selected .check-badge {
    opacity: 1; transform: scale(1);
}
.avatar-picker-label {
    text-align: center; font-size: 0.6rem; color: var(--text-muted);
    margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* Floating modal overrides for avatar picker */
.modal-floating-card { max-width: 440px; }
.floating-body {
    position: relative; z-index: 1;
    padding: 0 28px 24px;
}
.floating-avatar-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.floating-desc {
    font-size: 0.75rem; color: var(--text-muted);
    margin-bottom: 16px; line-height: 1.5;
}

/* Page transition */
.fade-in-page { animation: fadeInUp 0.5s ease both; }

/* Responsive */
@media (max-width: 768px) {
    .profile-hero { flex-direction: column; text-align: center; padding: 24px; gap: 16px; }
    .profile-hero-title { font-size: 1.3rem; }
    .form-card { padding: 20px; }
    .profile-avatar { width: 64px; height: 64px; font-size: 1.7rem; }
    .avatar-edit-overlay { font-size: 1.1rem; }
}
@media (max-width: 480px) {
    .profile-hero { padding: 18px; }
    .profile-hero-title { font-size: 1.1rem; }
    .form-card { padding: 16px; border-radius: 18px; }
    .form-card-header { margin-bottom: 16px; padding-bottom: 12px; gap: 10px; }
    .form-card-icon { width: 36px; height: 36px; font-size: 0.9rem; border-radius: 11px; }
    .form-card-title { font-size: 0.9rem; }
    .form-group { margin-bottom: 14px; }
    .form-control { padding: 9px 14px; font-size: 0.82rem; }
    .btn { padding: 9px 18px; font-size: 0.8rem; }
    .profile-avatar { width: 56px; height: 56px; font-size: 1.4rem; }
    .profile-avatar .avatar-badge { width: 22px; height: 22px; font-size: 0.55rem; border-width: 2px; }
    .avatar-edit-overlay { font-size: 0.9rem; }
    .floating-header { padding: 18px 18px 12px; }
    .floating-body { padding: 0 18px 18px; }
    .floating-title { font-size: 1rem; }
    .floating-icon-wrap { width: 40px; height: 40px; font-size: 1.1rem; border-radius: 13px; }
    .floating-btn { padding: 9px 14px; font-size: 0.78rem; border-radius: 12px; }
    .modal-floating-card { border-radius: 22px; }
    .floating-avatar-grid { gap: 8px; }
    .quote-card { padding: 14px 16px; border-radius: 16px; }
    .quote-text { font-size: 0.95rem; }
    .quote-icon { width: 32px; height: 32px; font-size: 0.85rem; border-radius: 10px; }
    .quote-card-inner { gap: 10px; }
    .quote-actions { gap: 4px; }
    .quote-btn { width: 30px; height: 30px; font-size: 0.65rem; border-radius: 8px; }
    .quote-glow { width: 120px; height: 120px; right: -30px; bottom: -40px; }
}
</style>

/* ── Profile Hero ──────────────────────── */
.profile-hero {
    display: flex;
    align-items: center;
    gap: 28px;
    margin-bottom: 32px;
    padding: 32px 36px;
    background: linear-gradient(135deg, rgba(240,180,41,0.08), rgba(255,140,66,0.03));
    border: 1px solid rgba(240,180,41,0.15);
    border-radius: 28px;
    position: relative;
}
.profile-hero .avatar-wrap {
    position: relative;
    flex-shrink: 0;
}
.profile-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #d4940a, #e07020, #d4940a);
}
.profile-avatar {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #d4940a, #e07020);
    display: flex; align-items: center; justify-content: center;
    font-size: 2.2rem; color: #1e1a0c;
    flex-shrink: 0;
    box-shadow: 0 8px 24px rgba(240,180,41,0.3);
    position: relative;
    transition: all 0.3s ease;
}
/* Avatar menu CSS removed — replaced by floating modal above */
.profile-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 32px rgba(240,180,41,0.45);
}
.profile-avatar .avatar-badge {
    position: absolute; bottom: -2px; right: -2px;
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--success);
    border: 3px solid var(--white);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.7rem; color: #fff;
}
.avatar-edit-overlay {
    position: absolute; inset: 0; border-radius: 50%;
    background: rgba(0,0,0,0.4);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity 0.3s ease;
    color: #fff; font-size: 1.4rem;
    pointer-events: none;
    cursor: pointer;
}
.profile-avatar:hover .avatar-edit-overlay {
    opacity: 1;
}
.profile-hero-body { flex: 1; }
.profile-hero-title { font-size: 1.6rem; font-weight: 900; letter-spacing: -0.03em; color: var(--text-primary); }
.profile-hero-title i { color: var(--accent-gold); margin-right: 8px; }
.profile-hero-sub { font-size: 0.82rem; color: var(--text-muted); margin-top: 4px; }
.profile-hero-glow {
    position: absolute; width: 300px; height: 300px;
    border-radius: 50%; right: -60px; top: -60px;
    background: radial-gradient(circle, rgba(240,180,41,0.1) 0%, transparent 70%);
    pointer-events: none;
}

/* ── Profile Cards ─────────────────────── */
.form-card {
    background: var(--card-bg);
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 24px;
    padding: 28px 32px;
    transition: all 0.3s;
    margin-bottom: 24px;
}
.form-card:hover { border-color: rgba(240,180,41,0.25); }
.form-card-header {
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 24px; padding-bottom: 16px;
    border-bottom: 1px solid var(--glass-border);
}
.form-card-icon {
    width: 44px; height: 44px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.form-card-icon.profile-icon { background: linear-gradient(135deg, rgba(240,180,41,0.2), rgba(240,180,41,0.06)); color: var(--accent-gold); border: 1px solid rgba(240,180,41,0.2); }
.form-card-icon.lock-icon { background: linear-gradient(135deg, rgba(59,130,246,0.2), rgba(59,130,246,0.06)); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2); }
.form-card-title { font-size: 1rem; font-weight: 700; color: var(--text-primary); line-height: 1.2; }
.form-card-subtitle { font-size: 0.72rem; color: var(--text-muted); font-weight: 500; }

.form-card-body { max-width: 520px; }

.form-group { margin-bottom: 20px; }
.form-group label {
    display: block; font-size: 0.78rem; font-weight: 600;
    color: var(--text-secondary); margin-bottom: 6px;
}
.form-control {
    padding: 11px 18px; background: var(--input-bg); border: 1px solid var(--input-border);
    border-radius: 40px; color: var(--text-primary); font-size: 0.88rem; outline: none;
    transition: all 0.25s; width: 100%;
}
.form-control:focus { border-color: var(--accent-gold); box-shadow: 0 0 0 3px rgba(240,180,41,0.15); }
.form-control::placeholder { color: var(--text-muted); opacity: 0.6; }
.form-control:disabled { opacity: 0.6; cursor: not-allowed; }
.form-hint { font-size: 0.68rem; color: var(--text-muted); margin-top: 4px; }

.btn {
    padding: 11px 24px; border-radius: 40px; font-weight: 600; font-size: 0.85rem;
    cursor: pointer; transition: all 0.25s ease; border: none;
    display: inline-flex; align-items: center; gap: 8px;
    text-decoration: none;
}
.btn-primary { background: linear-gradient(90deg, var(--accent-gold), var(--accent-orange)); color: #1e1a0c; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(240,180,41,0.25); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }
.btn-outline { background: transparent; border: 1px solid var(--glass-border); color: var(--text-secondary); }
.btn-outline:hover { background: rgba(240,180,41,0.1); border-color: var(--accent-gold); color: var(--accent-gold); transform: translateY(-1px); }
.divider {
    height: 1px; background: var(--glass-border); margin: 24px 0;
}

/* ── Quote Card ─────────────────────────── */
.quote-card {
    position: relative;
    margin-bottom: 28px;
    padding: 24px 32px;
    border-radius: 20px;
    background: linear-gradient(135deg, rgba(240,180,41,0.1), rgba(255,140,66,0.05));
    background-size: 200% 200%;
    border: 1px solid rgba(240,180,41,0.18);
    overflow: hidden;
    transition: all 0.35s cubic-bezier(0.22, 1, 0.36, 1);
    animation: quoteCardEntry 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
}
@keyframes quoteCardEntry {
    0% { opacity: 0; transform: translateY(24px) scale(0.97); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
.quote-card:hover {
    border-color: rgba(240,180,41,0.35);
    box-shadow: 0 8px 32px rgba(240,180,41,0.08);
    background-position: 100% 100%;
}
.quote-card::before {
    content: '\f10d'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
    position: absolute; top: -6px; left: -4px; font-size: 4.5rem;
    color: var(--accent-gold); opacity: 0.06; pointer-events: none;
    line-height: 1;
    animation: quoteMarkFloat 6s ease-in-out infinite;
}
@keyframes quoteMarkFloat {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    25% { transform: translateY(-6px) rotate(-3deg); }
    50% { transform: translateY(0) rotate(0deg); }
    75% { transform: translateY(4px) rotate(3deg); }
}
.quote-card::after {
    content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%;
    background: linear-gradient(180deg, var(--accent-gold), var(--accent-orange));
    border-radius: 0 2px 2px 0;
    animation: accentBarPulse 3s ease-in-out infinite;
}
@keyframes accentBarPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.quote-card-inner {
    display: flex; align-items: center; gap: 16px;
    position: relative; z-index: 1;
}
.quote-icon {
    flex-shrink: 0;
    width: 44px; height: 44px; border-radius: 14px;
    background: linear-gradient(135deg, var(--accent-gold), var(--accent-orange));
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; color: #1e1a0c;
    box-shadow: 0 4px 14px rgba(240,180,41,0.25);
    animation: quoteIconPulse 2.5s ease-in-out infinite;
}
@keyframes quoteIconPulse {
    0%, 100% { box-shadow: 0 4px 14px rgba(240,180,41,0.25); transform: scale(1); }
    50% { box-shadow: 0 4px 24px rgba(240,180,41,0.45); transform: scale(1.05); }
}
.quote-content { flex: 1; min-width: 0; }
.quote-text {
    font-size: 1.25rem; font-weight: 600; line-height: 1.6;
    color: var(--text-primary); margin: 0;
    transition: all 0.2s;
    word-wrap: break-word;
    min-height: 1.5em;
}
.quote-text .editable-hint {
    font-weight: 400; font-size: 0.78rem; color: var(--text-muted);
    opacity: 0; transition: opacity 0.25s;
}
.quote-card:hover .quote-text .editable-hint { opacity: 1; }
.quote-text-input {
    display: none;
    width: 100%; padding: 8px 14px;
    background: var(--input-bg); border: 2px solid var(--accent-gold);
    border-radius: 12px; color: var(--text-primary);
    font-size: 0.92rem; font-weight: 500;
    outline: none; line-height: 1.5;
    transition: box-shadow 0.2s;
}
.quote-text-input:focus { box-shadow: 0 0 0 3px rgba(240,180,41,0.15); }
.quote-text-input::placeholder { color: var(--text-muted); opacity: 0.5; }

.quote-actions {
    display: flex; gap: 6px; flex-shrink: 0;
    opacity: 0; transition: opacity 0.25s;
}
.quote-card:hover .quote-actions { opacity: 1; }
.quote-btn {
    width: 34px; height: 34px; border-radius: 10px;
    border: 1px solid var(--glass-border);
    background: var(--glass-bg); color: var(--text-muted);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem; transition: all 0.2s;
}
.quote-btn:hover { border-color: var(--accent-gold); color: var(--accent-gold); background: rgba(240,180,41,0.08); }
.quote-btn.save-btn:hover { border-color: var(--success); color: var(--success); background: rgba(16,185,129,0.08); }
.quote-btn.cancel-btn:hover { border-color: var(--danger); color: var(--danger); background: rgba(239,68,68,0.08); }

.quote-glow {
    position: absolute; width: 200px; height: 200px;
    border-radius: 50%; right: -40px; bottom: -60px;
    background: radial-gradient(circle, rgba(240,180,41,0.08) 0%, transparent 70%);
    pointer-events: none;
    animation: glowDrift 8s ease-in-out infinite;
}
@keyframes glowDrift {
    0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.4; }
    33% { transform: translate(-20px, -15px) scale(1.2); opacity: 0.7; }
    66% { transform: translate(10px, -8px) scale(0.9); opacity: 0.3; }
}

/* Shimmer border line */
.quote-card::before {
    animation: quoteMarkFloat 6s ease-in-out infinite;
}
.quote-shimmer {
    position: absolute; top: 0; left: -100%; width: 60%; height: 1px;
    background: linear-gradient(90deg, transparent, rgba(240,180,41,0.3), transparent);
    animation: shimmerSweep 4s ease-in-out infinite;
    pointer-events: none;
}
@keyframes shimmerSweep {
    0% { left: -60%; }
    50% { left: 100%; }
    100% { left: 100%; }
}
/* Second shimmer line opposite direction */
.quote-shimmer-2 {
    position: absolute; bottom: 0; right: -100%; width: 40%; height: 1px;
    background: linear-gradient(270deg, transparent, rgba(240,180,41,0.15), transparent);
    animation: shimmerSweep2 5s ease-in-out infinite 1s;
    pointer-events: none;
}
@keyframes shimmerSweep2 {
    0% { right: -40%; }
    50% { right: 100%; }
    100% { right: 100%; }
}

/* Floating decorative dots */
.quote-dot {
    position: absolute; border-radius: 50%;
    pointer-events: none; z-index: 0;
    opacity: 0.12;
}
.quote-dot.d1 {
    width: 12px; height: 12px; background: var(--accent-gold);
    top: 20%; right: 15%;
    animation: dotFloat1 7s ease-in-out infinite;
}
.quote-dot.d2 {
    width: 8px; height: 8px; background: var(--accent-orange);
    bottom: 25%; right: 35%;
    animation: dotFloat2 9s ease-in-out infinite 1s;
}
.quote-dot.d3 {
    width: 6px; height: 6px; background: var(--accent-gold);
    top: 40%; left: 20%;
    animation: dotFloat3 6s ease-in-out infinite 0.5s;
}
@keyframes dotFloat1 {
    0%, 100% { transform: translate(0, 0); }
    25% { transform: translate(-8px, -12px); }
    50% { transform: translate(4px, -6px); }
    75% { transform: translate(-4px, 8px); }
}
@keyframes dotFloat2 {
    0%, 100% { transform: translate(0, 0); }
    33% { transform: translate(6px, -10px); }
    66% { transform: translate(-10px, 4px); }
}
@keyframes dotFloat3 {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(10px, 8px); }
}

/* Typing cursor */
.quote-typing-cursor {
    display: inline-block;
    width: 2px; height: 1.1em;
    background: var(--accent-gold);
    margin-left: 2px;
    vertical-align: text-bottom;
    animation: cursorBlink 0.8s step-end infinite;
}
@keyframes cursorBlink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}

.quote-card.editing .quote-text { display: none; }
.quote-card.editing .quote-text-input { display: block; }

@media (max-width: 768px) {
    .quote-card { padding: 18px 20px; }
    .quote-card::before { font-size: 3rem; }
    .quote-text { font-size: 1.05rem; }
    .quote-icon { width: 36px; height: 36px; font-size: 1rem; border-radius: 11px; }
    .quote-card-inner { gap: 12px; }
    .quote-card:hover .quote-actions { opacity: 1; }
}
@media (max-width: 480px) {
    .quote-card { padding: 14px 16px; border-radius: 16px; }
    .quote-text { font-size: 0.95rem; }
    .quote-icon { width: 32px; height: 32px; font-size: 0.85rem; border-radius: 10px; }
    .quote-card-inner { gap: 10px; }
    .quote-actions { gap: 4px; }
    .quote-btn { width: 30px; height: 30px; font-size: 0.65rem; border-radius: 8px; }
    .quote-glow { width: 120px; height: 120px; right: -30px; bottom: -40px; }
}

/* ── Responsive ────────────────────────── */
@media (max-width: 768px) {
    .sidebar { position: fixed; left: -260px; top: 0; height: 100%; width: 260px; }
    .sidebar.collapsed { width: 260px; left: -260px; }
    .sidebar.open { left: 0; }
    .main-content { padding: 16px; }
    .profile-hero { flex-direction: column; text-align: center; padding: 24px; gap: 16px; }
    .profile-hero-title { font-size: 1.3rem; }
    .form-card { padding: 20px; }
    .profile-avatar { width: 64px; height: 64px; font-size: 1.7rem; }
    .avatar-edit-overlay { font-size: 1.1rem; }
    .top-bar { margin-bottom: 18px; padding-bottom: 12px; }
}
@media (max-width: 480px) {
    .main-content { padding: 10px; }
    .profile-hero { padding: 18px; }
    .profile-hero-title { font-size: 1.1rem; }
    .form-card { padding: 16px; border-radius: 18px; }
    .form-card-header { margin-bottom: 16px; padding-bottom: 12px; gap: 10px; }
    .form-card-icon { width: 36px; height: 36px; font-size: 0.9rem; border-radius: 11px; }
    .form-card-title { font-size: 0.9rem; }
    .form-group { margin-bottom: 14px; }
    .form-control { padding: 9px 14px; font-size: 0.82rem; }
    .btn { padding: 9px 18px; font-size: 0.8rem; }
    .profile-avatar { width: 56px; height: 56px; font-size: 1.4rem; }
    .profile-avatar .avatar-badge { width: 22px; height: 22px; font-size: 0.55rem; border-width: 2px; }
    .avatar-edit-overlay { font-size: 0.9rem; }
    .info-banner { padding: 10px 14px; }
    .info-banner p { font-size: 0.75rem; }
    .avatar-float-menu {
        width: 290px;
    }
}

/* ── Avatar Picker Modal ───────────────── */
.avatar-picker-item {
    width: 100%; aspect-ratio: 1; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; color: #fff; cursor: pointer;
    border: 3px solid transparent; transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.avatar-picker-item:hover {
    transform: scale(1.12);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}
.avatar-picker-item.selected {
    border-color: var(--accent-gold);
    box-shadow: 0 0 0 3px var(--accent-gold), 0 6px 20px rgba(240,180,41,0.3);
    transform: scale(1.08);
}
.avatar-picker-item .check-badge {
    position: absolute; bottom: -2px; right: -2px;
    width: 22px; height: 22px; border-radius: 50%;
    background: var(--accent-gold);
    border: 2px solid var(--white);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.55rem; color: #1e1a0c;
    opacity: 0; transform: scale(0.5); transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.avatar-picker-item.selected .check-badge {
    opacity: 1; transform: scale(1);
}
.avatar-picker-label {
    text-align: center; font-size: 0.6rem; color: var(--text-muted);
    margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* ── Transition for page load ──────────── */
.fade-in-page { animation: fadeInUp 0.5s ease both; }
/* fadeInUp keyframes defined in layout */

/* ── Floating Premium Modal ── */
.modal-floating {
    display: none;
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    z-index: 1000;
    align-items: center; justify-content: center;
}
.modal-floating.show {
    display: flex;
}
.modal-floating-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    animation: overlayFadeIn 0.35s ease both;
    z-index: -1;
}
@keyframes overlayFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.modal-floating-card {
    position: relative;
    background: var(--modal-bg);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid var(--glass-border);
    border-radius: 28px;
    padding: 0;
    max-width: 440px;
    width: 92%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 32px 80px rgba(0,0,0,0.35), 0 0 0 1px rgba(240,180,41,0.06);
    animation: modalFloatIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
    transform-origin: center bottom;
}
.modal-floating-card.closing {
    animation: modalFloatOut 0.3s cubic-bezier(0.55, 0, 1, 0.45) both;
}
@keyframes modalFloatIn {
    0% { opacity: 0; transform: translateY(40px) scale(0.96); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes modalFloatOut {
    0% { opacity: 1; transform: translateY(0) scale(1); }
    100% { opacity: 0; transform: translateY(30px) scale(0.97); }
}

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
@keyframes particleFloat {
    0%, 100% { transform: translateY(0) translateX(0) scale(1); }
    25% { transform: translateY(-12px) translateX(6px) scale(1.05); }
    50% { transform: translateY(-6px) translateX(-4px) scale(0.95); }
    75% { transform: translateY(-18px) translateX(8px) scale(1.02); }
}

.floating-accent-bar {
    position: relative; z-index: 1;
    height: 3px; width: 100%;
    background: linear-gradient(90deg, #d4940a, #f0b429, #ff8c42, #f0b429, #d4940a);
    background-size: 200% 100%;
    animation: accentBarShimmer 3s ease-in-out infinite;
    border-radius: 28px 28px 0 0;
}
@keyframes accentBarShimmer {
    0% { background-position: 0% 0%; }
    50% { background-position: 100% 0%; }
    100% { background-position: 0% 0%; }
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
@keyframes iconPulse {
    0%, 100% { box-shadow: 0 4px 16px rgba(240,180,41,0.35); }
    50% { box-shadow: 0 4px 28px rgba(240,180,41,0.6); }
}
.floating-title {
    font-size: 1.15rem; font-weight: 800; color: var(--text-primary);
    margin: 2px 0 2px; line-height: 1.2;
}
.floating-subtitle {
    font-size: 0.72rem; color: var(--text-muted); font-weight: 500;
    margin: 0;
}
.floating-close {
    margin-left: auto; flex-shrink: 0;
    width: 34px; height: 34px; border-radius: 12px;
    border: 1px solid var(--glass-border);
    background: var(--glass-bg); color: var(--text-muted);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem; transition: all 0.2s;
}
.floating-close:hover {
    background: rgba(239,68,68,0.12);
    border-color: rgba(239,68,68,0.3);
    color: #ef4444;
    transform: rotate(90deg);
}

/* Avatar grid inside floating modal */
.floating-body {
    position: relative; z-index: 1;
    padding: 0 28px 24px;
}
.floating-avatar-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.floating-desc {
    font-size: 0.75rem; color: var(--text-muted);
    margin-bottom: 16px; line-height: 1.5;
}

/* Actions inside floating modal */
.floating-actions {
    display: flex; gap: 10px;
    animation: fieldSlideUp 0.4s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: calc(var(--i, 1) * 0.07s);
}
.floating-btn {
    padding: 11px 18px; border-radius: 14px; font-weight: 700; font-size: 0.82rem;
    cursor: pointer; transition: all 0.25s; border: none;
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    text-decoration: none; flex: 1;
}
.floating-btn-primary {
    background: linear-gradient(90deg, #d4940a, #e07020);
    color: #1e1a0c;
    box-shadow: 0 4px 14px rgba(240,180,41,0.3);
    position: relative; overflow: hidden;
}
.floating-btn-primary::before {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
    transform: translateX(-100%);
    transition: transform 0.5s ease;
}
.floating-btn-primary:hover::before { transform: translateX(100%); }
.floating-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(240,180,41,0.4);
}
.floating-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }
.floating-btn-secondary {
    background: transparent; border: 1px solid var(--glass-border);
    color: var(--text-muted);
}
.floating-btn-secondary:hover {
    background: rgba(240,180,41,0.08);
    border-color: rgba(240,180,41,0.3);
    color: var(--accent-gold);
}

@keyframes fieldSlideUp {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Scrollbar */
.modal-floating-card::-webkit-scrollbar { width: 4px; }
.modal-floating-card::-webkit-scrollbar-track { background: transparent; }
.modal-floating-card::-webkit-scrollbar-thumb { background: var(--glass-border); border-radius: 4px; }

@media (max-width: 480px) {
    .floating-header { padding: 18px 18px 12px; }
    .floating-body { padding: 0 18px 18px; }
    .floating-title { font-size: 1rem; }
    .floating-icon-wrap { width: 40px; height: 40px; font-size: 1.1rem; border-radius: 13px; }
    .floating-btn { padding: 9px 14px; font-size: 0.78rem; border-radius: 12px; }
    .modal-floating-card { border-radius: 22px; }
    .floating-avatar-grid { gap: 8px; }
}

</style>
@endpush

@section('content')
<div class="app-container">
@include('layouts.sidebar', ['active' => 'profile'])

    <main class="main-content">
@include('layouts.topbar')
        <!-- Hero -->
        <div class="profile-hero fade-in-page">
            <div class="avatar-wrap">
                <div class="profile-avatar" id="profileAvatarDisplay" onclick="openAvatarModal()" style="cursor: pointer;">
                    <i class="fas fa-user" id="avatarPreviewIcon"></i>
                    <div class="avatar-edit-overlay"><i class="fas fa-camera"></i></div>
                    <div class="avatar-badge"><i class="fas fa-check"></i></div>
                </div>
            </div>
            <div class="profile-hero-body">
                <h1 class="profile-hero-title"><i class="fas fa-user-cog"></i> Pengaturan Profil</h1>
                <p class="profile-hero-sub"><i class="fas fa-circle" style="font-size: 0.4rem; color: var(--accent-gold); vertical-align: middle; margin-right: 6px;"></i>Kelola data diri dan keamanan akun Anda</p>
            </div>
            <div class="profile-hero-glow"></div>

        </div>

        <!-- Quote Card -->
        <div class="quote-card fade-in-page" id="quoteCard">
            <div class="quote-shimmer"></div>
            <div class="quote-shimmer-2"></div>
            <div class="quote-dot d1"></div>
            <div class="quote-dot d2"></div>
            <div class="quote-dot d3"></div>
            <div class="quote-card-inner">
                <div class="quote-icon"><i class="fas fa-quote-right"></i></div>
                <div class="quote-content">
                    <p class="quote-text" id="quoteDisplay">
                        <span id="quoteTextSpan"></span>
                        <span class="editable-hint"><i class="fas fa-pencil-alt" style="font-size:0.6rem;margin-right:3px;"></i>klik untuk edit</span>
                    </p>
                    <input type="text" class="quote-text-input" id="quoteInput"
                        placeholder="Tulis quotes motivasi..." maxlength="255">
                </div>
                <div class="quote-actions">
                    <button type="button" class="quote-btn save-btn" id="quoteSaveBtn" onclick="saveQuote()" title="Simpan">
                        <i class="fas fa-check"></i>
                    </button>
                    <button type="button" class="quote-btn cancel-btn" id="quoteCancelBtn" onclick="cancelEditQuote()" title="Batal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="quote-glow"></div>
        </div>

        <!-- Profile Form Card -->
        <div class="form-card fade-in-page">
            <div class="form-card-header">
                <div class="form-card-icon profile-icon"><i class="fas fa-id-card"></i></div>
                <div>
                    <div class="form-card-title">Data Profil</div>
                    <div class="form-card-subtitle">Informasi dasar akun Anda</div>
                </div>
            </div>
            <div class="form-card-body">
                <form id="profileForm">
                    @csrf
                    <div class="form-group">
                        <label for="profileFullName">Nama Lengkap <span style="color: var(--danger);">*</span></label>
                        <input type="text" class="form-control" id="profileFullName" required placeholder="Masukkan nama lengkap" autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label for="profileUsername">Username <span style="color: var(--danger);">*</span></label>
                        <input type="text" class="form-control" id="profileUsername" required minlength="3" placeholder="Minimal 3 karakter" autocomplete="username">
                        <div class="form-hint"><i class="fas fa-info-circle" style="font-size: 0.55rem; margin-right: 3px;"></i> Username harus unik, minimal 3 karakter</div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="saveProfileBtn">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <!-- Password Change Card -->
        <div class="form-card fade-in-page">
            <div class="form-card-header">
                <div class="form-card-icon lock-icon"><i class="fas fa-lock"></i></div>
                <div>
                    <div class="form-card-title">Ganti Password</div>
                    <div class="form-card-subtitle">Pastikan password Anda aman dan tidak mudah ditebak</div>
                </div>
            </div>
            <div class="form-card-body">
                <form id="passwordForm">
                    @csrf
                    <div class="form-group">
                        <label for="currentPassword">Password Lama <span style="color: var(--danger);">*</span></label>
                        <input type="password" class="form-control" id="currentPassword" required placeholder="Masukkan password lama" autocomplete="current-password">
                    </div>
                    <div class="form-group">
                        <label for="newPassword">Password Baru <span style="color: var(--danger);">*</span></label>
                        <input type="password" class="form-control" id="newPassword" required minlength="8" placeholder="Minimal 8 karakter" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Konfirmasi Password Baru <span style="color: var(--danger);">*</span></label>
                        <input type="password" class="form-control" id="confirmPassword" required placeholder="Ulangi password baru" autocomplete="new-password">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap; font-size: 0.7rem; color: var(--text-muted);">
                            <span><i class="fas fa-circle" style="font-size: 0.4rem; color: var(--success);"></i> Minimal 8 karakter</span>
                            <span><i class="fas fa-circle" style="font-size: 0.4rem; color: var(--accent-gold);"></i> Gunakan kombinasi huruf & angka</span>
                        </div>
                    </div>
                    <div style="margin-top: 18px;">
                        <button type="submit" class="btn btn-primary" id="savePasswordBtn">
                            <i class="fas fa-key"></i> Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Avatar Picker — Premium Floating Modal -->
<div id="avatarModal" class="modal modal-floating">
    <div class="modal-floating-overlay" onclick="closeAvatarModal()"></div>
    <div class="modal-floating-card" id="floatingAvatarCard">
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
                <i class="fas fa-images"></i>
            </div>
            <div>
                <h3 class="floating-title">Pilih Foto Profil</h3>
                <p class="floating-subtitle">Pilih avatar yang kamu suka</p>
            </div>
            <button type="button" class="floating-close" onclick="closeAvatarModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="floating-body">
            <p class="floating-desc">Pilih avatar favoritmu untuk ditampilkan sebagai foto profil.</p>
            
            <div class="floating-avatar-grid" id="avatarGrid">
                <!-- Avatars will be rendered by JS -->
            </div>

            <div class="floating-actions" style="--i:1;">
                <button type="button" class="floating-btn floating-btn-primary" onclick="saveAvatar()" id="saveAvatarBtn">
                    <i class="fas fa-check"></i>
                    <span>Simpan</span>
                </button>
                <button type="button" class="floating-btn floating-btn-secondary" onclick="closeAvatarModal()">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
        <footer class="app-footer">
            <span>&copy; 2026.</span>
            <span class="footer-heart">&nbsp;Made with <i class="fas fa-heart"></i> and coffee, dedicated to be useful.</span>
        </footer>
    </main>
</div>
@endsection

@push('scripts')
<script>
window.DOMpetConfig = {
    apiBase: '{{ url("/api") }}',
    logoutUrl: '{{ url("/logout") }}',
};

var currentUser = null;
var API_BASE = window.DOMpetConfig.apiBase;
var PASSWORD_MIN_LENGTH = 8;

// ── Utilities ─────────────────────────────

function showToast(message, type) {
    type = type || 'success';
    const iconMap = { success: 'success', error: 'error', info: 'info' };
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: iconMap[type] || 'success', title: message, toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, background: '#1a1a2e', color: '#fff' });
        return;
    }
    console.log('[' + type + '] ' + message);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(amount);
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ── API ───────────────────────────────────

async function apiRequest(endpoint, method, data, options) {
    method = method || 'GET';
    options = options || {};
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta || !csrfMeta.content) throw new Error('CSRF token tidak ditemukan.');
    var fetchOptions = {
        method: method, credentials: 'include',
        headers: { 'X-CSRF-TOKEN': csrfMeta.content, 'Accept': 'application/json' }
    };
    if (data && (method === 'POST' || method === 'PUT' || method === 'DELETE')) {
        var formData = new URLSearchParams();
        for (var key in data) formData.append(key, data[key]);
        if (method === 'PUT' || method === 'DELETE') { formData.append('_method', method); fetchOptions.method = 'POST'; }
        fetchOptions.body = formData.toString();
        fetchOptions.headers['Content-Type'] = 'application/x-www-form-urlencoded';
    }
    try {
        var response = await fetch(API_BASE + '/' + endpoint, fetchOptions);
        if (response.status === 401) { localStorage.removeItem('user'); window.location.href = '/login'; throw new Error('Unauthorized'); }
        var contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) throw new Error('Non-JSON response');
        var result = await response.json();
        if (!response.ok) throw new Error(result.error || result.message || 'Request failed');
        return result;
    } catch (error) {
        console.error('API Error:', error);
        if (!options.silent) showToast(error.message, 'error');
        throw error;
    }
}

async function checkAuth() {
    try {
        var data = await apiRequest('auth/check', 'GET', null, { silent: true });
        if (!data.authenticated) { localStorage.removeItem('user'); window.location.href = '/login'; return null; }
        localStorage.setItem('user', JSON.stringify(data.user));
        return data.user;
    } catch (error) { localStorage.removeItem('user'); window.location.href = '/login'; return null; }
}

async function logout() {
    try {
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        await fetch(window.DOMpetConfig.logoutUrl, { method: 'POST', credentials: 'include', headers: { 'X-CSRF-TOKEN': (csrfMeta && csrfMeta.content) || '' } });
    } catch (e) {}
    localStorage.removeItem('user');
    showToast('Logout berhasil', 'success');
    setTimeout(function() { window.location.href = '/login'; }, 500);
}

// ── Quote ──────────────────────────────────
var quoteOriginalText = '';

function loadQuote(quote) {
    var span = document.getElementById('quoteTextSpan');
    var input = document.getElementById('quoteInput');
    if (!span) return;
    if (input) {
        input.value = quote || '';
        input.placeholder = quote ? '' : 'Tulis quotes motivasi\u2026';
    }
    quoteOriginalText = quote || '';

    if (!quote) {
        span.innerHTML = '<span style="opacity:0.4;font-style:italic;">Tambahkan quotes motivasimu\u2026</span>';
        return;
    }

    var fullText = '\u201c' + escapeHtml(quote) + '\u201d';
    span.innerHTML = '';
    var idx = 0;
    var cursorSpan = document.createElement('span');
    cursorSpan.className = 'quote-typing-cursor';
    span.appendChild(cursorSpan);

    function typeChar() {
        if (idx < fullText.length) {
            span.insertBefore(document.createTextNode(fullText.charAt(idx)), cursorSpan);
            idx++;
            var delay = fullText.charAt(idx - 1) === ' ' ? 40 : 30;
            setTimeout(typeChar, delay);
        } else {
            cursorSpan.remove();
        }
    }
    setTimeout(typeChar, 300);
}

function editQuote() {
    var card = document.getElementById('quoteCard');
    var input = document.getElementById('quoteInput');
    if (!card || !input) return;
    card.classList.add('editing');
    input.focus();
    input.select();
}

function cancelEditQuote() {
    var card = document.getElementById('quoteCard');
    var input = document.getElementById('quoteInput');
    if (!card || !input) return;
    input.value = quoteOriginalText;
    card.classList.remove('editing');
}

async function saveQuote() {
    var card = document.getElementById('quoteCard');
    var input = document.getElementById('quoteInput');
    if (!card || !input) return;
    var newQuote = input.value.trim();
    try {
        await apiRequest('user/quote', 'PUT', { quote: newQuote });
        quoteOriginalText = newQuote;
        loadQuote(newQuote);
        if (currentUser) currentUser.quote = newQuote;
        card.classList.remove('editing');
        showToast('Quote berhasil diperbarui!', 'success');
    } catch (e) {
        console.error(e);
        input.value = quoteOriginalText;
        card.classList.remove('editing');
    }
}

document.addEventListener('click', function(e) {
    var card = document.getElementById('quoteCard');
    if (!card || !card.classList.contains('editing')) return;
    var display = document.getElementById('quoteDisplay');
    var input = document.getElementById('quoteInput');
    var saveBtn = document.getElementById('quoteSaveBtn');
    var cancelBtn = document.getElementById('quoteCancelBtn');
    if (!display || !input || !saveBtn || !cancelBtn) return;
    var isInside = card.contains(e.target);
    var isEditingAction = saveBtn.contains(e.target) || cancelBtn.contains(e.target);
    if (!isInside && !isEditingAction) cancelEditQuote();
});

// ── Avatar Presets ─────────────────────────
var selectedAvatarIndex = 0;

function renderAvatarPreview(index) {
    var avatar = window.AVATARS[index] || window.AVATARS[0];
    var iconEl = document.getElementById('avatarPreviewIcon');
    var container = document.getElementById('profileAvatarDisplay');
    if (iconEl) {
        iconEl.className = 'fas ' + avatar.icon;
    }
    if (container) {
        container.style.background = avatar.bg;
    }
    // Also update top bar avatar
    renderUserAvatar(index);
}

function openAvatarModal() {
    var el = document.getElementById('avatarModal');
    var card = document.getElementById('floatingAvatarCard');
    if (!el || !card) return;
    
    card.classList.remove('closing');
    el.classList.add('show');
    
    // Render avatar grid
    selectedAvatarIndex = currentUser && currentUser.avatar !== undefined ? parseInt(currentUser.avatar) : 0;
    var grid = document.getElementById('avatarGrid');
    if (!grid) return;
    grid.innerHTML = AVATARS.map(function(a, i) {
        var sel = i === selectedAvatarIndex ? 'selected' : '';
        return '<div style="text-align:center;">' +
            '<div class="avatar-picker-item ' + sel + '" ' +
            'style="background:' + a.bg + ';" ' +
            'onclick="selectAvatar(' + i + ')" data-index="' + i + '">' +
            '<i class="fas ' + a.icon + '"></i>' +
            '<div class="check-badge"><i class="fas fa-check"></i></div>' +
            '</div>' +
            '<div class="avatar-picker-label">' + a.label + '</div>' +
            '</div>';
    }).join('');
    
    // Re-trigger stagger animations
    setTimeout(function() {
        var fields = card.querySelectorAll('.floating-header, .floating-body, .floating-actions');
        fields.forEach(function(f) {
            f.style.animation = 'none';
            void f.offsetWidth;
            f.style.animation = '';
        });
    }, 50);
}

function closeAvatarModal() {
    var el = document.getElementById('avatarModal');
    var card = document.getElementById('floatingAvatarCard');
    if (!el) return;
    if (card) {
        card.classList.add('closing');
        setTimeout(function() {
            el.classList.remove('show');
            card.classList.remove('closing');
        }, 280);
    } else {
        el.classList.remove('show');
    }
}

function selectAvatar(index) {
    selectedAvatarIndex = index;
    document.querySelectorAll('.avatar-picker-item').forEach(function(el) {
        el.classList.toggle('selected', parseInt(el.dataset.index) === index);
    });
}

async function saveAvatar() {
    var btn = document.getElementById('saveAvatarBtn');
    var originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    btn.disabled = true;
    try {
        await apiRequest('user/avatar', 'PUT', { avatar: selectedAvatarIndex });
        if (currentUser) {
            currentUser.avatar = selectedAvatarIndex;
            localStorage.setItem('user', JSON.stringify(currentUser));
        }
        renderAvatarPreview(selectedAvatarIndex);
        showToast('Foto profil berhasil diperbarui!', 'success');
        closeAvatarModal();
    } catch (error) {
        console.error(error);
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

// ── Load Profile ──────────────────────────

function loadProfile() {
    if (!currentUser) return;
    var fullNameEl = document.getElementById('profileFullName');
    var usernameEl = document.getElementById('profileUsername');
    var sidebarNameEl = document.getElementById('sidebarUserName');
    var userNameEl = document.getElementById('userName');
    if (fullNameEl) fullNameEl.value = currentUser.full_name || '';
    if (usernameEl) usernameEl.value = currentUser.username || '';
    if (sidebarNameEl) sidebarNameEl.textContent = currentUser.full_name || currentUser.username || 'User';
    if (userNameEl) userNameEl.textContent = currentUser.full_name || currentUser.username || 'User';

    // Render avatar
    renderAvatarPreview(currentUser.avatar || 0);

    // Load quote
    loadQuote(currentUser.quote || '');

    // Clear password fields
    ['currentPassword', 'newPassword', 'confirmPassword'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.value = '';
    });
}

// ── Sidebar ───────────────────────────────

function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;
    if (window.innerWidth <= 768) { sidebar.classList.toggle('open'); if (overlay) overlay.classList.toggle('show', sidebar.classList.contains('open')); }
    else { sidebar.classList.toggle('collapsed'); }
}
function closeSidebar() {
    document.getElementById('sidebar')?.classList.remove('open');
    document.getElementById('sidebarOverlay')?.classList.remove('show');
}
document.addEventListener('click', function(event) {
    if (window.innerWidth > 768) return;
    var sidebar = document.getElementById('sidebar');
    var menuToggle = document.querySelector('.menu-toggle');
    if (sidebar && sidebar.classList.contains('open')) { if (!sidebar.contains(event.target) && (!menuToggle || !menuToggle.contains(event.target))) closeSidebar(); }
});

// ── Dark Mode ─────────────────────────────

function loadThemePreference() {
    var savedTheme = localStorage.getItem('darkMode');
    var isDark = savedTheme !== 'disabled';
    if (isDark) document.body.classList.add('dark-mode'); else document.body.classList.remove('dark-mode');
    var darkModeBtn = document.getElementById('darkModeToggle');
    if (darkModeBtn) darkModeBtn.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
}

function toggleDarkMode() {
    var isDark = !document.body.classList.contains('dark-mode');
    document.body.classList.toggle('dark-mode', isDark);
    localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
    var darkModeBtn = document.getElementById('darkModeToggle');
    if (darkModeBtn) darkModeBtn.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
}

// ── Init ──────────────────────────────────

document.addEventListener('DOMContentLoaded', async function() {
    loadThemePreference();
    var darkModeBtn = document.getElementById('darkModeToggle');
    if (darkModeBtn) darkModeBtn.addEventListener('click', toggleDarkMode);

    currentUser = await checkAuth();
    if (!currentUser) return;
    loadProfile();

    // ── Quote Click-to-Edit ────────────
    var quoteDisplay = document.getElementById('quoteDisplay');
    if (quoteDisplay) {
        quoteDisplay.addEventListener('click', editQuote);
    }

    // ── Profile Form Submit ──────────────
    var profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            var fullName = document.getElementById('profileFullName');
            if (!fullName || !fullName.value.trim()) { showToast('Nama lengkap wajib diisi', 'error'); return; }
            var btn = document.getElementById('saveProfileBtn');
            var originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            btn.disabled = true;
            try {
                var data = { full_name: fullName.value.trim() };
                var usernameInput = document.getElementById('profileUsername');
                if (usernameInput && usernameInput.value.trim()) data.username = usernameInput.value.trim();
                await apiRequest('user/profile', 'PUT', data);
                if (currentUser) currentUser.full_name = fullName.value.trim();
                var sidebarNameEl = document.getElementById('sidebarUserName');
                var userNameEl = document.getElementById('userName');
                if (sidebarNameEl) sidebarNameEl.textContent = fullName.value.trim();
                if (userNameEl) userNameEl.textContent = fullName.value.trim();
                showToast('Profil berhasil diperbarui', 'success');
            } catch (error) { console.error(error); }
            finally { btn.innerHTML = originalText; btn.disabled = false; }
        });
    }

    // ── Password Form Submit ─────────────
    var passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            var currentPw = document.getElementById('currentPassword');
            var newPw = document.getElementById('newPassword');
            var confirmPw = document.getElementById('confirmPassword');
            if (!currentPw.value) { showToast('Password lama wajib diisi', 'error'); return; }
            if (!newPw.value || newPw.value.length < PASSWORD_MIN_LENGTH) { showToast('Password baru minimal ' + PASSWORD_MIN_LENGTH + ' karakter', 'error'); return; }
            if (newPw.value !== confirmPw.value) { showToast('Konfirmasi password tidak cocok', 'error'); return; }
            var btn = document.getElementById('savePasswordBtn');
            var originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengubah...';
            btn.disabled = true;
            try {
                await apiRequest('user/password', 'PUT', { current_password: currentPw.value, new_password: newPw.value });
                showToast('Password berhasil diubah', 'success');
                currentPw.value = ''; newPw.value = ''; confirmPw.value = '';
            } catch (error) { console.error(error); }
            finally { btn.innerHTML = originalText; btn.disabled = false; }
        });
    }
});
</script>
@endpush
