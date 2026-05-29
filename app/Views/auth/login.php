<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - E-GuestBook</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            padding: 2rem 1rem;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 30% 20%, rgba(79, 70, 229, 0.12) 0%, transparent 60%),
                        radial-gradient(ellipse at 70% 80%, rgba(16, 185, 129, 0.1) 0%, transparent 60%);
            animation: bgPulse 12s ease-in-out infinite alternate;
        }
        @keyframes bgPulse {
            0% { transform: scale(1) rotate(0deg); }
            100% { transform: scale(1.1) rotate(5deg); }
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }
        .login-card .card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1.25rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
        }
        .login-logo {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.85rem;
            background: rgba(79, 70, 229, 0.15);
            border: 1px solid rgba(79, 70, 229, 0.25);
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #818cf8;
        }
        .form-control.dark-input {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            padding-left: 2.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        .form-control.dark-input:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            color: #f8fafc;
        }
        .form-control.dark-input::placeholder {
            color: #64748b;
        }
        .input-icon-wrapper {
            position: relative;
        }
        .input-icon-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1rem;
            z-index: 2;
        }
        .btn-admin-login {
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        .btn-admin-login:hover {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.35);
            color: white;
        }
        .form-label {
            color: #94a3b8;
            font-weight: 500;
            font-size: 0.85rem;
        }
        .divider-text {
            color: #475569;
            font-size: 0.8rem;
        }
        .admin-link {
            color: #64748b;
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
        }
        .admin-link:hover {
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h1 class="login-logo mb-2">
                <i class="bi bi-person-vcard-fill me-2"></i>E-GuestBook
            </h1>
            <div class="login-badge">
                <i class="bi bi-shield-lock-fill"></i> Panel Admin &amp; Sistem
            </div>
        </div>

        <div class="card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <h4 class="fw-bold text-white mb-1">Login Admin</h4>
                    <p class="text-secondary small mb-0">Masuk menggunakan Email dan Password Anda</p>
                </div>

                <form action="<?= base_url('login') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-envelope"></i>
                            <input type="email" class="form-control dark-input" id="email" name="email" 
                                   placeholder="Masukkan Email Anda" 
                                   value="<?= old('email') ?>" required autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-lock"></i>
                            <input type="password" class="form-control dark-input" id="password" name="password" 
                                   placeholder="Masukkan Password" required>
                            <span class="password-toggle-icon" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); cursor: pointer; color: #64748b; z-index: 10;">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="form-check mb-4 text-start">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember" <?php if (old('remember')): ?> checked <?php endif ?>>
                        <label class="form-check-label text-secondary small" for="remember">Ingat Saya</label>
                    </div>

                    <button type="submit" class="btn btn-admin-login w-100 mb-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                    </button>
                </form>

                <div class="text-center mt-3">
                    <span class="divider-text">Bukan admin?</span>
                    <a href="<?= base_url('pegawai-portal/login') ?>" class="admin-link ms-1">
                        Login sebagai Pegawai <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-secondary small mb-0">&copy; <?= date('Y') ?> Diskominfo Kabupaten Grobogan</p>
        </div>
    </div>

    <!-- JQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
        };

        <?php if (session()->getFlashdata('success')): ?>
            toastr.success("<?= esc(session()->getFlashdata('success')) ?>");
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            toastr.error("<?= esc(session()->getFlashdata('error')) ?>");
        <?php endif; ?>

        // Toggle password visibility
        $('#togglePasswordIcon').on('click', function() {
            const passwordInput = $('#password');
            const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
            passwordInput.attr('type', type);
            $(this).toggleClass('bi-eye bi-eye-slash');
        });
    </script>
</body>
</html>
