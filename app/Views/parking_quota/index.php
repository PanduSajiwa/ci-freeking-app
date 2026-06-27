<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manajemen Kuota Parkir</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="<?= base_url('/parkingquota/manage') ?>" class="btn btn-primary">
                        <i class="fas fa-cog"></i> Kelola Kuota
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Kuota Bulan Ini (<?= date('F Y') ?>)</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($currentQuota): ?>
                            <div class="progress-group">
                                Kuota Terpakai
                                <span class="float-right"><b><?= $currentQuota['used_quota'] ?></b>/<?= $currentQuota['total_quota'] ?></span>
                                <div class="progress progress-sm">
                                    <?php
                                    $percentage = ($currentQuota['used_quota'] / $currentQuota['total_quota']) * 100;
                                    $progressClass = $percentage > 80 ? 'bg-danger' : ($percentage > 60 ? 'bg-warning' : 'bg-success');
                                    ?>
                                    <div class="progress-bar <?= $progressClass ?>" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <strong>Sisa Kuota:</strong> <?= $currentQuota['total_quota'] - $currentQuota['used_quota'] ?>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning">
                                Kuota untuk bulan ini belum diatur. 
                                <a href="<?= base_url('/parkingquota/manage') ?>">Atur kuota sekarang</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Statistik Cepat</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Menunggu Validasi</span>
                                    <span class="info-box-number"><?= $pendingSubmissions ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Kuota</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Bulan/Tahun</th>
                                <th>Total Kuota</th>
                                <th>Terpakai</th>
                                <th>Sisa</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($monthlyQuotas as $quota): ?>
                            <tr>
                                <td><?= date('F Y', strtotime($quota['month_year'] . '-01')) ?></td>
                                <td><?= $quota['total_quota'] ?></td>
                                <td><?= $quota['used_quota'] ?></td>
                                <td><?= $quota['total_quota'] - $quota['used_quota'] ?></td>
                                <td>
                                    <?php
                                    $percentage = $quota['total_quota'] > 0 ? ($quota['used_quota'] / $quota['total_quota']) * 100 : 0;
                                    $badgeClass = $percentage > 80 ? 'danger' : ($percentage > 60 ? 'warning' : 'success');
                                    ?>
                                    <span class="badge badge-<?= $badgeClass ?>"><?= number_format($percentage, 1) ?>%</span>
                                </td>
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