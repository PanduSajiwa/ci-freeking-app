<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Ajukan Free Parking</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('/parkingsubmissions') ?>">Pengajuan</a></li>
                        <li class="breadcrumb-item active">Ajukan Baru</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Employee monthly limit info -->
            <?php if ($isEmployee): ?>
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h5><i class="icon fas fa-info-circle"></i> Batas Pengajuan Bulanan</h5>
                Setiap karyawan hanya bisa mengajukan <strong><?= $submissionLimit ?> kali dalam satu bulan</strong>.
                <br><strong>Pengajuan Anda bulan ini: <?= $submissionCount ?>/<?= $submissionLimit ?></strong>
                <?php if ($submissionCount >= $submissionLimit): ?>
                    <br><span class="text-danger">Anda telah mencapai batas pengajuan bulan ini.</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Pengajuan Free Parking</h3>
                </div>
                <form action="<?= base_url('/parkingsubmissions/create') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php if ($isEmployee): ?>
                                    <!-- Employee Manual Input Mode -->
                                    <div class="form-group">
                                        <label>Nama Karyawan</label>
                                        <input type="text" name="employee_name" class="form-control" value="<?= htmlspecialchars($userName) ?>" readonly required>
                                        <small class="text-muted">Nama karyawan otomatis dari akun login Anda.</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Plat Nomor Kendaraan</label>
                                        <input type="text" name="license_plate" class="form-control" placeholder="Contoh: B 1234 ABC" value="<?= htmlspecialchars(session()->getFlashdata('post_data')['license_plate'] ?? '') ?>" required>
                                        <small class="text-muted">Masukkan plat nomor kendaraan.</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Merek Kendaraan</label>
                                        <input type="text" name="vehicle_brand" class="form-control" placeholder="Contoh: Toyota" value="<?= htmlspecialchars(session()->getFlashdata('post_data')['vehicle_brand'] ?? '') ?>" required>
                                        <small class="text-muted">Masukkan merek kendaraan.</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Model Kendaraan</label>
                                        <input type="text" name="vehicle_model" class="form-control" placeholder="Contoh: Avanza" value="<?= htmlspecialchars(session()->getFlashdata('post_data')['vehicle_model'] ?? '') ?>" required>
                                        <small class="text-muted">Masukkan model kendaraan.</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Tipe Kendaraan</label>
                                        <select name="vehicle_type" class="form-control" required>
                                            <option value="">Pilih Tipe</option>
                                            <option value="car" <?= (session()->getFlashdata('post_data')['vehicle_type'] ?? '') === 'car' ? 'selected' : '' ?>>Mobil</option>
                                            <option value="motorcycle" <?= (session()->getFlashdata('post_data')['vehicle_type'] ?? '') === 'motorcycle' ? 'selected' : '' ?>>Motor</option>
                                            <option value="truck" <?= (session()->getFlashdata('post_data')['vehicle_type'] ?? '') === 'truck' ? 'selected' : '' ?>>Truk</option>
                                        </select>
                                        <small class="text-muted">Pilih tipe kendaraan.</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Warna Kendaraan</label>
                                        <input type="text" name="vehicle_color" class="form-control" placeholder="Contoh: Merah" value="<?= htmlspecialchars(session()->getFlashdata('post_data')['vehicle_color'] ?? '') ?>" required>
                                        <small class="text-muted">Masukkan warna kendaraan.</small>
                                    </div>
                                <?php else: ?>
                                    <!-- Admin/Non-Employee Mode (Dropdown) -->
                                    <div class="form-group">
                                        <label>Karyawan</label>
                                        <select name="customer_id" class="form-control" required>
                                            <option value="">Pilih Karyawan</option>
                                            <?php foreach ($customers as $customer): ?>
                                            <option value="<?= $customer['id'] ?>">
                                                <?= $customer['full_name'] ?> - <?= $customer['company'] ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Kendaraan</label>
                                        <select name="vehicle_id" class="form-control" required>
                                            <option value="">Pilih Kendaraan</option>
                                            <?php foreach ($vehicles as $vehicle): ?>
                                            <option value="<?= $vehicle['id'] ?>">
                                                <?= $vehicle['license_plate'] ?> - <?= $vehicle['brand'] ?> <?= $vehicle['model'] ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <label>Durasi (Hari)</label>
                                    <input type="number" name="duration_days" class="form-control" min="1" value="<?= htmlspecialchars(session()->getFlashdata('post_data')['duration_days'] ?? '') ?>" required>
                                    <small class="text-muted">Masukkan jumlah hari (tidak dibatasi oleh sistem).</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tujuan</label>
                                    <textarea name="purpose" class="form-control" rows="3" placeholder="Jelaskan tujuan penggunaan free parking..." required><?= htmlspecialchars(session()->getFlashdata('post_data')['purpose'] ?? '') ?></textarea>
                                </div>

                                <?php if ($isEmployee): ?>
                                    <!-- Employee: Only Parking Ticket Upload -->
                                    <div class="form-group">
                                        <label>Tiket Parkir</label>
                                        <input type="file" name="parking_ticket" class="form-control-file" accept="image/*,.pdf" required>
                                        <small class="text-muted">Upload tiket parkir (gambar atau PDF).</small>
                                    </div>
                                <?php else: ?>
                                    <!-- Admin/Non-Employee: All Document Options -->
                                    <div class="form-group">
                                        <label>Foto KTP (Opsional)</label>
                                        <input type="file" name="id_card_image" class="form-control-file" accept="image/*">
                                        <small class="text-muted">Opsional: foto KTP jika diperlukan.</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Foto Kendaraan (Opsional)</label>
                                        <input type="file" name="vehicle_image" class="form-control-file" accept="image/*">
                                        <small class="text-muted">Opsional: foto kendaraan jika diperlukan.</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Dokumen Pendukung - Kartu (Opsional)</label>
                                        <input type="file" name="supporting_doc_image" class="form-control-file" accept="image/*">
                                        <small class="text-muted">Opsional: kartu atau dokumen pendukung lain.</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Ajukan</button>
                        <a href="<?= base_url('/parkingsubmissions') ?>" class="btn btn-default">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>