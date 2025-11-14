<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Login Section -->
<section class="d-flex align-items-center justify-content-center bg-light position-relative py-3" style="margin-top: 20px; min-height: 40vh;">


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6">
                <!-- Header -->
                <div class="text-center mb-4">
                    <div class="bg-primary rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px;">
                        <i class="fas fa-building text-white fs-2"></i>
                    </div>
                    <h2 class="display-7 fw-bold text-dark mb-2">Welcome Back</h2>
                    <p class="text-muted">Sign in to your account</p>
                </div>

                <!-- Login Form -->
                <div class="card shadow border-0">
                    <div class="card-body p-4">
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

                            <div class="mb-2">
                                <label for="email" class="form-label fw-semibold text-dark">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    Email Address
                                </label>
                                <input type="email" id="email" name="email" class="form-control"
                                       placeholder="Enter your email address"
                                       value="<?= old('email') ?>" required>
                            </div>

                            <div class="mb-2">
                                <label for="password" class="form-label fw-semibold text-dark">
                                    <i class="fas fa-lock me-2 text-primary"></i>
                                    Password
                                </label>
                                <div class="position-relative">
                                    <input type="password" id="password" name="password" class="form-control pe-5"
                                           placeholder="Enter your password" required>
                                    <button type="button" onclick="togglePassword()"
                                            class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted pe-3">
                                        <i id="passwordToggle" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="form-check">
                                    <input type="checkbox" id="remember" name="remember" class="form-check-input">
                                    <label for="remember" class="form-check-label text-muted">
                                        Remember me
                                    </label>
                                </div>
                                <a href="<?= base_url('auth/forgot-password') ?>" class="text-primary text-decoration-none small">
                                    Forgot password?
                                </a>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Sign In
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
/* Simplified login page styles - animations removed for faster experience */
.form-control:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, var(--bs-primary), #0056b3);
    border: none;
}

.card {
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

// Simplified form interactions - animations removed for faster experience
document.addEventListener('DOMContentLoaded', function() {
    // Basic form submission handling
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
                submitBtn.disabled = true;

                // Re-enable if there's an error (form will reload)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 3000);
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
