<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Detail Tamu<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-sm-6">
        <h2 class="fw-bold text-dark">Detail Kunjungan Tamu</h2>
        <p class="text-secondary mb-0">Informasi lengkap kunjungan tamu #<?= esc($tamu['no_referensi']) ?></p>
    </div>
    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
        <a href="<?= base_url('tamu') ?>" class="btn btn-light border rounded-pill px-4 me-2">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Main Guest Card -->
    <div class="col-lg-8">
        <div class="glass-card shadow-sm border-0 mb-4">
            <div class="glass-card-header bg-light bg-opacity-50 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-person-vcard me-2 text-primary"></i>Informasi Tamu</span>
                <span class="badge rounded-pill badge-<?= esc($tamu['status_kunjungan']) ?> px-3 py-1 font-semibold text-capitalize">
                    <?= esc($tamu['status_kunjungan']) ?>
                </span>
            </div>
            
            <div class="glass-card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <span class="text-secondary d-block small">Nama Lengkap</span>
                        <strong class="text-dark fs-5"><?= esc($tamu['nama_tamu']) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-secondary d-block small">Nomor Induk Kependudukan (NIK)</span>
                        <strong class="text-dark font-monospace fs-6"><?= esc($tamu['nik']) ?></strong>
                    </div>
                    
                    <div class="col-md-6">
                        <span class="text-secondary d-block small">Instansi / Asal Tamu</span>
                        <strong class="text-dark"><?= esc($tamu['instansi']) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-secondary d-block small">No. HP (WhatsApp)</span>
                        <strong class="text-dark font-monospace"><?= esc($tamu['no_hp']) ?></strong>
                    </div>
                    
                    <div class="col-12">
                        <span class="text-secondary d-block small">Alamat Lengkap</span>
                        <p class="text-dark mb-0"><?= esc($tamu['alamat']) ?></p>
                    </div>
                </div>

                <h5 class="fw-bold text-indigo mb-3 border-bottom pb-2">Tujuan Kunjungan</h5>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <span class="text-secondary d-block small">Pegawai yang Dituju</span>
                        <strong class="text-dark"><i class="bi bi-person-circle text-secondary me-1"></i><?= esc($tamu['nama_pegawai'] ?? 'Tamu Agenda') ?></strong>
                        <?php if (!empty($tamu['jabatan'])): ?>
                            <div class="small text-muted ps-4"><?= esc($tamu['jabatan']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <span class="text-secondary d-block small">Unit Kerja / Bagian</span>
                        <strong class="text-dark"><?= esc($tamu['nama_bagian']) ?></strong>
                        <div class="small text-muted"><?= esc($tamu['nama_opd']) ?></div>
                        <?php if ($tamu['nama_subbagian']): ?>
                            <div class="text-muted small" style="font-size: 0.8rem;"><i class="bi bi-arrow-return-right me-1"></i><?= esc($tamu['nama_subbagian']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-12">
                        <span class="text-secondary d-block small">Keperluan / Maksud Kunjungan</span>
                        <p class="text-dark mb-0 bg-light p-3 rounded" style="white-space: pre-line;"><?= esc($tamu['keperluan'] ?? '-') ?></p>
                    </div>

                    <div class="col-md-6">
                        <span class="text-secondary d-block small">Sumber Registrasi / Agenda</span>
                        <?php if ($tamu['nama_agenda']): ?>
                            <span class="badge bg-indigo text-white mt-1 px-3"><i class="bi bi-calendar-event me-1"></i><?= esc($tamu['nama_agenda']) ?></span>
                        <?php else: ?>
                            <span class="badge bg-secondary text-white mt-1 px-3"><i class="bi bi-keyboard-fill me-1"></i>Input Manual Admin</span>
                        <?php endif; ?>
                    </div>
                </div>

                <h5 class="fw-bold text-indigo mb-3 border-bottom pb-2">Catatan Waktu</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="text-secondary d-block small">Waktu Kedatangan (Check-In)</span>
                        <strong class="text-dark"><i class="bi bi-calendar-check text-success me-1"></i><?= date('d M Y, H:i:s', strtotime($tamu['waktu_datang'])) ?> WIB</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-secondary d-block small">Waktu Kepulangan (Check-Out)</span>
                        <strong class="text-dark">
                            <?php if ($tamu['waktu_pulang']): ?>
                                <i class="bi bi-calendar-x text-danger me-1"></i><?= date('d M Y, H:i:s', strtotime($tamu['waktu_pulang'])) ?> WIB
                            <?php else: ?>
                                <span class="text-muted italic"><i class="bi bi-dash-circle me-1"></i>Belum Check-Out / Masih Berlangsung</span>
                            <?php endif; ?>
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attached Documents -->
        <?php if ($tamu['dokumen_pendukung']): ?>
        <div class="glass-card shadow-sm border-0 mb-4">
            <div class="glass-card-header bg-light bg-opacity-50">
                <span class="fw-bold text-dark"><i class="bi bi-paperclip me-2 text-primary"></i>Dokumen Lampiran</span>
            </div>
            <div class="glass-card-body text-center">
                <?php 
                    $fileExt = strtolower(pathinfo($tamu['dokumen_pendukung'], PATHINFO_EXTENSION));
                    $filePath = getUploadUrl($tamu['dokumen_pendukung'], 'file');
                ?>
                <?php if ($fileExt === 'pdf'): ?>
                    <div class="p-4 border rounded bg-light text-center">
                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-1 mb-2 d-block"></i>
                        <span class="d-block mb-3 text-secondary font-monospace"><?= esc($tamu['dokumen_pendukung']) ?></span>
                        <a href="<?= $filePath ?>" target="_blank" class="btn btn-primary rounded-pill px-4"><i class="bi bi-box-arrow-up-right me-1"></i> Buka PDF di Tab Baru</a>
                    </div>
                <?php else: ?>
                    <img src="<?= $filePath ?>" alt="Dokumen Lampiran" class="img-fluid rounded border shadow-sm" style="max-height: 450px;">
                    <div class="mt-3">
                        <a href="<?= $filePath ?>" download class="btn btn-sm btn-outline-primary rounded-pill px-3"><i class="bi bi-download me-1"></i> Unduh Lampiran</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right Side Sidebar: Captures & Status Update -->
    <div class="col-lg-4">
        <!-- Status Changer Card -->
        <div class="glass-card shadow-sm border-0 mb-4">
            <div class="glass-card-header bg-light bg-opacity-50">
                <span class="fw-bold text-dark"><i class="bi bi-gear-fill me-2 text-primary"></i>Kontrol Kunjungan</span>
            </div>
            <div class="glass-card-body">
                <form action="<?= base_url('tamu/update-status/' . encode_id($tamu['id'])) ?>" method="POST" id="statusForm">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="status_kunjungan" class="form-label fw-semibold">Ubah Status Kunjungan</label>
                        <select class="form-select" name="status_kunjungan" id="status_kunjungan" required>
                            <option value="menunggu" <?= $tamu['status_kunjungan'] === 'menunggu' ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                            <option value="berlangsung" <?= $tamu['status_kunjungan'] === 'berlangsung' ? 'selected' : '' ?>>Berlangsung</option>
                            <option value="selesai" <?= $tamu['status_kunjungan'] === 'selesai' ? 'selected' : '' ?>>Selesai (Tamu Pulang)</option>
                            <option value="batal" <?= $tamu['status_kunjungan'] === 'batal' ? 'selected' : '' ?>>Batal</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill"><i class="bi bi-save2 me-1"></i> Perbarui Status</button>
                </form>
            </div>
        </div>

        <!-- Documentation Card (Webcam capture / signature) -->
        <div class="glass-card shadow-sm border-0 mb-4">
            <div class="glass-card-header bg-light bg-opacity-50">
                <span class="fw-bold text-dark"><i class="bi bi-shield-lock-fill me-2 text-primary"></i>Identifikasi Fisik</span>
            </div>
            <div class="glass-card-body p-4">
                <!-- Foto Tamu -->
                <div class="mb-4 text-center">
                    <span class="text-secondary d-block small mb-2 text-start fw-semibold">Foto Selfie / Capture Kamera</span>
                    <?php if ($tamu['foto']): ?>
                        <img src="<?= getUploadUrl($tamu['foto'], 'foto') ?>" alt="Foto Selfie Tamu" class="img-fluid rounded border shadow-sm w-100" style="aspect-ratio: 4/3; object-fit: cover;">
                    <?php else: ?>
                        <div class="d-flex flex-column align-items-center justify-content-center border rounded bg-light" style="aspect-ratio: 4/3;">
                            <i class="bi bi-camera-fill text-muted fs-1 mb-2"></i>
                            <span class="text-secondary small">Foto tidak tersedia</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tanda Tangan -->
                <div class="text-center">
                    <span class="text-secondary d-block small mb-2 text-start fw-semibold">Tanda Tangan Digital</span>
                    <?php if ($tamu['tanda_tangan']): ?>
                        <div class="p-2 border rounded bg-white shadow-sm d-inline-block w-100">
                            <img src="<?= getUploadUrl($tamu['tanda_tangan'], 'ttd') ?>" alt="Tanda Tangan Tamu" class="img-fluid" style="max-height: 120px;">
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column align-items-center justify-content-center border rounded bg-light py-4" style="height: 120px;">
                            <i class="bi bi-vector-pen text-muted fs-1 mb-2"></i>
                            <span class="text-secondary small">Tanda tangan tidak tersedia</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
