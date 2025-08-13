<?= $this->extend('layouts/agent_dashboard') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Commission Dashboard</h2>
            <p class="text-muted">Track your earnings and commission distribution</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-2"></i>
                    Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>PDF Report</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel Export</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Commission Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold fs-4">₹<?= number_format($commissionStats['total_earned'], 2) ?></div>
                        <div class="small">Total Earned</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold fs-4">₹<?= number_format($commissionStats['paid_amount'], 2) ?></div>
                        <div class="small">Paid Amount</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold fs-4">₹<?= number_format($commissionStats['pending_amount'], 2) ?></div>
                        <div class="small">Pending Amount</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold fs-4">₹<?= number_format($commissionStats['this_month_earned'], 2) ?></div>
                        <div class="small">This Month</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Commission Chart -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Monthly Commission Trends
                </h5>
            </div>
            <div class="card-body">
                <canvas id="commissionChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">
                    <i class="fas fa-pie-chart me-2 text-success"></i>
                    Commission Breakdown
                </h5>
            </div>
            <div class="card-body">
                <div class="commission-breakdown">
                    <div class="breakdown-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Direct Sales</span>
                            <span class="fw-bold">₹<?= number_format($commissionStats['paid_amount'], 2) ?></span>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: <?= $commissionStats['total_earned'] > 0 ? ($commissionStats['paid_amount'] / $commissionStats['total_earned']) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="breakdown-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Downline Commissions</span>
                            <span class="fw-bold">₹<?= number_format(count($downlineEarnings) * 1000, 2) ?></span>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: <?= count($downlineEarnings) * 10 ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="breakdown-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Pending</span>
                            <span class="fw-bold">₹<?= number_format($commissionStats['pending_amount'], 2) ?></span>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: <?= $commissionStats['total_earned'] > 0 ? ($commissionStats['pending_amount'] / $commissionStats['total_earned']) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="text-center">
                        <div class="h5 text-primary">₹<?= number_format($commissionStats['total_earned'], 2) ?></div>
                        <div class="small text-muted">Total Commission Earned</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2 text-success"></i>
                    Recent Commission Transactions
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recentTransactions)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Property</th>
                                    <th>Sale Amount</th>
                                    <th>Commission</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $transaction): ?>
                                    <tr>
                                        <td>
                                            <div class="small">
                                                <?= date('M j, Y', strtotime($transaction['created_at'])) ?>
                                                <br>
                                                <span class="text-muted"><?= date('g:i A', strtotime($transaction['created_at'])) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold"><?= esc($transaction['property_title']) ?></div>
                                            <div class="small text-muted">
                                                Sold by: <?= esc($transaction['selling_agent_name']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">₹<?= number_format($transaction['sale_amount'], 2) ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-success">₹<?= number_format($transaction['commission_amount'], 2) ?></div>
                                            <div class="small text-muted"><?= $transaction['commission_percentage'] ?>%</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">Level <?= $transaction['hierarchy_level'] ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'processed' => 'info',
                                                'paid' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusColor = $statusColors[$transaction['transaction_status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $statusColor ?>">
                                                <?= ucfirst($transaction['transaction_status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No Commission Transactions Yet</h6>
                        <p class="text-muted">Commission transactions will appear here when you or your downline make sales.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users me-2 text-info"></i>
                    Downline Performance
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($downlineEarnings)): ?>
                    <div class="downline-performance">
                        <?php foreach (array_slice($downlineEarnings, 0, 5) as $earning): ?>
                            <div class="performance-item mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold"><?= esc($earning['selling_agent_name'] ?? 'Unknown Agent') ?></div>
                                        <div class="small text-muted">Level <?= $earning['hierarchy_level'] ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success">₹<?= number_format($earning['commission_amount'], 2) ?></div>
                                        <div class="small text-muted"><?= date('M j', strtotime($earning['created_at'])) ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($downlineEarnings) > 5): ?>
                        <div class="text-center mt-3">
                            <a href="<?= base_url('agent/downline') ?>" class="btn btn-sm btn-outline-primary">
                                View All Downline Performance
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-chart-line fa-2x text-muted mb-2"></i>
                        <p class="text-muted small">No downline earnings yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Commission Chart
    const ctx = document.getElementById('commissionChart').getContext('2d');
    const monthlyData = <?= json_encode($monthlyCommissions) ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month_short),
            datasets: [{
                label: 'Commission Earned (₹)',
                data: monthlyData.map(item => item.total_earned),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            },
            elements: {
                point: {
                    radius: 4,
                    hoverRadius: 6
                }
            }
        }
    });
});
</script>

<?= $this->endSection() ?>
