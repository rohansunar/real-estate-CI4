<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Reset Password Section -->
<section class="min-vh-100 d-flex align-items-center justify-content-center bg-light position-relative" style="margin-top: 76px;">
    <!-- Animated Background -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden">
        <div class="position-absolute bg-success opacity-10 rounded-circle" style="width: 300px; height: 300px; top: -150px; right: -150px; animation: float 6s ease-in-out infinite;"></div>
        <div class="position-absolute bg-info opacity-10 rounded-circle" style="width: 200px; height: 200px; bottom: -100px; left: -100px; animation: float 8s ease-in-out infinite reverse;"></div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <!-- Header -->
                <div class="text-center mb-5 animate-fade-in-down">
                    <div class="bg-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px;">
                        <i class="fas fa-lock text-white fs-2"></i>
                    </div>
                    <h2 class="display-6 fw-bold text-dark mb-2">Reset Password</h2>
                    <p class="text-muted">Enter your new password below.</p>
                </div>

                <!-- Reset Password Form -->
                <div class="card border-0 shadow-lg animate-fade-in-up">
                    <div class="card-body p-4 p-md-5">
                        <!-- Display Error Message -->
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Display Validation Errors -->
                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('auth/reset-password') ?>" method="post" id="resetPasswordForm">
                            <?= csrf_field() ?>
                            <input type="hidden" name="token" value="<?= esc($token) ?>">
                            <input type="hidden" name="email" value="<?= esc($email) ?>">

                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold text-dark">
                                    <i class="fas fa-lock me-2 text-primary"></i>
                                    New Password
                                </label>
                                <div class="position-relative">
                                    <input type="password" id="password" name="password" class="form-control form-control-lg pe-5"
                                           placeholder="Enter your new password" required minlength="6">
                                    <button type="button" onclick="togglePassword('password')"
                                            class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted pe-3">
                                        <i id="passwordToggle" class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Password must be at least 6 characters long.</div>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirm" class="form-label fw-semibold text-dark">
                                    <i class="fas fa-lock me-2 text-primary"></i>
                                    Confirm New Password
                                </label>
                                <div class="position-relative">
                                    <input type="password" id="password_confirm" name="password_confirm" class="form-control form-control-lg pe-5"
                                           placeholder="Confirm your new password" required minlength="6">
                                    <button type="button" onclick="togglePassword('password_confirm')"
                                            class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted pe-3">
                                        <i id="passwordConfirmToggle" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100 mb-4">
                                <i class="fas fa-check me-2"></i>
                                Reset Password
                            </button>
                        </form>

                        <!-- Back to Login -->
                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Remember your password?
                                <a href="<?= base_url('auth/login') ?>" class="text-primary text-decoration-none fw-semibold">
                                    Back to Login
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
/* Custom animations for reset password page */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-down {
    animation: fadeInDown 0.8s ease-out;
}

.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out;
}

.form-control:focus {
    border-color: var(--bs-success);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-success-rgb), 0.25);
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.btn-success {
    background: linear-gradient(135deg, var(--bs-success), #157347);
    border: none;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(var(--bs-success-rgb), 0.3);
}

.card {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}

.password-strength {
    height: 4px;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.password-strength.weak { background-color: #dc3545; }
.password-strength.medium { background-color: #ffc107; }
.password-strength.strong { background-color: #198754; }
</style>

<script>
// Password toggle functionality
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const toggleIcon = document.getElementById(fieldId === 'password' ? 'passwordToggle' : 'passwordConfirmToggle');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Enhanced form interactions
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirm');
    const resetForm = document.getElementById('resetPasswordForm');

    // Password strength indicator
    if (passwordField) {
        passwordField.addEventListener('input', function() {
            const password = this.value;
            let strength = 'weak';
            
            if (password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password)) {
                strength = 'strong';
            } else if (password.length >= 6) {
                strength = 'medium';
            }
            
            // Update visual feedback (you can add a strength indicator here)
        });
    }

    // Password confirmation validation
    if (confirmField) {
        confirmField.addEventListener('input', function() {
            if (this.value !== passwordField.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Form submission animation
    if (resetForm) {
        resetForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Resetting...';
            submitBtn.disabled = true;

            // Re-enable if there's an error (form will reload)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
    }
});
</script>
<?= $this->endSection() ?>
