<template>
    <div class="min-h-screen flex items-center justify-center overflow-hidden relative">
        <div class="fixed inset-0" style="background: radial-gradient(circle at 50% 50%, #0f172a 0%, #000 100%)">
            <div 
                class="absolute inset-0"
                style="
                    background-image: radial-gradient(circle at center, rgba(139, 92, 246, 0.03) 0%, transparent 8%);
                    background-size: 60px 60px;
                    background-position: center;
                "
            />
        </div>
    
        <div 
            class="absolute w-96 h-96 rounded-full opacity-20"
            style="
                background: conic-gradient(from 0deg, #7c3aed, #9333ea, #6d28d9);
                filter: blur(60px);
                animation: pulse 8s ease-in-out infinite, rotate 20s linear infinite;
            "
        />
    
        <div class="absolute inset-0">
            <div class="absolute inset-0 opacity-20" 
                style="
                    background-image: linear-gradient(to right, transparent, rgba(139, 92, 246, 0.1) 15%, rgba(139, 92, 246, 0.1) 85%, transparent),
                                    linear-gradient(to bottom, transparent, rgba(139, 92, 246, 0.1) 15%, rgba(139, 92, 246, 0.1) 85%, transparent);
                    background-size: 100% 1px, 1px 100%;
                    background-position: center;
                    background-repeat: no-repeat;
                "
            />
        </div>
    
        <div class="relative z-10 flex flex-col items-center justify-center w-full max-w-2xl mx-auto p-8 text-center">
            <div class="text-8xl mb-4 animate-float-slow">
                👨‍🚀
            </div>
            
            <h1 class="text-8xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-violet-400 via-purple-500 to-indigo-500 leading-none">
                404
            </h1>
            
            <h2 class="text-3xl font-bold tracking-tight mt-6 mb-2 text-white">
                Houston, nous avons un problème
            </h2>
            
            <p class="mt-2 text-xl text-gray-300 max-w-lg">
                Il semble que vous vous soyez aventuré dans un secteur inexploré de notre univers.
            </p>
            <p class="mt-2 mb-8 text-gray-400 max-w-lg">
                La page que vous recherchez a peut-être été aspirée par un trou noir, ou elle n'a tout simplement jamais existé.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 mt-2">
                <router-link 
                    to="/" 
                    class="group relative inline-flex justify-center items-center px-6 py-3 text-sm font-medium rounded-lg overflow-hidden transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]"
                >
                    <span class="absolute inset-0 bg-gradient-to-r from-violet-500 via-purple-500 to-indigo-500 rounded-lg" />
                    <span class="absolute inset-0 bg-gradient-to-r from-violet-400 via-purple-500 to-indigo-500 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
                    <span class="relative text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 group-hover:-translate-x-1 transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Retour à la base
                    </span>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
    name: 'NotFound'
});
</script>

<style scoped>
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); opacity: 0; }
        50% { transform: translateY(-30px); opacity: 1; }
    }
    
    .animate-float-slow {
        animation: floatSlow 6s ease-in-out infinite;
    }
    
    @keyframes floatSlow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }
</style>