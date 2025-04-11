<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Liste des collaborateurs</h1>
                <p class="text-gray-400 mt-1">Gérez les accès et les rôles de vos collaborateurs</p>
            </div>
        </div>
        
        <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700 rounded-lg p-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        v-model="searchQuery" 
                        placeholder="Rechercher un collaborateur..." 
                        class="w-full pl-10 pr-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                    />
                </div>
                
                <div class="flex items-center gap-2">
                    <label for="role-filter" class="text-sm text-gray-300 whitespace-nowrap">Filtrer par rôle:</label>
                    <select 
                        id="role-filter" 
                        v-model="roleFilter"
                        class="bg-gray-700 border border-gray-600 text-white rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                    >
                        <option value="">Tous les rôles</option>
                        <option v-for="role in uniqueRoles" :key="role.uuid" :value="role.uuid">
                            {{ role.name }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700 rounded-lg overflow-hidden">
            <div v-if="loading" class="flex justify-center items-center p-12">
                <div class="flex flex-col items-center">
                    <div class="spinner">
                        <div class="w-16 h-16 border-4 border-gray-600 border-t-violet-500 rounded-full animate-spin"></div>
                    </div>
                    <p class="mt-4 text-gray-400">Chargement des collaborateurs...</p>
                </div>
            </div>
            
            <div v-else-if="error" class="p-6 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-900/30 text-red-500 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-white mb-2">Erreur lors du chargement</h3>
                <p class="text-gray-400 max-w-md mx-auto">{{ error }}</p>
                <button 
                    @click="loadCollaborators" 
                    class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-violet-500 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Réessayer
                </button>
            </div>
            
            <div v-else-if="filteredCollaborators.length === 0" class="p-6 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-800 text-gray-400 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-white mb-2">Aucun collaborateur trouvé</h3>
                <p class="text-gray-400 max-w-md mx-auto">
                    {{ searchQuery ? "Aucun résultat ne correspond à votre recherche." : "Il n'y a pas encore de collaborateurs dans votre entreprise." }}
                </p>
                <div v-if="searchQuery" class="mt-4">
                    <button 
                        @click="searchQuery = ''" 
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-gray-500 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Effacer la recherche
                    </button>
                </div>
            </div>
            
            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Collaborateur
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Rôle
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Date d'ajout
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800/20 divide-y divide-gray-700">
                        <tr v-for="collaborator in filteredCollaborators" :key="collaborator.uuid" class="hover:bg-gray-700/30 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center">
                                        <span class="text-white font-medium">{{ getInitials(collaborator.firstname, collaborator.lastname) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-white">
                                            {{ collaborator.lastname.toUpperCase() }} {{ collaborator.firstname }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">{{ collaborator.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :style="{ 
                                        backgroundColor: hexToRgba(collaborator.role.color_hex, 0.2), 
                                        color: collaborator.role.color_hex 
                                    }"
                                >
                                    {{ collaborator.role.name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ formatDate(collaborator.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <button 
                                    @click="editCollaborator(collaborator)" 
                                    class="text-indigo-400 hover:text-indigo-300 transition-colors duration-150 p-1.5 rounded-full hover:bg-indigo-900/30"
                                    title="Modifier"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import apiFetch from '@/utils/apiFetch';

interface Role {
    uuid: string;
    name: string;
    color_hex: string;
}

interface Collaborator {
    uuid: string;
    firstname: string;
    lastname: string;
    email: string;
    role: Role;
    created_at: string;
}

export default defineComponent({
    name: 'CollaboratorsList',
    
    setup() {
        const router = useRouter();
        const collaborators = ref<Collaborator[]>([]);
        const loading = ref(true);
        const error = ref<string | null>(null);
        const searchQuery = ref('');
        const roleFilter = ref('');
        
        const loadCollaborators = async () => {
            loading.value = true;
            error.value = null;
            
            try {
                const response = await apiFetch.get('/personnel/licence');
                
                if (response && response.success && response.licenses) {
                    collaborators.value = response.licenses;
                } else {
                    throw new Error("Format de réponse inattendu");
                }
            } catch (err: any) {
                console.error('Erreur lors du chargement des collaborateurs:', err);
                error.value = err.message || "Une erreur est survenue lors du chargement des collaborateurs";
            } finally {
                loading.value = false;
            }
        };
        
        onMounted(() => {
            loadCollaborators();
        });
        
        const uniqueRoles = computed(() => {
            const roles: Role[] = [];
            const roleMap = new Map<string, Role>();
            
            collaborators.value.forEach(collab => {
                if (!roleMap.has(collab.role.uuid)) {
                    roleMap.set(collab.role.uuid, collab.role);
                    roles.push(collab.role);
                }
            });
            
            return roles.sort((a, b) => a.name.localeCompare(b.name));
        });
        
        const filteredCollaborators = computed(() => {
            return collaborators.value.filter(collab => {
                // Filtre par recherche
                const searchLower = searchQuery.value.toLowerCase();
                const matchesSearch = 
                    collab.firstname.toLowerCase().includes(searchLower) ||
                    collab.lastname.toLowerCase().includes(searchLower) ||
                    collab.email.toLowerCase().includes(searchLower) ||
                    collab.role.name.toLowerCase().includes(searchLower);
                
                const matchesRole = roleFilter.value ? collab.role.uuid === roleFilter.value : true;
                
                return matchesSearch && matchesRole;
            });
        });
        
        const getInitials = (firstname: string, lastname: string) => {
            return (firstname.charAt(0) + lastname.charAt(0)).toUpperCase();
        };
        
        const formatDate = (dateString: string) => {
            try {
                const date = new Date(dateString);
                return new Intl.DateTimeFormat('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                }).format(date);
            } catch {
                return dateString;
            }
        };
        
        const hexToRgba = (hex: string, alpha = 1) => {
            try {
                const r = parseInt(hex.slice(1, 3), 16);
                const g = parseInt(hex.slice(3, 5), 16);
                const b = parseInt(hex.slice(5, 7), 16);
                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            } catch {
                return `rgba(0, 0, 0, ${alpha})`;
            }
        };
        
        const editCollaborator = (collaborator: Collaborator) => {
            router.push(`/collaborateurs/modifier/${collaborator.uuid}`);
        };
        
        return {
            collaborators,
            loading,
            error,
            searchQuery,
            roleFilter,
            filteredCollaborators,
            uniqueRoles,
            loadCollaborators,
            getInitials,
            formatDate,
            hexToRgba,
            editCollaborator,
        };
    }
});
</script>

<style scoped>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>