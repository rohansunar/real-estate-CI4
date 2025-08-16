<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

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
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }

        .login-body {
            padding: 2rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
        }

        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 1.5rem;
        }

        .back-to-site {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        .back-to-site a {
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .back-to-site a:hover {
            color: var(--primary-color);
        }

        .agent-badge {
            background: linear-gradient(135deg, var(--warning-color) 0%, #e0a800 100%);
            color: #212529;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
            display: inline-block;
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 1rem;
            }

            .login-header,
            .login-body {
                padding: 1.5rem;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card animate__animated animate__fadeInUp">
            <!-- Login Header -->
            <div class="login-header">
                <div class="agent-badge">
                    <i class="fas fa-user-tie me-2"></i>Agent Portal
                </div>
                <h1><i class="fas fa-sign-in-alt me-2"></i>Agent Login</h1>
                <p>Access your agent dashboard</p>
            </div>

            <!-- Login Body -->
            <div class="login-body">
                <!-- Error Messages -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger animate__animated animate__fadeInDown">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success animate__animated animate__fadeInDown">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger animate__animated animate__fadeInDown">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form action="<?= base_url('agent/login') ?>" method="post" id="loginForm">
                    <?= csrf_field() ?>

                    <div class="form-floating">
                        <input type="email"
                               class="form-control"
                               id="email"
                               name="email"
                               placeholder="name@example.com"
                               value="<?= old('email') ?>"
                               required>
                        <label for="email">
                            <i class="fas fa-envelope me-2"></i>Email Address
                        </label>
                    </div>

                    <div class="form-floating">
                        <input type="password"
                               class="form-control"
                               id="password"
                               name="password"
                               placeholder="Password"
                               required>
                        <label for="password">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Login to Dashboard
                    </button>
                <?php echo view('agent/auth/_links'); ?>

                </form>

                <!-- Back to Site -->
                <div class="back-to-site">
                    <a href="<?= base_url() ?>">
                        <i class="fas fa-arrow-left me-2"></i>
                        Back to Main Website
                    </a>
                    <br>
                    <small class="text-muted mt-2 d-block">
                        Need help? Contact your administrator
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Form validation and enhancement
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
            submitBtn.disabled = true;

            // Re-enable button after 5 seconds as fallback
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });

        // Auto-focus on first input
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('input[type="email"]');
            if (firstInput) {
                firstInput.focus();
            }
        });
    </script>
</body>
</html>
