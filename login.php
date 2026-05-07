<?php
session_start();
$error_message = '';

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$admin_username = 'Bago';
$admin_password = '2004';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $admin_username;
        header("Location: index.php");
        exit;
    } else {
        $error_message = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0b214a">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Login - Sistem Informasi Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        .login-page-bg {
            background-image: url('omohada.jpg') !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
            background-attachment: fixed !important;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
            margin: 0;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            transition: background-image 1s ease-in-out;
        }
        .login-page-bg::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(11, 33, 74, 0.45); /* Mengurangi kegelapan overlay agar foto lebih terlihat (bisa disesuaikan 0.1 - 0.9) */
            backdrop-filter: blur(4px); /* Efek blur untuk menyamarkan gambar yang resolusinya rendah/pecah */
            -webkit-backdrop-filter: blur(4px);
            z-index: 1;
        }
        .login-card { max-width: 400px; width: 100%; z-index: 10; padding: 0 15px; position: relative; }
        .glass-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .glass-card .text-dark { color: #0b214a !important; font-weight: 800; text-shadow: none; letter-spacing: 1px; }
        .glass-card .text-muted { color: #64748b !important; font-size: 0.95rem; text-shadow: none !important; font-weight: 500; }
        .glass-card .form-label { font-weight: 600; color: #334155; font-size: 0.95rem; margin-bottom: 0.4rem; text-shadow: none; }
        .glass-card .form-control {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #1e293b !important;
            padding: 0.6rem 1rem !important;
            font-size: 0.95rem;
            font-weight: 500;
            text-shadow: none;
            border-radius: 4px;
            transition: all 0.2s ease;
            box-shadow: none !important;
        }
        .glass-card .form-control::placeholder { color: #94a3b8 !important; text-shadow: none; font-weight: 500; }
        .glass-card .form-control:focus {
            border-color: #0b214a !important;
            box-shadow: 0 0 0 0.2rem rgba(11, 33, 74, 0.2) !important;
        }
        .glass-card .password-container { position: relative; }
        .glass-card .password-container .form-control { padding-right: 2.5rem; }
        .glass-card .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 0;
        }
        .glass-card .toggle-password:hover { color: #0b214a; }
        .alert-glass {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            border-radius: 4px;
            font-weight: 600;
        }
        .btn-login {
            background: #4e73df;
            border: none;
            border-radius: 10rem;
            font-weight: 600;
            padding: 0.75rem;
            font-size: 0.8rem;
            text-shadow: none;
            transition: all 0.2s ease;
            box-shadow: none;
            animation: fadeInUpBtn 0.6s ease-out 0.4s both;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.15rem 1rem 0 rgba(58, 59, 69, 0.15);
            background: #2e59d9;
        }
        .logo-login {
            width: 85px;
            height: 85px;
            object-fit: cover;
            background-color: #ffffff;
            border-radius: 50%;
            padding: 0;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            animation: popInLogo 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes popInLogo {
            0% { transform: scale(0.5); opacity: 0; }
            70% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes fadeInUpBtn {
            0% { transform: translateY(20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-8px); }
            20%, 40%, 60%, 80% { transform: translateX(8px); }
        }
        .shake-error {
            animation: shakeError 0.5s cubic-bezier(.36,.07,.19,.97) both;
            border-color: #dc2626 !important;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.2) !important;
        }
            
        /* Memperbaiki bug background gambar membesar (zoom) di HP */
        @media (max-width: 767.98px) {
            .login-page-bg {
                background-attachment: scroll !important;
                background-position: center top !important;
                align-items: flex-start !important; /* Melepas kuncian tengah agar form bisa naik saat keyboard muncul */
                padding-top: 12vh !important; /* Memberikan ruang proporsional dari atas */
            }
            .login-card {
                padding: 0 10px; /* Menyesuaikan jarak card dengan sisi layar */
                margin: 0 auto 40px auto; /* Ruang ekstra di bawah agar tombol Login tidak tertutup keyboard */
            }
            .glass-card .card-body {
                padding: 2rem 1.5rem !important; /* Membuat form lebih proporsional di layar kecil */
            }
        }
    </style>
</head>
<body class="login-page-bg">
    <div class="login-card animate-fade-up">
        <div class="card shadow-lg border-0 glass-card <?= $error_message ? 'shake-error' : '' ?>">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <img src="logo.png?t=<?= time() ?>" alt="Logo Desa" class="logo-login" onerror="this.src='https://via.placeholder.com/85'">
                    <h4 class="text-dark mb-1">Desa Hilifalago</h4>
                    <p class="text-muted mb-4">Sistem Informasi Desa</p>
                </div>
                <?php if ($error_message): ?>
                    <div class="alert alert-glass small py-2 text-center mb-3">⚠️ <?= $error_message ?></div>
                <?php endif; ?>
                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan Username" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-container">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password" required>
                            <button type="button" class="toggle-password" id="togglePassword" tabindex="-1" title="Tampilkan Password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="d-grid"><button type="submit" class="btn btn-primary btn-lg btn-login text-white">Login</button></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Script untuk fitur Tampilkan/Sembunyikan Password
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            if (type === 'password') {
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>';
            } else {
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755l-.809-.805zm-4.475-4.475a2.5 2.5 0 0 1 3.036 3.036l-.824-.824a1.5 1.5 0 0 0-1.388-1.388l-.824-.824zM8 10.5a2.5 2.5 0 0 1-2.5-2.5c0-.174.02-.342.057-.504l.824.824c.058.266.24.482.504.542l.824.824c-.162.037-.33.057-.504.057zm-4.708-1.468C1.867 8.01 1 8 1 8s3 5.5 8 5.5c.89 0 1.72-.205 2.458-.567l-1.31-1.31a5.952 5.952 0 0 1-1.148.377 5.944 5.944 0 0 1-5.168-2.457A13.134 13.134 0 0 1 1.172 8c.058-.087.122-.183.195-.288.335-.48.83-1.12 1.465-1.755l1.458 1.458zM1.202 1.202l13.596 13.596-.708.708-13.596-13.596.708-.708z"/></svg>';
            }
        });

        // Script untuk Background Slideshow Otomatis
        const bgImages = [
            'omohada.jpg',
            'hhhh.jpg',
            'hilifalago.jpg',
            'hhh.jpg'
        ];
        
        // Melakukan preload gambar agar transisi lebih mulus saat baru pertama kali diputar
        bgImages.forEach(src => {
            const img = new Image();
            img.src = src;
        });

        let currentBgIndex = 0;
        const bgElement = document.querySelector('.login-page-bg');

        // Mengganti gambar background setiap 5000 ms (5 detik)
        setInterval(() => {
            currentBgIndex = (currentBgIndex + 1) % bgImages.length;
            bgElement.style.setProperty('background-image', `url('${bgImages[currentBgIndex]}')`, 'important');
        }, 5000);
    </script>
</body>
</html>