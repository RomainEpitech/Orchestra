.PHONY: help up build down migrate clean clean-backup backup restore dev-back

# Variables
DC=docker compose

# Couleurs
BLUE:=\033[34m
CYAN:=\033[36m
GREEN:=\033[32m
YELLOW:=\033[33m
PURPLE:=\033[35m
BOLD:=\033[1m
DIM:=\033[2m
NC:=\033[0m

help: ## Affiche l'aide
	@printf "\n$(BOLD)$(BLUE)📚 Orchestra - Liste des commandes disponibles:$(NC)\n"
	@printf "$(DIM)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(NC)\n"
	@printf "\n$(BOLD)$(GREEN)🚀 COMMANDES PRINCIPALES:$(NC)\n"
	@printf "$(DIM)  Commandes pour gérer l'application complète$(NC)\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*\[MAIN\]$$' $(MAKEFILE_LIST) | sort | sed -e 's/\[MAIN\]//g' | awk -v green="$(GREEN)" -v nc="$(NC)" 'BEGIN {FS = ":.*?## "}; {printf "  %s%-20s%s %s\n", green, $$1, nc, $$2}'
	@printf "\n$(DIM)────────────────────────────────────────────────────$(NC)\n"
	@printf "\n$(BOLD)$(YELLOW)🛠️  DÉVELOPPEMENT:$(NC)\n"
	@printf "$(DIM)  Commandes pour le développement local$(NC)\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*\[DEV\]$$' $(MAKEFILE_LIST) | sort | sed -e 's/\[DEV\]//g' | awk -v yellow="$(YELLOW)" -v nc="$(NC)" 'BEGIN {FS = ":.*?## "}; {printf "  %s%-20s%s %s\n", yellow, $$1, nc, $$2}'
	@printf "\n$(DIM)────────────────────────────────────────────────────$(NC)\n"
	@printf "\n$(BOLD)$(PURPLE)🔧 MAINTENANCE:$(NC)\n"
	@printf "$(DIM)  Commandes pour la maintenance du système$(NC)\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*\[MAINT\]$$' $(MAKEFILE_LIST) | sort | sed -e 's/\[MAINT\]//g' | awk -v purple="$(PURPLE)" -v nc="$(NC)" 'BEGIN {FS = ":.*?## "}; {printf "  %s%-20s%s %s\n", purple, $$1, nc, $$2}'
	@printf "\n$(DIM)────────────────────────────────────────────────────$(NC)\n"
	@printf "\n$(BOLD)$(CYAN)💾 DONNÉES:$(NC)\n"
	@printf "$(DIM)  Commandes pour la gestion des données$(NC)\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*\[DATA\]$$' $(MAKEFILE_LIST) | sort | sed -e 's/\[DATA\]//g' | awk -v cyan="$(CYAN)" -v nc="$(NC)" 'BEGIN {FS = ":.*?## "}; {printf "  %s%-20s%s %s\n", cyan, $$1, nc, $$2}'
	@printf "\n$(DIM)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(NC)\n"
	@printf "$(DIM)Utilisez 'make <command>' pour exécuter une commande$(NC)\n\n"

up: ## Démarre les conteneurs et initialise l'application [MAIN]
	@echo "🚀 Démarrage de l'application Orchestra..."
	$(DC) up -d
	@echo "⏳ Attente du démarrage de la base de données..."
	sleep 5
	@echo "🔄 Exécution des migrations..."
	$(DC) exec backend php artisan migrate
	@echo "✅ Application démarrée sur http://localhost:8080"
	@echo "📊 phpMyAdmin disponible sur http://localhost:8081"

build: ## Construit les images et exécute les migrations sans déployer [MAIN]
	@echo "🏗️  Construction des images..."
	$(DC) build --no-cache
	@echo "🔄 Exécution des migrations..."
	$(DC) run --rm backend php artisan migrate
	@echo "✅ Build terminé avec succès"

down: ## Arrête et supprime les conteneurs [MAIN]
	@echo "🛑 Arrêt de l'application..."
	$(DC) down
	@echo "✅ Application arrêtée"

migrate: ## Exécute les migrations [MAINT]
	@echo "🔄 Exécution des migrations..."
	$(DC) exec backend php artisan migrate

clean: ## Nettoie l'environnement Docker et les backups [MAINT]
	@printf "\n🧹 Nettoyage complet de l'environnement...\n"
	@$(DC) down -v
	@docker system prune -f
	@sh ./bin/Clean-docker.sh
	@sh ./bin/Clean-backup.sh

clean-backup: ## Nettoie uniquement les backups MySQL [DATA]
	@printf "\n🗑️  Nettoyage des backups...\n"
	@sh ./bin/Clean-backup.sh

backup: ## MySQL DB Backup [DATA]
	docker-compose exec -T backup sh -c "cd /opt/backup && ./backup.sh"

restore: ## MySQL DB Restore [DATA]
	$(DC) exec -T backup sh -c "cd /opt/backup && chmod +x Restore.sh && ./Restore.sh"

dev-back: ## Backend service start [DEV]
	@echo "🚀 Démarrage de l'environnement de développement backend..."
	$(DC) up -d backend db phpmyadmin nginx
	@echo "⏳ Attente du démarrage de la base de données..."
	sleep 5
	@echo "🔄 Exécution des migrations..."
	$(DC) exec backend php artisan migrate
	@echo "✅ Backend démarrée sur http://localhost:8080"
	@echo "📊 phpMyAdmin disponible sur http://localhost:8081"