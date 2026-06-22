<div id="profilePageSection" style="display:none;">
    <div class="section-header">
        <h2><i class="fas fa-user-cog"></i> Pengaturan Profil</h2>
        <p>Kelola data profil Anda</p>
    </div>
    <div class="stats-grid" style="max-width:600px;grid-template-columns:1fr;">
        <div class="card" style="padding:24px;">
            <form id="profileFormUser">
                @csrf
                <div class="form-group">
                    <label>Nama Lengkap <span style="color:var(--danger)">*</span></label>
                    <input type="text" class="form-control" id="profileFullNameUser" required placeholder="Masukkan nama lengkap">
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" id="profileUsernameUser" disabled style="color:var(--text-muted);">
                </div>
                <button type="submit" class="btn btn-primary" id="saveProfileBtnUser"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </form>
            <hr style="border-color:var(--glass-border);margin:28px 0;">
            <h4 style="color:var(--text-secondary);margin-bottom:16px;"><i class="fas fa-key"></i> Ganti Password</h4>
            <form id="passwordFormUser">
                @csrf
                <div class="form-group">
                    <label>Password Lama <span style="color:var(--danger)">*</span></label>
                    <input type="password" class="form-control" id="currentPasswordUser" required placeholder="Masukkan password lama">
                </div>
                <div class="form-group">
                    <label>Password Baru <span style="color:var(--danger)">*</span></label>
                    <input type="password" class="form-control" id="newPasswordUser" required minlength="8" placeholder="Minimal 8 karakter">
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password Baru <span style="color:var(--danger)">*</span></label>
                    <input type="password" class="form-control" id="confirmPasswordUser" required placeholder="Ulangi password baru">
                </div>
                <button type="submit" class="btn btn-primary" id="savePasswordBtnUser"><i class="fas fa-key"></i> Ganti Password</button>
            </form>
        </div>
    </div>
</div>
