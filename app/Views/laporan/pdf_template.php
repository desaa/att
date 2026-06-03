<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kunjungan Tamu Elektronik</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .header-container {
            text-align: center;
            border-bottom: 2px solid #333333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #1e1b4b;
        }
        .header-subtitle {
            font-size: 11px;
            font-style: italic;
            margin: 5px 0 0 0;
            color: #666666;
        }
        .meta-info {
            margin-bottom: 15px;
            font-size: 9px;
            width: 100%;
        }
        .meta-info td {
            padding: 2px 0;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .report-table th {
            background-color: #4f46e5;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            text-align: center;
        }
        .report-table td {
            padding: 6px 5px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .report-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .font-mono {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-capitalize {
            text-transform: capitalize;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: capitalize;
        }
        .status-menunggu { background-color: #fef3c7; color: #d97706; }
        .status-berlangsung { background-color: #dbeafe; color: #2563eb; }
        .status-selesai { background-color: #d1fae5; color: #059669; }
        .status-batal { background-color: #fee2e2; color: #dc2626; }
        
        .footer-container {
            margin-top: 30px;
            width: 100%;
            font-size: 9px;
        }
        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #333333;
            padding-top: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header-container">
        <h1 class="header-title">Laporan Kunjungan Tamu Elektronik</h1>
        <?php if (!empty($agendaName)): ?>
            <h3 style="margin: 5px 0 0 0; color: #4f46e5; text-transform: uppercase; font-size: 11px; font-weight: bold;"><?= esc($agendaName) ?></h3>
        <?php endif; ?>
        <p class="header-subtitle">Dinas Komunikasi dan Informatika Kabupaten Grobogan</p>
    </div>

    <!-- Meta Info -->
    <table class="meta-info">
        <tr>
            <td style="width: 120px;"><strong>Tanggal Cetak:</strong></td>
            <td><?= date('d F Y, H:i') ?> WIB</td>
            <td style="width: 120px; text-align: right;"><strong>Dicetak Oleh:</strong></td>
            <td style="text-align: right;"><?= esc($user->nama ?: $user->username) ?> (<?= $isSuperadmin ? 'Superadmin' : 'Admin Unit' ?>)</td>
        </tr>
        <tr>
            <td><strong>Cakupan Unit Kerja:</strong></td>
            <td colspan="3">
                <?= $isSuperadmin ? 'Semua Dinas / Organisasi Perangkat Daerah' : esc($user->nama_opd ?: 'Unit Kerja Admin') ?>
            </td>
        </tr>
    </table>

    <!-- Main Report Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 90px;">No. Referensi</th>
                <th style="width: 110px;">Nama Tamu / Asal</th>
                <th style="width: 100px;">Tujuan Kunjungan</th>
                <th style="width: 150px;">Maksud &amp; Keperluan</th>
                <th style="width: 90px;">Waktu Datang</th>
                <th style="width: 90px;">Waktu Pulang</th>
                <th style="width: 60px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tamus)): ?>
            <tr>
                <td colspan="8" class="text-center py-4">Tidak ada data kunjungan tamu untuk filter laporan ini.</td>
            </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($tamus as $t): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="font-mono text-center">#<?= esc($t['no_referensi']) ?></td>
                    <td>
                        <strong><?= esc($t['nama_tamu']) ?></strong><br>
                        <span style="color: #666666; font-size: 8px;"><?= esc($t['instansi']) ?></span><br>
                        <span style="color: #666666; font-size: 8px;">HP: <?= esc($t['no_hp']) ?></span>
                    </td>
                    <td>
                        <strong><?= esc($t['nama_pegawai'] ?? 'Tamu Agenda') ?></strong><br>
                        <span style="color: #666666; font-size: 8px;"><?= esc($t['nama_bagian']) ?></span>
                    </td>
                    <td style="font-size: 8.5px;">
                        <?php if (!empty($t['id_agenda']) && !empty($t['nama_agenda'])): ?>
                            Menghadiri <?= esc($t['nama_agenda']) ?>
                        <?php else: ?>
                            <?= esc($t['keperluan'] ?? '-') ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= date('d-m-Y H:i', strtotime($t['waktu_datang'])) ?></td>
                    <td class="text-center">
                        <?= $t['waktu_pulang'] ? date('d-m-Y H:i', strtotime($t['waktu_pulang'])) : '-' ?>
                    </td>
                    <td class="text-center">
                        <span class="status-badge status-<?= esc($t['status_kunjungan']) ?>">
                            <?= esc($t['status_kunjungan']) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Signature Block -->
    <div class="footer-container">
        <div class="signature-box">
            <p>Grobogan, <?= date('d F Y') ?></p>
            <p style="margin-top: 5px;">Mengetahui,</p>
            <div class="signature-line">
                <?= esc($user->nama ?: $user->username) ?>
            </div>
            <span style="font-size: 8px; color: #666666;"><?= $isSuperadmin ? 'Super Administrator' : 'Administrator Unit' ?></span>
        </div>
    </div>

</body>
</html>
