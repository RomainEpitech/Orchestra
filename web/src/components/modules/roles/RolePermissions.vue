<template>
    <div class="space-y-4">
        <div class="flex items-center">
            <h3 class="text-md font-medium text-white flex items-center">
                Permissions
                <span v-if="isSharedRole" class="ml-2 text-xs text-yellow-400 inline-flex items-center">
                    <svg class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Non modifiable
                </span>
            </h3>
        </div>
        
        <ul class="space-y-4">
            <li v-for="(modulePermissions, moduleName) in modulesList" :key="moduleName" class="bg-gray-900/40 rounded-lg border border-gray-700 overflow-hidden">
                <div 
                    class="p-4 flex items-center justify-between cursor-pointer hover:bg-gray-800/40 transition-colors"
                    @click="toggleModule(moduleName)"
                >
                    <h4 class="font-medium text-violet-400">{{ getModuleDisplayName(moduleName) }}</h4>
                    <div class="flex items-center">
                        <span class="text-xs text-gray-400 mr-3">
                            {{ getActivePermissionsCount(moduleName) }} / {{ Object.keys(modulePermissions).length }} actives
                        </span>
                        <svg 
                            class="h-5 w-5 text-gray-400 transition-transform duration-200" 
                            :class="{ 'transform rotate-180': expandedModules[moduleName] }"
                            fill="none" 
                            viewBox="0 0 24 24" 
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                
                <div 
                    v-show="expandedModules[moduleName]"
                    class="border-t border-gray-700 bg-gray-800/20"
                >
                    <ul class="divide-y divide-gray-700">
                        <li 
                            v-for="(label, permission) in modulePermissions" 
                            :key="permission"
                            class="p-3 px-4 flex items-center justify-between hover:bg-gray-800/30"
                        >
                            <label :for="`${moduleName}-${permission}`" class="block text-sm text-gray-300 cursor-pointer">
                                {{ label }}
                            </label>
                            
                            <div class="relative inline-flex items-center w-11 h-6">
                                <input 
                                    type="checkbox"
                                    :id="`${moduleName}-${permission}`"
                                    v-model="modelValue[moduleName][permission]"
                                    :disabled="disabled || isSharedRole"
                                    class="sr-only peer"
                                    @change="updateValue"
                                />
                                <div 
                                    class="w-full h-full bg-gray-700 rounded-full transition peer-checked:bg-violet-600 
                                        peer-disabled:opacity-60 peer-disabled:cursor-not-allowed"
                                ></div>
                                <span 
                                    class="absolute left-0 top-0 h-5 w-5 bg-white rounded-full transition-all 
                                        translate-x-1 peer-checked:translate-x-6
                                        peer-disabled:opacity-60 peer-disabled:cursor-not-allowed"
                                ></span>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</template>

<script lang="ts">
import { defineComponent, reactive, computed, onMounted } from 'vue';

export default defineComponent({
    name: 'RolePermissions',
    
    props: {
        modelValue: {
            type: Object,
            required: true
        },
        disabled: {
            type: Boolean,
            default: false
        },
        isSharedRole: {
            type: Boolean,
            default: false
        },
        modulesList: {
            type: Object,
            required: true
        }
    },
    
    emits: ['update:modelValue'],
    
    setup(props, { emit }) {
        const expandedModules = reactive<Record<string, boolean>>({});
        
        onMounted(() => {
            Object.keys(props.modulesList).forEach(moduleName => {
                expandedModules[moduleName] = false;
            });
            
            if (Object.keys(props.modulesList).length > 0) {
                expandedModules[Object.keys(props.modulesList)[0]] = true;
            }
        });
        
        const moduleNames = computed(() => Object.keys(props.modulesList));
        
        const getModuleDisplayName = (moduleKey: string): string => {
            const moduleNames: Record<string, string> = {
                personnel: 'Gestion du personnel',
                roles: 'Gestion des rôles',
                enterprise: 'Entreprise'
            };
            
            return moduleNames[moduleKey] || moduleKey;
        };
        
        const toggleModule = (moduleName: string) => {
            expandedModules[moduleName] = !expandedModules[moduleName];
        };
        
        const updateValue = () => {
            emit('update:modelValue', props.modelValue);
        };
        
        const getActivePermissionsCount = (moduleName: string): number => {
            if (!props.modelValue[moduleName]) return 0;
            
            return Object.values(props.modelValue[moduleName]).filter(val => val === true).length;
        };
        
        return {
            expandedModules,
            moduleNames,
            getModuleDisplayName,
            toggleModule,
            updateValue,
            getActivePermissionsCount
        };
    }
});
</script>