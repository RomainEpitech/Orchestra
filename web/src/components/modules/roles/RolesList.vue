<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Gestion des rôles</h1>
                <p class="text-gray-400 mt-1">Gérez les rôles et les permissions de votre entreprise</p>
            </div>
            <router-link
                v-if="hasRoleAuthority('roles', 'create')"
                to="/roles/ajouter"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-violet-500 transition-colors"
            >
                <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Ajouter un rôle
            </router-link>
        </div>

        <div v-if="notification" 
            :class="[
                'p-4 rounded-md border',
                notification.type === 'success' ? 'bg-green-900/30 border-green-800 text-green-300' : 'bg-red-900/30 border-red-800 text-red-300'
            ]"
        >
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg v-if="notification.type === 'success'" class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <svg v-else class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ notification.message }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button @click="notification = null" class="inline-flex rounded-md p-1.5" :class="notification.type === 'success' ? 'text-green-300 hover:text-green-200' : 'text-red-300 hover:text-red-200'">
                            <span class="sr-only">Fermer</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isLoading" class="flex justify-center py-12">
            <svg class="animate-spin h-8 w-8 text-violet-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div v-else-if="roles.length === 0" class="bg-gray-800/40 backdrop-blur-sm border border-gray-700 rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-300">Aucun rôle trouvé</h3>
            <p class="mt-1 text-sm text-gray-400">Commencez par créer un nouveau rôle pour votre entreprise.</p>
            <div class="mt-6">
                <router-link
                    v-if="hasRoleAuthority('roles', 'create')"
                    to="/roles/ajouter"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-violet-500 transition-colors"
                >
                    <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Ajouter un rôle
                </router-link>
            </div>
        </div>

        <div v-else class="bg-gray-800/40 backdrop-blur-sm border border-gray-700 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-800/80">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Nom du rôle
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Niveau hiérarchique
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Utilisateurs
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800/40 divide-y divide-gray-700">
                        <tr v-for="role in roles" :key="role.uuid" class="hover:bg-gray-700/40 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center" :style="{ backgroundColor: role.color_hex + '33' }">
                                        <span class="font-bold text-lg" :style="{ color: role.color_hex }">{{ role.name.charAt(0) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-white flex items-center">
                                            {{ role.name }}
                                            <svg v-if="!canEditRole(role)" class="ml-2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-300">
                                        {{ formatHierarchyLevel(role.hierarchy_level) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="role.is_shared" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-900/40 text-blue-300">
                                    Partagé
                                </span>
                                <span v-else class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-violet-900/40 text-violet-300">
                                    Personnalisé
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ role.users_count || 0 }} utilisateurs
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <router-link
                                        v-if="hasRoleAuthority('roles', 'read')"
                                        :to="`/roles/${role.uuid}`"
                                        class="text-indigo-400 hover:text-indigo-300 transition-colors"
                                    >
                                        Voir
                                    </router-link>
                                    <div v-else-if="!canEditRole(role)" class="text-gray-500 ml-3 text-xs cursor-not-allowed flex items-center">
                                        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                        Niveau supérieur
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import apiFetch from '@/utils/apiFetch';

interface Role {
    uuid: string;
    name: string;
    color_hex: string;
    hierarchy_level: number;
    is_shared: boolean;
    users_count?: number;
    authority?: Record<string, any>;
}

interface UserData {
    uuid: string;
    firstname: string;
    lastname: string;
    email: string;
    role: {
        uuid: string;
        name: string;
        authority: Record<string, any>;
        color_hex: string;
        hierarchy: number;
    };
    enterprise: {
        uuid: string;
        name: string;
    };
}

export default defineComponent({
    name: 'RolesList',
    
    setup() {
        const router = useRouter();
        const route = useRoute();
        
        const roles = ref<Role[]>([]);
        const isLoading = ref(true);
        const selectedRole = ref<Role | null>(null);
        const isDeleting = ref(false);
        const notification = ref<{ type: 'success' | 'error', message: string } | null>(null);
        const userData = ref<UserData | null>(null);
        
        const loadUserData = () => {
            const userDataString = localStorage.getItem('user');
            if (userDataString) {
                userData.value = JSON.parse(userDataString);
            }
        };

        const hasRoleAuthority = (module: string, permission: string): boolean => {
            if (!userData.value || !userData.value.role || !userData.value.role.authority) {
                return false;
            }

            if (userData.value.role.authority['*']) {
                return true;
            }

            return !!(userData.value.role.authority[module] && 
                userData.value.role.authority[module][permission]);
        };

        const formatHierarchyLevel = (level: number): string => {
            const levels: Record<number, string> = {
                1: 'Direction (1)',
                2: 'Administration (2)',
                3: 'Management (3)',
                4: 'Supervision (4)',
                5: 'Opération (5)',
                6: 'Base (6)'
            };
            return levels[level] || `Niveau ${level}`;
        };
        
        const canEditRole = (role: Role): boolean => {
            if (!userData.value || !userData.value.role) return false;
            
            if (userData.value.role.hierarchy === 1) {
                return true;
            }
            
            if (userData.value.role.authority && userData.value.role.authority['*']) return true;
            
            const userHierarchyLevel = userData.value.role.hierarchy;
            
            if (userHierarchyLevel === undefined) {
                console.warn('Niveau hiérarchique utilisateur non défini');
                return false;
            }
            
            return userHierarchyLevel < role.hierarchy_level;
        };
        
        const loadRoles = async () => {
            isLoading.value = true;
            
            try {
                const response = await apiFetch.get('/role/getAll');
                
                if (response && response.data) {
                    roles.value = response.data;
                    console.log('Rôles chargés:', roles.value);
                } else {
                    throw new Error("Format de réponse inattendu");
                }
            } catch (error: any) {
                console.error('Erreur lors du chargement des rôles:', error);
                notification.value = {
                    type: 'error',
                    message: "Impossible de charger les rôles. Veuillez réessayer."
                };
            } finally {
                isLoading.value = false;
            }
        };
        
        const checkQueryNotification = () => {
            const notificationType = route.query.notification as string;
            const notificationMessage = route.query.message as string;
            
            if (notificationType && notificationMessage) {
                notification.value = {
                    type: notificationType === 'success' ? 'success' : 'error',
                    message: notificationMessage
                };
                
                router.replace({ query: {} });
            }
        };
        
        onMounted(() => {
            loadUserData();
            loadRoles();
            checkQueryNotification();
        });
        
        return {
            roles,
            isLoading,
            notification,
            selectedRole,
            isDeleting,
            userData,
            hasRoleAuthority,
            formatHierarchyLevel,
            canEditRole,
        };
    }
});
</script>