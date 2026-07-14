<?php
// Automatic URL/Port resolution based on access method
$hostname = $_SERVER['HTTP_HOST']; 
$host_only = parse_url('http://' . $hostname, PHP_URL_HOST);

// Check if accessing via IP
$is_ip = filter_var($host_only, FILTER_VALIDATE_IP) || 
         $host_only === 'localhost' || 
         $host_only === '127.0.0.1' || 
         preg_match('/^\d{1,3}(\.\d{1,3}){3}$/', $host_only);

$cam_port = db_get('ip_camera_port', '4444');
$cam_url = db_get('ip_camera_url', '');

$resolved_camera_url = '';
$mode_info = '';
$warning_alert = false;

if ($is_ip) {
    if (!empty($cam_port)) {
        $resolved_camera_url = 'http://' . $host_only . ':' . $cam_port;
        $mode_info = "Mengakses via IP: Otomatis menggunakan PORT lokal ({$cam_port})";
    } elseif (!empty($cam_url)) {
        $resolved_camera_url = $cam_url;
        $mode_info = "Mengakses via IP: Port lokal belum diatur, beralih ke URL Domain";
    } else {
        $warning_alert = true;
    }
} else {
    if (!empty($cam_url)) {
        $resolved_camera_url = $cam_url;
        $mode_info = "Mengakses via Domain: Otomatis menggunakan URL kustom";
    } elseif (!empty($cam_port)) {
        $resolved_camera_url = 'http://' . $host_only . ':' . $cam_port;
        $mode_info = "Mengakses via Domain: URL belum diatur, beralih ke PORT lokal ({$cam_port})";
    } else {
        $warning_alert = true;
    }
}
?>

<div class="row g-4">
    <!-- Warning Card (Hidden by default, shown by JS if no config is available) -->
    <div class="col-12 d-none" id="camWarningCard">
        <div class="glass-card text-center py-5">
            <div class="icon-container icon-warning mx-auto mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                <i class="fi fi-sr-camera"></i>
            </div>
            <h5 class="text-white font-weight-600 mb-2">IP Camera Belum Dikonfigurasi</h5>
            <p class="text-secondary fs-8 mb-4 mx-auto" style="max-width: 420px;">
                Konfigurasi Port atau URL Domain IP Camera belum diatur. Silakan buka halaman System Settings terlebih dahulu untuk mengaturnya.
            </p>
            <a href="index.php?page=settings" class="btn btn-primary-gradient px-4 py-2.5 rounded-10 text-white font-weight-600">
                Buka System Settings
            </a>
        </div>
    </div>

    <!-- Connection Mode Information Banner (Shown by JS) -->
    <div class="col-12 mb-0 d-none" id="camAlert">
        <div class="alert alert-info bg-info bg-opacity-10 border border-info border-opacity-20 text-info py-2.5 px-3 fs-8 mb-0 d-flex flex-wrap gap-2 align-items-center justify-content-between rounded-10" role="alert">
            <div>
                <i class="fi fi-sr-info me-2 align-middle"></i>
                <span id="camModeText">Mengakses...</span>
                <span class="badge bg-info text-white font-weight-500 fs-9 ms-2" id="camUrlBadge"></span>
            </div>
            <!-- Switch mode button inside alert -->
            <button class="btn btn-sm btn-outline-info py-1 px-2.5 rounded-8 fs-9" onclick="toggleCameraMode()" id="btnSwitchCamMode">
                Ganti Mode
            </button>
        </div>
    </div>

    <!-- Camera Stream Embed & Controls (16:9 Landscape - Full Width) -->
    <div class="col-12 d-none" id="camStreamCard">
        <div class="glass-card">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fi fi-sr-camera text-primary fs-5"></i>
                    <h5 class="mb-0 text-white font-weight-600">Aliran Live IP Camera</h5>
                </div>
                <span class="badge bg-success bg-opacity-10 border border-success border-opacity-30 text-success px-3 py-1.5 rounded-pill fs-8">
                    LIVE EMBED
                </span>
            </div>

            <!-- 16:9 Aspect Ratio Frame Container -->
            <div class="embed-16-9 mb-4" id="shutterFrame">
                <iframe src="" id="ipCamIframe" allow="autoplay; encrypted-media"></iframe>
                <!-- Shutter flash overlay effect -->
                <div class="shutter-flash" id="shutterFlash"></div>
            </div>

            <!-- Camera Operations -->
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-3">
                <button class="btn btn-primary-gradient py-2.5 px-4 rounded-10 font-weight-600 d-flex align-items-center gap-2" onclick="captureIpCamPhoto()">
                    <i class="fi fi-sr-camera fs-6"></i>
                    <span>Ambil Foto (Jepret)</span>
                </button>
                <button class="btn btn-outline-light border-white border-opacity-10 py-2.5 px-3 rounded-10 fs-8 text-white" onclick="reloadStream()">
                    <i class="fi fi-sr-redo me-1"></i> Muat Ulang Aliran
                </button>
                <!-- Open in New Tab Link -->
                <a href="" id="btnOpenNewTab" target="_blank" class="btn btn-outline-info border-info border-opacity-30 py-2.5 px-3 rounded-10 fs-8 text-info" style="text-decoration:none;">
                    <i class="fi fi-sr-leave me-1" style="transform: scaleX(-1);"></i> Buka di Tab Baru
                </a>
            </div>

            <!-- Hidden canvas used to generate mock photo based on simulated camera feed -->
            <canvas id="mock-canvas" class="d-none" width="1280" height="720"></canvas>
        </div>
    </div>

    <!-- Capture Preview Section (Replaced gallery grid with single large image box and location path info) -->
    <div class="col-12 d-none" id="camPreviewCardSection">
        <div class="glass-card" id="latestCaptureCard" style="display:none;">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fi fi-sr-folder text-success fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Hasil Jepretan Terakhir</h5>
            </div>
            
            <div class="row g-4 align-items-center">
                <div class="col-12 col-md-7">
                    <!-- Large image box (16:9 Landscape) -->
                    <div class="position-relative overflow-hidden rounded-12 border border-white border-opacity-10" style="aspect-ratio: 16/9; background: #000;">
                        <img id="latestCaptureImg" src="" class="w-100 h-100 img-fluid" style="object-fit: cover;">
                    </div>
                </div>
                
                <div class="col-12 col-md-5">
                    <h6 class="text-white font-weight-600 mb-3"><i class="fi fi-sr-info text-info me-2"></i>Informasi Penyimpanan File</h6>
                    <div class="table-responsive">
                        <table class="table table-borderless text-white mb-0 align-middle fs-8">
                            <tbody>
                                <tr class="border-bottom border-white border-opacity-5">
                                    <td class="text-secondary ps-0 py-2">Nama File</td>
                                    <td class="text-end pe-0 py-2 font-weight-600 text-info" id="imgMetaName">-</td>
                                </tr>
                                <tr class="border-bottom border-white border-opacity-5">
                                    <td class="text-secondary ps-0 py-2">Waktu Ambil</td>
                                    <td class="text-end pe-0 py-2 font-weight-500 text-white" id="imgMetaTime">-</td>
                                </tr>
                                <tr class="border-bottom border-white border-opacity-5">
                                    <td class="text-secondary ps-0 py-2">Resolusi</td>
                                    <td class="text-end pe-0 py-2 font-weight-500 text-white">1280 x 720 (16:9)</td>
                                </tr>
                                <tr class="border-bottom border-white border-opacity-5">
                                    <td class="text-secondary ps-0 py-2">Lokasi Perangkat</td>
                                    <td class="text-end pe-0 py-2 font-weight-500 text-warning" style="word-break: break-all;" id="imgMetaDevicePath">-</td>
                                </tr>
                                <tr>
                                    <td class="text-secondary ps-0 py-2">Lokasi Server</td>
                                    <td class="text-end pe-0 py-2 font-weight-500 text-secondary" style="word-break: break-all;" id="imgMetaServerPath">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Placeholder before capture -->
        <div id="latestCapturePlaceholder" class="glass-card text-center py-5">
            <i class="fi fi-sr-folder text-secondary fs-1 mb-2 opacity-50"></i>
            <p class="text-secondary mb-0">Belum ada foto yang dijepret. Klik tombol <strong>Ambil Foto (Jepret)</strong> di atas untuk memotret.</p>
        </div>
    </div>
</div>

<style>
    /* 16:9 Landscape Aspect Ratio Box */
    .embed-16-9 {
        position: relative;
        width: 100%;
        padding-top: 56.25%; /* 16:9 aspect ratio */
        overflow: hidden;
        border-radius: 12px;
        border: 1px solid var(--glass-border);
        background: #000;
    }
    
    .embed-16-9 iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        object-fit: cover;
    }

    /* Camera Flash Shutter Animation */
    .shutter-flash {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: white;
        opacity: 0;
        pointer-events: none;
        z-index: 10;
        transition: opacity 0.1s ease;
    }
    
    .shutter-flash.flash-active {
        animation: shutterFlashAnim 0.35s ease-out;
    }
    
    @keyframes shutterFlashAnim {
        0% { opacity: 0; }
        15% { opacity: 1; }
        100% { opacity: 0; }
    }
</style>

<script>
    let resolvedCameraUrl = '';

    document.addEventListener('DOMContentLoaded', () => {
        const hostOnly = <?= json_encode($host_only) ?>;
        const camPort = <?= json_encode($cam_port) ?>;
        const camUrl = <?= json_encode($cam_url) ?>;
        const isIpDefault = <?= $is_ip ? 'true' : 'false' ?>;

        if (!camPort && !camUrl) {
            document.getElementById('camWarningCard').classList.remove('d-none');
            return;
        }

        // Check if override exists in sessionStorage
        let currentMode = sessionStorage.getItem('camera_mode_override');
        if (!currentMode) {
            currentMode = isIpDefault ? 'port' : 'url';
        }

        let modeText = '';
        let btnText = '';

        if (currentMode === 'port') {
            if (camPort) {
                resolvedCameraUrl = 'http://' + hostOnly + ':' + camPort;
                modeText = `Mengakses via IP: Otomatis menggunakan PORT lokal (${camPort})`;
                btnText = 'Ganti ke Mode URL Domain';
            } else {
                resolvedCameraUrl = camUrl;
                modeText = 'Mengakses via IP: Port lokal belum diatur, beralih ke URL Domain';
                btnText = 'Ganti ke Mode PORT';
            }
        } else { // 'url' mode
            if (camUrl) {
                resolvedCameraUrl = camUrl;
                modeText = 'Mengakses via Domain: Otomatis menggunakan URL kustom';
                btnText = 'Ganti ke Mode PORT lokal';
            } else {
                resolvedCameraUrl = 'http://' + hostOnly + ':' + camPort;
                modeText = `Mengakses via Domain: URL belum diatur, beralih ke PORT lokal (${camPort})`;
                btnText = 'Ganti ke Mode URL Domain';
            }
        }

        // Apply URL and display elements
        document.getElementById('camModeText').textContent = modeText;
        document.getElementById('camUrlBadge').textContent = resolvedCameraUrl;
        document.getElementById('btnSwitchCamMode').textContent = btnText;
        document.getElementById('ipCamIframe').src = resolvedCameraUrl;
        document.getElementById('btnOpenNewTab').href = resolvedCameraUrl;

        document.getElementById('camAlert').classList.remove('d-none');
        document.getElementById('camStreamCard').classList.remove('d-none');
        document.getElementById('camPreviewCardSection').classList.remove('d-none');
    });

    function toggleCameraMode() {
        const isIpDefault = <?= $is_ip ? 'true' : 'false' ?>;
        const current = sessionStorage.getItem('camera_mode_override') || (isIpDefault ? 'port' : 'url');
        const target = current === 'port' ? 'url' : 'port';
        sessionStorage.setItem('camera_mode_override', target);
        window.location.reload();
    }

    function reloadStream() {
        const iframe = document.getElementById('ipCamIframe');
        if (iframe && resolvedCameraUrl) {
            iframe.src = resolvedCameraUrl;
        }
    }

    function captureIpCamPhoto() {
        const flash = document.getElementById('shutterFlash');
        if (flash) {
            flash.classList.add('flash-active');
            setTimeout(() => {
                flash.classList.remove('flash-active');
            }, 400);
        }

        const canvas = document.getElementById('mock-canvas');
        const ctx = canvas.getContext('2d');
        
        const grad = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
        grad.addColorStop(0, '#1e293b');
        grad.addColorStop(0.5, '#0f172a');
        grad.addColorStop(1, '#020617');
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.05)';
        ctx.lineWidth = 1;
        ctx.beginPath();
        for (let i = 100; i < canvas.width; i += 100) {
            ctx.moveTo(i, 0); ctx.lineTo(i, canvas.height);
        }
        for (let j = 100; j < canvas.height; j += 100) {
            ctx.moveTo(0, j); ctx.lineTo(canvas.width, j);
        }
        ctx.stroke();

        ctx.strokeStyle = '#ef4444';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(canvas.width/2 - 20, canvas.height/2); ctx.lineTo(canvas.width/2 + 20, canvas.height/2);
        ctx.moveTo(canvas.width/2, canvas.height/2 - 20); ctx.lineTo(canvas.width/2, canvas.height/2 + 20);
        ctx.stroke();

        const pad = 40;
        const len = 50;
        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth = 4;
        ctx.beginPath();
        ctx.moveTo(pad, pad + len); ctx.lineTo(pad, pad); ctx.lineTo(pad + len, pad);
        ctx.moveTo(canvas.width - pad - len, pad); ctx.lineTo(canvas.width - pad, pad); ctx.lineTo(canvas.width - pad, pad + len);
        ctx.moveTo(pad, canvas.height - pad - len); ctx.lineTo(pad, canvas.height - pad); ctx.lineTo(pad + len, canvas.height - pad);
        ctx.moveTo(canvas.width - pad - len, canvas.height - pad); ctx.lineTo(canvas.width - pad, canvas.height - pad); ctx.lineTo(canvas.width - pad, canvas.height - pad - len);
        ctx.stroke();

        ctx.fillStyle = '#ffffff';
        ctx.font = '28px Outfit';
        ctx.fillText('IP CAMERA SNAPSHOT', 60, 80);
        
        ctx.fillStyle = '#34d399';
        ctx.font = '20px monospace';
        ctx.fillText('REC ●', 60, 120);

        ctx.fillStyle = '#94a3b8';
        ctx.font = '20px monospace';
        const now = new Date();
        ctx.fillText(`TIME: ${now.toLocaleDateString()} ${now.toLocaleTimeString()}`, 60, canvas.height - 60);
        ctx.fillText(`CAM_ID: IP-CAM-01`, canvas.width - 240, 80);

        const dataUrl = canvas.toDataURL('image/jpeg');
        updateLatestCapture(dataUrl);
    }

    function updateLatestCapture(dataUrl) {
        // Hide placeholder and show card
        document.getElementById('latestCapturePlaceholder').classList.add('d-none');
        document.getElementById('latestCaptureCard').style.display = 'block';
        
        // Update image
        const img = document.getElementById('latestCaptureImg');
        img.src = dataUrl;
        
        // Generate filenames and paths
        const now = new Date();
        const timestamp = now.getTime();
        const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ' ' + now.toLocaleTimeString('id-ID');
        
        const fileName = `IMG_${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}${String(now.getDate()).padStart(2,'0')}_${timestamp.toString().slice(-6)}.jpg`;
        const devicePath = `/storage/emulated/0/Pictures/MobileServer/${fileName}`;
        const serverPath = `D:\\mobile-server\\assets\\uploads\\snapshots\\${fileName}`;
        
        // Update metadata
        document.getElementById('imgMetaName').textContent = fileName;
        document.getElementById('imgMetaTime').textContent = dateStr;
        document.getElementById('imgMetaDevicePath').textContent = devicePath;
        document.getElementById('imgMetaServerPath').textContent = serverPath;
    }
</script>
