<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manajemen Pengguna</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="<?= base_url('/users/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah User
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
            
            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pengguna Sistem</h3>
                </div>
                <div class="card-body">
                    <table id="usersTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['username'] ?></td>
                                <td><?= $user['full_name'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td>
                                    <span class="badge badge-<?= getRoleBadge($user['role']) ?>">
                                        <?= strtoupper($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['is_active']): ?>
                                    <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                    <span class="badge badge-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                <td>
    <a href="<?= base_url('/users/edit/' . $user['id']) ?>" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i>
    </a>
    <form method="POST" action="<?= base_url('/users/toggleStatus/' . $user['id']) ?>" style="display: inline;">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-<?= $user['is_active'] ? 'secondary' : 'success' ?> btn-sm">
            <i class="fas fa-<?= $user['is_active'] ? 'ban' : 'check' ?>"></i>
        </button>
    </form>
    <?php if ($user['id'] != session()->get('user_id')): ?>
        <?php
        $userModel = new \App\Models\UserModel();
        $canDelete = $userModel->canDelete($user['id']);
        ?>
        <?php if ($canDelete): ?>
            <form method="POST" action="<?= base_url('/users/delete/' . $user['id']) ?>" style="display: inline;" onsubmit="return confirm('Yakin hapus user?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        <?php else: ?>
            <button class="btn btn-danger btn-sm" disabled title="User memiliki data historis dan tidak dapat dihapus">
                <i class="fas fa-trash"></i>
            </button>
            <small class="text-muted" title="User memiliki data historis">*</small>
        <?php endif; ?>
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
    $('#usersTable').DataTable({
        "responsive": true,
        "autoWidth": false,
    });
});
</script>