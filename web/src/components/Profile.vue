<template>
    <div class="max-w-4xl mx-auto bg-gray-800/40 backdrop-blur-sm rounded-lg border border-gray-700 shadow-xl overflow-hidden">
        <div class="relative h-48 bg-gradient-to-r from-violet-600 to-indigo-700">
            <div class="absolute inset-0 opacity-10" 
                style="background-size: 40px;"
            />
            
            <div class="absolute -bottom-12 left-8">
                <div class="p-1.5 rounded-full bg-gray-800/40 backdrop-blur-sm">
                    <div v-if="userAvatar" class="w-24 h-24 rounded-full overflow-hidden">
                        <img :src="userAvatar" alt="Avatar utilisateur" class="w-full h-full object-cover" />
                    </div>
                    <div v-else class="w-24 h-24 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center">
                        <span class="text-3xl font-bold text-white">{{ userInitials }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pt-16 px-8 pb-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ userData.firstname }} {{ userData.lastname }}</h1>
                    <p class="text-gray-400">{{ userData.role?.name || 'Utilisateur' }}</p>
                </div>
                
                <div class="mt-4 md:mt-0">
                    <button 
                        @click="navigateToUpdateProfile" 
                        class="inline-flex items-center px-4 py-2 bg-violet-600 rounded-md text-white text-sm font-medium hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-violet-500 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Modifier le profile
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-700/20 p-6 rounded-lg border border-gray-700">
                    <h2 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-violet-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        Informations personnelles
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-400">Nom</p>
                            <p class="text-white">{{ userData.lastname }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Prénom</p>
                            <p class="text-white">{{ userData.firstname }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Email</p>
                            <p class="text-white">{{ userData.email }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-700/20 p-6 rounded-lg border border-gray-700">
                    <h2 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-violet-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
                        </svg>
                        Entreprise
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-400">Entreprise</p>
                            <p class="text-white">{{ userData.enterprise?.name || 'Non spécifié' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Rôle</p>
                            <div class="flex items-center">
                                <span 
                                    class="inline-block w-3 h-3 rounded-full mr-2" 
                                    :style="{ backgroundColor: userData.role?.color_hex || '#9333EA' }"
                                />
                                <p class="text-white">{{ userData.role?.name || 'Utilisateur standard' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';

export default defineComponent({
    name: 'ProfileView',
    
    setup() {
        const router = useRouter();
        const userData = ref({
            firstname: '',
            lastname: '',
            email: '',
            avatar: null as string | null,
            role: {
                name: '',
                color_hex: ''
            },
            enterprise: {
                name: ''
            }
        });
        
        onMounted(() => {
            const userString = localStorage.getItem('user');
            if (userString) {
                try {
                    const parsedUser = JSON.parse(userString);
                    userData.value = {
                        ...userData.value,
                        ...parsedUser
                    };
                } catch (error) {
                    console.error('Failed to parse user data from localStorage:', error);
                }
            }
        });
        
        const userInitials = computed(() => {
            const firstName = userData.value.firstname || '';
            const lastName = userData.value.lastname || '';
            
            return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
        });
        
        const userAvatar = computed(() => {
            return userData.value.avatar;
        });
        
        const navigateToUpdateProfile = () => {
            router.push('/profile/update');
        };
        
        return {
            userData,
            userInitials,
            userAvatar,
            navigateToUpdateProfile
        };
    }
});
</script>