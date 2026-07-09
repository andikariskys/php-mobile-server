<div class="row g-4">
    <!-- Stat Cards Header Row -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="glass-card d-flex align-items-center gap-3">
            <div class="icon-container icon-primary mb-0">
                <i class="fi fi-sr-cpu"></i>
            </div>
            <div>
                <div class="stat-value" id="cpuUsageVal">24.5%</div>
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
                <!-- Changed RAM Used to 1.2 GB -->
                <div class="stat-value" id="ramUsageVal">1.2 GB</div>
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
                <div class="stat-value" id="batteryLevelVal">78%</div>
                <div class="stat-label">Baterai (Charging)</div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <div class="glass-card d-flex align-items-center gap-3">
            <div class="icon-container icon-info mb-0">
                <i class="fi fi-sr-wifi"></i>
            </div>
            <div>
                <!-- Changed Operator to XL -->
                <div class="stat-value">XL</div>
                <div class="stat-label">Operator LTE</div>
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
                            <!-- Changed to Xiaomi Redmi 4A -->
                            <td class="text-end py-2.5 pe-0 font-weight-500">Xiaomi Redmi 4A</td>
                        </tr>
                        <tr class="border-bottom border-white border-opacity-5">
                            <td class="text-secondary py-2.5 ps-0">Codename Perangkat</td>
                            <!-- Changed to rolex -->
                            <td class="text-end py-2.5 pe-0 font-weight-500">rolex</td>
                        </tr>
                        <tr class="border-bottom border-white border-opacity-5">
                            <td class="text-secondary py-2.5 ps-0">Android Version</td>
                            <!-- Changed to Android 10 -->
                            <td class="text-end py-2.5 pe-0 font-weight-500">Android 10</td>
                        </tr>
                        <tr class="border-bottom border-white border-opacity-5">
                            <td class="text-secondary py-2.5 ps-0">Hardware Model</td>
                            <!-- Changed to Qualcomm MSM8917 -->
                            <td class="text-end py-2.5 pe-0 font-weight-500">Qualcomm Snapdragon 425 (MSM8917)</td>
                        </tr>
                        <tr>
                            <td class="text-secondary py-2.5 ps-0">Uptime</td>
                            <!-- Changed to static 05h 12m 43s -->
                            <td class="text-end py-2.5 pe-0 font-weight-500" id="uptimeVal">05h 12m 43s</td>
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
                    <span class="text-primary-gradient font-weight-600" id="cpuUsageText">24.5%</span>
                </div>
                <div class="progress bg-white bg-opacity-10" style="height: 8px; border-radius: 4px;">
                    <div id="cpuUsageBar" class="progress-bar bg-primary-gradient" role="progressbar" style="width: 24.5%; border-radius: 4px; transition: width 1s ease;"></div>
                </div>
                <!-- Changed to Cortex-A53 (4 Cores) @ 1.40 GHz -->
                <div class="fs-8 text-secondary mt-1">Model: Cortex-A53 (4 Cores) @ 1.40 GHz</div>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5">
                        <div class="text-secondary fs-8 mb-1">Status Baterai</div>
                        <div class="text-white font-weight-600 fs-6" id="batteryStatusText">Charging</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5">
                        <div class="text-secondary fs-8 mb-1">Suhu Baterai</div>
                        <div class="text-warning font-weight-600 fs-6" id="batteryTempText">32.4 °C</div>
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
            
            <!-- RAM Progress - Changed to 2GB Total -->
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-1 fs-7">
                    <span class="text-white font-weight-500">RAM Memory</span>
                    <span class="text-secondary" id="ramDetailText">1.2 GB / 2.0 GB (60.0%)</span>
                </div>
                <div class="progress bg-white bg-opacity-10" style="height: 10px; border-radius: 5px;">
                    <div id="ramBar" class="progress-bar bg-success" role="progressbar" style="width: 60.0%; border-radius: 5px; transition: width 1s ease;"></div>
                </div>
                <div class="d-flex justify-content-between fs-8 text-secondary mt-1">
                    <span id="ramUsedLabel">Used: 1.2 GB</span>
                    <span id="ramAvailLabel">Available: 0.8 GB</span>
                </div>
            </div>

            <!-- Swap Progress - Changed to 2GB Total -->
            <div>
                <div class="d-flex justify-content-between mb-1 fs-7">
                    <span class="text-white font-weight-500">Swap Space</span>
                    <span class="text-secondary">0.5 GB / 2.0 GB (25.0%)</span>
                </div>
                <div class="progress bg-white bg-opacity-10" style="height: 10px; border-radius: 5px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 25.0%; border-radius: 5px;"></div>
                </div>
                <div class="d-flex justify-content-between fs-8 text-secondary mt-1">
                    <span>Used: 0.5 GB</span>
                    <span>Available: 1.5 GB</span>
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
            
            <!-- Internal Storage - Changed to 32GB -->
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-1 fs-7">
                    <span class="text-white font-weight-500">Internal Storage</span>
                    <span class="text-secondary">20.5 GB / 32.0 GB (64.0%)</span>
                </div>
                <div class="progress bg-white bg-opacity-10" style="height: 10px; border-radius: 5px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 64.0%; border-radius: 5px;"></div>
                </div>
                <div class="d-flex justify-content-between fs-8 text-secondary mt-1">
                    <span>Used: 20.5 GB</span>
                    <span>Available: 11.5 GB</span>
                </div>
            </div>

            <!-- SD Card -->
            <div>
                <div class="d-flex justify-content-between mb-1 fs-7">
                    <span class="text-white font-weight-500">SD Card Storage</span>
                    <span class="text-secondary">12.1 GB / 64.0 GB (18.9%)</span>
                </div>
                <div class="progress bg-white bg-opacity-10" style="height: 10px; border-radius: 5px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 18.9%; border-radius: 5px;"></div>
                </div>
                <div class="d-flex justify-content-between fs-8 text-secondary mt-1">
                    <span>Used: 12.1 GB</span>
                    <span>Available: 51.9 GB</span>
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
                        <div class="text-secondary fs-8 mb-1">Operator Name</div>
                        <div class="text-white font-weight-600">XL</div>
                    </div>
                </div>
                
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 text-center">
                        <div class="text-secondary fs-8 mb-1">PCI (Cell ID)</div>
                        <div class="text-white font-weight-600">384</div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 text-center">
                        <div class="text-secondary fs-8 mb-1">RSSI</div>
                        <div class="text-success font-weight-600">-65 dBm</div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 text-center">
                        <div class="text-secondary fs-8 mb-1">RSRP</div>
                        <div class="text-success font-weight-600">-92 dBm</div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 text-center">
                        <div class="text-secondary fs-8 mb-1">RSRQ</div>
                        <div class="text-warning font-weight-600">-12 dB</div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5 text-center">
                        <div class="text-secondary fs-8 mb-1">SINR</div>
                        <div class="text-success font-weight-600">18 dB</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Live CPU, Battery & Uptime simulations - Uptime is now static
    function updateDashboardStats() {
        // CPU Usage (random oscillation)
        const cpuUsageValEl = document.getElementById('cpuUsageVal');
        const cpuUsageTextEl = document.getElementById('cpuUsageText');
        const cpuUsageBarEl = document.getElementById('cpuUsageBar');
        
        if (cpuUsageValEl && cpuUsageTextEl && cpuUsageBarEl) {
            let current = parseFloat(cpuUsageValEl.textContent);
            let change = (Math.random() - 0.5) * 8; // Change by up to 4%
            let target = current + change;
            if (target < 5) target = 5;
            if (target > 85) target = 85;
            
            const fixedTarget = target.toFixed(1);
            cpuUsageValEl.textContent = fixedTarget + '%';
            cpuUsageTextEl.textContent = fixedTarget + '%';
            cpuUsageBarEl.style.width = fixedTarget + '%';
        }
        
        // Battery status/temperature oscillation
        const batTempEl = document.getElementById('batteryTempText');
        if (batTempEl) {
            let temp = parseFloat(batTempEl.textContent);
            let tempChange = (Math.random() - 0.5) * 0.2;
            let nextTemp = (temp + tempChange).toFixed(1);
            batTempEl.textContent = nextTemp + ' °C';
        }
    }

    // Run interval every second
    setInterval(updateDashboardStats, 1000);

    // Dynamic Memory simulation (minor memory adjustments) - Adjusted for 2GB total
    setInterval(() => {
        const ramValEl = document.getElementById('ramUsageVal');
        const ramDetailEl = document.getElementById('ramDetailText');
        const ramBar = document.getElementById('ramBar');
        const ramUsedLabel = document.getElementById('ramUsedLabel');
        const ramAvailLabel = document.getElementById('ramAvailLabel');
        
        if (ramValEl && ramDetailEl && ramBar) {
            let ram = parseFloat(ramValEl.textContent);
            let ramChange = (Math.random() - 0.5) * 0.05;
            let nextRam = (ram + ramChange).toFixed(2);
            if (nextRam < 1.0) nextRam = 1.0;
            if (nextRam > 1.4) nextRam = 1.4;
            
            const totalRam = 2.0;
            const pct = ((nextRam / totalRam) * 100).toFixed(1);
            const avail = (totalRam - nextRam).toFixed(2);
            
            ramValEl.textContent = nextRam + ' GB';
            ramDetailEl.textContent = `${nextRam} GB / ${totalRam.toFixed(1)} GB (${pct}%)`;
            ramBar.style.width = pct + '%';
            ramUsedLabel.textContent = `Used: ${nextRam} GB`;
            ramAvailLabel.textContent = `Available: ${avail} GB`;
        }
    }, 4000);
</script>
