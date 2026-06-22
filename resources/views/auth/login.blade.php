@extends('layouts.app')

@section('title', 'Login - Dompet Digital')

@push('styles')
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
        background-size: 200% 200%;
        animation: gentleFlow 12s ease infinite;
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        display: flex; flex-direction: column;
    }
    @keyframes gentleFlow {
        0% { background-position: 0% 0%; }
        50% { background-position: 100% 100%; }
        100% { background-position: 0% 0%; }
    }
    .auth-container { width: 100%; padding: 20px; flex: 1; display: flex; align-items: center; justify-content: center; }
    .auth-card {
        position: relative;
        overflow: hidden;
        max-width: 400px; margin: 0 auto;
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(16px);
        border-radius: 28px;
        padding: 2.2rem 2rem 1.8rem;
        box-shadow: 0 15px 40px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.12);
        animation: authFadeIn 0.6s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .auth-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #f0b429, #ff8c42 50%, transparent);
        opacity: 0.7;
    }
    @keyframes authFadeIn {
        0% { opacity: 0; transform: translateY(20px) scale(0.97); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    .auth-glow {
        position: absolute;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(240,180,41,0.15) 0%, transparent 70%);
        right: -60px;
        top: -60px;
        pointer-events: none;
    }
    .auth-header { text-align: center; margin-bottom: 1.8rem; position: relative; z-index: 1; }
    .auth-icon-wrap {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        margin: 0 auto 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(240,180,41,0.2), rgba(240,180,41,0.05));
        border: 1px solid rgba(240,180,41,0.2);
    }
    .auth-icon-wrap i { font-size: 28px; color: #f0b429; margin: 0; }
    .auth-header h1 {
        font-size: 1.6rem;
        background: linear-gradient(90deg, #f0b429, #ff8c42);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0 0 4px;
        font-weight: 700;
        letter-spacing: -0.03em;
    }
    .auth-header p { color: rgba(255,255,255,0.55); font-size: 0.82rem; margin: 0; }
    .form-group { margin-bottom: 1.1rem; }
    .form-group label {
        display: block; color: rgba(255,255,255,0.8);
        font-size: 0.78rem; margin-bottom: 5px; font-weight: 500;
    }
    .form-group label i { margin-right: 6px; font-size: 0.75rem; color: #f0b429; }
    .form-control {
        width: 100%; padding: 11px 16px;
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 40px;
        color: white;
        font-size: 0.88rem;
        outline: none;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .form-control:focus {
        border-color: #f0b429;
        background: rgba(255,255,255,0.12);
        box-shadow: 0 0 0 3px rgba(240,180,41,0.15), 0 0 15px rgba(240,180,41,0.06);
    }
    .form-control::placeholder { color: rgba(255,255,255,0.35); }
    .btn-primary {
        width: 100%; padding: 12px 16px;
        background: linear-gradient(90deg, #f0b429, #ff8c42);
        border: none; border-radius: 40px; color: #1e1a0c;
        font-weight: 700; font-size: 0.92rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        letter-spacing: 0.3px;
        position: relative;
        overflow: hidden;
    }
    .btn-primary::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transform: translateX(-100%);
        transition: transform 0.5s ease;
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(240,180,41,0.3);
    }
    .btn-primary:hover::after { transform: translateX(100%); }
    .btn-primary:active { transform: translateY(0) scale(0.98); }
    .spinner {
        width: 16px; height: 16px;
        border: 2px solid rgba(0,0,0,0.2);
        border-top-color: #1e1a0c; border-radius: 50%;
        display: inline-block; animation: spin 0.7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .auth-link { color: #f0b429; text-decoration: none; font-weight: 600; transition: opacity 0.2s; }
    .auth-link:hover { opacity: 0.8; text-decoration: underline; }
    .footer-text { text-align: center; margin-top: 22px; font-size: 0.78rem; color: rgba(255,255,255,0.5); position: relative; z-index: 1; }
    .alert {
        padding: 11px 16px; border-radius: 40px; margin-bottom: 16px;
        font-size: 0.8rem; text-align: center; display: flex;
        align-items: center; justify-content: center; gap: 8px;
        animation: authFadeIn 0.3s ease;
    }
    .alert i { font-size: 0.85rem; flex-shrink: 0; }
    .alert-error { background: rgba(239,68,68,0.15); color: #fca5a5; border: 1px solid rgba(239,68,68,0.2); }
    .alert-success { background: rgba(16,185,129,0.15); color: #6ee7b7; border: 1px solid rgba(16,185,129,0.2); }
    @media (max-width: 480px) {
        .auth-card { padding: 1.6rem 1.4rem 1.4rem; border-radius: 22px; }
        .auth-icon-wrap { width: 52px; height: 52px; border-radius: 16px; }
        .auth-icon-wrap i { font-size: 22px; }
        .auth-header h1 { font-size: 1.35rem; }
        .auth-header p { font-size: 0.78rem; }
        .form-group { margin-bottom: 0.9rem; }
        .form-group label { font-size: 0.75rem; }
        .form-control { padding: 10px 14px; font-size: 0.83rem; }
        .btn-primary { padding: 10px 14px; font-size: 0.85rem; }
        .auth-container { padding: 12px; }
    }
</style>
@endpush

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-glow"></div>
        <div class="auth-header">
            <div class="auth-icon-wrap"><i class="fas fa-wallet"></i></div>
            <h1>Dompet Digital</h1>
            <p>Silakan login untuk melanjutkan</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first('login') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}" id="loginForm">
            @csrf
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username</label>
                <input type="text" class="form-control" name="username" required placeholder="Masukkan username">
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" class="form-control" name="password" required placeholder="Masukkan password">
            </div>
            <button type="submit" class="btn-primary">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <div class="footer-text">
            Belum punya akun? <a href="{{ url('/register') }}" class="auth-link">Daftar di sini</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    btn.innerHTML = '<span class="spinner"></span> Memproses...';
    btn.disabled = true;
});
</script>
@endpush
@endsection
