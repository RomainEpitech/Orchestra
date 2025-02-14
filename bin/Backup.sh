#!/bin/sh

# Active l'interprétation des séquences d'échappement
export TERM=xterm-color

# Couleurs et styles
RED='\e[31m'
GREEN='\e[32m'
YELLOW='\e[33m'
BLUE='\e[34m'
CYAN='\e[36m'
NC='\e[0m'
BOLD='\e[1m'

# Fonction pour les séparateurs
print_separator() {
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
}

# Fonction pour les messages d'information
info() {
    echo -e "${CYAN}[INFO]${NC} $1"
}

# Fonction pour les succès
success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

# Fonction pour les erreurs
error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Fonction pour les avertissements
warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

mkdir -p /backups

# Configuration
BACKUP_DIR="/backups"
KEEP_DAYS=${BACKUP_KEEP_DAYS:-7}
DATE=$(date +%Y%m%d_%H%M%S)
DATABASE=${DB_DATABASE}
HOST=${DB_HOST}
USER=${DB_USERNAME}

# Fonction de backup
perform_backup() {
    print_separator
    info "Starting backup process for database: ${BOLD}${DATABASE}${NC}"
    print_separator
    
    # Vérification de la connexion et des tables
    info "Vérification des tables dans la base de données..."
    TABLES_COUNT=$(mysql --protocol=tcp \
        --host=${HOST} \
        --user=${USER} \
        --password=${DB_PASSWORD} \
        --database=${DATABASE} \
        -N -e "SHOW TABLES;" | wc -l)
    
    info "${TABLES_COUNT} tables trouvées"

    # Vérification du nombre de lignes dans les tables
    info "Vérification du contenu des tables..."
    mysql --protocol=tcp \
        --host=${HOST} \
        --user=${USER} \
        --password=${DB_PASSWORD} \
        --database=${DATABASE} \
        -e "SELECT TABLE_NAME, TABLE_ROWS FROM information_schema.tables WHERE TABLE_SCHEMA='${DATABASE}';" 2>&1
    
    info "Creating backup..."
    BACKUP_FILE="${BACKUP_DIR}/${DATABASE}_${DATE}.sql.gz"
    
    mysqldump --protocol=tcp \
            --complete-insert \
            --add-drop-table \
            --create-options \
            --disable-keys \
            --extended-insert=FALSE \
            --skip-comments \
            --skip-lock-tables \
            --skip-dump-date \
            --triggers \
            --routines \
            --events \
            --host=${HOST} \
            --user=${USER} \
            --password=${DB_PASSWORD} \
            --databases ${DATABASE} 2>/dev/null | \
            gzip > "${BACKUP_FILE}"
    
    # Vérification de la taille
    BACKUP_SIZE=$(ls -lh ${BACKUP_FILE} | awk '{print $5}')
    if [ -s "${BACKUP_FILE}" ]; then
        print_separator
        success "Backup completed successfully!"
        success "${TABLES_COUNT} tables clonées"
        info "📁 Backup location: ${BOLD}${BACKUP_FILE}${NC}"
        info "📦 Backup size: ${BOLD}${BACKUP_SIZE}${NC}"
        info "📊 Nombre de lignes: $(gunzip < ${BACKUP_FILE} | wc -l)"
    else
        error "Backup file is empty or backup failed"
        rm -f "${BACKUP_FILE}"
        print_separator
        exit 1
    fi
    
    # Nettoyage des vieux backups
    info "Cleaning up old backups (older than ${KEEP_DAYS} days)..."
    find ${BACKUP_DIR} -type f -name "*.sql.gz" -mtime +${KEEP_DAYS} -delete
    success "Cleanup completed"
    
    # Afficher la liste des backups disponibles
    print_separator
    echo -e "${CYAN}${BOLD}Available Backups:${NC}"
    printf "${YELLOW}\n"
    find ${BACKUP_DIR} -name "*.sql.gz" -type f -exec ls -lh {} \; | \
        awk '{print $5, $9}' | while read size file; do
            filename=$(basename "$file")
            printf "├─ 📦 %-8s %s\n" "$size" "$filename"
        done
    printf "${NC}\n"
    print_separator
}

# Si le script est exécuté avec l'argument --cron, configure et démarre le cron
if [ "$1" = "--cron" ]; then
    info "Setting up cron job..."
    
    # Installation de crond et autres utilitaires nécessaires
    apk add --no-cache dcron mysql-client
    
    # Création du dossier pour les logs
    mkdir -p /var/log
    touch /var/log/cron.log
    
    # Configuration du cron
    mkdir -p /etc/cron.d
    echo "${BACKUP_FREQUENCY} /opt/backup/backup.sh >> /var/log/cron.log 2>&1" > /etc/cron.d/backup-cron
    chmod 0644 /etc/cron.d/backup-cron
    
    success "Cron job configured with schedule: ${BOLD}${BACKUP_FREQUENCY}${NC}"
    info "Starting cron daemon..."
    
    # Démarrage du cron daemon
    crond -f -d 8
else
    # Exécution simple du backup
    perform_backup
fi