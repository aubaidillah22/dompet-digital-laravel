@extends('layouts.app')

@section('title', 'Dompet Utama - Dompet Digital')

@push('styles')
<style>
/* ── Dompet-specific styles ────────────── */
.stat-value { font-size: 1.5rem; }

/* Type Toggle */
.type-toggle {
    display: flex; gap: 8px;
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 16px; padding: 5px;
}
.type-option {
    flex: 1; display: flex; align-items: center; justify-content: center; gap: 7px;
    padding: 9px 12px; border-radius: 12px; border: none;
    background: transparent; color: var(--text-muted);
    font-size: 0.8rem; font-weight: 600; cursor: pointer;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.type-option i { font-size: 0.85rem; transition: transform 0.3s; }
.type-option:hover { color: var(--text-secondary); }
.type-option.type-income.active {
    background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05));
    color: #10b981; box-shadow: 0 2px 8px rgba(16,185,129,0.15);
}
.type-option.type-income.active i { transform: translateY(-2px); }
.type-option.type-expense.active {
    background: linear-gradient(135deg, rgba(239,68,68,0.15), rgba(239,68,68,0.05));
    color: #ef4444; box-shadow: 0 2px 8px rgba(239,68,68,0.15);
}
.type-option.type-expense.active i { transform: translateY(2px); }

/* Filter Bar */
.filter-bar-card {
    margin-bottom: 28px;
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
.filter-bar-top {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 12px;
}
.filter-period-label {
    display: flex; align-items: center; gap: 8px;
    font-size: 0.85rem; font-weight: 700;
    color: var(--text-primary); transition: all 0.3s;
}
.filter-period-label i { color: var(--accent-gold); }
.filter-period-label.label-update { animation: labelPop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); }
@keyframes labelPop { 0% { transform: scale(1); } 50% { transform: scale(1.06); color: var(--accent-gold); } 100% { transform: scale(1); } }
.filter-actions { display: flex; gap: 8px; }
.filter-actions .btn-sm { padding: 6px 12px; min-width: 34px; justify-content: center; }
.filter-bar-presets { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.filter-chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 14px; border: 1px solid var(--glass-border);
    border-radius: 40px; background: var(--glass-bg);
    color: var(--text-secondary); font-size: 0.78rem; font-weight: 600;
    cursor: pointer; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); white-space: nowrap;
}
.filter-chip i { font-size: 0.7rem; transition: transform 0.25s; }
.filter-chip:hover { background: rgba(240,180,41,0.12); border-color: var(--accent-gold); color: var(--accent-gold); transform: translateY(-1px); }
.filter-chip:hover i { transform: scale(1.15); }
.filter-chip.active { background: linear-gradient(90deg, var(--accent-gold), var(--accent-orange)); color: #1e1a0c; border-color: transparent; box-shadow: 0 2px 12px rgba(240,180,41,0.3); }
.filter-chip.active i { color: inherit; }
.filter-separator { width: 1px; height: 24px; background: var(--glass-border); margin: 0 4px; }
.filter-month-wrap { display: flex; align-items: center; gap: 6px; }
.filter-month-input {
    padding: 5px 10px; background: var(--input-bg);
    border: 1px solid var(--input-border); border-radius: 40px;
    color: var(--text-primary); font-size: 0.78rem; outline: none; cursor: pointer;
    transition: all 0.2s; width: 150px;
}
.filter-month-input:focus { border-color: var(--accent-gold); box-shadow: 0 0 0 3px rgba(240,180,41,0.15); }
.filter-month-input::-webkit-calendar-picker-indicator { cursor: pointer; }
body.dark-mode .filter-month-input::-webkit-calendar-picker-indicator { filter: invert(1); }

/* Floating input glow (not in app.css) */
.floating-input-glow {
    position: absolute; bottom: -1px; left: 50%; transform: translateX(-50%);
    width: 0; height: 2px;
    background: linear-gradient(90deg, transparent, var(--accent-gold), transparent);
    border-radius: 2px;
    transition: width 0.3s ease;
    pointer-events: none;
}
.floating-input:focus ~ .floating-input-glow { width: 80%; }

/* Form inside floating modal */
.modal-floating form { position: relative; z-index: 1; padding: 0 28px 24px; }

/* Export button variants */
.floating-btn-excel {
    width: 100%; padding: 14px 20px; border-radius: 14px; font-weight: 700;
    background: linear-gradient(90deg, #059669, #10b981);
    color: white; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    font-size: 0.88rem; transition: all 0.25s; box-shadow: 0 4px 14px rgba(16,185,129,0.3);
}
.floating-btn-excel:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16,185,129,0.45);
}
.floating-btn-pdf {
    width: 100%; padding: 14px 20px; border-radius: 14px; font-weight: 700;
    background: linear-gradient(90deg, #dc2626, #ef4444);
    color: white; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    font-size: 0.88rem; transition: all 0.25s; box-shadow: 0 4px 14px rgba(239,68,68,0.3);
}
.floating-btn-pdf:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239,68,68,0.45);
}
.floating-btn-outline {
    width: 100%; padding: 12px 20px; border-radius: 14px; font-weight: 600;
    background: transparent; border: 1px solid var(--glass-border);
    color: var(--text-muted); cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    font-size: 0.85rem; transition: all 0.25s;
}
.floating-btn-outline:hover {
    background: rgba(240,180,41,0.08);
    border-color: rgba(240,180,41,0.3);
    color: var(--accent-gold);
}

/* Hero Section (dompet-specific) */
.hero-section {
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 28px;
    padding: 32px;
    background: linear-gradient(135deg, rgba(240,180,41,0.08), rgba(255,140,66,0.03));
    border: 1px solid rgba(240,180,41,0.15);
    border-radius: 28px;
    position: relative;
    overflow: hidden;
}
.hero-section::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #d4940a, #e07020, #d4940a);
}
.hero-icon {
    width: 72px; height: 72px;
    border-radius: 24px;
    background: linear-gradient(135deg, #d4940a, #e07020);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem; color: #1e1a0c;
    flex-shrink: 0;
    box-shadow: 0 8px 24px rgba(240,180,41,0.3);
}
.hero-body { flex: 1; }
.hero-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 2px; }
.hero-amount { font-size: 2.4rem; font-weight: 900; letter-spacing: -0.03em; color: #d4940a; line-height: 1.2; }
body.dark-mode .hero-amount { color: #f0b429; }
.hero-sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }
.hero-glow {
    position: absolute; width: 300px; height: 300px;
    border-radius: 50%; right: -60px; top: -60px;
    background: radial-gradient(circle, rgba(240,180,41,0.1) 0%, transparent 70%);
    pointer-events: none;
}

/* Page-specific responsive overrides */
@media (max-width: 768px) {
    .filter-chip { padding: 5px 10px; font-size: 0.72rem; gap: 4px; }
    .filter-month-input { width: 130px; }
}
@media (max-width: 480px) {
    .stat-glow { display: none; }
    .filter-bar-card { padding: 10px 12px; margin-bottom: 18px; }
    .filter-bar-top { margin-bottom: 8px; }
    .filter-period-label { font-size: 0.72rem; }
    .filter-chip { padding: 4px 8px; font-size: 0.65rem; }
    .filter-chip i { font-size: 0.55rem; }
    .filter-month-input { width: 110px; font-size: 0.7rem; padding: 4px 8px; }
    .filter-actions .btn-sm { padding: 4px 8px; min-width: 30px; }
    .filter-separator { height: 18px; margin: 0 2px; }
    .grid-2 { gap: 12px; }
    .chart-canvas-wrap { min-height: 180px; }
    .chart-canvas-wrap canvas { max-height: 200px; }
    .chart-card-header { margin-bottom: 12px; padding-bottom: 8px; gap: 10px; }
    .chart-card-icon { width: 32px; height: 32px; font-size: 0.85rem; border-radius: 9px; }
    .chart-card-title { font-size: 0.78rem; }
    .chart-card-subtitle { font-size: 0.63rem; }
    .table-container th:nth-child(5),
    .table-container td:nth-child(5) { display: none; }
}
@media (max-width: 576px) {
    .hero-section { flex-direction: column; text-align: center; padding: 24px; gap: 16px; }
    .hero-amount { font-size: 1.8rem; }
}
@media (max-width: 400px) {
    .hero-section { padding: 18px; }
    .hero-icon { width: 56px; height: 56px; font-size: 1.4rem; border-radius: 18px; }
    .hero-amount { font-size: 1.5rem; }
}
</style>
.btn {
    padding: 10px 18px; border-radius: 40px; font-weight: 600; font-size: 0.8rem;
    cursor: pointer; transition: all 0.2s; border: none;
    display: inline-flex; align-items: center; gap: 8px;
}
.btn-primary { background: linear-gradient(90deg, var(--accent-gold), var(--accent-orange)); color: #1e1a0c; }
.btn-primary:hover { transform: scale(1.02); opacity: 0.95; }
.btn-outline { background: transparent; border: 1px solid var(--glass-border); color: var(--text-secondary); }
.btn-outline:hover { background: rgba(240,180,41,0.15); border-color: var(--accent-gold); color: var(--accent-gold); }
.btn-danger { background: linear-gradient(90deg, #dc2626, #ef4444); color: white; }
.btn-green { background: linear-gradient(90deg, #10b981, #34d399); color: white; }
.btn-green:hover { transform: scale(1.02); opacity: 0.95; }
.btn-sm { padding: 6px 12px; font-size: 0.7rem; }
.table-container { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid var(--glass-border); }
th { color: var(--text-muted); font-weight: 600; font-size: 0.75rem; letter-spacing: 0.5px; }
td { color: var(--text-secondary); font-size: 0.8rem; }
.badge-income, .badge-expense { padding: 4px 10px; border-radius: 40px; font-size: 0.7rem; font-weight: 600; display: inline-block; }
.badge-income { background: rgba(16,185,129,0.15); color: #34d399; }
.badge-expense { background: rgba(239,68,68,0.15); color: #fca5a5; }
.text-success { color: #34d399; font-weight: 600; }
.text-danger { color: #fca5a5; font-weight: 600; }
.form-control, select.form-control {
    padding: 10px 16px; background: var(--input-bg); border: 1px solid var(--input-border);
    border-radius: 40px; color: var(--text-primary); font-size: 0.85rem; outline: none;
    transition: all 0.2s; width: 100%;
}
.form-control:focus { border-color: var(--accent-gold); box-shadow: 0 0 0 3px rgba(240,180,41,0.15); }
.form-control::placeholder { color: var(--text-muted); }
select option { background: var(--white); color: var(--text-primary); }
textarea.form-control { border-radius: 20px; resize: vertical; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 6px; }
.modal {
    display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 1000;
    align-items: center; justify-content: center;
}
.modal-content {
    background: var(--modal-bg); backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border); border-radius: 24px;
    padding: 28px; max-width: 500px; width: 90%;
}
.modal-content h3 { margin-bottom: 20px; color: var(--accent-gold); }

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
    max-width: 520px;
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

/* Form Fields */
form { position: relative; z-index: 1; padding: 0 28px 24px; }
.floating-field {
    animation: fieldSlideUp 0.4s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: calc(var(--i, 1) * 0.07s);
    margin-bottom: 18px;
}
@keyframes fieldSlideUp {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}
.floating-label {
    display: flex; align-items: center; gap: 6px;
    font-size: 0.75rem; font-weight: 600; color: var(--text-secondary);
    margin-bottom: 6px;
}
.floating-label i { font-size: 0.7rem; color: var(--accent-gold); }
.floating-optional { font-weight: 400; color: var(--text-muted); font-size: 0.7rem; }
.floating-input {
    width: 100%; padding: 11px 16px;
    background: var(--input-bg); border: 1px solid var(--input-border);
    border-radius: 14px; color: var(--text-primary);
    font-size: 0.88rem; outline: none;
    transition: all 0.25s; position: relative;
}
.floating-input:focus {
    border-color: var(--accent-gold);
    box-shadow: 0 0 0 3px rgba(240,180,41,0.12);
}
.floating-input::placeholder { color: var(--text-muted); opacity: 0.5; }
.floating-input-glow {
    position: absolute; bottom: -1px; left: 50%; transform: translateX(-50%);
    width: 0; height: 2px;
    background: linear-gradient(90deg, transparent, var(--accent-gold), transparent);
    border-radius: 2px;
    transition: width 0.3s ease;
    pointer-events: none;
}
.floating-input:focus ~ .floating-input-glow { width: 80%; }

/* Type Toggle */
.type-toggle {
    display: flex; gap: 8px;
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 16px; padding: 5px;
}
.type-option {
    flex: 1; display: flex; align-items: center; justify-content: center; gap: 7px;
    padding: 9px 12px; border-radius: 12px; border: none;
    background: transparent; color: var(--text-muted);
    font-size: 0.8rem; font-weight: 600; cursor: pointer;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.type-option i { font-size: 0.85rem; transition: transform 0.3s; }
.type-option:hover { color: var(--text-secondary); }
.type-option.type-income.active {
    background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05));
    color: #10b981; box-shadow: 0 2px 8px rgba(16,185,129,0.15);
}
.type-option.type-income.active i { transform: translateY(-2px); }
.type-option.type-expense.active {
    background: linear-gradient(135deg, rgba(239,68,68,0.15), rgba(239,68,68,0.05));
    color: #ef4444; box-shadow: 0 2px 8px rgba(239,68,68,0.15);
}
.type-option.type-expense.active i { transform: translateY(2px); }

/* Select */
.floating-select-wrap { position: relative; }
.floating-select { appearance: none; -webkit-appearance: none; padding-right: 36px; cursor: pointer; }
.floating-select option { background: var(--white); color: var(--text-primary); }
.floating-select-arrow {
    position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
    color: var(--text-muted); font-size: 0.7rem; pointer-events: none;
    transition: transform 0.25s;
}
.floating-select:focus + .floating-select-arrow { transform: translateY(-50%) rotate(180deg); }

/* Amount */
.floating-amount-wrap {
    position: relative; display: flex; align-items: center;
}
.floating-currency-prefix {
    position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
    font-size: 0.85rem; font-weight: 700; color: var(--accent-gold);
    z-index: 1; pointer-events: none;
}
.floating-amount-input { padding-left: 40px; font-size: 1.1rem; font-weight: 700; letter-spacing: -0.02em; }

/* Textarea */
.floating-textarea { border-radius: 14px; resize: vertical; min-height: 60px; }

/* Actions */
.floating-actions {
    display: flex; gap: 10px; margin-top: 24px; padding-top: 18px;
    border-top: 1px solid var(--glass-border);
    animation: fieldSlideUp 0.4s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: calc(var(--i, 1) * 0.07s);
}
.floating-btn {
    padding: 12px 20px; border-radius: 14px; font-weight: 700; font-size: 0.85rem;
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
.floating-btn-primary i { margin-right: 4px; }
.floating-btn-secondary {
    background: transparent; border: 1px solid var(--glass-border);
    color: var(--text-muted); min-width: 90px;
}
.floating-btn-secondary:hover {
    background: rgba(240,180,41,0.08);
    border-color: rgba(240,180,41,0.3);
    color: var(--accent-gold);
}

/* Scrollbar */
.modal-floating-card::-webkit-scrollbar { width: 4px; }
.modal-floating-card::-webkit-scrollbar-track { background: transparent; }
.modal-floating-card::-webkit-scrollbar-thumb { background: var(--glass-border); border-radius: 4px; }

/* Responsive */
@media (max-width: 480px) {
    .floating-header { padding: 18px 18px 12px; }
    form { padding: 0 18px 18px; }
    .floating-title { font-size: 1rem; }
    .floating-icon-wrap { width: 40px; height: 40px; font-size: 1.1rem; border-radius: 13px; }
    .type-option { padding: 7px 10px; font-size: 0.74rem; }
    .floating-input { padding: 9px 14px; font-size: 0.82rem; border-radius: 12px; }
    .floating-amount-input { padding-left: 36px; font-size: 1rem; }
    .floating-btn { padding: 10px 16px; font-size: 0.8rem; border-radius: 12px; }
    .modal-floating-card { border-radius: 22px; }
}
/* Export button variants */
.floating-btn-excel {
    width: 100%; padding: 14px 20px; border-radius: 14px; font-weight: 700;
    background: linear-gradient(90deg, #059669, #10b981);
    color: white; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    font-size: 0.88rem; transition: all 0.25s; box-shadow: 0 4px 14px rgba(16,185,129,0.3);
}
.floating-btn-excel:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16,185,129,0.45);
}
.floating-btn-pdf {
    width: 100%; padding: 14px 20px; border-radius: 14px; font-weight: 700;
    background: linear-gradient(90deg, #dc2626, #ef4444);
    color: white; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    font-size: 0.88rem; transition: all 0.25s; box-shadow: 0 4px 14px rgba(239,68,68,0.3);
}
.floating-btn-pdf:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239,68,68,0.45);
}
.floating-btn-outline {
    width: 100%; padding: 12px 20px; border-radius: 14px; font-weight: 600;
    background: transparent; border: 1px solid var(--glass-border);
    color: var(--text-muted); cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    font-size: 0.85rem; transition: all 0.25s;
}
.floating-btn-outline:hover {
    background: rgba(240,180,41,0.08);
    border-color: rgba(240,180,41,0.3);
    color: var(--accent-gold);
}

.empty-state { text-align: center; padding: 48px; color: var(--text-muted); }
.empty-state i { font-size: 2rem; margin-bottom: 12px; }
.divider { height: 1px; background: var(--glass-border); margin: 16px 0; }
.filter-bar-card {
    margin-bottom: 28px;
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
.filter-bar-top {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 12px;
}
.filter-period-label {
    display: flex; align-items: center; gap: 8px;
    font-size: 0.85rem; font-weight: 700;
    color: var(--text-primary); transition: all 0.3s;
}
.filter-period-label i { color: var(--accent-gold); }
.filter-period-label.label-update { animation: labelPop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); }
@keyframes labelPop { 0% { transform: scale(1); } 50% { transform: scale(1.06); color: var(--accent-gold); } 100% { transform: scale(1); } }
.filter-actions { display: flex; gap: 8px; }
.filter-actions .btn-sm { padding: 6px 12px; min-width: 34px; justify-content: center; }
.filter-bar-presets { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.filter-chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 14px; border: 1px solid var(--glass-border);
    border-radius: 40px; background: var(--glass-bg);
    color: var(--text-secondary); font-size: 0.78rem; font-weight: 600;
    cursor: pointer; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); white-space: nowrap;
}
.filter-chip i { font-size: 0.7rem; transition: transform 0.25s; }
.filter-chip:hover { background: rgba(240,180,41,0.12); border-color: var(--accent-gold); color: var(--accent-gold); transform: translateY(-1px); }
.filter-chip:hover i { transform: scale(1.15); }
.filter-chip.active { background: linear-gradient(90deg, var(--accent-gold), var(--accent-orange)); color: #1e1a0c; border-color: transparent; box-shadow: 0 2px 12px rgba(240,180,41,0.3); }
.filter-chip.active i { color: inherit; }
.filter-separator { width: 1px; height: 24px; background: var(--glass-border); margin: 0 4px; }
.filter-month-wrap { display: flex; align-items: center; gap: 6px; }
.filter-month-input {
    padding: 5px 10px; background: var(--input-bg);
    border: 1px solid var(--input-border); border-radius: 40px;
    color: var(--text-primary); font-size: 0.78rem; outline: none; cursor: pointer;
    transition: all 0.2s; width: 150px;
}
.filter-month-input:focus { border-color: var(--accent-gold); box-shadow: 0 0 0 3px rgba(240,180,41,0.15); }
.filter-month-input::-webkit-calendar-picker-indicator { cursor: pointer; }
body.dark-mode .filter-month-input::-webkit-calendar-picker-indicator { filter: invert(1); }
.pagination-wrapper {
    display: flex; justify-content: space-between; align-items: center;
    padding: 14px 4px 4px; flex-wrap: wrap; gap: 12px;
}
.pagination-left { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.pagination-right { display: flex; align-items: center; gap: 4px; }
.page-size-label { font-size: 0.8rem; color: var(--text-secondary); }
.page-size-select {
    padding: 5px 10px; border: 1px solid var(--glass-border); border-radius: 8px;
    background: var(--glass-bg); color: var(--text-primary); font-size: 0.8rem; cursor: pointer;
    outline: none; width: auto;
}
.page-size-select option { background: var(--white); color: var(--text-primary); }
.pagination-info { font-size: 0.8rem; color: var(--text-muted); }
.page-btn {
    min-width: 34px; height: 34px; padding: 0 8px; border: 1px solid var(--glass-border);
    background: var(--glass-bg); color: var(--text-secondary); border-radius: 8px;
    cursor: pointer; font-size: 0.8rem; font-weight: 600; transition: all 0.2s;
    display: inline-flex; align-items: center; justify-content: center;
}
.page-btn:hover:not(.disabled):not(.active) { background: rgba(240,180,41,0.15); border-color: var(--accent-gold); color: var(--accent-gold); }
.page-btn.active { background: linear-gradient(90deg, var(--accent-gold), var(--accent-orange)); color: #1e1a0c; border-color: transparent; }
.page-btn.disabled { opacity: 0.35; cursor: not-allowed; }
.page-ellipsis { padding: 0 4px; color: var(--text-muted); font-weight: 600; }


.skeleton-loader {
    animation: shimmer 1.5s ease-in-out infinite;
    background: linear-gradient(90deg, rgba(200,200,210,0.15) 25%, rgba(200,200,210,0.3) 50%, rgba(200,200,210,0.15) 75%);
    background-size: 200% 100%; border-radius: 6px;
}
body.dark-mode .skeleton-loader {
    background: linear-gradient(90deg, rgba(255,255,255,0.04) 25%, rgba(255,255,255,0.12) 50%, rgba(255,255,255,0.04) 75%);
    background-size: 200% 100%;
}
.skeleton-row td { padding: 16px 12px; border-bottom: 1px solid var(--glass-border); }
.skeleton-cell { height: 14px; width: 80%; margin: 0 auto; }
.skeleton-cell.w-40 { width: 40%; }
.skeleton-cell.w-55 { width: 55%; }
.skeleton-cell.w-60 { width: 60%; }
.skeleton-cell.w-70 { width: 70%; }
.skeleton-cell.w-25 { width: 25%; }
.skeleton-cell.w-30 { width: 30%; }
.skeleton-cell.w-20 { width: 20%; }
.skeleton-badge { height: 22px; width: 90px; border-radius: 40px; display: inline-block; }



@media (max-width: 1024px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .pagination-wrapper { flex-direction: column; align-items: center; }
    .pagination-left { justify-content: center; }
    .pagination-right { flex-wrap: wrap; justify-content: center; }
    .sidebar { position: fixed; left: -260px; top: 0; height: 100%; width: 260px; }
    .sidebar.collapsed { width: 260px; left: -260px; }
    .sidebar.open { left: 0; }
    .stats-grid, .grid-2 { grid-template-columns: 1fr; }
    .main-content { padding: 16px; }
    .filter-chip { padding: 5px 10px; font-size: 0.72rem; gap: 4px; }
    .filter-month-input { width: 130px; }
    .top-bar { margin-bottom: 18px; padding-bottom: 12px; }
}
@media (max-width: 480px) {
    .main-content { padding: 10px; }
    .top-bar { margin-bottom: 14px; padding-bottom: 10px; gap: 8px; }
    .user-info { padding: 6px 12px; font-size: 0.75rem; gap: 5px; }
    .dark-mode-toggle { padding: 6px 10px; }
    .menu-toggle { padding: 6px 10px; }
    .stat-card { padding: 12px 14px; gap: 10px; }
    .stat-icon-wrap { width: 32px; height: 32px; font-size: 0.8rem; border-radius: 9px; }
    .stat-value { font-size: 1.05rem; }
    .stat-label { font-size: 0.6rem; letter-spacing: 0.4px; }
    .stat-period { font-size: 0.55rem; margin-top: 1px; }
    .stat-glow { display: none; }
    .filter-bar-card { padding: 10px 12px; margin-bottom: 18px; }
    .filter-bar-top { margin-bottom: 8px; }
    .filter-period-label { font-size: 0.72rem; }
    .filter-chip { padding: 4px 8px; font-size: 0.65rem; }
    .filter-chip i { font-size: 0.55rem; }
    .filter-month-input { width: 110px; font-size: 0.7rem; padding: 4px 8px; }
    .filter-actions .btn-sm { padding: 4px 8px; min-width: 30px; }
    .filter-separator { height: 18px; margin: 0 2px; }
    .grid-2 { gap: 12px; }
    .chart-canvas-wrap { min-height: 180px; }
    .chart-canvas-wrap canvas { max-height: 200px; }
    .chart-card-header { margin-bottom: 12px; padding-bottom: 8px; gap: 10px; }
    .chart-card-icon { width: 32px; height: 32px; font-size: 0.85rem; border-radius: 9px; }
    .chart-card-title { font-size: 0.78rem; }
    .chart-card-subtitle { font-size: 0.63rem; }
    .table-container th:nth-child(5),
    .table-container td:nth-child(5) { display: none; }
    th, td { padding: 8px 6px; font-size: 0.7rem; }
    .badge-income, .badge-expense { font-size: 0.6rem; padding: 2px 7px; }
    .pagination-wrapper { padding: 10px 2px 2px; }
    .pagination-left { gap: 5px; }
    .pagination-info { font-size: 0.65rem; }
    .page-btn { min-width: 28px; height: 28px; font-size: 0.68rem; padding: 0 5px; }
    .page-size-label { font-size: 0.7rem; }
    .page-size-select { font-size: 0.7rem; padding: 4px 8px; }
    .modal-content { padding: 18px; width: 95%; }
    .modal-content h3 { font-size: 0.9rem; margin-bottom: 14px; }
    .card { padding: 16px; border-radius: 18px; }
    .form-group { margin-bottom: 12px; }
    .form-control { padding: 8px 14px; font-size: 0.8rem; }
}
.hero-section {
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 28px;
    padding: 32px;
    background: linear-gradient(135deg, rgba(240,180,41,0.08), rgba(255,140,66,0.03));
    border: 1px solid rgba(240,180,41,0.15);
    border-radius: 28px;
    position: relative;
    overflow: hidden;
}
.hero-section::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #d4940a, #e07020, #d4940a);
}
.hero-icon {
    width: 72px; height: 72px;
    border-radius: 24px;
    background: linear-gradient(135deg, #d4940a, #e07020);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem; color: #1e1a0c;
    flex-shrink: 0;
    box-shadow: 0 8px 24px rgba(240,180,41,0.3);
}
.hero-body { flex: 1; }
.hero-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 2px; }
.hero-amount { font-size: 2.4rem; font-weight: 900; letter-spacing: -0.03em; color: #d4940a; line-height: 1.2; }
body.dark-mode .hero-amount { color: #f0b429; }
.hero-sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }
.hero-glow {
    position: absolute; width: 300px; height: 300px;
    border-radius: 50%; right: -60px; top: -60px;
    background: radial-gradient(circle, rgba(240,180,41,0.1) 0%, transparent 70%);
    pointer-events: none;
}
@media (max-width: 576px) {
    .hero-section { flex-direction: column; text-align: center; padding: 24px; gap: 16px; }
    .hero-amount { font-size: 1.8rem; }
}
@media (max-width: 400px) {
    .hero-section { padding: 18px; }
    .hero-icon { width: 56px; height: 56px; font-size: 1.4rem; border-radius: 18px; }
    .hero-amount { font-size: 1.5rem; }
}
</style>
@endpush

@section('content')
<div class="app-container">
@include('layouts.sidebar', ['active' => 'dompet'])

    <main class="main-content">
@include('layouts.topbar', ['profileOnclick' => 'showProfilePage()'])

        <!-- Ã¢â€â‚¬Ã¢â€â‚¬ Dompet Utama Hero Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ -->
        <div class="hero-section fade-in">
            <div class="hero-icon"><i class="fas fa-wallet"></i></div>
            <div class="hero-body">
                <div class="hero-label"><i class="fas fa-star" style="font-size:0.5rem;margin-right:3px;"></i> Wallet Aktif</div>
                <div class="hero-amount" id="mainBalanceHero">Rp 0</div>
                <div class="hero-sub"><i class="fas fa-circle" style="font-size:0.4rem;color:var(--accent-gold);"></i> Transaksi sehari-hari &amp; pengeluaran rutin</div>
            </div>
            <div class="hero-glow"></div>
        </div>

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

        <div class="grid-2">
            <div class="card chart-card chart-card-expense fade-in">
                <div class="chart-card-header">
                    <div class="chart-card-icon"><i class="fas fa-arrow-trend-down"></i></div>
                    <div>
                        <div class="chart-card-title">Grafik Pengeluaran</div>
                        <div class="chart-card-subtitle">per Kategori</div>
                    </div>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="expensePieChart"></canvas>
                </div>
            </div>
            <div class="card chart-card chart-card-income fade-in">
                <div class="chart-card-header">
                    <div class="chart-card-icon"><i class="fas fa-arrow-trend-up"></i></div>
                    <div>
                        <div class="chart-card-title">Grafik Pemasukan</div>
                        <div class="chart-card-subtitle">per Kategori</div>
                    </div>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="incomeDoughnutChart"></canvas>
                </div>
            </div>
        </div>

        <div class="filter-bar-card">
            <div class="filter-bar-top">
                <div class="filter-period-label" id="filterPeriodLabel">
                    <i class="fas fa-calendar-alt" style="font-size: 0.9rem;"></i>
                    <span id="filterLabelText">Semua Transaksi</span>
                </div>
            </div>
            <div class="filter-bar-presets">
                <button class="filter-chip active" data-period="all" onclick="setQuickFilter('all')">
                    <i class="fas fa-globe-asia"></i> Semua
                </button>
                <button class="filter-chip" data-period="this-month" onclick="setQuickFilter('this-month')">
                    <i class="fas fa-calendar-check"></i> Bulan Ini
                </button>
                <button class="filter-chip" data-period="last-month" onclick="setQuickFilter('last-month')">
                    <i class="fas fa-chevron-left"></i> Bulan Lalu
                </button>
                <div class="filter-separator"></div>
                <div class="filter-month-wrap">
                    <i class="fas fa-calendar-alt" style="color:var(--text-muted);font-size:0.75rem;"></i>
                    <input type="month" class="filter-month-input" id="monthPicker" onchange="changeMonth()">
                </div>
                <div class="filter-actions">
                    <button class="btn btn-primary btn-sm" onclick="loadTransactions()" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button class="btn btn-outline btn-sm" onclick="openModal('transactionModal')" title="Tambah Transaksi">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="btn btn-outline btn-sm" onclick="openModal('reportModal')" title="Export">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.85rem; color: var(--text-primary); margin-bottom: 12px;">
                <i class="fas fa-list" style="color: var(--accent-gold);"></i> Riwayat Transaksi
            </div>
            <div class="search-bar">
                <i class="fas fa-search search-bar-icon"></i>
                <input type="text" class="search-bar-input" id="transactionSearch" placeholder="Cari transaksi..." autocomplete="off">
                <button class="search-bar-clear" id="searchClearBtn" style="display:none;" onclick="clearTransactionSearch()"><i class="fas fa-times"></i></button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTable">
                        <tr><td colspan="6" style="text-align: center; padding: 32px;">
                            <div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Memuat data...</p></div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
            <div class="search-no-result" id="searchNoResult">
                <i class="fas fa-search"></i>
                <p>Tidak ada transaksi yang cocok</p>
            </div>
            <div id="paginationContainer"></div>
        </div>

        <!-- Modal Tambah Transaksi — Premium Floating -->
<div id="transactionModal" class="modal modal-floating">
    <div class="modal-floating-overlay"></div>
    <div class="modal-floating-card" id="floatingCard">
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
                <i class="fas fa-plus-circle"></i>
            </div>
            <div>
                <h3 class="floating-title">Tambah Transaksi</h3>
                <p class="floating-subtitle">Catat pemasukan atau pengeluaran baru</p>
            </div>
            <button type="button" class="floating-close" onclick="closeModal('transactionModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="transactionForm">
            @csrf
            
            <!-- Tanggal -->
            <div class="floating-field" style="--i:1;">
                <label class="floating-label">
                    <i class="fas fa-calendar-alt"></i> Tanggal
                </label>
                <input type="date" class="floating-input" id="transactionDate" required>
                <div class="floating-input-glow"></div>
            </div>

            <!-- Jenis — Visual Toggle -->
            <div class="floating-field" style="--i:2;">
                <label class="floating-label">
                    <i class="fas fa-tag"></i> Jenis Transaksi
                </label>
                <div class="type-toggle" id="typeToggle">
                    <input type="hidden" id="type" value="income">
                    <button type="button" class="type-option type-income active" data-value="income" onclick="setType('income')">
                        <i class="fas fa-arrow-down"></i>
                        <span>Pemasukan</span>
                    </button>
                    <button type="button" class="type-option type-expense" data-value="expense" onclick="setType('expense')">
                        <i class="fas fa-arrow-up"></i>
                        <span>Pengeluaran</span>
                    </button>
                </div>
            </div>

            <!-- Kategori -->
            <div class="floating-field" style="--i:3;">
                <label class="floating-label">
                    <i class="fas fa-layer-group"></i> Kategori
                </label>
                <div class="floating-select-wrap">
                    <select class="floating-input floating-select" id="category" required>
                        <option value="">Pilih Kategori</option>
                    </select>
                    <i class="fas fa-chevron-down floating-select-arrow"></i>
                </div>
                <div class="floating-field" id="manualCategoryGroup" style="display: none; --i:3.5;">
                    <input type="text" class="floating-input" id="manualCategory" placeholder="Masukkan kategori manual...">
                </div>
            </div>

            <!-- Nominal -->
            <div class="floating-field" style="--i:4;">
                <label class="floating-label">
                    <i class="fas fa-money-bill-wave"></i> Nominal (Rp)
                </label>
                <div class="floating-amount-wrap">
                    <span class="floating-currency-prefix">Rp</span>
                    <input type="text" class="floating-input floating-amount-input rupiah-input" id="amount" required placeholder="0">
                </div>
            </div>

            <!-- Catatan -->
            <div class="floating-field" style="--i:5;">
                <label class="floating-label">
                    <i class="fas fa-pen"></i> Catatan <span class="floating-optional">(opsional)</span>
                </label>
                <textarea class="floating-input floating-textarea" id="note" rows="2" placeholder="Tambahkan catatan..."></textarea>
            </div>

            <!-- Actions -->
            <div class="floating-actions" style="--i:6;">
                <button type="button" class="floating-btn floating-btn-primary" onclick="addTransaction(event)" id="saveTransactionBtn">
                    <i class="fas fa-paper-plane"></i>
                    <span>Simpan Transaksi</span>
                </button>
                <button type="button" class="floating-btn floating-btn-secondary" onclick="closeModal('transactionModal')">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Transaksi — Premium Floating -->
<div id="editTransactionModal" class="modal modal-floating">
    <div class="modal-floating-overlay"></div>
    <div class="modal-floating-card" id="floatingEditCard">
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
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h3 class="floating-title">Edit Transaksi</h3>
                <p class="floating-subtitle">Ubah data transaksi yang sudah ada</p>
            </div>
            <button type="button" class="floating-close" onclick="closeModal('editTransactionModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form>
            @csrf
            <input type="hidden" id="editTransactionId">
            
            <!-- Tanggal -->
            <div class="floating-field" style="--i:1;">
                <label class="floating-label">
                    <i class="fas fa-calendar-alt"></i> Tanggal
                </label>
                <input type="date" class="floating-input" id="editTransactionDate" required>
                <div class="floating-input-glow"></div>
            </div>

            <!-- Jenis -->
            <div class="floating-field" style="--i:2;">
                <label class="floating-label">
                    <i class="fas fa-tag"></i> Jenis
                </label>
                <div class="floating-select-wrap">
                    <select class="floating-input floating-select" id="editType" required>
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                    <i class="fas fa-chevron-down floating-select-arrow"></i>
                </div>
            </div>

            <!-- Kategori -->
            <div class="floating-field" style="--i:3;">
                <label class="floating-label">
                    <i class="fas fa-layer-group"></i> Kategori
                </label>
                <div class="floating-select-wrap">
                    <select class="floating-input floating-select" id="editCategory" required>
                        <option value="">Pilih Kategori</option>
                    </select>
                    <i class="fas fa-chevron-down floating-select-arrow"></i>
                </div>
                <div class="floating-field" id="editManualCategoryGroup" style="display: none; --i:3.5;">
                    <input type="text" class="floating-input" id="editManualCategory" placeholder="Masukkan kategori manual...">
                </div>
            </div>

            <!-- Nominal -->
            <div class="floating-field" style="--i:4;">
                <label class="floating-label">
                    <i class="fas fa-money-bill-wave"></i> Nominal (Rp)
                </label>
                <div class="floating-amount-wrap">
                    <span class="floating-currency-prefix">Rp</span>
                    <input type="text" class="floating-input floating-amount-input rupiah-input" id="editAmount" required placeholder="0">
                </div>
            </div>

            <!-- Catatan -->
            <div class="floating-field" style="--i:5;">
                <label class="floating-label">
                    <i class="fas fa-pen"></i> Catatan <span class="floating-optional">(opsional)</span>
                </label>
                <textarea class="floating-input floating-textarea" id="editNote" rows="2" placeholder="Tambahkan catatan..."></textarea>
            </div>

            <!-- Actions -->
            <div class="floating-actions" style="--i:6;">
                <button type="button" class="floating-btn floating-btn-primary" onclick="updateTransaction()" id="updateTransactionBtn">
                    <i class="fas fa-check"></i>
                    <span>Simpan Perubahan</span>
                </button>
                <button type="button" class="floating-btn floating-btn-secondary" onclick="closeModal('editTransactionModal')">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Transfer Antar Wallet -->
<div id="transferModal" class="modal">
    <div class="modal-content" style="max-width: 480px;">
        <h3><i class="fas fa-arrow-right-arrow-left"></i> Transfer Antar Wallet</h3>
        <form id="transferForm">
            @csrf
            <div class="form-group">
                <label>Dari Wallet</label>
                <select class="form-control" id="transferFromWallet" required>
                    <option value="">Pilih wallet asal</option>
                </select>
            </div>
            <div class="form-group">
                <label>Ke Wallet</label>
                <select class="form-control" id="transferToWallet" required>
                    <option value="">Pilih wallet tujuan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nominal (Rp)</label>
                <input type="text" class="form-control rupiah-input" id="transferAmount" required placeholder="0">
            </div>
            <div class="form-group">
                <label>Catatan (opsional)</label>
                <input type="text" class="form-control" id="transferNote" placeholder="Catatan transfer">
            </div>
            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button type="button" class="btn btn-primary" style="flex: 1; justify-content: center;" onclick="doTransfer()" id="transferBtn">
                    <i class="fas fa-paper-plane"></i> Transfer
                </button>
                <button type="button" class="btn btn-outline" onclick="closeModal('transferModal')">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Laporan & Export — Premium Floating -->
<div id="reportModal" class="modal modal-floating">
    <div class="modal-floating-overlay"></div>
    <div class="modal-floating-card" id="floatingReportCard">
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
                <i class="fas fa-file-export"></i>
            </div>
            <div>
                <h3 class="floating-title">Export Laporan</h3>
                <p class="floating-subtitle">Unduh data transaksi ke file Excel atau PDF</p>
            </div>
            <button type="button" class="floating-close" onclick="closeModal('reportModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form style="padding: 0 28px 24px;">
            <!-- Info -->
            <div class="floating-field" style="--i:1;">
                <p style="font-size: 0.82rem; color: var(--text-secondary); line-height: 1.6;">
                    Ekspor data transaksi ke file Excel atau PDF untuk keperluan laporan dan arsip.
                </p>
            </div>

            <!-- Excel -->
            <div class="floating-field" style="--i:2;">
                <button type="button" class="floating-btn-excel" onclick="exportToExcel(); closeModal('reportModal')">
                    <i class="fas fa-file-excel" style="font-size: 1.1rem;"></i>
                    <span>Export ke Excel</span>
                </button>
            </div>

            <!-- PDF -->
            <div class="floating-field" style="--i:3;">
                <button type="button" class="floating-btn-pdf" onclick="exportToPDF(); closeModal('reportModal')">
                    <i class="fas fa-file-pdf" style="font-size: 1.1rem;"></i>
                    <span>Export ke PDF</span>
                </button>
            </div>

            <!-- Tutup -->
            <div class="floating-field" style="--i:4;">
                <button type="button" class="floating-btn-outline" onclick="closeModal('reportModal')">
                    <i class="fas fa-times"></i>
                    <span>Tutup</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pengaturan Profil -->
<div id="profileModal" class="modal">
    <div class="modal-content" style="max-width: 480px;">
        <h3><i class="fas fa-user-cog"></i> Pengaturan Profil</h3>
        <div style="margin-top: 16px;">
            <h4 style="color: var(--text-primary); font-size: 0.95rem; font-weight: 700; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--glass-border);">
                <i class="fas fa-id-card"></i> Data Profil
            </h4>
            <form id="profileForm">
                @csrf
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" class="form-control" id="profileFullName" required placeholder="Masukkan nama lengkap">
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" id="profileUsername" required minlength="3" placeholder="Minimal 3 karakter">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;" id="saveProfileBtn">
                    <i class="fas fa-save"></i> Simpan Profil
                </button>
            </form>
        </div>
        <div style="margin-top: 20px;">
            <h4 style="color: var(--text-primary); font-size: 0.95rem; font-weight: 700; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--glass-border);">
                <i class="fas fa-lock"></i> Ganti Password
            </h4>
            <form id="passwordForm">
                @csrf
                <div class="form-group">
                    <label>Password Lama</label>
                    <input type="password" class="form-control" id="currentPassword" required placeholder="Masukkan password lama">
                </div>
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" class="form-control" id="newPassword" required minlength="8" placeholder="Minimal 8 karakter">
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="confirmPassword" required placeholder="Ulangi password baru">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;" id="savePasswordBtn">
                    <i class="fas fa-key"></i> Ganti Password
                </button>
            </form>
        </div>
        <div style="margin-top: 16px;">
            <button class="btn btn-outline" onclick="closeModal('profileModal')" style="width: 100%; justify-content: center;">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
        </div>
@include('layouts.profile-form-inline')
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

// Ã¢â€â‚¬Ã¢â€â‚¬ State Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
var currentUser = null;
var currentMonth = null;
var allTransactions = [];
var categories = [];
var wallets = [];
var currentWalletId = null;
var currentWalletBalance = 0;
var pagination = { currentPage: 1, perPage: 10, total: 0, totalPages: 0 };
var expenseChart = null;
var incomeChart = null;

var API_BASE = window.DOMpetConfig.apiBase;
var PASSWORD_MIN_LENGTH = 8;

// Ã¢â€â‚¬Ã¢â€â‚¬ Utilities Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
function formatDateInput(dateString) {
    const date = new Date(dateString);
    return date.toISOString().slice(0, 10);
}

function parseRupiahToNumber(rupiahString) {
    return parseInt(rupiahString.replace(/[^0-9]/g, '')) || 0;
}

function autoFormatRupiah(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
        input.value = value;
    }
}

function initRupiahFormatting() {
    document.querySelectorAll('.rupiah-input').forEach(input => {
        input.addEventListener('input', function() { autoFormatRupiah(this); });
    });
}

function showToast(message, type = 'success') {
    const iconMap = { success: 'success', error: 'error', info: 'info' };
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: iconMap[type] || 'success',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            background: '#1a1a2e',
            color: '#fff'
        });
        return;
    }
    console.log(`[${type}] ${message}`);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR',
        minimumFractionDigits: 0, maximumFractionDigits: 0
    }).format(amount);
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function matchId(a, b) { return Number(a) === Number(b); }
function findById(items, id) { return items.find(item => matchId(item.id, id)); }

function monthLabel(ym) {
    const names = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const [y, m] = ym.split('-');
    return `${names[parseInt(m, 10) - 1]} ${y}`;
}

// Ã¢â€â‚¬Ã¢â€â‚¬ API Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
async function apiRequest(endpoint, method = 'GET', data = null, options = {}) {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta || !csrfMeta.content) {
        throw new Error('CSRF token tidak ditemukan. Silakan refresh halaman.');
    }
    const fetchOptions = {
        method: method,
        credentials: 'include',
        headers: {
            'X-CSRF-TOKEN': csrfMeta.content,
            'Accept': 'application/json',
        }
    };

    if (data && (method === 'POST' || method === 'PUT' || method === 'DELETE')) {
        const formData = new URLSearchParams();
        for (const key in data) {
            formData.append(key, data[key]);
        }
        if (method === 'PUT' || method === 'DELETE') {
            formData.append('_method', method);
            fetchOptions.method = 'POST';
        }
        fetchOptions.body = formData.toString();
        fetchOptions.headers['Content-Type'] = 'application/x-www-form-urlencoded';
    }

    try {
        const response = await fetch(`${API_BASE}/${endpoint}`, fetchOptions);

        if (response.status === 401) {
            localStorage.removeItem('user');
            window.location.href = '/login';
            throw new Error('Unauthorized');
        }

        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response');
        }

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || result.message || 'Request failed');
        }

        return result;
    } catch (error) {
        console.error('API Error [' + endpoint + ']:', error);
        if (!options.silent) {
            showToast(error.message, 'error');
        }
        throw error;
    }
}

async function checkAuth() {
    try {
        const data = await apiRequest('auth/check', 'GET', null, { silent: true });
        if (!data.authenticated) {
            localStorage.removeItem('user');
            window.location.href = '/login';
            return null;
        }
        const stored = localStorage.getItem('user');
        let user = data.user;
        if (stored) {
            try {
                const cached = JSON.parse(stored);
                if (cached.saldo !== undefined) user = { ...user, saldo: cached.saldo };
            } catch (e) {}
        }
        localStorage.setItem('user', JSON.stringify(user));
        return user;
    } catch (error) {
        localStorage.removeItem('user');
        window.location.href = '/login';
        return null;
    }
}

async function logout() {
    try {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfMeta || !csrfMeta.content) {
            console.warn('CSRF token tidak ditemukan, logout mungkin gagal.');
        }
        await fetch(window.DOMpetConfig.logoutUrl, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-CSRF-TOKEN': csrfMeta?.content || '',
            }
        });
    } catch (error) {
        console.error('Logout error:', error);
    }
    localStorage.removeItem('user');
    showToast('Logout berhasil', 'success');
    setTimeout(() => { window.location.href = '/login'; }, 500);
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Skeleton Loader Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
function showTransactionSkeleton() {
    const tbody = document.getElementById('transactionsTable');
    if (!tbody) return;
    const rows = Array(5).fill('').map(() => `
        <tr class="skeleton-row">
            <td><div class="skeleton-cell skeleton-loader w-60"></div></td>
            <td><div class="skeleton-badge skeleton-loader"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-55"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-40"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-70"></div></td>
            <td><div style="display:flex;gap:6px;justify-content:center;">
                <div class="skeleton-loader" style="width:32px;height:32px;border-radius:8px;"></div>
                <div class="skeleton-loader" style="width:32px;height:32px;border-radius:8px;"></div>
            </div></td>
        </tr>
    `).join('');
    tbody.innerHTML = rows;
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Transactions Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
async function loadTransactions(page = 1) {
    showTransactionSkeleton();
    try {
        let endpoint = `transactions?page=${page}&limit=${pagination.perPage}`;
        if (currentMonth) endpoint += `&month=${currentMonth}`;
        if (currentWalletId) endpoint += `&wallet_id=${currentWalletId}`;
        const data = await apiRequest(endpoint);
        allTransactions = data.transactions;
        if (data.pagination) {
            const p = data.pagination;
            pagination.currentPage = p.current_page || 1;
            pagination.perPage = p.per_page || 10;
            pagination.total = p.total || 0;
            pagination.totalPages = p.total_pages || 0;
        }
        updateSummaryCards(data.summary);
        renderTransactionsTable(allTransactions);
        renderPagination();
        await updateBalance();
        // Reset search on data reload
        var searchInput = document.getElementById('transactionSearch');
        if (searchInput && searchInput.value) {
            searchInput.value = '';
            filterTransactionTable('');
        }
        return data;
    } catch (error) {
        console.error('Load transactions error:', error);
    }
}

function parseCurrencyText(text) {
    if (!text) return 0;
    return parseInt(text.replace(/[^0-9]/g, '')) || 0;
}

function animateValue(element, start, end, duration = 600, isCurrency = true) {
    if (!element) return;
    const startTime = performance.now();
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const current = Math.round(start + (end - start) * eased);
        if (isCurrency || typeof isCurrency === 'undefined') {
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

function updatePeriodLabels() {
    const ids = ['periodIncome', 'periodExpense', 'periodBalance', 'periodCount'];
    const text = currentMonth
        ? '<i class="fas fa-calendar-day"></i> ' + monthLabel(currentMonth)
        : '<i class="fas fa-infinity"></i> Semua waktu';
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.innerHTML = text;
    });
}

function updateSummaryCards(summary) {
    const els = {
        totalIncome: document.getElementById('totalIncome'),
        totalExpense: document.getElementById('totalExpense'),
        balance: document.getElementById('balance'),
        totalTransactions: document.getElementById('totalTransactions'),
    };
    const prevIncome = parseCurrencyText(els.totalIncome?.textContent);
    const prevExpense = parseCurrencyText(els.totalExpense?.textContent);
    const prevBalance = parseCurrencyText(els.balance?.textContent);
    const prevCount = parseCurrencyText(els.totalTransactions?.textContent);
    animateValue(els.totalIncome, prevIncome, summary.total_income, 600, true);
    animateValue(els.totalExpense, prevExpense, summary.total_expense, 600, true);
    animateValue(els.balance, prevBalance, summary.balance, 600, true);
    animateValue(els.totalTransactions, prevCount, summary.total_transactions, 500, false);
    if (els.balance) {
        els.balance.classList.toggle('balance-negative', summary.balance < 0);
    }
    updatePeriodLabels();
}

async function updateBalance() {
    try {
        let endpoint = 'user/balance';
        if (currentWalletId) endpoint += `?wallet_id=${currentWalletId}`;
        const data = await apiRequest(endpoint);
        const balanceEl = document.getElementById('balance');
        const mainBalanceHero = document.getElementById('mainBalanceHero');
        const fmt = formatCurrency(data.balance);
        if (balanceEl) {
            balanceEl.textContent = fmt;
            balanceEl.classList.toggle('balance-negative', data.balance < 0);
        }
        if (mainBalanceHero) mainBalanceHero.textContent = fmt;
        if (currentUser) {
            currentUser.saldo = data.balance;
            localStorage.setItem('user', JSON.stringify(currentUser));
        }
    } catch (error) {
        console.error('Update balance error:', error);
    }
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Wallets Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
async function loadWallets() {
    try {
        const data = await apiRequest('wallets');
        wallets = data.wallets;

        if (!currentWalletId && wallets.length > 0) {
            const main = wallets.find(w => w.type === 'main');
            if (main) currentWalletId = main.id;
        }

        updateWalletUI();
    } catch (error) {
        console.error('Load wallets error:', error);
    }
}

function updateWalletUI() {
    const mainWallet = wallets.find(w => w.type === 'main');
    const savingsWallet = wallets.find(w => w.type === 'savings');

    const mainBalanceHero = document.getElementById('mainBalanceHero');

    if (mainWallet) {
        const fmt = formatCurrency(mainWallet.balance);
        if (mainBalanceHero) mainBalanceHero.textContent = fmt;
        currentWalletId = mainWallet.id;
    }

    updateTransferModal();
}

function updateTransferModal() {
    const fromSelect = document.getElementById('transferFromWallet');
    const toSelect = document.getElementById('transferToWallet');
    if (!fromSelect || !toSelect) return;

    const options = wallets.map(w =>
        `<option value="${w.id}">${escapeHtml(w.name)} Ã¢â‚¬â€ ${formatCurrency(w.balance)}</option>`
    ).join('');

    fromSelect.innerHTML = '<option value="">Pilih wallet asal</option>' + options;
    toSelect.innerHTML = '<option value="">Pilih wallet tujuan</option>' + options;

    if (currentWalletId) {
        fromSelect.value = currentWalletId;
    }

    fromSelect.onchange = function() { filterTransferToWallet(); };
    filterTransferToWallet();
}

function filterTransferToWallet() {
    const fromSelect = document.getElementById('transferFromWallet');
    const toSelect = document.getElementById('transferToWallet');
    if (!fromSelect || !toSelect) return;

    const selectedFrom = fromSelect.value;
    const currentToValue = toSelect.value;

    const filtered = wallets.filter(w => String(w.id) !== String(selectedFrom));
    toSelect.innerHTML = '<option value="">Pilih wallet tujuan</option>' +
        filtered.map(w =>
            `<option value="${w.id}">${escapeHtml(w.name)} Ã¢â‚¬â€ ${formatCurrency(w.balance)}</option>`
        ).join('');

    if (currentToValue && filtered.some(w => String(w.id) === String(currentToValue))) {
        toSelect.value = currentToValue;
    }
}

async function doTransfer() {
    const fromWalletId = document.getElementById('transferFromWallet').value;
    const toWalletId = document.getElementById('transferToWallet').value;
    const amount = parseRupiahToNumber(document.getElementById('transferAmount').value);
    const note = document.getElementById('transferNote').value.trim();
    const btn = document.getElementById('transferBtn');

    if (!fromWalletId || !toWalletId) {
        showToast('Pilih wallet asal dan tujuan', 'error');
        return;
    }
    if (fromWalletId === toWalletId) {
        showToast('Wallet asal dan tujuan harus berbeda', 'error');
        return;
    }
    if (amount <= 0) {
        showToast('Nominal transfer harus lebih dari 0', 'error');
        return;
    }

    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mentransfer...';
    btn.disabled = true;

    try {
        await apiRequest('wallets/transfer', 'POST', {
            from_wallet_id: fromWalletId,
            to_wallet_id: toWalletId,
            amount: amount,
            note: note || 'Transfer antar wallet',
        });
        showToast('Transfer berhasil!', 'success');
        closeModal('transferModal');
        document.getElementById('transferForm')?.reset();
        await loadWallets();
        await loadTransactions();
        await loadPieChart();
    } catch (error) {
        console.error('Transfer error:', error);
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

function renderTransactionsTable(transactions) {
    const tbody = document.getElementById('transactionsTable');
    if (!tbody) return;
    if (transactions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6"><div class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada transaksi</p></div></td></tr>';
        return;
    }
    tbody.innerHTML = transactions.map(trans => `
        <tr class="fade-in">
            <td>${formatDate(trans.transaction_date)}</td>
            <td><span class="badge-${trans.type === 'income' ? 'income' : 'expense'}">${trans.type === 'income' ? 'Pemasukan' : 'Pengeluaran'}</span></td>
            <td>${escapeHtml(trans.category)}</td>
            <td class="${trans.type === 'income' ? 'text-success' : 'text-danger'}">${formatCurrency(trans.amount)}</td>
            <td>${escapeHtml(trans.note || '-')}</td>
            <td>
                <div style="display: flex; gap: 6px;">
                    <button class="btn btn-sm btn-primary" onclick="editTransaction(${trans.id})" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger" onclick="deleteTransaction(${trans.id})" title="Hapus"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Search Filter Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
var filteredTransactions = [];

function filterTransactionTable(query) {
    const tbody = document.getElementById('transactionsTable');
    const noResult = document.getElementById('searchNoResult');
    const clearBtn = document.getElementById('searchClearBtn');
    if (!tbody) return;

    if (!query) {
        renderTransactionsTable(allTransactions);
        if (noResult) noResult.style.display = 'none';
        if (clearBtn) clearBtn.style.display = 'none';
        return;
    }

    const q = query.toLowerCase();
    filteredTransactions = allTransactions.filter(t => {
        const searchText = (
            (t.transaction_date || '') + ' ' +
            (t.type === 'income' ? 'pemasukan' : 'pengeluaran') + ' ' +
            (t.category || '') + ' ' +
            (t.amount ? t.amount.toString() : '') + ' ' +
            (t.note || '') + ' ' +
            formatCurrency(t.amount || 0).replace(/[^a-zA-Z0-9]/g, '')
        ).toLowerCase();
        const cleanQ = q.replace(/[^a-zA-Z0-9]/g, '');
        return searchText.replace(/[^a-zA-Z0-9]/g, '').includes(cleanQ) || searchText.includes(q);
    });

    renderTransactionsTable(filteredTransactions);

    if (noResult) {
        noResult.style.display = filteredTransactions.length === 0 ? 'block' : 'none';
    }
    if (clearBtn) {
        clearBtn.style.display = query ? 'block' : 'none';
    }
}

function setupTransactionSearch() {
    const input = document.getElementById('transactionSearch');
    const clearBtn = document.getElementById('searchClearBtn');
    if (!input) return;

    input.addEventListener('input', function() {
        filterTransactionTable(this.value);
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            filterTransactionTable('');
            this.blur();
        }
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            input.value = '';
            filterTransactionTable('');
            input.focus();
        });
    }
}

function clearTransactionSearch() {
    const input = document.getElementById('transactionSearch');
    if (input) {
        input.value = '';
        filterTransactionTable('');
        input.focus();
    }
}

async function goToPage(page) { if (page < 1 || page > pagination.totalPages) return; await loadTransactions(page); loadPieChart(); }
async function changePageSize(size) { pagination.perPage = parseInt(size); await loadTransactions(1); loadPieChart(); }

function renderPagination() {
    const container = document.getElementById('paginationContainer');
    if (!container) return;
    const { currentPage, totalPages, total, perPage } = pagination;
    if (total === 0) { container.innerHTML = ''; return; }
    const start = ((currentPage - 1) * perPage) + 1;
    const end = Math.min(currentPage * perPage, total);
    const sizeOptions = [5, 10, 25, 50].map(n => `<option value="${n}" ${n === perPage ? 'selected' : ''}>${n}</option>`).join('');
    let pageButtons = '';
    const maxVisible = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);
    if (endPage - startPage < maxVisible - 1) startPage = Math.max(1, endPage - maxVisible + 1);
    if (startPage > 1) {
        pageButtons += `<button class="page-btn" onclick="goToPage(1)">1</button>`;
        if (startPage > 2) pageButtons += `<span class="page-ellipsis">...</span>`;
    }
    for (let i = startPage; i <= endPage; i++) {
        pageButtons += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
    }
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) pageButtons += `<span class="page-ellipsis">...</span>`;
        pageButtons += `<button class="page-btn" onclick="goToPage(${totalPages})">${totalPages}</button>`;
    }
    container.innerHTML = `
        <div class="pagination-wrapper">
            <div class="pagination-left">
                <label class="page-size-label">Tampilkan</label>
                <select class="page-size-select" onchange="changePageSize(this.value)">${sizeOptions}</select>
                <span class="page-size-label">data per halaman</span>
                <span class="pagination-info">&mdash; Menampilkan ${start}-${end} dari ${total} transaksi</span>
            </div>
            <div class="pagination-right">
                <button class="page-btn ${currentPage === 1 ? 'disabled' : ''}" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}><i class="fas fa-chevron-left"></i></button>
                ${pageButtons}
                <button class="page-btn ${currentPage === totalPages ? 'disabled' : ''}" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>`;
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Categories Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
async function loadCategories() {
    try {
        const data = await apiRequest('categories');
        categories = data.categories;
        ['category', 'editCategory'].filter(id => document.getElementById(id)).forEach(selectId => {
            const select = document.getElementById(selectId);
            select.innerHTML = '<option value="">Pilih Kategori</option>';
            categories.forEach(cat => { select.innerHTML += `<option value="${escapeHtml(cat.name)}">${escapeHtml(cat.name)}</option>`; });
            select.innerHTML += '<option value="Lainnya">Lainnya (Input Manual)</option>';
        });
    } catch (error) { console.error('Load categories error:', error); }
}

async function addTransaction(event) {
    if (event) event.preventDefault();
    const submitBtn = document.getElementById('saveTransactionBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner"></span> Menyimpan...';
    submitBtn.disabled = true;
    const amount = parseRupiahToNumber(document.getElementById('amount').value);
    let category = document.getElementById('category').value;
    if (category === 'Lainnya') {
        const manualCategory = document.getElementById('manualCategory')?.value;
        if (!manualCategory) { showToast('Silakan isi kategori manual', 'error'); submitBtn.innerHTML = originalText; submitBtn.disabled = false; return; }
        category = manualCategory;
    }
    const data = {
        transaction_date: document.getElementById('transactionDate').value,
        type: document.getElementById('type').value,
        category: category,
        amount: amount,
        note: document.getElementById('note').value,
        wallet_id: currentWalletId || undefined
    };
    if (!data.transaction_date || !data.type || !data.category || data.amount <= 0) {
        showToast('Semua field wajib diisi dengan benar', 'error');
        submitBtn.innerHTML = originalText; submitBtn.disabled = false; return;
    }
    try {
        await apiRequest('transactions', 'POST', data);
        showToast('Transaksi berhasil ditambahkan', 'success');
        closeModal('transactionModal');
        await loadTransactions();
        loadPieChart();
    } catch (error) { console.error(error); }
    finally { submitBtn.innerHTML = originalText; submitBtn.disabled = false; }
}

async function deleteTransaction(id) {
    const result = await Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Transaksi yang dihapus tidak dapat dikembalikan!",
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal',
        background: '#1a1a2e', color: '#fff'
    });
    if (result.isConfirmed) {
        try {
            await apiRequest(`transactions/${id}`, 'DELETE', { id });
            showToast('Transaksi berhasil dihapus', 'success');
            let page = pagination.currentPage;
            if (allTransactions.length === 1 && page > 1) page = page - 1;
            await loadTransactions(page);
            loadPieChart();
        } catch (error) { console.error(error); }
    }
}

async function editTransaction(id) {
    const transaction = findById(allTransactions, id);
    if (!transaction) { showToast('Transaksi tidak ditemukan', 'error'); return; }
    document.getElementById('editTransactionId').value = transaction.id;
    document.getElementById('editTransactionDate').value = formatDateInput(transaction.transaction_date);
    document.getElementById('editType').value = transaction.type;
    const editCategorySelect = document.getElementById('editCategory');
    const categoryExists = Array.from(editCategorySelect.options).some(opt => opt.value === transaction.category);
    if (!categoryExists && transaction.category) {
        const newOpt = document.createElement('option');
        newOpt.value = transaction.category; newOpt.textContent = transaction.category;
        editCategorySelect.insertBefore(newOpt, editCategorySelect.lastChild?.nextSibling || null);
    }
    editCategorySelect.value = transaction.category;
    const editManualGroup = document.getElementById('editManualCategoryGroup');
    if (editManualGroup) {
        editManualGroup.style.display = (editCategorySelect.value === 'Lainnya') ? 'block' : 'none';
        editCategorySelect.onchange = function() { editManualGroup.style.display = (this.value === 'Lainnya') ? 'block' : 'none'; };
    }
    document.getElementById('editAmount').value = parseInt(transaction.amount).toLocaleString('id-ID');
    document.getElementById('editNote').value = transaction.note || '';
    openModal('editTransactionModal');
}

async function updateTransaction() {
    const id = document.getElementById('editTransactionId').value;
    const amount = parseRupiahToNumber(document.getElementById('editAmount').value);
    let category = document.getElementById('editCategory').value;
    if (category === 'Lainnya') {
        const manualCategory = document.getElementById('editManualCategory')?.value;
        if (!manualCategory) { showToast('Silakan isi kategori manual', 'error'); return; }
        category = manualCategory;
    }
    const data = {
        id: parseInt(id),
        transaction_date: document.getElementById('editTransactionDate').value,
        type: document.getElementById('editType').value,
        category: category,
        amount: amount,
        note: document.getElementById('editNote').value,
        wallet_id: undefined
    };
    try {
        await apiRequest(`transactions/${id}`, 'PUT', data);
        showToast('Transaksi berhasil diupdate', 'success');
        closeModal('editTransactionModal');
        await loadTransactions(pagination.currentPage);
        loadPieChart();
    } catch (error) { console.error(error); }
}

function updateFilterLabel() {
    const label = document.getElementById('filterLabelText');
    const labelWrap = document.getElementById('filterPeriodLabel');
    if (!label) return;
    const chips = document.querySelectorAll('.filter-chip');
    let text;
    if (!currentMonth) {
        text = 'Semua Transaksi';
        chips.forEach(c => c.classList.toggle('active', c.dataset.period === 'all'));
    } else {
        text = monthLabel(currentMonth);
        chips.forEach(c => c.classList.remove('active'));
    }
    label.textContent = text;
    labelWrap.classList.remove('label-update');
    void labelWrap.offsetWidth;
    labelWrap.classList.add('label-update');
}

async function setQuickFilter(period) {
    const monthPicker = document.getElementById('monthPicker');
    const chips = document.querySelectorAll('.filter-chip');
    const now = new Date();
    chips.forEach(c => c.classList.toggle('active', c.dataset.period === period));
    if (period === 'all') {
        monthPicker.value = '';
        currentMonth = null;
    } else if (period === 'this-month') {
        const val = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
        monthPicker.value = val;
        currentMonth = val;
    } else if (period === 'last-month') {
        const d = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        const val = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
        monthPicker.value = val;
        currentMonth = val;
    }
    updateFilterLabel();
    await loadTransactions();
    await loadPieChart();
}

async function changeMonth() {
    const monthPicker = document.getElementById('monthPicker');
    if (monthPicker) {
        currentMonth = monthPicker.value || null;
        updateFilterLabel();
        await loadTransactions();
        loadPieChart();
    }
}

async function loadPieChart() {
    const expenseData = allTransactions.filter(t => t.type === 'expense');
    const incomeData = allTransactions.filter(t => t.type === 'income');
    if (typeof renderExpensePieChart === 'function') await renderExpensePieChart(expenseData);
    if (typeof renderIncomeDoughnutChart === 'function') await renderIncomeDoughnutChart(incomeData);
}

function setType(value) {
    document.getElementById('type').value = value;
    document.querySelectorAll('.type-option').forEach(b => {
        b.classList.toggle('active', b.dataset.value === value);
    });
    // Update category on type change
    const catSelect = document.getElementById('category');
    if (catSelect) catSelect.value = '';
    const manualGroup = document.getElementById('manualCategoryGroup');
    if (manualGroup) manualGroup.style.display = 'none';
}

function openModal(modalId) {
    const el = document.getElementById(modalId);
    if (!el) return;
    
    // Floating modal helper
    const floatingModals = ['transactionModal', 'editTransactionModal', 'reportModal'];
    const cardIds = {
        transactionModal: 'floatingCard',
        editTransactionModal: 'floatingEditCard',
        reportModal: 'floatingReportCard'
    };
    
    if (floatingModals.includes(modalId)) {
        el.classList.add('show');
        const card = document.getElementById(cardIds[modalId]);
        if (card) card.classList.remove('closing');
        
        // Special handling per modal
        if (modalId === 'transactionModal') {
            document.getElementById('transactionForm')?.reset();
            document.getElementById('transactionDate').value = new Date().toISOString().slice(0, 10);
            const mg = document.getElementById('manualCategoryGroup');
            if (mg) mg.style.display = 'none';
            setType('income');
            const categorySelect = document.getElementById('category');
            if (categorySelect) {
                categorySelect.onchange = function() {
                    const manualGroup = document.getElementById('manualCategoryGroup');
                    if (manualGroup) manualGroup.style.display = this.value === 'Lainnya' ? 'block' : 'none';
                };
            }
        }
        
        if (modalId === 'editTransactionModal') {
            const editCatSelect = document.getElementById('editCategory');
            if (editCatSelect) {
                editCatSelect.onchange = function() {
                    const manualGroup = document.getElementById('editManualCategoryGroup');
                    if (manualGroup) manualGroup.style.display = this.value === 'Lainnya' ? 'block' : 'none';
                };
            }
        }
        
        // Re-trigger stagger animations by reflow
        if (card) {
            const fields = card.querySelectorAll('.floating-field, .floating-actions');
            fields.forEach(f => {
                f.style.animation = 'none';
                void f.offsetWidth;
                f.style.animation = '';
            });
        }
        
        initRupiahFormatting();
        return;
    }
    
    // Regular modals
    el.style.display = 'flex';
}

function closeModal(modalId) {
    const el = document.getElementById(modalId);
    if (!el) return;
    
    // Floating modal handler
    const floatingModals = ['transactionModal', 'editTransactionModal', 'reportModal'];
    const cardIds = {
        transactionModal: 'floatingCard',
        editTransactionModal: 'floatingEditCard',
        reportModal: 'floatingReportCard'
    };
    
    if (floatingModals.includes(modalId)) {
        const card = document.getElementById(cardIds[modalId]);
        if (card) {
            card.classList.add('closing');
            setTimeout(() => {
                el.classList.remove('show');
                card.classList.remove('closing');
            }, 280);
        } else {
            el.classList.remove('show');
        }
        return;
    }
    
    el.style.display = 'none';
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Export Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
async function exportToExcel() {
    if (typeof XLSX === 'undefined') { showToast('Library Excel belum dimuat', 'error'); return; }
    showToast('Menyiapkan data...', 'info');
    try {
        let endpoint = `transactions?page=1&limit=10000`;
        if (currentWalletId) endpoint += `&wallet_id=${currentWalletId}`;
        const data = await apiRequest(endpoint, 'GET', null, { silent: true });
        const transactions = data.transactions || [];
        if (transactions.length === 0) { showToast('Tidak ada transaksi untuk diekspor', 'error'); return; }

        const exportData = transactions.map(t => ({
            'Tanggal': t.transaction_date ? new Date(t.transaction_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-',
            'Jenis': t.type === 'income' ? 'Pemasukan' : 'Pengeluaran',
            'Kategori': t.category,
            'Nominal': t.amount,
            'Catatan': t.note || '-'
        }));

        const ws = XLSX.utils.json_to_sheet(exportData);

        // Column widths
        ws['!cols'] = [
            { wch: 22 }, // Tanggal
            { wch: 14 }, // Jenis
            { wch: 20 }, // Kategori
            { wch: 18 }, // Nominal
            { wch: 30 }, // Catatan
        ];

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Riwayat Transaksi');
        XLSX.writeFile(wb, `transaksi_${new Date().toISOString().slice(0, 10)}.xlsx`);
        showToast('File Excel berhasil diunduh', 'success');
    } catch (error) {
        console.error('Export Excel error:', error);
        showToast('Gagal mengekspor Excel', 'error');
    }
}

async function exportToPDF() {
    if (typeof window.jspdf === 'undefined') { showToast('Library PDF belum dimuat', 'error'); return; }
    showToast('Menyiapkan data...', 'info');
    try {
        let endpoint = `transactions?page=1&limit=10000`;
        if (currentWalletId) endpoint += `&wallet_id=${currentWalletId}`;
        const data = await apiRequest(endpoint, 'GET', null, { silent: true });
        const transactions = data.transactions || [];
        if (transactions.length === 0) { showToast('Tidak ada transaksi untuk diekspor', 'error'); return; }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');
        const pageW = doc.internal.pageSize.getWidth();
        const pageH = doc.internal.pageSize.getHeight();
        const marginL = 14;
        const contentW = pageW - 28;

        // Ã¢â€â‚¬Ã¢â€â‚¬ Summary Ã¢â€â‚¬Ã¢â€â‚¬
        const totalIncome = transactions.filter(t => t.type === 'income').reduce((a, t) => a + parseFloat(t.amount), 0);
        const totalExpense = transactions.filter(t => t.type === 'expense').reduce((a, t) => a + parseFloat(t.amount), 0);
        const balance = totalIncome - totalExpense;

        const todayStr = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        const userName = document.getElementById('userName').textContent;

        // Ã¢â€â‚¬Ã¢â€â‚¬ Header Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        doc.setFillColor(15, 23, 42);
        doc.rect(0, 0, pageW, 32, 'F');

        // Gold accent bar
        doc.setFillColor(240, 180, 41);
        doc.rect(0, 30, pageW, 2, 'F');

        doc.setTextColor(255);
        doc.setFontSize(18);
        doc.setFont(undefined, 'bold');
        doc.text('Laporan Transaksi', pageW / 2, 12, { align: 'center' });

        doc.setFontSize(8);
        doc.setFont(undefined, 'normal');
        doc.setTextColor(200);
        doc.text(userName, pageW / 2, 20, { align: 'center' });

        doc.setFontSize(7);
        doc.setTextColor(148, 163, 184);
        doc.text(`Dompet Digital  \u2022  ${todayStr}`, pageW / 2, 27, { align: 'center' });

        // Ã¢â€â‚¬Ã¢â€â‚¬ Summary Cards Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
        const cardW = (contentW - 16) / 3;
        const cardY = 38;
        const cardH = 24;

        const drawCard = (x, label, value, bgColor, borderColor, textColor) => {
            doc.setFillColor(...bgColor);
            doc.setDrawColor(...borderColor);
            doc.roundedRect(x, cardY, cardW, cardH, 4, 4, 'FD');

            doc.setFontSize(7);
            doc.setFont(undefined, 'bold');
            doc.setTextColor(107, 114, 128);
            doc.text(label, x + cardW / 2, cardY + 7, { align: 'center' });

            doc.setFontSize(11);
            doc.setFont(undefined, 'bold');
            doc.setTextColor(...textColor);
            doc.text(value, x + cardW / 2, cardY + 18, { align: 'center' });
        };

        drawCard(marginL, 'PEMASUKAN', formatCurrency(totalIncome), [236, 253, 245], [167, 243, 208], [16, 185, 129]);
        drawCard(marginL + cardW + 8, 'PENGELUARAN', formatCurrency(totalExpense), [254, 242, 242], [254, 202, 202], [239, 68, 68]);
        drawCard(
            marginL + (cardW + 8) * 2,
            'SALDO',
            formatCurrency(balance),
            balance >= 0 ? [254, 249, 195] : [254, 242, 242],
            balance >= 0 ? [253, 230, 138] : [254, 202, 202],
            balance >= 0 ? [16, 185, 129] : [239, 68, 68]
        );

        // Ã¢â€â‚¬Ã¢â€â‚¬ Transaction count Ã¢â€â‚¬Ã¢â€â‚¬
        doc.setFontSize(8);
        doc.setFont(undefined, 'normal');
        doc.setTextColor(156, 163, 175);
        doc.text(`\u2501  ${transactions.length} transaksi tercatat  \u2501`, pageW / 2, cardY + 32, { align: 'center' });

        // Ã¢â€â‚¬Ã¢â€â‚¬ Divider Ã¢â€â‚¬Ã¢â€â‚¬
        doc.setDrawColor(226, 232, 240);
        doc.setLineWidth(0.5);
        doc.line(marginL, cardY + 40, pageW - marginL, cardY + 40);
        doc.setLineWidth(0.2);

        // Ã¢â€â‚¬Ã¢â€â‚¬ Running balance Ã¢â€â‚¬Ã¢â€â‚¬
        const sortedAsc = [...transactions].sort((a, b) =>
            new Date(a.transaction_date) - new Date(b.transaction_date) || a.id - b.id
        );
        let runningBalance = 0;
        const balanceMap = {};
        sortedAsc.forEach(t => {
            const amt = parseFloat(t.amount);
            runningBalance += t.type === 'income' ? amt : -amt;
            balanceMap[t.id] = runningBalance;
        });

        // Ã¢â€â‚¬Ã¢â€â‚¬ Table Ã¢â€â‚¬Ã¢â€â‚¬
        const tableRows = transactions.map(t => [
            t.transaction_date ? new Date(t.transaction_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-',
            t.type === 'income' ? 'Pemasukan' : 'Pengeluaran',
            t.category,
            formatCurrency(t.amount),
            formatCurrency(balanceMap[t.id] || 0),
            t.note || '-'
        ]);

        doc.autoTable({
            head: [['Tanggal', 'Jenis', 'Kategori', 'Nominal', 'Saldo', 'Catatan']],
            body: tableRows,
            startY: cardY + 44,
            theme: 'grid',
            headStyles: {
                fillColor: [30, 41, 59],
                textColor: 255,
                fontSize: 7.5,
                fontStyle: 'bold',
                halign: 'center',
                lineWidth: 0,
                cellPadding: 3.5,
            },
            bodyStyles: {
                fontSize: 7,
                textColor: [55, 65, 81],
                lineWidth: 0.3,
                lineColor: [226, 232, 240],
            },
            alternateRowStyles: {
                fillColor: [248, 250, 252],
            },
            styles: {
                cellPadding: 3,
                font: undefined,
            },
            columnStyles: {
                0: { cellWidth: 26, halign: 'center' },
                1: { cellWidth: 20, halign: 'center' },
                2: { cellWidth: 30 },
                3: { cellWidth: 28, halign: 'right' },
                4: { cellWidth: 28, halign: 'right' },
                5: { cellWidth: 'auto' },
            },
            margin: { left: marginL, right: marginL },
            tableLineColor: [226, 232, 240],
            tableLineWidth: 0.3,
        });

        // Ã¢â€â‚¬Ã¢â€â‚¬ Footer Summary Ã¢â€â‚¬Ã¢â€â‚¬
        const finalY = doc.lastAutoTable.finalY || cardY + 44;
        let footY = finalY + 12;

        if (footY > pageH - 38) {
            doc.addPage();
            footY = 20;
        }

        // Thin separator
        doc.setDrawColor(226, 232, 240);
        doc.setLineWidth(0.4);
        doc.line(marginL, footY, pageW - marginL, footY);
        footY += 6;

        // Summary bar
        doc.setFillColor(248, 250, 252);
        doc.setDrawColor(203, 213, 225);
        doc.roundedRect(marginL, footY, contentW, 20, 4, 4, 'FD');

        doc.setFontSize(7);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(100, 116, 139);
        doc.text('RINGKASAN', marginL + 6, footY + 7);

        doc.setFontSize(8);
        doc.setFont(undefined, 'normal');
        doc.setTextColor(71, 85, 105);
        doc.text(
            `${transactions.length} transaksi  |  Pemasukan: ${formatCurrency(totalIncome)}  |  Pengeluaran: ${formatCurrency(totalExpense)}  |  Saldo: ${formatCurrency(balance)}`,
            pageW / 2, footY + 14, { align: 'center' }
        );

        // Ã¢â€â‚¬Ã¢â€â‚¬ Page Numbers Ã¢â€â‚¬Ã¢â€â‚¬
        const totalPgs = doc.internal.getNumberOfPages();
        for (let i = 1; i <= totalPgs; i++) {
            doc.setPage(i);
            doc.setDrawColor(226, 232, 240);
            doc.setLineWidth(0.3);
            doc.line(marginL, pageH - 14, pageW - marginL, pageH - 14);

            doc.setFontSize(7);
            doc.setTextColor(156, 163, 175);
            doc.text(
                `Dompet Digital  \u2022  Halaman ${i} dari ${totalPgs}`,
                pageW / 2, pageH - 7, { align: 'center' }
            );
        }

        doc.save(`laporan_transaksi_${new Date().toISOString().slice(0, 10)}.pdf`);
        showToast('File PDF berhasil diunduh', 'success');
    } catch (error) {
        console.error('Export PDF error:', error);
        showToast('Gagal mengekspor PDF', 'error');
    }
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Charts Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
var expenseColors = ['#ef4444','#f97316','#f59e0b','#8b5cf6','#3b82f6','#ec4899','#14b8a6','#6366f1'];
var expenseHoverColors = ['#dc2626','#ea580c','#d97706','#7c3aed','#2563eb','#db2777','#0d9488','#4f46e5'];
var incomeColors = ['#10b981','#059669','#34d399','#047857','#6ee7b7','#065f46','#a7f3d0','#046c4e'];
var incomeHoverColors = ['#059669','#047857','#10b981','#065f46','#34d399','#046c4e','#6ee7b7','#064e3b'];

function getChartFont(isDark) {
    return {
        family: "'Segoe UI', Roboto, 'Helvetica Neue', sans-serif",
        size: 11,
        weight: '500'
    };
}

function getChartTooltipOpts(isDark) {
    return {
        backgroundColor: isDark ? 'rgba(30,27,58,0.95)' : 'rgba(255,255,255,0.95)',
        titleColor: isDark ? 'rgba(255,255,255,0.9)' : '#1e293b',
        bodyColor: isDark ? 'rgba(255,255,255,0.75)' : '#475569',
        borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
        borderWidth: 1,
        borderRadius: 12,
        padding: 12,
        boxPadding: 6,
        usePointStyle: true,
    };
}

function renderChart(canvasId, chartInstance, transactions, isExpense) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    const isDark = document.body.classList.contains('dark-mode');
    const labelColor = isDark ? 'rgba(255,255,255,0.65)' : 'rgba(71,85,105,0.8)';
    const colors = isExpense ? expenseColors : incomeColors;
    const hoverColors = isExpense ? expenseHoverColors : incomeHoverColors;
    const categoryMap = new Map();
    transactions.forEach(trans => {
        const cat = trans.category;
        const amount = parseFloat(trans.amount);
        categoryMap.set(cat, (categoryMap.get(cat) || 0) + amount);
    });
    const categories = Array.from(categoryMap.keys());
    const amounts = Array.from(categoryMap.values());
    if (chartInstance) chartInstance.destroy();
    if (typeof Chart === 'undefined') return;
    const bgColors = categories.map((_, i) => colors[i % colors.length]);
    const hvColors = categories.map((_, i) => hoverColors[i % hoverColors.length]);
    const newChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categories,
            datasets: [{
                data: amounts,
                backgroundColor: bgColors,
                hoverBackgroundColor: hvColors,
                borderWidth: 4,
                borderColor: isDark ? 'rgba(30,27,58,0.8)' : 'rgba(255,255,255,0.9)',
                hoverBorderColor: isDark ? 'rgba(30,27,58,0.9)' : 'rgba(255,255,255,1)',
                hoverOffset: 12,
                borderRadius: 4,
                spacing: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '68%',
            animation: {
                animateRotate: true,
                duration: 800,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: labelColor,
                        font: getChartFont(isDark),
                        padding: 16,
                        usePointStyle: true,
                        pointStyle: 'rectRounded',
                        borderRadius: 4
                    }
                },
                tooltip: {
                    ...getChartTooltipOpts(isDark),
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return ` ${label}: ${formatCurrency(value)} (${percent}%)`;
                        }
                    }
                }
            }
        }
    });
    return newChart;
}

async function renderExpensePieChart(expenseTransactions) {
    if (expenseChart) expenseChart.destroy();
    expenseChart = await renderChart('expensePieChart', null, expenseTransactions, true);
}

async function renderIncomeDoughnutChart(incomeTransactions) {
    if (incomeChart) incomeChart.destroy();
    incomeChart = await renderChart('incomeDoughnutChart', null, incomeTransactions, false);
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Dark Mode Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
function loadThemePreference() {
    const savedTheme = localStorage.getItem('darkMode');
    const isDark = savedTheme !== 'disabled';
    if (isDark) document.body.classList.add('dark-mode');
    else document.body.classList.remove('dark-mode');
    const darkModeBtn = document.getElementById('darkModeToggle');
    if (darkModeBtn) darkModeBtn.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
}

function updateDarkModeIcon(isDark) {
    const darkModeBtn = document.getElementById('darkModeToggle');
    if (darkModeBtn) darkModeBtn.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
}

function updateChartTheme(chart) {
    if (!chart) return;
    const isDark = document.body.classList.contains('dark-mode');
    chart.options.plugins.legend.labels.color = isDark ? 'rgba(255,255,255,0.65)' : 'rgba(71,85,105,0.8)';
    const tooltipOpts = getChartTooltipOpts(isDark);
    Object.assign(chart.options.plugins.tooltip, tooltipOpts);
    chart.update();
}

function toggleDarkMode() {
    const isDark = !document.body.classList.contains('dark-mode');
    document.body.classList.toggle('dark-mode', isDark);
    localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
    updateDarkModeIcon(isDark);
    updateChartTheme(expenseChart);
    updateChartTheme(incomeChart);
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
document.addEventListener('click', function(event) {
    if (window.innerWidth > 768) return;
    const s = document.getElementById('sidebar'); const t = document.querySelector('.menu-toggle');
    if (s?.classList.contains('open') && !s.contains(event.target) && !t?.contains(event.target)) closeSidebar();
});

// Ã¢â€â‚¬Ã¢â€â‚¬ Profile Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
function openProfileModal() {
    if (!currentUser) return;
    document.getElementById('profileFullName').value = currentUser.full_name || '';
    document.getElementById('profileUsername').value = currentUser.username || '';
    document.getElementById('currentPassword').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
}


async function saveProfile() {
    const fullName = document.getElementById('profileFullName').value.trim();
    const username = document.getElementById('profileUsername').value.trim();
    const btn = document.getElementById('saveProfileBtn');
    if (!fullName || !username) { showToast('Nama dan username wajib diisi', 'error'); return; }
    if (username.length < 3) { showToast('Username minimal 3 karakter', 'error'); return; }
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...'; btn.disabled = true;
    try {
        const data = await apiRequest('user/profile', 'PUT', { full_name: fullName, username: username });
        showToast(data.message || 'Profil berhasil diperbarui', 'success');
        currentUser.full_name = fullName; currentUser.username = username;
        localStorage.setItem('user', JSON.stringify(currentUser));
        document.getElementById('userName').textContent = fullName;
    } catch (error) { console.error(error); }
    finally { btn.innerHTML = originalText; btn.disabled = false; }
}

async function savePassword() {
    const currentPass = document.getElementById('currentPassword').value;
    const newPass = document.getElementById('newPassword').value;
    const confirmPass = document.getElementById('confirmPassword').value;
    const btn = document.getElementById('savePasswordBtn');
    if (!currentPass || !newPass) { showToast('Password lama dan baru wajib diisi', 'error'); return; }
    if (newPass.length < PASSWORD_MIN_LENGTH) { showToast(`Password baru minimal ${PASSWORD_MIN_LENGTH} karakter`, 'error'); return; }
    if (newPass !== confirmPass) { showToast('Konfirmasi password tidak cocok', 'error'); return; }
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengubah...'; btn.disabled = true;
    try {
        const data = await apiRequest('user/password', 'PUT', { current_password: currentPass, new_password: newPass });
        showToast(data.message || 'Password berhasil diubah', 'success');
        document.getElementById('currentPassword').value = '';
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
    } catch (error) { console.error(error); }
    finally { btn.innerHTML = originalText; btn.disabled = false; }
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Init Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬
(function() {
    async function initDompet() {
        loadThemePreference();
        const darkModeBtn = document.getElementById('darkModeToggle');
        if (darkModeBtn) darkModeBtn.addEventListener('click', toggleDarkMode);
        const pm = document.getElementById('profileModal');
        currentUser = await checkAuth();
        if (!currentUser) return;

        document.getElementById('userName').textContent = currentUser.full_name || currentUser.username;
        renderUserAvatar(currentUser.avatar || 0);

        const dateInput = document.getElementById('transactionDate');
        if (dateInput) dateInput.value = new Date().toISOString().slice(0, 10);
        const monthPicker = document.getElementById('monthPicker');
        if (monthPicker) monthPicker.value = '';
        await loadCategories();
        await loadWallets();
        await loadTransactions();
        await loadPieChart();
        initRupiahFormatting();
        updateFilterLabel();
        setupTransactionSearch();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDompet);
    } else {
        initDompet();
    }
})();
</script>

<script>
function showProfilePage() {
    window.location.href = '/profile';
}
</script>@endpush
