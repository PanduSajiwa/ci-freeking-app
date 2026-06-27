<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Laporan & Analytics</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Report Filters -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Filter Laporan</h3>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('/reports') ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tanggal Mulai</label>
                                    <input type="date" name="start_date" class="form-control" value="<?= $startDate ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tanggal Akhir</label>
                                    <input type="date" name="end_date" class="form-control" value="<?= $endDate ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Pengajuan</span>
                            <span class="info-box-number"><?= count($reports) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Disetujui</span>
                            <span class="info-box-number">
                                <?= count(array_filter($reports, function($r) { return $r['status'] == 'approved'; })) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending</span>
                            <span class="info-box-number">
                                <?= count(array_filter($reports, function($r) { return in_array($r['status'], ['submitted', 'under_review']); })) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ditolak</span>
                            <span class="info-box-number">
                                <?= count(array_filter($reports, function($r) { return $r['status'] == 'rejected'; })) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Type Statistics -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Statistik Tipe Kendaraan</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="vehicleTypeChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Kuota Parkir 6 Bulan Terakhir</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="quotaChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Report -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Laporan Detail Pengajuan</h3>
                    <div class="card-tools">
                        <form action="<?= base_url('/reports/generate') ?>" method="post" target="_blank">
                            <input type="hidden" name="report_type" value="submissions">
                            <input type="hidden" name="start_date" value="<?= $startDate ?>">
                            <input type="hidden" name="end_date" value="<?= $endDate ?>">
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <table id="reportsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tanggal</th>
                                <th>Karyawan</th>
                                <th>Perusahaan</th>
                                <th>Kendaraan</th>
                                <th>Tipe</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Kuota Diberikan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?= $report['submission_code'] ?></td>
                                <td><?= date('d/m/Y', strtotime($report['submission_date'])) ?></td>
                                <td><?= $report['full_name'] ?></td>
                                <td><?= $report['company'] ?></td>
                                <td><?= $report['license_plate'] ?></td>
                                <td><?= strtoupper($report['vehicle_type']) ?></td>
                                <td><?= $report['duration_days'] ?> hari</td>
                                <td>
                                    <span class="badge badge-<?= getStatusBadge($report['status']) ?>">
                                        <?= ucfirst($report['status']) ?>
                                    </span>
                                </td>
                                <td><?= $report['quota_given'] ?: '0' ?> hari</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    $('#reportsTable').DataTable();
    
    // Vehicle Type Chart
    var vehicleTypeCtx = document.getElementById('vehicleTypeChart').getContext('2d');
    var vehicleTypeChart = new Chart(vehicleTypeCtx, {
        type: 'doughnut',
        data: {
            labels: [<?= $vehicleTypeLabels ?>],
            datasets: [{
                data: [<?= $vehicleTypeData ?>],
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true
        }
    });
    
    // Quota Usage Chart
    var quotaCtx = document.getElementById('quotaChart').getContext('2d');
    var quotaChart = new Chart(quotaCtx, {
        type: 'bar',
        data: {
            labels: [<?= $quotaLabels ?>],
            datasets: [{
                label: 'Total Kuota',
                data: [<?= $quotaTotalData ?>],
                backgroundColor: '#007bff'
            }, {
                label: 'Terpakai',
                data: [<?= $quotaUsedData ?>],
                backgroundColor: '#28a745'
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>