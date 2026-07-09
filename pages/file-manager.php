<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-0 overflow-hidden" style="height: calc(100vh - 160px); min-height: 550px; border-radius: 16px;">
            <!-- Iframe to Tiny File Manager -->
            <iframe src="/filemanager/tinyfilemanager.php" style="width: 100%; height: 100%; border: none; background: #ffffff;" id="fileManagerIframe"></iframe>
        </div>
    </div>
</div>

<script>
    // Optional utility to handle file manager sizing adjustments
    window.addEventListener('resize', function() {
        const iframe = document.getElementById('fileManagerIframe');
        if (iframe) {
            // Keep dynamic resizing smooth
        }
    });
</script>
