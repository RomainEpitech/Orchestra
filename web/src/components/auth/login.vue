<template>
    <div class="min-h-screen flex items-center justify-center overflow-hidden relative">
        <div class="fixed inset-0" style="background: radial-gradient(circle at 50% 50%, #0f172a 0%, #000 100%)">
            <div 
                class="absolute inset-0"
                style="
                    background-image: radial-gradient(circle at center, rgba(139, 92, 246, 0.03) 0%, transparent 8%);
                    background-size: 60px 60px;
                    background-position: center;
                "
            />
        </div>
    
        <div 
            class="absolute w-96 h-96 rounded-full opacity-20"
            style="
                background: conic-gradient(from 0deg, #7c3aed, #9333ea, #6d28d9);
                filter: blur(60px);
                animation: pulse 8s ease-in-out infinite, rotate 20s linear infinite;
            "
        />
    
        <div class="absolute inset-0">
            <div class="absolute inset-0 opacity-20" 
                style="
                    background-image: linear-gradient(to right, transparent, rgba(139, 92, 246, 0.1) 15%, rgba(139, 92, 246, 0.1) 85%, transparent),
                                    linear-gradient(to bottom, transparent, rgba(139, 92, 246, 0.1) 15%, rgba(139, 92, 246, 0.1) 85%, transparent);
                    background-size: 100% 1px, 1px 100%;
                    background-position: center;
                    background-repeat: no-repeat;
                "
            />
        </div>
    
        <div class="relative z-10 w-full max-w-md mx-auto p-8">
            <div class="backdrop-blur-sm bg-gray-900/60 border border-gray-800 rounded-2xl shadow-xl relative overflow-hidden p-6">
                <div 
                    class="absolute -top-24 -left-24 w-48 h-48 rounded-full opacity-25" 
                    style="background: radial-gradient(circle at center, rgba(139, 92, 246, 0.8), transparent 70%); filter: blur(40px);"
                />
    
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold tracking-tight">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-violet-400 via-purple-500 to-indigo-500">
                            Connexion
                        </span>
                    </h2>
                    <p class="mt-2 text-sm text-gray-400">
                        Accédez à votre espace personnel
                    </p>
                </div>
            
            <form class="space-y-6" @submit.prevent="login">
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-300">
                        Adresse email
                    </label>
                    <div class="relative">
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            v-model="credentials.email"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-700 bg-gray-800/50 text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                            placeholder="nom@exemple.com"
                        />
                        <div class="absolute inset-0 rounded-lg opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none" 
                            style="background: linear-gradient(90deg, rgba(139, 92, 246, 0) 0%, rgba(139, 92, 246, 0.1) 50%, rgba(139, 92, 246, 0) 100%); background-size: 200% 100%; animation: shimmer 2s infinite;">
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-gray-300">
                            Mot de passe
                        </label>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-violet-400 hover:text-violet-300 transition-colors duration-200">
                                Mot de passe oublié?
                            </a>
                        </div>
                    </div>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            v-model="credentials.password"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-700 bg-gray-800/50 text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                            placeholder="••••••••"
                        />
                        <div class="absolute inset-0 rounded-lg opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none" 
                            style="background: linear-gradient(90deg, rgba(139, 92, 246, 0) 0%, rgba(139, 92, 246, 0.1) 50%, rgba(139, 92, 246, 0) 100%); background-size: 200% 100%; animation: shimmer 2s infinite;">
                        </div>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="group relative w-full flex justify-center items-center px-4 py-3 text-sm font-medium rounded-lg overflow-hidden transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]"
                    >
                        <span class="absolute inset-0 bg-gradient-to-r from-violet-500 via-purple-500 to-indigo-500 rounded-lg" />
                        <span class="absolute inset-0 bg-gradient-to-r from-violet-400 via-purple-500 to-indigo-500 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
                        
                        <span v-if="loading" class="absolute left-4 flex items-center">
                            <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        <span class="relative text-white flex items-center">
                            {{ loading ? 'Connexion en cours...' : 'Se connecter' }}
                            <span v-if="!loading" class="ml-2 inline-block group-hover:translate-x-1 transition-transform duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"></path>
                                    <path d="M12 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </span>
                    </button>
                </div>
            </form>
            
            <div v-if="errorMessage" class="mt-4 p-3 bg-red-900/50 border border-red-800 rounded-lg text-sm text-red-300">
                {{ errorMessage }}
            </div>
                <div class="mt-6 text-center text-sm">
                    <span class="text-gray-400">Pas encore de compte?</span>
                    <a href="/register" class="ml-1 font-medium text-violet-400 hover:text-violet-300 transition-colors duration-200">
                        Créer un compte
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
    import { defineComponent, ref } from 'vue';
    import { useRouter } from 'vue-router';
    import apiFetch from './../../utils/apiFetch';
    
    export default defineComponent({
        setup() {
            const router = useRouter();
            const credentials = ref({
                email: '',
                password: '',
            });
            const loading = ref(false);
            const errorMessage = ref('');
    
            const login = async () => {
                loading.value = true;
                errorMessage.value = '';
                
                try {
                    const response = await apiFetch.post<{ message: string, data: { token: string, user: any } }>('/auth/login', credentials.value);
                    
                    if (response && response.data && response.data.token) {
                        localStorage.setItem('token', response.data.token);
                        localStorage.setItem('user', JSON.stringify(response.data.user));
                        router.push({ name: 'dashboard' });
                    } else {
                        errorMessage.value = "Échec de l'authentification: Aucun token reçu";
                    }
                } catch (error: any) {
                    console.error('Login failed', error);
                    errorMessage.value = error.message;
                } finally {
                    loading.value = false;
                }
            };
        
            return {
                credentials,
                loading,
                errorMessage,
                login
            };
        }
    });
</script>

<style scoped>
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); opacity: 0; }
        50% { transform: translateY(-30px); opacity: 1; }
    }
</style>