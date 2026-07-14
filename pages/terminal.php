<?php
$term_port = db_get('terminal_port', '3001');
$term_url = db_get('terminal_url', '');
$hostname = $_SERVER['HTTP_HOST'];
$host_only = parse_url('http://' . $hostname, PHP_URL_HOST);
$is_ip = filter_var($host_only, FILTER_VALIDATE_IP) || 
         $host_only === 'localhost' || 
         $host_only === '127.0.0.1' || 
         preg_match('/^\d{1,3}(\.\d{1,3}){3}$/', $host_only);
?>
<div class="row g-4">
    <div class="col-12">
        <!-- Warning Card (Hidden by default, shown by JS if no config is available) -->
        <div class="glass-card text-center py-5 d-none" id="termWarningCard">
            <div class="icon-container icon-warning mx-auto mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                <i class="fi fi-sr-terminal"></i>
            </div>
            <h5 class="text-white font-weight-600 mb-2">Terminal Belum Dikonfigurasi</h5>
            <p class="text-secondary fs-8 mb-4 mx-auto" style="max-width: 420px;">
                Konfigurasi Port atau URL Domain Terminal belum diatur. Silakan buka halaman System Settings terlebih dahulu untuk mengaturnya.
            </p>
            <a href="index.php?page=settings" class="btn btn-primary-gradient px-4 py-2.5 rounded-10 text-white font-weight-600">
                Buka System Settings
            </a>
        </div>

        <!-- Connection Alert (Shown by JS) -->
        <div class="alert alert-info bg-info bg-opacity-10 border border-info border-opacity-20 text-info py-2 px-3 fs-8 mb-3 d-flex flex-wrap gap-2 align-items-center justify-content-between rounded-10 d-none" id="termAlert">
            <div>
                <i class="fi fi-sr-info me-2 align-middle"></i>
                <span id="termModeText">Mengakses...</span>
                <span class="badge bg-info text-white font-weight-500 fs-9 ms-2" id="termUrlBadge"></span>
            </div>
            <!-- Switch mode button inside alert -->
            <button class="btn btn-sm btn-outline-info py-1 px-2.5 rounded-8 fs-9" onclick="toggleTerminalMode()" id="btnSwitchMode">
                Ganti Mode
            </button>
        </div>

        <!-- Terminal Iframe -->
        <div class="glass-card p-0 overflow-hidden d-none" id="termIframeCard" style="height: calc(100vh - 200px); min-height: 520px; border-radius: 16px;">
            <iframe src="" style="width: 100%; height: 100%; border: none; background: #000000;" id="terminalIframe"></iframe>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const hostOnly = <?= json_encode($host_only) ?>;
        const termPort = <?= json_encode($term_port) ?>;
        const termUrl = <?= json_encode($term_url) ?>;
        const isIpDefault = <?= $is_ip ? 'true' : 'false' ?>;

        if (!termPort && !termUrl) {
            document.getElementById('termWarningCard').classList.remove('d-none');
            return;
        }

        // Check if override exists
        let currentMode = sessionStorage.getItem('terminal_mode_override');
        if (!currentMode) {
            currentMode = isIpDefault ? 'port' : 'url';
        }

        let resolvedUrl = '';
        let modeText = '';
        let btnText = '';

        if (currentMode === 'port') {
            if (termPort) {
                resolvedUrl = 'http://' + hostOnly + ':' + termPort;
                modeText = `Mengakses via IP: Otomatis menggunakan PORT lokal (${termPort})`;
                btnText = 'Ganti ke Mode URL Domain';
            } else {
                resolvedUrl = termUrl;
                modeText = 'Mengakses via IP: Port lokal belum diatur, beralih ke URL Domain';
                btnText = 'Ganti ke Mode PORT';
            }
        } else { // 'url' mode
            if (termUrl) {
                resolvedUrl = termUrl;
                modeText = 'Mengakses via Domain: Otomatis menggunakan URL kustom';
                btnText = 'Ganti ke Mode PORT lokal';
            } else {
                resolvedUrl = 'http://' + hostOnly + ':' + termPort;
                modeText = `Mengakses via Domain: URL belum diatur, beralih ke PORT lokal (${termPort})`;
                btnText = 'Ganti ke Mode URL Domain';
            }
        }

        // Set values and show elements
        document.getElementById('termModeText').textContent = modeText;
        document.getElementById('termUrlBadge').textContent = resolvedUrl;
        document.getElementById('btnSwitchMode').textContent = btnText;
        document.getElementById('terminalIframe').src = resolvedUrl;

        document.getElementById('termAlert').classList.remove('d-none');
        document.getElementById('termIframeCard').classList.remove('d-none');
    });

    function toggleTerminalMode() {
        const isIpDefault = <?= $is_ip ? 'true' : 'false' ?>;
        const current = sessionStorage.getItem('terminal_mode_override') || (isIpDefault ? 'port' : 'url');
        const target = current === 'port' ? 'url' : 'port';
        sessionStorage.setItem('terminal_mode_override', target);
        window.location.reload();
    }
</script>
