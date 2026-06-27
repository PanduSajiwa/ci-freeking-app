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
                <div class="card-body">
                    <table id="customersTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>NIK</th>
                                <th>Nama Lengkap</th>
                                <th>Perusahaan</th>
                                <th>Telepon</th>
                                <th>Email</th>
                                <th>Total Pengajuan</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?= $customer['nik'] ?></td>
                                <td><?= $customer['full_name'] ?></td>
                                <td><?= $customer['company'] ?></td>
                                <td><?= $customer['phone'] ?></td>
                                <td><?= $customer['email'] ?></td>
                                <td>
                                    <span class="badge badge-info"><?= $customer['total_submissions'] ?></span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($customer['created_at'])) ?></td>
                                <td>
                                    <!-- Only Admin can edit and delete customers -->
                                    <?php if (PermissionHelper::isAdmin()): ?>
                                        <a href="<?= base_url('/customers/edit/' . $customer['id']) ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="<?= base_url('/customers/delete/' . $customer['id']) ?>" style="display: inline;" onsubmit="return confirm('Yakin hapus customer <?= $customer['full_name'] ?>?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Hanya baca</span>
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