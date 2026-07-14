<!-- Load Leaflet CSS only on this page -->
<link rel="stylesheet" href="assets/leaflet/leaflet.css">

<div class="row g-4">
    <div class="col-12">
        <div class="glass-card">
            <!-- Header bar for Location Map -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fi fi-sr-marker text-primary fs-5"></i>
                    <h5 class="mb-0 text-white font-weight-600">Peta Lokasi Saat Ini (Leaflet JS)</h5>
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
                        <div class="text-white font-weight-600 fs-7" id="latitudeText">-6.2088</div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5">
                        <div class="text-secondary fs-8 mb-1">Longitude (Garis Bujur)</div>
                        <div class="text-white font-weight-600 fs-7" id="longitudeText">106.8456</div>
                    </div>
                </div>

                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-black bg-opacity-20 rounded-12 border border-white border-opacity-5">
                        <div class="text-secondary fs-8 mb-1">Akurasi GPS</div>
                        <div class="text-success font-weight-600 fs-7" id="accuracyText">N/A (Simulated)</div>
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
    
    // Default location (Jakarta, Indonesia)
    let defaultLat = -6.2088;
    let defaultLng = 106.8456;
    
    // Initialize map
    function initMap(lat, lng, accuracy = null) {
        const statusBadge = document.getElementById('locationStatus');
        const latText = document.getElementById('latitudeText');
        const lngText = document.getElementById('longitudeText');
        const accText = document.getElementById('accuracyText');
        
        latText.textContent = lat.toFixed(6);
        lngText.textContent = lng.toFixed(6);
        accText.textContent = accuracy ? `± ${accuracy.toFixed(1)} meter` : 'Mode Default (Jakarta)';
        
        if (!map) {
            // Create map instance
            map = L.map('map').setView([lat, lng], 15);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Create marker
            marker = L.marker([lat, lng]).addTo(map)
                .bindPopup('<b>Lokasi Perangkat</b><br>Mobile Server aktif di titik ini.')
                .openPopup();
        } else {
            // Update map view and marker position
            map.setView([lat, lng], 15);
            marker.setLatLng([lat, lng]);
            marker.getPopup().setContent('<b>Lokasi Perangkat Terbaru</b>').openOn(map);
        }

        statusBadge.textContent = accuracy ? 'GPS Terkoneksi' : 'Lokasi Default';
        statusBadge.className = accuracy ? 
            'badge bg-success bg-opacity-10 border border-success border-opacity-30 text-success px-3 py-1.5 rounded-pill fs-8' : 
            'badge bg-secondary bg-opacity-10 border border-white border-opacity-10 text-secondary px-3 py-1.5 rounded-pill fs-8';
    }

    // Try HTML5 Geolocation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = position.coords.accuracy;
                
                initMap(lat, lng, accuracy);
            },
            (error) => {
                console.warn('Geolocation Error:', error);
                // Fallback to default (Jakarta)
                initMap(defaultLat, defaultLng);
            },
            { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
        );
    } else {
        // Browser doesn't support geolocation
        initMap(defaultLat, defaultLng);
    }
</script>
