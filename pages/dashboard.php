<?php
// Handle AJAX requests for dashboard stats
if (isset($_GET['api']) && $_GET['api'] == '1') {
    header('Content-Type: application/json');
    echo json_encode($device->getDashboardData());
    exit;
}

$dashData = $device->getDashboardData();
$dev = $dashData['device'];
$cpu = $dashData['cpu'];
$bat = $dashData['battery'];
$mem = $dashData['memory'];
$gpu = $dashData['gpu'];
$sim = $dashData['sim'];
$net = $dashData['network'];
$storage = $dashData['storage'];
?>

<div class="row g-4">
    <!-- Stat Cards Header Row -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="glass-card d-flex align-items-center gap-3">
            <div class="icon-container icon-primary mb-0">
                <i class="fi fi-sr-cpu"></i>
            </div>
            <div>
                <div class="stat-value" id="cpuUsageVal"><?= $cpu['usage'] !== 'N/A' ? number_format((float)$cpu['usage'], 1) . '%' : 'N/A' ?></div>
                <div class="stat-label">CPU Usage</div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <div class="glass-card d-flex align-items-center gap-3">
            <div class="icon-container icon-success mb-0">
                <i class="fi fi-sr-database"></i>
            </div>
            <div>
                <div class="stat-value" id="ramUsageVal"><?= htmlspecialchars($mem['ram']['used_formatted']) ?></div>
                <div class="stat-label">Used Memory (RAM)</div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <div class="glass-card d-flex align-items-center gap-3">
            <div class="icon-container icon-warning mb-0">
                <i class="fi fi-sr-bolt"></i>
            </div>
            <div>
                <div class="stat-value" id="batteryLevelVal"><?= $bat['level'] !== 'N/A' ? $bat['level'] . '%' : 'N/A' ?></div>
                <div class="stat-label">Baterai (<?= htmlspecialchars($bat['status']) ?>)</div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <div class="glass-card d-flex align-items-center gap-3">
            <div class="icon-container icon-info mb-0">
                <i class="fi fi-sr-wifi"></i>
            </div>
            <div>
                <div class="stat-value" id="statOperatorVal"><?= htmlspecialchars($sim['active_operator']) ?></div>
                <div class="stat-label" id="statOperatorLabel">SIM Data: SIM <?= htmlspecialchars($sim['active_data_sim']) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <!-- Device Info -->
    <div class="col-12 col-lg-6">
        <div class="glass-card h-100">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fi fi-sr-smartphone text-primary fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Informasi Perangkat</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless text-white mb-0 align-middle">
                    <tbody>
                        <tr class="border-bottom border-white border-opacity-5">
                            <td class="text-secondary py-2.5 ps-0">Nama Perangkat</td>
                            <td class="text-end py-2.5 pe-0 font-weight-500"><?= htmlspecialchars($dev['display_name']) ?></td>
                        </tr>
                        <tr class="border-bottom border-white border-opacity-5">
                            <td class="text-secondary py-2.5 ps-0">Codename Perangkat</td>
                            <td class="text-end py-2.5 pe-0 font-weight-500"><?= htmlspecialchars($dev['codename']) ?></td>
                        </tr>
                        <tr class="border-bottom border-white border-opacity-5">
                            <td class="text-secondary py-2.5 ps-0">Android Version</td>
                            <td class="text-end py-2.5 pe-0 font-weight-500">Android <?= htmlspecialchars($dev['android_version']) ?></td>
                        </tr>
                        <tr class="border-bottom border-white border-opacity-5">
                            <td class="text-secondary py-2.5 ps-0">Hardware Model</td>
                            <td class="text-end py-2.5 pe-0 font-weight-500"><?= htmlspecialchars($dev['hardware_model']) ?></td>
                        </tr>
                        <tr class="border-bottom border-white border-opacity-5">
                            <td class="text-secondary py-2.5 ps-0">SIM 1 Operator</td>
                            <td class="text-end py-2.5 pe-0 font-weight-500 text-info" id="infoSim1Operator"><?= htmlspecialchars($dev['sim1_operator']) ?></td>
                        </tr>
                        <tr class="border-bottom border-white border-opacity-5">
                            <td class="text-secondary py-2.5 ps-0">SIM 2 Operator</td>
                            <td class="text-end py-2.5 pe-0 font-weight-500 text-info" id="infoSim2Operator"><?= htmlspecialchars($dev['sim2_operator']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-secondary py-2.5 ps-0">Uptime</td>
                            <td class="text-end py-2.5 pe-0 font-weight-500" id="uptimeVal"><?= htmlspecialchars($dev['uptime_formatted']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Battery & CPU details -->
    <div class="col-12 col-lg-6">
        <div class="glass-card h-100">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fi fi-sr-microchip text-primary fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Informasi CPU & Baterai</h5>
            </div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-1.5 fs-7">
                    <span class="text-secondary">CPU Usage (Core Load)</span>
                    <span class="text-primary-gradient font-weight-600" id="cpuUsageText"><?= $cpu['usage'] !== 'N/A' ? number_format((float)$cpu['usage'], 1) . '%' : 'N/A' ?></span>
                </div>
                <div class="progress bg-white bg-opacity-10" style="height: 8px; border-radius: 4px;">
                    <div id="cpuUsageBar" class="progress-bar bg-primary-gradient" role="progressbar" style="width: <?= $cpu['usage'] !== 'N/A' ? (float)$cpu['usage'] : 0 ?>%; border-radius: 4px; transition: width 1s ease;"></div>
                </div>
                <div class="fs-8 text-secondary mt-1">Model: <?= htmlspecialchars($cpu['model']) ?> (<?= $cpu['core_count'] ?> Cores)</div>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5">
                        <div class="text-secondary fs-8 mb-1">Status Baterai</div>
                        <div class="text-white font-weight-600 fs-6" id="batteryStatusText"><?= htmlspecialchars($bat['status']) ?></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5">
                        <div class="text-secondary fs-8 mb-1">Suhu & Tegangan</div>
                        <div class="text-warning font-weight-600 fs-6" id="batteryTempText">
                            <?php if ($bat['temperature'] !== 'N/A'): ?>
                                <?= number_format((float)$bat['temperature'], 1) ?> °C &nbsp;<span class="text-white fs-8 font-weight-400"><?= number_format((float)$bat['voltage'], 2) ?>V</span>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <!-- Memory RAM & Swap -->
    <div class="col-12 col-lg-6">
        <div class="glass-card">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fi fi-sr-database text-success fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Informasi Memori (RAM / Swap)</h5>
            </div>
            
            <!-- RAM Progress -->
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-1 fs-7">
                    <span class="text-white font-weight-500">RAM Memory</span>
                    <span class="text-secondary" id="ramDetailText">
                        <?php if ($mem['ram']['total_formatted'] !== 'N/A'): ?>
                            <?= htmlspecialchars($mem['ram']['used_formatted']) ?> / <?= htmlspecialchars($mem['ram']['total_formatted']) ?> (<?= htmlspecialchars($mem['ram']['percent']) ?>%)
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </span>
                </div>
                <div class="progress bg-white bg-opacity-10" style="height: 10px; border-radius: 5px;">
                    <div id="ramBar" class="progress-bar bg-success" role="progressbar" style="width: <?= $mem['ram']['percent'] ?>%; border-radius: 5px; transition: width 1s ease;"></div>
                </div>
                <div class="d-flex justify-content-between fs-8 text-secondary mt-1">
                    <span id="ramUsedLabel">Used: <?= htmlspecialchars($mem['ram']['used_formatted']) ?></span>
                    <span id="ramAvailLabel">Available: <?= htmlspecialchars($mem['ram']['available_formatted']) ?></span>
                </div>
            </div>

            <!-- Swap Progress -->
            <div>
                <div class="d-flex justify-content-between mb-1 fs-7">
                    <span class="text-white font-weight-500">Swap Space</span>
                    <span class="text-secondary" id="swapDetailText">
                        <?php if ($mem['swap']['total_formatted'] !== 'N/A'): ?>
                            <?= htmlspecialchars($mem['swap']['used_formatted']) ?> / <?= htmlspecialchars($mem['swap']['total_formatted']) ?> (<?= htmlspecialchars($mem['swap']['percent']) ?>%)
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </span>
                </div>
                <div class="progress bg-white bg-opacity-10" style="height: 10px; border-radius: 5px;">
                    <div id="swapBar" class="progress-bar bg-info" role="progressbar" style="width: <?= $mem['swap']['percent'] ?>%; border-radius: 5px; transition: width 1s ease;"></div>
                </div>
                <div class="d-flex justify-content-between fs-8 text-secondary mt-1">
                    <span id="swapUsedLabel">Used: <?= htmlspecialchars($mem['swap']['used_formatted']) ?></span>
                    <span id="swapAvailLabel">Available: <?= htmlspecialchars($mem['swap']['available_formatted']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Info -->
    <div class="col-12 col-lg-6">
        <div class="glass-card">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fi fi-sr-hard-drive text-info fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Informasi Storage</h5>
            </div>
            
            <!-- Internal Storage -->
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-1 fs-7">
                    <span class="text-white font-weight-500">Internal Storage</span>
                    <span class="text-secondary"><?= htmlspecialchars($storage['internal']['used_formatted']) ?> / <?= htmlspecialchars($storage['internal']['total_formatted']) ?> (<?= htmlspecialchars($storage['internal']['percent']) ?>%)</span>
                </div>
                <div class="progress bg-white bg-opacity-10" style="height: 10px; border-radius: 5px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: <?= $storage['internal']['percent'] ?>%; border-radius: 5px;"></div>
                </div>
                <div class="d-flex justify-content-between fs-8 text-secondary mt-1">
                    <span>Used: <?= htmlspecialchars($storage['internal']['used_formatted']) ?></span>
                    <span>Available: <?= htmlspecialchars($storage['internal']['available_formatted']) ?></span>
                </div>
            </div>

            <!-- SD Card -->
            <div>
                <div class="d-flex justify-content-between mb-1 fs-7">
                    <span class="text-white font-weight-500">SD Card Storage</span>
                    <span class="text-secondary"><?= htmlspecialchars($storage['sdcard']['used_formatted']) ?> / <?= htmlspecialchars($storage['sdcard']['total_formatted']) ?> (<?= htmlspecialchars($storage['sdcard']['percent']) ?>%)</span>
                </div>
                <div class="progress bg-white bg-opacity-10" style="height: 10px; border-radius: 5px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $storage['sdcard']['percent'] ?>%; border-radius: 5px;"></div>
                </div>
                <div class="d-flex justify-content-between fs-8 text-secondary mt-1">
                    <span>Used: <?= htmlspecialchars($storage['sdcard']['used_formatted']) ?></span>
                    <span>Available: <?= htmlspecialchars($storage['sdcard']['available_formatted']) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <!-- Operator Info -->
    <div class="col-12">
        <div class="glass-card">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fi fi-sr-wifi text-warning fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Informasi Operator & Sinyal Seluler</h5>
            </div>
            
            <div class="row g-3">
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 text-center">
                        <div class="text-secondary fs-8 mb-1">Signal Strength</div>
                        <div class="text-white font-weight-600" id="simLevelVal"><?= htmlspecialchars($sim['level']) ?></div>
                    </div>
                </div>
                
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 text-center">
                        <div class="text-secondary fs-8 mb-1">PCI (Cell ID)</div>
                        <div class="text-white font-weight-600" id="simPciVal"><?= htmlspecialchars($sim['pci']) ?></div>
                    </div>
                </div>
                
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 text-center">
                        <div class="text-secondary fs-8 mb-1">RSSI</div>
                        <div class="text-success font-weight-600" id="simRssiVal"><?= $sim['rssi'] !== 'N/A' ? htmlspecialchars($sim['rssi']) . ' dBm' : 'N/A' ?></div>
                    </div>
                </div>
                
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 text-center">
                        <div class="text-secondary fs-8 mb-1">RSRP</div>
                        <div class="text-success font-weight-600" id="simRsrpVal"><?= $sim['rsrp'] !== 'N/A' ? htmlspecialchars($sim['rsrp']) . ' dBm' : 'N/A' ?></div>
                    </div>
                </div>
                
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 text-center">
                        <div class="text-secondary fs-8 mb-1">RSRQ</div>
                        <div class="text-warning font-weight-600" id="simRsrqVal"><?= $sim['rsrq'] !== 'N/A' ? htmlspecialchars($sim['rsrq']) . ' dB' : 'N/A' ?></div>
                    </div>
                </div>
                
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 text-center">
                        <div class="text-secondary fs-8 mb-1">SINR</div>
                        <div class="text-success font-weight-600" id="simSinrVal"><?= $sim['sinr'] !== 'N/A' ? htmlspecialchars($sim['sinr']) . ' dB' : 'N/A' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Live CPU, RAM, Swap & SIM Signal Polling (Updates every 1 second)
    function updateDashboardStats() {
        const querySep = window.location.search ? '&' : '?';
        fetch(window.location.pathname + window.location.search + querySep + 'api=1')
            .then(res => res.json())
            .then(data => {
                // Update CPU Usage
                const cpuUsageValEl = document.getElementById('cpuUsageVal');
                const cpuUsageTextEl = document.getElementById('cpuUsageText');
                const cpuUsageBarEl = document.getElementById('cpuUsageBar');
                
                if (cpuUsageValEl && cpuUsageTextEl && cpuUsageBarEl) {
                    if (data.cpu.usage !== 'N/A') {
                        const usage = data.cpu.usage.toFixed(1) + '%';
                        cpuUsageValEl.textContent = usage;
                        cpuUsageTextEl.textContent = usage;
                        cpuUsageBarEl.style.width = data.cpu.usage + '%';
                    } else {
                        cpuUsageValEl.textContent = 'N/A';
                        cpuUsageTextEl.textContent = 'N/A';
                        cpuUsageBarEl.style.width = '0%';
                    }
                }
                
                // Update Battery status
                const batTempEl = document.getElementById('batteryTempText');
                const batStatusEl = document.getElementById('batteryStatusText');
                const batLevelEl = document.getElementById('batteryLevelVal');
                
                if (batTempEl && batStatusEl && batLevelEl) {
                    batLevelEl.textContent = data.battery.level !== 'N/A' ? data.battery.level + '%' : 'N/A';
                    batStatusEl.textContent = data.battery.status;
                    if (data.battery.temperature !== 'N/A') {
                        batTempEl.innerHTML = `${data.battery.temperature.toFixed(1)} °C &nbsp;<span class="text-white fs-8 font-weight-400">${data.battery.voltage.toFixed(2)}V</span>`;
                    } else {
                        batTempEl.textContent = 'N/A';
                    }
                }

                // Update Header Battery Pill if present
                const headerBat = document.getElementById('headerBatteryLevel');
                const headerBatInfo = document.getElementById('headerBatteryInfo');
                if (headerBat && headerBatInfo) {
                    headerBat.textContent = data.battery.level !== 'N/A' ? data.battery.level + '%' : 'N/A';
                    headerBatInfo.title = 'Status: ' + data.battery.status;
                }
                
                // Update Uptime
                const uptimeEl = document.getElementById('uptimeVal');
                if (uptimeEl) {
                    uptimeEl.textContent = data.device.uptime_formatted;
                }

                // Update SIM Operators in Info Table
                const infoSim1 = document.getElementById('infoSim1Operator');
                const infoSim2 = document.getElementById('infoSim2Operator');
                if (infoSim1) infoSim1.textContent = data.device.sim1_operator;
                if (infoSim2) infoSim2.textContent = data.device.sim2_operator;

                // Update RAM Info
                const ramValEl = document.getElementById('ramUsageVal');
                const ramDetailEl = document.getElementById('ramDetailText');
                const ramBar = document.getElementById('ramBar');
                const ramUsedLabel = document.getElementById('ramUsedLabel');
                const ramAvailLabel = document.getElementById('ramAvailLabel');
                
                if (ramValEl && ramDetailEl && ramBar) {
                    if (data.memory.ram.total_formatted !== 'N/A') {
                        ramValEl.textContent = data.memory.ram.used_formatted;
                        ramDetailEl.textContent = `${data.memory.ram.used_formatted} / ${data.memory.ram.total_formatted} (${data.memory.ram.percent}%)`;
                        ramBar.style.width = data.memory.ram.percent + '%';
                        ramUsedLabel.textContent = `Used: ${data.memory.ram.used_formatted}`;
                        ramAvailLabel.textContent = `Available: ${data.memory.ram.available_formatted}`;
                    } else {
                        ramValEl.textContent = 'N/A';
                        ramDetailEl.textContent = 'N/A';
                        ramBar.style.width = '0%';
                        ramUsedLabel.textContent = 'Used: N/A';
                        ramAvailLabel.textContent = 'Available: N/A';
                    }
                }

                // Update Swap Info
                const swapDetailEl = document.getElementById('swapDetailText');
                const swapBar = document.getElementById('swapBar');
                const swapUsedLabel = document.getElementById('swapUsedLabel');
                const swapAvailLabel = document.getElementById('swapAvailLabel');

                if (swapDetailEl && swapBar) {
                    if (data.memory.swap.total_formatted !== 'N/A') {
                        swapDetailEl.textContent = `${data.memory.swap.used_formatted} / ${data.memory.swap.total_formatted} (${data.memory.swap.percent}%)`;
                        swapBar.style.width = data.memory.swap.percent + '%';
                        if (swapUsedLabel) swapUsedLabel.textContent = `Used: ${data.memory.swap.used_formatted}`;
                        if (swapAvailLabel) swapAvailLabel.textContent = `Available: ${data.memory.swap.available_formatted}`;
                    } else {
                        swapDetailEl.textContent = 'N/A';
                        swapBar.style.width = '0%';
                        if (swapUsedLabel) swapUsedLabel.textContent = 'Used: N/A';
                        if (swapAvailLabel) swapAvailLabel.textContent = 'Available: N/A';
                    }
                }

                // Update Operator & Signal Info (Real-Time 1 second updates)
                const statOperatorVal = document.getElementById('statOperatorVal');
                const statOperatorLabel = document.getElementById('statOperatorLabel');
                const simLevelVal = document.getElementById('simLevelVal');
                const simPciVal = document.getElementById('simPciVal');
                const simRssiVal = document.getElementById('simRssiVal');
                const simRsrpVal = document.getElementById('simRsrpVal');
                const simRsrqVal = document.getElementById('simRsrqVal');
                const simSinrVal = document.getElementById('simSinrVal');

                if (statOperatorVal) statOperatorVal.textContent = data.sim.active_operator;
                if (statOperatorLabel) statOperatorLabel.textContent = 'SIM Data: SIM ' + data.sim.active_data_sim;
                if (simLevelVal) simLevelVal.textContent = data.sim.level;
                if (simPciVal) simPciVal.textContent = data.sim.pci;
                if (simRssiVal) simRssiVal.textContent = data.sim.rssi !== 'N/A' ? data.sim.rssi + ' dBm' : 'N/A';
                if (simRsrpVal) simRsrpVal.textContent = data.sim.rsrp !== 'N/A' ? data.sim.rsrp + ' dBm' : 'N/A';
                if (simRsrqVal) simRsrqVal.textContent = data.sim.rsrq !== 'N/A' ? data.sim.rsrq + ' dB' : 'N/A';
                if (simSinrVal) simSinrVal.textContent = data.sim.sinr !== 'N/A' ? data.sim.sinr + ' dB' : 'N/A';
            })
            .catch(error => {
                console.error('Error fetching dashboard status:', error);
            });
    }

    // Refresh every 1 second (1000ms) for real-time CPU, RAM, Swap & operator metrics
    setInterval(updateDashboardStats, 1000);
</script>
