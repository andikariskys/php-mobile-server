<?php
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    $db_user = db_get('username', 'admin');
    $db_pass = db_get('password', 'admin');

    if ($username === $db_user && $password === $db_pass) {
        $_SESSION['user_logged_in'] = true;
        header("Location: index.php?page=dashboard");
        exit;
    } else {
        $error = 'Username atau Password salah!';
    }
}

// Background image inline style from DB if present
$bg_style = '';
$bg_path = db_get('dashboard_bg');
if ($bg_path) {
    $bg_style = 'style="background-image: url(\'' . htmlspecialchars($bg_path) . '\');"';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mobile Web Server</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <!-- Flaticon UIcons CSS -->
    <link rel="stylesheet" href="assets/flaticon/css/uicons-solid-rounded.css">
    <!-- Custom Style CSS -->
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            animation: cardSlideIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes cardSlideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body <?= $bg_style ?>>
    <div class="bg-overlay"></div>

    <div class="container d-flex justify-content-center px-3">
        <div class="login-card glass-card p-4 p-sm-5 text-center">
            <div class="icon-container icon-primary mx-auto mb-3">
                <i class="fi fi-sr-smartphone text-white fs-3"></i>
            </div>
            
            <h3 class="text-white font-weight-700 mb-1">Mobile Server</h3>
            <p class="text-secondary fs-7 mb-4">Silakan masuk menggunakan akun Administrator Anda</p>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger bg-danger bg-opacity-20 border-danger border-opacity-30 text-danger rounded-10 text-start py-2.5 px-3 fs-7 mb-3 animated-fade-in" role="alert">
                    <i class="fi fi-sr-info me-2 align-middle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form action="index.php?page=login" method="POST">
                <div class="text-start mb-3">
                    <label for="username" class="form-label text-secondary fs-7 ms-1">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-black bg-opacity-25 border-end-0 border-white border-opacity-10 text-secondary" style="border-radius: 10px 0 0 10px;">
                            <i class="fi fi-sr-user fs-7"></i>
                        </span>
                        <input type="text" name="username" id="username" class="form-control form-glass border-start-0" style="border-radius: 0 10px 10px 0;" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>
                
                <div class="text-start mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label text-secondary fs-7 ms-1 mb-0">Password</label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-black bg-opacity-25 border-end-0 border-white border-opacity-10 text-secondary" style="border-radius: 10px 0 0 10px;">
                            <i class="fi fi-sr-lock fs-7"></i>
                        </span>
                        <input type="password" name="password" id="password" class="form-control form-glass border-start-0" style="border-radius: 0 10px 10px 0;" placeholder="Masukkan password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary-gradient w-100 py-2.5 rounded-10 text-white font-weight-600 mb-3">
                    Masuk Sekarang
                </button>
            </form>
            
            <div class="text-secondary fs-8 mt-2">
                Mobile Web Server v1.0.0
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
