<?= $this->extend('layouts/pegawai') ?>

<?= $this->section('title') ?>Detail Tamu<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="fw-bold text-dark mb-1">Detail Kunjungan Tamu</h2>
            <p class="text-secondary mb-0">
                <span class="font-monospace fw-semibold text-primary">#<?= esc($tamu['no_referensi']) ?></span>
            </p>
        </div>
        <a href="<?= base_url('pegawai-portal/tamu') ?>" class="btn btn-light border rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Guest Info -->
    <div class="col-lg-8">
        <div class="glass-card mb-4">
            <div class="glass-card-header d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-person-circle me-2 text-primary"></i>Informasi Tamu</h5>
                <span class="badge rounded-pill badge-<?= esc($tamu['status_kunjungan']) ?> px-3 py-2 text-capitalize fs-6">
                    <?= esc($tamu['status_kunjungan']) ?>
                </span>
            </div>
            <div class="glass-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold mb-0">Nama Lengkap</label>
                            <div class="fw-semibold text-dark"><?= esc($tamu['nama_tamu']) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold mb-0">NIK / NIP</label>
                            <div class="fw-semibold text-dark font-monospace"><?= esc($tamu['nik']) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold mb-0">Instansi</label>
                            <div class="fw-semibold text-dark"><?= esc($tamu['instansi']) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold mb-0">No. HP</label>
                            <div class="fw-semibold text-dark"><?= esc($tamu['no_hp']) ?></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold mb-0">Alamat</label>
                            <div class="fw-semibold text-dark"><?= esc($tamu['alamat']) ?></div>
                        </div>
                    </div>
                    <?php if (!empty($tamu['keperluan'])): ?>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold mb-0">Keperluan</label>
                            <div class="fw-semibold text-dark"><?= esc($tamu['keperluan']) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="glass-card">
            <div class="glass-card-header">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-success"></i>Timeline Kunjungan</h5>
            </div>
            <div class="glass-card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light text-center">
                            <i class="bi bi-box-arrow-in-right fs-4 text-primary d-block mb-1"></i>
                            <div class="small text-muted fw-semibold">Waktu Datang</div>
                            <div class="fw-bold text-dark"><?= date('d M Y, H:i', strtotime($tamu['waktu_datang'])) ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light text-center">
                            <i class="bi bi-box-arrow-right fs-4 text-danger d-block mb-1"></i>
                            <div class="small text-muted fw-semibold">Waktu Pulang</div>
                            <div class="fw-bold text-dark">
                                <?= $tamu['waktu_pulang'] ? date('d M Y, H:i', strtotime($tamu['waktu_pulang'])) : '-' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light text-center">
                            <i class="bi bi-hourglass-split fs-4 text-warning d-block mb-1"></i>
                            <div class="small text-muted fw-semibold">Durasi</div>
                            <div class="fw-bold text-dark">
                                <?php if ($tamu['waktu_pulang']): ?>
                                    <?php
                                        $diff = strtotime($tamu['waktu_pulang']) - strtotime($tamu['waktu_datang']);
                                        $hours = floor($diff / 3600);
                                        $mins  = floor(($diff % 3600) / 60);
                                        echo ($hours > 0 ? $hours . ' jam ' : '') . $mins . ' menit';
                                    ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Actions & Documents -->
    <div class="col-lg-4">
        <!-- Action Card -->
        <?php if ($tamu['status_kunjungan'] === 'menunggu' || $tamu['status_kunjungan'] === 'berlangsung'): ?>
        <div class="glass-card mb-4">
            <div class="glass-card-header">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-lightning-fill me-2 text-warning"></i>Aksi</h5>
            </div>
            <div class="glass-card-body">
                <?php if ($tamu['status_kunjungan'] === 'menunggu'): ?>
                    <form action="<?= base_url('pegawai-portal/tamu/konfirmasi/' . encode_id($tamu['id'])) ?>" method="POST" class="swal-confirm-form" data-confirm-title="<?= empty($tamu['id_pegawai_tujuan']) ? 'Ambil tamu ini?' : 'Konfirmasi tamu ini?' ?>" data-confirm-text="<?= empty($tamu['id_pegawai_tujuan']) ? 'Tamu akan menjadi tanggung jawab Anda dan status berubah menjadi berlangsung.' : 'Status kunjungan akan diubah menjadi berlangsung.' ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success w-100 rounded-pill py-2 mb-2">
                            <i class="bi bi-check-lg me-2"></i> <?= empty($tamu['id_pegawai_tujuan']) ? 'Ambil & Terima Tamu' : 'Terima Tamu' ?>
                        </button>
                    </form>
                <?php endif; ?>

                <?php if ($tamu['status_kunjungan'] === 'berlangsung'): ?>
                    <form action="<?= base_url('pegawai-portal/tamu/update-status/' . encode_id($tamu['id'])) ?>" method="POST" class="swal-confirm-form" data-confirm-title="Selesaikan kunjungan?" data-confirm-text="Status kunjungan akan diubah menjadi selesai.">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status_kunjungan" value="selesai">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">
                            <i class="bi bi-check-circle me-2"></i> Selesaikan Kunjungan
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Agenda Info -->
        <?php if (!empty($tamu['nama_agenda'])): ?>
        <div class="glass-card mb-4">
            <div class="glass-card-header">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-event me-2 text-indigo"></i>Agenda</h5>
            </div>
            <div class="glass-card-body">
                <div class="fw-semibold text-dark"><?= esc($tamu['nama_agenda']) ?></div>
                <div class="small text-secondary mt-1"><?= esc($tamu['nama_bagian']) ?> - <?= esc($tamu['nama_opd']) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Signature -->
        <?php if (!empty($tamu['tanda_tangan'])): ?>
        <div class="glass-card mb-4">
            <div class="glass-card-header">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pen me-2 text-primary"></i>Tanda Tangan</h5>
            </div>
            <div class="glass-card-body text-center">
                <img src="<?= getUploadUrl($tamu['tanda_tangan'], 'ttd') ?>" alt="Tanda Tangan" class="img-fluid border rounded" style="max-height: 150px; background: #fff;">
            </div>
        </div>
        <?php endif; ?>

        <!-- Photo -->
        <?php if (!empty($tamu['foto'])): ?>
        <div class="glass-card mb-4">
            <div class="glass-card-header">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-camera me-2 text-success"></i>Foto Tamu</h5>
            </div>
            <div class="glass-card-body text-center">
                <img src="<?= getUploadUrl($tamu['foto'], 'foto') ?>" alt="Foto Tamu" class="img-fluid rounded" style="max-height: 200px;">
            </div>
        </div>
        <?php endif; ?>

        <!-- Document -->
        <?php if (!empty($tamu['dokumen_pendukung'])): ?>
        <div class="glass-card">
            <div class="glass-card-header">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark me-2 text-danger"></i>Dokumen</h5>
            </div>
            <div class="glass-card-body text-center">
                <a href="<?= getUploadUrl($tamu['dokumen_pendukung'], 'file') ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="bi bi-download me-1"></i> Lihat Dokumen
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
