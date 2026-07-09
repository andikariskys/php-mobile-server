<?php
// Handle AJAX single volume updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_single_volume') {
    $key = isset($_POST['volume_key']) ? trim($_POST['volume_key']) : '';
    $val = isset($_POST['volume_value']) ? trim($_POST['volume_value']) : '50';
    if (!empty($key)) {
        db_set('volume_' . $key, $val);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'key' => $key, 'val' => $val]);
        exit;
    }
}

// Load volume configurations from SQLite
$vol_call = db_get('volume_call', '50');
$vol_system = db_get('volume_system', '50');
$vol_ring = db_get('volume_ring', '50');
$vol_music = db_get('volume_music', '50');
$vol_alarm = db_get('volume_alarm', '50');
$vol_notification = db_get('volume_notification', '50');
?>

<div class="row g-4">
    <!-- Volume Slider Controls (Without icons in labels) -->
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
            
            <div class="mt-3.5 fs-8 text-secondary text-center" id="saveIndicator">
                <i class="fi fi-sr-shield-check me-1 align-middle text-success"></i> Volume tersimpan secara dinamis.
            </div>
        </div>
    </div>

    <!-- TTS Settings Panel -->
    <div class="col-12 col-lg-7">
        <div class="glass-card h-100">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fi fi-sr-volume text-primary fs-5"></i>
                <h5 class="mb-0 text-white font-weight-600">Text-to-Speech (TTS)</h5>
            </div>
            
            <form id="ttsForm" onsubmit="speakText(event)">
                <div class="mb-3">
                    <label for="ttsInput" class="form-label text-secondary fs-7 ms-1">Teks untuk Diucapkan</label>
                    <textarea id="ttsInput" rows="4" class="form-control form-glass" placeholder="Ketik kata-kata atau kalimat di sini agar diucapkan oleh perangkat..." required>Halo! Selamat datang di Mobile Web Server. Halaman ini mendukung pemutaran Audio dan TTS.</textarea>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <label for="ttsVoice" class="form-label text-secondary fs-7 ms-1">Pilih Aplikasi TTS</label>
                        <!-- Replaced voices list with package names of TTS applications -->
                        <select id="ttsVoice" class="form-select form-glass">
                            <option value="com.codefactory.vocalizer.tts">Vocalizer (com.codefactory.vocalizer.tts)</option>
                            <option value="com.github.olaprog.rhvoice.android">RHVoice (com.github.olaprog.rhvoice.android)</option>
                            <option value="com.acapelagroup.android.tts">Acapela TTS (com.acapelagroup.android.tts)</option>
                            <option value="com.google.android.tts" selected>Google TTS (com.google.android.tts)</option>
                            <option value="com.reecedev.espeakng">eSpeak NG (com.reecedev.espeakng)</option>
                        </select>
                    </div>
                    
                    <div class="col-12 col-sm-3">
                        <label for="ttsRate" class="form-label text-secondary fs-8 ms-1">Kecepatan: <span id="rateVal" class="text-white font-weight-600">1.0</span></label>
                        <input type="range" id="ttsRate" class="form-range" min="0.5" max="2" step="0.1" value="1.0" oninput="document.getElementById('rateVal').textContent=this.value">
                    </div>

                    <div class="col-12 col-sm-3">
                        <label for="ttsPitch" class="form-label text-secondary fs-8 ms-1">Nada/Pitch: <span id="pitchVal" class="text-white font-weight-600">1.0</span></label>
                        <input type="range" id="ttsPitch" class="form-range" min="0.5" max="2" step="0.1" value="1.0" oninput="document.getElementById('pitchVal').textContent=this.value">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary-gradient px-4 py-2.5 rounded-10 text-white font-weight-600 w-100" id="btnSpeak">
                    <i class="fi fi-sr-play me-2 align-middle"></i> Putar Audio
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    let synth = window.speechSynthesis;
    const btnSpeak = document.getElementById('btnSpeak');
    
    function speakText(event) {
        event.preventDefault();
        
        if (typeof synth === 'undefined') return;
        
        const input = document.getElementById('ttsInput').value;
        const utterance = new SpeechSynthesisUtterance(input);
        
        utterance.rate = parseFloat(document.getElementById('ttsRate').value);
        utterance.pitch = parseFloat(document.getElementById('ttsPitch').value);
        
        utterance.onstart = function() {
            // Disable button during playback
            btnSpeak.disabled = true;
            btnSpeak.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Memutar Audio...';
        };
        
        utterance.onend = function() {
            btnSpeak.disabled = false;
            btnSpeak.innerHTML = '<i class="fi fi-sr-play me-2 align-middle"></i> Putar Audio';
        };

        utterance.onerror = function() {
            btnSpeak.disabled = false;
            btnSpeak.innerHTML = '<i class="fi fi-sr-play me-2 align-middle"></i> Putar Audio';
        };
        
        synth.speak(utterance);
    }

    // Update Slider text in UI
    function updateVolText(key, value) {
        document.getElementById(`val${key}Text`).textContent = value + '%';
    }

    // Save volume dynamically via fetch POST
    function saveVolume(key, value) {
        const indicator = document.getElementById('saveIndicator');
        indicator.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" style="width:10px; height:10px;"></span> Menyimpan volume...';
        
        const formData = new FormData();
        formData.append('action', 'save_single_volume');
        formData.append('volume_key', key);
        formData.append('volume_value', value);
        
        fetch('/audio', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                indicator.innerHTML = '<i class="fi fi-sr-shield-check me-1 align-middle text-success"></i> Volume tersimpan secara dinamis.';
            } else {
                indicator.innerHTML = '<span class="text-danger">Gagal menyimpan volume.</span>';
            }
        })
        .catch(err => {
            console.error(err);
            indicator.innerHTML = '<span class="text-danger">Gagal menghubungi server.</span>';
        });
    }
</script>

<style>
    .gap-3.5 {
        gap: 1.15rem;
    }
</style>
