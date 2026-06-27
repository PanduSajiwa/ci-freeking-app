<?php use App\Helpers\PermissionHelper; ?>
<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Master Data Kendaraan</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <!-- Only Admin can create vehicles -->
                    <?php if (PermissionHelper::isAdmin()): ?>
                        <a href="<?= base_url('/vehicles/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Kendaraan
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
                    <h3 class="card-title">Daftar Kendaraan</h3>
                </div>
                <div class="card-body">
                    <table id="vehiclesTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Plat Nomor</th>
                                <th>Tipe</th>
                                <th>Merk</th>
                                <th>Model</th>
                                <th>Warna</th>
                                <th>Total Pengajuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vehicles as $vehicle): ?>
                            <tr>
                                <td><?= $vehicle['license_plate'] ?></td>
                                <td>
                                    <span class="badge badge-info">
                                        <?= strtoupper($vehicle['vehicle_type']) ?>
                                    </span>
                                </td>
                                <td><?= $vehicle['brand'] ?></td>
                                <td><?= $vehicle['model'] ?></td>
                                <td><?= $vehicle['color'] ?></td>
                                <td>
                                    <span class="badge badge-secondary"><?= $vehicle['total_submissions'] ?></span>
                                </td>
                                <td>
                                    <!-- Only Admin can edit and delete vehicles -->
                                    <?php if (PermissionHelper::isAdmin()): ?>
                                        <a href="<?= base_url('/vehicles/edit/' . $vehicle['id']) ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="<?= base_url('/vehicles/delete/' . $vehicle['id']) ?>" style="display: inline;" onsubmit="return confirm('Yakin hapus kendaraan <?= $vehicle['license_plate'] ?>?');">
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
    $('#vehiclesTable').DataTable({
        "responsive": true,
        "autoWidth": false,
    });
});
</script>