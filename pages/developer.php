<?php
// Developer page displaying specific listen sockets information
$sockets = $device->getListeningSockets();

// Match common socket processes and apply categories/descriptions
foreach ($sockets as &$sock) {
    $name = strtolower($sock['process']);
    $port = '';
    
    // Extract port from local address (e.g. 0.0.0.0:3000 or :::3306 or [::]:3306)
    if (preg_match('/:(\d+)$/', $sock['local_address'], $matches)) {
        $port = $matches[1];
    }
    
    // Apply defaults
    $sock['category'] = 'System Process';
    $sock['desc'] = 'Layanan jaringan aktif pada perangkat.';
    $sock['color'] = 'icon-primary';
    $sock['icon'] = 'fi-sr-computer';

    // Grouping / Categorization
    if (strpos($name, 'node') !== false || $port === '3000' || $port === '5000') {
        $sock['process'] = 'Node.js App';
        $sock['category'] = 'Node.js Runtime';
        $sock['desc'] = 'Aplikasi server Javascript (Node.js) runtime.';
        $sock['color'] = 'icon-success';
        $sock['icon'] = 'fi-sr-code-branch';
    } elseif (strpos($name, 'python') !== false || $port === '8000') {
        $sock['process'] = 'Python Server';
        $sock['category'] = 'Python Runtime';
        $sock['desc'] = 'Aplikasi / skrip server Python runtime.';
        $sock['color'] = 'icon-primary';
        $sock['icon'] = 'fi-sr-code';
    } elseif (strpos($name, 'mysql') !== false || strpos($name, 'mariadb') !== false || strpos($name, 'mysqld') !== false || $port === '3306') {
        $sock['process'] = 'MariaDB Database';
        $sock['category'] = 'Database Server';
        $sock['desc'] = 'Sistem manajemen database relasional MariaDB/MySQL.';
        $sock['color'] = 'icon-info';
        $sock['icon'] = 'fi-sr-database';
    } elseif (strpos($name, 'redis') !== false || $port === '6379') {
        $sock['process'] = 'Redis Cache';
        $sock['category'] = 'Cache System';
        $sock['desc'] = 'Penyimpanan struktur data memori kunci-nilai Redis.';
        $sock['color'] = 'icon-danger';
        $sock['icon'] = 'fi-sr-database';
    } elseif (strpos($name, 'sshd') !== false || strpos($name, 'ssh') !== false || $port === '8022' || $port === '22') {
        $sock['process'] = 'SSH Remote Shell';
        $sock['category'] = 'Secure Access';
        $sock['desc'] = 'Secure Shell Daemon untuk login terminal jarak jauh.';
        $sock['color'] = 'icon-success';
        $sock['icon'] = 'fi-sr-lock';
    } elseif (strpos($name, 'ttyd') !== false || $port === '3001') {
        $sock['process'] = 'Web Terminal';
        $sock['category'] = 'Terminal Emulator';
        $sock['desc'] = 'Layanan web terminal emulator interaktif (ttyd).';
        $sock['color'] = 'icon-danger';
        $sock['icon'] = 'fi-sr-terminal';
    } elseif (strpos($name, 'php') !== false) {
        $sock['process'] = 'PHP Web Server';
        $sock['category'] = 'Web Server';
        $sock['desc'] = 'Layanan utama antarmuka pengguna web php.';
        $sock['color'] = 'icon-primary';
        $sock['icon'] = 'fi-sr-computer';
    } elseif (strpos($name, 'camera') !== false || strpos($name, 'ipcam') !== false || $port === '4444') {
        $sock['process'] = 'IP Camera Service';
        $sock['category'] = 'Media Server';
        $sock['desc'] = 'Layanan streaming kamera perangkat mobile.';
        $sock['color'] = 'icon-warning';
        $sock['icon'] = 'fi-sr-camera';
    }
}
unset($sock);

// Default category template definitions for reference guide
$default_categories = [
    [
        'name' => 'Node.js Application',
        'port' => 'Port 3000 / 5000',
        'icon' => 'fi-sr-code-branch',
        'color' => 'icon-success',
        'desc' => 'Dijalankan menggunakan node index.js atau pm2 start app.js'
    ],
    [
        'name' => 'Python Web Server',
        'port' => 'Port 8000 / 5000',
        'icon' => 'fi-sr-code',
        'color' => 'icon-primary',
        'desc' => 'Dijalankan menggunakan python -m http.server atau Flask/FastAPI'
    ],
    [
        'name' => 'MariaDB SQL Database',
        'port' => 'Port 3306',
        'icon' => 'fi-sr-database',
        'color' => 'icon-info',
        'desc' => 'Database SQL server, dijalankan lewat mariadb-safe atau mysqld'
    ],
    [
        'name' => 'Redis Cache Service',
        'port' => 'Port 6379',
        'icon' => 'fi-sr-database',
        'color' => 'icon-danger',
        'desc' => 'In-memory cache server, dijalankan menggunakan redis-server'
    ]
];
?>

<div class="alert alert-info bg-info bg-opacity-10 border border-info border-opacity-20 text-info py-2.5 px-3 fs-8 mb-4 rounded-10 animated-fade-in" role="alert">
    <span><i class="fi fi-sr-info me-2 align-middle"></i>Daftar port aktif yang dikelola oleh user (Hasil perintah: <code>ss -lptn</code>)</span>
</div>

<!-- Active Listening Sockets Section -->
<div class="row g-4 mb-5">
    <?php if (empty($sockets)): ?>
        <div class="col-12">
            <div class="glass-card text-center py-5">
                <i class="fi fi-sr-computer text-secondary fs-1 mb-2 opacity-50"></i>
                <p class="text-secondary mb-0">Tidak ada port socket mendengarkan yang aktif.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($sockets as $sock): ?>
            <div class="col-12 col-md-6 col-lg-4 animated-fade-in">
                <div class="glass-card h-100 d-flex flex-column justify-content-between p-4">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-container <?= htmlspecialchars($sock['color']) ?> mb-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                <i class="fi <?= htmlspecialchars($sock['icon']) ?>"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 border border-success border-opacity-25 text-success px-2 py-1 fs-9">
                                LISTEN (<?= strtoupper(htmlspecialchars($sock['protocol'])) ?>)
                            </span>
                        </div>
                        
                        <h5 class="text-white font-weight-700 mb-0"><?= htmlspecialchars($sock['process']) ?></h5>
                        <div class="fs-9 text-secondary font-weight-500 mb-2"><?= htmlspecialchars($sock['category']) ?></div>
                        <p class="text-secondary fs-8 mb-4"><?= htmlspecialchars($sock['desc']) ?></p>
                        
                        <div class="table-responsive">
                            <table class="table table-borderless text-white fs-8 mb-0">
                                <tbody>
                                    <tr class="border-bottom border-white border-opacity-5">
                                        <td class="text-secondary ps-0 py-1.5">Local Address</td>
                                        <td class="text-end pe-0 py-1.5 font-weight-500 text-info"><?= htmlspecialchars($sock['local_address']) ?></td>
                                    </tr>
                                    <tr class="border-bottom border-white border-opacity-5">
                                        <td class="text-secondary ps-0 py-1.5">Process PID</td>
                                        <td class="text-end pe-0 py-1.5 font-weight-500"><?= htmlspecialchars($sock['pid']) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary ps-0 py-1.5">Peer Address</td>
                                        <td class="text-end pe-0 py-1.5 font-weight-500 text-secondary"><?= htmlspecialchars($sock['peer_address']) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Category Templates Reference Guide Section -->
<div class="border-top border-white border-opacity-10 pt-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <i class="fi fi-sr-settings text-primary fs-5"></i>
        <h5 class="mb-0 text-white font-weight-600">Panduan Kategori Port Default (Termux)</h5>
    </div>
    
    <div class="row g-4">
        <?php foreach ($default_categories as $cat): ?>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="glass-card h-100 p-4" style="background: rgba(255,255,255, 0.01);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-container <?= htmlspecialchars($cat['color']) ?> mb-0" style="width: 40px; height: 40px; font-size: 1rem;">
                            <i class="fi <?= htmlspecialchars($cat['icon']) ?>"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-5 text-secondary px-2 py-0.5 fs-9" style="font-size:0.65rem;">
                            <?= htmlspecialchars($cat['port']) ?>
                        </span>
                    </div>
                    <h6 class="text-white font-weight-600 mb-1"><?= htmlspecialchars($cat['name']) ?></h6>
                    <p class="text-secondary fs-8 mb-0" style="line-height: 1.35;"><?= htmlspecialchars($cat['desc']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
