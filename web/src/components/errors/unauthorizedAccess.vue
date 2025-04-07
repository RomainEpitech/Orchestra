<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                    Accès non autorisé
                </h2>
                <p class="mt-2 text-center text-sm text-gray-400">
                    Vous n'avez pas les permissions nécessaires pour accéder à cette page.
                </p>
            </div>
            <div class="flex flex-col items-center justify-center">
                <div class="mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H9m3-3V8m0 0V6m0 2h2M9 8h2m0 12a9 9 0 11.001-18.001A9 9 0 0112 20z" />
                    </svg>
                </div>
                <p class="text-gray-300 text-center mb-8">
                    Cette section nécessite des privilèges particuliers qui n'ont pas été accordés à votre compte utilisateur.
                </p>
                <div class="flex space-x-4">
                    <router-link 
                        to="/dashboard" 
                        class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Retour au tableau de bord
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import { defineComponent } from 'vue';

    export default defineComponent({
        name: 'UnauthorizedAccess',
    });
</script>