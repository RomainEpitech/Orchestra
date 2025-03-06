# 📊 Système de Journalisation Orchestra

Ce dossier contient les fichiers de journalisation centralisée pour le projet Orchestra. Le système capture automatiquement les événements importants comme la création d'entreprises, l'ajout de licences, les modifications de modules, etc.

## 📁 Structure des fichiers

- `journal.log` - Le fichier principal qui contient tous les événements journalisés
- D'autres fichiers de journalisation peuvent être ajoutés selon les besoins

## 🔍 Format des journaux

Les entrées du journal suivent ce format standard :

```
[YYYY-MM-DD HH:MM:SS] [EVENT] EVENT_TYPE: {"key": "value", ...}
```

Où :
- `EVENT_TYPE` indique le type d'événement qui s'est produit

### Types d'événements principaux

- **Entreprises** : `ENTERPRISE_CREATED`, `ENTERPRISE_UPDATED`, `ENTERPRISE_DELETED`
- **Licences** : `LICENSE_CREATED`, `LICENSE_DELETED`, `LICENSE_UPDATED`
- **Modules** : `MODULE_ACTIVATED`, `MODULE_UPGRADED`, `MODULE_DEACTIVATED`
- **Rôles** : `ROLE_CREATED`, `ROLE_UPDATED`, `ROLE_ASSIGNED`

## 🛠️ Utilisation et consultation des logs

### Visualisation des logs

Pour consulter les logs, vous pouvez simplement afficher le contenu du fichier `journal.log` :

```bash
# Afficher tous les logs
cat logs/journal.log

# Afficher les 50 dernières entrées
tail -n 50 logs/journal.log

# Suivre les nouveaux logs en temps réel
tail -f logs/journal.log
```

### Filtrage des logs

Vous pouvez utiliser des commandes standard comme `grep` pour filtrer les logs selon vos besoins :

```bash
# Filtrer les événements de création d'entreprise
grep "ENTERPRISE_CREATED" logs/journal.log

# Filtrer les événements concernant un utilisateur spécifique (par email)
grep "john@example.com" logs/journal.log

# Filtrer les événements par date
grep "2025-03-06" logs/journal.log
```

## 🔄 Intégration avec Laravel

Le système est intégré avec Laravel via les composants suivants :

1. **Service de journalisation** (`App\Services\SystemLoggerService`)
2. **Provider** (`App\Providers\SystemLoggerServiceProvider`) 
3. **Observers** pour les modèles principaux dans `App\Observers`

### Journaliser depuis le code PHP

```php
// Dans un contrôleur ou un service
public function someMethod(SystemLoggerService $logger)
{
    // Journaliser un événement d'entreprise
    $logger->logEnterpriseEvent('CUSTOM_ACTION', [
        'uuid' => $enterprise->uuid,
        'name' => $enterprise->name,
        'action' => 'détails de l'action'
    ]);
    
    // Journaliser un événement de licence
    $logger->logLicenseEvent('CUSTOM_ACTION', [
        'user_uuid' => $user->uuid,
        'email' => $user->email
    ]);
}
```

## 📈 Exemples d'entrées de journal

```
[2025-03-06 14:25:12] [EVENT] ENTERPRISE_CREATED: {"uuid":"550e8400-e29b-41d4-a716-446655440000","name":"Acme Corp","owner_uuid":"7b8e8acd-5a52-4983-8a3c-9b35c6428c1c","created_at":"2025-03-06T14:25:12+00:00"}
[2025-03-06 14:30:45] [EVENT] LICENSE_CREATED: {"uuid":"d7c0add1-5047-4354-a9ad-5e1f0b5c4f29","email":"john.doe@acmecorp.com","fullname":"John Doe","enterprise_uuid":"550e8400-e29b-41d4-a716-446655440000","enterprise_name":"Acme Corp","role_uuid":"2a4f9d14-5a77-4e4a-9c8b-8c8d39e0f4c1","role_name":"Administrateur","is_owner":false,"created_at":"2025-03-06T14:30:45+00:00"}
[2025-03-06 15:15:33] [EVENT] MODULE_PREMIUM_ACTIVATED: {"uuid":"f5b6c7a9-e10c-4d70-8f29-9f3a7c2e7d8b","enterprise_uuid":"550e8400-e29b-41d4-a716-446655440000","enterprise_name":"Acme Corp","module_uuid":"3b4c5e6a-7f8d-9e0a-1b2c-3d4e5f6a7b8c","module_name":"Personnel","module_key":"personnel","status":"active","is_premium":true,"type":"premium","created_at":"2025-03-06T15:15:33+00:00"}
```

## 🔒 Maintenance

- Les fichiers de journalisation peuvent grossir rapidement. Pensez à les archiver périodiquement.
- Vous pouvez configurer une rotation des logs en utilisant des outils comme `logrotate`.
- Considérez la mise en place d'une tâche cron pour compresser ou archiver les logs anciens.

```bash
# Exemple de commande pour archiver les logs quotidiennement
# Ajoutez ceci à votre crontab
0 0 * * * cp /path/to/logs/journal.log /path/to/logs/archives/journal_$(date +\%Y\%m\%d).log && > /path/to/logs/journal.log
```

---

Pour toute question ou suggestion d'amélioration de ce système, veuillez contacter l'équipe de développement Orchestra.