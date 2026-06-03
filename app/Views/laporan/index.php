<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Laporan Kunjungan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Laporan &amp; Export</h2>
        <p class="text-secondary">Filter data kunjungan tamu dan ekspor laporan ke format PDF atau Excel.</p>
    </div>
</div>

<!-- Filters Panel -->
<div class="row mb-4">
    <div class="col-12">
        <div class="glass-card shadow-sm border-0">
            <div class="glass-card-header bg-light bg-opacity-50">
                <span class="fw-bold text-dark"><i class="bi bi-funnel-fill me-2 text-primary"></i>Filter Laporan</span>
            </div>
            <div class="glass-card-body">
                <form action="<?= base_url('laporan') ?>" method="GET" id="reportFilterForm" class="row g-3 align-items-end">
                    
                    <div class="col-12">
                        <div class="row g-2">
                            <?php if ($isSuperadmin): ?>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Dinas / OPD</label>
                                <select class="form-select form-select-sm select2-enable" name="kode_opd" id="kode_opd" style="width: 100%">
                                    <option value="">-- Semua OPD --</option>
                                    <?php foreach ($opds as $row): ?>
                                        <option value="<?= esc($row['kode_opd']) ?>" <?= $filters['kode_opd'] === $row['kode_opd'] ? 'selected' : '' ?>><?= esc($row['nama_opd']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php else: ?>
                            <input type="hidden" name="kode_opd" id="kode_opd" value="<?= esc($userKodeOpd) ?>">
                            <?php endif; ?>

                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Bagian / Bidang</label>
                                <select class="form-select form-select-sm select2-enable" name="kode_bagian" id="kode_bagian" style="width: 100%" data-selected="<?= esc($filters['kode_bagian']) ?>">
                                    <option value="">-- Semua Bagian --</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Subbagian / Subbidang</label>
                                <select class="form-select form-select-sm select2-enable" name="kode_subbagian" id="kode_subbagian" style="width: 100%" data-selected="<?= esc($filters['kode_subbagian']) ?>">
                                    <option value="">-- Semua Subbagian --</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="id_agenda" class="form-label small fw-semibold">Agenda Kegiatan</label>
                                <select class="form-select form-select-sm select2-enable" name="id_agenda" id="id_agenda" style="width: 100%;">
                                    <option value="">-- Semua Tamu (Reguler &amp; Agenda) --</option>
                                    <option value="reguler" <?= $filters['id_agenda'] === 'reguler' ? 'selected' : '' ?>>Tamu Reguler (Tanpa Agenda)</option>
                                    <optgroup label="Berdasarkan Agenda">
                                        <?php foreach ($agendas as $a): ?>
                                            <option value="<?= esc($a['id_agenda']) ?>" <?= $filters['id_agenda'] === (string)$a['id_agenda'] ? 'selected' : '' ?>><?= esc($a['nama_agenda']) ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label for="start_date" class="form-label small fw-semibold">Tanggal Mulai</label>
                        <input type="text" class="form-control form-control-sm date-picker" id="start_date" name="start_date" placeholder="Pilih Tanggal" value="<?= esc($filters['start_date']) ?>">
                    </div>

                    <div class="col-md-2">
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
                        <select class="form-select form-select-sm select2-enable" name="pegawai_id" id="pegawai_id" style="width: 100%;" data-selected="<?= esc($filters['pegawai_id']) ?>">
                            <option value="">-- Semua Pegawai --</option>
                            <?php foreach ($pegawais as $p): ?>
                                <option value="<?= esc($p['id']) ?>" <?= (int)$filters['pegawai_id'] === (int)$p['id'] ? 'selected' : '' ?>><?= esc($p['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill flex-fill py-2" title="Filter"><i class="bi bi-search me-1"></i> Cari</button>
                        <a href="<?= base_url('laporan') ?>" class="btn btn-sm btn-light border rounded-pill flex-fill py-2" title="Reset"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Preview Table & Export Actions -->
<div class="row">
    <div class="col-12">
        <div class="glass-card">
            <div class="glass-card-header d-flex justify-content-between align-items-center bg-light bg-opacity-50">
                <span class="fw-bold text-dark"><i class="bi bi-eye-fill me-2 text-indigo"></i>Pratinjau Laporan (<?= count($tamus) ?> Kunjungan)</span>
                
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-danger rounded-pill px-3" onclick="exportReport('pdf')">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF
                    </button>
                    <button class="btn btn-sm btn-success rounded-pill px-3" onclick="exportReport('excel')">
                        <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Ekspor Excel
                    </button>
                </div>
            </div>
            
            <div class="glass-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="reportPreviewTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 50px;">No</th>
                                <th>No. Referensi</th>
                                <th>Nama Tamu / Asal</th>
                                <th>Waktu Kunjungan</th>
                                <th>Keperluan</th>
                                <th>Pegawai Tujuan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($tamus as $t): ?>
                            <tr>
                                <td class="ps-4"><?= $no++ ?></td>
                                <td><span class="font-monospace fw-semibold text-primary">#<?= esc($t['no_referensi']) ?></span></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($t['nama_tamu']) ?></div>
                                    <div class="small text-secondary"><?= esc($t['instansi']) ?></div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">D: <?= date('d M Y, H:i', strtotime($t['waktu_datang'])) ?></div>
                                    <div class="small text-secondary">P: <?= $t['waktu_pulang'] ? date('d M Y, H:i', strtotime($t['waktu_pulang'])) : '-' ?></div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="<?php if (!empty($t['id_agenda']) && !empty($t['nama_agenda'])): ?>Menghadiri <?= esc($t['nama_agenda']) ?><?php else: ?><?= esc($t['keperluan'] ?? '-') ?><?php endif; ?>">
                                        <?php if (!empty($t['id_agenda']) && !empty($t['nama_agenda'])): ?>
                                            <i class="bi bi-calendar-event me-1"></i>Menghadiri <?= esc($t['nama_agenda']) ?>
                                        <?php else: ?>
                                            <?= esc($t['keperluan'] ?? '-') ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark"><?= esc($t['nama_pegawai'] ?? 'Tamu Agenda') ?></div>
                                    <?php if ($isSuperadmin): ?>
                                        <div class="text-muted small" style="font-size: 0.7rem;"><?= esc($t['nama_opd']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge rounded-pill badge-<?= esc($t['status_kunjungan']) ?> px-3 py-1 text-capitalize">
                                        <?= esc($t['status_kunjungan']) ?>
                                    </span>
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

        $('#reportPreviewTable').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                zeroRecords: "Tidak ada data kunjungan yang cocok dengan filter yang dipilih.",
                emptyTable: "Tidak ada data kunjungan yang cocok dengan filter yang dipilih.",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Lanjut",
                    previous: "Kembali"
                }
            }
        });

        // Cascading Filters
        let initialBagian = $('#kode_bagian').data('selected');
        let initialSubbagian = $('#kode_subbagian').data('selected');
        let initialPegawai = $('#pegawai_id').data('selected');

        function loadBagian(kodeOpd, selectedValue) {
            let bagianSelect = $('#kode_bagian');
            let subbagianSelect = $('#kode_subbagian');
            
            bagianSelect.empty().append('<option value="">-- Semua Bagian --</option>');
            subbagianSelect.empty().append('<option value="">-- Semua Subbagian --</option>');
            
            if (!kodeOpd) return;

            $.ajax({
                url: '<?= base_url("api/bagian") ?>/' + kodeOpd,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.length > 0) {
                        $.each(data, function(key, val) {
                            let selected = (selectedValue == val.kode_bagian) ? 'selected' : '';
                            bagianSelect.append('<option value="' + val.kode_bagian + '" ' + selected + '>' + val.nama_bagian + '</option>');
                        });
                    }
                    if (selectedValue) {
                        loadSubbagian(kodeOpd, selectedValue, initialSubbagian);
                    }
                }
            });
        }

        function loadSubbagian(kodeOpd, kodeBagian, selectedValue) {
            let subbagianSelect = $('#kode_subbagian');
            subbagianSelect.empty().append('<option value="">-- Semua Subbagian --</option>');
            
            if (!kodeOpd || !kodeBagian) return;

            $.ajax({
                url: '<?= base_url("api/subbagian") ?>/' + kodeOpd + '/' + kodeBagian,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.length > 0) {
                        $.each(data, function(key, val) {
                            let selected = (selectedValue == val.kode_subbagian) ? 'selected' : '';
                            subbagianSelect.append('<option value="' + val.kode_subbagian + '" ' + selected + '>' + val.nama_subbagian + '</option>');
                        });
                    }
                }
            });
        }

        function loadPegawai(kodeOpd, kodeBagian, kodeSubbagian, selectedValue) {
            let pegawaiSelect = $('#pegawai_id');
            if (!kodeOpd) return; 

            let url = '<?= base_url("api/pegawai") ?>/' + kodeOpd;
            if (kodeBagian) {
                url += '/' + kodeBagian;
                if (kodeSubbagian) {
                    url += '/' + kodeSubbagian;
                }
            }

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    pegawaiSelect.empty().append('<option value="">-- Semua Pegawai --</option>');
                    if (data.length > 0) {
                        $.each(data, function(key, val) {
                            let selected = (selectedValue == val.id) ? 'selected' : '';
                            pegawaiSelect.append('<option value="' + val.id + '" ' + selected + '>' + val.nama + '</option>');
                        });
                    }
                }
            });
        }

        $('#kode_opd').on('change', function() {
            let kodeOpd = $(this).val();
            loadBagian(kodeOpd, '');
            loadPegawai(kodeOpd, '', '', '');
        });

        $('#kode_bagian').on('change', function() {
            let kodeOpd = $('#kode_opd').val();
            let kodeBagian = $(this).val();
            loadSubbagian(kodeOpd, kodeBagian, '');
            loadPegawai(kodeOpd, kodeBagian, '', '');
        });

        $('#kode_subbagian').on('change', function() {
            let kodeOpd = $('#kode_opd').val();
            let kodeBagian = $('#kode_bagian').val();
            let kodeSubbagian = $(this).val();
            loadPegawai(kodeOpd, kodeBagian, kodeSubbagian, '');
        });

        // Initial Load
        if ($('#kode_opd').val()) {
            loadBagian($('#kode_opd').val(), initialBagian);
        }
    });

    function exportReport(format) {
        // Grab current filters
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        const status = $('#status').val();
        const pegawaiId = $('#pegawai_id').val();
        const idAgenda = $('#id_agenda').val();

        // Construct query string
        let queryParams = `?start_date=${startDate}&end_date=${endDate}&status=${status}&pegawai_id=${pegawaiId}&id_agenda=${idAgenda}`;
        
        let exportUrl = '<?= base_url("laporan") ?>/' + format + queryParams;
        window.open(exportUrl, '_blank');
    }
</script>
<?= $this->endSection() ?>
