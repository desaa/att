<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Master Data Bagian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-sm-6">
        <h2 class="fw-bold text-dark">Master Data Bagian</h2>
        <p class="text-secondary mb-0">Kelola data Bidang / Bagian di lingkungan OPD.</p>
    </div>
    <?php if (false): ?>
    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-lg me-2"></i>Tambah Bagian
        </button>
    </div>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-12">
        <div class="glass-card">
            <div class="glass-card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="bagianTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 80px;">No</th>
                                <th>OPD</th>
                                <th>Kode Bagian</th>
                                <th>Nama Bagian</th>
                                <?php if (false): ?>
                                <th class="text-end pe-4" style="width: 180px;">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($bagians as $bagian): ?>
                            <tr>
                                <td class="ps-4"><?= $no++ ?></td>
                                <td><span class="small fw-semibold text-secondary"><?= esc($bagian['nama_opd']) ?></span></td>
                                <td><span class="badge bg-secondary font-monospace"><?= esc($bagian['kode_bagian']) ?></span></td>
                                <td class="fw-semibold text-dark"><?= esc($bagian['nama_bagian']) ?></td>
                                <?php if (false): ?>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light border me-1" 
                                            onclick="editBagian('<?= esc($bagian['kode_opd']) ?>', '<?= esc($bagian['kode_bagian']) ?>', '<?= esc($bagian['nama_bagian'], 'js') ?>')" 
                                            title="Ubah">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border btn-delete" 
                                            data-url="<?= base_url('master/bagian/delete/' . esc($bagian['kode_opd']) . '/' . esc($bagian['kode_bagian'])) ?>" 
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
                <h5 class="modal-title fw-bold" id="addModalLabel">Tambah Bagian Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('master/bagian/store') ?>" method="POST">
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
                        <label for="kode_bagian" class="form-label fw-semibold">Kode Bagian</label>
                        <input type="text" class="form-control" id="kode_bagian" name="kode_bagian" placeholder="Contoh: 01" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_bagian" class="form-label fw-semibold">Nama Bagian</label>
                        <input type="text" class="form-control" id="nama_bagian" name="nama_bagian" placeholder="Contoh: Sekretariat" required>
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
                <h5 class="modal-title fw-bold" id="editModalLabel">Ubah Bagian</h5>
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
                        <label for="edit_kode_bagian" class="form-label fw-semibold">Kode Bagian</label>
                        <input type="text" class="form-control bg-light" id="edit_kode_bagian" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_bagian" class="form-label fw-semibold">Nama Bagian</label>
                        <input type="text" class="form-control" id="edit_nama_bagian" name="nama_bagian" required>
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
        $('.select2-enable').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#addModal')
        });

        $('#bagianTable').DataTable({
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
                text: "Data Bagian dan semua data terkait (Subbagian, Pegawai, Tamu) akan dihapus secara permanen!",
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

    function editBagian(kodeOpd, kodeBagian, nama) {
        // Find OPD name in table row
        let row = $(`button[onclick*="'${kodeOpd}'"][onclick*="'${kodeBagian}'"]`).closest('tr');
        let opdName = row.find('td:nth-child(2)').text();

        $('#edit_opd_name').val(opdName);
        $('#edit_kode_bagian').val(kodeBagian);
        $('#edit_nama_bagian').val(nama);
        
        $('#editForm').attr('action', '<?= base_url("master/bagian/update") ?>/' + kodeOpd + '/' + kodeBagian);
        $('#editModal').modal('show');
    }
</script>
<?= $this->endSection() ?>
