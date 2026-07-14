<?php
// Handle clearing via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear_id'])) {
        $clear_id = $_POST['clear_id'];
        
        if (is_numeric($clear_id)) {
            $clear_id = (int)$clear_id;
            if (isset($_SESSION['notifications'])) {
                $_SESSION['notifications'] = array_values(array_filter($_SESSION['notifications'], function($n) use ($clear_id) {
                    return $n['id'] !== $clear_id;
                }));
            }
        } else {
            // Real device notification ID
            $device->removeNotification($clear_id);
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    } elseif (isset($_POST['clear_all'])) {
        // Clear all real notifications
        $real_notifications = $device->getNotifications();
        foreach ($real_notifications as $n) {
            $device->removeNotification($n['id']);
        }
        $_SESSION['notifications'] = [];
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}

// Fetch real notifications from DeviceController (Uses ONLY termux-notification-list)
$real_notifications = $device->getNotifications();

$display_notifications = [];
if (!empty($real_notifications)) {
    foreach ($real_notifications as $n) {
        $app = isset($n['package']) && $n['package'] !== 'N/A' ? $n['package'] : 'N/A';
        $icon = 'fi-sr-bell';
        $icon_class = 'icon-info';
        
        if (strpos($app, 'whatsapp') !== false) {
            $app = 'WhatsApp';
            $icon = 'fi-sr-envelope';
            $icon_class = 'icon-success';
        } elseif (strpos($app, 'android') !== false) {
            $app = 'Sistem Android';
            $icon = 'fi-sr-bolt';
            $icon_class = 'icon-warning';
        } elseif (strpos($app, 'play') !== false) {
            $app = 'Google Play Store';
            $icon = 'fi-sr-folder';
            $icon_class = 'icon-info';
        } elseif ($app !== 'N/A') {
            $parts = explode('.', $app);
            $app = ucfirst(end($parts));
        }

        $display_notifications[] = [
            'id' => isset($n['id']) ? $n['id'] : '',
            'app' => $app,
            'icon' => $icon,
            'icon_class' => $icon_class,
            'title' => (isset($n['title']) && trim($n['title']) !== '') ? $n['title'] : 'N/A',
            'message' => (isset($n['content']) && trim($n['content']) !== '') ? $n['content'] : 'N/A',
            'time' => (isset($n['date']) && trim($n['date']) !== '') ? $n['date'] : 'N/A'
        ];
    }
} else {
    // FALLBACK: Load session mockup data
    if (!isset($_SESSION['notifications'])) {
        $_SESSION['notifications'] = [
            [
                'id' => 1,
                'app' => 'WhatsApp',
                'icon' => 'fi-sr-envelope',
                'icon_class' => 'icon-success',
                'title' => 'Budi (Grup Keluarga)',
                'message' => 'Jangan lupa nanti sore kita kumpul jam 5 ya di rumah makan biasa. Ditunggu kedatangannya!',
                'time' => '10 menit yang lalu'
            ],
            [
                'id' => 2,
                'app' => 'Google Play Store',
                'icon' => 'fi-sr-folder',
                'icon_class' => 'icon-info',
                'title' => 'Pembaruan Aplikasi',
                'message' => '4 aplikasi siap diperbarui. Silakan hubungkan ke Wi-Fi untuk memulai pengunduhan.',
                'time' => '1 jam yang lalu'
            ],
            [
                'id' => 3,
                'app' => 'Sistem Android',
                'icon' => 'fi-sr-bolt',
                'icon_class' => 'icon-warning',
                'title' => 'Baterai Tersisa 20%',
                'message' => 'Baterai perangkat Anda hampir habis. Aktifkan mode Penghemat Daya untuk memperpanjang daya.',
                'time' => '2 jam yang lalu'
            ],
            [
                'id' => 4,
                'app' => 'File Manager',
                'icon' => 'fi-sr-shield-check',
                'icon_class' => 'icon-primary',
                'title' => 'Pemindaian Selesai',
                'message' => 'Tidak ditemukan file sampah besar yang tidak terpakai. Penyimpanan optimal.',
                'time' => 'Kemarin'
            ]
        ];
    }
    $display_notifications = $_SESSION['notifications'];
}
?>

<div class="row g-4">
    <div class="col-12">
        <div class="glass-card">
            <!-- Header bar for Notifications -->
            <div class="d-flex align-items-center justify-content-between border-bottom border-white border-opacity-10 pb-3 mb-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fi fi-sr-bell text-warning fs-5"></i>
                    <h5 class="mb-0 text-white font-weight-600">Notifikasi Sistem Perangkat</h5>
                </div>
                
                <?php if (!empty($display_notifications)): ?>
                    <button class="btn btn-outline-danger btn-sm px-3 py-1.5 rounded-10 fs-8" onclick="clearAllNotifications()">
                        Hapus Semua
                    </button>
                <?php endif; ?>
            </div>
            
            <div id="notificationsContainer">
                <?php if (empty($display_notifications)): ?>
                    <div class="text-center py-5" id="noNotifications">
                        <i class="fi fi-sr-bell text-secondary fs-1 mb-2 opacity-50"></i>
                        <p class="text-secondary mb-0">Tidak ada notifikasi aktif saat ini.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($display_notifications as $notif): ?>
                        <div class="notification-item animated-fade-in" id="notif-<?= htmlspecialchars($notif['id']) ?>">
                            <div class="icon-container <?= htmlspecialchars($notif['icon_class']) ?> mb-0 flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                <i class="fi <?= htmlspecialchars($notif['icon']) ?>"></i>
                            </div>
                            
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-white font-weight-600 fs-7"><?= htmlspecialchars($notif['title']) ?></span>
                                        <span class="badge bg-white bg-opacity-5 text-secondary fs-9" style="font-size:0.65rem;"><?= htmlspecialchars($notif['app']) ?></span>
                                    </div>
                                    <span class="fs-9 text-secondary"><?= htmlspecialchars($notif['time']) ?></span>
                                </div>
                                <p class="text-secondary fs-8 mb-0"><?= htmlspecialchars($notif['message']) ?></p>
                            </div>
                            
                            <button class="btn btn-link text-secondary p-1 fs-5 ms-3 border-0 bg-transparent hover-danger" onclick="clearSingleNotification('<?= htmlspecialchars($notif['id']) ?>')" title="Hapus Notifikasi">
                                <span class="fs-8 text-secondary align-middle">x</span>
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function clearSingleNotification(id) {
        const item = document.getElementById(`notif-${id}`);
        if (item) {
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '0';
            item.style.transform = 'translateX(20px)';
            
            setTimeout(() => {
                item.remove();
                
                const container = document.getElementById('notificationsContainer');
                const remaining = container.querySelectorAll('.notification-item');
                if (remaining.length === 0) {
                    showNoNotificationsView();
                }
                
                const formData = new FormData();
                formData.append('clear_id', id);
                fetch('index.php?page=notification', {
                    method: 'POST',
                    body: formData
                });
            }, 300);
        }
    }

    function clearAllNotifications() {
        const container = document.getElementById('notificationsContainer');
        const items = container.querySelectorAll('.notification-item');
        
        items.forEach((item, index) => {
            setTimeout(() => {
                item.style.transition = 'all 0.3s ease';
                item.style.opacity = '0';
                item.style.transform = 'translateX(20px)';
            }, index * 100);
        });

        setTimeout(() => {
            items.forEach(item => item.remove());
            showNoNotificationsView();
            
            const formData = new FormData();
            formData.append('clear_all', '1');
            fetch('index.php?page=notification', {
                method: 'POST',
                body: formData
            });
            
            const clearAllBtn = document.querySelector('.glass-card button');
            if (clearAllBtn) clearAllBtn.remove();
        }, (items.length * 100) + 300);
    }

    function showNoNotificationsView() {
        const container = document.getElementById('notificationsContainer');
        container.innerHTML = `
            <div class="text-center py-5 animated-fade-in" id="noNotifications">
                <i class="fi fi-sr-bell text-secondary fs-1 mb-2 opacity-50"></i>
                <p class="text-secondary mb-0">Tidak ada notifikasi aktif saat ini.</p>
            </div>
        `;
    }
</script>

<style>
    .hover-danger:hover {
        color: #ef4444 !important;
    }
</style>
