<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tracking Penggunaan Parkir</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#recordUsageModal">
                        <i class="fas fa-plus"></i> Catat Penggunaan
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <div class="row mb-3">
                <div class="col-md-4">
                    <form method="get" action="<?= base_url('/parkingusage') ?>">
                        <div class="input-group">
                            <input type="month" name="month_year" class="form-control" value="<?= $currentMonth ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-info">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Penggunaan Parkir - <?= date('F Y', strtotime($currentMonth . '-01')) ?></h3>
                </div>
                <div class="card-body">
                    <table id="usageTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode Pengajuan</th>
                                <th>Nama Karyawan</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usageData as $usage): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($usage['usage_date'])) ?></td>
                                <td><?= $usage['submission_code'] ?></td>
                                <td><?= $usage['full_name'] ?></td>
                                <td><?= $usage['notes'] ?></td>
                                <td>
                                    <form method="POST" action="<?= base_url('/parkingusage/delete/' . $usage['id']) ?>" style="display: inline;" onsubmit="return confirm('Yakin hapus?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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

<!-- Modal Record Usage -->
<div class="modal fade" id="recordUsageModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Catat Penggunaan Parkir</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('/parkingusage/record') ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pengajuan</label>
                        <select name="submission_id" class="form-control" required>
                            <option value="">Pilih Pengajuan</option>
                            <?php foreach ($approvedSubmissions as $submission): ?>
                            <option value="<?= $submission['id'] ?>">
                                <?= $submission['submission_code'] ?> - <?= $submission['full_name'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Tanggal Penggunaan</label>
                        <input type="date" name="usage_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Catatan penggunaan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>

<script>
$(document).ready(function() {
    $('#usageTable').DataTable();
});
</script>