#!/bin/sh

# Active l'interprétation des séquences d'échappement
TERM=xterm

# Configuration des variables identiques au backup
DB_HOST=${DB_HOST:-db}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
DB_DATABASE=${DB_DATABASE}
BACKUP_DIR="/backups"

# Installation des outils si nécessaire (comme dans backup.sh)
if ! command -v mysql >/dev/null 2>&1; then
    echo "📦 Installation des outils nécessaires..."
    apk add --no-cache mysql-client
fi

# Bannière
echo "┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓"
echo "┃      Restauration de la Backup      ┃"
echo "┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛"
echo ""

# Trouver le dernier backup
LATEST_BACKUP=$(ls -t ${BACKUP_DIR}/*.gz 2>/dev/null | head -n1)

if [ -z "$LATEST_BACKUP" ]; then
    echo "❌ Aucun fichier de backup trouvé dans ${BACKUP_DIR}"
    exit 1
fi

echo "📂 Fichier de backup : $(basename ${LATEST_BACKUP})"
echo "📅 Date : $(date -r ${LATEST_BACKUP})"
echo "📊 Base de données : ${DB_DATABASE}"
echo ""

# Debug des variables de connexion
echo "🔍 Vérification du contenu de la backup..."
gunzip < "${LATEST_BACKUP}" | head -n 20
echo "..."

echo "📊 Nombre de lignes dans la backup:"
gunzip < "${LATEST_BACKUP}" | wc -l

echo "🔄 Voulez-vous continuer la restauration? (y/n)"
read -p "> " confirm

if [ "$confirm" = "y" ]; then
    echo "🔄 Restauration en cours..."
    gunzip < "${LATEST_BACKUP}" | mysql \
        --protocol=tcp \
        --host="${DB_HOST}" \
        --user="${DB_USERNAME}" \
        --password="${DB_PASSWORD}" \
        --port="${MYSQL_TCP_PORT}" \
        "${DB_DATABASE}" 2>&1

    if [ $? -eq 0 ]; then
        echo "✅ Restauration terminée avec succès!"
        
        # Vérification des tables après restauration
        echo "📋 Tables restaurées :"
        mysql \
            --protocol=tcp \
            --host="${DB_HOST}" \
            --user="${DB_USERNAME}" \
            --password="${DB_PASSWORD}" \
            --port="${MYSQL_TCP_PORT}" \
            "${DB_DATABASE}" \
            -e "SHOW TABLES;"
    else
        echo "❌ Erreur lors de la restauration"
        exit 1
    fi
else
    echo "❌ Restauration annulée"
    exit 1
fi