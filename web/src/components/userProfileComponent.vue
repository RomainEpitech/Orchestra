<template>
    <div class="border-t border-gray-800 p-4">
        <div class="flex items-center">
            <div class="relative flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center">
                    <span class="text-white font-medium">{{ userInitials }}</span>
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-green-500 border-2 border-gray-900"/>
            </div>
            <div class="ml-3 overflow-hidden">
                <p class="font-medium text-white line-clamp-1">{{ userFullName }}</p>
                <p class="text-xs text-gray-400 line-clamp-1">{{ userRole }}</p>
            </div>
        </div>
        <div class="mt-4 space-y-2">
            <button 
                @click="navigateToProfile" 
                class="w-full flex items-center px-3 py-2 text-sm rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition-colors duration-200"
                :disabled="isLoadingProfile"
            >
                <svg v-if="!isLoadingProfile" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <svg v-else class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isLoadingProfile ? 'Chargement...' : 'Mon profil' }}
            </button>
            
            <button 
                @click="handleLogout" 
                class="w-full flex items-center px-3 py-2 text-sm rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition-colors duration-200"
                :disabled="isLoggingOut"
            >
                <svg v-if="!isLoggingOut" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <svg v-else class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isLoggingOut ? 'Déconnexion...' : 'Déconnexion' }}
            </button>
        </div>
    </div>
</template>

<script lang="ts">
    import { defineComponent, ref, computed, onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import axios from 'axios';
    
    export default defineComponent({
        name: 'UserProfile',
        emits: ['logout'],
        
        setup(_, { emit }) {
            const router = useRouter();
            const userData = ref({
                firstname: 'User',
                lastname: '',
                role: { name: 'Utilisateur' }
            });
            const isLoggingOut = ref(false);
            const isLoadingProfile = ref(false);
            
            onMounted(() => {
                const userString = localStorage.getItem('user');
                if (userString) {
                    try {
                        const parsedUser = JSON.parse(userString);
                        userData.value = parsedUser;
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
            
            const userFullName = computed(() => {
                return `${userData.value.firstname} ${userData.value.lastname}`;
            });
            
            const userRole = computed(() => {
                return userData.value.role?.name || 'Utilisateur';
            });
            
            const navigateToProfile = () => {
                isLoadingProfile.value = true;
                setTimeout(() => {
                    router.push('/profile');
                    isLoadingProfile.value = false;
                }, 300);
            };
            
            const handleLogout = async () => {
                try {
                    isLoggingOut.value = true;
                    const token = localStorage.getItem('token');
                    await axios.post('/api/auth/logout', {}, {
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    });
                    
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    
                    emit('logout');
                    
                    if (router) {
                        router.push('/login');
                    }
                } catch (error) {
                    console.error('Erreur lors de la déconnexion:', error);
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    
                    if (router) {
                        router.push('/login');
                    }
                } finally {
                    isLoggingOut.value = false;
                }
            };
            
            return {
                userInitials,
                userFullName,
                userRole,
                isLoggingOut,
                isLoadingProfile,
                handleLogout,
                navigateToProfile
            };
        }
    });
</script>