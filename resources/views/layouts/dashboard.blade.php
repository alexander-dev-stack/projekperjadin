<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Monitoring Perjadin | @yield('title', 'Dashboard')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="{ sidebarOpen: true }">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" :class="{ 'collapsed': !sidebarOpen }">
            <div class="logo">
                <img src="{{ asset('img/logo_bskji.png') }}" alt="Logo" style="width: 32px; height: 32px; background: white; padding: 4px; border-radius: 6px;">
                <span style="font-size: 1.125rem;">E-MONITORING</span>
            </div>

            <nav class="nav-links">
                <li class="nav-item {{ Request::is('/') || Request::is('edit*') || Request::is('tambah*') ? 'active' : '' }}">
                    <a href="{{ url('/') }}">
                        <i data-lucide="users"></i>
                        <span>Data Pegawai</span>
                    </a>
                </li>

                @if(auth()->check() && auth()->user()->role === 'admin')
                <li class="nav-item {{ Request::is('settings*') ? 'active' : '' }}">
                    <a href="{{ url('/settings') }}">
                        <i data-lucide="settings"></i>
                        <span>Settings</span>
                    </a>
                </li>
                @endif
            </nav>

            <div style="margin-top: auto;">
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i data-lucide="log-out"></i>
                            <span>Logout</span>
                        </a>
                    </form>
                </li>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="welcome-text">
                    <h1>@yield('header_title', 'Ringkasan Monitoring')</h1>
                    <p>@yield('header_subtitle', 'Badan Standardisasi dan Kebijakan Jasa Industri')</p>
                </div>

                <div class="user-profile">
                    <div class="avatar">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
                    <div style="display: flex; flex-direction: column;">
                        <span style="font-weight: 600; font-size: 0.875rem;">{{ auth()->user()->name ?? 'Administrator' }}</span>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Pimpinan / Admin</span>
                    </div>
                    <i data-lucide="chevron-down" style="width: 16px; margin-left: 0.5rem; color: var(--text-muted);"></i>
                </div>
            </header>

            @if(session('success'))
                <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--secondary); padding: 1rem; border-radius: 0.75rem; color: var(--secondary); margin-bottom: 2rem;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; padding: 1rem; border-radius: 0.75rem; color: #ef4444; margin-bottom: 2rem;">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Content Area -->
            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
