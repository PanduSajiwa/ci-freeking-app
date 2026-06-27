<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #333; }
        .header .period { color: #666; font-size: 14px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f8f9fa; font-weight: bold; }
        .table tr:nth-child(even) { background-color: #f2f2f2; }
        .summary { margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 5px; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .badge-approved { background-color: #28a745; color: white; }
        .badge-pending { background-color: #ffc107; color: black; }
        .badge-rejected { background-color: #dc3545; color: white; }
        .footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENGAJUAN FREE PARKING</h1>
        <div class="period">
            Periode: <?= date('d F Y', strtotime($startDate)) ?> - <?= date('d F Y', strtotime($endDate)) ?>
        </div>
        <div class="period">
            Dicetak pada: <?= date('d F Y H:i:s') ?>
        </div>
    </div>

    <div class="summary">
        <strong>Ringkasan:</strong><br>
        Total Pengajuan: <?= count($submissions) ?> | 
        Disetujui: <?= count(array_filter($submissions, function($s) { return $s['status'] == 'approved'; })) ?> | 
        Pending: <?= count(array_filter($submissions, function($s) { return in_array($s['status'], ['submitted', 'under_review']); })) ?> | 
        Ditolak: <?= count(array_filter($submissions, function($s) { return $s['status'] == 'rejected'; })) ?>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Karyawan</th>
                <th>Perusahaan</th>
                <th>Kendaraan</th>
                <th>Tipe</th>
                <th>Durasi</th>
                <th>Kuota</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($submissions as $submission): ?>
            <tr>
                <td><?= $submission['submission_code'] ?></td>
                <td><?= date('d/m/Y', strtotime($submission['submission_date'])) ?></td>
                <td><?= $submission['full_name'] ?></td>
                <td><?= $submission['company'] ?></td>
                <td><?= $submission['license_plate'] ?></td>
                <td><?= strtoupper($submission['vehicle_type']) ?></td>
                <td><?= $submission['duration_days'] ?> hari</td>
                <td><?= $submission['quota_given'] ?: '0' ?> hari</td>
                <td>
                    <span class="badge badge-<?= $submission['status'] ?>">
                        <?= strtoupper($submission['status']) ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Laporan ini dibuat secara otomatis oleh Sistem Free Parking<br>
        PT. Perusahaan Contoh - <?= date('Y') ?>
    </div>
</body>
</html>