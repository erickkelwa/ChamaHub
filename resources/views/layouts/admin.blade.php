<!DOCTYPE html>
<html lang="en" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#4f46e5">
    <!-- iOS / Android PWA Meta Tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ChamaHub">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="ChamaHub">
    <meta name="msapplication-TileColor" content="#4f46e5">
    <meta name="msapplication-TileImage" content="/icons/icon-144.png">
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="/icons/icon-152.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192.png">
    <link rel="apple-touch-startup-image" href="/icons/icon-512.png">
    <link rel="manifest" href="/manifest.json">
    <title>ChamaHub - Admin & Member Portal</title>
    <!-- Favicon (Magnificent Aura Logo) -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><defs><linearGradient id=%22grad%22 x1=%220%25%22 y1=%220%25%22 x2=%22100%25%22 y2=%22100%25%22><stop offset=%220%25%22 stop-color=%22%234f46e5%22/><stop offset=%2250%25%22 stop-color=%22%23ec4899%22/><stop offset=%22100%25%22 stop-color=%22%23f59e0b%22/></linearGradient></defs><path d=%22M50 5 C80 5 95 25 95 50 C95 80 75 95 50 95 C20 95 5 75 5 50 C5 20 25 5 50 5 Z%22 fill=%22url(%23grad)%22 /><text x=%2250%22 y=%2268%22 font-size=%2250%22 font-family=%22Arial, sans-serif%22 font-weight=%22bold%22 text-anchor=%22middle%22 fill=%22white%22>∞</text></svg>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ============================================================
           CSS CUSTOM PROPERTIES — LIGHT MODE DEFAULTS
        ============================================================ */
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --sidebar-width: 280px;

            --bg-body:          #f8fafc;
            --bg-card:          #ffffff;
            --bg-card-hover:    #f8faff;
            --bg-table-stripe:  #f9fafb;

            --text-primary:     #1e293b;
            --text-muted:       #64748b;
            --text-heading:     #0f172a;

            --border-color:     #e2e8f0;
            --table-border:     #f1f5f9;

            --input-bg:         #ffffff;
            --input-border:     #cbd5e1;
            --input-text:       #1e293b;
            --input-placeholder:#94a3b8;

            --alert-bg:         #ffffff;
            --sidebar-user-bg:  rgba(0,0,0,0.12);
        }

        /* ============================================================
           DARK MODE OVERRIDES
        ============================================================ */
        html.dark-mode {
            --bg-body:          #0f172a;
            --bg-card:          #1e293b;
            --bg-card-hover:    #253348;
            --bg-table-stripe:  #172133;

            --text-primary:     #e2e8f0;
            --text-muted:       #94a3b8;
            --text-heading:     #f1f5f9;

            --border-color:     #334155;
            --table-border:     #2d3f57;

            --input-bg:         #1e293b;
            --input-border:     #334155;
            --input-text:       #e2e8f0;
            --input-placeholder:#64748b;

            --alert-bg:         #1e293b;
            --sidebar-user-bg:  rgba(0,0,0,0.3);
        }

        /* ============================================================
           BASE
        ============================================================ */
        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            overflow-x: hidden;
            width: 100%;
            max-width: 100%;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Outfit', sans-serif;
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        h1, h2, h3, h4, h5, h6 { color: var(--text-heading); }
        .text-muted { color: var(--text-muted) !important; }
        .form-label { color: var(--text-muted); font-weight: 500; font-size: 0.88rem; }

        /* ── Cards ── */
        .card {
            border: 1px solid var(--border-color) !important;
            border-radius: 16px;
            box-shadow: 0 4px 20px -8px rgba(0,0,0,0.08);
            background: var(--bg-card) !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 36px -12px rgba(0,0,0,0.13);
        }

        /* ── Tables ── */
        .table { color: var(--text-primary); }
        .table > :not(caption) > * > * {
            padding: 0.9rem;
            border-bottom-color: var(--table-border);
            background-color: transparent;
            color: var(--text-primary);
        }
        .table-striped > tbody > tr:nth-of-type(odd) > * { background-color: var(--bg-table-stripe); }
        .table-hover > tbody > tr:hover > * { background-color: var(--bg-card-hover); }
        .table thead th {
            background-color: var(--bg-table-stripe) !important;
            color: var(--text-muted) !important;
            border-bottom: 1px solid var(--border-color) !important;
            font-size: 0.78rem;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        /* ── Inputs ── */
        .form-control, .form-select {
            background-color: var(--input-bg) !important;
            border-color: var(--input-border) !important;
            color: var(--input-text) !important;
            border-radius: 10px;
            transition: background-color 0.3s, border-color 0.3s, color 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.15) !important;
        }
        .form-control::placeholder { color: var(--input-placeholder) !important; }

        /* ── Alerts ── */
        .alert {
            background-color: var(--alert-bg) !important;
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        /* ── Modals in dark mode ── */
        html.dark-mode .modal-content {
            background-color: var(--bg-card);
            color: var(--text-primary);
            border-color: var(--border-color);
        }
        html.dark-mode .modal-header,
        html.dark-mode .modal-footer { border-color: var(--border-color); }

        /* ── Buttons ── */
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            padding: 10px 22px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -6px rgba(79,70,229,0.6);
        }
        .badge { padding: 0.5em 0.8em; border-radius: 8px; font-weight: 500; }

        /* ── Quick action cards ── */
        .dashboard-btn {
            border-radius: 16px;
            border: 2px solid var(--border-color);
            background: var(--bg-card);
            color: var(--text-primary);
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .dashboard-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(79,70,229,0.12);
            border-color: #4f46e5;
            background: var(--bg-card-hover);
        }

        /* ============================================================
           SIDEBAR
        ============================================================        /* ── Sidebar Styles ── */
        .sidebar {
            height: 100vh;
            background: var(--primary-gradient);
            color: white;
            box-shadow: 4px 0 20px rgba(0,0,0,0.12);
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            z-index: 1050;
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        /* ── Magnificent Morphing Aura Logo ── */
        .chama-logo-container {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #4f46e5 0%, #ec4899 50%, #f59e0b 100%);
            background-size: 200% 200%;
            border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
            animation: morphLogo 8s ease-in-out infinite, gradientShift 8s ease infinite;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(236, 72, 153, 0.4);
            margin-right: 12px;
            position: relative;
            flex-shrink: 0;
        }
        .chama-logo-container::before {
            content: '';
            position: absolute;
            inset: 3px;
            background: rgba(255,255,255,0.15);
            border-radius: inherit;
            border: 1px solid rgba(255,255,255,0.3);
            animation: morphLogo 8s ease-in-out infinite reverse;
        }
        .chama-logo-icon {
            color: white;
            font-size: 1.3rem;
            z-index: 2;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }
        @keyframes morphLogo {
            0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; }
            34% { border-radius: 70% 30% 50% 50% / 30% 30% 70% 70%; }
            67% { border-radius: 100% 60% 60% 100% / 100% 100% 60% 60%; }
        }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Mobile: hide sidebar off-screen */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { 
                margin-left: 0 !important; 
                padding: 1rem !important; 
                width: 100% !important; 
                max-width: 100% !important; 
                min-width: 0 !important;
                overflow-x: hidden;
                box-sizing: border-box;
            }
            /* Tighten inner content padding on small screens */
            .main-content .p-4 { padding: 0.75rem !important; }
            /* Shrink oversized headings on mobile */
            h2 { font-size: 1.35rem !important; }
            h3 { font-size: 1.15rem !important; }
            /* Ensure stat value doesn't overflow */
            .card h3 { font-size: 1.2rem !important; word-break: break-word; }
        }

        @media (max-width: 575.98px) {
            /* Extra-small: reduce card padding */
            .card .p-4 { padding: 1rem !important; }
            /* Make page header buttons full-width on xs */
            .page-header-actions { width: 100%; }
            .page-header-actions .btn { width: 100%; justify-content: center; }
        }

        /* Desktop: fixed sidebar, push content cleanly without horizontal overflow */
        @media (min-width: 992px) {
            .main-content { 
                margin-left: var(--sidebar-width) !important; 
                width: calc(100% - var(--sidebar-width)) !important;
                max-width: calc(100% - var(--sidebar-width)) !important;
                min-width: 0 !important;
                padding: 2rem 2.5rem; 
                min-height: 100vh;
                box-sizing: border-box;
            }
            .mobile-header { display: none !important; }
        }

        /* Custom sleek scrollbar for sidebar */
        .sidebar-nav-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-nav-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-nav-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 10px;
        }
        .sidebar-nav-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.45);
        }

        /* Custom sleek scrollbar for responsive tables */
        .table-responsive {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 12px;
        }
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: var(--bg-table-stripe);
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--input-border);
            border-radius: 4px;
        }

        .sidebar-header {
            padding: 1.5rem;
            background: rgba(255,255,255,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        .sidebar-header h4 { font-weight: 700; letter-spacing: 1px; margin: 0; }

        .nav-link-custom {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            padding: 0.65rem 1.5rem; /* Reduced from 0.9rem to save space */
            display: flex; align-items: center;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
        }
        .nav-link-custom i { font-size: 1.1rem; margin-right: 12px; } /* Slightly smaller icon */
        .nav-link-custom:hover, .nav-link-custom.active {
            background-color: rgba(255,255,255,0.18);
            color: white;
            border-left-color: #fff;
            transform: translateX(4px);
        }

        /* Backdrop overlay */
        .sidebar-backdrop {
            position: fixed; top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 1040; display: none;
            backdrop-filter: blur(4px);
        }
        .sidebar-backdrop.show { display: block; }

        /* Mobile header bar */
        .mobile-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1rem 1.25rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: sticky; top: 0; z-index: 1030;
        }

        /* ── Dark-mode toggle (sidebar) ── */
        .dark-mode-toggle {
            display: flex; align-items: center; gap: 10px;
            padding: 0.65rem 1.5rem;
            color: rgba(255,255,255,0.85);
            cursor: pointer;
            border: none; background: transparent; width: 100%;
            font-size: 0.95rem; font-weight: 500;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            font-family: 'Outfit', sans-serif;
        }
        .dark-mode-toggle:hover {
            background: rgba(255,255,255,0.18);
            color: white; border-left-color: #fff;
        }
        .dark-mode-toggle i { font-size: 1.2rem; }

        .toggle-track {
            width: 40px; height: 22px;
            background: rgba(255,255,255,0.25);
            border-radius: 999px; position: relative;
            transition: background 0.3s; margin-left: auto; flex-shrink: 0;
        }
        .toggle-track.on { background: #fbbf24; }
        .toggle-thumb {
            position: absolute; top: 3px; left: 3px;
            width: 16px; height: 16px; border-radius: 50%;
            background: white; transition: transform 0.3s;
            box-shadow: 0 1px 4px rgba(0,0,0,0.3);
        }
        .toggle-track.on .toggle-thumb { transform: translateX(18px); }

        /* ── Animation ── */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeIn 0.5s ease-out forwards; }

        /* ═══════════════════════════════════════════════════════════════════
           MOBILE BOTTOM NAVIGATION BAR
        ═══════════════════════════════════════════════════════════════════ */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: calc(64px + env(safe-area-inset-bottom));
            background: var(--bg-card);
            border-top: 1px solid var(--border-color);
            z-index: 1025;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
            padding-bottom: env(safe-area-inset-bottom);
        }
        html.dark-mode .bottom-nav {
            box-shadow: 0 -4px 20px rgba(0,0,0,0.3);
        }
        .bottom-nav-inner {
            display: flex;
            align-items: stretch;
            justify-content: space-around;
            height: 64px;
        }
        .bottom-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.68rem;
            font-weight: 500;
            padding: 0 4px;
            border: none;
            background: transparent;
            cursor: pointer;
            position: relative;
            transition: color 0.2s ease;
            min-height: 48px; /* WCAG touch target */
        }
        .bottom-nav-item i {
            font-size: 1.3rem;
            line-height: 1;
            transition: transform 0.2s ease;
        }
        .bottom-nav-item.active,
        .bottom-nav-item:hover {
            color: #4f46e5;
        }
        .bottom-nav-item.active i {
            transform: translateY(-2px);
        }
        .bottom-nav-item.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 32px;
            height: 3px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            border-radius: 0 0 4px 4px;
        }
        .bottom-nav-badge {
            position: absolute;
            top: 6px;
            right: calc(50% - 18px);
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 17px;
            height: 17px;
            font-size: 0.6rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--bg-card);
        }

        /* Show bottom nav only on small screens */
        @media (max-width: 991.98px) {
            .bottom-nav { display: block; }
            /* Add padding so content isn't hidden behind bottom nav */
            .main-content {
                padding-bottom: calc(72px + env(safe-area-inset-bottom)) !important;
            }
        }

        /* Safe-area insets for iPhone notch / home indicator */
        .mobile-header {
            padding-top: max(1rem, env(safe-area-inset-top));
            padding-left: max(1.25rem, env(safe-area-inset-left));
            padding-right: max(1.25rem, env(safe-area-inset-right));
        }

        /* ═══════════════════════════════════════════════════════════════════
           PWA INSTALL BANNER
        ═══════════════════════════════════════════════════════════════════ */
        #pwa-install-banner {
            display: none;
            position: fixed;
            bottom: calc(72px + env(safe-area-inset-bottom));
            left: 1rem;
            right: 1rem;
            z-index: 2000;
            animation: slideUpBanner 0.4s cubic-bezier(0.34,1.56,0.64,1) forwards;
        }
        @keyframes slideUpBanner {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        #pwa-install-banner .banner-card {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 60%, #ec4899 100%);
            color: white;
            border-radius: 20px;
            padding: 1.1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 12px 40px rgba(79,70,229,0.45);
        }
        #pwa-install-banner .banner-logo {
            width: 46px;
            height: 46px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            border: 1px solid rgba(255,255,255,0.3);
        }
        #pwa-install-banner .banner-text { flex: 1; min-width: 0; }
        #pwa-install-banner .banner-text strong { display: block; font-size: 0.95rem; font-weight: 700; }
        #pwa-install-banner .banner-text span  { font-size: 0.78rem; opacity: 0.82; }
        #pwa-install-banner .btn-install {
            background: white;
            color: #4f46e5;
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            flex-shrink: 0;
            font-family: 'Outfit', sans-serif;
            transition: all 0.2s ease;
        }
        #pwa-install-banner .btn-install:hover { transform: scale(1.05); }
        #pwa-install-banner .btn-dismiss {
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.7);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0 0 0 0.25rem;
            line-height: 1;
            flex-shrink: 0;
        }

        /* ═══════════════════════════════════════════════════════════════════
           PULL-TO-REFRESH INDICATOR
        ═══════════════════════════════════════════════════════════════════ */
        #ptr-indicator {
            position: fixed;
            top: -60px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3000;
            background: var(--bg-card);
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border: 2px solid var(--border-color);
            transition: top 0.15s ease;
            pointer-events: none;
        }
        #ptr-indicator.visible { top: 12px; }
        #ptr-indicator.refreshing i { animation: spinRefresh 0.8s linear infinite; }
        @keyframes spinRefresh { to { transform: rotate(360deg); } }
    </style>
    @stack('styles')

    {{-- Apply dark mode BEFORE render to prevent flash --}}
    <script>
        (function() {
            if (localStorage.getItem('chamahub_dark_mode') === 'true') {
                document.getElementById('htmlRoot').classList.add('dark-mode');
            }
        })();
    </script>
</head>
<body>

    <!-- ── Mobile Header ── -->
    <div class="mobile-header d-flex justify-content-between align-items-center d-lg-none">
        <div class="d-flex align-items-center">
            <button class="btn text-white me-2 p-1 fs-3 border-0 bg-transparent" id="mobileMenuBtn" aria-label="Toggle Navigation">
                <i class="bi bi-list"></i>
            </button>
            <h5 class="mb-0 fw-bold d-flex align-items-center tracking-tight">
                <div class="chama-logo-container" style="width:30px; height:30px; margin-right:8px;">
                    <i class="bi bi-infinity chama-logo-icon" style="font-size: 1.1rem;"></i>
                </div>
                ChamaHub
            </h5>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button id="mobileDarkToggle" class="btn btn-sm text-white border-0 bg-transparent fs-5 p-1" title="Toggle dark mode">
                <i class="bi bi-moon-stars-fill" id="mobileDarkIcon"></i>
            </button>
            <span class="badge bg-white text-dark rounded-pill px-3 py-2 text-capitalize fw-bold shadow-sm">
                {{ auth()->user()->role }}
            </span>
        </div>
    </div>

    <!-- ── Pull-to-Refresh Indicator ── -->
    <div id="ptr-indicator">
        <i class="bi bi-arrow-clockwise" style="color: #4f46e5; font-size: 1.1rem;"></i>
    </div>

    <!-- ── PWA Install Banner ── -->
    <div id="pwa-install-banner">
        <div class="banner-card">
            <div class="banner-logo">∞</div>
            <div class="banner-text">
                <strong>Install ChamaHub</strong>
                <span>Add to Home Screen for the best experience</span>
            </div>
            <button class="btn-install" id="pwa-install-btn">Install</button>
            <button class="btn-dismiss" id="pwa-dismiss-btn" aria-label="Dismiss">✕</button>
        </div>
    </div>

    <!-- ── Backdrop ── -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="d-flex">

        <!-- ── Sidebar ── -->
        <div class="sidebar" id="sidebar">

            <div class="sidebar-header d-flex justify-content-between align-items-center">
                <h4 class="d-flex align-items-center mb-0 tracking-tight fw-bold" style="letter-spacing: -0.5px;">
                    <div class="chama-logo-container">
                        <i class="bi bi-infinity chama-logo-icon"></i>
                    </div>
                    ChamaHub
                </h4>
                <button class="btn text-white d-lg-none p-0 fs-4 border-0 bg-transparent" id="closeSidebarBtn">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="mt-3 flex-grow-1 sidebar-nav-scroll" style="overflow-y:auto; overflow-x:hidden; min-height:0;">
                <a href="/dashboard" class="nav-link-custom {{ request()->is('dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
                
                <a href="{{ route('decisions.index') }}" class="nav-link-custom {{ request()->is('decisions*') ? 'active' : '' }}">
                    <i class="bi bi-ui-radios"></i> Decisions & Polls
                </a>

                <a href="{{ route('admin.meetings.index') }}" class="nav-link-custom {{ request()->is('admin/meetings*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event-fill"></i> Meetings
                </a>

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'treasurer')
                    <a href="{{ route('admin.members.index') }}" class="nav-link-custom {{ request()->is('admin/members*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Members
                    </a>
                    <a href="{{ route('admin.contributions.index') }}" class="nav-link-custom {{ request()->is('admin/contributions*') ? 'active' : '' }}">
                        <i class="bi bi-wallet2"></i> Contributions
                    </a>
                    <a href="{{ route('admin.loans.index') }}" class="nav-link-custom {{ request()->is('admin/loans*') ? 'active' : '' }}">
                        <i class="bi bi-bank2"></i> Loans
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="nav-link-custom {{ request()->is('admin/reports*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Reports
                    </a>
                    <a href="{{ route('admin.dividends.index') }}" class="nav-link-custom {{ request()->is('admin/dividends*') ? 'active' : '' }}">
                        <i class="bi bi-pie-chart-fill"></i> Dividends
                    </a>
                    <a href="{{ route('admin.fines.index') }}" class="nav-link-custom {{ request()->is('admin/fines*') ? 'active' : '' }}">
                        <i class="bi bi-exclamation-triangle-fill"></i> Penalties & Fines
                    </a>
                @endif
            </div>

            <!-- Sidebar Footer Sign Out button for mobile & desktop -->
            <div class="p-3 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-full d-flex align-items-center justify-content-center gap-2 rounded-3 text-white border-white-50 w-100 py-2" style="font-size: 0.9rem; font-weight: 600;">
                        <i class="bi bi-box-arrow-right"></i> Sign Out
                    </button>
                </form>
            </div>

        </div>

        <!-- ── Main Content ── -->
        <div class="main-content flex-grow-1" style="display: flex; flex-direction: column;">

            <!-- ── Top Navbar ── -->
            <div class="top-navbar d-none d-lg-flex align-items-center justify-content-between px-4 py-2 border-bottom" style="background: var(--bg-card); position: sticky; top: 0; z-index: 1020; min-height: 60px;">
                <!-- Page breadcrumb / title area -->
                <div class="d-flex align-items-center gap-3">
                    <div class="chama-logo-container" style="width: 26px; height: 26px; margin-right: -4px; box-shadow: none;">
                        <i class="bi bi-infinity chama-logo-icon" style="font-size: 0.95rem;"></i>
                    </div>
                    <span class="text-muted small fw-semibold">ChamaHub Portal</span>
                </div>

                <!-- Right-hand controls -->
                <div class="d-flex align-items-center gap-3">

                    <!-- Dark Mode Toggle -->
                    <button id="darkModeToggle" class="navbar-icon-btn d-flex align-items-center gap-2" title="Toggle dark mode"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; border-radius: 50px; padding: 8px 16px; font-size: 0.82rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(102,126,234,0.4); transition: all 0.3s ease; letter-spacing: 0.3px;">
                        <i class="bi bi-moon-stars-fill" id="darkModeIcon" style="font-size: 1rem;"></i>
                        <span id="darkModeLabel">Dark Mode</span>
                    </button>

                    <!-- Notifications Bell -->
                    @php $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                    <a href="{{ route('notifications.index') }}" class="navbar-icon-btn position-relative d-flex align-items-center justify-content-center" title="Notifications"
                        style="width: 40px; height: 40px; border-radius: 50%; background: {{ $unreadCount > 0 ? 'linear-gradient(135deg, #ef4444, #dc2626)' : 'linear-gradient(135deg, #f1f5f9, #e2e8f0)' }}; color: {{ $unreadCount > 0 ? 'white' : '#64748b' }}; text-decoration: none; box-shadow: {{ $unreadCount > 0 ? '0 4px 12px rgba(239,68,68,0.45)' : '0 2px 8px rgba(0,0,0,0.08)' }}; transition: all 0.3s ease; border: none; font-size: 1rem;">
                        <i class="bi bi-bell-fill"></i>
                        @if($unreadCount > 0)
                            <span style="position: absolute; top: -3px; right: -3px; background: #fbbf24; color: #1e293b; border-radius: 50%; width: 18px; height: 18px; font-size: 0.65rem; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </a>

                    <!-- User dropdown -->
                    <div class="dropdown">
                        <button class="d-flex align-items-center gap-2 border-0 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                            style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border-radius: 50px; padding: 6px 14px 6px 6px; cursor: pointer; box-shadow: 0 4px 14px rgba(79,70,229,0.45); transition: all 0.3s ease;">
                            @if(auth()->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="avatar"
                                    style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.5);">
                            @else
                                <div style="width: 30px; height: 30px; border-radius: 50%; background: rgba(255,255,255,0.25); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 700; border: 2px solid rgba(255,255,255,0.4);">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="text-start d-none d-xl-block" style="line-height: 1.2;">
                                <div style="font-size: 0.82rem; font-weight: 700;">{{ auth()->user()->name }}</div>
                                <div style="font-size: 0.7rem; opacity: 0.75; text-transform: capitalize;">{{ auth()->user()->role }}</div>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 rounded-4 mt-2 p-2 shadow-lg" style="min-width: 220px; background: var(--bg-card);">
                            <li class="px-3 py-2 mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    @if(auth()->user()->profile_picture)
                                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="avatar" style="width:38px; height:38px; border-radius:50%; object-fit:cover;">
                                    @else
                                        <div style="width:38px; height:38px; border-radius:50%; background: var(--primary-gradient); color: white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1rem;">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="mb-0 fw-bold small" style="color: var(--text-heading);">{{ auth()->user()->name }}</p>
                                        <p class="mb-0 text-capitalize" style="font-size: 0.72rem; color: var(--text-muted);">{{ auth()->user()->role }}</p>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider my-1" style="border-color: var(--border-color);"></li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="{{ route('profile.edit') }}" style="color: var(--text-primary);">
                                    <i class="bi bi-person-circle text-primary"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" id="header-logout-form" class="d-none">
                                    @csrf
                                </form>
                                <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2 text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> Sign Out
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="p-4 flex-grow-1 fade-in-up">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0"
                     style="border-left: 4px solid #10b981 !important;" role="alert">
                    <i class="bi bi-check-circle-fill text-success me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0"
                     style="border-left: 4px solid #ef4444 !important;" role="alert">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
            </div><!-- /inner content -->
        </div><!-- /main-content -->
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════
         MOBILE BOTTOM NAVIGATION BAR (hidden on desktop via CSS)
    ═══════════════════════════════════════════════════════════════════ -->
    <nav class="bottom-nav" id="bottomNav" aria-label="Mobile navigation">
        <div class="bottom-nav-inner">

            <!-- Dashboard -->
            <a href="/dashboard" id="bnav-home"
               class="bottom-nav-item {{ request()->is('dashboard*') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Home</span>
            </a>

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'treasurer')
                <!-- Members (admin only) -->
                <a href="{{ route('admin.members.index') }}" id="bnav-members"
                   class="bottom-nav-item {{ request()->is('admin/members*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Members</span>
                </a>

                <!-- Contributions -->
                <a href="{{ route('admin.contributions.index') }}" id="bnav-savings"
                   class="bottom-nav-item {{ request()->is('admin/contributions*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i>
                    <span>Savings</span>
                </a>

                <!-- Loans -->
                <a href="{{ route('admin.loans.index') }}" id="bnav-loans"
                   class="bottom-nav-item {{ request()->is('admin/loans*') ? 'active' : '' }}">
                    <i class="bi bi-bank2"></i>
                    <span>Loans</span>
                </a>

                <!-- Reports -->
                <a href="{{ route('admin.reports.index') }}" id="bnav-reports"
                   class="bottom-nav-item {{ request()->is('admin/reports*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i>
                    <span>Reports</span>
                </a>

            @else
                <!-- Member: Savings -->
                <a href="{{ route('admin.contributions.index') }}" id="bnav-savings"
                   class="bottom-nav-item {{ request()->is('admin/contributions*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i>
                    <span>Savings</span>
                </a>

                <!-- Member: Loans -->
                <a href="{{ route('admin.loans.index') }}" id="bnav-loans"
                   class="bottom-nav-item {{ request()->is('admin/loans*') ? 'active' : '' }}">
                    <i class="bi bi-bank2"></i>
                    <span>Loans</span>
                </a>

                <!-- Member: Meetings -->
                <a href="{{ route('admin.meetings.index') }}" id="bnav-meetings"
                   class="bottom-nav-item {{ request()->is('admin/meetings*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event-fill"></i>
                    <span>Meetings</span>
                </a>

            @endif

            <!-- Notifications (all roles) -->
            @php $unreadBadge = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
            <a href="{{ route('notifications.index') }}" id="bnav-notif"
               class="bottom-nav-item {{ request()->is('notifications*') ? 'active' : '' }}">
                <i class="bi bi-bell-fill"></i>
                @if($unreadBadge > 0)
                    <span class="bottom-nav-badge">{{ $unreadBadge > 9 ? '9+' : $unreadBadge }}</span>
                @endif
                <span>Alerts</span>
            </a>

        </div>
    </nav>

    @stack('modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ═══════════════════════════════════════════════════════════════
            //  DARK MODE
            // ═══════════════════════════════════════════════════════════════
            const root       = document.getElementById('htmlRoot');
            const toggle     = document.getElementById('darkModeToggle');
            const icon       = document.getElementById('darkModeIcon');
            const label      = document.getElementById('darkModeLabel');
            const track      = document.getElementById('toggleTrack');
            const mobileBtn  = document.getElementById('mobileDarkToggle');
            const mobileIcon = document.getElementById('mobileDarkIcon');

            function applyDarkUI(isDark) {
                if (icon)       icon.className      = isDark ? 'bi bi-sun-fill'        : 'bi bi-moon-stars-fill';
                if (label)      label.textContent    = isDark ? 'Light'                 : 'Dark';
                if (track)      track.classList.toggle('on', isDark);
                if (mobileIcon) mobileIcon.className = isDark ? 'bi bi-sun-fill'        : 'bi bi-moon-stars-fill';
            }

            function toggleDark() {
                const isDark = root.classList.toggle('dark-mode');
                localStorage.setItem('chamahub_dark_mode', isDark);
                applyDarkUI(isDark);
            }

            applyDarkUI(root.classList.contains('dark-mode'));
            if (toggle)    toggle.addEventListener('click', toggleDark);
            if (mobileBtn) mobileBtn.addEventListener('click', toggleDark);

            // ═══════════════════════════════════════════════════════════════
            //  MOBILE SIDEBAR (open/close + swipe gesture)
            // ═══════════════════════════════════════════════════════════════
            const sidebar         = document.getElementById('sidebar');
            const backdrop        = document.getElementById('sidebarBackdrop');
            const mobileMenuBtn   = document.getElementById('mobileMenuBtn');
            const closeSidebarBtn = document.getElementById('closeSidebarBtn');

            function openSidebar()  { sidebar?.classList.add('show');    backdrop?.classList.add('show'); }
            function closeSidebar() { sidebar?.classList.remove('show'); backdrop?.classList.remove('show'); }
            function toggleSidebar() { sidebar?.classList.contains('show') ? closeSidebar() : openSidebar(); }

            mobileMenuBtn?.addEventListener('click', toggleSidebar);
            closeSidebarBtn?.addEventListener('click', closeSidebar);
            backdrop?.addEventListener('click', closeSidebar);

            // Swipe-right to open, swipe-left to close sidebar
            let touchStartX = null;
            document.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
            document.addEventListener('touchend', e => {
                if (touchStartX === null) return;
                const dx = e.changedTouches[0].clientX - touchStartX;
                touchStartX = null;
                if (dx > 60  && !sidebar?.classList.contains('show')) openSidebar();
                if (dx < -60 &&  sidebar?.classList.contains('show')) closeSidebar();
            }, { passive: true });

            // ═══════════════════════════════════════════════════════════════
            //  PULL-TO-REFRESH
            // ═══════════════════════════════════════════════════════════════
            const ptrEl = document.getElementById('ptr-indicator');
            let ptrStart = null, ptrActive = false;

            window.addEventListener('touchstart', e => {
                if (window.scrollY === 0) ptrStart = e.touches[0].clientY;
            }, { passive: true });

            window.addEventListener('touchmove', e => {
                if (ptrStart === null) return;
                const dy = e.touches[0].clientY - ptrStart;
                if (dy > 50 && window.scrollY === 0) {
                    ptrEl?.classList.add('visible');
                    ptrActive = true;
                }
            }, { passive: true });

            window.addEventListener('touchend', () => {
                if (ptrActive) {
                    ptrEl?.classList.add('refreshing');
                    setTimeout(() => window.location.reload(), 600);
                }
                ptrStart = null; ptrActive = false;
            }, { passive: true });

            // ═══════════════════════════════════════════════════════════════
            //  PWA INSTALL BANNER
            // ═══════════════════════════════════════════════════════════════
            let deferredPrompt = null;
            const installBanner  = document.getElementById('pwa-install-banner');
            const installBtn     = document.getElementById('pwa-install-btn');
            const dismissBtn     = document.getElementById('pwa-dismiss-btn');
            const DISMISS_KEY    = 'chamahub_pwa_dismissed';
            const DISMISS_DAYS   = 7;

            function isDismissed() {
                const ts = localStorage.getItem(DISMISS_KEY);
                if (!ts) return false;
                return (Date.now() - parseInt(ts)) < DISMISS_DAYS * 86400000;
            }

            window.addEventListener('beforeinstallprompt', e => {
                e.preventDefault();
                deferredPrompt = e;
                if (!isDismissed() && installBanner) {
                    installBanner.style.display = 'block';
                }
            });

            installBtn?.addEventListener('click', async () => {
                if (!deferredPrompt) return;
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                deferredPrompt = null;
                if (installBanner) installBanner.style.display = 'none';
                console.log('[PWA] Install outcome:', outcome);
            });

            dismissBtn?.addEventListener('click', () => {
                localStorage.setItem(DISMISS_KEY, Date.now().toString());
                if (installBanner) installBanner.style.display = 'none';
            });

            // Hide banner once app is installed
            window.addEventListener('appinstalled', () => {
                if (installBanner) installBanner.style.display = 'none';
                deferredPrompt = null;
                console.log('[PWA] App installed successfully!');
            });

            // ═══════════════════════════════════════════════════════════════
            //  OFFLINE BACKGROUND SYNC LISTENER
            // ═══════════════════════════════════════════════════════════════
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.addEventListener('message', event => {
                    if (event.data?.type === 'OFFLINE_SYNC_COMPLETE') {
                        // Show a toast that offline queued action was replayed
                        showToast('✅ Your offline action was synced successfully!', 'success');
                    }
                });
            }

            // ═══════════════════════════════════════════════════════════════
            //  SIMPLE TOAST HELPER
            // ═══════════════════════════════════════════════════════════════
            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                const colors = { success: '#10b981', error: '#ef4444', info: '#4f46e5' };
                toast.style.cssText = `
                    position: fixed; bottom: calc(80px + env(safe-area-inset-bottom));
                    left: 50%; transform: translateX(-50%) translateY(20px);
                    background: ${colors[type] || colors.info}; color: white;
                    padding: 0.75rem 1.5rem; border-radius: 50px;
                    font-family: 'Outfit', sans-serif; font-weight: 600; font-size: 0.88rem;
                    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
                    z-index: 9999; opacity: 0; transition: all 0.3s ease;
                    white-space: nowrap; max-width: 90vw;
                `;
                toast.textContent = message;
                document.body.appendChild(toast);
                requestAnimationFrame(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateX(-50%) translateY(0)';
                });
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 3500);
            }

            // Expose toast globally so other scripts can use it
            window.chamahubToast = showToast;
        });

        // ═══════════════════════════════════════════════════════════════════
        //  SERVICE WORKER + PUSH NOTIFICATIONS REGISTRATION
        // ═══════════════════════════════════════════════════════════════════
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', async () => {
                try {
                    const registration = await navigator.serviceWorker.register('/sw.js');
                    console.log('[SW] Registered. Scope:', registration.scope);

                    // ── Request push notification permission on demand ──
                    // We expose a global function so the UI can call it
                    window.chamahubRequestPush = async function () {
                        if (!('Notification' in window) || !('PushManager' in window)) {
                            console.warn('[Push] Not supported.');
                            return;
                        }
                        const permission = await Notification.requestPermission();
                        if (permission !== 'granted') return;

                        try {
                            // Note: Replace VAPID_PUBLIC_KEY below with your actual VAPID public key
                            // Generate at: https://vapidkeys.com/ or use php artisan vapid:generate
                            const VAPID_PUBLIC_KEY = '{{ env("VAPID_PUBLIC_KEY", "") }}';
                            if (!VAPID_PUBLIC_KEY) return; // VAPID not configured yet

                            const sub = await registration.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
                            });

                            const subJson = sub.toJSON();
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                            await fetch('/api/push-subscriptions', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-XSRF-TOKEN': decodeURIComponent(
                                        (document.cookie.match(/XSRF-TOKEN=([^;]+)/) || [])[1] || ''
                                    ),
                                    'Accept': 'application/json',
                                },
                                credentials: 'include',
                                body: JSON.stringify({
                                    endpoint: subJson.endpoint,
                                    keys: subJson.keys,
                                }),
                            });
                            console.log('[Push] Subscription saved.');
                        } catch (err) {
                            console.error('[Push] Subscribe failed:', err);
                        }
                    };

                    // ── Helper: queue a request for background sync ──
                    window.chamahubQueueOffline = function (url, method, headers, body) {
                        navigator.serviceWorker.ready.then(reg => {
                            reg.active?.postMessage({
                                type: 'QUEUE_OFFLINE_REQUEST',
                                request: { url, method, headers, body },
                            });
                            reg.sync?.register('chamahub-bg-sync').catch(() => {});
                        });
                    };

                } catch (err) {
                    console.error('[SW] Registration failed:', err);
                }
            });
        }

        // ── VAPID key decoder helper ──
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64  = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const raw = atob(base64);
            return new Uint8Array([...raw].map(c => c.charCodeAt(0)));
        }
    </script>
    @stack('scripts')
</body>
</html>
