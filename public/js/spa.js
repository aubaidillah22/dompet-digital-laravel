// SPA Navigation — Dompet Digital
// =================================
// Intercepts internal navigation clicks, fetches content via AJAX,
// and swaps only the main content area. Sidebar, topbar, and footer stay fixed.

(function() {
    'use strict';

    // ── Config ──────────────────────────────
    const SPA_LINKS_SELECTOR = 'a[href^="/dashboard"], a[href^="/dompet"], a[href^="/tabungan"], a[href^="/profile"]';
    const CONTENT_SELECTOR = 'main.main-content';
    
    // Page transition durations (ms)
    const TRANSITION_OUT_DURATION = 180;
    const TRANSITION_IN_DURATION = 300;
    
    // Inject page transition styles once
    (function injectTransitionStyles() {
        if (document.getElementById('spa-transition-styles')) return;
        const style = document.createElement('style');
        style.id = 'spa-transition-styles';
        style.textContent = `
            /* Page transition: leave */
            main.main-content.page-leave {
                opacity: 0;
                transform: translateY(-12px);
                transition: opacity ${TRANSITION_OUT_DURATION}ms cubic-bezier(0.4, 0, 0.2, 1),
                            transform ${TRANSITION_OUT_DURATION}ms cubic-bezier(0.4, 0, 0.2, 1);
                pointer-events: none;
                user-select: none;
            }
            /* Page transition: enter */
            main.main-content.page-enter {
                opacity: 0;
                transform: translateY(16px);
                animation: pageEnter ${TRANSITION_IN_DURATION}ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
            }
            @keyframes pageEnter {
                0% { opacity: 0; transform: translateY(16px); }
                100% { opacity: 1; transform: translateY(0); }
            }

        `;
        document.head.appendChild(style);
    })();

    let currentUrl = window.location.pathname;
    
    // Track styles added by SPA navigation so we can clean them up
    const SPA_STYLE_MARKER = 'data-spa-style';
    let styleCounter = 0;

    // ── Utilities ───────────────────────────

    function isInternalLink(href) {
        if (!href || href === '#' || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
            return false;
        }
        try {
            const url = new URL(href, window.location.origin);
            return url.hostname === window.location.hostname && 
                   (url.pathname.startsWith('/dashboard') || 
                    url.pathname.startsWith('/dompet') || 
                    url.pathname.startsWith('/tabungan') ||
                    url.pathname.startsWith('/profile'));
        } catch (e) {
            return false;
        }
    }

    function isSamePage(pathname) {
        return pathname === currentUrl;
    }

    // ── Content Extraction ──────────────────

    function extractContent(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const content = doc.querySelector(CONTENT_SELECTOR);
        return content ? content.innerHTML : null;
    }

    function extractTitle(html) {
        const match = html.match(/<title>([^<]*)<\/title>/i);
        return match ? match[1] : 'Dompet Digital';
    }

    // ── Style Management ────────────────────
    // Remove previously added SPA styles, then add new ones.
    // Prevents accumulation of duplicate :root variable definitions.

    function cleanOldSpaStyles() {
        document.querySelectorAll(`style[${SPA_STYLE_MARKER}]`).forEach(el => el.remove());
    }

    function applyNewStyles(html) {
        cleanOldSpaStyles();

        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const styles = doc.querySelectorAll('style');

        styles.forEach(style => {
            const text = style.textContent.trim();
            if (!text) return;

            // Check if identical style already exists (from initial page load)
            const existingStyles = document.querySelectorAll('style:not([' + SPA_STYLE_MARKER + '])');
            let isDuplicate = false;
            existingStyles.forEach(es => {
                if (es.textContent.trim() === text) isDuplicate = true;
            });

            if (!isDuplicate) {
                const newStyle = style.cloneNode(true);
                newStyle.setAttribute(SPA_STYLE_MARKER, '' + (++styleCounter));
                document.head.appendChild(newStyle);
            }
        });
    }

    // ── Script Execution ────────────────────
    // Scripts from @push('scripts') in Blade are rendered outside
    // main.main-content (at the bottom of <body>). We must extract
    // those too, not just scripts inside the content area.

    function executeScripts(newHtml) {
        const temp = document.createElement('div');
        temp.innerHTML = newHtml;
        const scripts = temp.querySelectorAll('script');

        scripts.forEach(oldScript => {
            const newScript = document.createElement('script');
            Array.from(oldScript.attributes).forEach(attr => {
                newScript.setAttribute(attr.name, attr.value);
            });
            if (oldScript.textContent) {
                newScript.textContent = oldScript.textContent;
            }
            const hasSrc = oldScript.hasAttribute('src');
            document.body.appendChild(newScript);
            // Only remove inline scripts immediately (they already executed)
            if (!hasSrc && document.body.contains(newScript)) {
                document.body.removeChild(newScript);
            }
        });
    }

    // Extract page-specific scripts from the fetched HTML's <body>
    // (including @push('scripts') placed outside main.main-content),
    // while excluding:
    //   - scripts inside main.main-content (handled by executeScripts already)
    //   - scripts from <head> (spa.js, Chart.js, etc.) to avoid re-executing
    //     library/SPA code and creating duplicate observers & listeners.
    function extractPageScripts(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Only get scripts from <body> — head scripts (libraries, spa.js)
        // must NOT be re-executed to avoid duplicates.
        const bodyScripts = Array.from(doc.body.querySelectorAll('script'));
        
        // Get scripts inside main content (already handled by executeScripts)
        const content = doc.querySelector(CONTENT_SELECTOR);
        const contentScripts = content ? Array.from(content.querySelectorAll('script')) : [];
        
        // Filter: only body scripts OUTSIDE main.main-content
        const pageScripts = bodyScripts.filter(s => !contentScripts.includes(s));
        
        return pageScripts.map(s => s.outerHTML).join('\n');
    }

    // ── Load Content ────────────────────────

    function showContentSkeleton() {
        const content = document.querySelector(CONTENT_SELECTOR);
        if (!content) return;
        // Fill the content area with a perfectly centered spinner
        content.style.display = 'flex';
        content.style.alignItems = 'center';
        content.style.justifyContent = 'center';
        content.style.padding = '0';
        content.style.minHeight = '100vh';
        content.innerHTML = `
            <div style="text-align: center;">
                <div style="
                    width: 44px; height: 44px; margin: 0 auto 14px;
                    border-radius: 50%;
                    border: 3px solid rgba(240,180,41,0.1);
                    border-top-color: #f0b429;
                    animation: spin 0.7s linear infinite;
                "></div>
                <p style="color: var(--text-muted, #94a3b8); font-size: 0.85rem; font-weight: 500; letter-spacing: 0.3px;">Memuat...</p>
            </div>
        `;
    }

    function clearContentStyles() {
        const content = document.querySelector(CONTENT_SELECTOR);
        if (!content) return;
        content.style.display = '';
        content.style.alignItems = '';
        content.style.justifyContent = '';
        content.style.padding = '';
        content.style.minHeight = '';
        content.style.boxSizing = '';
    }

    // ── Sidebar Helpers ────────────────────

    function getSidebar() {
        return document.querySelector('.sidebar') || document.querySelector('aside.sidebar');
    }

    function normalizePath(p) {
        // Remove trailing slash and ensure consistent format
        let normalized = p.replace(/\/$/, '');
        // Handle root path
        if (!normalized) normalized = '/';
        return normalized;
    }

    // Route map: path → nav-link href match (handles partial matches for prefixes)
    const ROUTE_MAP = [
        { prefix: '/dashboard', href: '/dashboard' },
        { prefix: '/dompet',     href: '/dompet' },
        { prefix: '/tabungan',   href: '/tabungan' },
        { prefix: '/profile',    href: '/profile' },
    ];

    function matchRoute(pathname) {
        const normalized = normalizePath(pathname);
        for (const route of ROUTE_MAP) {
            if (normalized === route.href) {
                return route.href;
            }
            // Check prefix match: ensure next char after prefix is / or ?
            const afterPrefix = normalized.slice(route.prefix.length);
            if (afterPrefix.length > 0 && (afterPrefix[0] === '/' || afterPrefix[0] === '?')) {
                return route.href;
            }
        }
        return null;
    }

    function updateActiveNav(pathname) {
        const sidebar = getSidebar();
        if (!sidebar) return;

        const matchedHref = matchRoute(pathname);
        if (!matchedHref) return;

        // Remove active from ALL sidebar nav-links
        sidebar.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });

        // Activate the matching nav-link
        sidebar.querySelectorAll('.nav-link').forEach(link => {
            const href = link.getAttribute('href');
            if (href && normalizePath(href) === matchedHref) {
                link.classList.add('active');
            }
        });
    }

    // Backup: extract sidebar nav state from fetched HTML and apply it
    // This handles cases where the server-rendered sidebar has the correct active state
    function updateSidebarFromHtml(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const fetchedSidebar = doc.querySelector('.sidebar') || doc.querySelector('aside.sidebar');
        if (!fetchedSidebar) return;

        const currentSidebar = getSidebar();
        if (!currentSidebar) return;

        // Find which nav-link is active in the fetched sidebar
        const activeLink = fetchedSidebar.querySelector('.nav-link.active');
        if (!activeLink) return;

        const activeHref = activeLink.getAttribute('href');
        if (!activeHref) return;

        // Remove active from all sidebar nav-links
        currentSidebar.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });

        // Activate the matching one
        currentSidebar.querySelectorAll('.nav-link').forEach(link => {
            const href = link.getAttribute('href');
            if (href && normalizePath(href) === normalizePath(activeHref)) {
                link.classList.add('active');
            }
        });
    }

    // ── Page Transition Animations ──────────
    let isNavigating = false;

    function animatePageOut() {
        return new Promise(function (resolve) {
            const content = document.querySelector(CONTENT_SELECTOR);
            if (!content) { resolve(); return; }
            content.classList.add('page-leave');
            setTimeout(function () {
                content.classList.remove('page-leave');
                resolve();
            }, TRANSITION_OUT_DURATION);
        });
    }

    function animatePageIn() {
        return new Promise(function (resolve) {
            const content = document.querySelector(CONTENT_SELECTOR);
            if (!content) { resolve(); return; }
            content.classList.remove('page-leave');
            content.classList.add('page-enter');
            // Remove class after animation completes
            setTimeout(function () {
                content.classList.remove('page-enter');
                resolve();
            }, TRANSITION_IN_DURATION);
        });
    }

    async function loadContent(targetPath) {
        // Step 1: Animate current page out
        await animatePageOut();

        // Step 2: Show skeleton briefly
        showContentSkeleton();

        try {
            const response = await fetch(targetPath, {
                credentials: 'include',
                headers: {
                    'Accept': 'text/html, application/xhtml+xml',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            if (!response.ok) {
                window.location.href = targetPath;
                return false;
            }

            const html = await response.text();

            const newContent = extractContent(html);
            if (!newContent) {
                window.location.href = targetPath;
                return false;
            }

            // Update title
            document.title = extractTitle(html);

            // Apply page-specific styles, cleaning old ones
            applyNewStyles(html);

            // Swap content
            const contentArea = document.querySelector(CONTENT_SELECTOR);
            if (!contentArea) {
                window.location.href = targetPath;
                return false;
            }
            contentArea.innerHTML = newContent;
            clearContentStyles();

            // Execute scripts — both content scripts AND page-level scripts
            // (page-level scripts come from @push('scripts') in Blade templates
            // and are rendered outside main.main-content by @stack('scripts'))
            const pageScriptsHtml = extractPageScripts(html);
            const allScripts = newContent + pageScriptsHtml;
            try {
                executeScripts(allScripts);
            } catch (scriptError) {
                // Script errors should not break SPA navigation
                console.warn('SPA script execution error (non-fatal):', scriptError);
            }

            // Update nav highlight — try both methods for reliability
            updateSidebarFromHtml(html);
            updateActiveNav(targetPath);

            currentUrl = targetPath;

            // Step 3: Animate new page in
            await animatePageIn();

            return true;

        } catch (error) {
            console.error('SPA navigation error:', error);
            window.location.href = targetPath;
            return false;
        }
    }

    // ── Navigation ──────────────────────────

    async function navigateTo(url) {
        if (isSamePage(url.pathname)) return;
        if (isNavigating) return; // Prevent concurrent navigations
        isNavigating = true;
        try {
            const success = await loadContent(url.pathname);
            if (success) {
                window.history.pushState({ path: url.pathname }, '', url.pathname);
            }
        } finally {
            isNavigating = false;
        }
    }

    // ── Event Handlers ──────────────────────

    function handleLinkClick(event) {
        const link = event.currentTarget;
        const href = link.getAttribute('href');

        if (!isInternalLink(href)) return;

        // Ignore if modified click (new tab/window)
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        event.preventDefault();

        const url = new URL(href, window.location.origin);
        navigateTo(url);
    }

    // ── Mutation Observer for Dynamic Links ─

    function attachClickHandlers() {
        document.querySelectorAll(SPA_LINKS_SELECTOR).forEach(link => {
            if (link._spaHandler) {
                link.removeEventListener('click', link._spaHandler);
            }
            const handler = handleLinkClick.bind(link);
            link._spaHandler = handler;
            link.addEventListener('click', handler);
        });
    }

    const observer = new MutationObserver(() => {
        attachClickHandlers();
    });

    // ── Initialization ──────────────────────

    function init() {
        attachClickHandlers();

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });

        // Handle browser back/forward
        window.addEventListener('popstate', function(event) {
            const path = event.state ? event.state.path : window.location.pathname;
            if (path && path !== currentUrl) {
                loadContent(path);
            }
        });

        // Set initial active nav — use both methods for reliability
        updateActiveNav(window.location.pathname);

        // Also observe sidebar for any unwanted class changes
        // (e.g. from scripts in loaded pages re-initializing the sidebar)
        const sidebarObserver = new MutationObserver(function(mutations) {
            // Only react if the active class on a sidebar nav-link was toggled OFF
            // and no nav-link is currently active — restore it
            const sidebar = getSidebar();
            if (!sidebar) return;
            const hasActive = sidebar.querySelector('.nav-link.active');
            if (!hasActive) {
                // Restore based on current URL
                updateActiveNav(window.location.pathname);
            }
        });
        // Start observing after a small delay to avoid initial mutation noise
        setTimeout(function() {
            const sidebar = getSidebar();
            if (sidebar) {
                sidebarObserver.observe(sidebar, {
                    attributes: true,
                    attributeFilter: ['class'],
                    subtree: true,
                });
            }
        }, 500);
    }

    // ── Start ───────────────────────────────

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
