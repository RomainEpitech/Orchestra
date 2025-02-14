# bin/Clean-backup.sh
#!/bin/bash

# Active l'interprétation des séquences d'échappement
TERM=xterm

# Couleurs et styles
RED=$(tput setaf 1)
GREEN=$(tput setaf 2)
YELLOW=$(tput setaf 3)
BLUE=$(tput setaf 4)
CYAN=$(tput setaf 6)
BOLD=$(tput bold)
NC=$(tput sgr0)

# Animation de chargement
spinner() {
    local pid=$1
    local message=$2
    local spin='⠋⠙⠹⠸⠼⠴⠦⠧⠇⠏'
    local charwidth=1
    local i=0
    tput civis # Cache le curseur
    while kill -0 $pid 2>/dev/null; do
        i=$(((i + $charwidth) % ${#spin}))
        printf "\r%s %s" "${spin:$i:$charwidth}" "$message"
        sleep .1
    done
    tput cnorm # Restore le curseur
    printf "\r✓ %s\n" "$message"
}

# Fonction pour afficher une bannière
print_banner() {
    printf "\n"
    printf "┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓\n"
    printf "┃        Nettoyage des Backups        ┃\n"
    printf "┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛\n"
    printf "\n"
}

# Animation de suppression
delete_file() {
    local file=$1
    printf "🗑️  Suppression de %s" "$file"
    for i in {1..3}; do
        printf "."
        sleep 0.3
    done
    printf " ✓\n"
}

# Fonction principale
main() {
    print_banner
    
    if [ ! -d "./backups" ]; then
        printf "⚠️  Le dossier backups n'existe pas\n\n"
        exit 0
    fi
    
    # Compte le nombre de fichiers
    local count=$(find ./backups -name "*.gz" -type f | wc -l)
    
    if [ $count -eq 0 ]; then
        printf "📂 Aucun fichier de backup à supprimer\n\n"
        exit 0
    fi
    
    printf "📊 Nombre de fichiers à supprimer : %s\n\n" "$count"
    
    # Suppression des fichiers
    find ./backups -name "*.gz" -type f | while read file; do
        delete_file "$(basename "$file")"
        rm -f "$file"
    done
    
    printf "\n✨ Nettoyage terminé avec succès !\n\n"
}

# Exécution du script
main