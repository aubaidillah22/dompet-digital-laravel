// Dashboard Dompet Digital — Full Stats View
// =============================================

var API_BASE = window.DOMpetConfig.apiBase;
var currentUser = null;
var dashboardData = null;
var trendChart = null, expensePieChart = null, incomeDoughnutChart = null, savingsGrowthChart = null;

// ── Category Colors ───────────────────────

var CATEGORY_COLORS = [
    '#f0b429', '#ef4444', '#3b82f6', '#10b981', '#8b5cf6',
    '#ec4899', '#f59e0b', '#06b6d4', '#d946ef', '#14b8a6',
    '#f97316', '#6366f1', '#84cc16', '#e11d48', '#0ea5e9',
    '#a855f7', '#22c55e', '#eab308', '#64748b', '#f472b6',
];

function getCategoryColor(index) {
    return CATEGORY_COLORS[index % CATEGORY_COLORS.length];
}

// ── Utilities (shared via app-utils.js) ────

function parseCurrencyText(text) {
    if (!text) return 0;
    return parseInt(text.replace(/[^0-9]/g, '')) || 0;
}

// ── API (shared via app-utils.js) ──────────

// ── Load Dashboard Stats ─────────────────

async function loadDashboardStats() {
    try {
        var result = await apiRequest('dashboard/stats');
        if (!result.success) {
            showToast('Gagal memuat data dashboard', 'error');
            return;
        }
        dashboardData = result;
        renderAll(dashboardData);
    } catch (error) {
        console.error('Load dashboard stats error:', error);
    }
}

function renderAll(data) {
    renderHeroWallets(data);
    renderStatsCards(data);
    renderGoalBar(data);
    renderRecentTransactions(data);

    // Charts need DOM to be ready
    setTimeout(function() {
        renderTrendChart(data);
        renderExpensePieChart(data);
        renderIncomeDoughnutChart(data);
        renderSavingsGrowthChart(data);
        destroySplash();
    }, 100);
}

// ── Hero Wallets ──────────────────────────

function renderHeroWallets(data) {
    var mainEl = document.getElementById('heroMainBalance');
    var savingsEl = document.getElementById('heroSavingsBalance');

    if (data.wallets.main && mainEl) {
        animateValue(mainEl, 0, data.wallets.main.balance, 700, true);
    }
    if (data.wallets.savings && savingsEl) {
        animateValue(savingsEl, 0, data.wallets.savings.balance, 700, true);
    }
}

// ── Stats Cards ───────────────────────────

function renderStatsCards(data) {
    var s = data.summary;
    var all = s.all;
    var tm = s.this_month;

    // Row 1: All-time stats
    animateValue(document.getElementById('totalIncome'), 0, all.total_income, 700, true);
    animateValue(document.getElementById('totalExpense'), 0, all.total_expense, 700, true);

    var balanceEl = document.getElementById('balance');
    var mainWallet = data.wallets.main;
    var mainBalance = mainWallet ? mainWallet.balance : 0;
    animateValue(balanceEl, 0, mainBalance, 700, true);
    if (balanceEl) {
        balanceEl.classList.toggle('balance-negative', mainBalance < 0);
    }

    animateValue(document.getElementById('totalTransactions'), 0, all.total_transactions, 500, false);

    // Row 2: Monthly stats
    animateValue(document.getElementById('monthlyIncome'), 0, tm.total_income, 600, true);
    animateValue(document.getElementById('monthlyExpense'), 0, tm.total_expense, 600, true);

    // Expense ratio
    var ratioEl = document.getElementById('expenseRatio');
    if (ratioEl) {
        var ratio = tm.total_income > 0 ? (tm.total_expense / tm.total_income) * 100 : 0;
        ratioEl.textContent = ratio.toFixed(1) + '%';
    }

    // Average monthly spending (based on total months with data)
    var avgEl = document.getElementById('avgMonthly');
    if (avgEl) {
        var monthsCount = data.monthly_trends.length || 1;
        var avg = all.total_expense / monthsCount;
        avgEl.textContent = formatCurrency(Math.round(avg));
    }

    // Update period labels
    var now = new Date();
    var monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    var thisMonthName = monthNames[now.getMonth()] + ' ' + now.getFullYear();

    // Update stat period labels
    var periodEls = {
        periodIncome: 'Semua waktu',
        periodExpense: 'Semua waktu',
        periodBalance: 'Semua waktu',
        periodCount: 'Semua waktu',
    };
    var monthPeriod = 'Bulan ' + thisMonthName;
    var monthlyIncomePeriod = document.querySelector('.stat-month .stat-period');
    if (monthlyIncomePeriod) monthlyIncomePeriod.textContent = monthPeriod;
    var monthlyExpenseLabel = document.querySelectorAll('.stat-card.stat-expense .stat-period');
    // The second stat-expense card (monthly) period
    var statCards = document.querySelectorAll('.stat-card');
    var monthlyExpensePeriod = null;
    for (var i = 0; i < statCards.length; i++) {
        var label = statCards[i].querySelector('.stat-label');
        if (label && label.textContent === 'Pengeluaran Bulan Ini') {
            monthlyExpensePeriod = statCards[i].querySelector('.stat-period');
            break;
        }
    }
    if (monthlyExpensePeriod) monthlyExpensePeriod.textContent = monthPeriod;
}

// ── Savings Goal ──────────────────────────

function renderGoalBar(data) {
    var savings = data.wallets.savings;
    var balance = savings ? savings.balance : 0;
    var target = savings && savings.savings_target ? savings.savings_target : 10000000;
    var pct = Math.min((balance / target) * 100, 100);

    var progressText = document.getElementById('goalProgressText');
    var progressBar = document.getElementById('goalProgressBar');
    var pctText = document.getElementById('goalPctText');

    if (progressText) progressText.textContent = formatCurrency(balance) + ' / ' + formatCurrency(target);
    if (progressBar) {
        progressBar.style.width = pct + '%';
    }
    if (pctText) pctText.textContent = pct.toFixed(1) + '% tercapai';
}

// ── Recent Transactions ───────────────────

var allTransactions = [];

function renderRecentTransactions(data) {
    var tbody = document.getElementById('recentTransactions');
    if (!tbody) return;

    var tx = data.recent_transactions || [];
    allTransactions = tx;

    if (tx.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:32px;">' +
            '<div style="display:flex;flex-direction:column;align-items:center;gap:8px;color:var(--text-muted);">' +
            '<i class="fas fa-inbox" style="font-size:1.5rem;"></i>' +
            '<p>Belum ada transaksi</p></div></td></tr>';
        return;
    }

    filterTransactions('');
    setupSearchListener();
}

function filterTransactions(query) {
    var tbody = document.getElementById('recentTransactions');
    var noResult = document.getElementById('searchNoResult');
    if (!tbody) return;

    query = query.toLowerCase().trim();

    var filtered = allTransactions;
    if (query) {
        filtered = allTransactions.filter(function(t) {
            var walletName = t.wallet ? (t.wallet.name || '').toLowerCase() : '';
            var badgeText = (t.type === 'income' ? 'Pemasukan' : (t.type === 'expense' ? 'Pengeluaran' : 'Transfer')).toLowerCase();
            var dateStr = (t.transaction_date || '').toLowerCase();
            var categoryStr = (t.category || '').toLowerCase();
            var noteStr = (t.note || '').toLowerCase();
            var amountStr = formatCurrency(t.amount).toLowerCase().replace(/[^a-z0-9]/g, '');
            var searchStr = query.replace(/[^a-z0-9]/g, '');

            return dateStr.indexOf(query) !== -1 ||
                   badgeText.indexOf(query) !== -1 ||
                   categoryStr.indexOf(query) !== -1 ||
                   walletName.indexOf(query) !== -1 ||
                   noteStr.indexOf(query) !== -1 ||
                   (amountStr.indexOf(searchStr) !== -1);
        });
    }

    if (filtered.length === 0 && query) {
        tbody.innerHTML = '';
        if (noResult) noResult.style.display = 'block';
        return;
    }
    if (noResult) noResult.style.display = 'none';

    var html = '';
    for (var i = 0; i < filtered.length; i++) {
        var t = filtered[i];
        var badgeClass = t.type === 'income' ? 'badge-income' : (t.type === 'expense' ? 'badge-expense' : 'badge-transfer');
        var badgeText = t.type === 'income' ? 'Pemasukan' : (t.type === 'expense' ? 'Pengeluaran' : 'Transfer');
        var amountClass = t.type === 'income' ? 'text-success' : 'text-danger';
        var amountSign = t.type === 'income' ? '+' : '-';
        var walletName = t.wallet ? escapeHtml(t.wallet.name) : '-';

        html += '<tr>' +
            '<td>' + formatDate(t.transaction_date) + '</td>' +
            '<td><span class="' + badgeClass + '">' + badgeText + '</span></td>' +
            '<td>' + escapeHtml(t.category) + '</td>' +
            '<td>' + walletName + '</td>' +
            '<td class="' + amountClass + '">' + amountSign + ' ' + formatCurrency(t.amount) + '</td>' +
            '<td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
            escapeHtml(t.note) + '</td>' +
            '</tr>';
    }
    tbody.innerHTML = html;
}

var searchListenerAttached = false;

function setupSearchListener() {
    if (searchListenerAttached) return;

    var searchInput = document.getElementById('transactionSearch');
    var clearBtn = document.getElementById('searchClearBtn');
    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        var val = this.value;
        if (clearBtn) clearBtn.style.display = val ? 'flex' : 'none';
        filterTransactions(val);
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            this.style.display = 'none';
            filterTransactions('');
            searchInput.focus();
        });
    }

    searchListenerAttached = true;
}

function clearTransactionSearch() {
    var input = document.getElementById('transactionSearch');
    var clearBtn = document.getElementById('searchClearBtn');
    if (input) {
        input.value = '';
        if (clearBtn) clearBtn.style.display = 'none';
        filterTransactions('');
        input.focus();
    }
}

// ── Charts ────────────────────────────────

function renderTrendChart(data) {
    var canvas = document.getElementById('trendChart');
    if (!canvas || typeof Chart === 'undefined') return;

    if (trendChart) { trendChart.destroy(); }

    var trends = data.monthly_trends || [];
    var labels = trends.map(function(t) { return formatMonth(t.month); });
    var incomes = trends.map(function(t) { return t.total_income; });
    var expenses = trends.map(function(t) { return t.total_expense; });

    var isDark = document.body.classList.contains('dark-mode');
    var gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    var textColor = isDark ? 'rgba(255,255,255,0.6)' : 'rgba(0,0,0,0.5)';

    trendChart = new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: incomes,
                    backgroundColor: isDark ? 'rgba(16,185,129,0.7)' : 'rgba(16,185,129,0.8)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.4,
                },
                {
                    label: 'Pengeluaran',
                    data: expenses,
                    backgroundColor: isDark ? 'rgba(239,68,68,0.7)' : 'rgba(239,68,68,0.8)',
                    borderColor: '#ef4444',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { color: textColor, boxWidth: 12, padding: 8, font: { size: 11 } }
                }
            },
            scales: {
                x: {
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { size: 10 } }
                },
                y: {
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { size: 10 }, callback: function(v) { return formatCurrency(v); } }
                }
            }
        }
    });
}

function renderExpensePieChart(data) {
    var canvas = document.getElementById('expensePieChart');
    if (!canvas || typeof Chart === 'undefined') return;

    if (expensePieChart) { expensePieChart.destroy(); }

    var categories = data.top_expense_categories || [];
    if (categories.length === 0) {
        canvas.parentNode.innerHTML = '<div style="text-align:center;padding:48px;color:var(--text-muted);">' +
            '<i class="fas fa-chart-pie" style="font-size:2rem;margin-bottom:8px;display:block;"></i>' +
            'Belum ada data pengeluaran</div>';
        return;
    }

    var isDark = document.body.classList.contains('dark-mode');
    var textColor = isDark ? 'rgba(255,255,255,0.6)' : 'rgba(0,0,0,0.5)';

    var labels = categories.map(function(c) { return c.category; });
    var values = categories.map(function(c) { return c.total; });
    var colors = labels.map(function(_, i) { return getCategoryColor(i); });

    expensePieChart = new Chart(canvas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderColor: isDark ? '#1e1b3a' : '#ffffff',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: textColor, boxWidth: 12, padding: 8, font: { size: 10 } }
                }
            }
        }
    });
}

function renderIncomeDoughnutChart(data) {
    var canvas = document.getElementById('incomeDoughnutChart');
    if (!canvas || typeof Chart === 'undefined') return;

    if (incomeDoughnutChart) { incomeDoughnutChart.destroy(); }

    var categories = data.top_income_categories || [];
    if (categories.length === 0) {
        canvas.parentNode.innerHTML = '<div style="text-align:center;padding:48px;color:var(--text-muted);">' +
            '<i class="fas fa-chart-doughnut" style="font-size:2rem;margin-bottom:8px;display:block;"></i>' +
            'Belum ada data pemasukan</div>';
        return;
    }

    var isDark = document.body.classList.contains('dark-mode');
    var textColor = isDark ? 'rgba(255,255,255,0.6)' : 'rgba(0,0,0,0.5)';

    var labels = categories.map(function(c) { return c.category; });
    var values = categories.map(function(c) { return c.total; });
    var colors = labels.map(function(_, i) { return getCategoryColor(i); });

    incomeDoughnutChart = new Chart(canvas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderColor: isDark ? '#1e1b3a' : '#ffffff',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: textColor, boxWidth: 12, padding: 8, font: { size: 10 } }
                }
            }
        }
    });
}

function renderSavingsGrowthChart(data) {
    var canvas = document.getElementById('savingsGrowthChart');
    if (!canvas || typeof Chart === 'undefined') return;

    if (savingsGrowthChart) { savingsGrowthChart.destroy(); }

    var growth = data.savings_growth || [];
    if (growth.length === 0) {
        canvas.parentNode.innerHTML = '<div style="text-align:center;padding:48px;color:var(--text-muted);">' +
            '<i class="fas fa-piggy-bank" style="font-size:2rem;margin-bottom:8px;display:block;"></i>' +
            'Belum ada data tabungan</div>';
        return;
    }

    // Build cumulative data
    var cumulative = 0;
    var labels = growth.map(function(g) { return formatMonth(g.month); });
    var values = growth.map(function(g) {
        cumulative += g.total;
        return cumulative;
    });

    var isDark = document.body.classList.contains('dark-mode');
    var gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    var textColor = isDark ? 'rgba(255,255,255,0.6)' : 'rgba(0,0,0,0.5)';

    var gradient = canvas.getContext('2d').createLinearGradient(0, 0, 0, 200);
    if (isDark) {
        gradient.addColorStop(0, 'rgba(16,185,129,0.25)');
        gradient.addColorStop(1, 'rgba(16,185,129,0.01)');
    } else {
        gradient.addColorStop(0, 'rgba(16,185,129,0.15)');
        gradient.addColorStop(1, 'rgba(16,185,129,0.01)');
    }

    savingsGrowthChart = new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Saldo Tabungan',
                data: values,
                fill: true,
                backgroundColor: gradient,
                borderColor: '#10b981',
                borderWidth: 2.5,
                pointBackgroundColor: '#10b981',
                pointBorderColor: isDark ? '#1e1b3a' : '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.35,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { color: textColor, boxWidth: 12, padding: 8, font: { size: 11 } }
                }
            },
            scales: {
                x: {
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { size: 10 } }
                },
                y: {
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { size: 10 }, callback: function(v) { return formatCurrency(v); } }
                }
            }
        }
    });
}

// ── Sidebar & Splash (shared via app-utils.js) ──

// ── Dark Mode ─────────────────────────────

function loadThemePreference() {
    var savedTheme = localStorage.getItem('darkMode');
    var isDark = savedTheme !== 'disabled';
    if (isDark) document.body.classList.add('dark-mode');
    else document.body.classList.remove('dark-mode');
    var darkModeBtn = document.getElementById('darkModeToggle');
    if (darkModeBtn) darkModeBtn.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
}

function toggleDarkMode() {
    var isDark = !document.body.classList.contains('dark-mode');
    document.body.classList.toggle('dark-mode', isDark);
    localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
    var darkModeBtn = document.getElementById('darkModeToggle');
    if (darkModeBtn) darkModeBtn.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    // Re-render charts with new theme
    if (dashboardData) {
        setTimeout(function() {
            renderTrendChart(dashboardData);
            renderExpensePieChart(dashboardData);
            renderIncomeDoughnutChart(dashboardData);
            renderSavingsGrowthChart(dashboardData);
        }, 150);
    }
}

// ── Modal Helpers (shared via app-utils.js) ──

function openModal(modalId) {
    var el = document.getElementById(modalId);
    if (el) { el.style.display = 'flex'; return; }
    if (modalId === 'transferModal') {
        window.location.href = '/tabungan';
    } else if (modalId === 'profileModal') {
        openProfileModal();
    }
}

function openProfileModal() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Pengaturan Profil',
            html: '<div style="text-align:left;">' +
                '<div style="margin-bottom:12px;"><label style="font-size:0.75rem;color:#94a3b8;display:block;margin-bottom:4px;">Nama Lengkap</label>' +
                '<input id="swalFullName" class="form-control" value="' + escapeHtml(currentUser ? currentUser.full_name : '') + '" style="width:100%;padding:8px 14px;border-radius:40px;border:1px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.08);color:#fff;outline:none;"></div>' +
                '<div style="margin-bottom:12px;"><label style="font-size:0.75rem;color:#94a3b8;display:block;margin-bottom:4px;">Username</label>' +
                '<input id="swalUsername" class="form-control" value="' + escapeHtml(currentUser ? currentUser.username : '') + '" style="width:100%;padding:8px 14px;border-radius:40px;border:1px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.08);color:#94a3b8;outline:none;" disabled></div>' +
                '<div><label style="font-size:0.75rem;color:#94a3b8;display:block;margin-bottom:4px;">Password Baru</label>' +
                '<input id="swalPassword" type="password" class="form-control" placeholder="Kosongkan jika tidak diubah" style="width:100%;padding:8px 14px;border-radius:40px;border:1px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.08);color:#fff;outline:none;"></div>',
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d4940a',
            cancelButtonColor: '#6b7280',
            background: '#1a1a2e',
            color: '#fff',
            preConfirm: async function() {
                var fullName = document.getElementById('swalFullName') && document.getElementById('swalFullName').value.trim();
                var password = document.getElementById('swalPassword') && document.getElementById('swalPassword').value;
                if (!fullName) { Swal.showValidationMessage('Nama lengkap wajib diisi'); return false; }
                try {
                    var data = { full_name: fullName };
                    if (password && password.length >= 8) data.password = password;
                    await apiRequest('user/profile', 'PUT', data);
                    if (currentUser) currentUser.full_name = fullName;
                    var nameEl = document.getElementById('userName');
                    if (nameEl) nameEl.textContent = fullName;
                    showToast('Profil berhasil diupdate', 'success');
                } catch (e) {
                    Swal.showValidationMessage(e.message || 'Gagal update profil');
                    return false;
                }
            }
        });
    }
}

// ── Sidebar click-outside ─────────────────

document.addEventListener('click', function(event) {
    if (window.innerWidth > 768) return;
    var sidebar = document.getElementById('sidebar');
    var menuToggle = document.querySelector('.menu-toggle');
    if (sidebar && sidebar.classList.contains('open')) {
        if (!sidebar.contains(event.target) && (!menuToggle || !menuToggle.contains(event.target))) closeSidebar();
    }
});

// ── Init ──────────────────────────────────

async function initDashboard() {
    currentUser = await checkAuth();
    if (!currentUser) return;

    var userNameSpan = document.getElementById('userName');
    if (userNameSpan) userNameSpan.textContent = currentUser.full_name || currentUser.username || 'User';

    renderUserAvatar(currentUser.avatar || 0);

    await loadDashboardStats();
}

(function() {
    function initDashboardPage() {
        loadThemePreference();
        var darkModeBtn = document.getElementById('darkModeToggle');
        if (darkModeBtn) darkModeBtn.addEventListener('click', toggleDarkMode);
        initDashboard();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardPage);
    } else {
        initDashboardPage();
    }
})();
