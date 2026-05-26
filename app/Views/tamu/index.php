<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Data Tamu<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-sm-6">
        <h2 class="fw-bold text-dark">Daftar Buku Tamu</h2>
        <p class="text-secondary mb-0">Kelola dan pantau kunjungan tamu di unit kerja Anda.</p>
    </div>
    <?php if (!$isSuperadmin): ?>
    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
        <a href="<?= base_url('tamu/input') ?>" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-person-plus-fill me-2"></i>Input Manual
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Filters Panel -->
<div class="row mb-4">
    <div class="col-12">
        <div class="glass-card shadow-sm border-0">
            <div class="glass-card-header bg-light bg-opacity-50">
                <span class="fw-bold text-dark"><i class="bi bi-funnel-fill me-2 text-primary"></i>Filter Pencarian</span>
            </div>
            <div class="glass-card-body">
                <form action="<?= base_url('tamu') ?>" method="GET" class="row g-3 align-items-end">
                    
                    <div class="col-md-3">
                        <label for="start_date" class="form-label small fw-semibold">Tanggal Mulai</label>
                        <input type="text" class="form-control form-control-sm date-picker" id="start_date" name="start_date" placeholder="Pilih Tanggal" value="<?= esc($filters['start_date']) ?>">
                    </div>

                    <div class="col-md-3">
                        <label for="end_date" class="form-label small fw-semibold">Tanggal Selesai</label>
                        <input type="text" class="form-control form-control-sm date-picker" id="end_date" name="end_date" placeholder="Pilih Tanggal" value="<?= esc($filters['end_date']) ?>">
                    </div>

                    <div class="col-md-2">
                        <label for="status" class="form-label small fw-semibold">Status Kunjungan</label>
                        <select class="form-select form-select-sm" name="status" id="status">
                            <option value="">-- Semua --</option>
                            <option value="menunggu" <?= $filters['status'] === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                            <option value="berlangsung" <?= $filters['status'] === 'berlangsung' ? 'selected' : '' ?>>Berlangsung</option>
                            <option value="selesai" <?= $filters['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option value="batal" <?= $filters['status'] === 'batal' ? 'selected' : '' ?>>Batal</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="pegawai_id" class="form-label small fw-semibold">Pegawai Tujuan</label>
                        <select class="form-select form-select-sm select2-enable" name="pegawai_id" id="pegawai_id" style="width: 100%;">
                            <option value="">-- Semua Pegawai --</option>
                            <?php foreach ($pegawais as $p): ?>
                                <option value="<?= esc($p['id']) ?>" <?= (int)$filters['pegawai_id'] === (int)$p['id'] ? 'selected' : '' ?>><?= esc($p['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-1 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill flex-fill py-2" title="Cari"><i class="bi bi-search"></i></button>
                        <a href="<?= base_url('tamu') ?>" class="btn btn-sm btn-light border rounded-pill flex-fill py-2" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Table Panel -->
<div class="row">
    <div class="col-12">
        <div class="glass-card">
            <div class="glass-card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="guestbookTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No. Referensi</th>
                                <th>Nama Tamu / Asal</th>
                                <th>Waktu Kunjungan</th>
                                <th>Keperluan</th>
                                <th>Target &amp; Unit Tujuan</th>
                                <th>Agenda</th>
                                <th>Status</th>
                                <th class="text-end pe-4" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tamus as $tamu): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="font-monospace fw-semibold text-primary">#<?= esc($tamu['no_referensi']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($tamu['nama_tamu']) ?></div>
                                    <div class="small text-secondary"><?= esc($tamu['instansi']) ?></div>
                                    <div class="text-muted small" style="font-size: 0.725rem;"><i class="bi bi-telephone-fill me-1"></i><?= esc($tamu['no_hp']) ?></div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark"><i class="bi bi-arrow-login me-1"></i>Datang: <?= date('d M Y, H:i', strtotime($tamu['waktu_datang'])) ?></div>
                                    <div class="small text-secondary">
                                        <i class="bi bi-arrow-logout me-1"></i>Pulang: 
                                        <?= $tamu['waktu_pulang'] ? date('d M Y, H:i', strtotime($tamu['waktu_pulang'])) : '-' ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 180px;" title="<?= esc($tamu['keperluan']) ?>">
                                        <?= esc($tamu['keperluan']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark"><i class="bi bi-person-circle me-1 text-secondary"></i><?= esc($tamu['nama_pegawai']) ?></div>
                                    <div class="small text-secondary"><?= esc($tamu['nama_bagian']) ?></div>
                                    <?php if ($isSuperadmin): ?>
                                        <div class="text-muted small" style="font-size: 0.725rem;"><?= esc($tamu['nama_opd']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($tamu['nama_agenda']): ?>
                                        <span class="badge bg-indigo-subtle text-indigo small border border-indigo-subtle text-truncate" style="max-width: 120px;" title="<?= esc($tamu['nama_agenda']) ?>">
                                            <i class="bi bi-calendar-event me-1"></i><?= esc($tamu['nama_agenda']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small italic">Manual</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge rounded-pill badge-<?= esc($tamu['status_kunjungan']) ?> px-3 py-1 text-capitalize">
                                        <?= esc($tamu['status_kunjungan']) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= base_url('tamu/detail/' . $tamu['id']) ?>" class="btn btn-sm btn-light border btn-icon" title="Detail Kunjungan">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize date picker
        flatpickr(".date-picker", {
            dateFormat: "Y-m-d"
        });

        // Initialize Select2
        $('.select2-enable').select2({
            theme: 'bootstrap-5'
        });

        $('#guestbookTable').DataTable({
            responsive: true,
            order: [[2, 'desc']], // Order by waktu datang desc
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Lanjut",
                    previous: "Kembali"
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
