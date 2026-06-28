<?php use App\Helpers\PermissionHelper; ?>
<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Master Data Karyawan</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <!-- Only Admin can create customers -->
                    <?php if (PermissionHelper::isAdmin()): ?>
                        <a href="<?= base_url('/customers/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Karyawan
                        </a>
                    <?php endif; ?>
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
                    <h3 class="card-title">Daftar Karyawan</h3>
                </div>
                <div class="card-body" style="overflow-x: auto;">
                    <style>
                        #customersTable {
                            font-size: 13px;
                        }
                        #customersTable th {
                            background-color: #f8f9fa;
                            font-weight: 600;
                            padding: 12px 8px;
                            white-space: nowrap;
                        }
                        #customersTable td {
                            padding: 10px 8px;
                            vertical-align: middle;
                        }
                        #customersTable td:nth-child(2) { max-width: 150px; word-wrap: break-word; }
                        #customersTable td:nth-child(3) { max-width: 120px; word-wrap: break-word; }
                        #customersTable td:nth-child(5) { max-width: 140px; word-wrap: break-word; }
                        .table-responsive {
                            min-height: 300px;
                        }
                    </style>

                    <table id="customersTable" class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th style="width: 100px;">NIK</th>
                                <th style="width: 150px;">Nama Lengkap</th>
                                <th style="width: 120px;">Perusahaan</th>
                                <th style="width: 90px;">Telepon</th>
                                <th style="width: 140px;">Email</th>
                                <th style="width: 80px; text-align: center;">Total</th>
                                <th style="width: 90px;">Tgl Dibuat</th>
                                <th style="width: 90px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><code style="font-size: 11px;"><?= $customer['nik'] ?: '-' ?></code></td>
                                <td><?= $customer['full_name'] ?></td>
                                <td><?= $customer['company'] ?></td>
                                <td><?= $customer['phone'] ?: '-' ?></td>
                                <td style="font-size: 12px;"><small><?= $customer['email'] ?: '-' ?></small></td>
                                <td style="text-align: center;">
                                    <span class="badge badge-info"><?= $customer['total_submissions'] ?></span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($customer['created_at'])) ?></td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <!-- Only Admin can edit and delete customers -->
                                    <?php if (PermissionHelper::isAdmin()): ?>
                                        <a href="<?= base_url('/customers/edit/' . $customer['id']) ?>" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="<?= base_url('/customers/delete/' . $customer['id']) ?>" style="display: inline;" onsubmit="return confirm('Yakin hapus customer <?= $customer['full_name'] ?>?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Lihat</span>
                                    <?php endif; ?>
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
    $('#customersTable').DataTable({
        "responsive": true,
        "autoWidth": false,
    });
});
</script>