<?php
// Handle AJAX location update request
if (isset($_GET['api']) && $_GET['api'] == '1') {
    header('Content-Type: application/json');
    echo json_encode($device->getLocation());
    exit;
}

$loc = $device->getLocation();
?>

<!-- Load Leaflet CSS only on this page -->
<link rel="stylesheet" href="assets/leaflet/leaflet.css">

<div class="row g-4">
    <div class="col-12">
        <div class="glass-card">
            <!-- Header bar for Location Map -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fi fi-sr-marker text-primary fs-5"></i>
                    <h5 class="mb-0 text-white font-weight-600">Peta Lokasi Perangkat (Leaflet JS)</h5>
                </div>
                
                <!-- Status Badge -->
                <span class="badge bg-warning bg-opacity-10 border border-warning border-opacity-30 text-warning px-3 py-1.5 rounded-pill fs-8" id="locationStatus">
                    Meminta Koordinat GPS...
                </span>
            </div>

            <!-- Map View Container -->
            <div id="map" class="mb-4"></div>
            
            <!-- Coordinates details row -->
            <div class="row g-3">
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5">
                        <div class="text-secondary fs-8 mb-1">Latitude (Garis Lintang)</div>
                        <div class="text-white font-weight-600 fs-7" id="latitudeText"><?= is_numeric($loc['latitude']) ? number_format($loc['latitude'], 6) : 'N/A' ?></div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5">
                        <div class="text-secondary fs-8 mb-1">Longitude (Garis Garis)</div>
                        <div class="text-white font-weight-600 fs-7" id="longitudeText"><?= is_numeric($loc['longitude']) ? number_format($loc['longitude'], 6) : 'N/A' ?></div>
                    </div>
                </div>

                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5">
                        <div class="text-secondary fs-8 mb-1">Akurasi GPS (Provider)</div>
                        <div class="text-success font-weight-600 fs-7" id="accuracyText">
                            <?= (is_numeric($loc['accuracy']) && $loc['accuracy'] > 0) ? '± ' . number_format($loc['accuracy'], 1) . ' meter (' . htmlspecialchars($loc['provider']) . ')' : 'N/A' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Leaflet JS on this page -->
<script src="assets/leaflet/leaflet.js"></script>

<script>
    let map;
    let marker;
    
    // Server-provided coordinates
    let serverLat = <?= is_numeric($loc['latitude']) ? (float)$loc['latitude'] : 0 ?>;
    let serverLng = <?= is_numeric($loc['longitude']) ? (float)$loc['longitude'] : 0 ?>;
    let serverAccuracy = <?= is_numeric($loc['accuracy']) ? (float)$loc['accuracy'] : 0 ?>;
    
    // Initialize map
    function initMap(lat, lng, accuracy = null) {
        const statusBadge = document.getElementById('locationStatus');
        const latText = document.getElementById('latitudeText');
        const lngText = document.getElementById('longitudeText');
        const accText = document.getElementById('accuracyText');
        
        latText.textContent = lat !== 0 ? lat.toFixed(6) : 'N/A';
        lngText.textContent = lng !== 0 ? lng.toFixed(6) : 'N/A';
        accText.textContent = accuracy && accuracy > 0 ? `± ${accuracy.toFixed(1)} meter` : 'N/A';
        
        if (lat === 0 && lng === 0) {
            statusBadge.textContent = 'GPS Lokasi Tidak Tersedia';
            statusBadge.className = 'badge bg-danger bg-opacity-10 border border-danger border-opacity-30 text-danger px-3 py-1.5 rounded-pill fs-8';
            return;
        }

        if (!map) {
            map = L.map('map').setView([lat, lng], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            marker = L.marker([lat, lng]).addTo(map)
                .bindPopup('<b>Lokasi Perangkat</b><br>Handphone Server aktif di titik ini.')
                .openPopup();
        } else {
            map.setView([lat, lng], 15);
            marker.setLatLng([lat, lng]);
            marker.getPopup().setContent('<b>Lokasi Perangkat Terbaru</b>').openOn(map);
        }

        statusBadge.textContent = accuracy && accuracy > 0 ? 'GPS Perangkat Terkoneksi' : 'Lokasi Terakhir';
        statusBadge.className = accuracy && accuracy > 0 ? 
            'badge bg-success bg-opacity-10 border border-success border-opacity-30 text-success px-3 py-1.5 rounded-pill fs-8' : 
            'badge bg-secondary bg-opacity-10 border border-white border-opacity-10 text-secondary px-3 py-1.5 rounded-pill fs-8';
    }

    // Initialize with server-provided coordinates
    if (serverLat !== 0 || serverLng !== 0) {
        initMap(serverLat, serverLng, serverAccuracy);
    } else {
        // Fallback to default (Jakarta, Indonesia) if coordinates are zero
        initMap(-6.2088, 106.8456);
    }

    // Refresh location from device every 15 seconds
    function refreshDeviceLocation() {
        const querySep = window.location.search ? '&' : '?';
        fetch(window.location.pathname + window.location.search + querySep + 'api=1')
            .then(res => res.json())
            .then(data => {
                const lat = isNaN(parseFloat(data.latitude)) ? 0 : parseFloat(data.latitude);
                const lng = isNaN(parseFloat(data.longitude)) ? 0 : parseFloat(data.longitude);
                const acc = isNaN(parseFloat(data.accuracy)) ? 0 : parseFloat(data.accuracy);
                if (lat !== 0 && lng !== 0) {
                    initMap(lat, lng, acc);
                }
            })
            .catch(err => console.error('Error fetching GPS coordinates:', err));
    }
    
    // Periodically update coordinates
    setInterval(refreshDeviceLocation, 15000);
</script>
