<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Buku Tamu Elektronik</title>
    
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
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <?php
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
    ?>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= base_url('dashboard') ?>" class="sidebar-brand">
                <i class="bi bi-person-vcard-fill me-2"></i>E-GuestBook
            </a>
            <button class="btn d-lg-none text-white p-0" onclick="toggleSidebar()">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>
        
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="<?= base_url('dashboard') ?>" class="sidebar-link <?= url_is('dashboard*') ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2-fill"></i>Dashboard
                </a>
            </li>
            
            <li class="sidebar-menu-label">Layanan Tamu</li>
            
            <li class="sidebar-item">
                <a href="<?= base_url('tamu') ?>" class="sidebar-link <?= (url_is('tamu*') && !url_is('tamu/input')) ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i>Data Tamu
                </a>
            </li>
            
            <?php if (!$isSuperadmin): ?>
            <li class="sidebar-item">
                <a href="<?= base_url('tamu/input') ?>" class="sidebar-link <?= url_is('tamu/input*') ? 'active' : '' ?>">
                    <i class="bi bi-person-plus-fill"></i>Input Manual
                </a>
            </li>
            <?php endif; ?>
            
            <li class="sidebar-item">
                <a href="<?= base_url('agenda') ?>" class="sidebar-link <?= url_is('agenda*') ? 'active' : '' ?>">
                    <i class="bi bi-calendar-event-fill"></i>Manajemen Agenda
                </a>
            </li>

            <li class="sidebar-item">
                <a href="<?= base_url('laporan') ?>" class="sidebar-link <?= url_is('laporan*') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>Laporan &amp; Export
                </a>
            </li>
            
            <?php if ($isSuperadmin): ?>
            <li class="sidebar-menu-label">Master Data &amp; Sistem</li>
            
            <li class="sidebar-item">
                <a href="<?= base_url('users') ?>" class="sidebar-link <?= url_is('users*') ? 'active' : '' ?>">
                    <i class="bi bi-person-gear-fill"></i>Manajemen User
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="<?= base_url('master/opd') ?>" class="sidebar-link <?= url_is('master/opd*') || url_is('master/bagian*') || url_is('master/subbagian*') ? 'active' : '' ?>">
                    <i class="bi bi-building-fill"></i>OPD &amp; Bagian
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="<?= base_url('pegawai') ?>" class="sidebar-link <?= url_is('pegawai*') ? 'active' : '' ?>">
                    <i class="bi bi-person-workspace"></i>Master Pegawai
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="<?= base_url('audit') ?>" class="sidebar-link <?= url_is('audit*') ? 'active' : '' ?>">
                    <i class="bi bi-shield-check"></i>Audit Log
                </a>
            </li>
            <?php endif; ?>
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
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px; font-weight: 600;">
                        <?= substr($user->nama ?? $user->username, 0, 1) ?>
                    </div>
                    <div class="d-none d-sm-block text-start me-1">
                        <div class="fw-semibold text-truncate" style="max-width: 150px;"><?= esc($user->nama ?? $user->username) ?></div>
                        <div class="small text-muted" style="font-size: 0.75rem;"><?= $isSuperadmin ? 'Superadmin' : 'Admin Unit' ?></div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="userDropdown" style="border-radius: 0.75rem;">
                    <li>
                        <div class="dropdown-header text-dark">
                            <strong>Login as:</strong><br>
                            <span class="text-muted"><?= esc($user->email) ?></span>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger d-flex align-items-center" href="<?= base_url('logout') ?>">
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
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Toastr JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <!-- Signature Pad JS -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <!-- Inputmask JS -->
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.8/dist/inputmask.min.js"></script>
    <!-- Moment JS -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }
        
        // Toastr settings
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
        };

        // Flash message handling
        <?php if (session()->getFlashdata('success')): ?>
            toastr.success("<?= esc(session()->getFlashdata('success')) ?>");
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            toastr.error("<?= esc(session()->getFlashdata('error')) ?>");
        <?php endif; ?>
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
