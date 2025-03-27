<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-900 px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <!-- Logo/Header -->
            <div class="text-center">
            <h2 class="mt-6 text-3xl font-bold tracking-tight text-white">
                Connexion
            </h2>
            <p class="mt-2 text-sm text-gray-400">
                Veuillez saisir vos identifiants pour vous connecter
            </p>
            </div>
            
            <!-- Form -->
            <form class="mt-8 space-y-6" @submit.prevent="login">
            <div class="space-y-6 rounded-md shadow-sm">
                <!-- Email Field -->
                <div>
                <label for="email" class="block text-sm font-medium text-gray-300">
                    Adresse email
                </label>
                <div class="mt-1">
                    <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    v-model="credentials.email"
                    class="block w-full appearance-none rounded-md border border-gray-700 bg-gray-800 px-3 py-2 placeholder-gray-500 text-white shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                    placeholder="nom@exemple.com"
                    />
                </div>
                </div>
                
                <!-- Password Field -->
                <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-gray-300">
                    Mot de passe
                    </label>
                    <div class="text-sm">
                    <a href="#" class="font-medium text-indigo-400 hover:text-indigo-300">
                        Mot de passe oublié?
                    </a>
                    </div>
                </div>
                <div class="mt-1">
                    <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    v-model="credentials.password"
                    class="block w-full appearance-none rounded-md border border-gray-700 bg-gray-800 px-3 py-2 placeholder-gray-500 text-white shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                    placeholder="••••••••"
                    />
                </div>
                </div>
            </div>
            
            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                <input
                    id="remember-me"
                    name="remember-me"
                    type="checkbox"
                    v-model="credentials.remember"
                    class="h-4 w-4 rounded border-gray-700 bg-gray-800 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-gray-900"
                />
                <label for="remember-me" class="ml-2 block text-sm text-gray-300">
                    Se souvenir de moi
                </label>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div>
                <button
                type="submit"
                :disabled="loading"
                class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900 disabled:opacity-70"
                >
                <span v-if="loading" class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <!-- Loading spinner -->
                    <svg class="h-5 w-5 animate-spin text-indigo-400" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <span>{{ loading ? 'Connexion en cours...' : 'Se connecter' }}</span>
                </button>
            </div>
            </form>
            
            <!-- Error Message -->
            <div v-if="errorMessage" class="mt-3 rounded-md bg-red-900/50 p-3 text-sm text-red-300">
            {{ errorMessage }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                credentials: {
                    email: '',
                    password: '',
                    remember: false
                },
                loading: false,
                errorMessage: ''
            }
            },
            methods: {
            async login() {
                this.loading = true;
                this.errorMessage = '';
                
                try {
                // Remplacer par votre appel API réel
                const response = await this.$axios.post('/api/login', this.credentials);
                
                // Stocker le token
                localStorage.setItem('token', response.data.data.token);
                
                // Configurer les en-têtes pour les futures requêtes
                this.$axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.data.token}`;
                
                // Rediriger vers le dashboard
                this.$router.push({ name: 'dashboard' });
                } catch (error) {
                console.error('Login failed', error);
                
                if (error.response && error.response.data && error.response.data.message) {
                    this.errorMessage = error.response.data.message;
                } else {
                    this.errorMessage = 'Une erreur est survenue lors de la connexion';
                }
                } finally {
                this.loading = false;
                }
            }
        }
    }
</script>