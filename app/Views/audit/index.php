<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Audit Log Sistem<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Audit Log Sistem</h2>
        <p class="text-secondary mb-0">Catatan riwayat aktivitas pengguna di dalam sistem Buku Tamu Elektronik.</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="glass-card">
            <div class="glass-card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="auditTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 80px;">No</th>
                                <th>Waktu</th>
                                <th>Pengguna</th>
                                <th>Aktivitas</th>
                                <th>Tabel Terkait</th>
                                <th>ID Record</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($logs as $log): ?>
                            <tr>
                                <td class="ps-4"><?= $no++ ?></td>
                                <td>
                                    <div><?= date('d M Y', strtotime($log['created_at'])) ?></div>
                                    <div class="small text-secondary"><i class="bi bi-clock me-1"></i><?= date('H:i:s', strtotime($log['created_at'])) ?> WIB</div>
                                </td>
                                <td>
                                    <?php if ($log['username']): ?>
                                        <span class="fw-semibold text-indigo">@<?= esc($log['username']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted italic">Publik / Sistem</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-dark fw-medium"><?= esc($log['aktivitas']) ?></td>
                                <td>
                                    <?php if ($log['tabel_terkait']): ?>
                                        <span class="badge bg-secondary-subtle text-secondary border px-3 text-capitalize"><?= esc($log['tabel_terkait']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($log['id_record']): ?>
                                        <span class="font-monospace fw-semibold text-secondary">#<?= esc($log['id_record']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="font-monospace small text-muted"><i class="bi bi-pc-display me-1"></i><?= esc($log['ip_address'] ?: '-') ?></span>
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
        $('#auditTable').DataTable({
            responsive: true,
            order: [[1, 'desc']], // Order by waktu desc
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
