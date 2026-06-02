<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Master Data Subbagian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-sm-6">
        <h2 class="fw-bold text-dark">Master Data Subbagian</h2>
        <p class="text-secondary mb-0">Kelola data Subbagian di lingkungan Bagian / Bidang.</p>
    </div>
    <?php if (false): ?>
    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-lg me-2"></i>Tambah Subbagian
        </button>
    </div>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-12">
        <div class="glass-card">
            <div class="glass-card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="subbagianTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 80px;">No</th>
                                <th>OPD</th>
                                <th>Bagian</th>
                                <th>Kode Subbagian</th>
                                <th>Nama Subbagian</th>
                                <?php if (false): ?>
                                <th class="text-end pe-4" style="width: 180px;">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($subbagians as $sub): ?>
                            <tr>
                                <td class="ps-4"><?= $no++ ?></td>
                                <td><span class="small fw-semibold text-secondary"><?= esc($sub['nama_opd']) ?></span></td>
                                <td><span class="small fw-semibold text-secondary"><?= esc($sub['nama_bagian']) ?></span></td>
                                <td><span class="badge bg-secondary font-monospace"><?= esc($sub['kode_subbagian']) ?></span></td>
                                <td class="fw-semibold text-dark"><?= esc($sub['nama_subbagian']) ?></td>
                                <?php if (false): ?>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light border me-1" 
                                            onclick="editSubbagian('<?= esc($sub['kode_opd']) ?>', '<?= esc($sub['kode_bagian']) ?>', '<?= esc($sub['kode_subbagian']) ?>', '<?= esc($sub['nama_subbagian'], 'js') ?>')" 
                                            title="Ubah">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border btn-delete" 
                                            data-url="<?= base_url('master/subbagian/delete/' . esc($sub['kode_opd']) . '/' . esc($sub['kode_bagian']) . '/' . esc($sub['kode_subbagian'])) ?>" 
                                            title="Hapus">
                                        <i class="bi bi-trash3-fill text-danger"></i>
                                    </button>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="addModalLabel">Tambah Subbagian Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('master/subbagian/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label for="kode_opd" class="form-label fw-semibold">Pilih OPD</label>
                        <select class="form-select select2-enable" name="kode_opd" id="kode_opd" required style="width: 100%">
                            <option value="">-- Pilih OPD --</option>
                            <?php foreach ($opds as $opd): ?>
                                <option value="<?= esc($opd['kode_opd']) ?>">[<?= esc($opd['kode_opd']) ?>] <?= esc($opd['nama_opd']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kode_bagian" class="form-label fw-semibold">Pilih Bagian</label>
                        <select class="form-select select2-enable" name="kode_bagian" id="kode_bagian" required style="width: 100%" disabled>
                            <option value="">-- Pilih Bagian --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kode_subbagian" class="form-label fw-semibold">Kode Subbagian</label>
                        <input type="text" class="form-control" id="kode_subbagian" name="kode_subbagian" placeholder="Contoh: 001" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_subbagian" class="form-label fw-semibold">Nama Subbagian</label>
                        <input type="text" class="form-control" id="nama_subbagian" name="nama_subbagian" placeholder="Contoh: Umum dan Kepegawaian" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="editModalLabel">Ubah Subbagian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label for="edit_opd_name" class="form-label fw-semibold">OPD</label>
                        <input type="text" class="form-control bg-light" id="edit_opd_name" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="edit_bagian_name" class="form-label fw-semibold">Bagian</label>
                        <input type="text" class="form-control bg-light" id="edit_bagian_name" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="edit_kode_subbagian" class="form-label fw-semibold">Kode Subbagian</label>
                        <input type="text" class="form-control bg-light" id="edit_kode_subbagian" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_subbagian" class="form-label fw-semibold">Nama Subbagian</label>
                        <input type="text" class="form-control" id="edit_nama_subbagian" name="nama_subbagian" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 in Modals
        $('#kode_opd').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#addModal')
        });

        $('#kode_bagian').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#addModal')
        });

        // Cascading Dropdown (OPD -> Bagian)
        $('#kode_opd').on('change', function() {
            let kodeOpd = $(this).val();
            let bagianSelect = $('#kode_bagian');
            
            bagianSelect.empty().append('<option value="">-- Pilih Bagian --</option>');
            
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

        $('#subbagianTable').DataTable({
            responsive: true,
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

        // Delete confirmation
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const deleteUrl = $(this).data('url');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data Subbagian akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                customClass: {
                    confirmButton: 'btn btn-danger rounded-pill px-4 me-2',
                    cancelButton: 'btn btn-light rounded-pill px-4'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = deleteUrl;
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = $('#csrf-token').attr('name');
                    csrf.value = $('#csrf-token').attr('value');
                    
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    function editSubbagian(kodeOpd, kodeBagian, kodeSubbagian, nama) {
        let row = $(`button[onclick*="'${kodeOpd}'"][onclick*="'${kodeBagian}'"][onclick*="'${kodeSubbagian}'"]`).closest('tr');
        let opdName = row.find('td:nth-child(2)').text();
        let bagianName = row.find('td:nth-child(3)').text();

        $('#edit_opd_name').val(opdName);
        $('#edit_bagian_name').val(bagianName);
        $('#edit_kode_subbagian').val(kodeSubbagian);
        $('#edit_nama_subbagian').val(nama);
        
        $('#editForm').attr('action', '<?= base_url("master/subbagian/update") ?>/' + kodeOpd + '/' + kodeBagian + '/' + kodeSubbagian);
        $('#editModal').modal('show');
    }
</script>
<?= $this->endSection() ?>
