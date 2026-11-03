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
            margin-bottom: 28px;
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
            transition: transform 0.2s ease, box-shadow 0.2s ease;
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
            padding: 9px 18px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-theme-secondary:hover {
            background: var(--bd);
            color: var(--tx);
            transform: translateY(-1px);
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
        }

        .btn-theme-danger:hover {
            background: var(--da);
            color: #ffffff;
        }

        .btn-circle {
            width: 40px;
            height: 40px;
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
                            {{ Auth::user()->currency }} Currency
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                </a>

                <div class="d-flex gap-2">
                    <button class="btn btn-theme-secondary w-100 justify-content-center" id="theme-toggle-btn" title="Toggle theme" onclick="toggleTheme()">
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
                    <!-- Quick Add Subscription Button -->
                    <a href="{{ route('subscriptions.create') }}" class="btn-theme">
                        <i class="bi bi-plus-lg"></i>
                        <span>Add Subscription</span>
                    </a>

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

            <!-- Flash Alerts / Feedback Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert" style="background: var(--okb); color: var(--ok); border-left: 4px solid var(--ok) !important;">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert" style="background: var(--sec); color: var(--pri); border-left: 4px solid var(--pri) !important;">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert" style="background: var(--wab); color: var(--wa); border-left: 4px solid var(--wa) !important;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert" style="background: var(--dab); color: var(--da); border-left: 4px solid var(--da) !important;">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <strong>Please check the errors below:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

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
        <a href="{{ route('trials.index') }}" class="bnav-link {{ request()->routeIs('trials.*') ? 'active' : '' }}">
            <i class="bi bi-hourglass-split"></i>
            <span>Trials</span>
        </a>
        <a href="{{ route('analytics.index') }}" class="bnav-link {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
            <i class="bi bi-pie-chart"></i>
            <span>Charts</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="bnav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-person"></i>
            <span>Profile</span>
        </a>
    </nav>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Switcher Script -->
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
        }

        // Initialize Theme
        const savedTheme = localStorage.getItem('subtrack-theme') || 'light';
        applyTheme(savedTheme);
    </script>

    @yield('scripts')
</body>
</html>
