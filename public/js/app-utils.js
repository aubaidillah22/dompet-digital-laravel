// Dompet Digital — Shared Utility Functions
// ===========================================
// Included via layouts/app.blade.php — available on all pages.

(function (window) {
    'use strict';

    var utils = {};

    // ── Configuration ──────────────────────────

    utils.API_BASE = (window.DOMpetConfig && window.DOMpetConfig.apiBase) || '/api';
    utils.LOGOUT_URL = (window.DOMpetConfig && window.DOMpetConfig.logoutUrl) || '/logout';
    utils.PASSWORD_MIN_LENGTH = 8;

    // ── Toast Notifications ────────────────────

    utils.showToast = function (message, type) {
        type = type || 'success';
        var iconMap = { success: 'success', error: 'error', info: 'info' };
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
        console.log('[' + type + '] ' + message);
    };

    // ── Currency Formatting ────────────────────

    utils.formatCurrency = function (amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    };

    utils.parseRupiahToNumber = function (rupiahString) {
        return parseInt((rupiahString || '').replace(/[^0-9]/g, '')) || 0;
    };

    utils.formatDate = function (dateString) {
        if (!dateString) return '-';
        var d = new Date(dateString + (dateString.includes('T') ? '' : 'T00:00:00'));
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    };

    utils.formatDateShort = function (dateString) {
        if (!dateString) return '-';
        var d = new Date(dateString + (dateString.includes('T') ? '' : 'T00:00:00'));
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    };

    utils.formatMonth = function (monthStr) {
        if (!monthStr) return '-';
        var parts = monthStr.split('-');
        var d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, 1);
        return d.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
    };

    utils.monthLabel = function (ym) {
        var names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        var parts = ym.split('-');
        return names[parseInt(parts[1], 10) - 1] + ' ' + parts[0];
    };

    utils.escapeHtml = function (text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };

    // ── Rupiah Input Auto-format ───────────────

    utils.autoFormatRupiah = function (input) {
        var value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            value = parseInt(value, 10).toLocaleString('id-ID');
            input.value = value;
        }
    };

    utils.initRupiahFormatting = function () {
        document.querySelectorAll('.rupiah-input').forEach(function (input) {
            if (input._rupiahHandler) return;
            input._rupiahHandler = function () { utils.autoFormatRupiah(input); };
            input.addEventListener('input', input._rupiahHandler);
        });
    };

    // ── Animate Value (count-up effect) ────────

    utils.animateValue = function (element, start, end, duration, isCurrency) {
        if (!element) return;
        duration = duration || 600;
        isCurrency = isCurrency !== false;
        var startTime = performance.now();

        function update(currentTime) {
            var elapsed = currentTime - startTime;
            var progress = Math.min(elapsed / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = Math.round(start + (end - start) * eased);
            element.textContent = isCurrency ? utils.formatCurrency(current) : current;
            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.classList.remove('count-up');
                void element.offsetWidth;
                element.classList.add('count-up');
            }
        }
        requestAnimationFrame(update);
    };

    // ── API Request ────────────────────────────

    utils.apiRequest = async function (endpoint, method, data, options) {
        method = method || 'GET';
        options = options || {};
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfMeta || !csrfMeta.content) {
            throw new Error('CSRF token tidak ditemukan. Silakan refresh halaman.');
        }
        var fetchOptions = {
            method: method,
            credentials: 'include',
            headers: {
                'X-CSRF-TOKEN': csrfMeta.content,
                'Accept': 'application/json'
            }
        };

        if (data && (method === 'POST' || method === 'PUT' || method === 'DELETE')) {
            var formData = new URLSearchParams();
            for (var key in data) {
                if (data.hasOwnProperty(key)) formData.append(key, data[key]);
            }
            if (method === 'PUT' || method === 'DELETE') {
                formData.append('_method', method);
                fetchOptions.method = 'POST';
            }
            fetchOptions.body = formData.toString();
            fetchOptions.headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        try {
            var response = await fetch(utils.API_BASE + '/' + endpoint, fetchOptions);

            if (response.status === 401) {
                localStorage.removeItem('user');
                window.location.href = '/login';
                throw new Error('Unauthorized');
            }

            var contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                throw new Error('Server returned non-JSON response');
            }

            var result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || result.message || 'Request failed');
            }

            return result;
        } catch (error) {
            console.error('API Error [' + endpoint + ']:', error);
            if (!options.silent) {
                utils.showToast(error.message, 'error');
            }
            throw error;
        }
    };

    // ── Auth ───────────────────────────────────

    utils.currentUser = null;

    utils.checkAuth = async function () {
        try {
            var data = await utils.apiRequest('auth/check', 'GET', null, { silent: true });
            if (!data.authenticated) {
                localStorage.removeItem('user');
                window.location.href = '/login';
                return null;
            }
            localStorage.setItem('user', JSON.stringify(data.user));
            utils.currentUser = data.user;
            return data.user;
        } catch (error) {
            localStorage.removeItem('user');
            window.location.href = '/login';
            return null;
        }
    };

    utils.logout = async function () {
        try {
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            await fetch(utils.LOGOUT_URL, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'X-CSRF-TOKEN': (csrfMeta && csrfMeta.content) || ''
                }
            });
        } catch (e) { /* silent */ }
        localStorage.removeItem('user');
        utils.showToast('Logout berhasil', 'success');
        setTimeout(function () { window.location.href = '/login'; }, 500);
    };

    // ── Sidebar ────────────────────────────────

    utils.toggleSidebar = function () {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        if (!sidebar) return;
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('show', sidebar.classList.contains('open'));
        } else {
            sidebar.classList.toggle('collapsed');
        }
    };

    utils.closeSidebar = function () {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        if (sidebar) sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('show');
    };

    // ── Dark Mode ──────────────────────────────

    utils.loadThemePreference = function () {
        var savedTheme = localStorage.getItem('darkMode');
        var isDark = savedTheme !== 'disabled';
        document.body.classList.toggle('dark-mode', isDark);
        var btn = document.getElementById('darkModeToggle');
        if (btn) btn.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    };

    utils.toggleDarkMode = function () {
        var isDark = !document.body.classList.contains('dark-mode');
        document.body.classList.toggle('dark-mode', isDark);
        localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
        var btn = document.getElementById('darkModeToggle');
        if (btn) btn.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    };

    // ── Modal Helpers ──────────────────────────

    utils.openModal = function (modalId) {
        var el = document.getElementById(modalId);
        if (el) { el.style.display = 'flex'; }
    };

    utils.closeModal = function (modalId) {
        var el = document.getElementById(modalId);
        if (el) el.style.display = 'none';
    };

    // ── Floating Modal Helpers ─────────────────

    utils.openFloatingModal = function (modalId) {
        var el = document.getElementById(modalId);
        var card = el && el.querySelector('.modal-floating-card');
        if (!el) return;
        if (card) card.classList.remove('closing');
        el.classList.add('show');
        // Re-trigger stagger animations
        setTimeout(function () {
            if (!card) return;
            var fields = card.querySelectorAll('.floating-field, .floating-actions, .floating-header, .floating-body');
            fields.forEach(function (f) {
                f.style.animation = 'none';
                void f.offsetWidth;
                f.style.animation = '';
            });
        }, 50);
    };

    utils.closeFloatingModal = function (modalId) {
        var el = document.getElementById(modalId);
        if (!el) return;
        var card = el.querySelector('.modal-floating-card');
        if (card) {
            card.classList.add('closing');
            setTimeout(function () {
                el.classList.remove('show');
                card.classList.remove('closing');
            }, 280);
        } else {
            el.classList.remove('show');
        }
    };

    // ── Splash Screen ──────────────────────────

    utils.destroySplash = function () {
        var splash = document.getElementById('splashScreen');
        if (splash) {
            if (window.splashDotInterval) clearInterval(window.splashDotInterval);
            splash.style.opacity = '0';
            splash.style.visibility = 'hidden';
            splash.style.pointerEvents = 'none';
            setTimeout(function () {
                if (splash.parentNode) splash.parentNode.removeChild(splash);
            }, 700);
        }
    };

    // ── Skeleton Loaders ───────────────────────

    utils.showSkeleton = function (tableId, cols, rows) {
        var tbody = document.getElementById(tableId);
        if (!tbody) return;
        rows = rows || 5;
        var widths = ['w-60', 'w-55', 'w-40', 'w-70', 'w-25', 'w-30'];
        var html = '';
        for (var r = 0; r < rows; r++) {
            html += '<tr class="skeleton-row">';
            for (var c = 0; c < cols; c++) {
                html += '<td><div class="skeleton-cell skeleton-loader ' + (widths[c % widths.length]) + '"></div></td>';
            }
            html += '</tr>';
        }
        tbody.innerHTML = html;
    };

    // ── Pagination ─────────────────────────────

    utils.renderPagination = function (containerId, pagination, goPageFn, changeSizeFn) {
        var container = document.getElementById(containerId);
        if (!container) return;
        var current = pagination.currentPage || 1;
        var totalPages = pagination.totalPages || 1;
        var total = pagination.total || 0;
        var perPage = pagination.perPage || 10;
        if (total === 0) { container.innerHTML = ''; return; }

        var start = ((current - 1) * perPage) + 1;
        var end = Math.min(current * perPage, total);
        var sizeOpts = [5, 10, 25, 50].map(function (n) {
            return '<option value="' + n + '" ' + (n === perPage ? 'selected' : '') + '>' + n + '</option>';
        }).join('');

        var btns = '';
        var maxV = 5;
        var sP = Math.max(1, current - Math.floor(maxV / 2));
        var eP = Math.min(totalPages, sP + maxV - 1);
        if (eP - sP < maxV - 1) sP = Math.max(1, eP - maxV + 1);

        if (sP > 1) {
            btns += '<button class="page-btn" onclick="' + goPageFn + '(1)">1</button>';
            if (sP > 2) btns += '<span class="page-ellipsis">...</span>';
        }
        for (var i = sP; i <= eP; i++) {
            btns += '<button class="page-btn ' + (i === current ? 'active' : '') + '" onclick="' + goPageFn + '(' + i + ')">' + i + '</button>';
        }
        if (eP < totalPages) {
            if (eP < totalPages - 1) btns += '<span class="page-ellipsis">...</span>';
            btns += '<button class="page-btn" onclick="' + goPageFn + '(' + totalPages + ')">' + totalPages + '</button>';
        }

        container.innerHTML = '<div class="pagination-wrapper">' +
            '<div class="pagination-left">' +
            '<label class="page-size-label">Tampilkan</label>' +
            '<select class="page-size-select" onchange="' + changeSizeFn + '(this.value)">' + sizeOpts + '</select>' +
            '<span class="page-size-label">data per halaman</span>' +
            '<span class="pagination-info">&mdash; Menampilkan ' + start + '-' + end + ' dari ' + total + '</span>' +
            '</div>' +
            '<div class="pagination-right">' +
            '<button class="page-btn ' + (current <= 1 ? 'disabled' : '') + '" onclick="' + goPageFn + '(' + (current - 1) + ')"' + (current <= 1 ? ' disabled' : '') + '><i class="fas fa-chevron-left"></i></button>' +
            btns +
            '<button class="page-btn ' + (current >= totalPages ? 'disabled' : '') + '" onclick="' + goPageFn + '(' + (current + 1) + ')"' + (current >= totalPages ? ' disabled' : '') + '><i class="fas fa-chevron-right"></i></button>' +
            '</div></div>';
    };

    // ── Search Table Filter ────────────────────

    utils.filterTransactionTable = function (transactions, query, typeLabelMap) {
        if (!query) return transactions;
        var q = query.toLowerCase().trim();
        return transactions.filter(function (t) {
            var typeLabel = typeLabelMap ? (typeLabelMap[t.type] || t.type) : t.type;
            var searchText = (
                (t.transaction_date || '') + ' ' +
                (typeLabel || '') + ' ' +
                (t.category || '') + ' ' +
                (t.amount ? t.amount.toString() : '') + ' ' +
                (t.note || '') + ' ' +
                utils.formatCurrency(t.amount || 0).replace(/[^a-zA-Z0-9]/g, '')
            ).toLowerCase();
            var cleanQ = q.replace(/[^a-zA-Z0-9]/g, '');
            return searchText.replace(/[^a-zA-Z0-9]/g, '').includes(cleanQ) || searchText.includes(q);
        });
    };

    // ── Expose ─────────────────────────────────

    // For backwards compatibility, expose as global functions
    window.showToast = utils.showToast;
    window.formatCurrency = utils.formatCurrency;
    window.parseRupiahToNumber = utils.parseRupiahToNumber;
    window.formatDate = utils.formatDate;
    window.escapeHtml = utils.escapeHtml;
    window.autoFormatRupiah = utils.autoFormatRupiah;
    window.apiRequest = utils.apiRequest;
    window.checkAuth = utils.checkAuth;
    window.logout = utils.logout;
    window.toggleSidebar = utils.toggleSidebar;
    window.closeSidebar = utils.closeSidebar;
    window.loadThemePreference = utils.loadThemePreference;
    window.toggleDarkMode = utils.toggleDarkMode;
    window.openModal = utils.openModal;
    window.closeModal = utils.closeModal;
    window.openFloatingModal = utils.openFloatingModal;
    window.closeFloatingModal = utils.closeFloatingModal;
    window.destroySplash = utils.destroySplash;
    window.showSkeleton = utils.showSkeleton;
    window.renderPagination = utils.renderPagination;
    window.animateValue = utils.animateValue;
    window.monthLabel = utils.monthLabel;
    window.formatDateShort = utils.formatDateShort;
    window.formatMonth = utils.formatMonth;
    window.initRupiahFormatting = utils.initRupiahFormatting;

    // Store as AppUtils for programmatic access
    window.AppUtils = utils;

})(window);
