<template>
	<div class="min-h-screen flex bg-gray-950 text-white">
		<Sidebar @sidebar-toggle="handleSidebarToggle" />
		
		<div 
			class="transition-all duration-300 fixed top-0 right-0 bottom-0 overflow-hidden flex flex-col"
			:style="{ left: sidebarCollapsed ? '0' : '16rem' }"
		>
			<header class="bg-gray-900/60 backdrop-blur-sm border-b border-gray-800 h-16 flex items-center px-6">
				<div class="flex-1 flex">
					<h2 class="text-xl font-semibold">{{ pageTitle }}</h2>
				</div>
				
				<div class="flex items-center space-x-4">
					<button class="text-gray-400 hover:text-white transition-colors duration-200 relative">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
						</svg>
						<span class="absolute -top-1 -right-1 bg-red-500 rounded-full w-4 h-4 flex items-center justify-center text-xs">2</span>
					</button>
				</div>
			</header>
			
			<main class="flex-1 overflow-auto p-6 relative">
				<div 
					class="fixed inset-0 pointer-events-none" 
					style="
						background: radial-gradient(circle at 50% 50%, #0f172a 0%, #000 100%);
						z-index: -10;
					"
				>
					<div 
						class="absolute inset-0"
						style="
							background-image: radial-gradient(circle at center, rgba(139, 92, 246, 0.03) 0%, transparent 8%);
							background-size: 60px 60px;
							background-position: center;
						"
					/>
				</div>
				
				<div class="min-h-full">
					<router-view></router-view>
				</div>
			</main>
		</div>
	</div>
</template>

<script lang="ts">
import { defineComponent, ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import Sidebar from './sidebar.vue';

export default defineComponent({
	name: 'DashboardLayout',
	components: {
		Sidebar
	},
	
	setup() {
		const route = useRoute();
		const userMenuOpen = ref(false);
		const sidebarCollapsed = ref(false);
		
		const handleSidebarToggle = (collapsed: boolean) => {
			sidebarCollapsed.value = collapsed;
		};
		
		const pageTitle = computed(() => {
			const routeName = route.name as string;
			
			switch (routeName) {
				case 'dashboard':
					return 'Tableau de bord';
				case 'collaborateurs':
					return 'Gestion des collaborateurs';
				case 'parametres':
					return 'Paramètres';
				default:
					return 'Dashboard';
			}
		});
		
		return {
			userMenuOpen,
			pageTitle,
			sidebarCollapsed,
			handleSidebarToggle
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