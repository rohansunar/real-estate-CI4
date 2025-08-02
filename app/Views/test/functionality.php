<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Functionality Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            System Functionality Test Results
                        </h3>
                    </div>
                    <div class="card-body">
                        
                        <!-- Database Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-database me-2"></i>
                                    Database Connection
                                </h5>
                                <div class="alert <?= $results['database'] === 'Connected' ? 'alert-success' : 'alert-danger' ?>">
                                    <i class="fas fa-<?= $results['database'] === 'Connected' ? 'check-circle' : 'times-circle' ?> me-2"></i>
                                    Status: <?= $results['database'] ?>
                                </div>
                            </div>
                        </div>

                        <!-- Models Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-code me-2"></i>
                                    Models
                                </h5>
                                <div class="row">
                                    <?php foreach ($results['models'] as $model => $status): ?>
                                        <div class="col-md-3 mb-2">
                                            <div class="card <?= $status === 'OK' ? 'border-success' : 'border-danger' ?>">
                                                <div class="card-body text-center py-2">
                                                    <i class="fas fa-<?= $status === 'OK' ? 'check' : 'times' ?> text-<?= $status === 'OK' ? 'success' : 'danger' ?>"></i>
                                                    <div class="small"><?= $model ?></div>
                                                    <div class="small text-muted"><?= $status ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Tables Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-table me-2"></i>
                                    Database Tables
                                </h5>
                                <div class="row">
                                    <?php foreach ($results['tables'] as $table => $status): ?>
                                        <div class="col-md-2 mb-2">
                                            <div class="card <?= $status === 'Exists' ? 'border-success' : 'border-danger' ?>">
                                                <div class="card-body text-center py-2">
                                                    <i class="fas fa-<?= $status === 'Exists' ? 'check' : 'times' ?> text-<?= $status === 'Exists' ? 'success' : 'danger' ?>"></i>
                                                    <div class="small"><?= $table ?></div>
                                                    <div class="small text-muted"><?= $status ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Data Counts -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Data Counts
                                </h5>
                                <div class="row">
                                    <?php foreach ($results['counts'] as $table => $count): ?>
                                        <div class="col-md-3 mb-2">
                                            <div class="card border-info">
                                                <div class="card-body text-center py-2">
                                                    <div class="h4 text-info mb-1"><?= number_format($count) ?></div>
                                                    <div class="small text-capitalize"><?= $table ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Services Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-cog me-2"></i>
                                    Services
                                </h5>
                                <div class="row">
                                    <?php foreach ($results['services'] as $service => $status): ?>
                                        <div class="col-md-3 mb-2">
                                            <div class="card <?= $status === 'OK' ? 'border-success' : 'border-danger' ?>">
                                                <div class="card-body text-center py-2">
                                                    <i class="fas fa-<?= $status === 'OK' ? 'check' : 'times' ?> text-<?= $status === 'OK' ? 'success' : 'danger' ?>"></i>
                                                    <div class="small"><?= $service ?></div>
                                                    <div class="small text-muted"><?= $status ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Directories -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-folder me-2"></i>
                                    Upload Directories
                                </h5>
                                <div class="row">
                                    <?php foreach ($results['upload_dirs'] as $dir => $status): ?>
                                        <div class="col-md-3 mb-2">
                                            <div class="card <?= $status === 'Writable' ? 'border-success' : 'border-warning' ?>">
                                                <div class="card-body text-center py-2">
                                                    <i class="fas fa-<?= $status === 'Writable' ? 'check' : 'exclamation-triangle' ?> text-<?= $status === 'Writable' ? 'success' : 'warning' ?>"></i>
                                                    <div class="small"><?= $dir ?></div>
                                                    <div class="small text-muted"><?= $status ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Test Actions -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-play me-2"></i>
                                    Test Actions
                                </h5>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-success" onclick="testPropertyCreation()">
                                        <i class="fas fa-home me-2"></i>
                                        Test Property Creation
                                    </button>
                                    <button class="btn btn-info" onclick="testAgentCreation()">
                                        <i class="fas fa-user-tie me-2"></i>
                                        Test Agent Creation
                                    </button>
                                    <button class="btn btn-warning" onclick="cleanupTestData()">
                                        <i class="fas fa-trash me-2"></i>
                                        Cleanup Test Data
                                    </button>
                                    <a href="<?= base_url() ?>" class="btn btn-primary">
                                        <i class="fas fa-home me-2"></i>
                                        Visit Homepage
                                    </a>
                                    <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                                        <i class="fas fa-tachometer-alt me-2"></i>
                                        Visit Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Results Area -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div id="testResults" class="alert alert-info" style="display: none;">
                                    <div id="testResultsContent"></div>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($results['error'])): ?>
                            <div class="alert alert-danger mt-4">
                                <h6>Error:</h6>
                                <pre><?= esc($results['error']) ?></pre>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showResult(message, type = 'info') {
            const resultsDiv = document.getElementById('testResults');
            const contentDiv = document.getElementById('testResultsContent');
            
            resultsDiv.className = `alert alert-${type}`;
            contentDiv.innerHTML = message;
            resultsDiv.style.display = 'block';
        }

        function testPropertyCreation() {
            showResult('<i class="fas fa-spinner fa-spin me-2"></i>Testing property creation...', 'info');
            
            fetch('<?= base_url('test/testPropertyCreation') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showResult(`<i class="fas fa-check me-2"></i>${data.message} (ID: ${data.property_id})`, 'success');
                    } else {
                        showResult(`<i class="fas fa-times me-2"></i>${data.message}`, 'danger');
                    }
                })
                .catch(error => {
                    showResult(`<i class="fas fa-times me-2"></i>Error: ${error.message}`, 'danger');
                });
        }

        function testAgentCreation() {
            showResult('<i class="fas fa-spinner fa-spin me-2"></i>Testing agent creation...', 'info');
            
            fetch('<?= base_url('test/testAgentCreation') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showResult(`<i class="fas fa-check me-2"></i>${data.message} (ID: ${data.agent_id})`, 'success');
                    } else {
                        showResult(`<i class="fas fa-times me-2"></i>${data.message}`, 'danger');
                    }
                })
                .catch(error => {
                    showResult(`<i class="fas fa-times me-2"></i>Error: ${error.message}`, 'danger');
                });
        }

        function cleanupTestData() {
            showResult('<i class="fas fa-spinner fa-spin me-2"></i>Cleaning up test data...', 'info');
            
            fetch('<?= base_url('test/cleanup') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showResult(`<i class="fas fa-check me-2"></i>${data.message}`, 'success');
                    } else {
                        showResult(`<i class="fas fa-times me-2"></i>${data.message}`, 'danger');
                    }
                })
                .catch(error => {
                    showResult(`<i class="fas fa-times me-2"></i>Error: ${error.message}`, 'danger');
                });
        }
    </script>
</body>
</html>
