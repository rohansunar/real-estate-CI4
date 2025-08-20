<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) : 'Dashboard' ?> | White Rock Realtor Admin</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Custom Styles -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">

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

        /* User avatar fallback */
        .user-avatar-fallback {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
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
                            <?php if (isset($totalProperties) && $totalProperties > 0): ?>
                                <span class="nav-badge"><?= $totalProperties ?></span>
                            <?php endif; ?>
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
                            <?php if (isset($unreadContacts) && $unreadContacts > 0): ?>
                                <span class="nav-badge bg-danger"><?= $unreadContacts ?></span>
                            <?php endif; ?>
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
                <div class="nav-section-title">Agent Management</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard/agents') ?>"
                           class="nav-link <?= strpos(uri_string(), 'agents') !== false ? 'active' : '' ?>">
                            <div class="nav-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <span class="nav-text">Agents</span>
                        </a>
                    </li>
                </ul>
                        <li class="nav-item">
                            <a href="<?= base_url('dashboard/agents/hierarchy') ?>"
                               class="nav-link <?= strpos(uri_string(), 'agents/hierarchy') !== false ? 'active' : '' ?>">
                                <div class="nav-icon">
                                    <i class="fas fa-sitemap"></i>
                                </div>
                                <span class="nav-text">Agents Hierarchy</span>
                            </a>
                        </li>

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
            <button class="btn btn-ghost d-lg-none" id="sidebarToggle">
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
                <!-- Notifications -->
                <div class="dropdown me-3">
                    <button class="btn btn-ghost position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <?php if (isset($unreadContacts) && $unreadContacts > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $unreadContacts ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <?php if (isset($unreadContacts) && $unreadContacts > 0): ?>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('dashboard/enquiries') ?>">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope text-primary me-3"></i>
                                        <div>
                                            <div class="fw-medium">New enquiries</div>
                                            <small class="text-muted"><?= $unreadContacts ?> unread messages</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php else: ?>
                            <li>
                                <div class="dropdown-item-text text-center py-3">
                                    <i class="fas fa-bell-slash text-muted mb-2"></i>
                                    <div class="text-muted">No new notifications</div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="dropdown">
                    <button class="btn btn-ghost d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                        <img src="<?= base_url('assets/images/user.png') ?>"
                             alt="User Avatar"
                             class="rounded-circle me-2"
                             style="width: 2rem; height: 2rem; object-fit: cover;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 2rem; height: 2rem; display: none;">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <i class="fas fa-chevron-down ms-2"></i>
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

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="<?= base_url('assets/js/dashboard.js') ?>?v=1"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
