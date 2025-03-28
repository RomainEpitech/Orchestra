<template>
    <div class="min-h-screen flex items-center justify-center overflow-hidden relative">
        <!-- Fond et animations cosmiques (inchangés) -->
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

        <div class="relative z-10 w-full max-w-lg mx-auto p-8">
            <div class="backdrop-blur-sm bg-gray-900/60 border border-gray-800 rounded-2xl shadow-xl relative overflow-hidden p-6">
                <div 
                    class="absolute -top-24 -left-24 w-48 h-48 rounded-full opacity-25" 
                    style="background: radial-gradient(circle at center, rgba(139, 92, 246, 0.8), transparent 70%); filter: blur(40px);"
                />
    
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold tracking-tight">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-violet-400 via-purple-500 to-indigo-500">
                            Créer un compte
                        </span>
                    </h2>
                    <p class="mt-2 text-sm text-gray-400">
                        Rejoignez-nous pour accéder à toutes les fonctionnalités
                    </p>
                </div>
                
                <!-- Section des erreurs - Affichage amélioré avec des cartes individuelles -->
                <div v-if="validationErrors.length > 0" class="mb-6 space-y-2">
                    <div v-for="(error, index) in validationErrors" :key="index" 
                        class="flex items-start p-3 bg-red-900/40 border border-red-800/70 rounded-lg text-sm backdrop-blur-sm animate-fadeIn">
                        <!-- Icône d'erreur -->
                        <div class="flex-shrink-0 mr-2 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <!-- Message d'erreur -->
                        <div class="text-red-300 flex-1">{{ error }}</div>
                    </div>
                </div>
            
                <form class="space-y-4" @submit.prevent="register">
                    <div>
                        <label for="enterprise_name" class="block text-sm font-medium text-gray-300">
                            Nom de l'entreprise
                        </label>
                        <div class="relative mt-1">
                            <input
                                id="enterprise_name"
                                name="enterprise_name"
                                type="text"
                                v-model="formData.enterprise_name"
                                :class="{'border-red-500 focus:ring-red-500': hasFieldError('enterprise_name')}"
                                required
                                class="block w-full px-4 py-3 rounded-lg border border-gray-700 bg-gray-800/50 text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                                placeholder="Nom de votre entreprise"
                            />
                            <div class="absolute inset-0 rounded-lg opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none" 
                                style="background: linear-gradient(90deg, rgba(139, 92, 246, 0) 0%, rgba(139, 92, 246, 0.1) 50%, rgba(139, 92, 246, 0) 100%); background-size: 200% 100%; animation: shimmer 2s infinite;">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-300">
                                Prénom
                            </label>
                            <div class="relative mt-1">
                                <input
                                    id="first_name"
                                    name="first_name"
                                    type="text"
                                    v-model="formData.first_name"
                                    :class="{'border-red-500 focus:ring-red-500': hasFieldError('first_name')}"
                                    required
                                    class="block w-full px-4 py-3 rounded-lg border border-gray-700 bg-gray-800/50 text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Votre prénom"
                                />
                                <div class="absolute inset-0 rounded-lg opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none" 
                                    style="background: linear-gradient(90deg, rgba(139, 92, 246, 0) 0%, rgba(139, 92, 246, 0.1) 50%, rgba(139, 92, 246, 0) 100%); background-size: 200% 100%; animation: shimmer 2s infinite;">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-300">
                                Nom
                            </label>
                            <div class="relative mt-1">
                                <input
                                    id="last_name"
                                    name="last_name"
                                    type="text"
                                    v-model="formData.last_name"
                                    :class="{'border-red-500 focus:ring-red-500': hasFieldError('last_name')}"
                                    required
                                    class="block w-full px-4 py-3 rounded-lg border border-gray-700 bg-gray-800/50 text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Votre nom"
                                />
                                <div class="absolute inset-0 rounded-lg opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none" 
                                    style="background: linear-gradient(90deg, rgba(139, 92, 246, 0) 0%, rgba(139, 92, 246, 0.1) 50%, rgba(139, 92, 246, 0) 100%); background-size: 200% 100%; animation: shimmer 2s infinite;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300">
                            Adresse email
                        </label>
                        <div class="relative mt-1">
                            <input
                                id="email"
                                name="email"
                                type="email"
                                v-model="formData.email"
                                :class="{'border-red-500 focus:ring-red-500': hasFieldError('email')}"
                                required
                                autocomplete="email"
                                class="block w-full px-4 py-3 rounded-lg border border-gray-700 bg-gray-800/50 text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                                placeholder="nom@exemple.com"
                            />
                            <div class="absolute inset-0 rounded-lg opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none" 
                                style="background: linear-gradient(90deg, rgba(139, 92, 246, 0) 0%, rgba(139, 92, 246, 0.1) 50%, rgba(139, 92, 246, 0) 100%); background-size: 200% 100%; animation: shimmer 2s infinite;">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300">
                            Mot de passe
                        </label>
                        <div class="relative mt-1">
                            <input
                                id="password"
                                name="password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="formData.password"
                                :class="{'border-red-500 focus:ring-red-500': hasFieldError('password')}"
                                required
                                autocomplete="new-password"
                                class="block w-full px-4 py-3 rounded-lg border border-gray-700 bg-gray-800/50 text-white placeholder-gray-500 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                                placeholder="••••••••"
                            />
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-200"
                            >
                                <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <div class="absolute inset-0 rounded-lg opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none" 
                                style="background: linear-gradient(90deg, rgba(139, 92, 246, 0) 0%, rgba(139, 92, 246, 0.1) 50%, rgba(139, 92, 246, 0) 100%); background-size: 200% 100%; animation: shimmer 2s infinite;">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-300">
                            Confirmer le mot de passe
                        </label>
                        <div class="relative mt-1">
                            <input
                                id="confirm_password"
                                name="confirm_password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="formData.confirm_password"
                                :class="{'border-red-500 focus:ring-red-500': hasFieldError('confirm_password')}"
                                required
                                autocomplete="new-password"
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
                                {{ loading ? 'Création en cours...' : 'Créer mon compte' }}
                                <span v-if="!loading" class="ml-2 inline-block group-hover:translate-x-1 transition-transform duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14"></path>
                                        <path d="M12 5l7 7-7 7"></path>
                                    </svg>
                                </span>
                            </span>
                        </button>
                    </div>

                    <div class="text-center text-sm mt-4">
                        <span class="text-gray-400">Vous avez déjà un compte?</span>
                        <router-link to="/login" class="ml-1 font-medium text-violet-400 hover:text-violet-300 transition-colors duration-200">
                            Se connecter
                        </router-link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import { defineComponent, ref } from 'vue';
    import { useRouter } from 'vue-router';
    import apiFetch from './../../utils/apiFetch';

    export default defineComponent({
        name: 'Register',
        setup() {
            const router = useRouter();
            const formData = ref({
                enterprise_name: '',
                first_name: '',
                last_name: '',
                email: '',
                password: '',
                confirm_password: ''
            });
            const loading = ref(false);
            const validationErrors = ref<string[]>([]);
            const errorFields = ref<Record<string, boolean>>({});
            const showPassword = ref(false);

            const hasFieldError = (fieldName: string) => {
                return errorFields.value[fieldName] === true;
            };

            const validateForm = () => {
                validationErrors.value = [];
                errorFields.value = {};
                
                if (formData.value.password !== formData.value.confirm_password) {
                    validationErrors.value.push('Les mots de passe ne correspondent pas');
                    errorFields.value.password = true;
                    errorFields.value.confirm_password = true;
                    return false;
                }
                
                if (formData.value.password.length < 8) {
                    validationErrors.value.push('Le mot de passe doit contenir au moins 8 caractères');
                    errorFields.value.password = true;
                    return false;
                }
                
                return true;
            };

            const register = async () => {
                if (!validateForm()) return;
                
                loading.value = true;
                validationErrors.value = [];
                errorFields.value = {};
                
                try {
                    const response = await apiFetch.post('/enterprise/register', {
                        enterprise_name: formData.value.enterprise_name,
                        first_name: formData.value.first_name,
                        last_name: formData.value.last_name,
                        email: formData.value.email,
                        password: formData.value.password,
                        confirm_password: formData.value.confirm_password
                    });
                    
                    router.push({ name: 'login' });
                } catch (error: any) {
                    console.error('Registration failed', error);
                    
                    if (error.errors && Object.keys(error.errors).length > 0) {
                        Object.entries(error.errors).forEach(([field, messages]) => {
                            const messageArray = Array.isArray(messages) ? messages : [String(messages)];
                            validationErrors.value.push(messageArray[0]);
                            errorFields.value[field] = true;
                        });
                    } else {
                        validationErrors.value.push(error.message || "Une erreur s'est produite lors de l'inscription");
                    }
                } finally {
                    loading.value = false;
                }
            };
        
            return {
                formData,
                loading,
                validationErrors,
                errorFields,
                showPassword,
                register,
                hasFieldError
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
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>