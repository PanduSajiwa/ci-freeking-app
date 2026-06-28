<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Approval Pengajuan Free Parking</h1>
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pengajuan Menunggu Approval</h3>
                </div>
                <div class="card-body">
                    <table id="approvalTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Karyawan</th>
                                <th>Perusahaan</th>
                                <th>Plat Nomor</th>
                                <th>Tanggal</th>
                                <th>Durasi</th>
                                <th>Tujuan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td><?= $submission['submission_code'] ?></td>
                                <td><?= $submission['employee_name'] ?></td>
                                <td><?= $submission['company'] ?></td>
                                <td><?= $submission['license_plate'] ?></td>
                                <td><?= $submission['submission_date'] ?></td>
                                <td><?= $submission['duration_days'] ?> hari</td>
                                <td><?= substr($submission['purpose'], 0, 50) ?>...</td>
                                <td>
                                    <?php
                                    // Show approval status for both manager and parking dept
                                    $managerStatus = $submission['operation_manager_approval'] ?? 'pending';
                                    $parkingStatus = $submission['parking_dept_approval'] ?? 'pending';

                                    // Determine overall status badge
                                    if ($managerStatus == 'rejected' || $parkingStatus == 'rejected') {
                                        $badgeClass = 'danger';
                                        $statusText = '✗ Rejected';
                                    } elseif ($managerStatus == 'approved' && $parkingStatus == 'approved') {
                                        $badgeClass = 'success';
                                        $statusText = '✓✓ Approved';
                                    } else {
                                        $badgeClass = 'warning';
                                        $statusText = '⏳ Pending';
                                    }
                                    ?>
                                    <span class="badge badge-<?= $badgeClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                    <br>
                                    <small style="display: block; margin-top: 4px;">
                                        Manager: <strong><?= ucfirst($managerStatus) ?></strong> |
                                        Parking: <strong><?= ucfirst($parkingStatus) ?></strong>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url('/parkingsubmissions/view/' . $submission['id']) ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <?php if ($submission['operation_manager_approval'] == 'pending' && session()->get('role') == 'operation_manager'): ?>
                                        <a href="<?= base_url('/parkingsubmissions/approve/' . $submission['id']) ?>" class="btn btn-success btn-sm" onclick="return confirm('Setujui pengajuan?')">
                                            <i class="fas fa-check"></i> Approve
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal<?= $submission['id'] ?>">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                        <?php elseif ($submission['parking_dept_approval'] == 'pending' && session()->get('role') == 'parking_dept'): ?>
                                        <!-- Parking dept can approve anytime -->
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#approveWithQuotaModal<?= $submission['id'] ?>">
                                            <i class="fas fa-check"></i> Beri Kuota
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal<?= $submission['id'] ?>">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Modal untuk Reject -->
                                    <div class="modal fade" id="rejectModal<?= $submission['id'] ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Tolak Pengajuan</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form action="<?= base_url('/parkingsubmissions/reject/' . $submission['id']) ?>" method="post">
                                                    <?= csrf_field() ?>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Alasan Penolakan</label>
                                                            <textarea name="notes" class="form-control" rows="3" required placeholder="Berikan alasan penolakan..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal untuk Approve dengan Kuota -->
                                    <div class="modal fade" id="approveWithQuotaModal<?= $submission['id'] ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Berikan Kuota Parkir</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Show approval status -->
                                                    <div class="alert alert-info">
                                                        <strong>Status Approval:</strong><br>
                                                        Manager: <span class="badge badge-<?= $submission['operation_manager_approval'] == 'approved' ? 'success' : 'warning' ?>">
                                                            <?= ucfirst($submission['operation_manager_approval']) ?>
                                                        </span><br>
                                                        Parking Dept: <span class="badge badge-<?= $submission['parking_dept_approval'] == 'approved' ? 'success' : 'warning' ?>">
                                                            <?= ucfirst($submission['parking_dept_approval']) ?>
                                                        </span>
                                                    </div>

                                                    <?php if ($submission['operation_manager_approval'] != 'approved'): ?>
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-circle"></i>
                                                        <strong>Catatan:</strong> Manager belum memberikan approval. Anda dapat memberikan kuota sekarang, tapi approval final memerlukan kedua pihak.
                                                    </div>
                                                    <?php endif; ?>

                                                    <form action="<?= base_url('/parkingsubmissions/approve/' . $submission['id']) ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <div class="form-group">
                                                            <label>Jumlah slot kuota yang diberikan</label>
                                                            <input type="number" name="quota_given" class="form-control" min="1" value="1" required>
                                                            <small class="text-muted">Masukkan jumlah slot parkir yang ingin diberikan.</small>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-success">Setujui & Pakai Kuota</button>
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

<script>
$(document).ready(function() {
    $('#approvalTable').DataTable();
});
</script>