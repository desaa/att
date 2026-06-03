<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Ubah Agenda<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Ubah Agenda</h2>
        <p class="text-secondary">Perbarui data agenda kegiatan Anda.</p>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="glass-card">
            <div class="glass-card-body">
                <form action="<?= base_url('agenda/update/' . encode_id($agenda['id_agenda'])) ?>" method="POST" id="agendaForm">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="nama_agenda" class="form-label fw-semibold">Nama Kegiatan / Agenda</label>
                        <input type="text" class="form-control" id="nama_agenda" name="nama_agenda" placeholder="Contoh: Rapat Koordinasi IT Grobogan" required value="<?= old('nama_agenda', $agenda['nama_agenda']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label fw-semibold">Deskripsi Kegiatan</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan secara singkat mengenai kegiatan ini..."><?= old('deskripsi', $agenda['deskripsi']) ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggal_mulai" class="form-label fw-semibold">Waktu Mulai</label>
                            <input type="text" class="form-control bg-white" id="tanggal_mulai" name="tanggal_mulai" required value="<?= old('tanggal_mulai', substr($agenda['tanggal_mulai'], 0, 16)) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_selesai" class="form-label fw-semibold">Waktu Selesai</label>
                            <input type="text" class="form-control bg-white" id="tanggal_selesai" name="tanggal_selesai" required value="<?= old('tanggal_selesai', substr($agenda['tanggal_selesai'], 0, 16)) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="lokasi" class="form-label fw-semibold">Lokasi Kegiatan</label>
                        <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Contoh: Aula lt.2 Diskominfo" required value="<?= old('lokasi', $agenda['lokasi']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Status Agenda</label>
                        <select class="form-select" name="status" id="status" required>
                            <option value="aktif" <?= old('status', $agenda['status']) === 'aktif' ? 'selected' : '' ?>>Aktif (Tamu Bisa Registrasi)</option>
                            <option value="nonaktif" <?= old('status', $agenda['status']) === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            <option value="selesai" <?= old('status', $agenda['status']) === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>

                    <h5 class="fw-bold text-indigo mt-4 mb-3 border-bottom pb-2">Unit Kerja</h5>

                    <div class="mb-3">
                        <?php if ($isSuperadmin): ?>
                            <label for="kode_opd" class="form-label fw-semibold">Dinas / OPD Penyelenggara</label>
                            <select class="form-select select2-enable" name="kode_opd" id="kode_opd" required style="width: 100%">
                                <option value="">-- Pilih OPD --</option>
                                <?php foreach ($opds as $row): ?>
                                    <option value="<?= esc($row['kode_opd']) ?>" <?= old('kode_opd', $agenda['kode_opd']) === $row['kode_opd'] ? 'selected' : '' ?>>[<?= esc($row['kode_opd']) ?>] <?= esc($row['nama_opd']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <label class="form-label fw-semibold">Dinas / OPD Penyelenggara</label>
                            <input type="text" class="form-control bg-light" value="<?= esc($opd['nama_opd']) ?>" readonly disabled>
                            <input type="hidden" name="kode_opd" id="kode_opd" value="<?= esc($opd['kode_opd']) ?>">
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="kode_bagian" class="form-label fw-semibold">Bagian / Bidang Penyelenggara (Opsional)</label>
                        <select class="form-select select2-enable" name="kode_bagian" id="kode_bagian" style="width: 100%">
                            <option value="">-- Pilih Bagian (Opsional) --</option>
                            <?php foreach ($bagians as $row): ?>
                                <option value="<?= esc($row['kode_bagian']) ?>" <?= old('kode_bagian', $agenda['kode_bagian']) === $row['kode_bagian'] ? 'selected' : '' ?>><?= esc($row['nama_bagian']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="kode_subbagian" class="form-label fw-semibold">Subbagian / Subbidang Penyelenggara (Opsional)</label>
                        <select class="form-select select2-enable" name="kode_subbagian" id="kode_subbagian" style="width: 100%" <?= !empty($subbagians) ? '' : 'disabled' ?>>
                            <option value="">-- Pilih Subbagian (Opsional) --</option>
                            <?php foreach ($subbagians as $row): ?>
                                <option value="<?= esc($row['kode_subbagian']) ?>" <?= old('kode_subbagian', $agenda['kode_subbagian']) === $row['kode_subbagian'] ? 'selected' : '' ?>><?= esc($row['nama_subbagian']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="penanggung_jawab" class="form-label fw-semibold">Penanggung Jawab (PJ)</label>
                        <select class="form-select select2-enable" id="penanggung_jawab" name="penanggung_jawab" required style="width: 100%">
                            <option value="">-- Pilih Penanggung Jawab --</option>
                            <?php foreach ($pegawais as $peg): ?>
                                <option value="<?= esc($peg['nama']) ?>" <?= old('penanggung_jawab', $agenda['penanggung_jawab']) === $peg['nama'] ? 'selected' : '' ?>><?= esc($peg['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="<?= base_url('agenda') ?>" class="btn btn-light rounded-pill px-4">Kembali</a>
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
        // Initialize Select2
        $('.select2-enable').select2({
            theme: 'bootstrap-5'
        });

        // Flatpickr on text inputs
        flatpickr("#tanggal_mulai", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
        });

        flatpickr("#tanggal_selesai", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
        });

        // Cascading Dropdown (OPD -> Bagian)
        $('#kode_opd').on('change', function() {
            let kodeOpd = $(this).val();
            let bagianSelect = $('#kode_bagian');
            let subbagianSelect = $('#kode_subbagian');
            
            if ($(this).data('initialized')) {
                bagianSelect.empty().append('<option value="">-- Pilih Bagian (Opsional) --</option>').trigger('change');
                subbagianSelect.empty().append('<option value="">-- Pilih Subbagian (Opsional) --</option>').prop('disabled', true).trigger('change');
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

            if ($(this).data('initialized')) {
                subbagianSelect.empty().append('<option value="">-- Pilih Subbagian (Opsional) --</option>').trigger('change');
            } else {
                $(this).data('initialized', true);
                loadPegawaiForAgenda(kodeOpd, kodeBagian, $('#kode_subbagian').val());
                return; // Keep existing preselected Subbagian
            }

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

            // Load pegawai for selected bagian
            loadPegawaiForAgenda(kodeOpd, kodeBagian, '');
        });

        // Load pegawai when subbagian changes
        $('#kode_subbagian').on('change', function() {
            let kodeOpd = $('#kode_opd').val();
            let kodeBagian = $('#kode_bagian').val();
            let kodeSubbagian = $(this).val();
            loadPegawaiForAgenda(kodeOpd, kodeBagian, kodeSubbagian);
        });

        // Load pegawai when OPD changes (for superadmin)
        $('#kode_opd').on('change', function() {
            let kodeOpd = $(this).val();
            let bagianSelect = $('#kode_bagian');
            let subbagianSelect = $('#kode_subbagian');

            if ($(this).data('initialized')) {
                bagianSelect.empty().append('<option value="">-- Pilih Bagian (Opsional) --</option>').trigger('change');
                subbagianSelect.empty().append('<option value="">-- Pilih Subbagian (Opsional) --</option>').prop('disabled', true).trigger('change');
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

            loadPegawaiForAgenda(kodeOpd, '', '');
        });

        function loadPegawaiForAgenda(kodeOpd, kodeBagian, kodeSubbagian) {
            let pegawaiSelect = $('#penanggung_jawab');

            if (!kodeOpd) {
                pegawaiSelect.empty().append('<option value="">-- Pilih Penanggung Jawab --</option>').trigger('change');
                return;
            }

            pegawaiSelect.prop('disabled', true);

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
                    pegawaiSelect.prop('disabled', false);
                    let currentValue = pegawaiSelect.val();
                    pegawaiSelect.empty().append('<option value="">-- Pilih Penanggung Jawab --</option>');
                    if (data.length > 0) {
                        $.each(data, function(key, val) {
                            pegawaiSelect.append('<option value="' + val.nama + '">' + val.nama + '</option>');
                        });
                        // Restore current value if it still exists
                        if (currentValue) {
                            pegawaiSelect.val(currentValue);
                        }
                    }
                    pegawaiSelect.trigger('change');
                },
                error: function() {
                    showAppToast('error', 'Gagal mengambil data Pegawai.');
                    pegawaiSelect.prop('disabled', false);
                }
            });
        }

        $('#kode_opd').data('initialized', false);
        $('#kode_bagian').data('initialized', false);
    });
</script>
<?= $this->endSection() ?>
