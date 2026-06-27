<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Pengajuan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('/parkingsubmissions') ?>">Pengajuan</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Pengajuan</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Kode Pengajuan</th>
                                    <td><?= $submission['submission_code'] ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pengajuan</th>
                                    <td><?= date('d/m/Y', strtotime($submission['submission_date'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Durasi</th>
                                    <td><?= $submission['duration_days'] ?> hari</td>
                                </tr>
                                <tr>
                                    <th>Tujuan</th>
                                    <td><?= $submission['purpose'] ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-<?= getStatusBadge($submission['status']) ?>">
                                            <?= strtoupper($submission['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Karyawan</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">NIK</th>
                                    <td><?= $submission['nik'] ?></td>
                                </tr>
                                <tr>
                                    <th>Nama Lengkap</th>
                                    <td><?= $submission['customer_name'] ?></td>
                                </tr>
                                <tr>
                                    <th>Perusahaan</th>
                                    <td><?= $submission['company'] ?></td>
                                </tr>
                                <tr>
                                    <th>Telepon</th>
                                    <td><?= $submission['phone'] ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= $submission['email'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Kendaraan</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Plat Nomor</th>
                                    <td><?= $submission['license_plate'] ?></td>
                                </tr>
                                <tr>
                                    <th>Tipe Kendaraan</th>
                                    <td><?= strtoupper($submission['vehicle_type']) ?></td>
                                </tr>
                                <tr>
                                    <th>Merk</th>
                                    <td><?= $submission['brand'] ?></td>
                                </tr>
                                <tr>
                                    <th>Model</th>
                                    <td><?= $submission['model'] ?></td>
                                </tr>
                                <tr>
                                    <th>Warna</th>
                                    <td><?= $submission['color'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Berkas</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if ($submission['id_card_image']): ?>
                                <div class="col-md-4 text-center">
                                    <p><strong>KTP</strong></p>
                                    <a href="<?= base_url('/uploads/' . $submission['id_card_image']) ?>" target="_blank">
                                        <img src="<?= base_url('/uploads/' . $submission['id_card_image']) ?>" class="img-fluid img-thumbnail" style="max-height: 100px;">
                                    </a>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($submission['vehicle_image']): ?>
                                <div class="col-md-4 text-center">
                                    <p><strong>Kendaraan</strong></p>
                                    <a href="<?= base_url('/uploads/' . $submission['vehicle_image']) ?>" target="_blank">
                                        <img src="<?= base_url('/uploads/' . $submission['vehicle_image']) ?>" class="img-fluid img-thumbnail" style="max-height: 100px;">
                                    </a>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($submission['supporting_doc_image']): ?>
                                <div class="col-md-4 text-center">
                                    <p><strong>Dokumen Pendukung</strong></p>
                                    <a href="<?= base_url('/uploads/' . $submission['supporting_doc_image']) ?>" target="_blank">
                                        <img src="<?= base_url('/uploads/' . $submission['supporting_doc_image']) ?>" class="img-fluid img-thumbnail" style="max-height: 100px;">
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval History -->
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Approval</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Operation Manager</h5>
                            <?php if ($submission['operation_manager_approval']): ?>
                            <p>
                                Status: <span class="badge badge-<?= $submission['operation_manager_approval'] == 'approved' ? 'success' : 'danger' ?>">
                                    <?= strtoupper($submission['operation_manager_approval']) ?>
                                </span><br>
                                Tanggal: <?= date('d/m/Y H:i', strtotime($submission['operation_manager_approval_date'])) ?><br>
                                Catatan: <?= $submission['operation_manager_notes'] ?: '-' ?>
                            </p>
                            <?php else: ?>
                            <p class="text-muted">Menunggu approval</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Parking Department</h5>
                            <?php if ($submission['parking_dept_approval']): ?>
                                <p>
                                Status: <span class="badge badge-<?= $submission['parking_dept_approval'] == 'approved' ? 'success' : 'danger' ?>">
                                    <?= strtoupper($submission['parking_dept_approval']) ?>
                                </span><br>
                                Kuota Diberikan: <?= !empty($submission['quota_given']) ? $submission['quota_given'] . ' slot' : 'Tidak' ?><br>
                                Tanggal: <?= date('d/m/Y H:i', strtotime($submission['parking_dept_approval_date'])) ?><br>
                                Catatan: <?= $submission['parking_dept_notes'] ?: '-' ?>
                            </p>
                            <?php else: ?>
                            <p class="text-muted">Menunggu approval</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Terminate button for admin and parking_dept -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <?php if (in_array(session()->get('role'), ['admin', 'parking_dept'])): ?>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#terminateModal">
                                    <i class="fas fa-ban"></i> Hentikan / Putus Pengajuan
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Terminate Modal -->
                    <div class="modal fade" id="terminateModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Hentikan Pengajuan</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <form action="<?= base_url('/parkingsubmissions/terminate/' . $submission['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Alasan Penghentian</label>
                                            <textarea name="termination_reason" class="form-control" rows="4" placeholder="Jelaskan alasan penghentian..." required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-danger">Hentikan Pengajuan</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>

<?php // Using helper function getStatusBadge() from app/Helpers/auth_helper.php ?>