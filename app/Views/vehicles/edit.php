<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Kendaraan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('/vehicles') ?>">Data Kendaraan</a></li>
                        <li class="breadcrumb-item active">Edit Kendaraan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Kendaraan</h3>
                </div>
                <form action="<?= base_url('/vehicles/edit/' . $vehicle['id']) ?>" method="post">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Plat Nomor *</label>
                                    <input type="text" name="license_plate" class="form-control" value="<?= $vehicle['license_plate'] ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Tipe Kendaraan *</label>
                                    <select name="vehicle_type" class="form-control" required>
                                        <option value="car" <?= $vehicle['vehicle_type'] == 'car' ? 'selected' : '' ?>>Mobil</option>
                                        <option value="motorcycle" <?= $vehicle['vehicle_type'] == 'motorcycle' ? 'selected' : '' ?>>Motor</option>
                                        <option value="truck" <?= $vehicle['vehicle_type'] == 'truck' ? 'selected' : '' ?>>Truk</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Merk</label>
                                    <input type="text" name="brand" class="form-control" value="<?= $vehicle['brand'] ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Model</label>
                                    <input type="text" name="model" class="form-control" value="<?= $vehicle['model'] ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>Warna</label>
                                    <input type="text" name="color" class="form-control" value="<?= $vehicle['color'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="<?= base_url('/vehicles') ?>" class="btn btn-default">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>