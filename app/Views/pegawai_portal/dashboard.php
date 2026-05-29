<?= $this->extend('layouts/pegawai') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Dashboard Pegawai</h2>
        <p class="text-secondary mb-0">Selamat datang, <strong><?= esc(session()->get('pegawai_nama')) ?></strong>. Berikut ringkasan kunjungan tamu untuk Anda.</p>
    </div>
</div>

<!-- Stats row -->
<div class="row g-4 mb-4">
    <!-- Today's Guests -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass-card stat-widget h-100">
            <div class="glass-card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Tamu Hari Ini</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0"><?= esc($totalToday) ?></h3>
                </div>
                <div class="stat-icon bg-primary shadow-sm">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass-card stat-widget h-100">
            <div class="glass-card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Menunggu Verifikasi</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0"><?= esc($totalPending) ?></h3>
                </div>
                <div class="stat-icon bg-danger shadow-sm">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Ongoing -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass-card stat-widget h-100">
            <div class="glass-card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Sedang Berlangsung</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0"><?= esc($totalOngoing) ?></h3>
                </div>
                <div class="stat-icon bg-warning shadow-sm">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed This Month -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass-card stat-widget h-100">
            <div class="glass-card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Selesai (Bulan Ini)</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0"><?= esc($totalCompleted) ?></h3>
                </div>
                <div class="stat-icon bg-success shadow-sm">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="glass-card h-100">
            <div class="glass-card-header">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Tren Kunjungan Tamu (7 Hari Terakhir)</h5>
            </div>
            <div class="glass-card-body">
                <canvas id="trendChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Active Guests -->
<div class="row">
    <div class="col-12">
        <div class="glass-card">
            <div class="glass-card-header d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-list-stars me-2 text-indigo"></i>Tamu Aktif (Menunggu & Berlangsung)</h5>
                <a href="<?= base_url('pegawai-portal/tamu?status=menunggu') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
            </div>
            <div class="glass-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No. Referensi</th>
                                <th>Nama Tamu / Asal</th>
                                <th>Waktu Datang</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentGuests)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-secondary">
                                    <i class="bi bi-emoji-smile fs-3 d-block mb-2"></i>
                                    Tidak ada tamu yang menunggu saat ini.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($recentGuests as $tamu): ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="font-monospace fw-semibold text-primary">#<?= esc($tamu['no_referensi']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?= esc($tamu['nama_tamu']) ?></div>
                                        <div class="small text-secondary"><?= esc($tamu['instansi']) ?></div>
                                    </td>
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
                                        <?php if ($tamu['status_kunjungan'] === 'menunggu'): ?>
                                            <form action="<?= base_url('pegawai-portal/tamu/konfirmasi/' . $tamu['id']) ?>" method="POST" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-success btn-icon" title="Konfirmasi" onclick="return confirm('Konfirmasi tamu ini?')">
                                                    <i class="bi bi-check-lg"></i> Terima
                                                </button>
                                            </form>
                                        <?php elseif ($tamu['status_kunjungan'] === 'berlangsung'): ?>
                                            <form action="<?= base_url('pegawai-portal/tamu/update-status/' . $tamu['id']) ?>" method="POST" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="status_kunjungan" value="selesai">
                                                <button type="submit" class="btn btn-sm btn-outline-primary btn-icon" title="Selesaikan" onclick="return confirm('Selesaikan kunjungan tamu ini?')">
                                                    <i class="bi bi-check-circle"></i> Selesai
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="<?= base_url('pegawai-portal/tamu/detail/' . $tamu['id']) ?>" class="btn btn-sm btn-light border btn-icon" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
    document.addEventListener("DOMContentLoaded", function() {
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($trendLabels) ?>,
                datasets: [{
                    label: 'Jumlah Kunjungan',
                    data: <?= json_encode($trendData) ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#64748b' },
                        grid: { borderDash: [5, 5], color: '#e2e8f0' }
                    },
                    x: {
                        ticks: { color: '#64748b' },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
