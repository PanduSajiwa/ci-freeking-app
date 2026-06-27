<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('/users') ?>">Manajemen User</a></li>
                        <li class="breadcrumb-item active">Edit User</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Edit User</h3>
                </div>
                <form action="<?= base_url('/users/edit/' . $user['id']) ?>" method="post">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                    <?php endif; ?>

                    <?php $validation = session()->getFlashdata('validation'); ?>
                    <?php if (!empty($validation) && is_array($validation)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($validation as $field => $msg): ?>
                                    <?php if (is_array($msg)): ?>
                                        <?php foreach ($msg as $m): ?>
                                            <li><?= esc($field . ': ' . $m) ?></li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li><?= esc($field . ': ' . $msg) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username *</label>
                                    <input type="text" name="username" class="form-control" value="<?= old('username') ?: $user['username'] ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Password (Kosongkan jika tidak diubah)</label>
                                    <input type="password" name="password" class="form-control" placeholder="Masukkan password baru">
                                </div>
                                
                                <div class="form-group">
                                    <label>Nama Lengkap *</label>
                                    <input type="text" name="full_name" class="form-control" value="<?= old('full_name') ?: $user['full_name'] ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= old('email') ?: $user['email'] ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>Role *</label>
                                    <select name="role" class="form-control" required>
                                        <option value="admin" <?= (old('role') ?: $user['role']) == 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="operation_manager" <?= (old('role') ?: $user['role']) == 'operation_manager' ? 'selected' : '' ?>>Operation Manager</option>
                                        <option value="parking_dept" <?= (old('role') ?: $user['role']) == 'parking_dept' ? 'selected' : '' ?>>Parking Department</option>
                                        <option value="employee" <?= (old('role') ?: $user['role']) == 'employee' ? 'selected' : '' ?>>Employee</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" <?= (old('is_active') !== null ? (old('is_active') ? 'checked' : '') : ($user['is_active'] ? 'checked' : '')) ?>>
                                        <label class="custom-control-label" for="is_active">User Aktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="<?= base_url('/users') ?>" class="btn btn-default">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>