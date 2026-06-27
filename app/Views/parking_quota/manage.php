<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Kelola Kuota Parkir</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('/parkingquota') ?>">Manajemen Kuota</a></li>
                        <li class="breadcrumb-item active">Kelola Kuota</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <?php if (!empty($currentQuota)): ?>
                <?php
                    $used = intval($currentQuota['used_quota']);
                    $total = intval($currentQuota['total_quota']);
                    $ratio = $total > 0 ? $used / $total : 0;
                ?>
                <?php if ($ratio >= 0.8): ?>
                    <div class="alert alert-warning">
                        <strong>Perhatian:</strong> Kuota bulan ini hampir habis (<?= $used ?> / <?= $total ?>).
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Atur Kuota Bulanan</h3>
                        </div>
                        <form action="<?= base_url('/parkingquota/manage') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Bulan dan Tahun *</label>
                                    <input type="month" name="month_year" class="form-control" 
                                           value="<?= date('Y-m') ?>" 
                                           min="<?= date('Y-m') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Total Kuota *</label>
                                    <input type="number" name="total_quota" class="form-control" 
                                           min="1" max="1000" value="<?= $currentQuota['total_quota'] ?? 100 ?>" required>
                                    <small class="text-muted">Jumlah maksimal parkir gratis yang dapat diberikan dalam bulan tersebut</small>
                                </div>
                                
                                <?php if ($currentQuota): ?>
                                <div class="alert alert-info">
                                    <strong>Info:</strong> Kuota saat ini untuk <?= date('F Y', strtotime($currentQuota['month_year'] . '-01')) ?>: 
                                    <?= $currentQuota['total_quota'] ?> parkir<br>
                                    Terpakai: <?= $currentQuota['used_quota'] ?> parkir
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Simpan Kuota</button>
                                <a href="<?= base_url('/parkingquota') ?>" class="btn btn-default">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Panduan Kuota</h3>
                        </div>
                        <div class="card-body">
                            <h5><i class="fas fa-info-circle"></i> Cara Kerja Sistem Kuota:</h5>
                            <ul>
                                <li>Kuota diatur per bulan</li>
                                <li>Setiap pengajuan yang disetujui akan mengurangi kuota</li>
                                <li>Kuota yang sudah terpakai tidak dapat dikembalikan</li>
                                <li>Sistem akan memperingati ketika kuota hampir habis</li>
                            </ul>
                            
                            <h5><i class="fas fa-lightbulb"></i> Tips:</h5>
                            <ul>
                                <li>Atur kuota berdasarkan rata-rata kebutuhan bulanan</li>
                                <li>Monitor penggunaan kuota secara berkala</li>
                                <li>Perhatikan pola penggunaan di akhir bulan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Pengajuan Menunggu Kuota</h3>
                </div>
                <div class="card-body">
                    <?php
                    $pendingSubmissions = model('ParkingSubmissionModel')
                        ->where('operation_manager_approval', 'approved')
                        ->where('parking_dept_approval', 'pending')
                        ->countAllResults();
                    ?>
                    
                    <?php if ($pendingSubmissions > 0): ?>
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Ada <?= $pendingSubmissions ?> pengajuan yang menunggu pemberian kuota!</h5>
                        <p>Segera berikan kuota untuk pengajuan yang sudah disetujui Operation Manager.</p>
                        <a href="<?= base_url('/parkingsubmissions/approval') ?>" class="btn btn-warning">
                            <i class="fas fa-arrow-right"></i> Ke Halaman Approval
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-success">
                        <h5><i class="icon fas fa-check"></i> Tidak ada pengajuan yang menunggu kuota</h5>
                        <p>Semua pengajuan telah diproses.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>