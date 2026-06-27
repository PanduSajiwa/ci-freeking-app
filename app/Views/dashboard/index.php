<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Role-specific Alerts -->
            <?php if (isset($pendingApprovals) && $pendingApprovals > 0): ?>
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h5><i class="icon fas fa-exclamation-triangle"></i> Pending Approvals!</h5>
                Anda memiliki <?= $pendingApprovals ?> pengajuan yang menunggu persetujuan.
                <a href="<?= base_url('/parkingsubmissions/approval') ?>" class="alert-link">Lihat di sini</a>
            </div>
            <?php endif; ?>

            <div class="row">
                <!-- Statistic Cards -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $totalSubmissions ?></h3>
                            <p>Total Pengajuan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <a href="<?= base_url('/parkingsubmissions') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $pendingSubmissions ?></h3>
                            <p>Pending Approval</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="<?= base_url('/parkingsubmissions') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $approvedSubmissions ?></h3>
                            <p>Disetujui</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="<?= base_url('/parkingsubmissions') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <!-- <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?= $currentMonthQuota['used_quota'] ?? 0 ?>/<?= $currentMonthQuota['total_quota'] ?? 0 ?></h3>
                            <p>Kuota Bulan Ini</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="small-box-footer text-muted">Manajemen kuota otomatis (tidak ada menu)</div>
                    </div>
                </div> -->
            </div>

            <!-- Additional Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><?= $totalCustomers ?></h3>
                            <p>Total Karyawan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="<?= base_url('/customers') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-dark">
                        <div class="inner">
                            <h3><?= $totalVehicles ?></h3>
                            <p>Total Kendaraan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-car"></i>
                        </div>
                        <a href="<?= base_url('/vehicles') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- Employee-specific stats -->
                <?php if (isset($mySubmissions)): ?>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $mySubmissions ?></h3>
                            <p>Pengajuan Saya</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <a href="<?= base_url('/parkingsubmissions') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $myPendingSubmissions ?></h3>
                            <p>Pending Saya</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <a href="<?= base_url('/parkingsubmissions') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Recent Submissions -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pengajuan Terbaru</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('/parkingsubmissions') ?>" class="btn btn-tool">
                                    <i class="fas fa-plus"></i> Lihat Semua
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Karyawan</th>
                                        <th>Kendaraan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentSubmissions as $submission): ?>
                                    <tr>
                                        <td><?= $submission['submission_code'] ?></td>
                                        <td><?= $submission['full_name'] ?></td>
                                        <td><?= $submission['license_plate'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($submission['submission_date'])) ?></td>
                                        <td>
                                            <span class="badge badge-<?= getStatusBadge($submission['status']) ?>">
                                                <?= ucfirst($submission['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>