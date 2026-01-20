<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap | Munu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f3f4f6;
        }

        .login-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .login-left {
            flex: 1;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 50px;
            position: relative;
            z-index: 2;
        }

        .login-right {
            flex: 1.5;
            background: #1e1e2d;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .login-form {
            width: 100%;
            max-width: 400px;
        }

        .brand-logo {
            width: 120px;
            height: 120px;
            min-width: 120px;
            min-height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e90ff 0%, #00bfff 100%);
            box-shadow: 0 4px 15px rgba(30, 144, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            overflow: hidden;
            border: none;
            flex-shrink: 0;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            padding: 0;
            border-radius: 50%;
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-bottom: 15px;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: transform 0.2s;
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        /* Slider Styles */
        .slider-content {
            text-align: center;
            color: white;
            z-index: 10;
            max-width: 80%;
        }

        .slider-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            transition: opacity 1s ease-in-out;
            opacity: 0.4;
        }

        .slider-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.9) 0%, rgba(168, 85, 247, 0.9) 100%);
            z-index: 1;
        }

        /* Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .toggle-form {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .toggle-form a {
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <!-- Left Side: Login Form -->
        <div class="login-left">
            <div class="brand-logo">
                <!-- User's Logo Placeholder - Will be dynamic -->
                <img src="<?= base_url('public/assets/img/logo.png') ?>" alt="Munu Logo"
                    onerror="this.src='https://via.placeholder.com/150?text=MUNU'">
            </div>

            <div class="login-form fade-in" id="loginForm">
                <h3 class="fw-bold mb-1 text-center text-dark">Hoş Geldiniz</h3>
                <p class="text-muted text-center mb-4">Hesabınıza giriş yapın</p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger py-2 small">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success py-2 small">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('Auth/login') ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Kullanıcı Adı</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="fa-solid fa-user text-muted"></i></span>
                            <input type="text" name="username" class="form-control border-start-0 ps-0"
                                placeholder="Kullanıcı adınız" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Şifre</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="fa-solid fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0 ps-0"
                                placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2">
                        Giriş Yap <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>

                    <div class="toggle-form">
                        Hesabınız yok mu? <a onclick="toggleForms()">Şirket Oluştur</a>
                    </div>
                </form>
            </div>

            <!-- Register Form (Hidden by default) -->
            <div class="login-form fade-in" id="registerForm" style="display: none;">
                <h3 class="fw-bold mb-1 text-center text-dark">Yeni Şirket</h3>
                <p class="text-muted text-center mb-4">İşletmeniz için hesap oluşturun</p>

                <?php if (session()->getFlashdata('reg_error')): ?>
                    <div class="alert alert-danger py-2 small">
                        <?= session()->getFlashdata('reg_error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('Auth/register_company') ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Şirket Adı</label>
                        <input type="text" name="company_name" class="form-control" placeholder="Örn: ABC Market"
                            required>
                    </div>

                    <hr class="my-3">
                    <p class="small text-muted mb-3 fw-bold">Yönetici Bilgileri</p>

                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Kullanıcı Adı</label>
                                <input type="text" name="username" class="form-control" placeholder="Kullanıcı adınız"
                                    required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Ad Soyad</label>
                                <input type="text" name="full_name" class="form-control" placeholder="Adınız Soyadınız"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Telefon</label>
                                <input type="tel" name="phone" class="form-control" placeholder="05XX XXX XX XX"
                                    required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">E-posta</label>
                                <input type="email" name="email" class="form-control" placeholder="ornek@email.com">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Şifre</label>
                        <input type="password" name="password" class="form-control" placeholder="Güçlü bir şifre"
                            required>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2">
                        Kayıt Ol ve Başla
                    </button>

                    <div class="toggle-form">
                        Zaten hesabınız var mı? <a onclick="toggleForms()">Giriş Yap</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Side: Slider -->
        <div class="login-right">
            <div class="slider-overlay"></div>
            <!-- Background Images -->
            <div class="slider-bg"
                style="background-image: url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2000&auto=format&fit=crop'); opacity: 1;">
            </div>
            <div class="slider-bg"
                style="background-image: url('https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=2000&auto=format&fit=crop'); opacity: 0;">
            </div>
            <div class="slider-bg"
                style="background-image: url('https://images.unsplash.com/photo-1556742049-0cfed4f7a07d?q=80&w=2000&auto=format&fit=crop'); opacity: 0;">
            </div>

            <div class="slider-content">
                <div class="mb-4">
                    <i class="fa-solid fa-chart-line fa-3x"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3">İşletmenizi Büyütün</h1>
                <p class="lead opacity-75">Munu ile gelir-gider takibi, stok yönetimi ve<br>yapay zeka destekli finansal
                    analizler parmaklarınızın ucunda.</p>
            </div>
        </div>
    </div>

    <script>
        function toggleForms() {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');

            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
                window.location.hash = '';
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
                window.location.hash = 'register';
            }
        }

        // Slider Logic
        const slides = document.querySelectorAll('.slider-bg');
        let currentSlide = 0;

        setInterval(() => {
            slides[currentSlide].style.opacity = 0;
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].style.opacity = 1;
        }, 5000);

        // Check hash on load
        if (window.location.hash === '#register') {
            toggleForms();
        }
    </script>
</body>

</html>