@component('mail::message')
# Bienvenue sur Orchestra

Bonjour {{ $user->firstname }},

L'entreprise **{{ $enterprise->name }}** a activé votre licence sur Orchestra.

Vous pouvez désormais vous connecter à l'application avec les identifiants suivants:

**Email**: {{ $user->email }}  
**Mot de passe temporaire**: {{ $temporaryPassword }}

N'oubliez pas de changer votre mot de passe lors de votre première connexion.

@component('mail::button', ['url' => config('app.frontend_url').'/login'])
Se connecter à Orchestra
@endcomponent

À bientôt,  
L'équipe Orchestra
@endcomponent