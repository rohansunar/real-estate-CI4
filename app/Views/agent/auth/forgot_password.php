<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="text-center mb-3">
                    <h2 class="fw-bold text-primary">Associate Forgot Password</h2>
                    <p class="text-muted small">Enter your associate email address and we'll send you a reset link.</p>
                </div>

                <div class="card border-0 shadow-lg rounded-3">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-envelope-at-fill text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('agent/forgot-password') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">Associate Email</label>
                                <input type="email" class="form-control form-control-lg rounded-pill" id="email" name="email" placeholder="name@example.com" value="<?= old('email') ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-semibold">Send Reset Link</button>
                        </form>
                        <div class="text-center mt-4">
                            <a href="<?= base_url('agent/login') ?>" class="text-decoration-none text-muted small fw-semibold">← Back to Associate Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

