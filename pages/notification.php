<?php
// Initialize notifications session data if not exists
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

// Handle clearing via POST if JS is disabled or for session persistence
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear_id'])) {
        $clear_id = (int)$_POST['clear_id'];
        $_SESSION['notifications'] = array_values(array_filter($_SESSION['notifications'], function($n) use ($clear_id) {
            return $n['id'] !== $clear_id;
        }));
    } elseif (isset($_POST['clear_all'])) {
        $_SESSION['notifications'] = [];
    }
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
                
                <?php if (!empty($_SESSION['notifications'])): ?>
                    <button class="btn btn-outline-danger btn-sm px-3 py-1.5 rounded-10 fs-8" onclick="clearAllNotifications()">
                        Hapus Semua
                    </button>
                <?php endif; ?>
            </div>
            
            <div id="notificationsContainer">
                <?php if (empty($_SESSION['notifications'])): ?>
                    <div class="text-center py-5" id="noNotifications">
                        <i class="fi fi-sr-bell text-secondary fs-1 mb-2 opacity-50"></i>
                        <p class="text-secondary mb-0">Tidak ada notifikasi aktif saat ini.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($_SESSION['notifications'] as $notif): ?>
                        <div class="notification-item animated-fade-in" id="notif-<?= $notif['id'] ?>">
                            <div class="icon-container <?= $notif['icon_class'] ?> mb-0 flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                <i class="fi <?= $notif['icon'] ?>"></i>
                            </div>
                            
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-white font-weight-600 fs-7"><?= htmlspecialchars($notif['title']) ?></span>
                                        <span class="badge bg-white bg-opacity-5 text-secondary fs-9" style="font-size:0.65rem;"><?= htmlspecialchars($notif['app']) ?></span>
                                    </div>
                                    <span class="fs-9 text-secondary"><?= $notif['time'] ?></span>
                                </div>
                                <p class="text-secondary fs-8 mb-0"><?= htmlspecialchars($notif['message']) ?></p>
                            </div>
                            
                            <button class="btn btn-link text-secondary p-1 fs-5 ms-3 border-0 bg-transparent hover-danger" onclick="clearSingleNotification(<?= $notif['id'] ?>)" title="Hapus Notifikasi">
                                <i class="fi fi-sr-lock" style="font-size: 1.1rem; color: #94a3b8; filter: grayscale(1);"></i>
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
            // Animasi fade out dan collapse
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '0';
            item.style.transform = 'translateX(20px)';
            
            setTimeout(() => {
                item.remove();
                
                // Cek jika tidak ada notifikasi lagi di UI
                const container = document.getElementById('notificationsContainer');
                const remaining = container.querySelectorAll('.notification-item');
                if (remaining.length === 0) {
                    showNoNotificationsView();
                }
                
                // Sync ke backend session via fetch
                const formData = new FormData();
                formData.append('clear_id', id);
                fetch('/notification', {
                    method: 'POST',
                    body: formData
                });
            }, 3000);
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
            
            // Sync ke backend session via fetch
            const formData = new FormData();
            formData.append('clear_all', '1');
            fetch('/notification', {
                method: 'POST',
                body: formData
            });
            
            // Remove clear all button
            const header = document.querySelector('.glass-card .d-flex');
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
    .hover-danger:hover i {
        color: #ef4444 !important;
    }
    .hover-danger span {
        display: inline-block;
        padding-left: 2px;
        font-weight: 700;
        vertical-align: middle;
    }
</style>
