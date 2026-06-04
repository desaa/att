<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>QR Code Tamu Umum<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-sm-6">
        <h2 class="fw-bold text-dark">QR Code Tamu Umum</h2>
        <p class="text-secondary mb-0">Tampilkan atau cetak QR Code registrasi mandiri untuk tamu umum non-agenda.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Selector Panel -->
    <div class="col-md-5">
        <div class="glass-card mb-4">
            <div class="glass-card-header bg-light">
                <span class="fw-bold text-dark"><i class="bi bi-funnel-fill me-2 text-primary"></i>Pilih Unit Kerja</span>
            </div>
            <div class="glass-card-body">
                <form action="<?= base_url('tamu/qr-umum') ?>" method="GET">
                    <div class="mb-3">
                        <?php if ($isSuperadmin): ?>
                            <label for="kode_opd" class="form-label fw-semibold">Dinas / OPD</label>
                            <select class="form-select select2-enable" name="kode_opd" id="kode_opd" required>
                                <option value="">-- Pilih OPD --</option>
                                <?php foreach ($opds as $row): ?>
                                    <option value="<?= esc($row['kode_opd']) ?>" <?= $selected_opd === $row['kode_opd'] ? 'selected' : '' ?>>[<?= esc($row['kode_opd']) ?>] <?= esc($row['nama_opd']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <label class="form-label fw-semibold">Dinas / OPD</label>
                            <input type="text" class="form-control bg-light" value="<?= esc($opd['nama_opd']) ?>" readonly disabled>
                            <input type="hidden" name="kode_opd" id="kode_opd" value="<?= esc($opd['kode_opd']) ?>">
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="kode_bagian" class="form-label fw-semibold">Bagian / Bidang (Opsional)</label>
                        <select class="form-select select2-enable" name="kode_bagian" id="kode_bagian" <?= empty($bagians) && $isSuperadmin ? 'disabled' : '' ?>>
                            <option value="">-- Semua Bagian --</option>
                            <?php foreach ($bagians as $row): ?>
                                <option value="<?= esc($row['kode_bagian']) ?>" <?= $selected_bagian === $row['kode_bagian'] ? 'selected' : '' ?>><?= esc($row['nama_bagian']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="kode_subbagian" class="form-label fw-semibold">Subbagian / Subbidang (Opsional)</label>
                        <select class="form-select select2-enable" name="kode_subbagian" id="kode_subbagian" <?= empty($subbagians) ? 'disabled' : '' ?>>
                            <option value="">-- Semua Subbagian --</option>
                            <?php foreach ($subbagians as $row): ?>
                                <option value="<?= esc($row['kode_subbagian']) ?>" <?= $selected_subbagian === $row['kode_subbagian'] ? 'selected' : '' ?>><?= esc($row['nama_subbagian']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-2">
                        <i class="bi bi-qr-code me-2"></i>Tampilkan QR Code
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- QR Code Display Panel -->
    <div class="col-md-7">
        <div class="glass-card text-center h-100">
            <div class="glass-card-body p-4 p-md-5">
                <?php if (isset($qr_image)): ?>
                    <div id="printArea" class="p-4 bg-white border rounded shadow-sm d-inline-block">
                        <div class="mb-2">
                            <img src="<?= base_url('assets/app-logo/logotextbiru.png') ?>" alt="e-AdaTamu Logo" style="height: 40px; width: auto; object-fit: contain;">
                        </div>
                        <h5 class="fw-bold text-secondary mb-2">Pendaftaran Tamu Umum</h5>
                        
                        <hr class="my-3" style="border-top: 2px dashed #cbd5e1;">
                        
                        <div class="mb-2">
                            <?php if (!empty($nama_subbagian)): ?>
                                <span class="badge bg-indigo-subtle text-indigo px-3 py-2 rounded-pill font-monospace mb-1" style="font-size: 0.85rem;">
                                    <?= esc($nama_subbagian) ?>
                                </span><br>
                            <?php endif; ?>
                            <?php if (!empty($nama_bagian)): ?>
                                <span class="badge bg-indigo-subtle text-indigo px-3 py-2 rounded-pill font-monospace" style="font-size: 0.85rem;">
                                    <?= esc($nama_bagian) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="fw-semibold text-muted small mb-4"><?= esc($nama_opd) ?></div>
                        
                        <img src="<?= esc($qr_image) ?>" alt="QR Code" class="img-fluid mb-4" style="width: 260px; height: 260px;">
                        
                        <p class="text-dark small mb-0 fw-bold"><i class="bi bi-phone-vibrate me-1"></i> SCAN UNTUK DAFTAR</p>
                        <p class="text-muted mb-0" style="font-size: 0.725rem;">Isi buku tamu mandiri melalui handphone Anda</p>
                    </div>

                    <div class="mt-4 text-start">
                        <label class="form-label small fw-semibold text-secondary">Tautan Registrasi (URL):</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control font-monospace bg-light" id="qrUrl" value="<?= esc($qr_url) ?>" readonly>
                            <button class="btn btn-outline-primary" onclick="copyUrl()"><i class="bi bi-copy"></i> Salin</button>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-outline-primary rounded-pill px-4" onclick="printQr()">
                            <i class="bi bi-printer-fill me-2"></i>Cetak QR Code
                        </button>
                    </div>
                <?php else: ?>
                    <div class="py-5 text-secondary">
                        <i class="bi bi-qr-code fs-1 d-block mb-3 text-muted"></i>
                        <p class="mb-0">Silakan pilih OPD terlebih dahulu untuk menampilkan QR Code Tamu Umum.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize select2
        $('.select2-enable').select2({
            theme: 'bootstrap-5'
        });

        // Cascading Dropdown (OPD -> Bagian)
        $('#kode_opd').on('change', function() {
            let kodeOpd = $(this).val();
            let bagianSelect = $('#kode_bagian');
            let subbagianSelect = $('#kode_subbagian');
            
            bagianSelect.empty().append('<option value="">-- Semua Bagian --</option>').trigger('change');
            subbagianSelect.empty().append('<option value="">-- Semua Subbagian --</option>').prop('disabled', true).trigger('change');
            
            if (kodeOpd) {
                bagianSelect.prop('disabled', true);
                $.ajax({
                    url: '<?= base_url("api/bagian") ?>/' + kodeOpd,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        bagianSelect.prop('disabled', false);
                        if (data.length > 0) {
                            $.each(data, function(key, val) {
                                bagianSelect.append('<option value="' + val.kode_bagian + '">' + val.nama_bagian + '</option>');
                            });
                        }
                    },
                    error: function() {
                        showAppToast('error', 'Gagal mengambil data Bagian.');
                    }
                });
            } else {
                bagianSelect.prop('disabled', true);
            }
        });

        // Cascading Dropdown (Bagian -> Subbagian)
        $('#kode_bagian').on('change', function() {
            let kodeOpd = $('#kode_opd').val();
            let kodeBagian = $(this).val();
            let subbagianSelect = $('#kode_subbagian');
            
            subbagianSelect.empty().append('<option value="">-- Semua Subbagian --</option>').trigger('change');
            
            if (kodeOpd && kodeBagian) {
                subbagianSelect.prop('disabled', true);
                $.ajax({
                    url: '<?= base_url("api/subbagian") ?>/' + kodeOpd + '/' + kodeBagian,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        subbagianSelect.prop('disabled', false);
                        if (data.length > 0) {
                            $.each(data, function(key, val) {
                                subbagianSelect.append('<option value="' + val.kode_subbagian + '">' + val.nama_subbagian + '</option>');
                            });
                        }
                    },
                    error: function() {
                        showAppToast('error', 'Gagal mengambil data Subbagian.');
                    }
                });
            } else {
                subbagianSelect.prop('disabled', true);
            }
        });
    });

    function copyUrl() {
        const urlInput = document.getElementById("qrUrl");
        urlInput.select();
        urlInput.setSelectionRange(0, 99999);
        
        navigator.clipboard.writeText(urlInput.value).then(() => {
            showAppToast('success', 'Tautan registrasi berhasil disalin!');
        }, () => {
            showAppToast('error', 'Gagal menyalin tautan.');
        });
    }

    function printQr() {
        const printContent = document.getElementById('printArea').innerHTML;
        
        const printWindow = window.open('', '_blank', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Cetak QR Code Tamu Umum</title>');
        printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
        printWindow.document.write('<style>body{display:flex;align-items:center;justify-content:center;height:100vh;text-align:center;} #printArea{border:none !important; shadow:none !important;}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<div id="printArea" class="text-center">' + printContent + '</div>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 500);
    }
</script>
<?= $this->endSection() ?>
