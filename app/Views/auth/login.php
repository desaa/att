<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - e-AdaTamu</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            overflow-x: hidden;
            background-color: #0f172a;
        }

        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            width: 100%;
            background: url('<?= base_url("flogin/image.jpeg") ?>') no-repeat;
            background-size: cover;
            background-position: center;
            padding: 20px;
            position: relative;
        }

        .login-box {
            position: relative;
            width: 400px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 30px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
            z-index: 2;
        }

        form {
            width: 100%;
        }

        h2 {
            font-size: 1.5em;
            color: #1e293b;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
        }

        .input-box {
            position: relative;
            width: 100%;
            margin: 25px 0;
            border-bottom: 2px solid #cbd5e1;
        }

        .input-box label {
            position: absolute;
            top: 50%;
            left: 5px;
            transform: translateY(-50%);
            font-size: 0.95em;
            color: #64748b;
            pointer-events: none;
            transition: .4s;
        }

        .input-box input:focus ~ label,
        .input-box input.has-value ~ label {
            top: -5px;
            font-size: 0.8em;
            color: #4f46e5;
        }

        .input-box input {
            width: 100%;
            height: 45px;
            background: transparent;
            border: none;
            outline: none;
            font-size: 0.95em;
            color: #0f172a;
            padding: 0 35px 0 5px;
        }

        .input-box .icon {
            position: absolute;
            right: 8px;
            top: 50%;
            color: #64748b;
            transform: translateY(-50%);
            font-size: 1.25em;
        }

        .remember-forgot {
            margin: -10px 0 20px;
            font-size: .8em;
            color: #475569;
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .remember-forgot label {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .remember-forgot label input {
            accent-color: #4f46e5;
            cursor: pointer;
        }

        .remember-forgot a {
            color: #4f46e5;
            text-decoration: none;
            transition: .2s;
        }

        .remember-forgot a:hover {
            text-decoration: underline;
            color: #3b82f6;
        }

        #btn-submit {
            width: 100%;
            height: 45px;
            background-color: #4f46e5;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 0.95em;
            color: #fff;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        #btn-submit:hover:not(:disabled) {
            background-color: #3b82f6;
            color: #fff;
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.4);
        }

        #btn-submit:disabled {
            background-color: #e2e8f0;
            color: #94a3b8;
            cursor: not-allowed;
        }

        .register-link {
            font-size: .8em;
            color: #475569;
            text-align: center;
            margin: 20px 0 0;
            width: 100%;
        }

        .register-link p a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
            transition: .2s;
        }

        .register-link p a:hover {
            text-decoration: underline;
            color: #3b82f6;
        }

        /* Captcha Styles */
        .captcha-wrapper {
            margin-bottom: 20px;
            width: 100%;
            display: none;
        }

        .captcha-canvas-container {
            position: relative;
            width: 100%;
            height: 155px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 12px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .slider-container {
            position: relative;
            width: 100%;
            height: 40px;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }

        .slider-track-bg {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            border-radius: 20px;
            background: rgba(79, 70, 229, 0.3);
            width: 0%;
        }

        .slider-text {
            position: absolute;
            width: 100%;
            text-align: center;
            color: #475569;
            font-size: 0.78em;
            font-weight: 500;
            pointer-events: none;
            user-select: none;
        }

        .captcha-slider {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            margin: 0;
            z-index: 10;
        }

        .slider-btn {
            position: absolute;
            left: 0;
            top: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            transition: background 0.2s, color 0.2s;
            z-index: 5;
        }

        .slider-btn i {
            color: #000;
            font-size: 1.1em;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }

        .shake-anim {
            animation: shake 0.4s ease-in-out;
        }

        @media (max-width: 500px) {
            .wrapper {
                padding: 0;
            }
            .login-box {
                width: 100%;
                height: 100vh;
                border: none;
                border-radius: 0;
                background: #ffffff;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="login-box">
            <form action="<?= base_url('login') ?>" method="POST" id="loginForm">
                <?= csrf_field() ?>
                
                <div class="text-center mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <img src="<?= base_url('assets/app-logo/logo-only.png') ?>" alt="Logo Icon" style="height: 72px; width: auto; object-fit: contain;">
                        <img src="<?= base_url('assets/app-logo/text-only.png') ?>" alt="e-AdaTamu" style="height: 40px; width: auto; object-fit: contain; margin-left: -12px;">
                    </div>
                </div>

                <h2>Login Admin</h2>

                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="mail"></ion-icon>
                    </span>
                    <input type="email" id="email" name="email" value="<?= old('email') ?>" required autocomplete="off">
                    <label>Email</label>
                </div>

                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="lock-closed" id="togglePasswordIcon" style="cursor: pointer;"></ion-icon>
                    </span>
                    <input type="password" id="password" name="password" required>
                    <label>Password</label>
                </div>

                <div class="remember-forgot">
                    <label>
                        <input type="checkbox" name="remember" <?php if (old('remember')): ?> checked <?php endif ?>> Ingat Saya
                    </label>
                    <!-- <a href="#">Lupa Password?</a> -->
                </div>

                <!-- Slider Captcha Geser Puzzle -->
                <div class="captcha-wrapper">
                    <div class="captcha-canvas-container" id="captcha-canvas-container">
                        <canvas id="captcha-bg" width="336" height="155" style="display: block; width: 100%; height: 100%;"></canvas>
                        <canvas id="captcha-piece" width="336" height="155" style="position: absolute; left: 0; top: 0;"></canvas>
                    </div>
                    <div class="slider-container">
                        <div class="slider-track-bg"></div>
                        <div class="slider-text">Isi email & password untuk verifikasi</div>
                        <input type="range" id="captcha-slider" class="captcha-slider" min="0" max="290" value="0" disabled>
                        <div class="slider-btn">
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>
                </div>

                <button type="button" id="btn-submit" disabled>
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                </button>

                <div class="register-link">
                    <p>Bukan admin? <a href="<?= base_url('pegawai-portal/login') ?>">Login sebagai Pegawai <i class="bi bi-arrow-right"></i></a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Ionicons -->
    <script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <!-- JQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
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

        <?php if (session()->getFlashdata('success')): ?>
            showAppToast('success', <?= json_encode(session()->getFlashdata('success')) ?>);
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            showAppToast('error', <?= json_encode(session()->getFlashdata('error')) ?>);
        <?php endif; ?>

        $(document).ready(function() {
            // Toggle label positioning depending on whether input is filled
            $('input').on('focus blur input change', function() {
                if ($(this).val().trim() !== '') {
                    $(this).addClass('has-value');
                } else {
                    $(this).removeClass('has-value');
                }
            });
            $('input').each(function() {
                if ($(this).val().trim() !== '') {
                    $(this).addClass('has-value');
                }
            });

            // Password Toggle Visibility
            $('#togglePasswordIcon').on('click', function() {
                const passwordInput = $('#password');
                const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                passwordInput.attr('type', type);
                const iconName = type === 'password' ? 'lock-closed' : 'lock-open';
                $(this).attr('name', iconName);
            });

            // Inputs check to enable/disable slide captcha
            let captchaVerified = false;

            function checkInputs() {
                if (captchaVerified) return;
                let email = $('#email').val().trim();
                let password = $('#password').val().trim();
                if (email !== '' && password !== '') {
                    if ($('.captcha-wrapper').is(':hidden')) {
                        $('#btn-submit').prop('disabled', false);
                    }
                } else {
                    $('#btn-submit').prop('disabled', true);
                }
            }

            $('#email, #password').on('input change keyup', checkInputs);

            $('#btn-submit').on('click', function() {
                if (!captchaVerified) {
                    $('.captcha-wrapper').slideDown();
                    $(this).prop('disabled', true).html('<i class="bi bi-puzzle me-1"></i> Selesaikan Puzzle');
                    $('#captcha-slider').prop('disabled', false);
                    $('.slider-text').text('Geser puzzle untuk verifikasi');
                }
            });

            // CAPTCHA PUZZLE LOGIC
            const img = new Image();
            img.src = '<?= base_url("flogin/image.jpeg") ?>';
            
            const canvasBg = document.getElementById('captcha-bg');
            const canvasPiece = document.getElementById('captcha-piece');
            const ctxBg = canvasBg.getContext('2d');
            const ctxPiece = canvasPiece.getContext('2d');
            
            let targetX = 0;
            let targetY = 0;
            const pieceSize = 45;
            const r = 8; // notch circle radius

            function drawPuzzlePath(ctx, x, y) {
                ctx.beginPath();
                ctx.moveTo(x, y);
                // Top edge notch (outward)
                ctx.lineTo(x + pieceSize / 2 - r, y);
                ctx.arc(x + pieceSize / 2, y, r, Math.PI, 0, false);
                ctx.lineTo(x + pieceSize, y);
                // Right edge notch (outward)
                ctx.lineTo(x + pieceSize, y + pieceSize / 2 - r);
                ctx.arc(x + pieceSize, y + pieceSize / 2, r, 1.5 * Math.PI, 0.5 * Math.PI, false);
                ctx.lineTo(x + pieceSize, y + pieceSize);
                // Bottom edge notch (inward)
                ctx.lineTo(x + pieceSize / 2 + r, y + pieceSize);
                ctx.arc(x + pieceSize / 2, y + pieceSize, r, 0, Math.PI, false);
                ctx.lineTo(x, y + pieceSize);
                // Left edge notch (inward)
                ctx.lineTo(x, y + pieceSize / 2 + r);
                ctx.arc(x, y + pieceSize / 2, r, 0.5 * Math.PI, 1.5 * Math.PI, true);
                ctx.closePath();
            }

            img.onload = function() {
                initCaptcha();
            };

            function initCaptcha() {
                captchaVerified = false;
                // Width of container is 336 (since width: 100% inside 340px outer padded container).
                // Random targetX between 120 and 260.
                // targetY between 20 and 90.
                targetX = Math.floor(Math.random() * (250 - 110 + 1)) + 110;
                targetY = Math.floor(Math.random() * (90 - 20 + 1)) + 20;

                // Max slider distance = 336 - 45 - 2*r = ~270
                $('#captcha-slider').attr('max', 270);
                $('#captcha-slider').val(0);
                $('#captcha-piece').css('left', '0px');
                $('.slider-btn').css('left', '0px');
                $('.slider-track-bg').css('width', '0%');
                
                // 1. Draw background image
                ctxBg.clearRect(0, 0, 336, 155);
                ctxBg.drawImage(img, 0, 0, 336, 155);

                // 2. Draw puzzle cutout on background
                ctxBg.save();
                drawPuzzlePath(ctxBg, targetX, targetY);
                ctxBg.fillStyle = 'rgba(0, 0, 0, 0.65)';
                ctxBg.fill();
                ctxBg.strokeStyle = 'rgba(255, 255, 255, 0.8)';
                ctxBg.lineWidth = 2;
                ctxBg.stroke();
                ctxBg.restore();

                // 3. Draw puzzle piece slice on local x = 0
                ctxPiece.clearRect(0, 0, 336, 155);
                ctxPiece.save();
                drawPuzzlePath(ctxPiece, 0, targetY);
                ctxPiece.clip();
                ctxPiece.drawImage(img, -targetX, 0, 336, 155);
                ctxPiece.strokeStyle = 'rgba(255, 255, 255, 0.85)';
                ctxPiece.lineWidth = 2;
                ctxPiece.stroke();
                ctxPiece.restore();

                checkInputs();
            }

            // Slider update as dragging
            $('#captcha-slider').on('input', function() {
                let val = $(this).val();
                $('#captcha-piece').css('left', val + 'px');
                $('.slider-btn').css('left', val + 'px');
                $('.slider-track-bg').css('width', (parseInt(val) + 20) + 'px');
            });

            // Check match on slider release
            $('#captcha-slider').on('change', function() {
                let val = parseInt($(this).val());
                let tolerance = 6; // 6 pixels threshold
                if (Math.abs(val - targetX) <= tolerance) {
                    // Success!
                    captchaVerified = true;
                    $('.slider-btn').css('background', '#10b981').html('<i class="bi bi-check-lg" style="color:#fff;"></i>');
                    $('.slider-text').text('Verifikasi Berhasil').css('color', '#34d399');
                    $('#captcha-slider').prop('disabled', true);
                    
                    setTimeout(function() {
                        $('#loginForm').submit();
                    }, 500);
                } else {
                    // Shake and reset
                    $('#captcha-canvas-container').addClass('shake-anim');
                    setTimeout(function() {
                        $('#captcha-canvas-container').removeClass('shake-anim');
                        initCaptcha();
                    }, 450);
                }
            });
        });
    </script>
</body>
</html>
