<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Portal Pegawai</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <?php
        $pegawaiNama = session()->get('pegawai_nama');
        $pegawaiNip  = session()->get('pegawai_nip');
        $pegawaiJabatan = session()->get('pegawai_jabatan');
    ?>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= base_url('pegawai-portal/dashboard') ?>" class="sidebar-brand">
                <i class="bi bi-person-vcard-fill me-2"></i>Portal Pegawai
            </a>
            <button class="btn d-lg-none text-white p-0" onclick="toggleSidebar()">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>
        
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="<?= base_url('pegawai-portal/dashboard') ?>" class="sidebar-link <?= url_is('pegawai-portal/dashboard*') ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2-fill"></i>Dashboard
                </a>
            </li>
            
            <li class="sidebar-menu-label">Layanan Tamu</li>
            
            <li class="sidebar-item">
                <a href="<?= base_url('pegawai-portal/tamu?status=menunggu') ?>" class="sidebar-link <?= url_is('pegawai-portal/tamu*') ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i>Daftar Tamu
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper" id="main-wrapper">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle me-3" id="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <div class="d-none d-md-block text-secondary small">
                    <i class="bi bi-clock me-1"></i> <?= date('d M Y') ?>
                </div>
            </div>
            
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px; font-weight: 600;">
                        <?= substr($pegawaiNama ?? 'P', 0, 1) ?>
                    </div>
                    <div class="d-none d-sm-block text-start me-1">
                        <div class="fw-semibold text-truncate" style="max-width: 150px;"><?= esc($pegawaiNama ?? 'Pegawai') ?></div>
                        <div class="small text-muted" style="font-size: 0.75rem;">Pegawai</div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="userDropdown" style="border-radius: 0.75rem;">
                    <li>
                        <div class="dropdown-header text-dark">
                            <strong>Login as:</strong><br>
                            <span class="text-muted">NIP: <?= esc($pegawaiNip ?? '-') ?></span><br>
                            <span class="text-muted small"><?= esc($pegawaiJabatan ?? '') ?></span>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <?php if (false): ?>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="<?= base_url('pegawai-portal/ganti-password') ?>">
                            <i class="bi bi-key me-2"></i> Ganti Password
                        </a>
                    </li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger d-flex align-items-center" href="<?= base_url('pegawai-portal/logout') ?>">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- Content Body -->
        <main class="content-body">
            <!-- CSRF Protection Token (for JS requests) -->
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf-token">
            
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- JQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables Bundle JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }
        
        function showAppToast(icon, title) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: title,
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        }

        // Flash message handling
        <?php if (session()->getFlashdata('success')): ?>
            showAppToast('success', <?= json_encode(session()->getFlashdata('success')) ?>);
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            showAppToast('error', <?= json_encode(session()->getFlashdata('error')) ?>);
        <?php endif; ?>

        document.addEventListener('submit', function(event) {
            const form = event.target.closest('.swal-confirm-form');
            if (!form || form.dataset.confirmed === 'true') {
                return;
            }

            event.preventDefault();
            Swal.fire({
                icon: 'question',
                title: form.dataset.confirmTitle || 'Konfirmasi aksi?',
                text: form.dataset.confirmText || 'Aksi ini akan diproses.',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            });
        });
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
