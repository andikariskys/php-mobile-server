<?php
$auth_success = '';
$auth_error = '';
$bg_success = '';
$bg_error = '';
$integration_success = '';
$integration_error = '';

// Handle Settings Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Change Credentials
    if (isset($_POST['action']) && $_POST['action'] === 'change_creds') {
        $new_user = isset($_POST['new_username']) ? trim($_POST['new_username']) : '';
        $new_pass = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
        
        if (!empty($new_user) && !empty($new_pass)) {
            db_set('username', $new_user);
            db_set('password', $new_pass);
            $auth_success = 'Kredensial login berhasil diperbarui di database!';
        } else {
            $auth_error = 'Username dan Password tidak boleh kosong.';
        }
    }
    
    // 2. Upload Background Image
    if (isset($_POST['action']) && $_POST['action'] === 'upload_bg') {
        if (isset($_FILES['bg_image']) && $_FILES['bg_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['bg_image']['tmp_name'];
            $file_name = $_FILES['bg_image']['name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (in_array($ext, $allowed_exts)) {
                $upload_dir = __DIR__ . '/../assets/uploads';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $dest_file = $upload_dir . '/bg_custom.jpg';
                
                if (move_uploaded_file($file_tmp, $dest_file)) {
                    $bg_path = '/assets/uploads/bg_custom.jpg?t=' . time();
                    db_set('dashboard_bg', $bg_path);
                    $bg_success = 'Gambar latar belakang berhasil diunggah dan diterapkan!';
                } else {
                    $bg_error = 'Gagal menyimpan file ke folder assets.';
                }
            } else {
                $bg_error = 'Ekstensi file tidak diizinkan. Gunakan JPG, JPEG, PNG, WEBP, atau GIF.';
            }
        } else {
            $bg_error = 'Gagal mengunggah file. Pastikan ukuran file tidak terlalu besar.';
        }
    }
    
    // 3. Reset Background
    if (isset($_POST['action']) && $_POST['action'] === 'reset_bg') {
        db_set('dashboard_bg', '');
        $bg_success = 'Latar belakang berhasil di-reset ke default gradient.';
    }

    // 4. Save External Integrations (IP Camera & Terminal)
    if (isset($_POST['action']) && $_POST['action'] === 'save_integration') {
        $ip_cam_port = isset($_POST['ip_camera_port']) ? trim($_POST['ip_camera_port']) : '';
        $ip_cam_url = isset($_POST['ip_camera_url']) ? trim($_POST['ip_camera_url']) : '';
        $term_port = isset($_POST['terminal_port']) ? trim($_POST['terminal_port']) : '';
        $term_url = isset($_POST['terminal_url']) ? trim($_POST['terminal_url']) : '';
        
        db_set('ip_camera_port', $ip_cam_port);
        db_set('ip_camera_url', $ip_cam_url);
        db_set('terminal_port', $term_port);
        db_set('terminal_url', $term_url);
        
        $integration_success = 'Konfigurasi IP Camera dan Terminal berhasil disimpan!';
    }
}

// Load data from DB
$db_user = db_get('username', 'admin');
$bg_path = db_get('dashboard_bg', '');
$ip_camera_url = db_get('ip_camera_url', '');
$ip_camera_port = db_get('ip_camera_port', '4444');
$terminal_url = db_get('terminal_url', '');
$terminal_port = db_get('terminal_port', '3001');
?>

<div class="row g-4">
    <!-- Change Credentials Card -->
    <div class="col-12 col-lg-6">
        <div class="glass-card h-100">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fi fi-sr-lock text-primary fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Kredensial Login</h5>
            </div>
            
            <?php if (!empty($auth_success)): ?>
                <div class="alert alert-success bg-success bg-opacity-20 border-success border-opacity-30 text-success rounded-10 py-2.5 px-3 fs-7 mb-3 animated-fade-in" role="alert">
                    <i class="fi fi-sr-shield-check me-2 align-middle"></i> <?= $auth_success ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($auth_error)): ?>
                <div class="alert alert-danger bg-danger bg-opacity-20 border-danger border-opacity-30 text-danger rounded-10 py-2.5 px-3 fs-7 mb-3 animated-fade-in" role="alert">
                    <i class="fi fi-sr-info me-2 align-middle"></i> <?= $auth_error ?>
                </div>
            <?php endif; ?>
            
            <form action="/settings" method="POST">
                <input type="hidden" name="action" value="change_creds">
                
                <div class="mb-3">
                    <label for="new_username" class="form-label text-secondary fs-7 ms-1">Username Baru</label>
                    <input type="text" name="new_username" id="new_username" class="form-control form-glass" value="<?= htmlspecialchars($db_user) ?>" required>
                </div>
                
                <div class="mb-4">
                    <label for="new_password" class="form-label text-secondary fs-7 ms-1">Password Baru</label>
                    <input type="password" name="new_password" id="new_password" class="form-control form-glass" placeholder="Masukkan password baru" required>
                </div>
                
                <button type="submit" class="btn btn-primary-gradient px-4 py-2.5 rounded-10 text-white font-weight-600 w-100">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- Background Image Upload Card -->
    <div class="col-12 col-lg-6">
        <div class="glass-card h-100">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fi fi-sr-settings text-primary fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Latar Belakang Dashboard</h5>
            </div>
            
            <?php if (!empty($bg_success)): ?>
                <div class="alert alert-success bg-success bg-opacity-20 border-success border-opacity-30 text-success rounded-10 py-2.5 px-3 fs-7 mb-3 animated-fade-in" role="alert">
                    <i class="fi fi-sr-shield-check me-2 align-middle"></i> <?= $bg_success ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($bg_error)): ?>
                <div class="alert alert-danger bg-danger bg-opacity-20 border-danger border-opacity-30 text-danger rounded-10 py-2.5 px-3 fs-7 mb-3 animated-fade-in" role="alert">
                    <i class="fi fi-sr-info me-2 align-middle"></i> <?= $bg_error ?>
                </div>
            <?php endif; ?>
            
            <form action="/settings" method="POST" enctype="multipart/form-data" class="mb-3">
                <input type="hidden" name="action" value="upload_bg">
                
                <div class="mb-4">
                    <label for="bg_image" class="form-label text-secondary fs-7 ms-1">Unggah Gambar Baru</label>
                    <input type="file" name="bg_image" id="bg_image" class="form-control form-glass" accept="image/*" required>
                    <div class="fs-8 text-secondary mt-1.5">
                        Format yang didukung: JPG, PNG, WEBP. Maksimal 2MB. Gambar akan otomatis memburam (blur) di latar belakang dashboard untuk menjaga keterbacaan teks.
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary-gradient px-4 py-2.5 rounded-10 text-white font-weight-600 w-100 mb-2">
                    Unggah & Terapkan Gambar
                </button>
            </form>

            <?php if (!empty($bg_path)): ?>
                <form action="/settings" method="POST">
                    <input type="hidden" name="action" value="reset_bg">
                    <button type="submit" class="btn btn-outline-danger px-4 py-2 rounded-10 w-100 fs-8">
                        Reset ke Latar Belakang Default
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Integration Settings Row -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="glass-card">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fi fi-sr-computer text-info fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Konfigurasi Integrasi Eksternal</h5>
            </div>

            <?php if (!empty($integration_success)): ?>
                <div class="alert alert-success bg-success bg-opacity-20 border-success border-opacity-30 text-success rounded-10 py-2.5 px-3 fs-7 mb-3 animated-fade-in" role="alert">
                    <i class="fi fi-sr-shield-check me-2 align-middle"></i> <?= $integration_success ?>
                </div>
            <?php endif; ?>
            
            <form action="/settings" method="POST">
                <input type="hidden" name="action" value="save_integration">
                
                <!-- IP Camera inputs split into Port and URL -->
                <div class="row g-3 mb-4 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="ip_camera_port" class="form-label text-secondary fs-7 ms-1">Port IP Camera</label>
                        <input type="text" name="ip_camera_port" id="ip_camera_port" class="form-control form-glass" value="<?= htmlspecialchars($ip_camera_port) ?>" placeholder="4444">
                    </div>
                    <div class="col-12 col-md-9">
                        <label for="ip_camera_url" class="form-label text-secondary fs-7 ms-1">URL Domain IP Camera</label>
                        <input type="text" name="ip_camera_url" id="ip_camera_url" class="form-control form-glass" value="<?= htmlspecialchars($ip_camera_url) ?>" placeholder="Contoh: ip-cam.xxxx.com atau http://192.168.1.50:8081/video">
                    </div>
                    <div class="col-12 fs-8 text-secondary mt-1 ms-1">
                        * Input <strong>Port</strong> digunakan saat mengakses melalui Hotspot/IP lokal. Input <strong>URL Domain</strong> digunakan saat mengakses melalui internet dengan domain.
                    </div>
                </div>

                <!-- Terminal inputs split into Port and URL -->
                <div class="row g-3 mb-4 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="terminal_port" class="form-label text-secondary fs-7 ms-1">Port Terminal (ttyd)</label>
                        <input type="text" name="terminal_port" id="terminal_port" class="form-control form-glass" value="<?= htmlspecialchars($terminal_port) ?>" placeholder="3001">
                    </div>
                    <div class="col-12 col-md-9">
                        <label for="terminal_url" class="form-label text-secondary fs-7 ms-1">URL Domain Terminal</label>
                        <input type="text" name="terminal_url" id="terminal_url" class="form-control form-glass" value="<?= htmlspecialchars($terminal_url) ?>" placeholder="Contoh: term.xxxx.com atau http://192.168.1.100:7681">
                    </div>
                    <div class="col-12 fs-8 text-secondary mt-1 ms-1">
                        * Input <strong>Port</strong> digunakan saat mengakses melalui Hotspot/IP lokal. Input <strong>URL Domain</strong> digunakan saat mengakses melalui internet dengan domain.
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary-gradient px-4 py-2.5 rounded-10 text-white font-weight-600">
                    Simpan Integrasi
                </button>
            </form>
        </div>
    </div>
</div>
