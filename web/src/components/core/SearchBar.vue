<template>
    <div class="px-3 py-3 border-b border-gray-800">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input 
                type="text" 
                v-model="searchQuery" 
                placeholder="Rechercher dans les menus..." 
                class="block w-full bg-gray-800 border border-gray-700 rounded-lg py-2 pl-10 pr-3 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
            />
            <button 
                v-if="searchQuery" 
                @click="clearSearch" 
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-white transition-colors duration-200"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch } from 'vue';
import { moduleDefinitions } from '../../definitions/MenuDefinition';

export interface SearchResults {
    modules: string[];
    items: string[];
}

export default defineComponent({
    name: 'SearchBar',
    emits: ['search-update'],
    
    setup(_, { emit }) {
        const searchQuery = ref('');
        
        // Observer les changements dans la recherche
        watch(searchQuery, (newValue) => {
            if (newValue.trim() === '') {
                // Réinitialiser la recherche
                emit('search-update', undefined);
                return;
            }
            
            const searchResults = performSearch(newValue);
            emit('search-update', searchResults);
        });
        
        const performSearch = (query: string): SearchResults => {
            const lowerQuery = query.toLowerCase().trim();
            const results: SearchResults = {
                modules: [],
                items: []
            };
            
            for (const [moduleKey, module] of Object.entries(moduleDefinitions)) {
                // Vérifier si le nom du module correspond
                const moduleMatches = module.name.toLowerCase().includes(lowerQuery);
                
                // Vérifier les éléments de menu correspondants
                const matchingItems = module.menuItems.filter(item => 
                    item.title.toLowerCase().includes(lowerQuery)
                );
                
                if (moduleMatches || matchingItems.length > 0) {
                    // Ajouter le module à la liste des résultats
                    results.modules.push(moduleKey);
                    
                    // Ajouter les chemins des éléments correspondants
                    matchingItems.forEach(item => {
                        results.items.push(item.path);
                    });
                }
            }
            
            return results;
        };
        
        const clearSearch = () => {
            searchQuery.value = '';
            emit('search-update', undefined);
        };
        
        return {
            searchQuery,
            clearSearch
        };
    }
});
</script>