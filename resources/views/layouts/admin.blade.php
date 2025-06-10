<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CafeSync Admin - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-lg: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--gradient-primary);
            transition: all 0.3s ease;
            z-index: 1000;
            transform: translateX(-100%);
            box-shadow: var(--shadow-lg);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        /* Desktop sidebar always visible */
        @media (min-width: 992px) {
            .sidebar {
                position: fixed;
                transform: translateX(0);
            }

            .sidebar.collapsed {
                width: var(--sidebar-collapsed-width);
            }

            .main-content {
                margin-left: var(--sidebar-width);
                transition: margin-left 0.3s ease;
            }

            .main-content.expanded {
                margin-left: var(--sidebar-collapsed-width);
            }
        }

        /* Mobile overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* Sidebar Header */
        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-brand {
            color: white;
            text-decoration: none;
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand:hover {
            color: white;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Navigation */
        .sidebar-nav {
            padding: 1rem 0;
            flex: 1;
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 2px 0.75rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            position: relative;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(4px);
        }

        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: -0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: white;
            border-radius: 2px;
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-text {
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar.collapsed .sidebar-brand .brand-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        /* Top Navigation Bar */
        .top-navbar {
            background: white;
            box-shadow: var(--shadow-sm);
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .mobile-menu-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #495057;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .mobile-menu-btn:hover {
            background: #f8f9fa;
        }

        @media (min-width: 992px) {
            .mobile-menu-btn {
                display: none;
            }
        }

        /* Main Content */
        .main-content {
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .content-wrapper {
            padding: 1.5rem;
        }

        @media (max-width: 991.98px) {
            .content-wrapper {
                padding: 1rem;
            }
        }

        /* Cards */
        .card {
            box-shadow: var(--shadow-sm);
            border: none;
            border-radius: 12px;
            transition: box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
        }

        .stats-card {
            border-left: 4px solid;
            background: white;
        }

        .stats-card.primary {
            border-left-color: #007bff;
        }

        .stats-card.success {
            border-left-color: #28a745;
        }

        .stats-card.warning {
            border-left-color: #ffc107;
        }

        .stats-card.danger {
            border-left-color: #dc3545;
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: var(--shadow-sm);
        }

        /* Responsive Utilities */
        @media (max-width: 575.98px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .btn-group-vertical {
                width: 100%;
            }

            .btn-group-vertical .btn {
                margin-bottom: 0.5rem;
            }
        }

        /* Loading Animation */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Breadcrumb */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 1rem;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: #6c757d;
        }

        /* User Profile Dropdown */
        .user-profile {
            position: relative;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .user-avatar:hover {
            transform: scale(1.05);
        }

        /* Responsive Tables */
        @media (max-width: 767.98px) {

            .table-responsive table,
            .table-responsive thead,
            .table-responsive tbody,
            .table-responsive th,
            .table-responsive td,
            .table-responsive tr {
                display: block;
            }

            .table-responsive thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            .table-responsive tr {
                border: 1px solid #ccc;
                margin-bottom: 10px;
                padding: 10px;
                border-radius: 8px;
                background: white;
            }

            .table-responsive td {
                border: none;
                position: relative;
                padding-left: 50%;
                padding-top: 10px;
                padding-bottom: 10px;
            }

            .table-responsive td:before {
                content: attr(data-label) ": ";
                position: absolute;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: 600;
                color: #495057;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                <i class="fas fa-coffee"></i>
                <span class="brand-text">CafeSync</span>
            </a>
            <button class="sidebar-toggle d-lg-none" id="sidebarClose">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="sidebar-nav">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i>
                <span class="nav-text">Dashboard</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                href="#">
                <i class="fas fa-tags"></i>
                <span class="nav-text">Categories</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}"
                href="{{ route('admin.menus.index') }}">
                <i class="fas fa-utensils"></i>
                <span class="nav-text">Menus</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                href="#">
                <i class="fas fa-shopping-cart"></i>
                <span class="nav-text">Orders</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.tables.*') ? 'active' : '' }}"
                href="#">
                <i class="fas fa-chair"></i>
                <span class="nav-text">Tables</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                href="#">
                <i class="fas fa-chart-bar"></i>
                <span class="nav-text">Reports</span>
            </a>
        </div>

        <div class="p-3 border-top" style="border-color: rgba(255, 255, 255, 0.1) !important;">
            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-text">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="mobile-menu-btn d-lg-none" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
                <button class="sidebar-toggle d-none d-lg-block" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="ms-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        @stack('breadcrumb')
                    </ol>
                </nav>
            </div>

            <!-- User Profile -->
            <div class="user-profile">
                <div class="user-avatar" data-bs-toggle="dropdown">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">
                            <i class="fas fa-user me-2"></i>Profile
                        </a></li>
                    <li><a class="dropdown-item" href="#">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                </ul>
            </div>
        </nav>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Alerts -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mainContent = document.getElementById('mainContent');

            // Mobile menu toggle
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.add('show');
                    sidebarOverlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                });
            }

            // Close sidebar on mobile
            function closeMobileSidebar() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }

            if (sidebarClose) {
                sidebarClose.addEventListener('click', closeMobileSidebar);
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeMobileSidebar);
            }

            // Desktop sidebar toggle
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');

                    // Save state to localStorage
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    localStorage.setItem('sidebarCollapsed', isCollapsed);
                });

                // Restore sidebar state
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                }
            }

            // Close mobile sidebar when clicking nav links
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        closeMobileSidebar();
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    closeMobileSidebar();
                }
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Add loading state to forms
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                        submitBtn.disabled = true;

                        // Re-enable if form validation fails
                        setTimeout(() => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }, 3000);
                    }
                });
            });
        });

        // Helper function for responsive tables
        function initResponsiveTables() {
            const tables = document.querySelectorAll('.table-responsive table');
            tables.forEach(table => {
                const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
                const rows = table.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    cells.forEach((cell, index) => {
                        if (headers[index]) {
                            cell.setAttribute('data-label', headers[index]);
                        }
                    });
                });
            });
        }

        // Initialize responsive tables when DOM is ready
        document.addEventListener('DOMContentLoaded', initResponsiveTables);
    </script>

    @stack('scripts')
</body>

</html>