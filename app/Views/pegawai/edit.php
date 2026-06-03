<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Ubah Pegawai<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Ubah Pegawai</h2>
        <p class="text-secondary">Perbarui data pegawai target kunjungan.</p>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="glass-card">
            <div class="glass-card-body">
                <form action="<?= base_url('pegawai/update/' . encode_id($pegawai['id'])) ?>" method="POST" id="pegawaiForm">
                    <?= csrf_field() ?>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nip" class="form-label fw-semibold">NIP Pegawai (18 digit)</label>
                            <input type="text" class="form-control font-monospace" id="nip" name="nip" placeholder="Contoh: 199001012015011001" required value="<?= old('nip', $pegawai['nip']) ?>">
                            <div class="small text-muted mt-1">Harus berupa 18 digit angka NIP resmi.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="nama" class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Lengkap dengan Gelar" required value="<?= old('nama', $pegawai['nama']) ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="jabatan" class="form-label fw-semibold">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Contoh: Kepala Bidang TIK" required value="<?= old('jabatan', $pegawai['jabatan']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">Status Pegawai</label>
                            <select class="form-select" name="status" id="status" required>
                                <option value="aktif" <?= old('status', $pegawai['status']) === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= old('status', $pegawai['status']) === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="fw-bold text-indigo mt-4 mb-3 border-bottom pb-2">Unit Kerja</h5>

                    <div class="mb-3">
                        <?php if ($isSuperadmin): ?>
                            <label for="kode_opd" class="form-label fw-semibold">Dinas / OPD</label>
                            <select class="form-select select2-enable" name="kode_opd" id="kode_opd" required style="width: 100%">
                                <option value="">-- Pilih OPD --</option>
                                <?php foreach ($opds as $row): ?>
                                    <option value="<?= esc($row['kode_opd']) ?>" <?= old('kode_opd', $pegawai['kode_opd']) === $row['kode_opd'] ? 'selected' : '' ?>>[<?= esc($row['kode_opd']) ?>] <?= esc($row['nama_opd']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <label class="form-label fw-semibold">Dinas / OPD</label>
                            <input type="text" class="form-control bg-light" value="<?= esc($opd['nama_opd']) ?>" readonly disabled>
                            <input type="hidden" name="kode_opd" id="kode_opd" value="<?= esc($opd['kode_opd']) ?>">
                        <?php endif; ?>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="kode_bagian" class="form-label fw-semibold">Bagian / Bidang</label>
                            <select class="form-select select2-enable" name="kode_bagian" id="kode_bagian" required style="width: 100%">
                                <option value="">-- Pilih Bagian --</option>
                                <?php foreach ($bagians as $row): ?>
                                    <option value="<?= esc($row['kode_bagian']) ?>" <?= old('kode_bagian', $pegawai['kode_bagian']) === $row['kode_bagian'] ? 'selected' : '' ?>><?= esc($row['nama_bagian']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="kode_subbagian" class="form-label fw-semibold">Subbagian / Seksi (Opsional)</label>
                            <select class="form-select select2-enable" name="kode_subbagian" id="kode_subbagian" style="width: 100%" <?= !empty($subbagians) ? '' : 'disabled' ?>>
                                <option value="">-- Pilih Subbagian --</option>
                                <?php foreach ($subbagians as $row): ?>
                                    <option value="<?= esc($row['kode_subbagian']) ?>" <?= old('kode_subbagian', $pegawai['kode_subbagian']) === $row['kode_subbagian'] ? 'selected' : '' ?>><?= esc($row['nama_subbagian']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="<?= base_url('pegawai') ?>" class="btn btn-light rounded-pill px-4">Kembali</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Apply inputmask to NIP (18 digits)
        Inputmask({ mask: "999999999999999999", placeholder: "" }).mask(document.getElementById("nip"));

        // Initialize Select2
        $('.select2-enable').select2({
            theme: 'bootstrap-5'
        });

        // Cascading Dropdown (OPD -> Bagian)
        $('#kode_opd').on('change', function() {
            let kodeOpd = $(this).val();
            let bagianSelect = $('#kode_bagian');
            let subSelect = $('#kode_subbagian');
            
            // Skip clean if it's the first initialization load
            if ($(this).data('initialized')) {
                bagianSelect.empty().append('<option value="">-- Pilih Bagian --</option>').trigger('change');
                subSelect.empty().append('<option value="">-- Pilih Subbagian --</option>').prop('disabled', true).trigger('change');
            } else {
                $(this).data('initialized', true);
                return; // Keep existing preselected Bagian
            }
            
            if (kodeOpd) {
                bagianSelect.prop('disabled', true);
                
                $.ajax({
                    url: '<?= base_url("api/bagian") ?>/' + kodeOpd,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.length > 0) {
                            $.each(data, function(key, val) {
                                bagianSelect.append('<option value="' + val.kode_bagian + '">' + val.nama_bagian + '</option>');
                            });
                            bagianSelect.prop('disabled', false);
                        } else {
                            bagianSelect.prop('disabled', true);
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
            let subSelect = $('#kode_subbagian');
            
            // Skip clean if it's the first initialization load
            if ($(this).data('initialized')) {
                subSelect.empty().append('<option value="">-- Pilih Subbagian --</option>').trigger('change');
            } else {
                $(this).data('initialized', true);
                return; // Keep existing preselected Subbagian
            }
            
            if (kodeOpd && kodeBagian) {
                subSelect.prop('disabled', true);
                
                $.ajax({
                    url: '<?= base_url("api/subbagian") ?>/' + kodeOpd + '/' + kodeBagian,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.length > 0) {
                            $.each(data, function(key, val) {
                                subSelect.append('<option value="' + val.kode_subbagian + '">' + val.nama_subbagian + '</option>');
                            });
                            subSelect.prop('disabled', false);
                        } else {
                            subSelect.prop('disabled', true);
                        }
                    },
                    error: function() {
                        showAppToast('error', 'Gagal mengambil data Subbagian.');
                    }
                });
            } else {
                subSelect.prop('disabled', true);
            }
        });

        // Set initialized values to prevent clean on first focus
        $('#kode_opd').data('initialized', false);
        $('#kode_bagian').data('initialized', false);
    });
</script>
<?= $this->endSection() ?>
