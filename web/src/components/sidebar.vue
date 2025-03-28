<template>
	<div class="h-screen flex flex-col overflow-hidden">
		<!-- Sidebar principale -->
		<div 
			class="fixed top-0 left-0 h-screen border-r border-gray-800 bg-gray-900/80 backdrop-blur-sm transition-all duration-300 ease-in-out z-20"
			:class="{ 'w-64': !collapsed, 'w-0': collapsed }"
		>
			<div class="h-full flex flex-col" :class="{ 'opacity-0': collapsed }">
				<div class="p-4 flex items-center justify-between border-b border-gray-800">
					<div class="flex items-center overflow-hidden">
						<h1 class="font-bold text-xl text-white overflow-hidden whitespace-nowrap">
							{{ enterpriseName }}
						</h1>
					</div>
					<button 
						@click="toggleSidebar" 
						class="text-gray-400 hover:text-white transition-colors duration-200 flex-shrink-0"
					>
						<svg 
							xmlns="http://www.w3.org/2000/svg" 
							class="h-6 w-6"
							fill="none" 
							viewBox="0 0 24 24" 
							stroke="currentColor"
						>
							<path 
								stroke-linecap="round" 
								stroke-linejoin="round" 
								stroke-width="2" 
								d="M11 19l-7-7 7-7m8 14l-7-7 7-7" 
							/>
						</svg>
					</button>
				</div>
				
				<nav class="flex-1 py-4 overflow-y-auto">
					<ul class="space-y-1 px-2">
						<li v-for="(item, index) in menuItems" :key="index">
							<router-link 
								:to="item.path" 
								class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-800"
								:class="{ 'active-link': isActiveRoute(item.path) }"
							>
								<div class="text-gray-400" :class="{ 'text-violet-400': isActiveRoute(item.path) }" v-html="item.icon"></div>
								<span class="ml-3 whitespace-nowrap overflow-hidden">
									{{ item.title }}
								</span>
							</router-link>
						</li>
					</ul>
				</nav>
				
				<div class="border-t border-gray-800 p-4">
					<div class="flex items-center">
						<div class="relative flex-shrink-0">
							<div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center">
								<span class="text-white font-medium">{{ userInitials }}</span>
							</div>
							<div class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-green-500 border-2 border-gray-900"></div>
						</div>
						<div class="ml-3 overflow-hidden">
							<p class="font-medium text-white line-clamp-1">{{ userFullName }}</p>
							<p class="text-xs text-gray-400 line-clamp-1">{{ userRole }}</p>
						</div>
					</div>
					<div class="mt-4 space-y-2">
						<button 
							@click="logout" 
							class="w-full flex items-center px-3 py-2 text-sm rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition-colors duration-200"
						>
							<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
							</svg>
							Déconnexion
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Bouton élégant pour afficher la sidebar quand elle est cachée -->
		<button 
			v-if="collapsed"
			@click="toggleSidebar"
			class="fixed left-0 top-1/2 transform -translate-y-1/2 transition-all duration-300 z-30 group"
			aria-label="Ouvrir la barre latérale"
		>
			<div class="flex items-center">
				<div class="h-32 w-2 bg-gradient-to-b from-violet-500 to-indigo-600 rounded-r-md opacity-70 group-hover:opacity-100 transition-opacity duration-200"></div>
				<div class="px-2 py-4 bg-gray-900/80 backdrop-blur-sm border border-gray-800 -ml-0.5 rounded-r-lg flex items-center shadow-lg">
					<div class="flex flex-col items-center space-y-3">
						<svg 
							xmlns="http://www.w3.org/2000/svg" 
							class="h-5 w-5 text-white group-hover:text-violet-400 transition-colors duration-200"
							fill="none" 
							viewBox="0 0 24 24" 
							stroke="currentColor"
						>
							<path 
								stroke-linecap="round" 
								stroke-linejoin="round" 
								stroke-width="2" 
								d="M13 5l7 7-7 7" 
							/>
						</svg>
					</div>
				</div>
			</div>
		</button>
	</div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import apiFetch from '../utils/apiFetch';

export default defineComponent({
	name: 'Sidebar',
	emits: ['sidebar-toggle'],
	
	setup(_, { emit }) {
		const router = useRouter();
		const route = useRoute();
		
		const collapsed = ref(false);
		const userData = ref<any>(null);
		
		onMounted(() => {
			const userString = localStorage.getItem('user');
			if (userString) {
				try {
					userData.value = JSON.parse(userString);
				} catch (error) {
					console.error('Failed to parse user data', error);
				}
			}
		});
		
		const enterpriseName = computed(() => {
			return userData.value?.enterprise?.name || 'Enterprise';
		});
		
		const userInitials = computed(() => {
			if (!userData.value) return 'U';
			
			const firstName = userData.value.firstname || '';
			const lastName = userData.value.lastname || '';
			
			return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
		});
		
		const userFullName = computed(() => {
			if (!userData.value) return 'Utilisateur';
			return `${userData.value.firstname} ${userData.value.lastname}`;
		});
		
		const userRole = computed(() => {
			if (!userData.value || !userData.value.role) return 'Utilisateur';
			return userData.value.role.name;
		});
		
		const menuItems = computed(() => [
			{
				title: 'Tableau de bord',
				path: '/dashboard',
				routeName: 'dashboard',
				icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
					</svg>`
			},
			{
				title: 'Collaborateurs',
				path: '/collaborateurs',
				routeName: 'collaborateurs',
				icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
					</svg>`
			},
			{
				title: 'Paramètres',
				path: '/parametres',
				routeName: 'parametres',
				icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
					</svg>`
			}
		]);
		
		const isActiveRoute = (path: string) => {
			return route.path === path || route.path.startsWith(`${path}/`);
		};
		
		const toggleSidebar = () => {
			collapsed.value = !collapsed.value;
			emit('sidebar-toggle', collapsed.value);
		};
		
		const logout = async () => {
			try {
				await apiFetch.post('/auth/logout');
			} catch (error) {
				console.error('Logout failed', error);
			} finally {
				localStorage.removeItem('token');
				localStorage.removeItem('user');
				router.push('/login');
			}
		};
		
		return {
			collapsed,
			userInitials,
			userFullName,
			userRole,
			menuItems,
			isActiveRoute,
			toggleSidebar,
			logout,
			enterpriseName
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

:deep(.router-link-active) .active-link-icon {
	color: #8b5cf6;
}

::-webkit-scrollbar {
	width: 6px;
}

::-webkit-scrollbar-track {
	background: transparent;
}

::-webkit-scrollbar-thumb {
	background-color: rgba(139, 92, 246, 0.5);
	border-radius: 20px;
}

::-webkit-scrollbar-thumb:hover {
	background-color: rgba(139, 92, 246, 0.7);
}
</style>