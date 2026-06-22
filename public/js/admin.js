// Admin Panel Dompet Digital
// ==========================

var API_BASE = window.DOMpetConfig.apiBase;
var PASSWORD_MIN_LENGTH = 8;
let currentUser = null;
let allUsers = [];
let userPagination = { currentPage: 1, perPage: 10, total: 0, totalPages: 0 };

// Utilities (shared functions via app-utils.js)

function matchId(a, b) { return Number(a) === Number(b); }
function findById(items, id) { return items.find(item => matchId(item.id, id)); }

// API (shared via app-utils.js)

// Sidebar click-outside

document.addEventListener('click', function(event) {
    if (window.innerWidth > 768) return;
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.querySelector('.menu-toggle');
    if (sidebar && sidebar.classList.contains('open')) { if (!sidebar.contains(event.target) && !menuToggle?.contains(event.target)) closeSidebar(); }
});

// Init

async function initAdmin() {
    currentUser = await checkAuth();
    if (!currentUser) return;
    if (currentUser.role !== 'admin') {
        Swal.fire({ icon: 'error', title: 'Akses Ditolak', text: 'Halaman ini hanya untuk admin', background: '#1a1a2e', color: '#fff' }).then(() => { window.location.href = '/dashboard'; });
        return;
    }
    document.getElementById('adminName').textContent = currentUser.full_name;
    await loadAdminStats();
    await loadUsers();
    hideSplashScreen();
}

function hideSplashScreen() {
    const splash = document.getElementById('splashScreen');
    if (splash) {
        clearInterval(window.splashDotInterval);
        splash.style.opacity = '0';
        splash.style.visibility = 'hidden';
        splash.style.pointerEvents = 'none';
        setTimeout(() => { if (splash.parentNode) splash.parentNode.removeChild(splash); }, 700);
    }
}

// Stats & Charts

async function loadAdminStats() {
    try {
        const data = await apiRequest('admin/stats');
        document.getElementById('totalUsers').textContent = data.stats.total_users;
        document.getElementById('totalTransactions').textContent = data.stats.total_transactions;
        document.getElementById('totalIncome').textContent = formatCurrency(data.stats.total_income);
        document.getElementById('totalExpense').textContent = formatCurrency(data.stats.total_expense);
        
        const mainBalEl = document.getElementById('totalMainBalance');
        const savBalEl = document.getElementById('totalSavingsBalance');
        if (mainBalEl) mainBalEl.textContent = formatCurrency(data.stats.total_main_balance || 0);
        if (savBalEl) savBalEl.textContent = formatCurrency(data.stats.total_savings_balance || 0);
        
        renderAdminExpenseChart(data.chart_data);
        renderAdminIncomeChart(data.chart_data);
        if (data.wallet_chart_data) renderAdminWalletChart(data.wallet_chart_data);
    } catch (error) { console.error('Load stats error:', error); }
}

let adminExpenseChartInstance = null;
let adminIncomeChartInstance = null;
let adminWalletChartInstance = null;
let lastChartData = null;
function refreshAdminChart() { if (lastChartData) { renderAdminExpenseChart(lastChartData); renderAdminIncomeChart(lastChartData); } }

function renderAdminWalletChart(data) {
    const canvas = document.getElementById('walletBarChart');
    if (!canvas) return;
    const oldFallback = document.getElementById('walletBarChartFallback');
    if (oldFallback) oldFallback.remove();
    canvas.style.display = 'block';
    if (adminWalletChartInstance) { adminWalletChartInstance.destroy(); adminWalletChartInstance = null; }
    const sorted = [...data].sort((a, b) => (parseFloat(b.main_balance) + parseFloat(b.savings_balance)) - (parseFloat(a.main_balance) + parseFloat(a.savings_balance)));
    const labels = sorted.map(item => item.full_name || item.username);
    const mainValues = sorted.map(item => parseFloat(item.main_balance || 0));
    const savingsValues = sorted.map(item => parseFloat(item.savings_balance || 0));
    if (labels.length === 0) {
        canvas.style.display = 'none';
        let fallback = document.createElement('div');
        fallback.id = 'walletBarChartFallback';
        fallback.innerHTML = '<div class="empty-state"><i class="fas fa-wallet"></i><p>Belum ada data saldo dompet</p></div>';
        canvas.insertAdjacentElement('afterend', fallback);
        return;
    }
    const isDark = document.body.classList.contains('dark-mode');
    const textColor = isDark ? '#e2e8f0' : '#334155';
    const gridColor = isDark ? 'rgba(148,163,184,0.12)' : 'rgba(148,163,184,0.2)';
    const containerHeight = Math.max(220, labels.length * 48 + 80);
    canvas.parentElement.style.minHeight = containerHeight + 'px';
    canvas.parentElement.style.height = containerHeight + 'px';
    const ctx = canvas.getContext('2d');
    adminWalletChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Dompet Utama', data: mainValues, backgroundColor: isDark ? 'rgba(240,180,41,0.75)' : 'rgba(212,148,10,0.8)', borderColor: isDark ? '#f0b429' : '#d4940a', borderWidth: 1.5, borderRadius: 4, barThickness: labels.length > 5 ? 18 : 24 },
                { label: 'Tabungan', data: savingsValues, backgroundColor: isDark ? 'rgba(16,185,129,0.75)' : 'rgba(16,185,129,0.8)', borderColor: '#10b981', borderWidth: 1.5, borderRadius: 4, barThickness: labels.length > 5 ? 18 : 24 }
            ]
        },
        options: {
            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
            layout: { padding: { right: 24, top: 8 } },
            plugins: {
                legend: { position: 'bottom', labels: { color: textColor, font: { size: 11, weight: '600' }, padding: 16, usePointStyle: true, pointStyle: 'circle' } },
                tooltip: { backgroundColor: isDark ? '#1e293b' : '#ffffff', titleColor: isDark ? '#f1f5f9' : '#1e293b', bodyColor: isDark ? '#e2e8f0' : '#334155', borderColor: isDark ? '#334155' : '#e2e8f0', borderWidth: 1, padding: 12, cornerRadius: 8, displayColors: true, callbacks: { label: function(context) { return context.dataset.label + ': Rp ' + context.parsed.x.toLocaleString('id-ID'); } } }
            },
            scales: {
                x: { beginAtZero: true, stacked: false, grid: { color: gridColor, drawBorder: false }, ticks: { color: textColor, font: { size: 10 }, callback: function(value) { if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt'; if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb'; return 'Rp ' + value; } } },
                y: { grid: { display: false }, ticks: { color: textColor, font: { size: 11, weight: '600' }, crossAlign: 'far' } }
            },
            animation: { duration: 700, easing: 'easeOutQuart' }
        }
    });
}

function renderAdminIncomeChart(data) {
    lastChartData = data;
    const canvas = document.getElementById('incomeBarChart');
    if (!canvas) return;
    const oldFallback = document.getElementById('incomeBarChartFallback');
    if (oldFallback) oldFallback.remove();
    canvas.style.display = 'block';
    if (adminIncomeChartInstance) { adminIncomeChartInstance.destroy(); adminIncomeChartInstance = null; }
    const sorted = [...data].sort((a, b) => parseFloat(b.total_income || 0) - parseFloat(a.total_income || 0));
    const labels = sorted.map(item => item.full_name || item.username);
    const values = sorted.map(item => parseFloat(item.total_income || 0));
    if (labels.length === 0) {
        canvas.style.display = 'none';
        let fallback = document.createElement('div');
        fallback.id = 'incomeBarChartFallback';
        fallback.innerHTML = '<div class="empty-state"><i class="fas fa-chart-bar"></i><p>Belum ada data pemasukan</p></div>';
        canvas.insertAdjacentElement('afterend', fallback);
        return;
    }
    const isDark = document.body.classList.contains('dark-mode');
    const textColor = isDark ? '#e2e8f0' : '#334155';
    const gridColor = isDark ? 'rgba(148,163,184,0.12)' : 'rgba(148,163,184,0.2)';
    const maxVal = Math.max(...values, 1);
    const barColors = values.map(v => { const r = v / maxVal; return `rgba(${Math.round(16 + (52-16)*(1-r))}, ${Math.round(185 + (211-185)*(1-r))}, ${Math.round(129 + (153-129)*(1-r))}, 0.85)`; });
    const barBorders = values.map(v => { const r = v / maxVal; return `rgba(${Math.round(16 + (52-16)*(1-r))}, ${Math.round(185 + (211-185)*(1-r))}, ${Math.round(129 + (153-129)*(1-r))}, 1)`; });
    const containerHeight = Math.max(200, labels.length * 42 + 80);
    canvas.parentElement.style.minHeight = containerHeight + 'px';
    canvas.parentElement.style.height = containerHeight + 'px';
    const ctx = canvas.getContext('2d');
    adminIncomeChartInstance = new Chart(ctx, {
        type: 'bar',
        data: { labels: labels, datasets: [{ label: 'Total Pemasukan', data: values, backgroundColor: barColors, borderColor: barBorders, borderWidth: 1.5, borderRadius: 6, barThickness: labels.length > 5 ? 28 : 36 }] },
        options: {
            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
            layout: { padding: { right: 20 } },
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: isDark ? '#1e293b' : '#ffffff', titleColor: isDark ? '#f1f5f9' : '#1e293b', bodyColor: isDark ? '#e2e8f0' : '#334155', borderColor: isDark ? '#334155' : '#e2e8f0', borderWidth: 1, padding: 12, cornerRadius: 8, displayColors: false, callbacks: { label: function(context) { return 'Rp ' + context.parsed.x.toLocaleString('id-ID'); } } }
            },
            scales: {
                x: { beginAtZero: true, grid: { color: gridColor, drawBorder: false }, ticks: { color: textColor, font: { size: 11 }, callback: function(value) { if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt'; if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb'; return 'Rp ' + value; } } },
                y: { grid: { display: false }, ticks: { color: textColor, font: { size: 12, weight: '600' }, crossAlign: 'far' } }
            },
            animation: { duration: 700, easing: 'easeOutQuart' }
        }
    });
}

function renderAdminExpenseChart(data) {
    lastChartData = data;
    const canvas = document.getElementById('expenseBarChart');
    if (!canvas) return;
    const oldFallback = document.getElementById('expenseBarChartFallback');
    if (oldFallback) oldFallback.remove();
    canvas.style.display = 'block';
    if (adminExpenseChartInstance) { adminExpenseChartInstance.destroy(); adminExpenseChartInstance = null; }
    const sorted = [...data].sort((a, b) => parseFloat(b.total_expense || 0) - parseFloat(a.total_expense || 0));
    const labels = sorted.map(item => item.full_name || item.username);
    const values = sorted.map(item => parseFloat(item.total_expense || 0));
    if (labels.length === 0) {
        canvas.style.display = 'none';
        let fallback = document.createElement('div');
        fallback.id = 'expenseBarChartFallback';
        fallback.innerHTML = '<div class="empty-state"><i class="fas fa-chart-bar"></i><p>Belum ada data pengeluaran</p></div>';
        canvas.insertAdjacentElement('afterend', fallback);
        return;
    }
    const isDark = document.body.classList.contains('dark-mode');
    const textColor = isDark ? '#e2e8f0' : '#334155';
    const gridColor = isDark ? 'rgba(148,163,184,0.12)' : 'rgba(148,163,184,0.2)';
    const maxVal = Math.max(...values, 1);
    const barColors = values.map(v => { const r = v / maxVal; return `rgba(${Math.round(239 + (249-239)*(1-r))}, ${Math.round(68 + (115-68)*(1-r))}, ${Math.round(68 + (22-68)*(1-r))}, 0.85)`; });
    const barBorders = values.map(v => { const r = v / maxVal; return `rgba(${Math.round(239 + (249-239)*(1-r))}, ${Math.round(68 + (115-68)*(1-r))}, ${Math.round(68 + (22-68)*(1-r))}, 1)`; });
    const containerHeight = Math.max(200, labels.length * 42 + 80);
    canvas.parentElement.style.minHeight = containerHeight + 'px';
    canvas.parentElement.style.height = containerHeight + 'px';
    const ctx = canvas.getContext('2d');
    adminExpenseChartInstance = new Chart(ctx, {
        type: 'bar',
        data: { labels: labels, datasets: [{ label: 'Total Pengeluaran', data: values, backgroundColor: barColors, borderColor: barBorders, borderWidth: 1.5, borderRadius: 6, barThickness: labels.length > 5 ? 28 : 36 }] },
        options: {
            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
            layout: { padding: { right: 20 } },
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: isDark ? '#1e293b' : '#ffffff', titleColor: isDark ? '#f1f5f9' : '#1e293b', bodyColor: isDark ? '#e2e8f0' : '#334155', borderColor: isDark ? '#334155' : '#e2e8f0', borderWidth: 1, padding: 12, cornerRadius: 8, displayColors: false, callbacks: { label: function(context) { return 'Rp ' + context.parsed.x.toLocaleString('id-ID'); } } }
            },
            scales: {
                x: { beginAtZero: true, grid: { color: gridColor, drawBorder: false }, ticks: { color: textColor, font: { size: 11 }, callback: function(value) { if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt'; if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb'; return 'Rp ' + value; } } },
                y: { grid: { display: false }, ticks: { color: textColor, font: { size: 12, weight: '600' }, crossAlign: 'far' } }
            },
            animation: { duration: 700, easing: 'easeOutQuart' }
        }
    });
}

// Skeleton Loader

function showUserTableSkeleton() {
    const tbody = document.getElementById('usersTable');
    if (!tbody) return;
    const rows = Array(6).fill('').map(() => `
        <tr class="skeleton-row">
            <td><div class="skeleton-cell skeleton-loader w-30"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-55"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-60"></div></td>
            <td><div class="skeleton-badge skeleton-loader" style="width:60px;"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-25"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-40"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-40"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-40"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-60"></div></td>
            <td><div style="display:flex;gap:6px;justify-content:center;">
                <div class="skeleton-loader" style="width:32px;height:32px;border-radius:8px;"></div>
                <div class="skeleton-loader" style="width:32px;height:32px;border-radius:8px;"></div>
                <div class="skeleton-loader" style="width:32px;height:32px;border-radius:8px;"></div>
            </div></td>
        </tr>
    `).join('');
    tbody.innerHTML = rows;
}

function showUserTxSkeleton() {
    const content = document.getElementById('userTransactionsContent');
    if (!content) return;
    const rows = Array(4).fill('').map(() => `
        <tr class="skeleton-row">
            <td><div class="skeleton-cell skeleton-loader w-60"></div></td>
            <td><div class="skeleton-badge skeleton-loader"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-55"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-40"></div></td>
            <td><div class="skeleton-cell skeleton-loader w-70"></div></td>
        </tr>
    `).join('');
    content.innerHTML = '<table><thead><tr><th>Tanggal</th><th>Jenis</th><th>Kategori</th><th>Nominal</th><th>Catatan</th></tr></thead><tbody>' + rows + '</tbody></table>';
}

// User Management

async function loadUsers(page) {
    showUserTableSkeleton();
    try {
        const targetPage = (page !== undefined) ? page : userPagination.currentPage;
        const search = document.getElementById('searchUser').value.trim();
        let endpoint = `admin/users?page=${targetPage}&limit=${userPagination.perPage}`;
        if (search) endpoint += `&search=${encodeURIComponent(search)}`;
        const data = await apiRequest(endpoint);
        allUsers = data.users;
        if (data.pagination) {
            userPagination.currentPage = data.pagination.current_page;
            userPagination.perPage = data.pagination.per_page;
            userPagination.total = data.pagination.total;
            userPagination.totalPages = data.pagination.total_pages;
        }
        renderUsersTable(allUsers);
        renderUserPagination();
    } catch (error) { console.error('Load users error:', error); }
}

function renderUserPagination() {
    const container = document.getElementById('userPaginationContainer');
    if (!container) return;
    const { currentPage, totalPages, total, perPage } = userPagination;
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
        pageButtons += `<button class="page-btn" onclick="goToUserPage(1)">1</button>`;
        if (startPage > 2) pageButtons += `<span class="page-ellipsis">...</span>`;
    }
    for (let i = startPage; i <= endPage; i++) {
        pageButtons += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="goToUserPage(${i})">${i}</button>`;
    }
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) pageButtons += `<span class="page-ellipsis">...</span>`;
        pageButtons += `<button class="page-btn" onclick="goToUserPage(${totalPages})">${totalPages}</button>`;
    }
    container.innerHTML = `
        <div class="pagination-wrapper">
            <div class="pagination-left">
                <label class="page-size-label">Tampilkan</label>
                <select class="page-size-select" onchange="changeUserPageSize(this.value)">${sizeOptions}</select>
                <span class="page-size-label">data per halaman</span>
                <span class="pagination-info">&mdash; Menampilkan ${start}-${end} dari ${total} user</span>
            </div>
            <div class="pagination-right">
                <button class="page-btn ${currentPage === 1 ? 'disabled' : ''}" onclick="goToUserPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}><i class="fas fa-chevron-left"></i></button>
                ${pageButtons}
                <button class="page-btn ${currentPage === totalPages ? 'disabled' : ''}" onclick="goToUserPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>`;
}

function goToUserPage(page) {
    if (page < 1 || page > userPagination.totalPages) return;
    loadUsers(page);
}

function changeUserPageSize(size) {
    userPagination.perPage = parseInt(size);
    loadUsers(1);
}

function openFloatingModal(id) {
    const el = document.getElementById(id);
    const card = el?.querySelector('.modal-floating-card');
    if (!el) return;
    if (card) card.classList.remove('closing');
    el.classList.add('show');
    setTimeout(() => {
        const fields = el.querySelectorAll('.floating-field, .floating-actions, .floating-header, .floating-body');
        fields.forEach(f => {
            f.style.animation = 'none';
            void f.offsetWidth;
            f.style.animation = '';
        });
    }, 50);
}

function closeFloatingModal(id) {
    const el = document.getElementById(id);
    const card = el?.querySelector('.modal-floating-card');
    if (!el) return;
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

function renderUsersTable(users) {
    const tbody = document.getElementById('usersTable');
    if (users.length === 0) { tbody.innerHTML = '<tr><td colspan="12"><div class="empty-state"><i class="fas fa-users-slash"></i><p>Tidak ada data user</p></div></td></tr>'; return; }
    tbody.innerHTML = users.map(user => `
        <tr class="fade-in">
            <td>${user.id}</td>
            <td style="font-weight: 600;">${escapeHtml(user.username)}</td>
            <td>${escapeHtml(user.full_name)}</td>
            <td><span class="badge-role badge-${user.role}">${user.role}</span></td>
            <td>${user.total_transactions || 0}</td>
            <td class="text-success">${formatCurrency(user.total_income || 0)}</td>
            <td class="text-danger">${formatCurrency(user.total_expense || 0)}</td>
            <td style="color: var(--accent-gold); font-weight: 600;">${formatCurrency(user.main_wallet_balance || 0)}</td>
            <td style="color: #10b981; font-weight: 600;">${formatCurrency(user.savings_wallet_balance || 0)}</td>
            <td style="font-weight: 600;">${formatCurrency(user.balance || 0)}</td>
            <td>${formatDate(user.joined_date)}</td>
            <td>
                <div style="display: flex; gap: 6px;">
                    <button class="btn btn-sm btn-secondary" onclick="showUserTransactions(${user.id})" title="Lihat Transaksi"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-primary" onclick="editUser(${user.id})" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})" title="Hapus" ${matchId(user.id, currentUser.id) ? 'disabled' : ''}><i class="fas fa-trash"></i></button>
                </div>
            </td>
        </tr>
    `).join('');
}

function filterUsers() {
    userPagination.currentPage = 1;
    loadUsers(1);
}

async function saveNewUser() {
    const fullName = document.getElementById('addFullName').value.trim();
    const username = document.getElementById('addUsername').value.trim();
    const password = document.getElementById('addPassword').value;
    const role = document.getElementById('addRole').value;
    const btn = document.getElementById('addUserBtn');
    if (!fullName || !username || !password) { showToast('Semua field wajib diisi', 'error'); return; }
    if (username.length < 3) { showToast('Username minimal 3 karakter', 'error'); return; }
    if (password.length < PASSWORD_MIN_LENGTH) { showToast('Password minimal ' + PASSWORD_MIN_LENGTH + ' karakter', 'error'); return; }
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...'; btn.disabled = true;
    try {
        await apiRequest('admin/users', 'POST', { full_name: fullName, username: username, password: password, role: role });
        showToast('User berhasil ditambahkan', 'success');
        document.getElementById('addUserForm').reset();
        closeFloatingModal('addUserModal');
        loadUsers(1); loadAdminStats();
    } catch (error) { console.error(error); }
    finally { btn.innerHTML = originalText; btn.disabled = false; }
}

function editUser(userId) {
    const user = findById(allUsers, userId);
    if (!user) { showToast('Data user tidak ditemukan', 'error'); return; }
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editFullName').value = user.full_name;
    document.getElementById('editRole').value = user.role;
    document.getElementById('editPassword').value = '';
    openFloatingModal('editUserModal');
}

async function saveUserEdit() {
    const userId = document.getElementById('editUserId').value;
    const fullName = document.getElementById('editFullName').value;
    const role = document.getElementById('editRole').value;
    const password = document.getElementById('editPassword').value;
    const data = { id: parseInt(userId), full_name: fullName, role: role };
    if (password) { if (password.length < PASSWORD_MIN_LENGTH) { showToast('Password minimal ' + PASSWORD_MIN_LENGTH + ' karakter', 'error'); return; } data.password = password; }
    try {
        await apiRequest('admin/users/' + userId, 'PUT', data);
        showToast('User berhasil diupdate', 'success');
        closeFloatingModal('editUserModal');
        loadUsers(userPagination.currentPage); loadAdminStats();
    } catch (error) { console.error(error); }
}

async function deleteUser(userId) {
    const result = await Swal.fire({ title: 'Hapus User?', text: 'User dan semua transaksinya akan dihapus permanen!', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal', background: '#1a1a2e', color: '#fff' });
    if (result.isConfirmed) {
        try { await apiRequest('admin/users/' + userId, 'DELETE', { id: userId }); showToast('User berhasil dihapus', 'success'); let page = userPagination.currentPage; if (allUsers.length === 1 && page > 1) page = page - 1; loadUsers(page); loadAdminStats(); }
        catch (error) { console.error(error); }
    }
}

// User Transaction Modal

let userTxState = { userId: null, userName: '', page: 1, limit: 8, totalPages: 1, day: '', month: '', year: '' };

async function showUserTransactions(userId, page = 1) {
    showUserTxSkeleton();
    try {
        if (userTxState.userId !== null && userTxState.userId !== userId) {
            document.getElementById('txFilterDay').value = '';
            document.getElementById('txFilterMonth').value = '';
            document.getElementById('txFilterYear').value = '';
            userTxState.day = '';
            userTxState.month = '';
            userTxState.year = '';
        }
        const day = document.getElementById('txFilterDay')?.value || '';
        const month = document.getElementById('txFilterMonth')?.value || '';
        const year = document.getElementById('txFilterYear')?.value || '';
        let endpoint = 'admin/users/' + userId + '/transactions?page=' + page + '&limit=' + userTxState.limit;
        if (day) endpoint += '&day=' + day;
        if (month) endpoint += '&month=' + month;
        if (year) endpoint += '&year=' + year;
        const data = await apiRequest(endpoint);
        const user = findById(allUsers, userId);
        const displayName = user ? user.full_name : 'User #' + userId;
        userTxState.userId = userId;
        userTxState.userName = displayName;
        userTxState.page = data.pagination.current_page;
        userTxState.totalPages = data.pagination.total_pages;

        document.getElementById('userTxTitle').textContent = 'Transaksi - ' + escapeHtml(displayName);

        const content = document.getElementById('userTransactionsContent');
        if (data.transactions.length === 0) {
            content.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>Tidak ada transaksi untuk ' + escapeHtml(displayName) + '</p></div>';
            document.getElementById('userTxPagination').style.display = 'none';
        } else {
            let html = '<table><thead><tr><th>Tanggal</th><th>Jenis</th><th>Kategori</th><th>Nominal</th><th>Catatan</th></tr></thead><tbody>';
            data.transactions.forEach(t => {
                html += '<tr><td>' + formatDate(t.transaction_date) + '</td><td><span class="' + (t.type === 'income' ? 'badge-income' : 'badge-expense') + '">' + (t.type === 'income' ? 'Pemasukan' : 'Pengeluaran') + '</span></td><td>' + escapeHtml(t.category) + '</td><td class="' + (t.type === 'income' ? 'text-success' : 'text-danger') + '">' + formatCurrency(t.amount) + '</td><td>' + escapeHtml(t.note || '-') + '</td></tr>';
            });
            html += '</tbody></table>';
            content.innerHTML = html;
            renderUserTxPagination();
        }
        openFloatingModal('userTransactionsModal');
    } catch (error) { console.error(error); }
}

function applyUserTxFilter(page) {
    const userId = userTxState.userId;
    if (!userId) return;
    const day = document.getElementById('txFilterDay')?.value || '';
    const month = document.getElementById('txFilterMonth')?.value || '';
    const year = document.getElementById('txFilterYear')?.value || '';
    userTxState.day = day;
    userTxState.month = month;
    userTxState.year = year;
    showUserTransactions(userId, page || 1);
}

function resetUserTxFilter() {
    const userId = userTxState.userId;
    if (!userId) return;
    document.getElementById('txFilterDay').value = '';
    document.getElementById('txFilterMonth').value = '';
    document.getElementById('txFilterYear').value = '';
    userTxState.day = '';
    userTxState.month = '';
    userTxState.year = '';
    showUserTransactions(userId, 1);
}

function renderUserTxPagination() {
    const { page, totalPages } = userTxState;
    const bar = document.getElementById('userTxPagination');
    if (totalPages <= 1) { bar.style.display = 'none'; return; }
    bar.style.display = 'flex';
    let html = '';
    html += '<button class="page-btn ' + (page <= 1 ? 'disabled' : '') + '" onclick="showUserTransactions(userTxState.userId, 1)"><i class="fas fa-angle-double-left"></i></button>';
    html += '<button class="page-btn ' + (page <= 1 ? 'disabled' : '') + '" onclick="showUserTransactions(userTxState.userId, ' + (page - 1) + ')"><i class="fas fa-chevron-left"></i></button>';
    let start = Math.max(1, page - 2), end = Math.min(totalPages, page + 2);
    if (start > 1) html += '<span class="page-info">...</span>';
    for (let i = start; i <= end; i++) {
        html += '<button class="page-btn ' + (i === page ? 'active' : '') + '" onclick="showUserTransactions(userTxState.userId, ' + i + ')">' + i + '</button>';
    }
    if (end < totalPages) html += '<span class="page-info">...</span>';
    html += '<button class="page-btn ' + (page >= totalPages ? 'disabled' : '') + '" onclick="showUserTransactions(userTxState.userId, ' + (page + 1) + ')"><i class="fas fa-chevron-right"></i></button>';
    html += '<button class="page-btn ' + (page >= totalPages ? 'disabled' : '') + '" onclick="showUserTransactions(userTxState.userId, ' + totalPages + ')"><i class="fas fa-angle-double-right"></i></button>';
    bar.innerHTML = html;
}

// Export

async function fetchAllUsersForExport() {
    try {
        const data = await apiRequest('admin/users?limit=10000', 'GET', null, { silent: true });
        return data.users || [];
    } catch (error) {
        console.error('Failed to fetch all users for export:', error);
        showToast('Gagal mengambil data semua user', 'error');
        return null;
    }
}

async function exportUsersToExcel() {
    if (typeof XLSX === 'undefined') { showToast('Library Excel belum dimuat', 'error'); return; }
    showToast('Mengambil data semua user...', 'info');
    const exportUsers = await fetchAllUsersForExport();
    if (!exportUsers || exportUsers.length === 0) { showToast('Tidak ada data user untuk diekspor', 'error'); return; }
    const exportData = exportUsers.map(user => ({ 'ID': user.id, 'Username': user.username, 'Nama Lengkap': user.full_name, 'Role': user.role, 'Total Transaksi': user.total_transactions || 0, 'Total Pemasukan': user.total_income || 0, 'Total Pengeluaran': user.total_expense || 0, 'Saldo': user.balance || 0, 'Tanggal Bergabung': formatDate(user.joined_date) }));
    const ws = XLSX.utils.json_to_sheet(exportData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Data User');
    XLSX.writeFile(wb, 'data_user_' + new Date().toISOString().slice(0, 10) + '.xlsx');
    showToast('File Excel berhasil diunduh', 'success');
}

async function exportUsersToPDF() {
    if (typeof window.jspdf === 'undefined') { showToast('Library PDF belum dimuat, silakan refresh halaman', 'error'); return; }
    showToast('Mengambil data semua user...', 'info');
    const exportUsers = await fetchAllUsersForExport();
    if (!exportUsers || exportUsers.length === 0) { showToast('Tidak ada data user untuk diekspor', 'error'); return; }
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    const pageW = doc.internal.pageSize.getWidth();
    const pageH = doc.internal.pageSize.getHeight();
    const marginL = 14;
    const fmtRp = function(n) { return 'Rp ' + parseFloat(n || 0).toLocaleString('id-ID'); };
    doc.setFillColor(30, 41, 59); doc.rect(0, 0, pageW, 32, 'F');
    doc.setTextColor(255); doc.setFontSize(18); doc.setFont(undefined, 'bold');
    doc.text('Laporan Data Pengguna', pageW / 2, 16, { align: 'center' });
    doc.setFontSize(9); doc.setFont(undefined, 'normal');
    doc.text('Admin Panel Dompet Digital  |  ' + new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }), pageW / 2, 25, { align: 'center' });
    doc.setTextColor(0);
    const tableRows = exportUsers.map(u => [u.id, u.username, u.full_name, u.role, u.total_transactions || 0, fmtRp(u.total_income || 0), fmtRp(u.total_expense || 0), fmtRp(u.balance || 0), formatDate(u.joined_date)]);
    doc.autoTable({
        head: [['ID', 'Username', 'Nama', 'Role', 'Transaksi', 'Pemasukan', 'Pengeluaran', 'Saldo', 'Bergabung']],
        body: tableRows, startY: 40, theme: 'striped',
        headStyles: { fillColor: [51, 65, 85], textColor: 255, fontSize: 7, fontStyle: 'bold', halign: 'center' },
        bodyStyles: { fontSize: 7, textColor: [50, 50, 50] }, alternateRowStyles: { fillColor: [248, 250, 252] },
        styles: { cellPadding: 2.2 },
        columnStyles: { 0: { cellWidth: 10, halign: 'center' }, 1: { cellWidth: 22 }, 2: { cellWidth: 'auto' }, 3: { cellWidth: 14, halign: 'center' }, 4: { cellWidth: 16, halign: 'center' }, 5: { cellWidth: 24, halign: 'right', textColor: [16, 185, 129] }, 6: { cellWidth: 24, halign: 'right', textColor: [239, 68, 68] }, 7: { cellWidth: 24, halign: 'right', fontStyle: 'bold' }, 8: { cellWidth: 22, halign: 'center' } },
        margin: { left: marginL }
    });
    const finalY = doc.lastAutoTable.finalY;
    let y = finalY + 10;
    if (y > pageH - 50) { doc.addPage(); y = 18; }
    const totalUsers = exportUsers.length;
    const totalAdmins = exportUsers.filter(u => u.role === 'admin').length;
    const totalRegUsers = totalUsers - totalAdmins;
    const grandBalance = exportUsers.reduce(function(a, b) { return a + parseFloat(b.balance || 0); }, 0);
    doc.setFillColor(248, 250, 252); doc.setDrawColor(200, 210, 225);
    doc.roundedRect(marginL, y, pageW - 28, 28, 3, 3, 'FD'); y += 7;
    doc.setFontSize(10); doc.setFont(undefined, 'bold'); doc.setTextColor(30, 41, 59);
    doc.text('Ringkasan', marginL + 5, y); y += 6;
    doc.setFontSize(8.5); doc.setFont(undefined, 'normal'); doc.setTextColor(70, 80, 100);
    doc.text('Total Pengguna: ' + totalUsers + ' (' + totalAdmins + ' admin, ' + totalRegUsers + ' user)', marginL + 5, y); y += 5;
    doc.text('Total Saldo Keseluruhan: ' + fmtRp(grandBalance), marginL + 5, y);
    const totalPages = doc.internal.getNumberOfPages();
    for (let i = 1; i <= totalPages; i++) { doc.setPage(i); doc.setFontSize(8); doc.setTextColor(150); doc.text('Admin Panel - Halaman ' + i + ' dari ' + totalPages, pageW / 2, pageH - 10, { align: 'center' }); doc.setTextColor(0); }
    doc.save('data_pengguna_' + new Date().toISOString().slice(0, 10) + '.pdf');
    showToast('File PDF berhasil diunduh', 'success');
}

// Navigation

function showSection(section) {
    const dashboardSection = document.getElementById('dashboardSection');
    const usersSection = document.getElementById('usersSection');
    const profileSection = document.getElementById('profileSection');
    const navLinks = document.querySelectorAll('.nav-link');
    dashboardSection.style.display = 'none';
    usersSection.style.display = 'none';
    if (profileSection) profileSection.style.display = 'none';
    if (section === 'dashboard') { dashboardSection.style.display = 'block'; loadAdminStats(); }
    else if (section === 'users') { usersSection.style.display = 'block'; loadUsers(1); }
    else if (section === 'profile') { if (profileSection) { profileSection.style.display = 'block'; loadProfile(); } }
    navLinks.forEach(function(link) { link.classList.remove('active'); });
    if (section === 'dashboard') navLinks[0].classList.add('active');
    else if (section === 'users') navLinks[1].classList.add('active');
    else if (section === 'profile') {
        for (let i = 0; i < navLinks.length; i++) {
            if (navLinks[i].getAttribute('onclick') === "showSection('profile')") {
                navLinks[i].classList.add('active'); break;
            }
        }
    }
}

// Profile

function loadProfile() {
    var fullNameEl = document.getElementById('profileFullName');
    var usernameEl = document.getElementById('profileUsername');
    if (!fullNameEl || !usernameEl) return;
    if (!currentUser) return;
    fullNameEl.value = currentUser.full_name || '';
    usernameEl.value = currentUser.username || '';
    var pw = document.getElementById('profilePassword');
    if (pw) pw.value = '';
    var cpw = document.getElementById('currentPassword');
    if (cpw) cpw.value = '';
    var npw = document.getElementById('newPassword');
    if (npw) npw.value = '';
    var conpw = document.getElementById('confirmPassword');
    if (conpw) conpw.value = '';
}

// Dark Mode

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

// Init

document.addEventListener('DOMContentLoaded', function() {
    loadThemePreference();
    var darkModeBtn = document.getElementById('darkModeToggle');
    if (darkModeBtn) darkModeBtn.addEventListener('click', toggleDarkMode);
    initAdmin();

    var profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            var fullName = document.getElementById('profileFullName');
            var password = document.getElementById('profilePassword');
            if (!fullName || !fullName.value.trim()) {
                showToast('Nama lengkap wajib diisi', 'error');
                return;
            }
            var btn = document.getElementById('saveProfileBtn');
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...'; }
            try {
                var usernameEl = document.getElementById('profileUsername');
                var data = { full_name: fullName.value.trim() };
                if (usernameEl && usernameEl.value) data.username = usernameEl.value;
                if (password && password.value.length >= 8) data.password = password.value;
                await apiRequest('user/profile', 'PUT', data);
                var nameEl = document.getElementById('adminName');
                if (nameEl) nameEl.textContent = fullName.value.trim();
                var sidebarNameEl = document.getElementById('sidebarUserName');
                if (sidebarNameEl) sidebarNameEl.textContent = fullName.value.trim();
                if (currentUser) currentUser.full_name = fullName.value.trim();
                showToast('Profil berhasil diperbarui', 'success');
            } catch (error) {
                console.error('Update profile error:', error);
            } finally {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan'; }
            }
        });
    }

    var passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            var currentPw = document.getElementById('currentPassword');
            var newPw = document.getElementById('newPassword');
            var confirmPw = document.getElementById('confirmPassword');
            if (!currentPw || !newPw || !confirmPw) return;
            if (!currentPw.value) { showToast('Password lama wajib diisi', 'error'); return; }
            if (!newPw.value || newPw.value.length < 8) { showToast('Password baru minimal 8 karakter', 'error'); return; }
            if (newPw.value !== confirmPw.value) { showToast('Konfirmasi password tidak cocok', 'error'); return; }
            var btn = document.getElementById('savePasswordBtn');
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...'; }
            try {
                await apiRequest('user/password', 'PUT', {
                    current_password: currentPw.value,
                    new_password: newPw.value,
                });
                showToast('Password berhasil diubah', 'success');
                currentPw.value = '';
                newPw.value = '';
                confirmPw.value = '';
            } catch (error) {
                console.error('Change password error:', error);
            } finally {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-key"></i> Ganti Password'; }
            }
        });
    }
});