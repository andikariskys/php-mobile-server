<div class="row g-4">
    <div class="col-12">
        <!-- Navigation Tabs -->
        <div class="d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-glass active-tab-btn" onclick="loadTab('root', 'filemanager/tinyfilemanager.php')" id="tab-root">
                <i class="fi fi-sr-folder-tree me-2 align-middle"></i> Root
            </button>
            <button class="btn btn-glass" onclick="loadTab('storage', 'filemanager/tinyfilemanager.php?p=sdcard')" id="tab-storage">
                <i class="fi fi-sr-sd-card me-2 align-middle"></i> Storage
            </button>
            <button class="btn btn-glass" onclick="loadTab('termux', 'filemanager/tinyfilemanager.php?p=data%2Fdata%2Fcom.termux%2Ffiles%2Fhome')" id="tab-termux">
                <i class="fi fi-sr-terminal me-2 align-middle"></i> Termux Home
            </button>
            <button class="btn btn-glass" onclick="loadTab('www', 'filemanager/tinyfilemanager.php?p=data%2Fadb%2Fphp7%2Ffiles%2Fwww')" id="tab-www">
                <i class="fi fi-sr-globe me-2 align-middle"></i> www (Webroot)
            </button>
        </div>

        <div class="glass-card p-0 overflow-hidden position-relative" style="height: calc(100vh - 210px); min-height: 500px; border-radius: 16px;">
            <!-- Loading Animation Overlay -->
            <div id="iframeLoader" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center" style="z-index: 10; background: rgba(11, 15, 25, 0.85); backdrop-filter: blur(8px); transition: opacity 0.4s ease;">
                <div class="spinner-border text-primary-gradient mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                <h6 class="text-white font-weight-600 mb-1">Memuat File Manager...</h6>
                <p class="text-secondary fs-8 mb-0">Mengambil data struktur direktori</p>
            </div>

            <!-- Iframe to Tiny File Manager -->
            <iframe src="filemanager/tinyfilemanager.php" style="width: 100%; height: 100%; border: none; background: #ffffff;" id="fileManagerIframe" onload="onIframeLoaded()"></iframe>
        </div>
    </div>
</div>

<style>
    .btn-glass {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #94a3b8;
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-glass:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.15);
    }
    
    .btn-glass.active-tab-btn {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
</style>

<script>
    function loadTab(tabId, url) {
        // Update active class on buttons
        const buttons = document.querySelectorAll('.btn-glass');
        buttons.forEach(btn => {
            btn.classList.remove('active-tab-btn');
        });
        
        document.getElementById(`tab-${tabId}`).classList.add('active-tab-btn');
        
        // Show loader
        const loader = document.getElementById('iframeLoader');
        if (loader) {
            loader.style.opacity = '1';
            loader.style.pointerEvents = 'auto';
            loader.style.display = 'flex';
        }
        
        // Set new source
        const iframe = document.getElementById('fileManagerIframe');
        if (iframe) {
            iframe.src = url;
        }
    }
    
    function onIframeLoaded() {
        const loader = document.getElementById('iframeLoader');
        if (loader) {
            loader.style.opacity = '0';
            loader.style.pointerEvents = 'none';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 400);
        }
    }
</script>
