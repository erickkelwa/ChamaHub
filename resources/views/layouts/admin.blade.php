<!DOCTYPE html>
<html lang="en" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChamaHub - Admin & Member Portal</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

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

        body {
            background-color: var(--bg-body);
            font-family: 'Outfit', sans-serif;
            color: var(--text-primary);
            overflow-x: hidden;
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
        ============================================================ */
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

        /* Mobile: hide sidebar off-screen */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0 !important; padding: 1.25rem 1rem !important; }
        }

        /* Desktop: fixed sidebar, push content */
        @media (min-width: 992px) {
            .main-content { margin-left: var(--sidebar-width); padding: 2rem 2.5rem; min-height: 100vh; }
            .mobile-header { display: none !important; }
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
            padding: 0.9rem 1.5rem;
            display: flex; align-items: center;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
        }
        .nav-link-custom i { font-size: 1.2rem; margin-right: 12px; }
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
            <h5 class="mb-0 fw-bold d-flex align-items-center">
                <i class="bi bi-hexagon-fill me-2 fs-4 opacity-75"></i> ChamaHub
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

    <!-- ── Backdrop ── -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="d-flex">

        <!-- ── Sidebar ── -->
        <div class="sidebar" id="sidebar">

            <div class="sidebar-header d-flex justify-content-between align-items-center">
                <h4 class="d-flex align-items-center mb-0">
                    <i class="bi bi-hexagon-fill me-2 fs-3 text-white-50"></i> ChamaHub
                </h4>
                <button class="btn text-white d-lg-none p-0 fs-4 border-0 bg-transparent" id="closeSidebarBtn">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="mt-3 flex-grow-1" style="overflow-y:auto;">
                <a href="/dashboard" class="nav-link-custom {{ request()->is('dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
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
                    <a href="{{ route('admin.meetings.index') }}" class="nav-link-custom {{ request()->is('admin/meetings*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event-fill"></i> Meetings
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="nav-link-custom {{ request()->is('admin/reports*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Reports
                    </a>
                @endif

                <a href="{{ route('profile.edit') }}" class="nav-link-custom {{ request()->is('profile*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i> My Profile
                </a>
            </div>

            <!-- Bottom section -->
            <div class="p-3 mt-auto border-top border-white border-opacity-10">

                <!-- Dark Mode Toggle -->
                <button class="dark-mode-toggle mb-2" id="darkModeToggle">
                    <i class="bi bi-moon-stars-fill" id="darkModeIcon"></i>
                    <span id="darkModeLabel">Dark Mode</span>
                    <div class="toggle-track" id="toggleTrack">
                        <div class="toggle-thumb"></div>
                    </div>
                </button>

                <!-- User info pill -->
                <div class="d-flex align-items-center px-3 py-2 mb-2 rounded-3" style="background: var(--sidebar-user-bg);">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="{{ auth()->user()->name }}" class="me-2" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                    @else
                        <i class="bi bi-person-circle fs-5 me-2 text-white"></i>
                    @endif
                    <div class="text-truncate" style="min-width:0;">
                        <strong class="d-block text-white small">{{ auth()->user()->name }}</strong>
                        <span class="text-white-50 small text-capitalize">{{ auth()->user()->role }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn w-100 text-start nav-link-custom border-0 bg-transparent py-2">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- ── Main Content ── -->
        <div class="main-content flex-grow-1 fade-in-up">

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
        </div>
    </div>

    @stack('modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── Dark Mode ──────────────────────────────────
            const root       = document.getElementById('htmlRoot');
            const toggle     = document.getElementById('darkModeToggle');
            const icon       = document.getElementById('darkModeIcon');
            const label      = document.getElementById('darkModeLabel');
            const track      = document.getElementById('toggleTrack');
            const mobileBtn  = document.getElementById('mobileDarkToggle');
            const mobileIcon = document.getElementById('mobileDarkIcon');

            function applyDarkUI(isDark) {
                if (icon)       icon.className      = isDark ? 'bi bi-sun-fill'        : 'bi bi-moon-stars-fill';
                if (label)      label.textContent    = isDark ? 'Light Mode'            : 'Dark Mode';
                if (track)      track.classList.toggle('on', isDark);
                if (mobileIcon) mobileIcon.className = isDark ? 'bi bi-sun-fill'        : 'bi bi-moon-stars-fill';
            }

            function toggleDark() {
                const isDark = root.classList.toggle('dark-mode');
                localStorage.setItem('chamahub_dark_mode', isDark);
                applyDarkUI(isDark);
            }

            // Sync UI to current state (may already be dark from inline script)
            applyDarkUI(root.classList.contains('dark-mode'));

            if (toggle)    toggle.addEventListener('click', toggleDark);
            if (mobileBtn) mobileBtn.addEventListener('click', toggleDark);

            // ── Mobile Sidebar ─────────────────────────────
            const sidebar         = document.getElementById('sidebar');
            const backdrop        = document.getElementById('sidebarBackdrop');
            const mobileMenuBtn   = document.getElementById('mobileMenuBtn');
            const closeSidebarBtn = document.getElementById('closeSidebarBtn');

            function toggleSidebar() {
                sidebar?.classList.toggle('show');
                backdrop?.classList.toggle('show');
            }

            mobileMenuBtn?.addEventListener('click', toggleSidebar);
            closeSidebarBtn?.addEventListener('click', toggleSidebar);
            backdrop?.addEventListener('click', toggleSidebar);
        });
    </script>
    @stack('scripts')
</body>
</html>
