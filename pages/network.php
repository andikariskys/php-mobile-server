<?php
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_ip') {
    $ip = isset($_POST['ip_address']) ? trim($_POST['ip_address']) : '';
    $subnet = isset($_POST['subnet_mask']) ? trim($_POST['subnet_mask']) : '';
    $use_prefix = isset($_POST['use_prefix']) ? '1' : '0';
    
    db_set('ip_address', $ip);
    db_set('subnet_mask', $subnet);
    db_set('use_prefix', $use_prefix);
    
    $_SESSION['net_success'] = 'Konfigurasi IP Statis berhasil disimpan!';
    header("Location: /network");
    exit;
}

if (isset($_SESSION['net_success'])) {
    $success_msg = $_SESSION['net_success'];
    unset($_SESSION['net_success']);
}

// Load config from SQLite
$ip_val = db_get('ip_address', '192.168.1.100');
$subnet_val = db_get('subnet_mask', '255.255.255.0');
$use_prefix_val = db_get('use_prefix', '0');
?>

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
                        <span class="fs-8 text-success" id="wifiStatusText">Enabled (Connected to 'Antigravity_5G')</span>
                    </div>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="wifiSwitch" checked onchange="toggleNetwork('wifi')">
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
                        <span class="fs-8 text-secondary" id="mobileDataStatusText">Disabled (Wi-Fi Active)</span>
                    </div>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="mobileDataSwitch" onchange="toggleNetwork('mobileData')">
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
                        <span class="fs-8 text-secondary" id="airplaneStatusText">Disabled</span>
                    </div>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="airplaneSwitch" onchange="toggleNetwork('airplane')">
                </div>
            </div>

            <!-- Bluetooth Toggle -->
            <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 mb-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-container icon-info mb-0" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fi fi-sr-bluetooth" id="bluetoothIcon"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-0">Bluetooth</h6>
                        <span class="fs-8 text-success" id="bluetoothStatusText">Enabled (Idle)</span>
                    </div>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="bluetoothSwitch" checked onchange="toggleNetwork('bluetooth')">
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
                        <tr class="border-bottom border-white border-opacity-5">
                            <td class="ps-0 py-2.5 font-weight-600 text-info">lo</td>
                            <td class="pe-0 py-2.5 text-end text-secondary">127.0.0.1/8</td>
                        </tr>
                        <tr class="border-bottom border-white border-opacity-5">
                            <td class="ps-0 py-2.5 font-weight-600 text-info">wlan0</td>
                            <td class="pe-0 py-2.5 text-end">
                                <div class="text-white">192.168.43.99/24</div>
                                <div class="text-secondary fs-9">192.168.43.1/24</div>
                            </td>
                        </tr>
                        <tr class="border-bottom border-white border-opacity-5">
                            <td class="ps-0 py-2.5 font-weight-600 text-info">rmnet_data0</td>
                            <td class="pe-0 py-2.5 text-end text-secondary">10.114.97.52/29</td>
                        </tr>
                        <tr>
                            <td class="ps-0 py-2.5 font-weight-600 text-info">ap0</td>
                            <td class="pe-0 py-2.5 text-end text-secondary">192.168.44.1/24</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Static IP Configuration -->
    <div class="col-12 col-lg-7">
        <div class="glass-card">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fi fi-sr-settings text-primary fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Konfigurasi IP Statis</h5>
            </div>
            
            <form id="ipForm" action="/network" method="POST">
                <input type="hidden" name="action" value="save_ip">
                
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label text-secondary fs-7 ms-1">IP Address</label>
                        <input type="text" name="ip_address" class="form-control form-glass" value="<?= htmlspecialchars($ip_val) ?>" placeholder="192.168.1.XX" required>
                    </div>
                </div>

                <!-- Toggle Prefix Switch -->
                <div class="form-check form-switch mb-3 ms-1">
                    <input class="form-check-input" type="checkbox" role="switch" id="togglePrefix" name="use_prefix" value="1" <?= $use_prefix_val === '1' ? 'checked' : '' ?> onchange="togglePrefixMode()">
                    <label class="form-check-label text-secondary fs-7" for="togglePrefix">Gunakan Panjang Prefix (CIDR) alih-alih Subnet Mask</label>
                </div>

                <!-- Subnet Mask or Prefix Input Field -->
                <div class="mb-4">
                    <label id="subnetLabel" for="subnetMaskInput" class="form-label text-secondary fs-7 ms-1">
                        <?= $use_prefix_val === '1' ? 'Panjang Prefix (CIDR)' : 'Subnet Mask' ?>
                    </label>
                    <input type="text" id="subnetMaskInput" name="subnet_mask" class="form-control form-glass" value="<?= htmlspecialchars($subnet_val) ?>" placeholder="<?= $use_prefix_val === '1' ? 'Contoh: 24' : '255.255.255.0' ?>" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                    <div class="text-secondary fs-8">
                        <i class="fi fi-sr-info me-1 align-middle"></i> Pengaturan IP disimpan di database server.
                    </div>
                    <button type="submit" class="btn btn-primary-gradient px-4 py-2.5 rounded-10 text-white font-weight-600">
                        Simpan Konfigurasi
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
                Konfigurasi berhasil disimpan!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    <?php if (!empty($success_msg)): ?>
    document.addEventListener('DOMContentLoaded', () => {
        showToast(<?= json_encode($success_msg) ?>);
    });
    <?php endif; ?>

    function togglePrefixMode() {
        const toggle = document.getElementById('togglePrefix');
        const label = document.getElementById('subnetLabel');
        const input = document.getElementById('subnetMaskInput');
        
        if (toggle.checked) {
            label.textContent = 'Panjang Prefix (CIDR)';
            input.placeholder = 'Contoh: 24';
            if (input.value.includes('.')) {
                input.value = '24';
            }
        } else {
            label.textContent = 'Subnet Mask';
            input.placeholder = 'Contoh: 255.255.255.0';
            if (!input.value.includes('.') && !isNaN(input.value)) {
                input.value = '255.255.255.0';
            }
        }
    }

    function toggleNetwork(type) {
        const switchEl = document.getElementById(`${type}Switch`);
        const statusEl = document.getElementById(`${type}StatusText`);
        const isChecked = switchEl.checked;
        
        statusEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" style="width:10px; height:10px;"></span> Memproses...';
        switchEl.disabled = true;
        
        setTimeout(() => {
            switchEl.disabled = false;
            if (type === 'wifi') {
                if (isChecked) {
                    statusEl.textContent = 'Enabled (Connected to \'Antigravity_5G\')';
                    statusEl.className = 'fs-8 text-success';
                    if (document.getElementById('airplaneSwitch').checked) {
                        document.getElementById('airplaneSwitch').checked = false;
                        document.getElementById('airplaneStatusText').textContent = 'Disabled';
                        document.getElementById('airplaneStatusText').className = 'fs-8 text-secondary';
                    }
                } else {
                    statusEl.textContent = 'Disabled';
                    statusEl.className = 'fs-8 text-secondary';
                }
            } else if (type === 'mobileData') {
                if (isChecked) {
                    statusEl.textContent = 'Enabled (LTE Active)';
                    statusEl.className = 'fs-8 text-success';
                    if (document.getElementById('airplaneSwitch').checked) {
                        document.getElementById('airplaneSwitch').checked = false;
                        document.getElementById('airplaneStatusText').textContent = 'Disabled';
                        document.getElementById('airplaneStatusText').className = 'fs-8 text-secondary';
                    }
                } else {
                    statusEl.textContent = 'Disabled';
                    statusEl.className = 'fs-8 text-secondary';
                }
            } else if (type === 'airplane') {
                if (isChecked) {
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
                if (isChecked) {
                    statusEl.textContent = 'Enabled (Idle)';
                    statusEl.className = 'fs-8 text-success';
                } else {
                    statusEl.textContent = 'Disabled';
                    statusEl.className = 'fs-8 text-secondary';
                }
            }
            
            showToast(`Status ${type.toUpperCase()} diubah.`);
        }, 1200);
    }

    function showToast(msg) {
        const toastEl = document.getElementById('netToast');
        const toastMsg = document.getElementById('netToastMessage');
        toastMsg.textContent = msg;
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
</script>
