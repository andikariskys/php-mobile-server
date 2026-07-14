<?php
// Determine the active link class helper
function is_active($path) {
    global $uri;
    if ($path === '/dashboard' && ($uri === '/dashboard' || $uri === '/')) {
        return 'active';
    }
    return ($uri === $path || strpos($uri, $path . '/') === 0) ? 'active' : '';
}

// Check if background image is configured in SQLite
$bg_style = '';
$bg_path = db_get('dashboard_bg');
if ($bg_path) {
    $bg_style = 'style="background-image: url(\'' . htmlspecialchars($bg_path) . '\');"';
}

$db_user = db_get('username', 'admin');

// Get real header battery info
$headerBattery = $device->getBatteryDetails();
$headerBatteryLevel = isset($headerBattery['level']) ? $headerBattery['level'] . '%' : 'N/A';
$headerBatteryStatus = isset($headerBattery['status']) ? $headerBattery['status'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Web Server</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    
    <!-- Flaticon UIcons CSS -->
    <link rel="stylesheet" href="assets/flaticon/css/uicons-solid-rounded.css">
    
    <!-- Custom Style CSS -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body <?= $bg_style ?>>
    <div class="bg-overlay"></div>
    
    <!-- Sidebar Backdrop for Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar Layout -->
    <aside class="sidebar" id="sidebarMenu">
        <div class="sidebar-header d-flex flex-column align-items-start gap-0">
            <a href="index.php?page=dashboard" class="sidebar-brand">Mobile Server</a>
            <!-- Added Created by subtitle here -->
            <span class="text-secondary" style="font-size: 0.725rem; margin-top: -2px;">Created by: <a href="http://www.andikariskys.my.id" target="_blank" class="text-secondary text-decoration-none hover-white">www.andikariskys.my.id</a></span>
        </div>
        
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="index.php?page=dashboard" class="sidebar-link <?= is_active('/dashboard') ?>">
                    <i class="fi fi-sr-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="index.php?page=file-manager" class="sidebar-link <?= is_active('/file-manager') ?>">
                    <i class="fi fi-sr-folder"></i>
                    <span>File Manager</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="index.php?page=device-control" class="sidebar-link <?= is_active('/device-control') ?>">
                    <i class="fi fi-sr-power"></i>
                    <span>Device Control</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="index.php?page=network" class="sidebar-link <?= is_active('/network') ?>">
                    <i class="fi fi-sr-wifi"></i>
                    <span>Network Settings</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="index.php?page=sms" class="sidebar-link <?= is_active('/sms') ?>">
                    <i class="fi fi-sr-envelope"></i>
                    <span>SMS Manager</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="index.php?page=notification" class="sidebar-link <?= is_active('/notification') ?>">
                    <i class="fi fi-sr-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="index.php?page=camera" class="sidebar-link <?= is_active('/camera') ?>">
                    <i class="fi fi-sr-camera"></i>
                    <span>Camera Control</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="index.php?page=audio" class="sidebar-link <?= is_active('/audio') ?>">
                    <i class="fi fi-sr-volume"></i>
                    <span>Audio & TTS</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="index.php?page=location" class="sidebar-link <?= is_active('/location') ?>">
                    <i class="fi fi-sr-marker"></i>
                    <span>Location Map</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="index.php?page=terminal" class="sidebar-link <?= is_active('/terminal') ?>">
                    <i class="fi fi-sr-terminal"></i>
                    <span>Terminal</span>
                </a>
            </li>
            <!-- Added Developer Menu -->
            <li class="sidebar-item">
                <a href="index.php?page=developer" class="sidebar-link <?= is_active('/developer') ?>">
                    <i class="fi fi-sr-menu-dots"></i>
                    <span>Developer</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="index.php?page=settings" class="sidebar-link <?= is_active('/settings') ?>">
                    <i class="fi fi-sr-settings"></i>
                    <span>System Settings</span>
                </a>
            </li>
            
            <!-- Added Documentation Menu (Disabled / Read-only with distinct visual effect) -->
            <li class="sidebar-item border-top border-white border-opacity-10 mt-3 pt-3">
                <a href="#" class="sidebar-link disabled" style="opacity: 0.55; cursor: not-allowed; border: 1px dashed rgba(255,255,255,0.15); background: rgba(255,255,255,0.02); pointer-events: none;" onclick="return false;">
                    <i class="fi fi-sr-info text-warning"></i>
                    <span class="text-white">Dokumentasi</span>
                    <span class="badge bg-warning text-dark fs-9 ms-auto px-1.5 py-0.5" style="font-size: 0.6rem; animation: pulse 2s infinite;">SOON</span>
                </a>
            </li>
        </ul>
        
        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">
                    <?= strtoupper(substr($db_user, 0, 2)) ?>
                </div>
                <div class="user-info me-auto">
                    <p class="user-name"><?= htmlspecialchars($db_user) ?></p>
                    <p class="user-role">Administrator</p>
                </div>
                <a href="index.php?page=logout" class="text-danger fs-5" title="Logout" style="text-decoration: none;">
                    <i class="fi fi-sr-leave"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggler" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
                    <i class="fi fi-sr-menu-burger" style="font-size: 1.25rem;"></i>
                </button>
                <h4 class="mb-0 text-white font-weight-600 d-none d-sm-block">
                    <?php
                    // Get title according to current page
                    switch($uri) {
                        case '/dashboard':
                        case '/': echo 'Dashboard Overview'; break;
                        case '/file-manager': echo 'Tiny File Manager'; break;
                        case '/device-control': echo 'Device Control Panel'; break;
                        case '/network': echo 'Network & Connections'; break;
                        case '/sms': echo 'SMS Messages'; break;
                        case '/notification': echo 'Android Notifications'; break;
                        case '/camera': echo 'Camera Control'; break;
                        case '/audio': echo 'Audio & TTS'; break;
                        case '/location': echo 'Device Location Map'; break;
                        case '/terminal': echo 'Terminal Session'; break;
                        case '/developer': echo 'Developer Info'; break;
                        case '/settings': echo 'Account & UI Settings'; break;
                        default: echo 'Mobile Server'; break;
                    }
                    ?>
                </h4>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Status Pill: Connection -->
                <div class="d-none d-md-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-success bg-opacity-10 border border-success border-opacity-25">
                    <span class="d-inline-block w-2.5 h-2.5 bg-success rounded-circle" style="width: 8px; height: 8px; animation: pulse 2s infinite;"></span>
                    <span class="text-success font-weight-500 fs-7">Connected</span>
                </div>
                
                <!-- Status Pill: Battery - Removed white background -->
                <div class="d-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-transparent border border-white border-opacity-10" id="headerBatteryInfo" title="Status: <?= htmlspecialchars($headerBatteryStatus) ?>">
                    <i class="fi fi-sr-battery-full text-warning"></i>
                    <span class="text-white font-weight-500 fs-7" id="headerBatteryLevel"><?= htmlspecialchars($headerBatteryLevel) ?></span>
                </div>
            </div>
        </header>
        
        <main class="content-area animated-fade-in">
            <?php include $page_content_file; ?>
        </main>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Layout Interactions -->
    <script>
        // Sidebar Toggling logic for mobile responsive screens
        const sidebarMenu = document.getElementById('sidebarMenu');
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function toggleSidebar() {
            sidebarMenu.classList.toggle('show');
            sidebarBackdrop.classList.toggle('show');
        }

        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', toggleSidebar);
        }
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', toggleSidebar);
        }

        // Simulate varying battery level in header
        setInterval(() => {
            const batText = document.getElementById('headerBatteryLevel');
            if (batText) {
                let current = parseInt(batText.textContent);
                if (current > 5) {
                    if (Math.random() > 0.8) {
                        current -= 1;
                        batText.textContent = current + '%';
                    }
                }
            }
        }, 15000);
    </script>
    
    <style>
        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.5; }
        }
        .fs-7 { font-size: 0.8rem; }
    </style>
</body>
</html>
