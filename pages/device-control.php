<?php
// Handle POST control actions
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $res = false;
    
    if ($action === 'reboot') {
        $res = $device->rebootDevice();
        if ($res) {
            $msg = 'Perangkat sedang memulai ulang... Server terputus.';
        } else {
            $error = 'Gagal mengirim perintah restart ke perangkat.';
        }
    } elseif ($action === 'shutdown') {
        $res = $device->shutdownDevice();
        if ($res) {
            $msg = 'Perangkat sedang dimatikan... Hubungan ke server terputus.';
        } else {
            $error = 'Gagal mengirim perintah shutdown ke perangkat.';
        }
    } elseif ($action === 'cache') {
        $res = $device->clearCache();
        if ($res) {
            $msg = 'Sistem Cache berhasil dibersihkan! RAM telah disegarkan.';
        } else {
            $error = 'Gagal membersihkan cache perangkat.';
        }
    }

    if (isset($_GET['api']) && $_GET['api'] == '1') {
        header('Content-Type: application/json');
        echo json_encode(['success' => $res, 'message' => $res ? $msg : $error]);
        exit;
    }
}
?>

<div class="row g-4">
    <!-- Reboot Card -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="glass-card text-center h-100 d-flex flex-column justify-content-between p-4">
            <div>
                <div class="icon-container icon-warning mx-auto mb-3">
                    <i class="fi fi-sr-redo"></i>
                </div>
                <h5 class="text-white font-weight-600 mb-2">Reboot Device</h5>
                <p class="text-secondary fs-7 mb-4">
                    Memulai ulang (restart) perangkat mobile secara penuh. Tindakan ini akan memutuskan koneksi server sementara waktu hingga sistem selesai memuat ulang.
                </p>
            </div>
            <button class="btn btn-warning w-100 py-2.5 rounded-10 font-weight-600 text-dark" onclick="confirmAction('reboot')">
                <i class="fi fi-sr-redo me-2 align-middle"></i> Restart Sekarang
            </button>
        </div>
    </div>

    <!-- Shutdown Card -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="glass-card text-center h-100 d-flex flex-column justify-content-between p-4">
            <div>
                <div class="icon-container icon-danger mx-auto mb-3">
                    <i class="fi fi-sr-power"></i>
                </div>
                <h5 class="text-white font-weight-600 mb-2">Shutdown Device</h5>
                <p class="text-secondary fs-7 mb-4">
                    Mematikan perangkat mobile sepenuhnya. Server web tidak akan dapat diakses sampai perangkat dinyalakan kembali secara manual.
                </p>
            </div>
            <button class="btn btn-danger w-100 py-2.5 rounded-10 font-weight-600" onclick="confirmAction('shutdown')">
                <i class="fi fi-sr-power me-2 align-middle"></i> Matikan Perangkat
            </button>
        </div>
    </div>

    <!-- Clear Cache Card -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="glass-card text-center h-100 d-flex flex-column justify-content-between p-4">
            <div>
                <div class="icon-container icon-success mx-auto mb-3">
                    <i class="fi fi-sr-shield-check"></i>
                </div>
                <h5 class="text-white font-weight-600 mb-2">Clear Cache</h5>
                <p class="text-secondary fs-7 mb-4">
                    Membersihkan file cache sistem, sisa aplikasi, dan penyimpanan sementara untuk mempercepat kinerja RAM dan performa perangkat.
                </p>
            </div>
            <button class="btn btn-success w-100 py-2.5 rounded-10 font-weight-600" onclick="confirmAction('cache')">
                <i class="fi fi-sr-shield-check me-2 align-middle"></i> Bersihkan Cache
            </button>
        </div>
    </div>
</div>

<!-- Simulation Loading Backdrop Overlay -->
<div id="controlBackdrop" class="position-fixed top-0 left-0 w-100 h-100 d-none justify-content-center align-items-center" style="z-index: 1060; background: rgba(11, 15, 25, 0.9); backdrop-filter: blur(10px);">
    <div class="text-center text-white animated-fade-in p-4" style="max-width: 400px;">
        <div class="spinner-border text-primary-gradient mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
        <h5 id="statusTitle" class="font-weight-600 mb-2">Memproses Tindakan...</h5>
        <p id="statusDesc" class="text-secondary fs-7 mb-0">Harap tunggu, instruksi sedang dikirim ke perangkat.</p>
    </div>
</div>

<!-- Action Toast notifications container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <div id="actionToast" class="toast align-items-center text-white border-0 glass-card bg-success bg-opacity-75" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fs-7" id="toastMessage">
                Cache berhasil dibersihkan!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    function confirmAction(type) {
        let title = '';
        let confirmText = '';
        let successMsg = '';
        
        switch(type) {
            case 'reboot':
                title = 'Reboot Perangkat?';
                confirmText = 'Apakah Anda yakin ingin memulai ulang perangkat? Server web akan offline sementara waktu.';
                successMsg = 'Perangkat sedang memulai ulang...';
                break;
            case 'shutdown':
                title = 'Matikan Perangkat?';
                confirmText = 'Apakah Anda yakin ingin mematikan perangkat? Server web akan mati sepenuhnya.';
                successMsg = 'Perangkat dinonaktifkan. Hubungan ke server terputus.';
                break;
            case 'cache':
                title = 'Bersihkan Cache?';
                confirmText = 'Bersihkan cache memori sementara perangkat sekarang?';
                successMsg = 'Sistem Cache berhasil dibersihkan!';
                break;
        }

        if (confirm(confirmText)) {
            // Show simulation overlay
            const backdrop = document.getElementById('controlBackdrop');
            const statusTitle = document.getElementById('statusTitle');
            const statusDesc = document.getElementById('statusDesc');
            
            backdrop.classList.remove('d-none');
            backdrop.classList.add('d-flex');
            
            statusTitle.textContent = 'Mengirim Perintah...';
            statusDesc.textContent = `Menghubungi perangkat untuk ${type === 'cache' ? 'clear cache' : type}...`;
            
            // Build Form Data
            let formData = new FormData();
            formData.append('action', type);
            
            const querySep = window.location.search ? '&' : '?';
            fetch(window.location.pathname + window.location.search + querySep + 'api=1', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                backdrop.classList.add('d-none');
                backdrop.classList.remove('d-flex');
                
                if (res.success) {
                    if (type === 'cache') {
                        const toastEl = document.getElementById('actionToast');
                        const toastMsg = document.getElementById('toastMessage');
                        toastMsg.textContent = res.message;
                        const toast = new bootstrap.Toast(toastEl);
                        toast.show();
                    } else {
                        alert(res.message);
                        window.location.href = 'index.php?page=login';
                    }
                } else {
                    alert('Gagal: ' + res.message);
                }
            })
            .catch(err => {
                backdrop.classList.add('d-none');
                backdrop.classList.remove('d-flex');
                alert('Gagal menghubungi server untuk mengeksekusi perintah.');
            });
        }
    }
</script>
