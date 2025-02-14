.PHONY: up down build clean migrate backup

# Variables
DC=docker compose

# Couleurs pour le terminal
CYAN=\033[0;36m
NC=\033[0m # No Color

help: ## Affiche l'aide
	@printf "\n📚 Orchestra - Liste des commandes disponibles:\n"
	@printf "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  %-20s %s\n", $$1, $$2}'
	@printf "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"

up: ## Démarre les conteneurs et initialise l'application
	@echo "🚀 Démarrage de l'application Orchestra..."
	$(DC) up -d
	@echo "⏳ Attente du démarrage de la base de données..."
	sleep 5
	@echo "🔄 Exécution des migrations..."
	$(DC) exec backend php artisan migrate
	@echo "✅ Application démarrée sur http://localhost:8080"
	@echo "📊 phpMyAdmin disponible sur http://localhost:8081"

build: ## Construit les images et exécute les migrations sans déployer
	@echo "🏗️  Construction des images..."
	$(DC) build --no-cache
	@echo "🔄 Exécution des migrations..."
	$(DC) run --rm backend php artisan migrate
	@echo "✅ Build terminé avec succès"

down: ## Arrête et supprime les conteneurs
	@echo "🛑 Arrêt de l'application..."
	$(DC) down
	@echo "✅ Application arrêtée"

migrate: ## Exécute les migrations
	@echo "🔄 Exécution des migrations..."
	$(DC) exec backend php artisan migrate

clean: ## Nettoie l'environnement Docker et les backups
	@printf "\n🧹 Nettoyage complet de l'environnement...\n"
	@$(DC) down -v
	@docker system prune -f
	@sh ./bin/Clean-docker.sh
	@sh ./bin/Clean-backup.sh

clean-backup: ## Nettoie uniquement les backups MySQL
	@printf "\n🗑️  Nettoyage des backups...\n"
	@sh ./bin/Clean-backup.sh

backup: ## MySQL DB Backup
	docker-compose exec -T backup sh -c "cd /opt/backup && ./backup.sh"

restore: ## MySQL DB Restore
	$(DC) exec -T backup sh -c "cd /opt/backup && chmod +x Restore.sh && ./Restore.sh"