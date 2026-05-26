<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Dashboard</h2>
        <p class="text-secondary mb-0">Selamat datang di Panel E-GuestBook Diskominfo Kabupaten Grobogan.</p>
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

    <!-- Monthly Guests -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass-card stat-widget h-100">
            <div class="glass-card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Tamu Bulan Ini</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0"><?= esc($totalMonth) ?></h3>
                </div>
                <div class="stat-icon bg-success shadow-sm">
                    <i class="bi bi-calendar3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Agendas -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass-card stat-widget h-100">
            <div class="glass-card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Agenda Aktif</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0"><?= esc($totalAgendas) ?></h3>
                </div>
                <div class="stat-icon bg-warning shadow-sm">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Verification -->
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
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <!-- Trend Chart -->
    <div class="<?= $isSuperadmin ? 'col-lg-8' : 'col-12' ?>">
        <div class="glass-card h-100">
            <div class="glass-card-header">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Tren Kunjungan (7 Hari Terakhir)</h5>
            </div>
            <div class="glass-card-body">
                <canvas id="trendChart" style="max-height: 320px;"></canvas>
            </div>
        </div>
    </div>

    <!-- OPD Chart (Superadmin Only) -->
    <?php if ($isSuperadmin): ?>
    <div class="col-lg-4">
        <div class="glass-card h-100">
            <div class="glass-card-header">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Top 5 OPD Terbanyak</h5>
            </div>
            <div class="glass-card-body d-flex align-items-center justify-content-center">
                <?php if (empty($opdLabels)): ?>
                    <div class="text-center text-muted">Tidak ada data untuk ditampilkan</div>
                <?php else: ?>
                    <canvas id="opdChart" style="max-height: 320px;"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Recent Guests List -->
<div class="row">
    <div class="col-12">
        <div class="glass-card">
            <div class="glass-card-header d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-list-stars me-2 text-indigo"></i>Kunjungan Terbaru</h5>
                <a href="<?= base_url('tamu') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
            </div>
            <div class="glass-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No. Referensi</th>
                                <th>Nama Tamu / Asal</th>
                                <th>Keperluan</th>
                                <th>Pegawai &amp; Unit Tujuan</th>
                                <th>Waktu Datang</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentGuests)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-secondary">Belum ada kunjungan tamu hari ini.</td>
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
                                        <div class="text-truncate" style="max-width: 200px;" title="<?= esc($tamu['keperluan']) ?>">
                                            <?= esc($tamu['keperluan']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><i class="bi bi-person-circle me-1"></i><?= esc($tamu['nama_pegawai']) ?></div>
                                        <div class="small text-secondary"><?= esc($tamu['nama_bagian']) ?> - <?= esc($tamu['nama_opd']) ?></div>
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
                                        <a href="<?= base_url('tamu/detail/' . $tamu['id']) ?>" class="btn btn-sm btn-light border btn-icon" title="Detail Kunjungan">
                                            <i class="bi bi-eye"></i> Detail
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
        // 1. Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($trendLabels) ?>,
                datasets: [{
                    label: 'Jumlah Kunjungan',
                    data: <?= json_encode($trendData) ?>,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4f46e5',
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

        // 2. OPD Chart (Superadmin Only)
        <?php if ($isSuperadmin && !empty($opdLabels)): ?>
        const opdCtx = document.getElementById('opdChart').getContext('2d');
        new Chart(opdCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($opdLabels) ?>,
                datasets: [{
                    data: <?= json_encode($opdData) ?>,
                    backgroundColor: ['#4f46e5', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
                    borderRadius: 8,
                    maxBarThickness: 30
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
                        ticks: {
                            color: '#64748b',
                            callback: function(val, index) {
                                // Shorten long labels on bar chart
                                let label = this.getLabelForValue(val);
                                return label.length > 15 ? label.substring(0, 15) + '...' : label;
                            }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>
