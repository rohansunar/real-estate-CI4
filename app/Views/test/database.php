<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <h1>Database Migration Test Results</h1>
        <p class="lead">Testing the Node.js to CodeIgniter 4 migration</p>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Test Results</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($tests as $testName => $result): ?>
                            <div class="d-flex align-items-center mb-3">
                                <?php if ($result['status'] === 'success'): ?>
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                <?php else: ?>
                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                <?php endif; ?>
                                <div>
                                    <strong><?= esc(ucwords(str_replace('_', ' ', $testName))) ?>:</strong>
                                    <span class="<?= $result['status'] === 'success' ? 'text-success' : 'text-danger' ?>">
                                        <?= esc($result['message']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="<?= base_url('test/createSampleData') ?>" class="btn btn-primary btn-sm mb-2">
                            <i class="fas fa-plus"></i> Create Sample Data
                        </a>
                        <br>
                        <a href="<?= base_url('properties') ?>" class="btn btn-success btn-sm mb-2">
                            <i class="fas fa-home"></i> View Properties
                        </a>
                        <br>
                        <a href="<?= base_url('auth/login') ?>" class="btn btn-info btn-sm mb-2">
                            <i class="fas fa-sign-in-alt"></i> Test Login
                        </a>
                        <br>
                        <a href="<?= base_url('test/database') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-refresh"></i> Refresh Tests
                        </a>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Migration Summary</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> MongoDB → MySQL</li>
                            <li><i class="fas fa-check text-success"></i> Express.js → CodeIgniter 4</li>
                            <li><i class="fas fa-check text-success"></i> Mongoose → Eloquent Models</li>
                            <li><i class="fas fa-check text-success"></i> Handlebars → PHP Views</li>
                            <li><i class="fas fa-check text-success"></i> Express Routes → CI4 Routes</li>
                            <li><i class="fas fa-check text-success"></i> Express Middleware → CI4 Filters</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
