<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Karyawan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('/customers') ?>">Data Karyawan</a></li>
                        <li class="breadcrumb-item active">Edit Karyawan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Karyawan</h3>
                </div>
                <form action="<?= base_url('/customers/edit/' . $customer['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif ?>
                    <?php if ($validation = session()->getFlashdata('validation')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($validation as $field => $msg): ?>
                                    <li><?= esc($msg) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NIK *</label>
                                    <input type="text" name="nik" class="form-control" value="<?= old('nik') ?: $customer['nik'] ?>" required maxlength="16">
                                </div>
                                
                                <div class="form-group">
                                    <label>Nama Lengkap *</label>
                                    <input type="text" name="full_name" class="form-control" value="<?= old('full_name') ?: $customer['full_name'] ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Telepon</label>
                                    <input type="text" name="phone" class="form-control" value="<?= old('phone') ?: $customer['phone'] ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= old('email') ?: $customer['email'] ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>Perusahaan</label>
                                    <input type="text" name="company" class="form-control" value="<?= old('company') ?: $customer['company'] ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea name="address" class="form-control" rows="3"><?= old('address') ?: $customer['address'] ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="<?= base_url('/customers') ?>" class="btn btn-default">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>