<?php
// Developer page displaying specific listen sockets information
$sockets = $device->getListeningSockets();

// Default port suggestions guide
$default_categories = [
    [
        'name' => 'Aplikasi Web PHP',
        'port' => 'Port 8000 - 9000',
        'icon' => 'fi-sr-rectangle-code',
        'color' => 'icon-primary',
        'desc' => 'Disarankan menggunakan port kisaran 8000-an atau 9000-an untuk aplikasi web berbasis PHP (misalnya: php -S localhost:8000).'
    ],
    [
        'name' => 'Aplikasi Node.js',
        'port' => 'Port 3000 / 5000',
        'icon' => 'fi-sr-code-branch',
        'color' => 'icon-success',
        'desc' => 'Umumnya menggunakan port 3000 atau 5000 untuk server Express, NestJS, Next.js, atau aplikasi runtime JavaScript lainnya.'
    ],
    [
        'name' => 'Database MariaDB / MySQL',
        'port' => 'Port 3306 / 3606',
        'icon' => 'fi-sr-database',
        'color' => 'icon-info',
        'desc' => 'Sangat disarankan menggunakan port default 3306, atau port alternatif 3606 untuk menghindari konflik layanan database.'
    ],
    [
        'name' => 'Python Web Server',
        'port' => 'Port 8000 / 5000',
        'icon' => 'fi-sr-computer',
        'color' => 'icon-warning',
        'desc' => 'Biasanya berjalan pada port 8000 (Python http.server) atau port 5000 (Flask / FastAPI) saat dideploy.'
    ]
];
?>

<!-- Information for cloudflared tunnel -->
<div class="alert alert-primary bg-primary bg-opacity-10 border border-primary border-opacity-20 text-primary py-2.5 px-3 fs-8 mb-3 rounded-10 animated-fade-in" role="alert">
    <span><i class="fi fi-sr-info me-2 align-middle"></i>Gunakan <strong>port ini pada cloudflared tunnel</strong> jika ingin mengonlinekan aplikasi atau agar dapat diakses secara publik.</span>
</div>

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
                            <div class="icon-container icon-primary mb-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                <i class="fi fi-sr-network"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 border border-success border-opacity-25 text-success px-2 py-1 fs-9">
                                LISTEN (<?= strtoupper(htmlspecialchars($sock['protocol'])) ?>)
                            </span>
                        </div>
                        
                        <h5 class="text-white font-weight-700 mb-3 text-truncate" title="<?= htmlspecialchars($sock['process']) ?>"><?= htmlspecialchars($sock['process']) ?></h5>
                        
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
        <h5 class="mb-0 text-white font-weight-600">Rekomendasi Penggunaan Port Layanan</h5>
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
