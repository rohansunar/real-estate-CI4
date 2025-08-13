<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Forgot Password Section -->
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
                    <div class="bg-warning rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px;">
                        <i class="fas fa-key text-white fs-2"></i>
                    </div>
                    <h2 class="display-6 fw-bold text-dark mb-2">Forgot Password?</h2>
                    <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>
                </div>

                <!-- Forgot Password Form -->
                <div class="card border-0 shadow-lg animate-fade-in-up">
                    <div class="card-body p-4 p-md-5">
                        <!-- Display Success Message -->
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

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

                        <form action="<?= base_url('auth/forgot-password') ?>" method="post" id="forgotPasswordForm">
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

                            <button type="submit" class="btn btn-warning btn-lg w-100 mb-4">
                                <i class="fas fa-paper-plane me-2"></i>
                                Send Reset Link
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

                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="text-muted small mb-0">
                        Having trouble? Contact our
                        <a href="#" class="text-primary text-decoration-none">Support Team</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
/* Custom animations for forgot password page */
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
    border-color: var(--bs-warning);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-warning-rgb), 0.25);
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.btn-warning {
    background: linear-gradient(135deg, var(--bs-warning), #e0a800);
    border: none;
    transition: all 0.3s ease;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(var(--bs-warning-rgb), 0.3);
}

.card {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}

.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
</style>

<script>
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
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
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
