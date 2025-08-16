<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="min-vh-100 d-flex align-items-center justify-content-center bg-light position-relative" style="margin-top: 76px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Agent Forgot Password</h2>
                    <p class="text-muted">Enter your agent email address and we'll send you a reset link.</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
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
                            <div class="mb-3">
                                <label for="email" class="form-label">Agent Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="<?= old('email') ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="<?= base_url('agent/login') ?>" class="text-decoration-none">Back to Agent Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

