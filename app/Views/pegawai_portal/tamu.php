<?= $this->extend('layouts/pegawai') ?>

<?= $this->section('title') ?>Daftar Tamu<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="fw-bold text-dark mb-1">Daftar Tamu</h2>
            <p class="text-secondary mb-0">Daftar tamu yang ditujukan kepada Anda dan tamu OPD yang belum memiliki pegawai tujuan.</p>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="glass-card mb-4">
    <div class="glass-card-body py-2 px-3">
        <ul class="nav nav-pills gap-2 flex-nowrap overflow-auto" style="white-space: nowrap;">
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 <?= $status === 'menunggu' ? 'active' : '' ?>" 
                   href="<?= base_url('pegawai-portal/tamu?status=menunggu') ?>">
                    <i class="bi bi-clock-history me-1"></i> Menunggu Verifikasi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 <?= $status === 'berlangsung' ? 'active' : '' ?>" 
                   href="<?= base_url('pegawai-portal/tamu?status=berlangsung') ?>">
                    <i class="bi bi-hourglass-split me-1"></i> Berlangsung
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 <?= $status === 'selesai' ? 'active' : '' ?>" 
                   href="<?= base_url('pegawai-portal/tamu?status=selesai') ?>">
                    <i class="bi bi-check-circle me-1"></i> Selesai
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 <?= $status === 'semua' ? 'active' : '' ?>" 
                   href="<?= base_url('pegawai-portal/tamu?status=semua') ?>">
                    <i class="bi bi-list-ul me-1"></i> Semua
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Tamu Table -->
<div class="glass-card">
    <div class="glass-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tamuTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No. Referensi</th>
                        <th>Nama Tamu</th>
                        <th>Instansi</th>
                        <th>Waktu Datang</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tamus)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-secondary">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                            <div class="fw-semibold">Tidak ada data tamu</div>
                            <div class="small">dengan status "<?= esc($status) ?>"</div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($tamus as $tamu): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="font-monospace fw-semibold text-primary">#<?= esc($tamu['no_referensi']) ?></span>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= esc($tamu['nama_tamu']) ?></div>
                                <div class="small text-secondary"><?= esc($tamu['no_hp'] ?? '-') ?></div>
                                <?php if (empty($tamu['id_pegawai_tujuan'])): ?>
                                    <span class="badge rounded-pill bg-info-subtle text-info-emphasis mt-1">Belum ada pegawai tujuan</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($tamu['instansi']) ?></td>
                            <td>
                                <div><?= date('d M Y', strtotime($tamu['waktu_datang'])) ?></div>
                                <div class="small text-secondary"><i class="bi bi-clock me-1"></i><?= date('H:i', strtotime($tamu['waktu_datang'])) ?> WIB</div>
                            </td>
                            <td>
                                <span class="badge rounded-pill badge-<?= esc($tamu['status_kunjungan']) ?> px-3 py-1 text-capitalize">
                                    <?= esc($tamu['status_kunjungan']) ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex gap-1 justify-content-end">
                                    <?php if ($tamu['status_kunjungan'] === 'menunggu'): ?>
                                        <form action="<?= base_url('pegawai-portal/tamu/konfirmasi/' . $tamu['id']) ?>" method="POST" class="d-inline confirm-form">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">
                                                <i class="bi bi-check-lg me-1"></i> <?= empty($tamu['id_pegawai_tujuan']) ? 'Ambil & Terima' : 'Terima' ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($tamu['status_kunjungan'] === 'berlangsung'): ?>
                                        <form action="<?= base_url('pegawai-portal/tamu/update-status/' . $tamu['id']) ?>" method="POST" class="d-inline selesai-form">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status_kunjungan" value="selesai">
                                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="bi bi-check-circle me-1"></i> Selesai
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <a href="<?= base_url('pegawai-portal/tamu/detail/' . $tamu['id']) ?>" class="btn btn-sm btn-light border rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        <?php if (!empty($tamus)): ?>
        $('#tamuTable').DataTable({
            responsive: true,
            order: [[3, 'desc']],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                emptyTable: "Tidak ada data tamu",
                paginate: { first: "Awal", last: "Akhir", next: "&raquo;", previous: "&laquo;" }
            }
        });
        <?php endif; ?>

        // SweetAlert confirmation for Terima
        $('.confirm-form').on('submit', function(e) {
            e.preventDefault();
            let form = this;
            Swal.fire({
                title: 'Konfirmasi Tamu?',
                text: 'Tamu akan diterima dan status berubah menjadi "Berlangsung".',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="bi bi-check-lg me-1"></i> Ya, Terima',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });

        // SweetAlert confirmation for Selesai
        $('.selesai-form').on('submit', function(e) {
            e.preventDefault();
            let form = this;
            Swal.fire({
                title: 'Selesaikan Kunjungan?',
                text: 'Status tamu akan berubah menjadi "Selesai" dan waktu pulang akan dicatat.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Ya, Selesai',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
</script>
<?= $this->endSection() ?>
