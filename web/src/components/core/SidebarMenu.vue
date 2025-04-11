<template>
    <div>
        <ul class="space-y-1 px-2">
            <li v-if="!hasSearch || matchesDashboard">
                <router-link 
                    to="/dashboard" 
                    class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-800"
                    :class="{ 'active-link': isActiveRoute('/dashboard') }"
                >
                    <div class="text-gray-400" :class="{ 'text-violet-400': isActiveRoute('/dashboard') }">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                    </div>
                    <span class="ml-3 whitespace-nowrap overflow-hidden">
                        Tableau de bord
                    </span>
                </router-link>
            </li>
            
            <li v-for="(module, moduleKey) in filteredModules" :key="moduleKey">
                <div 
                    @click="toggleModuleMenu(moduleKey)"
                    class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-800 cursor-pointer"
                    :class="{ 'bg-gray-800/50': isModuleMenuOpen(moduleKey) }"
                >
                    <div class="text-gray-400" :class="{ 'text-violet-400': isModuleActive(moduleKey) }">
                        <component :is="getModuleIcon(moduleKey)" />
                    </div>
                    <span class="ml-3 whitespace-nowrap overflow-hidden flex-1">
                        {{ module.name }}
                    </span>
                    <div 
                        class="flex-shrink-0 transition-transform duration-200 text-gray-400"
                        :class="{ 'transform rotate-180': isModuleMenuOpen(moduleKey) }"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                
                <!-- Items de menu filtrés -->
                <div 
                    v-if="isModuleMenuOpen(moduleKey) || hasSearch"
                    class="mt-1 ml-5 space-y-1 transition-all duration-200"
                >
                    <router-link 
                        v-for="subItem in getFilteredMenuItems(moduleKey)" 
                        :key="subItem.path"
                        :to="subItem.path" 
                        class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 hover:bg-gray-800 text-sm"
                        :class="{ 'active-link': isActiveRoute(subItem.path) }"
                    >
                        <div class="w-1.5 h-1.5 rounded-full bg-gray-500 mr-2" :class="{ 'bg-violet-400': isActiveRoute(subItem.path) }"/>
                        <span class="whitespace-nowrap overflow-hidden">
                            {{ subItem.title }}
                        </span>
                    </router-link>
                </div>
            </li>
            
            <li v-if="(hasSettingsAccess && !hasSearch) || (hasSettingsAccess && matchesSettings)">
                <router-link 
                    to="/parametres" 
                    class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-800"
                    :class="{ 'active-link': isActiveRoute('/parametres') }"
                >
                    <div class="text-gray-400" :class="{ 'text-violet-400': isActiveRoute('/parametres') }">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span class="ml-3 whitespace-nowrap overflow-hidden">
                        Paramètres
                    </span>
                </router-link>
            </li>
        </ul>
    </div>
</template>

<script lang="ts">
    import { defineComponent, ref, computed, onMounted, PropType } from 'vue';
    import { useRoute } from 'vue-router';
    import { moduleDefinitions, moduleIcons } from '../../definitions/MenuDefinition';
    import { SearchResults } from './SearchBar.vue';

    export default defineComponent({
        name: 'SidebarMenu',
        props: {
            searchResults: {
                type: Object as PropType<SearchResults>,
                default: undefined
            }
        },
        
        setup(props) {
            const route = useRoute();
            const openModuleMenus = ref<Set<string>>(new Set());
            const userPermissions = ref<any>({});
            
            onMounted(() => {
                loadUserPermissions();
                
                const currentModule = findCurrentModuleFromRoute();
                if (currentModule) {
                    openModuleMenus.value.add(currentModule);
                }
            });
            
            const loadUserPermissions = () => {
                const userString = localStorage.getItem('user');
                if (userString) {
                    try {
                        const userData = JSON.parse(userString);
                        if (userData.role && userData.role.authority) {
                            userPermissions.value = userData.role.authority;
                            console.log('Permissions chargées:', userPermissions.value);
                        }
                    } catch (error) {
                        console.error('Erreur lors du chargement des permissions:', error);
                    }
                }
            };
            
            const findCurrentModuleFromRoute = () => {
                const path = route.path;
                
                for (const moduleKey in moduleDefinitions) {
                    const items = moduleDefinitions[moduleKey].menuItems;
                    for (const item of items) {
                        if (path === item.path || path.startsWith(`${item.path}/`)) {
                            return moduleKey;
                        }
                    }
                }
                
                return null;
            };
            
            const hasModuleAccess = (moduleKey: string) => {
                const module = moduleDefinitions[moduleKey];
                if (!module) return false;
                
                const modulePermissions = userPermissions.value[module.permissionKey];
                return modulePermissions && modulePermissions.read === true;
            };
            
            const hasSettingsAccess = computed(() => {
                return userPermissions.value.enterprise?.read === true;
            });
            
            const hasSearch = computed(() => {
                return props.searchResults !== undefined;
            });
            
            const matchesDashboard = computed(() => {
                if (!props.searchResults) return false;
                
                return "Tableau de bord".toLowerCase().includes(props.searchResults?.modules.join('').toLowerCase()) || 
                    props.searchResults?.items.includes('/dashboard');
            });
            
            const matchesSettings = computed(() => {
                if (!props.searchResults) return false;
                
                return "Paramètres".toLowerCase().includes(props.searchResults?.modules.join('').toLowerCase()) || 
                    props.searchResults?.items.includes('/parametres');
            });
            
            const filteredModules = computed(() => {
                const modules: Record<string, any> = {};
                
                for (const [key, module] of Object.entries(moduleDefinitions)) {
                    if (!hasModuleAccess(key)) continue;
                    
                    if (hasSearch.value && props.searchResults) {
                        if (props.searchResults.modules.includes(key)) {
                            modules[key] = module;
                        }
                    } else {
                        modules[key] = module;
                    }
                }
                
                return modules;
            });
            
            const toggleModuleMenu = (moduleKey: string) => {
                if (openModuleMenus.value.has(moduleKey)) {
                    openModuleMenus.value.delete(moduleKey);
                } else {
                    openModuleMenus.value.add(moduleKey);
                }
            };
            
            const isModuleMenuOpen = (moduleKey: string) => {
                return openModuleMenus.value.has(moduleKey);
            };
            
            const isModuleActive = (moduleKey: string) => {
                const items = moduleDefinitions[moduleKey]?.menuItems || [];
                return items.some(item => isActiveRoute(item.path));
            };
            
            const getModuleIcon = (moduleKey: string) => {
                return moduleIcons[moduleKey as keyof typeof moduleIcons] || moduleIcons.default;
            };
            
            const getFilteredMenuItems = (moduleKey: string) => {
                const module = moduleDefinitions[moduleKey];
                if (!module) return [];
                
                let items = module.menuItems.filter(item => {
                    if (item.requiredPermission) {
                        const modulePermissions = userPermissions.value[module.permissionKey];
                        if (!modulePermissions || !modulePermissions[item.requiredPermission]) {
                            return false;
                        }
                    }
                    
                    if (hasSearch.value && props.searchResults) {
                        return props.searchResults.items.includes(item.path);
                    }
                    
                    return true;
                });
                
                return items;
            };
            
            const isActiveRoute = (path: string) => {
                if (route.path === path) {
                    return true;
                }

                if (path.endsWith('/') && route.path.startsWith(path)) {
                    return true;
                }
                
                const pathSegments = path.split('/');
                const routeSegments = route.path.split('/');
                
                if (routeSegments.length < pathSegments.length) {
                    return false;
                }
                
                for (let i = 0; i < pathSegments.length; i++) {
                    if (pathSegments[i] !== routeSegments[i]) {
                        return false;
                    }
                }

                if (routeSegments.length > pathSegments.length) {
                    const nextSegment = routeSegments[pathSegments.length];
                    if (/^[0-9]+$/.test(nextSegment) || /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(nextSegment)) {
                        return true;
                    }
                    return false;
                }
                
                return true;
            };
            
            return {
                filteredModules,
                hasSettingsAccess,
                hasSearch,
                matchesDashboard,
                matchesSettings,
                isActiveRoute,
                toggleModuleMenu,
                isModuleMenuOpen,
                isModuleActive,
                getModuleIcon,
                getFilteredMenuItems
            };
        }
    });
</script>

<style scoped>
    .active-link {
        background: linear-gradient(90deg, rgba(124, 58, 237, 0.2) 0%, rgba(139, 92, 246, 0.1) 100%);
        border-left: 3px solid #8b5cf6;
        padding-left: calc(1rem - 3px);
    }
</style>