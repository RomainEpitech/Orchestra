<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Langues supportées
        $supportedLocales = ['en', 'fr'];
        
        // Récupérer la langue depuis l'en-tête Accept-Language
        $locale = $request->header('Accept-Language');
        
        // Extraire le code de langue de base si nécessaire (e.g., "fr-FR" -> "fr")
        if ($locale) {
            $locale = explode('-', $locale)[0];
        }
        
        // Vérifier si la langue est supportée, sinon utiliser l'anglais par défaut
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'en';
        }
        
        // Définir la locale pour cette requête
        app()->setLocale($locale);
        
        return $next($request);
    }
}