<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Agent Dashboard') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <?= $this->renderSection('styles') ?>
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --agent-primary: #28a745;
            --agent-secondary: #17a2b8;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--agent-primary) 0%, #1e7e34 100%);
            min-height: 100vh;
            width: 280px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .sidebar-header h4 {
            color: white;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .sidebar-header .agent-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .nav-section {
            padding: 1rem 0;
        }

        .nav-section-title {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0 1.5rem;
            margin-bottom: 0.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            background: none;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-right: 3px solid white;
        }

        .nav-icon {
            width: 20px;
            margin-right: 1rem;
            text-align: center;
        }

        .main-content {
            margin-left: 280px;
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        .header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid #e9ecef;
        }

        .agent-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .agent-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--agent-primary) 0%, var(--agent-secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--agent-primary) 0%, var(--agent-secondary) 100%);
            color: white;
        }

        .stat-card .card-body {
            padding: 2rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        .btn-agent-primary {
            background: linear-gradient(135deg, var(--agent-primary) 0%, #1e7e34 100%);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-agent-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            color: white;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <h4><i class="fas fa-user-tie me-2"></i>Agent Portal</h4>
            <div class="agent-badge">
                <?= esc(session()->get('agent_unique_id') ?? 'AGENT') ?>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="nav flex-column">
            <!-- Dashboard Section -->
            <div class="nav-section">
                <div class="nav-section-title">Dashboard</div>
                <a href="<?= base_url('agent/dashboard') ?>" 
                   class="nav-link <?= strpos(uri_string(), 'agent/dashboard') !== false && !strpos(uri_string(), 'agent/dashboard/') ? 'active' : '' ?>">
                    <div class="nav-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <span class="nav-text">Overview</span>
                </a>
            </div>

            <!-- Profile Section -->
            <div class="nav-section">
                <div class="nav-section-title">Profile</div>
                <a href="<?= base_url('agent/profile') ?>" 
                   class="nav-link <?= strpos(uri_string(), 'agent/profile') !== false ? 'active' : '' ?>">
                    <div class="nav-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="nav-text">My Profile</span>
                </a>
            </div>

            <!-- Team Section -->
            <div class="nav-section">
                <div class="nav-section-title">Team Management</div>
                <a href="<?= base_url('agent/sub-agents') ?>"
                   class="nav-link <?= strpos(uri_string(), 'agent/sub-agents') !== false ? 'active' : '' ?>">
                    <div class="nav-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="nav-text">Sub-Agents</span>
                </a>
                <a href="<?= base_url('agent/hierarchy') ?>"
                   class="nav-link <?= strpos(uri_string(), 'agent/hierarchy') !== false ? 'active' : '' ?>">
                    <div class="nav-icon">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <span class="nav-text">Hierarchy Tree</span>
                </a>
                <a href="<?= base_url('agent/downline') ?>"
                   class="nav-link <?= strpos(uri_string(), 'agent/downline') !== false ? 'active' : '' ?>">
                    <div class="nav-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <span class="nav-text">Downline Management</span>
                </a>
            </div>

            <!-- Commission Section -->
            <!-- <div class="nav-section">
                <div class="nav-section-title">Commission</div>
                <a href="<?= base_url('agent/commissions') ?>"
                   class="nav-link <?= strpos(uri_string(), 'agent/commissions') !== false ? 'active' : '' ?>">
                    <div class="nav-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <span class="nav-text">Commission Dashboard</span>
                </a>
            </div> -->

            <!-- Logout Section -->
            <div class="nav-section nav-section-bottom">
                <a href="<?= base_url('agent/logout') ?>" class="nav-link">
                    <div class="nav-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <span class="nav-text">Logout</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Header -->
        <header class="header d-flex align-items-center justify-content-between">
            <!-- Mobile menu button -->
            <button class="btn btn-link d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Page Title -->
            <div class="flex-grow-1">
                <h1 class="h4 mb-0 fw-semibold">
                    <?= isset($pageTitle) ? esc($pageTitle) : 'Agent Dashboard' ?>
                </h1>
            </div>

            <!-- Agent Info -->
            <div class="agent-info">
                <div class="agent-avatar">
                    <?= strtoupper(substr(session()->get('agent_name') ?? 'A', 0, 1)) ?>
                </div>
                <div class="d-none d-md-block">
                    <div class="fw-semibold"><?= esc(session()->get('agent_name') ?? 'Agent') ?></div>
                    <small class="text-muted"><?= esc(session()->get('agent_email') ?? '') ?></small>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show m-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Main Content Area -->
        <main class="p-4">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
