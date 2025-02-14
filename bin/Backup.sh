#!/bin/sh

# Création du répertoire de backup s'il n'existe pas
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
    echo "Starting backup of ${DATABASE} database..."
    
    # Utilisation de --protocol=tcp et --enable-cleartext-plugin pour la compatibilité
    mysqldump --protocol=tcp --column-statistics=0 --host=${HOST} --user=${USER} --password=${DB_PASSWORD} ${DATABASE} 2>/dev/null | gzip > ${BACKUP_DIR}/${DATABASE}_${DATE}.sql.gz
    
    if [ -s "${BACKUP_DIR}/${DATABASE}_${DATE}.sql.gz" ]; then
        echo "Database backup successfully completed for ${DATABASE}"
        echo "Backup saved as: ${BACKUP_DIR}/${DATABASE}_${DATE}.sql.gz"
        echo "Backup size: $(ls -lh ${BACKUP_DIR}/${DATABASE}_${DATE}.sql.gz | awk '{print $5}')"
    else
        echo "Error: Backup file is empty or backup failed for ${DATABASE}"
        rm -f "${BACKUP_DIR}/${DATABASE}_${DATE}.sql.gz"
        exit 1
    fi
    
    # Nettoyage des vieux backups
    find ${BACKUP_DIR} -type f -name "*.sql.gz" -mtime +${KEEP_DAYS} -delete
    echo "Old backups cleaned up"
    
    # Afficher la liste des backups disponibles
    echo "Available backups:"
    ls -lh ${BACKUP_DIR}
}

# Si le script est exécuté avec l'argument --cron, configure et démarre le cron
if [ "$1" = "--cron" ]; then
    # Installation de crond et autres utilitaires nécessaires
    apk add --no-cache dcron mysql-client

    # Création du dossier pour les logs
    mkdir -p /var/log
    touch /var/log/cron.log

    # Configuration du cron
    mkdir -p /etc/cron.d
    echo "${BACKUP_FREQUENCY} /opt/backup/backup.sh >> /var/log/cron.log 2>&1" > /etc/cron.d/backup-cron
    chmod 0644 /etc/cron.d/backup-cron

    # Démarrage du cron daemon
    crond -f -d 8
else
    # Exécution simple du backup
    perform_backup
fi