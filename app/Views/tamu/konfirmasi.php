<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Konfirmasi Pendaftaran<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="glass-card shadow-lg border-0" style="border-radius: 1.25rem;">
    <div class="glass-card-body p-5 text-center">
        <!-- Success Icon Animation -->
        <div class="text-success mb-4" style="font-size: 5rem; animation: pulse 2s infinite;">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        
        <h3 class="fw-bold text-dark mb-1">Pendaftaran Berhasil!</h3>
        <p class="text-secondary small mb-4">Terima kasih, data kunjungan Anda telah disimpan ke sistem Buku Tamu Elektronik.</p>
        
        <!-- Ticket Card -->
        <div class="card border-0 bg-light p-4 text-start mb-4 shadow-sm" style="border-radius: 0.75rem; border-left: 5px solid #10b981 !important;">
            <div class="text-center mb-3">
                <span class="text-muted small text-uppercase fw-semibold tracking-wider">Nomor Referensi Kunjungan:</span>
                <h2 class="fw-bold text-dark font-monospace mt-1 mb-0" style="letter-spacing: 1px;">#<?= esc($tamu['no_referensi']) ?></h2>
            </div>
            
            <hr class="my-3 border-dashed">
            
            <div class="row g-3 small">
                <div class="col-6">
                    <span class="text-secondary d-block">Nama Tamu</span>
                    <strong class="text-dark"><?= esc($tamu['nama_tamu']) ?></strong>
                </div>
                <div class="col-6">
                    <span class="text-secondary d-block">Instansi / Asal</span>
                    <strong class="text-dark"><?= esc($tamu['instansi']) ?></strong>
                </div>
                
                <div class="col-6">
                    <span class="text-secondary d-block">
                        <?= !empty($tamu['id_agenda']) ? 'Agenda' : 'Pegawai Tujuan' ?>
                    </span>
                    <strong class="text-dark">
                        <i class="bi <?= !empty($tamu['id_agenda']) ? 'bi-calendar-event me-1 text-secondary' : 'bi-person-fill me-1 text-secondary' ?>"></i>
                        <?php if (!empty($tamu['id_agenda'])): ?>
                            <?= esc($tamu['nama_agenda'] ?? 'Agenda') ?>
                        <?php else: ?>
                            <?= !empty($tamu['nama_pegawai']) ? esc($tamu['nama_pegawai']) : 'Belum ditentukan' ?>
                        <?php endif; ?>
                    </strong>
                </div>
                <div class="col-6">
                    <span class="text-secondary d-block">Unit Kerja Tujuan</span>
                    <strong class="text-dark"><?= esc($tamu['nama_bagian'] ?: ($tamu['nama_opd'] ?? 'Belum ditentukan')) ?></strong>
                </div>

                <div class="col-6">
                    <span class="text-secondary d-block">Waktu Datang</span>
                    <strong class="text-dark"><i class="bi bi-clock me-1 text-secondary"></i><?= date('d M Y, H:i', strtotime($tamu['waktu_datang'])) ?> WIB</strong>
                </div>
                <div class="col-6">
                    <span class="text-secondary d-block">Status Kunjungan</span>
                    <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis px-3 py-1 font-semibold text-capitalize"><?= esc($tamu['status_kunjungan']) ?></span>
                </div>
            </div>
        </div>

        <div class="alert alert-warning border-0 d-flex align-items-start text-start mb-4" style="border-radius: 0.75rem;">
            <i class="bi bi-info-circle-fill fs-5 me-3 text-warning"></i>
            <div class="small text-warning-emphasis">
                <strong>PENTING:</strong> Silakan screenshot halaman ini atau catat <strong>Nomor Referensi</strong> Anda, lalu tunjukkan kepada petugas pelayanan/admin unit untuk verifikasi masuk.
            </div>
        </div>

        <div class="d-flex justify-content-center gap-2 border-top pt-4">
            <button class="btn btn-light rounded-pill px-4" onclick="window.print()"><i class="bi bi-printer-fill me-1"></i> Cetak Tanda Terima</button>
            <a href="<?= base_url('tamu/agenda/' . $tamu['no_referensi']) ?>" class="btn btn-primary rounded-pill px-4" style="display:none;" id="btn-again">Kembali ke Form</a>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    @media print {
        body.public-body {
            background: #ffffff !important;
            padding: 0;
        }
        .glass-card {
            box-shadow: none !important;
            border: none !important;
        }
        .btn, .alert, .public-logo, p.text-secondary {
            display: none !important;
        }
        .card {
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
        }
    }
</style>
<?= $this->endSection() ?>
