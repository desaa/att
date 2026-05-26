<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Buat Agenda<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Buat Agenda Baru</h2>
        <p class="text-secondary">Buat agenda baru untuk kegiatan scan QR Code.</p>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="glass-card">
            <div class="glass-card-body">
                <form action="<?= base_url('agenda/store') ?>" method="POST" id="agendaForm">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="nama_agenda" class="form-label fw-semibold">Nama Kegiatan / Agenda</label>
                        <input type="text" class="form-control" id="nama_agenda" name="nama_agenda" placeholder="Contoh: Rapat Koordinasi IT Grobogan" required value="<?= old('nama_agenda') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label fw-semibold">Deskripsi Kegiatan</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan secara singkat mengenai kegiatan ini..."><?= old('deskripsi') ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggal_mulai" class="form-label fw-semibold">Waktu Mulai</label>
                            <input type="datetime-local" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required value="<?= old('tanggal_mulai') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_selesai" class="form-label fw-semibold">Waktu Selesai</label>
                            <input type="datetime-local" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required value="<?= old('tanggal_selesai') ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="lokasi" class="form-label fw-semibold">Lokasi Kegiatan</label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Contoh: Aula lt.2 Diskominfo" required value="<?= old('lokasi') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="penanggung_jawab" class="form-label fw-semibold">Penanggung Jawab (PJ)</label>
                            <input type="text" class="form-control" id="penanggung_jawab" name="penanggung_jawab" placeholder="Contoh: Budi Santoso, S.Kom" required value="<?= old('penanggung_jawab') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Status Awal Agenda</label>
                        <select class="form-select" name="status" id="status" required>
                            <option value="aktif" <?= old('status') === 'aktif' ? 'selected' : '' ?>>Aktif (Tamu Bisa Registrasi)</option>
                            <option value="nonaktif" <?= old('status') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>

                    <h5 class="fw-bold text-indigo mt-4 mb-3 border-bottom pb-2">Unit Kerja</h5>

                    <div class="mb-3">
                        <?php if ($isSuperadmin): ?>
                            <label for="kode_opd" class="form-label fw-semibold">Dinas / OPD Penyelenggara</label>
                            <select class="form-select select2-enable" name="kode_opd" id="kode_opd" required style="width: 100%">
                                <option value="">-- Pilih OPD --</option>
                                <?php foreach ($opds as $row): ?>
                                    <option value="<?= esc($row['kode_opd']) ?>" <?= old('kode_opd') === $row['kode_opd'] ? 'selected' : '' ?>>[<?= esc($row['kode_opd']) ?>] <?= esc($row['nama_opd']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <label class="form-label fw-semibold">Dinas / OPD Penyelenggara</label>
                            <input type="text" class="form-control bg-light" value="<?= esc($opd['nama_opd']) ?>" readonly disabled>
                            <input type="hidden" name="kode_opd" id="kode_opd" value="<?= esc($opd['kode_opd']) ?>">
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="kode_bagian" class="form-label fw-semibold">Bagian / Bidang Penyelenggara</label>
                        <select class="form-select select2-enable" name="kode_bagian" id="kode_bagian" required style="width: 100%" <?= !$isSuperadmin ? '' : 'disabled' ?>>
                            <option value="">-- Pilih Bagian --</option>
                            <?php if (!$isSuperadmin): ?>
                                <?php foreach ($bagians as $row): ?>
                                    <option value="<?= esc($row['kode_bagian']) ?>" <?= old('kode_bagian') === $row['kode_bagian'] ? 'selected' : '' ?>><?= esc($row['nama_bagian']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="<?= base_url('agenda') ?>" class="btn btn-light rounded-pill px-4">Kembali</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Agenda</button>
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

        // Flatpickr on datetime-local
        flatpickr("#tanggal_mulai", {
            enableTime: true,
            dateFormat: "Y-m-d\\TH:i",
            time_24hr: true
        });

        flatpickr("#tanggal_selesai", {
            enableTime: true,
            dateFormat: "Y-m-d\\TH:i",
            time_24hr: true
        });

        // Cascading Dropdown (OPD -> Bagian)
        $('#kode_opd').on('change', function() {
            let kodeOpd = $(this).val();
            let bagianSelect = $('#kode_bagian');
            
            bagianSelect.empty().append('<option value="">-- Pilih Bagian --</option>').trigger('change');
            
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
                        toastr.error('Gagal mengambil data Bagian.');
                    }
                });
            } else {
                bagianSelect.prop('disabled', true);
            }
        });
    });
</script>
<?= $this->endSection() ?>
