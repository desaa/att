<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Master Data OPD<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-sm-6">
        <h2 class="fw-bold text-dark">Master Data OPD</h2>
        <p class="text-secondary mb-0">Kelola data Dinas / Organisasi Perangkat Daerah.</p>
    </div>
    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-lg me-2"></i>Tambah OPD
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="glass-card">
            <div class="glass-card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="opdTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 80px;">No</th>
                                <th>Kode OPD</th>
                                <th>Nama OPD</th>
                                <th class="text-end pe-4" style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($opds as $opd): ?>
                            <tr>
                                <td class="ps-4"><?= $no++ ?></td>
                                <td><span class="badge bg-secondary font-monospace"><?= esc($opd['kode_opd']) ?></span></td>
                                <td class="fw-semibold text-dark"><?= esc($opd['nama_opd']) ?></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light border me-1" 
                                            onclick="editOpd('<?= esc($opd['kode_opd']) ?>', '<?= esc($opd['nama_opd'], 'js') ?>')" 
                                            title="Ubah">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border btn-delete" 
                                            data-url="<?= base_url('master/opd/delete/' . esc($opd['kode_opd'])) ?>" 
                                            title="Hapus">
                                        <i class="bi bi-trash3-fill text-danger"></i>
                                    </button>
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

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="addModalLabel">Tambah OPD Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('master/opd/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label for="kode_opd" class="form-label fw-semibold">Kode OPD</label>
                        <input type="text" class="form-control" id="kode_opd" name="kode_opd" placeholder="Contoh: 05" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_opd" class="form-label fw-semibold">Nama OPD</label>
                        <input type="text" class="form-control" id="nama_opd" name="nama_opd" placeholder="Contoh: Dinas Komunikasi dan Informatika" required>
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
                <h5 class="modal-title fw-bold" id="editModalLabel">Ubah OPD</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label for="edit_kode_opd" class="form-label fw-semibold">Kode OPD</label>
                        <input type="text" class="form-control bg-light" id="edit_kode_opd" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_opd" class="form-label fw-semibold">Nama OPD</label>
                        <input type="text" class="form-control" id="edit_nama_opd" name="nama_opd" required>
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
        $('#opdTable').DataTable({
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

        // Delete confirmation using SweetAlert2
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const deleteUrl = $(this).data('url');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data OPD dan semua data terkait (Bagian, Subbagian, Pegawai, Tamu) akan dihapus secara permanen!",
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
                    // We need to submit a form post to perform deletion securely with CSRF
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

    function editOpd(kode, nama) {
        $('#edit_kode_opd').val(kode);
        $('#edit_nama_opd').val(nama);
        $('#editForm').attr('action', '<?= base_url("master/opd/update") ?>/' + kode);
        $('#editModal').modal('show');
    }
</script>
<?= $this->endSection() ?>
