<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Login Section -->
<section class="min-vh-100 d-flex align-items-center justify-content-center bg-light position-relative" style="margin-top: 76px;">
    <!-- Animated Background -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden">
        <div class="position-absolute bg-primary opacity-10 rounded-circle" style="width: 300px; height: 300px; top: -150px; right: -150px; animation: float 6s ease-in-out infinite;"></div>
        <div class="position-absolute bg-secondary opacity-10 rounded-circle" style="width: 200px; height: 200px; bottom: -100px; left: -100px; animation: float 8s ease-in-out infinite reverse;"></div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <!-- Header -->
                <div class="text-center mb-5 animate-fade-in-down">
                    <div class="bg-primary rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px;">
                        <i class="fas fa-building text-white fs-2"></i>
                    </div>
                    <h2 class="display-6 fw-bold text-dark mb-2">Welcome Back</h2>
                    <p class="text-muted">Sign in to your Real Estate account</p>
                </div>

                <!-- Login Form -->
                <div class="card shadow-lg border-0 animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div class="card-body p-5">
                        <?php if (isset($errors) && $errors): ?>
                            <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                                <div class="d-flex">
                                    <i class="fas fa-exclamation-circle text-danger me-3 mt-1"></i>
                                    <div>
                                        <h6 class="alert-heading fw-semibold mb-2">Please fix the following errors:</h6>
                                        <ul class="mb-0 small">
                                            <?php foreach ($errors as $error): ?>
                                                <li><?= esc($error) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('auth/login') ?>" method="post" id="loginForm">
                            <?= csrf_field() ?>

                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold text-dark">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    Email Address
                                </label>
                                <input type="email" id="email" name="email" class="form-control form-control-lg"
                                       placeholder="Enter your email address"
                                       value="<?= old('email') ?>" required>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold text-dark">
                                    <i class="fas fa-lock me-2 text-primary"></i>
                                    Password
                                </label>
                                <div class="position-relative">
                                    <input type="password" id="password" name="password" class="form-control form-control-lg pe-5"
                                           placeholder="Enter your password" required>
                                    <button type="button" onclick="togglePassword()"
                                            class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted pe-3">
                                        <i id="passwordToggle" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="form-check">
                                    <input type="checkbox" id="remember" name="remember" class="form-check-input">
                                    <label for="remember" class="form-check-label text-muted">
                                        Remember me
                                    </label>
                                </div>
                                <a href="#" class="text-primary text-decoration-none small">
                                    Forgot password?
                                </a>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-4">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Sign In
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="text-muted small mb-0">
                        By signing in, you agree to our
                        <a href="#" class="text-primary text-decoration-none">Terms of Service</a>
                        and
                        <a href="#" class="text-primary text-decoration-none">Privacy Policy</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
/* Custom animations for login page */
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
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, var(--bs-primary), #0056b3);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(var(--bs-primary-rgb), 0.3);
}

.card {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}
</style>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.getElementById('passwordToggle');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordToggle.classList.remove('fa-eye');
        passwordToggle.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        passwordToggle.classList.remove('fa-eye-slash');
        passwordToggle.classList.add('fa-eye');
    }
}

// Enhanced form interactions
document.addEventListener('DOMContentLoaded', function() {
    // Add floating label effect
    const formControls = document.querySelectorAll('.form-control');
    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        control.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });

        // Check if already has value
        if (control.value) {
            control.parentElement.classList.add('focused');
        }
    });

    // Add form submission animation
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
            submitBtn.disabled = true;

            // Re-enable if there's an error (form will reload)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
    }

    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});
</script>
<?= $this->endSection() ?>
