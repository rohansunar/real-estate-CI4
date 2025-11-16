<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) : 'Dashboard' ?> | Gold Properties Admin</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS with Cache-Busting -->
    <?= css_link('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css') ?>

    <!-- Icons with Cache-Busting -->
    <?= css_link('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css') ?>

    <!-- Custom Dashboard Styles with Automatic Cache-Busting -->
    <?= css_link('assets/css/style.css') ?>

    <!-- Dashboard Specific Styles -->
    <style>
        /* Fix sidebar scrolling */
        .sidebar {
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            scrollbar-width: thin;
            scrollbar-color: rgba(0,0,0,0.2) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(0,0,0,0.3);
        }

        /* Ensure main content doesn't overlap sidebar */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
        }

        /* Mobile-First Responsive Design */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1050;
                width: 280px;
                max-width: 85vw;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            /* Mobile header adjustments */
            .header {
                padding: 0 1rem;
                height: 4rem;
            }

            .header h1 {
                font-size: 1.25rem;
            }

            /* Mobile-friendly buttons */
            .btn {
                min-height: 44px;
                min-width: 44px;
                padding: 0.75rem 1rem;
            }

            #sidebarToggle {
                margin-right: 1rem;
            }
        }

        /* Extra small devices */
        @media (max-width: 575.98px) {
            .sidebar {
                width: 100vw;
                max-width: 100vw;
            }

            .main-content {
                padding: 0.75rem;
            }

            .header {
                padding: 0 0.75rem;
                height: 3.5rem;
            }

            .header h1 {
                font-size: 1.125rem;
            }
        }

        /*
         * ========================================================================
         * ADMIN DASHBOARD STYLING - NOTIFICATION BADGES REMOVED & USER ICON ADDED
         * ========================================================================
         *
         * This section contains the styling for the admin dashboard components.
         *
         * CHANGES MADE:
         * - Completely removed all notification badge functionality
         * - Replaced user.png image with Font Awesome fa-user-circle icon
         * - Maintained responsive design and theme compatibility
         * - Preserved accessibility features (WCAG 2.1 AA compliance)
         *
         * COMPONENTS INCLUDED:
         * - User avatar icon display with Font Awesome
         * - Responsive scaling for mobile devices
         * - Theme support (light/dark mode)
         * - Hover effects and smooth transitions
         * - High contrast mode support
         *
         * @version 3.3.0
         * @updated 2025-08-25 - Removed notifications, added Font Awesome user icon
         * @author Admin Dashboard Team
         */

        /*
         * User Avatar Icon Display - Font Awesome Implementation
         *
         * Implements Font Awesome user-circle icon for user avatar display
         * with proper sizing, alignment, hover states, and theme support.
         *
         * Key Features:
         * - Font Awesome fa-user-circle icon for consistent display
         * - Perfect alignment within dropdown button container
         * - Smooth hover transitions and theme compatibility
         * - Responsive scaling for mobile devices
         * - WCAG 2.1 AA accessibility compliance
         * - No image loading dependencies
         */

        /* Avatar container with proper positioning */
        .user-avatar-container {
            position: relative;
            width: 2.25rem;
            height: 2.25rem;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Font Awesome user avatar icon styling */
        .user-avatar-icon {
            font-size: 2.25rem;
            color: var(--text-secondary, #64748b);
            display: block;
            line-height: 1;

            /* Smooth transitions for all states */
            transition: all 0.3s ease,
                       color 0.3s ease,
                       transform 0.2s ease,
                       filter 0.3s ease;
        }

        /* Avatar icon hover state */
        .user-avatar-icon:hover {
            color: var(--primary-color, #2563eb);
            transform: scale(1.08);
            filter: drop-shadow(0 0.25rem 0.5rem rgba(37, 99, 235, 0.15));
        }

        /* User info text styling with proper alignment */
        .user-info-text {
            color: var(--text-primary, #1e293b);
            font-weight: 500;
            font-size: 0.875rem;
            line-height: 1.25;
            margin-left: 0.75rem;
            transition: color 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120px;
        }

        /* Theme support for user avatar icon */
        [data-theme="dark"] .user-avatar-icon {
            color: var(--text-secondary, #cbd5e1);
        }

        [data-theme="dark"] .user-avatar-icon:hover {
            color: var(--primary-color, #2563eb);
            filter: drop-shadow(0 0.25rem 0.5rem rgba(37, 99, 235, 0.25));
        }

        /* Responsive adjustments for mobile devices */
        @media (max-width: 767.98px) {
            .user-avatar-container {
                width: 2rem;
                height: 2rem;
            }

            .user-avatar-icon {
                font-size: 2rem;
            }

            .user-info-text {
                font-size: 0.8rem;
                max-width: 100px;
            }
        }

        @media (max-width: 575.98px) {
            .user-avatar-container {
                width: 1.875rem;
                height: 1.875rem;
            }

            .user-avatar-icon {
                font-size: 1.875rem;
            }

            .user-info-text {
                font-size: 0.75rem;
                max-width: 80px;
            }
        }

        /* Specific responsive breakpoints for testing requirements */
        @media (max-width: 414px) {
            /* iPhone 6/7/8 Plus and similar devices */
            .user-avatar-container {
                width: 1.75rem;
                height: 1.75rem;
            }

            .user-avatar-icon {
                font-size: 1.75rem;
            }

            .user-info-text {
                max-width: 70px;
            }
        }

        @media (max-width: 375px) {
            /* iPhone 6/7/8 and similar devices */
            .user-avatar-container {
                width: 1.625rem;
                height: 1.625rem;
            }

            .user-avatar-icon {
                font-size: 1.625rem;
            }

            .user-info-text {
                display: none !important; /* Hide on very small screens */
            }
        }

        @media (max-width: 320px) {
            /* iPhone SE and similar small devices */
            .user-avatar-container {
                width: 1.5rem;
                height: 1.5rem;
            }

            .user-avatar-icon {
                font-size: 1.5rem;
            }
        }

        /* Removed redundant responsive rules - now handled in the main responsive sections above */

        /* High contrast mode support for accessibility */
        @media (prefers-contrast: high) {
            .user-avatar-icon {
                filter: contrast(1.2);
                font-weight: 900;
            }
        }

        /* User menu button specific styling to fix alignment issues */
        .user-menu-button.btn-ghost {
            width: auto !important;
            height: auto !important;
            padding: 0.5rem 0.75rem !important;
            min-height: 2.5rem;
            border-radius: 0.5rem;
            background: rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .user-menu-button.btn-ghost:hover {
            background: rgba(37, 99, 235, 0.08);
            border-color: rgba(37, 99, 235, 0.15);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.15);
        }

        .user-menu-button.btn-ghost:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Responsive adjustments for user menu button */
        @media (max-width: 767.98px) {
            .user-menu-button.btn-ghost {
                padding: 0.375rem 0.5rem !important;
                min-height: 2.25rem;
            }
        }

        @media (max-width: 575.98px) {
            .user-menu-button.btn-ghost {
                padding: 0.25rem 0.375rem !important;
                min-height: 2rem;
            }
        }

        /* Dark theme support for user menu button */
        [data-theme="dark"] .user-menu-button.btn-ghost {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }

        [data-theme="dark"] .user-menu-button.btn-ghost:hover {
            background: rgba(37, 99, 235, 0.15);
            border-color: rgba(37, 99, 235, 0.25);
        }

        /* Old image-based avatar styles removed - now using Font Awesome icon */
    </style>

    <!-- Meta tags -->
    <meta name="description" content="Real Estate Admin Dashboard">
    <meta name="robots" content="noindex, nofollow">

    <?= $this->renderSection('styles') ?>
</head>

<body class="bg-light">
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <div class="d-flex align-items-center">
                <div class="sidebar-logo">
                    <i class="fas fa-building"></i>
                </div>
                <div class="sidebar-brand">
                    <h6 class="mb-0">Real Estate</h6>
                    <small class="text-muted">Admin Panel</small>
                </div>
            </div>
            <button class="btn btn-ghost d-lg-none" id="sidebarClose">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- User Profile Section -->
        <!-- <div class="sidebar-user">
            <div class="d-flex align-items-center">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-info">
                    <div class="user-name"><?= isset($user) && $user ? esc($user['name'] ?? $user['email']) : 'Admin User' ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
        </div> -->

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <!-- Main Navigation -->
            <div class="nav-section">
                <!-- <div class="nav-section-title">Main</div> -->
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard') ?>"
                           class="nav-link <?= uri_string() === 'dashboard' ? 'active' : '' ?>">
                            <div class="nav-icon">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Properties Section -->
            <div class="nav-section">
                <div class="nav-section-title">Properties</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard/properties') ?>"
                           class="nav-link <?= strpos(uri_string(), 'properties') !== false ? 'active' : '' ?>">
                            <div class="nav-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <span class="nav-text">Properties</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Customer Management Section -->
            <div class="nav-section">
                <div class="nav-section-title">Customer Management</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard/enquiries') ?>"
                           class="nav-link <?= strpos(uri_string(), 'enquiries') !== false ? 'active' : '' ?>">
                            <div class="nav-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <span class="nav-text">Enquiries</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard/subscribers') ?>"
                           class="nav-link <?= strpos(uri_string(), 'subscribers') !== false ? 'active' : '' ?>">
                            <div class="nav-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <span class="nav-text">Subscribers</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Agent Management Section -->
            <div class="nav-section">
                <div class="nav-section-title">Associate Management</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard/agents') ?>"
                           class="nav-link <?= strpos(uri_string(), 'agents') !== false ? 'active' : '' ?>">
                            <div class="nav-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <span class="nav-text">Associates</span>
                        </a>
                    </li>
                <!-- </ul>
                        <li class="nav-item">
                            <a href="<?= base_url('dashboard/agents/hierarchy') ?>"
                               class="nav-link <?= strpos(uri_string(), 'agents/hierarchy') !== false ? 'active' : '' ?>">
                                <div class="nav-icon">
                                    <i class="fas fa-sitemap"></i>
                                </div>
                                <span class="nav-text">Agents Hierarchy</span>
                            </a>
                        </li>
                </ul> -->
            </div>

            <!-- Blog Management Section -->
            <div class="nav-section">
                <div class="nav-section-title">Blog Management</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard/blog') ?>"
                           class="nav-link <?= strpos(uri_string(), 'blog') !== false ? 'active' : '' ?>">
                            <div class="nav-icon">
                                <i class="fas fa-blog"></i>
                            </div>
                            <span class="nav-text">Blog Posts</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- System Section -->
            <div class="nav-section">
                <div class="nav-section-title">System</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="<?= base_url() ?>" target="_blank" class="nav-link">
                            <div class="nav-icon">
                                <i class="fas fa-external-link-alt"></i>
                            </div>
                            <span class="nav-text">View Website</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Logout Section -->
            <div class="nav-section nav-section-bottom">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="<?= base_url('auth/logout') ?>" class="nav-link nav-link-logout">
                            <div class="nav-icon">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <span class="nav-text">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="header d-flex align-items-center justify-content-between px-4">
            <!-- Mobile menu button -->
            <button class="btn btn-ghost d-lg-none" id="sidebarToggle"
                    aria-label="Toggle navigation menu"
                    style="width: 2.25rem; height: 2.25rem;">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Page Title -->
            <div class="flex-grow-1">
                <h1 class="h4 mb-0 fw-semibold">
                    <?= isset($pageTitle) ? esc($pageTitle) : 'Dashboard' ?>
                </h1>
            </div>

            <!-- Header Actions -->
            <div class="d-flex align-items-center">
                <!-- User Menu -->
                <div class="dropdown">
                    <button class="btn btn-ghost user-menu-button d-flex align-items-center"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-label="User menu for <?= isset($user) && $user ? esc($user['name'] ?? $user['email']) : 'Admin User' ?>"
                            aria-expanded="false"
                            aria-haspopup="true">
                        <!--
                             User Avatar Icon - Font Awesome Implementation

                             Uses Font Awesome fa-user-circle icon instead of user.png image
                             for consistent display without image loading dependencies.

                             Features:
                             - No image loading required (always displays correctly)
                             - Responsive scaling across all screen sizes
                             - Theme-compatible colors (light/dark mode support)
                             - Smooth hover transitions and accessibility compliance
                             - WCAG 2.1 AA compliant with proper ARIA labels
                        -->
                        <div class="user-avatar-container me-2">
                            <i class="fas fa-user-circle user-avatar-icon"
                               aria-label="Avatar for <?= isset($user) && $user ? esc($user['name'] ?? $user['email']) : 'Admin User' ?>"
                               role="img"></i>
                        </div>
                        <span class="user-info-text d-none d-md-inline">
                            <?= isset($user) && $user ? esc($user['name'] ?? $user['email']) : 'Admin User' ?>
                        </span>
                        <i class="fas fa-chevron-down ms-2" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
            <?php if (isset($error) && $error): ?>
                <div class="toast show" role="alert">
                    <div class="toast-header bg-danger text-white">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong class="me-auto">Error</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body"><?= esc($error) ?></div>
                </div>
            <?php endif; ?>

            <?php if (isset($success) && $success): ?>
                <div class="toast show" role="alert">
                    <div class="toast-header bg-success text-white">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong class="me-auto">Success</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body"><?= esc($success) ?></div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Main Content Area -->
        <main class="p-4">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Bootstrap 5 JS with Cache-Busting -->
    <?= js_script('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js') ?>

    <!-- Custom Dashboard JavaScript with Automatic Cache-Busting -->
    <?= js_script('assets/js/dashboard.js') ?>

    <script>
        /**
         * Admin Dashboard Component Enhancement Script
         *
         * Provides comprehensive error handling and accessibility features
         * for user avatar display with Font Awesome icons.
         *
         * Features:
         * - User avatar icon display and fallback handling
         * - ARIA label management for accessibility
         * - Keyboard navigation support
         * - Memory leak prevention
         * - WCAG 2.1 AA compliance
         */

        // Global event handlers array for cleanup
        const dashboardEventHandlers = [];

        /* Avatar error handling removed - now using Font Awesome icon which doesn't require loading */

        /* Notification-related functions removed - no longer needed */

        /**
         * Initialize keyboard navigation support
         *
         * Adds keyboard navigation support for dropdown triggers and
         * other interactive elements to meet WCAG 2.1 AA requirements.
         */
        function initializeKeyboardNavigation() {
            // User menu keyboard support
            const userMenuButton = document.querySelector('.user-avatar-container').closest('button');
            if (userMenuButton) {
                const keyHandler = function(event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        userMenuButton.click();
                    }
                };

                userMenuButton.addEventListener('keydown', keyHandler);
                dashboardEventHandlers.push({
                    element: userMenuButton,
                    event: 'keydown',
                    handler: keyHandler
                });
            }
        }

        /**
         * Initialize dashboard components with comprehensive error handling
         *
         * Sets up all dashboard functionality including avatar handling,
         * accessibility features, and keyboard navigation.
         */
        function initializeDashboardComponents() {
            try {
                // Avatar initialization removed - now using Font Awesome icon which doesn't require loading

                // Initialize keyboard navigation
                initializeKeyboardNavigation();

            } catch (error) {
                // Handle initialization errors gracefully
                // In production, this could be logged to an error tracking service
            }
        }

        /**
         * Cleanup function to prevent memory leaks
         *
         * Removes all event listeners when the page is unloaded to prevent
         * memory leaks in single-page applications or when navigating away.
         */
        function cleanupDashboardComponents() {
            dashboardEventHandlers.forEach(({ element, event, handler }) => {
                try {
                    element.removeEventListener(event, handler);
                } catch (error) {
                    // Ignore cleanup errors
                }
            });
            dashboardEventHandlers.length = 0;
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', initializeDashboardComponents);

        // Cleanup when page is unloaded
        window.addEventListener('beforeunload', cleanupDashboardComponents);
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
