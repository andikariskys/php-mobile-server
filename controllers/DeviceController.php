<?php
/**
 * DeviceController - PHP WebUI and API Controller
 * 
 * Provides unified methods to fetch system monitoring data and run control commands
 * on Android devices. Automatically detects and prioritizes standard Android system commands
 * (sysfs, dumpsys, content query, etc.) to achieve maximum speed and low overhead.
 * Falls back to Termux:API commands where requested or as secondary fallbacks.
 */

// Include DB helpers if they are not already loaded
if (!function_exists('db_get')) {
    $dbPath = dirname(__DIR__) . '/db.php';
    if (file_exists($dbPath)) {
        require_once $dbPath;
    }
}

class DeviceController {
    /**
     * @var bool True to prepend commands with "su -c" for Magisk/SuperUser environment
     */
    private $useSu;

    /**
     * @var bool True to enable Termux API fallbacks
     */
    private $useTermuxApi;

    /**
     * @var string Bin directory for Termux executables
     */
    private $termuxBinPath;

    /**
     * Constructor
     */
    public function __construct($useSu = true, $useTermuxApi = true, $termuxBinPath = '/data/data/com.termux/files/usr/bin') {
        $this->useSu = $useSu;
        $this->useTermuxApi = $useTermuxApi;
        $this->termuxBinPath = rtrim($termuxBinPath, '/');
    }

    /**
     * Internal shell execution helper
     */
    private function exec($command, $runAsRoot = false, $useTermuxEnv = false) {
        if ($useTermuxEnv) {
            $command = "export PATH=" . $this->termuxBinPath . ":\$PATH && " . $command;
        }

        if ($runAsRoot && $this->useSu) {
            $command = "su -c " . escapeshellarg($command);
        }

        $output = shell_exec($command);
        return $output !== null ? trim($output) : '';
    }

    /**
     * Format byte values to human readable strings
     */
    private function formatBytes($bytes, $precision = 2) {
        if (empty($bytes) || $bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(log($bytes) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Parse CPU usage by reading /proc/stat with a short pause
     */
    private function calculateCpuUsage() {
        $stat1 = $this->readProcStat();
        if (!$stat1) return 0.0;
        
        usleep(100000); // 100ms interval
        
        $stat2 = $this->readProcStat();
        if (!$stat2) return 0.0;

        $idle1 = $stat1['idle'] + $stat1['iowait'];
        $nonIdle1 = $stat1['user'] + $stat1['nice'] + $stat1['system'] + $stat1['irq'] + $stat1['softirq'] + $stat1['steal'];
        $total1 = $idle1 + $nonIdle1;

        $idle2 = $stat2['idle'] + $stat2['iowait'];
        $nonIdle2 = $stat2['user'] + $stat2['nice'] + $stat2['system'] + $stat2['irq'] + $stat2['softirq'] + $stat2['steal'];
        $total2 = $idle2 + $nonIdle2;

        $totalDiff = $total2 - $total1;
        $idleDiff = $idle2 - $idle1;

        if ($totalDiff == 0) return 0.0;

        $percentage = ($totalDiff - $idleDiff) / $totalDiff * 100;
        return round($percentage, 2);
    }

    /**
     * Read /proc/stat structure
     */
    private function readProcStat() {
        if (!file_exists('/proc/stat')) return null;
        $data = file_get_contents('/proc/stat');
        if (!$data) return null;
        
        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            if (preg_match('/^cpu\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/', $line, $matches)) {
                return [
                    'user' => (int)$matches[1],
                    'nice' => (int)$matches[2],
                    'system' => (int)$matches[3],
                    'idle' => (int)$matches[4],
                    'iowait' => (int)$matches[5],
                    'irq' => (int)$matches[6],
                    'softirq' => (int)$matches[7],
                    'steal' => (int)$matches[8],
                ];
            }
        }
        return null;
    }

    /**
     * Helper to decode IPv6/v4 hex strings back to readable IP addresses
     */
    private function hexToIp($hex, $proto) {
        if ($proto === 'tcp') {
            $ip = pack("H*", $hex);
            $ipArr = unpack("C*", $ip);
            return implode('.', array_reverse($ipArr));
        } else {
            $parts = str_split($hex, 8);
            $ipParts = [];
            foreach ($parts as $part) {
                $subParts = str_split($part, 2);
                $ipParts[] = implode('', array_reverse($subParts));
            }
            $ipHex = implode('', $ipParts);
            return implode(':', str_split($ipHex, 4));
        }
    }

    /**
     * Fallback listener socket parser using /proc/net/tcp and tcp6
     */
    private function parseProcNetTcp() {
        $sockets = [];
        $files = ['/proc/net/tcp' => 'tcp', '/proc/net/tcp6' => 'tcp6'];
        
        foreach ($files as $file => $proto) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                if (empty($content)) continue;
                $lines = explode("\n", $content);
                array_shift($lines); // remove header
                
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    $parts = preg_split('/\s+/', $line);
                    if (count($parts) >= 10) {
                        $state = $parts[3];
                        if ($state !== '0A') continue; // 0A = TCP_LISTEN
                        
                        $localHex = explode(':', $parts[1]);
                        $localIp = $this->hexToIp($localHex[0], $proto);
                        $localPort = hexdec($localHex[1]);
                        
                        $peerHex = explode(':', $parts[2]);
                        $peerIp = $this->hexToIp($peerHex[0], $proto);
                        $peerPort = hexdec($peerHex[1]);
                        
                        $sockets[] = [
                            'protocol' => $proto,
                            'local_address' => "$localIp:$localPort",
                            'peer_address' => "$peerIp:$peerPort",
                            'process' => 'unknown',
                            'pid' => '-',
                        ];
                    }
                }
            }
        }
        return $sockets;
    }

    /**
     * 1. GET DEVICE DETAILS
     */
    public function getDeviceDetails() {
        $brand = $this->exec("getprop ro.product.brand");
        if (empty($brand)) {
            $brand = $this->exec("getprop ro.product.manufacturer");
        }
        $brand = !empty($brand) ? $brand : 'N/A';
        
        $model = $this->exec("getprop ro.product.model");
        $model = !empty($model) ? $model : 'N/A';
        
        $codename = $this->exec("getprop ro.product.device");
        if (empty($codename)) {
            $codename = $this->exec("getprop ro.build.product");
        }
        $codename = !empty($codename) ? $codename : 'N/A';
        
        $androidVer = $this->exec("getprop ro.build.version.release");
        $androidVer = !empty($androidVer) ? $androidVer : 'N/A';
        
        $hardware = $this->exec("getprop ro.board.platform");
        if (empty($hardware)) {
            if (file_exists('/proc/cpuinfo')) {
                $cpuinfo = file_get_contents('/proc/cpuinfo');
                if (preg_match('/^Hardware\s*:\s*(.+)/m', $cpuinfo, $matches)) {
                    $hardware = trim($matches[1]);
                }
            }
        }
        $hardware = !empty($hardware) ? preg_replace('/^(Qualcomm Technologies, Inc|MediaTek|Broadcom)\s*/i', '', $hardware) : 'N/A';

        // Uptime calculations
        $uptimeSecs = 0;
        $uptimeFormatted = 'N/A';
        if (file_exists('/proc/uptime')) {
            $uptimeData = file_get_contents('/proc/uptime');
            $parts = explode(' ', $uptimeData);
            $uptimeSecs = (float)$parts[0];
            $uptimeSecsInt = (int)$uptimeSecs;
            
            $days = floor($uptimeSecsInt / 86400);
            $hours = floor(($uptimeSecsInt % 86400) / 3600);
            $minutes = floor(($uptimeSecsInt % 3600) / 60);
            
            if ($days > 0) {
                $uptimeFormatted = "{$days}d {$hours}h {$minutes}m";
            } else {
                $uptimeFormatted = "{$hours}h {$minutes}m";
            }
        }

        // SIM Operators
        $simData = $this->getSIMDetails();

        return [
            'brand' => $brand,
            'model' => $model,
            'display_name' => ($brand !== 'N/A' || $model !== 'N/A') ? trim("$brand $model") : 'N/A',
            'codename' => $codename,
            'android_version' => $androidVer,
            'hardware_model' => $hardware,
            'uptime_seconds' => $uptimeSecs,
            'uptime_formatted' => $uptimeFormatted,
            'sim1_operator' => $simData['sim1_operator'],
            'sim2_operator' => $simData['sim2_operator']
        ];
    }

    /**
     * 2. GET CPU INFO
     */
    public function getCPUDetails() {
        $coreCount = 0;
        $cpuModel = '';

        if (file_exists('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor\s*:/m', $cpuinfo, $matches);
            $coreCount = count($matches[0]);

            if (preg_match('/^model name\s*:\s*(.+)/m', $cpuinfo, $matches)) {
                $cpuModel = trim($matches[1]);
            } else if (preg_match('/^Hardware\s*:\s*(.+)/m', $cpuinfo, $matches)) {
                $cpuModel = trim($matches[1]);
            }
        }

        if (empty($cpuModel)) {
            $cpuModel = $this->exec("getprop ro.board.platform");
        }

        return [
            'usage' => $this->calculateCpuUsage(),
            'model' => !empty($cpuModel) ? $cpuModel : 'N/A',
            'core_count' => $coreCount > 0 ? $coreCount : 'N/A'
        ];
    }

    /**
     * 3. GET BATTERY DETAILS
     */
    public function getBatteryDetails() {
        $battery = [];

        // 1. Try reading sysfs directly
        $sysfsPath = '/sys/class/power_supply/battery';
        if (is_dir($sysfsPath)) {
            $level = file_exists("$sysfsPath/capacity") ? (int)trim(file_get_contents("$sysfsPath/capacity")) : null;
            $status = file_exists("$sysfsPath/status") ? trim(file_get_contents("$sysfsPath/status")) : null;
            $tempRaw = file_exists("$sysfsPath/temp") ? (int)trim(file_get_contents("$sysfsPath/temp")) : null;
            $voltageRaw = file_exists("$sysfsPath/voltage_now") ? (int)trim(file_get_contents("$sysfsPath/voltage_now")) : null;
            $currentRaw = file_exists("$sysfsPath/current_now") ? (int)trim(file_get_contents("$sysfsPath/current_now")) : null;

            if ($level !== null && $level >= 0 && $level <= 100) {
                $temp = $tempRaw !== null ? $tempRaw / 10 : 0.0;
                if ($temp > 120) $temp = $temp / 10;

                $voltage = 0.0;
                if ($voltageRaw !== null) {
                    if ($voltageRaw > 1000000) $voltage = round($voltageRaw / 1000000, 2);
                    elseif ($voltageRaw > 1000) $voltage = round($voltageRaw / 1000, 2);
                    else $voltage = (float)$voltageRaw;
                }

                $current = 0;
                if ($currentRaw !== null) {
                    if (abs($currentRaw) > 1000000) $current = round($currentRaw / 1000000);
                    elseif (abs($currentRaw) > 1000) $current = round($currentRaw / 1000);
                    else $current = $currentRaw;
                }

                $battery = [
                    'level' => $level,
                    'status' => !empty($status) ? ucfirst(strtolower($status)) : 'N/A',
                    'temperature' => $temp,
                    'voltage' => $voltage,
                    'current' => $current,
                    'health' => 'GOOD',
                    'plugged' => 'N/A'
                ];
            }
        }

        // 2. Try dumpsys battery
        if (empty($battery)) {
            $dumpsys = $this->exec("dumpsys battery", true);
            if (!empty($dumpsys) && strpos($dumpsys, 'level:') !== false) {
                preg_match('/level:\s*(\d+)/i', $dumpsys, $levelMatch);
                preg_match('/temperature:\s*(\d+)/i', $dumpsys, $tempMatch);
                preg_match('/status:\s*(\d+)/i', $dumpsys, $statusMatch);
                preg_match('/AC\s*powered:\s*(true|false)/i', $dumpsys, $acMatch);
                preg_match('/USB\s*powered:\s*(true|false)/i', $dumpsys, $usbMatch);
                
                $statusMap = [1 => 'Unknown', 2 => 'Charging', 3 => 'Discharging', 4 => 'Not Charging', 5 => 'Full'];
                $statusId = isset($statusMatch[1]) ? (int)$statusMatch[1] : 1;
                $status = isset($statusMap[$statusId]) ? $statusMap[$statusId] : 'N/A';

                $level = isset($levelMatch[1]) ? (int)$levelMatch[1] : 0;
                $tempRaw = isset($tempMatch[1]) ? (int)$tempMatch[1] : 0;
                $temperature = $tempRaw / 10;

                $plugged = 'UNPLUGGED';
                if (isset($acMatch[1]) && strtolower($acMatch[1]) === 'true') {
                    $plugged = 'AC';
                } elseif (isset($usbMatch[1]) && strtolower($usbMatch[1]) === 'true') {
                    $plugged = 'USB';
                }

                $battery = [
                    'level' => $level,
                    'status' => $status,
                    'temperature' => $temperature,
                    'voltage' => 0.0,
                    'current' => 0,
                    'health' => 'N/A',
                    'plugged' => $plugged
                ];
            }
        }

        // 3. Fallback to Termux API
        if (empty($battery) && $this->useTermuxApi) {
            $json = $this->exec("termux-battery-status", false, true);
            $data = json_decode($json, true);
            if (is_array($data)) {
                $battery = [
                    'level' => isset($data['percentage']) ? (int)$data['percentage'] : 0,
                    'status' => isset($data['status']) ? ucfirst(strtolower($data['status'])) : 'N/A',
                    'temperature' => isset($data['temperature']) ? (float)$data['temperature'] : 0.0,
                    'voltage' => isset($data['voltage']) ? round($data['voltage'] / 1000, 2) : 0.0,
                    'current' => isset($data['current']) ? (int)$data['current'] : 0,
                    'health' => isset($data['health']) ? $data['health'] : 'GOOD',
                    'plugged' => isset($data['plugged']) ? str_replace('PLUGGED_', '', $data['plugged']) : 'UNPLUGGED'
                ];
            }
        }

        if (empty($battery)) {
            $battery = [
                'level' => 'N/A',
                'status' => 'N/A',
                'temperature' => 0.0,
                'voltage' => 0.0,
                'current' => 0,
                'health' => 'N/A',
                'plugged' => 'N/A'
            ];
        }

        return $battery;
    }

    /**
     * 4. GET MEMORY DETAILS (RAM & Swap)
     */
    public function getMemoryDetails() {
        $meminfo = '';
        if (file_exists('/proc/meminfo')) {
            $meminfo = file_get_contents('/proc/meminfo');
        }

        $data = [];
        if ($meminfo) {
            preg_match_all('/^(\w+):\s+(\d+)\s+kB/m', $meminfo, $matches);
            for ($i = 0; $i < count($matches[1]); $i++) {
                $data[$matches[1][$i]] = (int)$matches[2][$i] * 1024;
            }
        }

        $ramTotal = isset($data['MemTotal']) ? $data['MemTotal'] : 0;
        $ramFree = isset($data['MemFree']) ? $data['MemFree'] : 0;
        $buffers = isset($data['Buffers']) ? $data['Buffers'] : 0;
        $cached = isset($data['Cached']) ? $data['Cached'] : 0;

        if (isset($data['MemAvailable'])) {
            $ramAvail = $data['MemAvailable'];
        } else {
            $ramAvail = $ramFree + $buffers + $cached;
        }

        $ramUsed = $ramTotal - $ramAvail;
        $ramPercent = $ramTotal > 0 ? round(($ramUsed / $ramTotal) * 100, 1) : 0.0;

        $swapTotal = isset($data['SwapTotal']) ? $data['SwapTotal'] : 0;
        $swapFree = isset($data['SwapFree']) ? $data['SwapFree'] : 0;
        $swapUsed = $swapTotal - $swapFree;
        $swapPercent = $swapTotal > 0 ? round(($swapUsed / $swapTotal) * 100, 1) : 0.0;

        $swapCached = isset($data['SwapCached']) ? $data['SwapCached'] : 0;
        $dirty = isset($data['Dirty']) ? $data['Dirty'] : 0;

        return [
            'ram' => [
                'total' => $ramTotal,
                'used' => $ramUsed,
                'available' => $ramAvail,
                'percent' => $ramPercent,
                'total_formatted' => $ramTotal > 0 ? $this->formatBytes($ramTotal) : 'N/A',
                'used_formatted' => $ramTotal > 0 ? $this->formatBytes($ramUsed) : 'N/A',
                'available_formatted' => $ramTotal > 0 ? $this->formatBytes($ramAvail) : 'N/A'
            ],
            'swap' => [
                'total' => $swapTotal,
                'used' => $swapUsed,
                'available' => $swapFree,
                'percent' => $swapPercent,
                'total_formatted' => $swapTotal > 0 ? $this->formatBytes($swapTotal) : 'N/A',
                'used_formatted' => $swapTotal > 0 ? $this->formatBytes($swapUsed) : 'N/A',
                'available_formatted' => $swapTotal > 0 ? $this->formatBytes($swapFree) : 'N/A'
            ],
            'swap_cached' => $swapCached,
            'swap_cached_formatted' => $swapCached > 0 ? $this->formatBytes($swapCached) : 'N/A',
            'dirty' => $dirty,
            'dirty_formatted' => $dirty > 0 ? $this->formatBytes($dirty) : 'N/A'
        ];
    }

    /**
     * 5. GET GPU DETAILS
     */
    public function getGPUDetails() {
        $gpuModel = $this->exec("cat /sys/kernel/gpu/gpu_model");
        if (empty($gpuModel)) {
            $gpuModel = $this->exec("cat /sys/class/kgsl/kgsl-3d0/gpu_model");
        }
        if (empty($gpuModel)) {
            $gpuModel = "N/A";
        }

        $gpuOpenGL = 'N/A';
        if (file_exists('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            if (preg_match('/^Hardware\s*:\s*(.+)/m', $cpuinfo, $matches)) {
                $gpuOpenGL = trim($matches[1]);
            }
        }

        $gpuFreq = 0;
        $freqPaths = [
            "/sys/class/kgsl/kgsl-3d0/gpuclk",
            "/sys/class/kgsl/kgsl-3d0/devfreq/cur_freq",
            "/sys/class/kgsl/kgsl-3d0/cur_freq"
        ];
        foreach ($freqPaths as $path) {
            if (file_exists($path)) {
                $val = (int)trim(file_get_contents($path));
                if ($val > 0) {
                    $gpuFreq = $val > 1000000 ? round($val / 1000000) : $val;
                    break;
                }
            }
        }

        $gpuLoad = 0.0;
        $loadPaths = [
            "/sys/class/kgsl/kgsl-3d0/gpubusy",
            "/sys/class/kgsl/kgsl-3d0/gpu_busy_percent"
        ];
        foreach ($loadPaths as $path) {
            if (file_exists($path)) {
                if ($path === "/sys/class/kgsl/kgsl-3d0/gpubusy") {
                    $busyContent = trim(file_get_contents($path));
                    $parts = explode(' ', $busyContent);
                    if (count($parts) >= 2 && (int)$parts[1] > 0) {
                        $gpuLoad = round(((int)$parts[0] / (int)$parts[1]) * 100, 1);
                    }
                } else {
                    $gpuLoad = (float)trim(file_get_contents($path));
                }
                break;
            }
        }

        return [
            'model' => $gpuModel,
            'opengl' => $gpuOpenGL,
            'frequency' => $gpuFreq > 0 ? $gpuFreq : 'N/A',
            'load' => $gpuLoad
        ];
    }

    /**
     * 6. GET SIM DETAILS
     * Parse mLte signals with SINR divided by 10
     */
    public function getSIMDetails() {
        $simOperators = explode(',', $this->exec("getprop gsm.sim.operator.alpha"));
        if (empty($simOperators[0]) || trim($simOperators[0]) === '') {
            $simOperators = explode(',', $this->exec("getprop gsm.operator.alpha"));
        }
        $sim1 = isset($simOperators[0]) && trim($simOperators[0]) !== '' ? trim($simOperators[0]) : 'N/A';
        $sim2 = isset($simOperators[1]) && trim($simOperators[1]) !== '' ? trim($simOperators[1]) : 'N/A';
        
        $activeDataSim = 1;
        $activeSlot = $this->exec("settings get global multi_sim_data_call");
        if (trim($activeSlot) !== '' && $activeSlot !== 'null') {
            $activeDataSim = (int)trim($activeSlot) + 1;
        }
        $activeOperator = ($activeDataSim === 2) ? $sim2 : $sim1;
        if ($activeOperator === 'N/A') {
            $activeOperator = 'N/A';
        }

        $pci = 'N/A';
        $rssi = 'N/A';
        $rsrp = 'N/A';
        $rsrq = 'N/A';
        $sinr = 'N/A';
        $level = 'N/A';

        // 1. Parse active SIM signals from grep command
        $teleGrep = $this->exec('dumpsys telephony.registry | grep -i "mLte=CellSignalStrengthLte"', true);
        if ($teleGrep) {
            $lines = explode("\n", $teleGrep);
            foreach ($lines as $line) {
                if (preg_match('/mLte=CellSignalStrengthLte:\s*rssi=([-\d]+)\s*rsrp=([-\d]+)\s*rsrq=([-\d]+)\s*rssnr=([-\d]+)\s*.*?level=(\d+)/i', $line, $matches)) {
                    $tempRssi = $matches[1];
                    $tempRsrp = $matches[2];
                    $tempRsrq = $matches[3];
                    $tempRssnr = $matches[4];
                    $tempLevel = $matches[5];

                    // Skip unregistered or invalid SIM signal (2147483647 is standard Android Integer.MAX_VALUE)
                    if ($tempRssi !== '2147483647' && $tempRssi !== '') {
                        $rssi = $tempRssi;
                        $rsrp = $tempRsrp;
                        $rsrq = $tempRsrq;
                        
                        if ($tempRssnr !== '2147483647' && $tempRssnr !== '') {
                            $sinr = number_format((float)$tempRssnr / 10, 1);
                        }
                        
                        $level = 'Level ' . $tempLevel;
                        break; // Found active registered SIM signal
                    }
                }
            }
        }

        // 2. Parse PCI separately from full dumpsys
        $teleAll = $this->exec("dumpsys telephony.registry", true);
        if ($teleAll) {
            if (preg_match('/Pci=([0-9]+)/i', $teleAll, $matches)) {
                $pci = $matches[1];
            }
        }

        return [
            'sim1_operator' => $sim1,
            'sim2_operator' => $sim2,
            'active_data_sim' => $activeDataSim,
            'active_operator' => $activeOperator,
            'pci' => $pci,
            'rssi' => $rssi,
            'rsrp' => $rsrp,
            'rsrq' => $rsrq,
            'sinr' => $sinr,
            'level' => $level
        ];
    }

    /**
     * 7. GET NETWORK DETAILS
     * WiFi SSIDs are retrieved using dumpsys wifi with the SSID filter logic
     */
    public function getNetworkDetails() {
        $wifiStatus = 'Disabled';
        $wifiSsid = 'N/A';
        $mobileDataStatus = 'Disabled';
        $airplaneMode = 'Disabled';
        $bluetoothStatus = 'Disabled';

        // 1. Try system dumpsys wifi with grep SSID
        $dumpsysWifi = $this->exec("dumpsys wifi | grep -i 'SSID'", true);
        if (!empty($dumpsysWifi)) {
            // Find connected WiFi SSID from the mWifiInfo SSID: ... pattern
            if (preg_match('/SSID:\s*([^,]+)/i', $dumpsysWifi, $matches)) {
                $wifiSsid = trim($matches[1], ' "');
            }
            
            $supplicantState = 'UNKNOWN';
            if (preg_match('/Supplicant state:\s*([A-Z_]+)/i', $dumpsysWifi, $matches)) {
                $supplicantState = trim($matches[1]);
            }
            
            if (($supplicantState === 'COMPLETED' || strpos($dumpsysWifi, 'state: COMPLETED') !== false) && $wifiSsid !== '<unknown ssid>' && $wifiSsid !== 'N/A') {
                $wifiStatus = 'Connected';
            } else {
                $wifiOn = $this->exec("settings get global wifi_on");
                $wifiStatus = trim($wifiOn) === '1' ? 'Enabled (Not Connected)' : 'Disabled';
            }
        } else {
            $wifiOn = $this->exec("settings get global wifi_on");
            $wifiStatus = trim($wifiOn) === '1' ? 'Enabled' : 'Disabled';
        }

        // Mobile Data
        $dataOn = $this->exec("settings get global mobile_data");
        if (trim($dataOn) === '1') {
            $mobileDataStatus = 'Enabled';
        }

        // Airplane Mode
        $airplaneOn = $this->exec("settings get global airplane_mode_on");
        if (trim($airplaneOn) === '1') {
            $airplaneMode = 'Enabled';
        }

        // Bluetooth
        $btOn = $this->exec("settings get global bluetooth_on");
        if (trim($btOn) === '1') {
            $bluetoothStatus = 'Enabled';
        }

        // Network Interfaces
        $interfaces = [];
        $netDir = '/sys/class/net';
        if (is_dir($netDir)) {
            $files = scandir($netDir);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || $file === 'lo') continue;

                $ipRaw = $this->exec("ip addr show " . escapeshellarg($file) . " | grep 'inet '");
                $ip = 'N/A';
                if ($ipRaw && preg_match('/inet\s+([^\s]+)/', $ipRaw, $matches)) {
                    $ip = $matches[1];
                }
                if ($ip === 'N/A') continue;

                $rxFile = "$netDir/$file/statistics/rx_bytes";
                $txFile = "$netDir/$file/statistics/tx_bytes";
                $rxBytes = file_exists($rxFile) ? (float)trim(file_get_contents($rxFile)) : 0;
                $txBytes = file_exists($txFile) ? (float)trim(file_get_contents($txFile)) : 0;

                $interfaces[] = [
                    'name' => $file,
                    'ip' => $ip,
                    'rx_bytes' => $rxBytes,
                    'tx_bytes' => $txBytes,
                    'rx_formatted' => $rxBytes > 0 ? $this->formatBytes($rxBytes) : 'N/A',
                    'tx_formatted' => $txBytes > 0 ? $this->formatBytes($txBytes) : 'N/A'
                ];
            }
        }

        return [
            'wifi_status' => $wifiStatus,
            'wifi_ssid' => $wifiSsid,
            'mobile_data_status' => $mobileDataStatus,
            'airplane_mode' => $airplaneMode,
            'bluetooth_status' => $bluetoothStatus,
            'interfaces' => $interfaces
        ];
    }

    /**
     * 8. GET SMS LIST
     */
    public function getSMSList($limit = 10, $offset = 0) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $smsList = [];

        $totalToFetch = $limit + $offset;
        $cmd = "content query --uri content://sms --projection _id,thread_id,address,date,read,type,body --limit $totalToFetch";
        $output = $this->exec($cmd, true);
        
        if ($output && strpos($output, 'Row:') !== false) {
            $lines = explode("\n", $output);
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                preg_match('/_id=([^,]+),\s*thread_id=([^,]+),\s*address=([^,]+),\s*date=([^,]+),\s*read=([^,]+),\s*type=([^,]+),\s*body=(.*)/i', $line, $matches);
                if ($matches) {
                    $dateMs = (float)$matches[4];
                    $dateSecs = floor($dateMs / 1000);
                    $typeId = (int)$matches[6];
                    
                    $smsList[] = [
                        'id' => trim($matches[1]),
                        'thread_id' => trim($matches[2]),
                        'type' => $typeId === 1 ? 'inbox' : ($typeId === 2 ? 'sent' : 'unknown'),
                        'address' => trim($matches[3]),
                        'number' => trim($matches[3]),
                        'date' => date('Y-m-d H:i:s', $dateSecs),
                        'body' => trim($matches[7]),
                        'read' => trim($matches[5]) === '1',
                    ];
                }
            }
            if ($offset > 0) {
                $smsList = array_slice($smsList, $offset, $limit);
            }
        }

        if (empty($smsList) && $this->useTermuxApi) {
            $json = $this->exec("termux-sms-list -l $limit -o $offset", false, true);
            $data = json_decode($json, true);
            if (is_array($data)) {
                $smsList = array_map(function($sms) {
                    return [
                        'id' => isset($sms['_id']) ? $sms['_id'] : (isset($sms['id']) ? $sms['id'] : 'N/A'),
                        'thread_id' => isset($sms['threadid']) ? $sms['threadid'] : 'N/A',
                        'type' => isset($sms['type']) ? $sms['type'] : 'unknown',
                        'address' => isset($sms['address']) ? $sms['address'] : 'N/A',
                        'number' => isset($sms['number']) ? $sms['number'] : 'N/A',
                        'date' => isset($sms['received']) ? $sms['received'] : 'N/A',
                        'body' => isset($sms['body']) ? $sms['body'] : 'N/A',
                        'read' => isset($sms['read']) ? (bool)$sms['read'] : false,
                    ];
                }, $data);
            }
        }

        return $smsList;
    }

    /**
     * 9. GET NOTIFICATIONS
     * Uses ONLY termux-notification-list
     */
    public function getNotifications() {
        $notifications = [];

        if ($this->useTermuxApi) {
            $json = $this->exec("termux-notification-list", false, true);
            $data = json_decode($json, true);
            if (is_array($data)) {
                foreach ($data as $notif) {
                    $notifications[] = [
                        'id' => isset($notif['id']) ? $notif['id'] : '',
                        'package' => isset($notif['packageName']) ? $notif['packageName'] : 'N/A',
                        'title' => isset($notif['title']) && trim($notif['title']) !== '' ? $notif['title'] : 'N/A',
                        'content' => isset($notif['content']) && trim($notif['content']) !== '' ? $notif['content'] : 'N/A',
                        'date' => isset($notif['when']) ? $notif['when'] : 'N/A',
                    ];
                }
            }
        }

        return array_slice($notifications, 0, 20);
    }

    /**
     * 10. GET CAMERA INFO
     */
    public function getCameraInfo() {
        if ($this->useTermuxApi) {
            $json = $this->exec("termux-camera-info", false, true);
            $data = json_decode($json, true);
            if (is_array($data)) {
                return $data;
            }
        }
        
        return [
            ['id' => '0', 'facing' => 'back'],
            ['id' => '1', 'facing' => 'front']
        ];
    }

    /**
     * 11. GET AUDIO VOLUMES
     * Uses ONLY termux-volume
     */
    public function getAudioVolumes() {
        $volumes = [];

        if ($this->useTermuxApi) {
            $json = $this->exec("termux-volume", false, true);
            $data = json_decode($json, true);
            if (is_array($data)) {
                foreach ($data as $stream) {
                    $volumes[$stream['stream']] = [
                        'volume' => $stream['volume'],
                        'max_volume' => $stream['max_volume'],
                        'percent' => $stream['max_volume'] > 0 ? round(($stream['volume'] / $stream['max_volume']) * 100) : 0
                    ];
                }
            }
        }

        return $volumes;
    }

    /**
     * 12. GET LOCATION (GPS)
     */
    public function getLocation() {
        $location = [];

        $output = $this->exec("dumpsys location", true);
        if ($output) {
            $latitude = 0.0;
            $longitude = 0.0;
            $accuracy = 0.0;
            $provider = 'unknown';

            if (preg_match('/Location\[(\w+)\s+([-\d\.]+),([-\d\.]+)\s+hAcc=([-\d\.]+)/i', $output, $matches)) {
                $provider = $matches[1];
                $latitude = (float)$matches[2];
                $longitude = (float)$matches[3];
                $accuracy = (float)$matches[4];
            } elseif (preg_match('/([-\d\.]+),([-\d\.]+)\s+acc=([-\d\.]+)/i', $output, $matches)) {
                $latitude = (float)$matches[1];
                $longitude = (float)$matches[2];
                $accuracy = (float)$matches[3];
            }

            if ($latitude !== 0.0 || $longitude !== 0.0) {
                $location = [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'altitude' => 0.0,
                    'accuracy' => $accuracy,
                    'provider' => $provider,
                    'time' => date('Y-m-d H:i:s')
                ];
            }
        }

        if (empty($location) && $this->useTermuxApi) {
            $json = $this->exec("termux-location -p gps -r", false, true);
            if (empty($json) || strpos($json, 'latitude') === false) {
                $json = $this->exec("termux-location -p network -r", false, true);
            }
            $data = json_decode($json, true);
            if (is_array($data) && isset($data['latitude'])) {
                $location = [
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'altitude' => isset($data['altitude']) ? $data['altitude'] : 0.0,
                    'accuracy' => isset($data['accuracy']) ? $data['accuracy'] : 0.0,
                    'provider' => isset($data['provider']) ? $data['provider'] : 'gps',
                    'time' => date('Y-m-d H:i:s')
                ];
            }
        }

        if (empty($location)) {
            $location = [
                'latitude' => 0.0,
                'longitude' => 0.0,
                'altitude' => 0.0,
                'accuracy' => 0.0,
                'provider' => 'N/A',
                'time' => 'N/A'
            ];
        }

        return $location;
    }

    /**
     * 13. GET DEVELOPER DETAILS (Listening Sockets)
     */
    public function getListeningSockets() {
        $output = $this->exec("ss -lptn", true);
        if (empty($output)) {
            $output = $this->exec("netstat -lptn", true);
        }

        $sockets = [];
        if ($output) {
            $lines = explode("\n", $output);
            foreach ($lines as $line) {
                if (strpos($line, 'LISTEN') === false) continue;

                $normalized = preg_replace('/\s+/', ' ', trim($line));
                $parts = explode(' ', $normalized);

                if (count($parts) >= 5) {
                    $local = $parts[3];
                    $peer = $parts[4];
                    $process = 'N/A';
                    $pid = 'N/A';

                    if (isset($parts[5]) && preg_match('/users:\(\("([^"]+)",pid=(\d+)/', $parts[5], $matches)) {
                        $process = $matches[1];
                        $pid = $matches[2];
                    }

                    $sockets[] = [
                        'protocol' => (strpos($local, ':::') !== false || strpos($local, '[::]') !== false) ? 'tcp6' : 'tcp',
                        'local_address' => $local,
                        'peer_address' => $peer,
                        'process' => $process,
                        'pid' => $pid
                    ];
                }
            }
        }

        if (empty($sockets)) {
            $sockets = $this->parseProcNetTcp();
        }

        return $sockets;
    }

    /**
     * GET STORAGE DETAILS
     */
    public function getStorageDetails() {
        $storage = [
            'internal' => ['total' => 0, 'used' => 0, 'available' => 0, 'percent' => 0.0, 'total_formatted' => 'N/A', 'used_formatted' => 'N/A', 'available_formatted' => 'N/A'],
            'sdcard' => ['total' => 0, 'used' => 0, 'available' => 0, 'percent' => 0.0, 'total_formatted' => 'N/A', 'used_formatted' => 'N/A', 'available_formatted' => 'N/A']
        ];

        $dfInternal = $this->exec("df -k /data", true);
        if ($dfInternal) {
            $lines = explode("\n", $dfInternal);
            $dataLine = '';
            foreach ($lines as $line) {
                if (strpos($line, '/data') !== false || strpos($line, '/sdcard') !== false) {
                    $dataLine = $line;
                    break;
                }
            }
            if (empty($dataLine) && isset($lines[1])) {
                $dataLine = $lines[1];
            }
            if (!empty($dataLine)) {
                $parts = preg_split('/\s+/', trim($dataLine));
                if (count($parts) >= 4) {
                    $total = (float)$parts[1] * 1024;
                    $used = (float)$parts[2] * 1024;
                    $avail = (float)$parts[3] * 1024;
                    $percent = $total > 0 ? round(($used / $total) * 100, 1) : 0.0;
                    $storage['internal'] = [
                        'total' => $total,
                        'used' => $used,
                        'available' => $avail,
                        'percent' => $percent,
                        'total_formatted' => $this->formatBytes($total),
                        'used_formatted' => $this->formatBytes($used),
                        'available_formatted' => $this->formatBytes($avail)
                    ];
                }
            }
        }

        $dfAll = $this->exec("df -k", true);
        if ($dfAll) {
            $lines = explode("\n", $dfAll);
            foreach ($lines as $line) {
                if (strpos($line, '/storage/') !== false && strpos($line, 'self') === false && strpos($line, 'emulated') === false) {
                    $parts = preg_split('/\s+/', trim($line));
                    if (count($parts) >= 4) {
                        $total = (float)$parts[1] * 1024;
                        $used = (float)$parts[2] * 1024;
                        $avail = (float)$parts[3] * 1024;
                        $percent = $total > 0 ? round(($used / $total) * 100, 1) : 0.0;
                        $storage['sdcard'] = [
                            'total' => $total,
                            'used' => $used,
                            'available' => $avail,
                            'percent' => $percent,
                            'total_formatted' => $this->formatBytes($total),
                            'used_formatted' => $this->formatBytes($used),
                            'available_formatted' => $this->formatBytes($avail)
                        ];
                        break;
                    }
                }
            }
        }

        return $storage;
    }

    /**
     * AGGREGATED DASHBOARD DATA
     */
    public function getDashboardData() {
        return [
            'device' => $this->getDeviceDetails(),
            'cpu' => $this->getCPUDetails(),
            'battery' => $this->getBatteryDetails(),
            'memory' => $this->getMemoryDetails(),
            'gpu' => $this->getGPUDetails(),
            'sim' => $this->getSIMDetails(),
            'network' => $this->getNetworkDetails(),
            'storage' => $this->getStorageDetails()
        ];
    }

    /**
     * PERSISTENT SETTINGS - READ
     */
    public function getDbSetting($key, $default = null) {
        if (function_exists('db_get')) {
            return db_get($key, $default);
        }
        return $default;
    }

    /**
     * PERSISTENT SETTINGS - WRITE
     */
    public function setDbSetting($key, $value) {
        if (function_exists('db_set')) {
            return db_set($key, $value);
        }
        return false;
    }

    /**
     * GET TTS ENGINES
     * termux-tts-engines
     */
    public function getTTSEngines() {
        if ($this->useTermuxApi) {
            $json = $this->exec("termux-tts-engines", false, true);
            $data = json_decode($json, true);
            if (is_array($data)) {
                return $data;
            }
        }
        return [];
    }

    /**
     * GET GPS STATUS
     */
    public function getGpsStatus() {
        $out = $this->exec("cmd location is-location-enabled", true);
        if ($out === '') {
            $out = $this->exec("settings get secure location_mode", true);
            return trim($out) !== '0' && trim($out) !== '';
        }
        return trim($out) === 'true';
    }

    /**
     * TOGGLE GPS
     */
    public function toggleGps($enable) {
        $cmd = $enable 
            ? "cmd location set-location-enabled true"
            : "cmd location set-location-enabled false";
        $this->exec($cmd, true);
        return true;
    }

    /**
     * ADD STATIC IP
     * Runs ip addr add <ip>/<prefix> dev <interface>
     */
    public function addStaticIp($ip, $prefix, $interface = 'wlan0') {
        $cmd = "ip addr add " . escapeshellarg("$ip/$prefix") . " dev " . escapeshellarg($interface);
        $this->exec($cmd, true);
        return true;
    }

    // =========================================================================
    // SYSTEM AND DEVICE ACTIONS (WRITE / TRIGGER)
    // =========================================================================

    /**
     * Action: Reboot Device
     */
    public function rebootDevice() {
        return $this->exec("reboot", true) !== null;
    }

    /**
     * Action: Shutdown Device
     */
    public function shutdownDevice() {
        $res = $this->exec("reboot -p", true);
        if ($res === '') {
            $res = $this->exec("poweroff", true);
        }
        return $res !== null;
    }

    /**
     * Action: Clear RAM Caches & App Temporary files
     */
    public function clearCache() {
        $this->exec("rm -rf /cache/* && rm -rf /data/dalvik-cache/*", true);
        return true;
    }

    /**
     * Action: Toggle Wifi
     */
    public function toggleWifi($enable) {
        $stateStr = $enable ? 'true' : 'false';
        $cmdStr = $enable ? 'enable' : 'disable';

        $this->exec("cmd wifi set-wifi-enabled $cmdStr", true);
        $this->exec("svc wifi $cmdStr", true);

        if ($this->useTermuxApi) {
            $this->exec("termux-wifi-enable $stateStr", false, true);
        }
        return true;
    }

    public function toggleMobileData($enable) {
        $val = $enable ? '1' : '0';
        $cmdStr = $enable ? 'enable' : 'disable';
        $this->exec("svc data $cmdStr && settings put global mobile_data $val", true);
        return true;
    }

    /**
     * Action: Toggle Airplane Mode
     */
    public function toggleAirplaneMode($enable) {
        $val = $enable ? '1' : '0';
        $stateStr = $enable ? 'true' : 'false';

        $this->exec("settings put global airplane_mode_on $val", true);
        $this->exec("am broadcast -a android.intent.action.AIRPLANE_MODE --ez state $stateStr", true);
        return true;
    }

    /**
     * Action: Toggle Bluetooth
     */
    public function toggleBluetooth($enable) {
        $code = $enable ? 6 : 8;
        $this->exec("service call bluetooth_manager $code", true);
        $this->exec("cmd bluetooth_manager " . ($enable ? "enable" : "disable"), true);
        return true;
    }

    /**
     * Action: Send SMS
     */
    public function sendSMS($recipient, $message) {
        $recipients = explode(',', $recipient);
        $successCount = 0;

        foreach ($recipients as $number) {
            $number = trim($number);
            if (empty($number)) continue;

            $cmd = "service call phone 7 s16 " . escapeshellarg($number) . " s16 \"\" s16 " . escapeshellarg($message);
            $res = $this->exec($cmd, true);
            
            if ($res && strpos($res, 'Result:') !== false) {
                $successCount++;
            } else if ($this->useTermuxApi) {
                $cmd = "termux-sms-send -n " . escapeshellarg($number) . " " . escapeshellarg($message);
                $this->exec($cmd, false, true);
                $successCount++;
            }
        }
        return $successCount > 0;
    }

    /**
     * Action: Remove Notification by ID
     */
    public function removeNotification($id) {
        if ($this->useTermuxApi) {
            $this->exec("termux-notification-remove " . escapeshellarg($id), false, true);
            return true;
        }
        return false;
    }

    /**
     * Action: Capture Photo
     */
    public function capturePhoto($cameraId, $outputFile) {
        if ($this->useTermuxApi) {
            $fileName = basename($outputFile);
            $tempFile = '/sdcard/Pictures/temp_' . $fileName;
            $sdcardPath = '/sdcard/Pictures/' . $fileName;

            $cmd = "termux-camera-photo -c " . escapeshellarg($cameraId) . " " . escapeshellarg($tempFile) . 
                   " && cp " . escapeshellarg($tempFile) . " " . escapeshellarg($sdcardPath) . 
                   " && mv " . escapeshellarg($tempFile) . " " . escapeshellarg($outputFile) .
                   " && chmod 666 " . escapeshellarg($sdcardPath) .
                   " && chmod 666 " . escapeshellarg($outputFile);

            $this->exec($cmd, true, true); // Run as root to ensure camera access and permission bypass
            return file_exists($outputFile) && filesize($outputFile) > 0;
        }
        return false;
    }

    /**
     * Action: Delete captured photo from SD Card storage
     */
    public function deletePhotoFromSdcard($fileName) {
        $fileName = basename($fileName);
        $sdcardPath = '/sdcard/Pictures/' . $fileName;
        $this->exec("rm -f " . escapeshellarg($sdcardPath), true);
        return true;
    }

    /**
     * Action: Set Volume Level for a specific stream
     */
    public function setVolume($stream, $level) {
        $level = (int)$level;
        
        $streamMap = ['call' => 0, 'system' => 1, 'ring' => 2, 'music' => 3, 'alarm' => 4, 'notification' => 5];
        if (isset($streamMap[$stream])) {
            $streamId = $streamMap[$stream];
            $this->exec("media volume --stream $streamId --set $level", true);
        }

        if ($this->useTermuxApi) {
            $cmd = "termux-volume " . escapeshellarg($stream) . " $level";
            $this->exec($cmd, false, true);
        }
        return true;
    }

    /**
     * Action: Speak Text using TTS
     */
    public function speakText($text, $engine = null, $rate = 1.0, $pitch = 1.0) {
        if ($this->useTermuxApi) {
            $cmd = "termux-tts-speak";
            if (!empty($engine)) {
                $cmd .= " -e " . escapeshellarg($engine);
            }
            if ($rate != 1.0) {
                $cmd .= " -r " . (float)$rate;
            }
            if ($pitch != 1.0) {
                $cmd .= " -p " . (float)$pitch;
            }
            $cmd .= " " . escapeshellarg($text);
            $this->exec($cmd, false, true);
            return true;
        }
        return false;
    }
}
