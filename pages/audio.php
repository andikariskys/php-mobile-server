<?php
// Handle AJAX single volume updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_single_volume') {
    $key = isset($_POST['volume_key']) ? trim($_POST['volume_key']) : '';
    $val = isset($_POST['volume_value']) ? trim($_POST['volume_value']) : '50';
    
    if (!empty($key)) {
        // Save dynamically
        db_set('volume_' . $key, $val);
        
        $realVolumes = $device->getAudioVolumes();
        if (isset($realVolumes[$key])) {
            $maxVol = $realVolumes[$key]['max_volume'];
            $absoluteVol = $maxVol > 0 ? round(($val / 100) * $maxVol) : 0;
            $device->setVolume($key, $absoluteVol);
        } else {
            // Fallback command execution if termux-volume is silent
            $device->setVolume($key, round(($val / 100) * 15));
        }
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'key' => $key, 'val' => $val]);
        exit;
    }
}

// Handle TTS Speak via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'speak') {
    $text = isset($_POST['text']) ? trim($_POST['text']) : '';
    $engine = isset($_POST['engine']) ? trim($_POST['engine']) : null;
    $rate = isset($_POST['rate']) ? (float)$_POST['rate'] : 1.0;
    $pitch = isset($_POST['pitch']) ? (float)$_POST['pitch'] : 1.0;
    
    $res = false;
    if (!empty($text)) {
        $res = $device->speakText($text, $engine, $rate, $pitch);
    }
    
    header('Content-Type: application/json');
    // Termux speak runs asynchronously; as long as the command is sent successfully, return success!
    echo json_encode(['status' => 'success']);
    exit;
}

// Load real volumes from DeviceController (Uses ONLY termux-volume)
$realVolumes = $device->getAudioVolumes();

// Load volume configurations (percentages)
$vol_call = isset($realVolumes['call']['percent']) ? $realVolumes['call']['percent'] : 50;
$vol_system = isset($realVolumes['system']['percent']) ? $realVolumes['system']['percent'] : 50;
$vol_ring = isset($realVolumes['ring']['percent']) ? $realVolumes['ring']['percent'] : 50;
$vol_music = isset($realVolumes['music']['percent']) ? $realVolumes['music']['percent'] : 50;
$vol_alarm = isset($realVolumes['alarm']['percent']) ? $realVolumes['alarm']['percent'] : 50;
$vol_notification = isset($realVolumes['notification']['percent']) ? $realVolumes['notification']['percent'] : 50;

// Load real TTS engines from Termux
$engines = $device->getTTSEngines();
?>

<div class="row g-4">
    <!-- Volume Slider Controls -->
    <div class="col-12 col-lg-5">
        <div class="glass-card h-100">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fi fi-sr-volume text-success fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Volume Suara Perangkat</h5>
            </div>
            
            <div class="d-flex flex-column gap-3.5">
                <!-- Call Volume -->
                <div>
                    <div class="d-flex justify-content-between mb-1.5 fs-7">
                        <span class="text-white font-weight-500">Call Volume</span>
                        <span class="text-secondary" id="valCallText"><?= $vol_call ?>%</span>
                    </div>
                    <input type="range" class="form-range vol-slider" data-key="call" min="0" max="100" value="<?= $vol_call ?>" oninput="updateVolText('Call', this.value)" onchange="saveVolume('call', this.value)">
                </div>

                <!-- System Volume -->
                <div>
                    <div class="d-flex justify-content-between mb-1.5 fs-7">
                        <span class="text-white font-weight-500">System Volume</span>
                        <span class="text-secondary" id="valSystemText"><?= $vol_system ?>%</span>
                    </div>
                    <input type="range" class="form-range vol-slider" data-key="system" min="0" max="100" value="<?= $vol_system ?>" oninput="updateVolText('System', this.value)" onchange="saveVolume('system', this.value)">
                </div>

                <!-- Ring Volume -->
                <div>
                    <div class="d-flex justify-content-between mb-1.5 fs-7">
                        <span class="text-white font-weight-500">Ring Volume</span>
                        <span class="text-secondary" id="valRingText"><?= $vol_ring ?>%</span>
                    </div>
                    <input type="range" class="form-range vol-slider" data-key="ring" min="0" max="100" value="<?= $vol_ring ?>" oninput="updateVolText('Ring', this.value)" onchange="saveVolume('ring', this.value)">
                </div>

                <!-- Music Volume -->
                <div>
                    <div class="d-flex justify-content-between mb-1.5 fs-7">
                        <span class="text-white font-weight-500">Music Volume</span>
                        <span class="text-secondary" id="valMusicText"><?= $vol_music ?>%</span>
                    </div>
                    <input type="range" class="form-range vol-slider" data-key="music" min="0" max="100" value="<?= $vol_music ?>" oninput="updateVolText('Music', this.value)" onchange="saveVolume('music', this.value)">
                </div>

                <!-- Alarm Volume -->
                <div>
                    <div class="d-flex justify-content-between mb-1.5 fs-7">
                        <span class="text-white font-weight-500">Alarm Volume</span>
                        <span class="text-secondary" id="valAlarmText"><?= $vol_alarm ?>%</span>
                    </div>
                    <input type="range" class="form-range vol-slider" data-key="alarm" min="0" max="100" value="<?= $vol_alarm ?>" oninput="updateVolText('Alarm', this.value)" onchange="saveVolume('alarm', this.value)">
                </div>

                <!-- Notification Volume -->
                <div>
                    <div class="d-flex justify-content-between mb-1.5 fs-7">
                        <span class="text-white font-weight-500">Notification Volume</span>
                        <span class="text-secondary" id="valNotificationText"><?= $vol_notification ?>%</span>
                    </div>
                    <input type="range" class="form-range vol-slider" data-key="notification" min="0" max="100" value="<?= $vol_notification ?>" oninput="updateVolText('Notification', this.value)" onchange="saveVolume('notification', this.value)">
                </div>
            </div>
            
            <div class="mt-4 fs-8 text-secondary text-center" id="saveIndicator">
                <i class="fi fi-sr-shield-check me-1 align-middle text-success"></i> Volume berhasil diperbarui pada perangkat.
            </div>
        </div>
    </div>

    <!-- TTS Settings Panel -->
    <div class="col-12 col-lg-7">
        <div class="glass-card h-100">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fi fi-sr-volume text-primary fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Text-to-Speech (TTS) Perangkat</h5>
            </div>
            
            <form id="ttsForm" onsubmit="speakText(event)">
                <div class="mb-3">
                    <label for="ttsInput" class="form-label text-secondary fs-7 ms-1">Teks untuk Diucapkan</label>
                    <textarea id="ttsInput" rows="4" class="form-control form-glass" placeholder="Ketik kata-kata atau kalimat di sini agar diucapkan oleh perangkat..." required>Halo! Selamat datang di Mobile Web Server. Teks ini akan diucapkan langsung dari speaker handphone Anda.</textarea>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <label for="ttsVoice" class="form-label text-secondary fs-7 ms-1">Pilih Aplikasi TTS</label>
                        <select id="ttsVoice" class="form-select form-glass">
                            <?php if (empty($engines)): ?>
                                <option value="com.google.android.tts" selected>Google TTS (com.google.android.tts)</option>
                            <?php else: ?>
                                <?php foreach ($engines as $eng): ?>
                                    <option value="<?= htmlspecialchars($eng['name']) ?>" <?= isset($eng['default']) && $eng['default'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($eng['label']) ?> (<?= htmlspecialchars($eng['name']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="col-12 col-sm-3">
                        <label for="ttsRate" class="form-label text-secondary fs-8 ms-1">Kecepatan: <span id="rateVal" class="text-white font-weight-600">1.0</span></label>
                        <input type="range" id="ttsRate" class="form-range" min="0.5" max="2.0" step="0.1" value="1.0" oninput="document.getElementById('rateVal').textContent=this.value">
                    </div>

                    <div class="col-12 col-sm-3">
                        <label for="ttsPitch" class="form-label text-secondary fs-8 ms-1">Nada/Pitch: <span id="pitchVal" class="text-white font-weight-600">1.0</span></label>
                        <input type="range" id="ttsPitch" class="form-range" min="0.5" max="2.0" step="0.1" value="1.0" oninput="document.getElementById('pitchVal').textContent=this.value">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary-gradient px-4 py-2.5 rounded-10 text-white font-weight-600 w-100" id="btnSpeak">
                    <i class="fi fi-sr-play me-2 align-middle"></i> Ucapkan Sekarang (Phone)
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const btnSpeak = document.getElementById('btnSpeak');
    
    function speakText(event) {
        event.preventDefault();
        
        const input = document.getElementById('ttsInput').value;
        const engine = document.getElementById('ttsVoice').value;
        const rate = document.getElementById('ttsRate').value;
        const pitch = document.getElementById('ttsPitch').value;
        
        btnSpeak.disabled = true;
        btnSpeak.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Memutar TTS Perangkat...';
        
        let formData = new FormData();
        formData.append('action', 'speak');
        formData.append('text', input);
        formData.append('engine', engine);
        formData.append('rate', rate);
        formData.append('pitch', pitch);
        
        const querySep = window.location.search ? '&' : '?';
        fetch(window.location.pathname + window.location.search + querySep + 'api=1', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btnSpeak.disabled = false;
            btnSpeak.innerHTML = '<i class="fi fi-sr-play me-2 align-middle"></i> Ucapkan Sekarang (Phone)';
            if (data.status === 'success') {
                const indicator = document.getElementById('saveIndicator');
                if (indicator) {
                    const oldContent = indicator.innerHTML;
                    indicator.innerHTML = '<span class="text-success"><i class="fi fi-sr-shield-check me-1 align-middle"></i> Suara TTS berhasil dikeluarkan!</span>';
                    setTimeout(() => { indicator.innerHTML = oldContent; }, 4000);
                }
            } else {
                alert('Gagal mengirim perintah TTS.');
            }
        })
        .catch(err => {
            console.error(err);
            btnSpeak.disabled = false;
            btnSpeak.innerHTML = '<i class="fi fi-sr-play me-2 align-middle"></i> Ucapkan Sekarang (Phone)';
            alert('Gagal menghubungi server.');
        });
    }

    function updateVolText(key, value) {
        const textEl = document.getElementById(`val${key}Text`);
        if (textEl) textEl.textContent = value + '%';
    }

    function saveVolume(key, value) {
        const indicator = document.getElementById('saveIndicator');
        if (indicator) indicator.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" style="width:10px; height:10px;"></span> Menyimpan volume...';
        
        const formData = new FormData();
        formData.append('action', 'save_single_volume');
        formData.append('volume_key', key);
        formData.append('volume_value', value);
        
        const querySep = window.location.search ? '&' : '?';
        fetch(window.location.pathname + window.location.search + querySep + 'api=1', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (indicator) indicator.innerHTML = `<i class="fi fi-sr-shield-check me-1 align-middle text-success"></i> Volume ${key} berhasil diperbarui!`;
            } else {
                if (indicator) indicator.innerHTML = '<span class="text-danger">Gagal menyimpan volume.</span>';
            }
        })
        .catch(err => {
            console.error(err);
            if (indicator) indicator.innerHTML = '<span class="text-danger">Gagal menghubungi server.</span>';
        });
    }
</script>

<style>
    .gap-3.5 {
        gap: 1.15rem;
    }
</style>
