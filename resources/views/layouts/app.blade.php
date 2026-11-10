<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - SubTrack | Pelacak Langganan</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --pri: #4f46e5;
            --pri-hover: #4338ca;
            --sec: #eef2ff;
            --bg: #f8fafc;
            --card: #ffffff;
            --tx: #0f172a;
            --mu: #64748b;
            --bd: #e2e8f0;
            --ok: #10b981;
            --okb: #ecfdf5;
            --wa: #f59e0b;
            --wab: #fffbeb;
            --da: #ef4444;
            --dab: #fef2f2;
            --sh: 0 4px 24px rgba(15, 23, 42, 0.06);
            --sidebar-w: 260px;
        }

        [data-bs-theme="dark"] {
            --pri: #6366f1;
            --pri-hover: #818cf8;
            --sec: #1e1b4b;
            --bg: #0b0f19;
            --card: #131b2e;
            --tx: #f8fafc;
            --mu: #94a3b8;
            --bd: #1e293b;
            --ok: #34d399;
            --okb: rgba(16, 185, 129, 0.15);
            --wa: #fbbf24;
            --wab: rgba(245, 158, 11, 0.15);
            --da: #f87171;
            --dab: rgba(239, 68, 68, 0.15);
            --sh: 0 4px 24px rgba(0, 0, 0, 0.35);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background-color: var(--bg);
            color: var(--tx);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            font-weight: 500;
            line-height: 1.5;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* Subtle background glow */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            opacity: 0.6;
            background: radial-gradient(circle at 92% 4%, rgba(79, 70, 229, 0.08), transparent 32%),
                        radial-gradient(circle at 4% 88%, rgba(14, 165, 233, 0.08), transparent 35%);
        }

        /* Layout structure */
        #app-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        aside.sidebar {
            width: var(--sidebar-w);
            background: var(--card);
            border-right: 1px solid var(--bd);
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 24px 16px;
            flex-shrink: 0;
            z-index: 100;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 10px 24px;
            text-decoration: none;
            color: var(--tx);
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--pri), #06b6d4);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 14px;
            color: var(--mu);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }

        .nav-link-custom:hover {
            background-color: var(--sec);
            color: var(--pri);
            transform: translateX(3px);
        }

        .nav-link-custom.active {
            background-color: var(--pri);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        .nav-link-custom.active .badge-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .nav-icon {
            font-size: 1.15rem;
            margin-right: 12px;
            width: 20px;
            display: inline-flex;
            justify-content: center;
        }

        .badge-count {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 999px;
            background: var(--sec);
            color: var(--pri);
        }

        .badge-count.danger {
            background: var(--dab);
            color: var(--da);
        }

        .sidebar-footer {
            padding-top: 16px;
            border-top: 1px solid var(--bd);
        }

        .user-preview-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 10px;
            border-radius: 14px;
            background: var(--sec);
            margin-bottom: 12px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--pri);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
        }

        /* Main Content Container */
        main.main-content {
            flex: 1;
            min-width: 0;
            padding: 24px 36px 60px;
            max-width: 1300px;
            margin: 0 auto;
        }

        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--bd);
        }

        .page-header h1 {
            font-size: 1.55rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .page-header p {
            color: var(--mu);
            font-size: 0.88rem;
            margin: 2px 0 0;
        }

        /* Cards */
        .card-custom {
            background: var(--card);
            border: 1px solid var(--bd);
            border-radius: 20px;
            padding: 22px;
            box-shadow: var(--sh);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .card-custom:hover {
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
        }

        /* Metric Card */
        .metric-card {
            background: var(--card);
            border: 1px solid var(--bd);
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--sh);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .metric-card:hover {
            transform: translateY(-2px);
        }

        .metric-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .metric-value {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 8px 0 2px;
        }

        .metric-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--mu);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        /* Service Squircle Icon */
        .service-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--sec);
            display: grid;
            place-items: center;
            font-size: 1.4rem;
            flex-shrink: 0;
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.04);
        }

        /* Custom Badges */
        .badge-pill-custom {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .badge-active {
            background: var(--okb);
            color: var(--ok);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .badge-trial {
            background: var(--wab);
            color: var(--wa);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .badge-cancelled {
            background: var(--sec);
            color: var(--mu);
            border: 1px solid var(--bd);
        }

        /* Urgency levels */
        .badge-urgent-critical {
            background: var(--dab);
            color: var(--da);
            border: 1px solid rgba(239, 68, 68, 0.3);
            animation: pulseWarning 2s infinite;
        }

        .badge-urgent-warning {
            background: var(--wab);
            color: var(--wa);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .badge-urgent-normal {
            background: var(--sec);
            color: var(--mu);
        }

        @keyframes pulseWarning {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        /* Critical Due Border Alert */
        .card-critical {
            border: 2px solid var(--da) !important;
            box-shadow: 0 4px 20px rgba(239, 68, 68, 0.15) !important;
        }

        .card-warning {
            border: 1.5px solid var(--wa) !important;
        }

        /* Buttons */
        .btn-theme {
            background: var(--pri);
            color: #ffffff;
            border: 0;
            border-radius: 999px;
            padding: 9px 20px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            text-decoration: none;
            cursor: pointer;
        }

        .btn-theme:hover {
            background: var(--pri-hover);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        }

        .btn-theme-secondary {
            background: var(--sec);
            color: var(--tx);
            border: 1px solid var(--bd);
            border-radius: 999px;
            padding: 8px 18px;
            font-weight: 600;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-theme-secondary:hover {
            background: var(--bd);
            color: var(--tx);
            transform: translateY(-1px);
        }

        .btn-theme-success {
            background: var(--okb);
            color: var(--ok);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 999px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-theme-success:hover {
            background: var(--ok);
            color: #ffffff;
        }

        .btn-theme-danger {
            background: var(--dab);
            color: var(--da);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 999px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-theme-danger:hover {
            background: var(--da);
            color: #ffffff;
        }

        .btn-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid var(--bd);
            background: var(--card);
            color: var(--tx);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-circle:hover {
            background: var(--sec);
            color: var(--pri);
            transform: translateY(-1px);
        }

        /* Form Controls */
        .form-control-custom, .form-select-custom {
            background: var(--card);
            border: 1.5px solid var(--bd);
            border-radius: 14px;
            padding: 10px 16px;
            font-size: 0.92rem;
            color: var(--tx);
            transition: all 0.2s ease;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            background: var(--card);
            border-color: var(--pri);
            color: var(--tx);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
            outline: none;
        }

        /* Chips / Filter Pills */
        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid var(--bd);
            background: var(--card);
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--mu);
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .filter-chip:hover {
            background: var(--sec);
            color: var(--tx);
            border-color: var(--pri);
        }

        .filter-chip.active {
            background: var(--pri);
            border-color: var(--pri);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
        }

        /* Progress Bar for trials */
        .trial-bar {
            height: 8px;
            border-radius: 99px;
            background: var(--sec);
            overflow: hidden;
        }

        .trial-bar-fill {
            height: 100%;
            border-radius: 99px;
            background: var(--pri);
            transition: width 0.6s ease;
        }

        .trial-bar-fill.warning {
            background: var(--wa);
        }

        .trial-bar-fill.danger {
            background: var(--da);
        }

        /* Table custom styling */
        .table-custom {
            --bs-table-bg: transparent;
            color: var(--tx);
            vertical-align: middle;
            margin-bottom: 0;
        }

        .table-custom th {
            color: var(--mu);
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 14px 16px;
            border-bottom: 1px solid var(--bd);
        }

        .table-custom td {
            padding: 16px;
            border-bottom: 1px solid var(--bd);
        }

        .table-custom tr:last-child td {
            border-bottom: none;
        }

        /* Modern Toast Styling */
        .custom-toast {
            background: var(--card);
            border: 1px solid var(--bd);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            color: var(--tx);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 320px;
            animation: slideInUp 0.3s ease forwards;
        }

        .custom-toast.success {
            border-left: 5px solid var(--ok);
        }

        .custom-toast.danger {
            border-left: 5px solid var(--da);
        }

        .custom-toast.info {
            border-left: 5px solid var(--pri);
        }

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mobile Bottom Nav */
        .mobile-bottom-nav {
            display: none;
        }

        @media (max-width: 991.98px) {
            aside.sidebar {
                display: none;
            }

            main.main-content {
                padding: 16px 16px 90px;
            }

            .mobile-bottom-nav {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: var(--card);
                border-top: 1px solid var(--bd);
                padding: 8px 4px calc(8px + env(safe-area-inset-bottom, 0px));
                justify-content: space-around;
                z-index: 1000;
                box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.05);
            }

            .bnav-link {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 2px;
                font-size: 0.72rem;
                font-weight: 600;
                color: var(--mu);
                text-decoration: none;
                padding: 4px 8px;
                border-radius: 12px;
                transition: all 0.2s ease;
            }

            .bnav-link i {
                font-size: 1.25rem;
            }

            .bnav-link.active {
                color: var(--pri);
                background: var(--sec);
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div id="app-layout">
        <!-- Sidebar for Desktop -->
        <aside class="sidebar">
            <div>
                <!-- Brand Logo -->
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <div class="brand-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <span>SubTrack</span>
                </a>

                <!-- Navigation Links -->
                <nav class="sidebar-nav">
                    <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <div>
                            <span class="nav-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                            <span>Dashboard</span>
                        </div>
                    </a>

                    <a href="{{ route('subscriptions.index') }}" class="nav-link-custom {{ request()->routeIs('subscriptions.index') || request()->routeIs('subscriptions.show') || request()->routeIs('subscriptions.edit') ? 'active' : '' }}">
                        <div>
                            <span class="nav-icon"><i class="bi bi-arrow-repeat"></i></span>
                            <span>Subscriptions</span>
                        </div>
                        @php
                            $userActiveCount = Auth::user()->subscriptions()->where('status', 'Active')->count();
                        @endphp
                        <span class="badge-count">{{ $userActiveCount }}</span>
                    </a>

                    <a href="{{ route('trials.index') }}" class="nav-link-custom {{ request()->routeIs('trials.index') ? 'active' : '' }}">
                        <div>
                            <span class="nav-icon"><i class="bi bi-hourglass-split"></i></span>
                            <span>Free Trials</span>
                        </div>
                        @php
                            $userTrialCount = Auth::user()->subscriptions()->where('status', 'Free Trial')->count();
                        @endphp
                        @if($userTrialCount > 0)
                            <span class="badge-count danger">{{ $userTrialCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('analytics.index') }}" class="nav-link-custom {{ request()->routeIs('analytics.index') ? 'active' : '' }}">
                        <div>
                            <span class="nav-icon"><i class="bi bi-pie-chart-fill"></i></span>
                            <span>Analytics</span>
                        </div>
                    </a>

                    <a href="{{ route('subscriptions.trash') }}" class="nav-link-custom {{ request()->routeIs('subscriptions.trash') ? 'active' : '' }}">
                        <div>
                            <span class="nav-icon"><i class="bi bi-trash3"></i></span>
                            <span>Trash</span>
                        </div>
                        @php
                            $trashedCount = Auth::user()->subscriptions()->onlyTrashed()->count();
                        @endphp
                        @if($trashedCount > 0)
                            <span class="badge-count">{{ $trashedCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('profile.edit') }}" class="nav-link-custom {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <div>
                            <span class="nav-icon"><i class="bi bi-gear-fill"></i></span>
                            <span>Settings</span>
                        </div>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Bottom: User & Logout -->
            <div class="sidebar-footer">
                <a href="{{ route('profile.edit') }}" class="user-preview-card text-decoration-none">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-bold text-truncate" style="font-size: 0.88rem; color: var(--tx);">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="text-truncate" style="font-size: 0.75rem; color: var(--mu);">
                            {{ Auth::user()->currency }} &bull; Budget: {{ Auth::user()->formatCurrency(Auth::user()->monthly_budget ?? 200) }}
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                </a>

                <div class="d-flex gap-2">
                    <button class="btn btn-theme-secondary w-100 justify-content-center" id="theme-toggle-btn" title="Toggle theme (Press D)" onclick="toggleTheme()">
                        <i class="bi bi-moon-stars" id="theme-icon"></i>
                        <span id="theme-text" style="font-size: 0.82rem;">Dark Mode</span>
                    </button>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn-circle" title="Log Out" style="width: 38px; height: 38px;">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="page-header">
                    <h1>@yield('page_title', 'Dashboard')</h1>
                    <p>@yield('page_subtitle', 'Manage and optimize your recurring subscriptions.')</p>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <!-- Quick Global Search trigger -->
                    @if(request()->routeIs('subscriptions.index'))
                        <div class="d-none d-md-flex align-items-center text-muted small px-3 py-1.5 rounded-pill border" style="background: var(--card); border-color: var(--bd) !important;">
                            <i class="bi bi-search me-2"></i>
                            <span>Type <kbd class="px-1.5 py-0.5 rounded border small bg-body" style="font-size: 0.7rem;">/</kbd> to search</span>
                        </div>
                    @endif

                    <!-- Quick Add Modal Trigger Button -->
                    <button type="button" class="btn-theme" data-bs-toggle="modal" data-bs-target="#quickAddModal" title="Add Subscription (Press N)">
                        <i class="bi bi-plus-lg"></i>
                        <span>Add Subscription</span>
                        <kbd class="d-none d-lg-inline-block text-white-50 px-1.5 py-0.2 rounded border border-white-50 ms-1" style="font-size: 0.65rem; background: rgba(255,255,255,0.15);">N</kbd>
                    </button>

                    <!-- Mobile Theme Toggle -->
                    <button class="btn-circle d-lg-none" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                        <i class="bi bi-moon-stars" id="theme-icon-mobile"></i>
                    </button>

                    <!-- Mobile Logout -->
                    <form action="{{ route('logout') }}" method="POST" class="d-lg-none">
                        @csrf
                        <button type="submit" class="btn-circle text-danger" title="Log Out">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Page Content Section -->
            @yield('content')
        </main>
    </div>

    <!-- Mobile Bottom Navigation Bar (like KeikiSpace) -->
    <nav class="mobile-bottom-nav">
        <a href="{{ route('dashboard') }}" class="bnav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('subscriptions.index') }}" class="bnav-link {{ request()->routeIs('subscriptions.*') ? 'active' : '' }}">
            <i class="bi bi-arrow-repeat"></i>
            <span>Subs</span>
        </a>
        <button type="button" class="bnav-link border-0 bg-transparent text-primary" data-bs-toggle="modal" data-bs-target="#quickAddModal" style="color: var(--pri) !important;">
            <i class="bi bi-plus-circle-fill fs-3"></i>
            <span>Add</span>
        </button>
        <a href="{{ route('trials.index') }}" class="bnav-link {{ request()->routeIs('trials.*') ? 'active' : '' }}">
            <i class="bi bi-hourglass-split"></i>
            <span>Trials</span>
        </a>
        <a href="{{ route('analytics.index') }}" class="bnav-link {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
            <i class="bi bi-pie-chart"></i>
            <span>Charts</span>
        </a>
    </nav>

    <!-- Global Floating Toast Container -->
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1090;"></div>

    <!-- Quick Add Subscription Modal -->
    <div class="modal fade" id="quickAddModal" tabindex="-1" aria-labelledby="quickAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg" style="background: var(--card); color: var(--tx);">
                <div class="modal-header border-bottom px-4 pt-4 pb-3" style="border-color: var(--bd) !important;">
                    <div>
                        <h5 class="modal-title fw-bold" id="quickAddModalLabel">
                            <i class="bi bi-plus-circle-fill me-2 text-primary" style="color: var(--pri) !important;"></i>
                            Quick Add Subscription
                        </h5>
                        <p class="text-muted small mb-0">Record a recurring service or free trial without navigating away.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    <!-- Quick Autofill Presets -->
                    <div class="p-2.5 rounded-3 mb-3" style="background: var(--sec); border: 1px solid var(--bd);">
                        <div class="small text-muted fw-semibold mb-2">⚡ 1-Click Popular Presets:</div>
                        <div class="d-flex flex-wrap gap-1.5">
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-1 px-2.5 small" onclick="quickFillModal('Spotify Premium', 10.99, 'Monthly', 'Entertainment', 'Active', '🎵')">🎵 Spotify</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-1 px-2.5 small" onclick="quickFillModal('Netflix Standard', 15.49, 'Monthly', 'Entertainment', 'Active', '🎬')">🎬 Netflix</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-1 px-2.5 small" onclick="quickFillModal('ChatGPT Plus', 20.00, 'Monthly', 'Work', 'Active', '🤖')">🤖 ChatGPT</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-1 px-2.5 small" onclick="quickFillModal('YouTube Premium', 13.99, 'Monthly', 'Entertainment', 'Active', '▶️')">▶️ YouTube</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-1 px-2.5 small" onclick="quickFillModal('GitHub Copilot', 10.00, 'Monthly', 'Work', 'Active', '💻')">💻 Copilot</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-1 px-2.5 small" onclick="quickFillModal('Gym Membership', 45.00, 'Monthly', 'Health', 'Active', '🏋️')">🏋️ Gym</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-1 px-2.5 small" onclick="quickFillModal('iCloud+ 2TB', 9.99, 'Monthly', 'Utilities', 'Active', '☁️')">☁️ iCloud</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill py-1 px-2.5 small" onclick="quickFillModal('Figma Pro Trial', 15.00, 'Monthly', 'Work', 'Free Trial', '📐')">⏳ Figma Trial</button>
                        </div>
                    </div>

                    <form action="{{ route('subscriptions.store') }}" method="POST" id="quickAddForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-8">
                                <label for="m_name" class="form-label small fw-semibold">Service Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-custom" id="m_name" name="name" required placeholder="e.g. Spotify, Gym, AWS">
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="m_icon" class="form-label small fw-semibold">Icon / Emoji</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" id="m_icon_preview" style="border-color: var(--bd); font-size: 1.2rem;">
                                        🎬
                                    </span>
                                    <input type="text" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2" id="m_icon" name="icon" value="🎬" maxlength="10">
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="m_cost" class="form-label small fw-semibold">Cost <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3 fw-bold" style="border-color: var(--bd); color: var(--pri);">
                                        {{ Auth::user()->currency }}
                                    </span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2" 
                                           id="m_cost" name="cost" value="9.99" required oninput="calcModalCost()">
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="m_cycle" class="form-label small fw-semibold">Billing Cycle <span class="text-danger">*</span></label>
                                <select class="form-select form-select-custom" id="m_cycle" name="billing_cycle" required onchange="calcModalCost()">
                                    <option value="Monthly" selected>Monthly</option>
                                    <option value="Yearly">Yearly</option>
                                    <option value="Weekly">Weekly</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="m_category" class="form-label small fw-semibold">Category <span class="text-danger">*</span></label>
                                <select class="form-select form-select-custom" id="m_category" name="category" required>
                                    <option value="Entertainment" selected>Entertainment</option>
                                    <option value="Utilities">Utilities</option>
                                    <option value="Health">Health</option>
                                    <option value="Work">Work</option>
                                    <option value="Education">Education</option>
                                    <option value="Food">Food</option>
                                    <option value="Shopping">Shopping</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="m_status" class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                                <select class="form-select form-select-custom" id="m_status" name="status" required>
                                    <option value="Active" selected>Active</option>
                                    <option value="Free Trial">Free Trial</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="m_date" class="form-label small fw-semibold">Next Billing / Renewal Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-custom" id="m_date" name="next_billing_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                            </div>

                            <div class="col-12">
                                <label for="m_notes" class="form-label small fw-semibold">Notes (Optional)</label>
                                <textarea class="form-control form-control-custom" id="m_notes" name="notes" rows="2" placeholder="Plan details or cancellation reminders..."></textarea>
                            </div>
                        </div>

                        <!-- Live Cost Preview -->
                        <div class="rounded-3 p-2.5 my-3 d-flex justify-content-between align-items-center" style="background: var(--sec); border: 1px solid var(--bd);">
                            <span class="small text-muted">Effective Cost: <strong id="m_monthly_preview" style="color: var(--pri);">{{ Auth::user()->currency }}9.99 / mo</strong></span>
                            <span class="small text-muted">Annual: <strong id="m_yearly_preview">{{ Auth::user()->currency }}119.88 / yr</strong></span>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-theme-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-theme">
                                <i class="bi bi-check-lg"></i> Add Subscription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reusable Confirmation Dialog Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow-lg text-center p-4" style="background: var(--card); color: var(--tx);">
                <div class="rounded-circle d-grid place-items-center mx-auto mb-3" id="confirmIconBox" style="width: 52px; height: 52px; background: var(--dab); color: var(--da); font-size: 1.5rem;">
                    <i class="bi bi-exclamation-triangle-fill" id="confirmIcon"></i>
                </div>
                <h5 class="fw-bold mb-1" id="confirmTitle">Confirm Action</h5>
                <p class="text-muted small mb-4" id="confirmMessage">Are you sure you want to perform this action?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-theme-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-theme-danger" id="confirmBtnAction">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme & Global App Scripts -->
    <script>
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('subtrack-theme', theme);

            const icon = document.getElementById('theme-icon');
            const iconMobile = document.getElementById('theme-icon-mobile');
            const text = document.getElementById('theme-text');

            if (theme === 'dark') {
                if (icon) icon.className = 'bi bi-sun';
                if (iconMobile) iconMobile.className = 'bi bi-sun';
                if (text) text.textContent = 'Light Mode';
            } else {
                if (icon) icon.className = 'bi bi-moon-stars';
                if (iconMobile) iconMobile.className = 'bi bi-moon-stars';
                if (text) text.textContent = 'Dark Mode';
            }
        }

        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next);
            showToast('Theme Updated', `Switched to ${next} mode.`, 'info');
        }

        // Initialize Theme
        const savedTheme = localStorage.getItem('subtrack-theme') || 'light';
        applyTheme(savedTheme);

        // Toast System
        function showToast(title, message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const icon = type === 'success' ? 'bi-check-circle-fill text-success' : (type === 'danger' ? 'bi-exclamation-circle-fill text-danger' : 'bi-info-circle-fill text-primary');
            const toastEl = document.createElement('div');
            toastEl.className = `custom-toast ${type} mb-2`;
            toastEl.innerHTML = `
                <i class="bi ${icon} fs-5"></i>
                <div class="flex-grow-1">
                    <strong class="d-block small" style="color: var(--tx);">${title}</strong>
                    <span class="small text-muted">${message}</span>
                </div>
                <button type="button" class="btn-close small ms-2" onclick="this.parentElement.remove()"></button>
            `;
            container.appendChild(toastEl);

            setTimeout(() => {
                toastEl.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                toastEl.style.opacity = '0';
                toastEl.style.transform = 'translateY(10px)';
                setTimeout(() => toastEl.remove(), 400);
            }, 3500);
        }

        // Show flash messages automatically
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', () => showToast('Success', "{{ session('success') }}", 'success'));
        @endif
        @if(session('info'))
            document.addEventListener('DOMContentLoaded', () => showToast('Notice', "{{ session('info') }}", 'info'));
        @endif
        @if(session('warning'))
            document.addEventListener('DOMContentLoaded', () => showToast('Warning', "{{ session('warning') }}", 'danger'));
        @endif

        // Modal Calculation and Autofill
        function quickFillModal(name, cost, cycle, cat, status, icon) {
            document.getElementById('m_name').value = name;
            document.getElementById('m_cost').value = cost;
            document.getElementById('m_cycle').value = cycle;
            document.getElementById('m_category').value = cat;
            document.getElementById('m_status').value = status;
            document.getElementById('m_icon').value = icon;
            document.getElementById('m_icon_preview').textContent = icon;
            calcModalCost();
        }

        document.getElementById('m_icon')?.addEventListener('input', function() {
            document.getElementById('m_icon_preview').textContent = this.value || '✨';
        });

        function calcModalCost() {
            const cost = parseFloat(document.getElementById('m_cost')?.value) || 0;
            const cycle = document.getElementById('m_cycle')?.value || 'Monthly';
            const curr = "{{ Auth::user()->currency }}";

            let m = cost;
            let y = cost * 12;

            if (cycle === 'Weekly') {
                m = (cost * 52) / 12;
                y = cost * 52;
            } else if (cycle === 'Yearly') {
                m = cost / 12;
                y = cost;
            }

            const mEl = document.getElementById('m_monthly_preview');
            const yEl = document.getElementById('m_yearly_preview');
            if (mEl) mEl.textContent = curr + m.toFixed(2) + ' / mo';
            if (yEl) yEl.textContent = curr + y.toFixed(2) + ' / yr';
        }

        // Custom Confirm Dialog UX
        let pendingFormSubmission = null;
        function requestConfirm(event, formElement, title, message, btnClass = 'btn-theme-danger', btnText = 'Confirm') {
            event.preventDefault();
            pendingFormSubmission = formElement;

            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmMessage').textContent = message;
            const btn = document.getElementById('confirmBtnAction');
            btn.className = `btn ${btnClass}`;
            btn.textContent = btnText;

            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        }

        document.getElementById('confirmBtnAction')?.addEventListener('click', function() {
            if (pendingFormSubmission) {
                pendingFormSubmission.submit();
            }
        });

        // Global Keyboard Shortcuts (N: New sub, /: Search, D: Dark mode)
        document.addEventListener('keydown', function(e) {
            if (['input', 'textarea', 'select'].includes(document.activeElement.tagName.toLowerCase())) {
                return; // Don't trigger shortcuts when typing in inputs
            }

            if (e.key === 'n' || e.key === 'N') {
                e.preventDefault();
                const m = new bootstrap.Modal(document.getElementById('quickAddModal'));
                m.show();
            } else if (e.key === 'd' || e.key === 'D') {
                e.preventDefault();
                toggleTheme();
            } else if (e.key === '/') {
                e.preventDefault();
                const s = document.querySelector('input[name="search"]') || document.getElementById('liveSearch');
                if (s) {
                    s.focus();
                    s.select();
                }
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
