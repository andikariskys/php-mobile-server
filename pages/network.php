<?php
$success_msg = '';
$error_msg = '';

// Handle AJAX Network toggle commands
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'toggle_network') {
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $enabled = isset($_POST['enabled']) && $_POST['enabled'] == '1';
        
        $res = false;
        switch($type) {
            case 'wifi':
                $res = $device->toggleWifi($enabled);
                break;
            case 'mobileData':
                $res = $device->toggleMobileData($enabled);
                break;
            case 'airplane':
                $res = $device->toggleAirplaneMode($enabled);
                break;
            case 'bluetooth':
                $res = $device->toggleBluetooth($enabled);
                break;
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $res, 'type' => $type, 'enabled' => $enabled]);
        exit;
    }
    
    if ($_POST['action'] === 'toggle_gps') {
        $enabled = isset($_POST['enabled']) && $_POST['enabled'] == '1';
        $res = $device->toggleGps($enabled);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $res, 'type' => 'gps', 'enabled' => $enabled]);
        exit;
    }
}

// Handle Add Static IP POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_ip') {
    $ip = isset($_POST['ip_address']) ? trim($_POST['ip_address']) : '';
    $prefix = isset($_POST['subnet_mask']) ? trim($_POST['subnet_mask']) : '24';
    
    // Execute directly and DO NOT save to database!
    $res = $device->addStaticIp($ip, $prefix);
    if ($res) {
        $_SESSION['net_success'] = "Berhasil mengeksekusi penambahan IP Statis: $ip/$prefix pada wlan0!";
    } else {
        $_SESSION['net_error'] = "Gagal menambahkan IP Statis pada dev wlan0.";
    }
    
    header("Location: index.php?page=network");
    exit;
}

if (isset($_SESSION['net_success'])) {
    $success_msg = $_SESSION['net_success'];
    unset($_SESSION['net_success']);
}
if (isset($_SESSION['net_error'])) {
    $error_msg = $_SESSION['net_error'];
    unset($_SESSION['net_error']);
}

// Load real network status from DeviceController
$netDetails = $device->getNetworkDetails();
$wifiStatus = $netDetails['wifi_status'];
$wifiSsid = $netDetails['wifi_ssid'];
$mobileDataStatus = $netDetails['mobile_data_status'];
$airplaneMode = $netDetails['airplane_mode'];
$bluetoothStatus = $netDetails['bluetooth_status'];
$interfaces = $netDetails['interfaces'];

// GPS Info
$gpsEnabled = $device->getGpsStatus();
$gpsChecked = $gpsEnabled ? 'checked' : '';
$gpsLabelClass = $gpsEnabled ? 'text-success' : 'text-secondary';

// Formulate statuses and checked attributes
$wifiChecked = ($wifiStatus === 'Connected' || strpos($wifiStatus, 'Enabled') !== false) ? 'checked' : '';
$wifiLabel = ($wifiStatus === 'Connected') ? "Enabled (Connected to '{$wifiSsid}')" : $wifiStatus;
$wifiLabelClass = ($wifiStatus === 'Connected' || strpos($wifiStatus, 'Enabled') !== false) ? 'text-success' : 'text-secondary';

$mobileChecked = ($mobileDataStatus === 'Enabled') ? 'checked' : '';
$mobileLabelClass = ($mobileDataStatus === 'Enabled') ? 'text-success' : 'text-secondary';

$airplaneChecked = ($airplaneMode === 'Enabled') ? 'checked' : '';
$airplaneLabelClass = ($airplaneMode === 'Enabled') ? 'text-danger' : 'text-secondary';

$bluetoothChecked = ($bluetoothStatus === 'Enabled') ? 'checked' : '';
$bluetoothLabelClass = ($bluetoothStatus === 'Enabled') ? 'text-success' : 'text-secondary';
?>

<?php if (!empty($success_msg)): ?>
    <div class="alert alert-success bg-success bg-opacity-20 border border-success border-opacity-30 text-success rounded-10 py-2.5 px-3 fs-7 mb-4 animated-fade-in" role="alert">
        <i class="fi fi-sr-shield-check me-2 align-middle"></i> <?= $success_msg ?>
    </div>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger bg-danger bg-opacity-20 border border-danger border-opacity-30 text-danger rounded-10 py-2.5 px-3 fs-7 mb-4 animated-fade-in" role="alert">
        <i class="fi fi-sr-info me-2 align-middle"></i> <?= $error_msg ?>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Network Interfaces Status -->
    <div class="col-12 col-lg-5">
        <!-- Connectivity switches -->
        <div class="glass-card mb-4">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fi fi-sr-wifi text-primary fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Kontrol Konektivitas</h5>
            </div>

            <!-- WiFi Toggle -->
            <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 mb-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-container icon-primary mb-0" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fi fi-sr-wifi" id="wifiIcon"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-0">Wi-Fi</h6>
                        <span class="fs-8 <?= $wifiLabelClass ?>" id="wifiStatusText"><?= htmlspecialchars($wifiLabel) ?></span>
                    </div>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="wifiSwitch" <?= $wifiChecked ?> onchange="toggleNetwork('wifi')">
                </div>
            </div>

            <!-- Mobile Data Toggle -->
            <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 mb-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-container icon-success mb-0" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fi fi-sr-signal-alt" id="mobileDataIcon"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-0">Data Seluler</h6>
                        <span class="fs-8 <?= $mobileLabelClass ?>" id="mobileDataStatusText"><?= htmlspecialchars($mobileDataStatus) ?></span>
                    </div>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="mobileDataSwitch" <?= $mobileChecked ?> onchange="toggleNetwork('mobileData')">
                </div>
            </div>

            <!-- Airplane Mode Toggle -->
            <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 mb-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-container icon-danger mb-0" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fi fi-sr-plane" id="airplaneIcon" style="transform: rotate(45deg);"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-0">Mode Pesawat</h6>
                        <span class="fs-8 <?= $airplaneLabelClass ?>" id="airplaneStatusText"><?= htmlspecialchars($airplaneMode) ?></span>
                    </div>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="airplaneSwitch" <?= $airplaneChecked ?> onchange="toggleNetwork('airplane')">
                </div>
            </div>

            <!-- Bluetooth Toggle -->
            <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 mb-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-container icon-info mb-0" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fi fi-sr-bluetooth" id="bluetoothIcon"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-0">Bluetooth</h6>
                        <span class="fs-8 <?= $bluetoothLabelClass ?>" id="bluetoothStatusText"><?= htmlspecialchars($bluetoothStatus) ?></span>
                    </div>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="bluetoothSwitch" <?= $bluetoothChecked ?> onchange="toggleNetwork('bluetooth')">
                </div>
            </div>

            <!-- GPS Toggle -->
            <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 mb-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-container icon-warning mb-0" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fi fi-sr-marker" id="gpsIcon"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-0">GPS Lokasi</h6>
                        <span class="fs-8 <?= $gpsLabelClass ?>" id="gpsStatusText"><?= $gpsEnabled ? 'Enabled' : 'Disabled' ?></span>
                    </div>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="gpsSwitch" <?= $gpsChecked ?> onchange="toggleGps()">
                </div>
            </div>
        </div>

        <!-- Network Interface & IP Table Card -->
        <div class="glass-card">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fi fi-sr-computer text-info fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Daftar Interface Network</h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-borderless text-white mb-0 align-middle">
                    <thead>
                        <tr class="border-bottom border-white border-opacity-10 text-secondary fs-8">
                            <th class="ps-0 py-2">Interface</th>
                            <th class="pe-0 py-2 text-end">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="fs-8">
                        <?php if (empty($interfaces)): ?>
                            <tr>
                                <td colspan="2" class="text-center py-3 text-secondary">Tidak ada interface aktif.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($interfaces as $if): ?>
                                <tr class="border-bottom border-white border-opacity-5">
                                    <td class="ps-0 py-2.5 font-weight-600 text-info"><?= htmlspecialchars($if['name']) ?></td>
                                    <td class="pe-0 py-2.5 text-end">
                                        <div class="text-white"><?= htmlspecialchars($if['ip']) ?></div>
                                        <div class="text-secondary fs-9" style="font-size: 0.725rem;">Down: <?= htmlspecialchars($if['rx_formatted']) ?> &nbsp;|&nbsp; Up: <?= htmlspecialchars($if['tx_formatted']) ?></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Static IP Configuration Form (Not Saved to DB) -->
    <div class="col-12 col-lg-7">
        <div class="glass-card">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fi fi-sr-settings text-primary fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Tambah IP Statis (Temporary wlan0)</h5>
            </div>
            
            <form id="ipForm" action="index.php?page=network" method="POST">
                <input type="hidden" name="action" value="save_ip">
                
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label text-secondary fs-7 ms-1">IP Address</label>
                        <input type="text" name="ip_address" class="form-control form-glass" value="" placeholder="Contoh: 192.168.43.1" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-secondary fs-7 ms-1">Panjang Prefix (CIDR)</label>
                    <input type="text" name="subnet_mask" class="form-control form-glass" value="24" placeholder="Contoh: 24" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                    <div class="text-secondary fs-8" style="max-width: 60%;">
                        <i class="fi fi-sr-info me-1 align-middle"></i> Menambahkan alamat IP langsung ke perangkat interface wlan0 secara runtime tanpa disimpan di database.
                    </div>
                    <button type="submit" class="btn btn-primary-gradient px-4 py-2.5 rounded-10 text-white font-weight-600">
                        Tambah IP Statis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Network Toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <div id="netToast" class="toast align-items-center text-white border-0 glass-card bg-success bg-opacity-75" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fs-7" id="netToastMessage">
                Berhasil diproses!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    function toggleGps() {
        const switchEl = document.getElementById('gpsSwitch');
        const statusEl = document.getElementById('gpsStatusText');
        const isChecked = switchEl.checked;
        
        statusEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" style="width:10px; height:10px;"></span> Memproses...';
        switchEl.disabled = true;
        
        let formData = new FormData();
        formData.append('action', 'toggle_gps');
        formData.append('enabled', isChecked ? '1' : '0');
        
        const querySep = window.location.search ? '&' : '?';
        fetch(window.location.pathname + window.location.search + querySep + 'api=1', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            switchEl.disabled = false;
            if (res.success) {
                statusEl.textContent = res.enabled ? 'Enabled' : 'Disabled';
                statusEl.className = res.enabled ? 'fs-8 text-success' : 'fs-8 text-secondary';
                showToast(`GPS berhasil ${res.enabled ? 'diaktifkan' : 'dinonaktifkan'}.`);
            } else {
                switchEl.checked = !isChecked;
                statusEl.textContent = !isChecked ? 'Enabled' : 'Disabled';
                statusEl.className = !isChecked ? 'fs-8 text-success' : 'fs-8 text-secondary';
                showToast('Gagal mengubah status GPS.');
            }
        })
        .catch(err => {
            switchEl.disabled = false;
            switchEl.checked = !isChecked;
            statusEl.textContent = 'Gagal';
            statusEl.className = 'fs-8 text-danger';
            showToast('Koneksi server terputus.');
        });
    }

    function toggleNetwork(type) {
        const switchEl = document.getElementById(`${type}Switch`);
        const statusEl = document.getElementById(`${type}StatusText`);
        const isChecked = switchEl.checked;
        
        statusEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" style="width:10px; height:10px;"></span> Memproses...';
        switchEl.disabled = true;
        
        let formData = new FormData();
        formData.append('action', 'toggle_network');
        formData.append('type', type);
        formData.append('enabled', isChecked ? '1' : '0');
        
        const querySep = window.location.search ? '&' : '?';
        fetch(window.location.pathname + window.location.search + querySep + 'api=1', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            switchEl.disabled = false;
            if (res.success) {
                if (type === 'wifi') {
                    if (res.enabled) {
                        statusEl.textContent = 'Enabled';
                        statusEl.className = 'fs-8 text-success';
                    } else {
                        statusEl.textContent = 'Disabled';
                        statusEl.className = 'fs-8 text-secondary';
                    }
                } else if (type === 'mobileData') {
                    if (res.enabled) {
                        statusEl.textContent = 'Enabled';
                        statusEl.className = 'fs-8 text-success';
                    } else {
                        statusEl.textContent = 'Disabled';
                        statusEl.className = 'fs-8 text-secondary';
                    }
                } else if (type === 'airplane') {
                    if (res.enabled) {
                        statusEl.textContent = 'Enabled';
                        statusEl.className = 'fs-8 text-danger';
                        
                        document.getElementById('wifiSwitch').checked = false;
                        document.getElementById('wifiStatusText').textContent = 'Disabled';
                        document.getElementById('wifiStatusText').className = 'fs-8 text-secondary';
                        
                        document.getElementById('mobileDataSwitch').checked = false;
                        document.getElementById('mobileDataStatusText').textContent = 'Disabled';
                        document.getElementById('mobileDataStatusText').className = 'fs-8 text-secondary';
                    } else {
                        statusEl.textContent = 'Disabled';
                        statusEl.className = 'fs-8 text-secondary';
                    }
                } else if (type === 'bluetooth') {
                    if (res.enabled) {
                        statusEl.textContent = 'Enabled (Idle)';
                        statusEl.className = 'fs-8 text-success';
                    } else {
                        statusEl.textContent = 'Disabled';
                        statusEl.className = 'fs-8 text-secondary';
                    }
                }
                showToast(`Status ${type.toUpperCase()} berhasil diubah.`);
            } else {
                switchEl.checked = !isChecked; // Revert switch position
                statusEl.textContent = isChecked ? 'Disabled' : 'Enabled';
                statusEl.className = isChecked ? 'fs-8 text-secondary' : 'fs-8 text-success';
                showToast(`Gagal mengubah status ${type.toUpperCase()}.`);
            }
        })
        .catch(err => {
            switchEl.disabled = false;
            switchEl.checked = !isChecked; // Revert
            statusEl.textContent = 'Gagal menghubungi server';
            statusEl.className = 'fs-8 text-danger';
            showToast('Koneksi server terputus.');
        });
    }

    function showToast(msg) {
        const toastEl = document.getElementById('netToast');
        const toastMsg = document.getElementById('netToastMessage');
        toastMsg.textContent = msg;
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
</script>
