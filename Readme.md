# 🎵 Orchestra

Orchestra est une plateforme SaaS modulaire d'intranet d'entreprise conçue pour faciliter la gestion des ressources humaines, des rôles, des autorisations et d'autres aspects de la vie d'entreprise à travers un système flexible de modules.

## 📋 Aperçu

Orchestra permet aux entreprises de créer leur propre espace intranet personnalisé où les employés peuvent collaborer, communiquer et accéder aux ressources nécessaires selon leurs rôles et leurs autorisations. La plateforme est construite autour d'un système de modules qui permet d'ajouter ou de retirer des fonctionnalités selon les besoins spécifiques de chaque entreprise.

### ✨ Principales fonctionnalités

- **Gestion des entreprises** : Création et administration d'espaces d'entreprise dédiés
- **Gestion des utilisateurs** : Ajout, suppression et gestion des licences utilisateurs
- **Système de rôles et autorisations** : Contrôle d'accès granulaire basé sur des rôles hiérarchiques
- **Architecture modulaire** : Ajout de fonctionnalités via un système de modules extensible
- **Versions gratuites et premium** : Différentes limites selon le plan de l'entreprise

## 🏗️ Architecture technique

Orchestra est développé avec les technologies suivantes :

- **Backend** : Laravel (PHP)
- **Base de données** : MySQL
- **Conteneurisation** : Docker et Docker Compose
- **Serveur web** : Nginx
- **Environnement de développement** : Conteneurs Docker pour le développement local

## 🛠️ Configuration requise

- Docker et Docker Compose
- Make (pour les commandes de l'utilitaire Makefile)
- Git

## 🚀 Installation

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/your-username/orchestra.git
   cd orchestra
   ```

2. Copiez le fichier d'environnement et configurez vos variables :
   ```bash
   cp .env.example .env
   ```

3. Lancez l'application avec Make :
   ```bash
   make up
   ```

4. Accédez à l'application via :
   - Backend API : http://localhost:8080
   - phpMyAdmin : http://localhost:8081

## 📦 Modules disponibles

Orchestra est conçu pour fonctionner avec plusieurs modules. Actuellement, les modules suivants sont disponibles :

- **Enterprise** : Module de base pour la gestion des informations d'entreprise
- **Personnel** : Gestion des employés et des utilisateurs (avec limite de 10 utilisateurs en version gratuite)

D'autres modules sont prévus pour les versions futures, notamment :
- Gestion des réunions
- Messagerie instantanée
- Gestion d'événements
- Rapports et analyses
- Et bien plus encore...

## 🔧 Commandes utiles

Orchestra utilise un Makefile pour simplifier les opérations courantes :

```bash
# Afficher l'aide
make help

# Démarrer l'application complète
make up

# Construire les images Docker
make build

# Arrêter l'application
make down

# Exécuter les migrations
make migrate

# Démarrer l'environnement de développement backend
make dev-back

# Sauvegarder la base de données
make backup

# Restaurer la base de données
make restore

# Nettoyer l'environnement Docker et les backups
make clean
```

## 🧪 Tests

Orchestra comprend des tests unitaires et fonctionnels pour garantir la qualité du code :

```bash
# Exécuter tous les tests
make test

# Exécuter un test unitaire spécifique
make unit-test TestName

# Exécuter un test de fonctionnalité spécifique
make feature-test TestName
```

## 🔐 Sécurité et autorisations

Orchestra implémente un système sophistiqué de contrôle d'accès basé sur les rôles :

- Les rôles ont des niveaux hiérarchiques définissant leurs relations de pouvoir
- Les autorisations sont définies par module et par action
- Les administrateurs ne peuvent gérer que les utilisateurs de niveaux hiérarchiques inférieurs
- Le propriétaire de l'entreprise a des privilèges spéciaux, comme la capacité de gérer tous les utilisateurs

## 🌱 Extensibilité

Le système de modules d'Orchestra permet une grande flexibilité :

- Chaque module peut avoir une version gratuite avec des limites et une version premium
- Les modules peuvent être activés ou désactivés par entreprise
- Le système est conçu pour faciliter l'ajout de nouveaux modules sans modifier le cœur de l'application

## 👥 Contributeurs

Saskoue

---

Développé avec ❤️ par l'équipe Orchestra