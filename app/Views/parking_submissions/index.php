<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pengajuan Free Parking</h1>
                </div>
                <?php if (in_array(session()->get('role'), ['admin', 'employee'])): ?>
                <div class="col-sm-6 text-right">
                    <a href="<?= base_url('/parkingsubmissions/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajukan Baru
                    </a>
                </div>
                <?php endif; ?>
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
                    <h3 class="card-title">Daftar Pengajuan</h3>
                </div>
                <div class="card-body">
                    <table id="submissionsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Karyawan</th>
                                <th>Plat Nomor</th>
                                <th>Tanggal</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td><?= $submission['submission_code'] ?></td>
                                <td><?= $submission['customer_name'] ?></td>
                                <td><?= $submission['license_plate'] ?></td>
                                <td><?= date('d/m/Y', strtotime($submission['submission_date'])) ?></td>
                                <td><?= $submission['duration_days'] ?> hari</td>
                                <td>
                                    <?php
                                    $badgeClass = [
                                        'draft' => 'secondary',
                                        'submitted' => 'warning',
                                        'under_review' => 'info',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'completed' => 'primary'
                                    ][$submission['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge badge-<?= $badgeClass ?>">
                                        <?= ucfirst($submission['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('/parkingsubmissions/view/' . $submission['id']) ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (session()->get('role') === 'admin'): ?>
                                    <form method="POST" action="<?= base_url('/parkingsubmissions/delete/' . $submission['id']) ?>" style="display: inline;" onsubmit="return confirm('Yakin hapus pengajuan?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
    $('#submissionsTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "order": [[3, "desc"]] // Order by date descending
    });
});
</script>