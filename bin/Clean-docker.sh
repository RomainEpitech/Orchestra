#!/bin/bash

echo "🔄 Arrêt des conteneurs..."
docker-compose down

echo "🗑️  Suppression des volumes..."
docker-compose down -v

echo "🗑️  Suppression des conteneurs..."
docker rm -f $(docker ps -a -q) 2>/dev/null || true

echo "🗑️  Suppression des images..."
docker rmi -f $(docker images -a -q) 2>/dev/null || true

echo "🧹 Nettoyage des volumes..."
docker volume rm $(docker volume ls -q) 2>/dev/null || true

echo "🧹 Nettoyage des builds..."
docker builder prune -a --force

echo "🧹 Nettoyage du cache système Docker..."
docker system prune -a --force

echo "✨ Nettoyage complet terminé!"