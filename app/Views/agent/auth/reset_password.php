<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="min-vh-100 d-flex align-items-center justify-content-center bg-light position-relative" style="margin-top: 76px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Agent Reset Password</h2>
                    <p class="text-muted">Enter and confirm your new password.</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
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

                        <form action="<?= base_url('agent/reset-password') ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="token" value="<?= esc($token) ?>">
                            <input type="hidden" name="email" value="<?= esc($email) ?>">

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                <div class="form-text">Minimum 6 characters.</div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required minlength="6">
                            </div>

                            <button type="submit" class="btn btn-success w-100">Reset Password</button>
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

