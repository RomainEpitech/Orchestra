<template>
	<div class="h-screen flex flex-col overflow-hidden">
		<div 
			class="fixed top-0 left-0 h-screen border-r border-gray-800 bg-gray-900/80 backdrop-blur-sm transition-all duration-300 ease-in-out z-20"
			:class="{ 'w-64': !collapsed, 'w-0': collapsed }"
		>
			<div class="h-full flex flex-col" :class="{ 'opacity-0': collapsed }">
				<div class="p-4 flex items-center justify-between border-b border-gray-800">
					<div class="flex items-center overflow-hidden">
						<div class="mr-2 w-8 h-8 flex-shrink-0 bg-gradient-to-br from-violet-500 to-indigo-600 rounded-md flex items-center justify-center">
							<span class="text-white font-bold">{{ enterpriseName.charAt(0) }}</span>
						</div>
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
					<SidebarMenu />
				</nav>
				
				<userProfileComponent @logout="handleLogout" />
			</div>
		</div>

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
import userProfileComponent from './userProfileComponent.vue';
import SidebarMenu from './SidebarMenu.vue';

export default defineComponent({
	name: 'Sidebar',
	components: {
		userProfileComponent,
		SidebarMenu
	},
	emits: ['sidebar-toggle', 'logout'],
	
	setup(_, { emit }) {
		const collapsed = ref(false);
		const enterpriseData = ref({ name: 'Enterprise' });
		
		onMounted(() => {
			const userString = localStorage.getItem('user');
			if (userString) {
				try {
					const parsedUser = JSON.parse(userString);
					if (parsedUser.enterprise) {
						enterpriseData.value = parsedUser.enterprise;
					}
				} catch (error) {
					console.error('Failed to parse user data from localStorage:', error);
				}
			}
		});
		
		const enterpriseName = computed(() => {
			return enterpriseData.value?.name || 'Enterprise';
		});
		
		const toggleSidebar = () => {
			collapsed.value = !collapsed.value;
			emit('sidebar-toggle', collapsed.value);
		};
		
		const handleLogout = () => {
			emit('logout');
		};
		
		return {
			collapsed,
			enterpriseName,
			toggleSidebar,
			handleLogout
		};
	}
});
</script>

<style scoped>
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