<template>
    <div class="space-y-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ isEditMode ? 'Modifier un rôle' : 'Détails du rôle' }}</h1>
                <p class="text-gray-400 mt-1">{{ isEditMode ? 'Mise à jour des informations et des permissions du rôle' : 'Consultation des informations et des permissions du rôle' }}</p>
            </div>
            
            <button 
                v-if="!isEditMode && !isSharedRole && hasRoleAuthority('roles', 'update')" 
                @click="enableEditMode"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-violet-500 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Éditer
            </button>
        </div>

        <div v-if="isLoading" class="bg-gray-800/40 backdrop-blur-sm border border-gray-700 rounded-lg p-6 flex justify-center items-center min-h-[300px]">
            <div class="flex flex-col items-center">
                <div class="spinner">
                    <div class="w-16 h-16 border-4 border-gray-600 border-t-violet-500 rounded-full animate-spin"></div>
                </div>
                <p class="mt-4 text-gray-400">Chargement des informations du rôle...</p>
            </div>
        </div>

        <div v-else-if="error" class="bg-gray-800/40 backdrop-blur-sm border border-gray-700 rounded-lg p-6 text-center min-h-[300px] flex flex-col justify-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-900/30 text-red-500 mb-4 mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-white mb-2">Erreur lors du chargement</h3>
            <p class="text-gray-400 max-w-md mx-auto">{{ error }}</p>
            <button 
                @click="loadRole" 
                class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-violet-500 transition-colors mx-auto"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Réessayer
            </button>
        </div>

        <div v-else class="space-y-6">
            <div v-if="validationErrors.length > 0" class="bg-red-900/30 border border-red-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-300">Merci de corriger les erreurs suivantes:</h3>
                        <div class="mt-2 text-sm text-red-300">
                            <ul class="list-disc pl-5 space-y-1">
                                <li v-for="(error, index) in validationErrors" :key="index">{{ error }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="successMessage" class="bg-green-900/30 border border-green-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-300">{{ successMessage }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700 rounded-lg p-6">
                <div v-if="isSharedRole" class="mb-6 bg-blue-900/20 border border-blue-800/40 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-300">Rôle système partagé</h3>
                            <div class="mt-2 text-sm text-gray-300">
                                <p>Ce rôle est un rôle partagé par le système et ne peut pas être modifié.</p>
                            </div>
                        </div>
                    </div>
                </div>
            
                <h2 class="text-lg font-medium text-white mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Informations du rôle
                </h2>
                
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div class="space-y-4">
                        <RoleFormField
                            id="role-name"
                            label="Nom du rôle"
                            v-model="formData.name"
                            :required="true"
                            :disabled="!isEditMode"
                            :error="getFieldError('name')"
                            placeholder="Nom du rôle"
                        />
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <RoleFormField
                                id="hierarchy-level"
                                label="Niveau hiérarchique"
                                :required="true"
                                :disabled="!isEditMode"
                                :error="getFieldError('hierarchy_level')"
                            >
                                <select
                                    id="hierarchy-level"
                                    v-model="formData.hierarchy_level"
                                    :required="isEditMode"
                                    :disabled="!isEditMode"
                                    class="block w-full bg-gray-700 border border-gray-600 rounded-lg py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                                    :class="{'border-red-500': getFieldError('hierarchy_level'), 'opacity-75': !isEditMode, 'cursor-not-allowed': !isEditMode}"
                                >
                                    <option value="" disabled>Sélectionnez un niveau</option>
                                    <option v-for="level in hierarchyLevels" :key="level.value" :value="level.value">
                                        {{ level.label }}
                                    </option>
                                </select>
                            </RoleFormField>
                            
                            <ColorPicker
                                id="color"
                                label="Couleur"
                                v-model="formData.color_hex"
                                :required="true"
                                :disabled="!isEditMode"
                                :error="getFieldError('color_hex')"
                            />
                        </div>
                    </div>
                    
                    <div class="pt-4">
                        <RolePermissions 
                            v-model="formData.authority"
                            :disabled="!isEditMode"
                            :isSharedRole="isSharedRole"
                            :modulesList="modulesList"
                        />
                    </div>
                    
                    <div v-if="isEditMode" class="flex justify-end space-x-3 pt-6">
                        <button
                            type="button"
                            @click="cancelEdit"
                            class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-violet-500 transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Annuler
                        </button>
                        <button
                            type="submit"
                            :disabled="isSubmitting || isSharedRole"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white"
                            :class="isSharedRole ? 'bg-gray-600 cursor-not-allowed' : 'bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-violet-500'"
                        >
                            <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ isSubmitting ? 'Enregistrement...' : 'Enregistrer les modifications' }}
                        </button>
                    </div>
                    
                    <div v-else class="flex justify-end space-x-3 pt-6">
                        <router-link
                            to="/roles"
                            class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-violet-500 transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Retour à la liste
                        </router-link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import apiFetch from '@/utils/apiFetch';
import RoleFormField from './RoleFormField.vue';
import ColorPicker from './ColorPicker.vue';
import RolePermissions from './RolePermissions.vue';

export default defineComponent({
    name: 'EditRole',
    
    components: {
        RoleFormField,
        ColorPicker,
        RolePermissions
    },
    
    setup() {
        const route = useRoute();
        const isLoading = ref(true);
        const isSubmitting = ref(false);
        const isEditMode = ref(false);
        const error = ref<string | null>(null);
        const errorFields = ref<Record<string, string>>({});
        const validationErrors = ref<string[]>([]);
        const successMessage = ref<string | null>(null);
        const originalFormData = ref<any>(null);
        
        const modulesList = {
            enterprise: {
                read: 'Lecture',
                edit: 'Modification'
            },
            personnel: {
                read: 'Lecture',
                create: 'Création',
                edit: 'Modification',
                delete: 'Suppression'
            },
            roles: {
                read: 'Lecture',
                create: 'Création',
                edit: 'Modification',
                delete: 'Suppression'
            },
        };
        
        const hierarchyLevels = [
            { value: 1, label: 'Direction (1)' },
            { value: 2, label: 'Administration (2)' },
            { value: 3, label: 'Management (3)' },
            { value: 4, label: 'Supervision (4)' },
            { value: 5, label: 'Opération (5)' },
            { value: 6, label: 'Base (6)' }
        ];
        
        const formData = ref({
            name: '',
            hierarchy_level: '',
            color_hex: '#9333EA',
            authority: {
                personnel: {
                    read: false,
                    create: false,
                    update: false,
                    delete: false
                },
                roles: {
                    read: false,
                    create: false,
                    update: false,
                    delete: false
                },
                enterprise: {
                    read: false,
                    update: false
                }
            }
        });
        
        const isSharedRole = ref(false);
        const hasRoleAuthority = (module: string, permission: string): boolean => {
            try {
                const userString = localStorage.getItem('user');
                if (!userString) return false;
                
                const userData = JSON.parse(userString);
                if (!userData.role || !userData.role.authority) return false;
                
                const authority = userData.role.authority;
                if (authority['*']) return true;
                
                return !!(authority[module] && authority[module][permission]);
            } catch (error) {
                return false;
            }
        };
        
        const getFieldError = (fieldName: string): string => {
            return errorFields.value[fieldName] || '';
        };
        
        const enableEditMode = (): void => {
            if (isSharedRole.value) return;
            originalFormData.value = JSON.parse(JSON.stringify(formData.value));
            isEditMode.value = true;
        };
        
        const cancelEdit = (): void => {
            if (originalFormData.value) {
                formData.value = JSON.parse(JSON.stringify(originalFormData.value));
            }
            
            validationErrors.value = [];
            errorFields.value = {};

            isEditMode.value = false;
        };
        
        const loadRole = async () => {
            isLoading.value = true;
            error.value = null;

            const roleUuid = route.params.uuid as string;
            
            try {
                const response = await apiFetch.get(`/role/${roleUuid}`);                
                if (response && response.success && response.data) {
                    const roleData = response.data;

                    isSharedRole.value = roleData.is_shared === true;
                    const initialAuthority: any = {};
                    
                    Object.keys(modulesList).forEach(moduleKey => {
                        initialAuthority[moduleKey] = {};
                        Object.keys(modulesList[moduleKey as keyof typeof modulesList]).forEach(permission => {
                            initialAuthority[moduleKey][permission] = false;
                        });
                    });
                    if (roleData.authority) {
                        Object.keys(roleData.authority).forEach(moduleKey => {
                            if (initialAuthority[moduleKey]) {
                                Object.keys(roleData.authority[moduleKey]).forEach(permission => {
                                    if (initialAuthority[moduleKey][permission] !== undefined) {
                                        initialAuthority[moduleKey][permission] = roleData.authority[moduleKey][permission];
                                    }
                                });
                            }
                        });
                    }
                    
                    formData.value = {
                        name: roleData.name,
                        hierarchy_level: roleData.hierarchy_level,
                        color_hex: roleData.color_hex,
                        authority: initialAuthority
                    };
                    
                    originalFormData.value = JSON.parse(JSON.stringify(formData.value));
                } else {
                    throw new Error("Format de réponse inattendu");
                }
            } catch (err: any) {
                error.value = err.message || "Une erreur est survenue lors du chargement des données du rôle";
            } finally {
                isLoading.value = false;
            }
        };
        
        const validateForm = () => {
            validationErrors.value = [];
            errorFields.value = {};
            
            if (!formData.value.name.trim()) {
                validationErrors.value.push("Le nom du rôle est requis");
                errorFields.value.name = "Le nom du rôle est requis";
            }
            
            if (!formData.value.hierarchy_level) {
                validationErrors.value.push("Le niveau hiérarchique est requis");
                errorFields.value.hierarchy_level = "Le niveau hiérarchique est requis";
            }
            
            const hexColorRegex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
            if (!formData.value.color_hex || !hexColorRegex.test(formData.value.color_hex)) {
                validationErrors.value.push("Veuillez entrer une couleur hexadécimale valide (ex: #9333EA)");
                errorFields.value.color_hex = "Veuillez entrer une couleur hexadécimale valide (ex: #9333EA)";
            }
            
            return validationErrors.value.length === 0;
        };
        
        const submitForm = async () => {
            if (!isEditMode.value) return;
            if (isSharedRole.value) return;
            if (!validateForm()) return;
            
            isSubmitting.value = true;
            successMessage.value = null;

            const roleUuid = route.params.uuid as string;
            
            try {
                const response = await apiFetch.put(`/role/${roleUuid}`, formData.value);
                if (response && response.success) {
                    successMessage.value = `Le rôle ${formData.value.name} a été mis à jour avec succès.`;
                    originalFormData.value = JSON.parse(JSON.stringify(formData.value));
                    isEditMode.value = false;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    throw new Error(response.message || "Une erreur est survenue lors de la mise à jour du rôle");
                }
            } catch (error: any) {
                console.error('Erreur lors de la mise à jour du rôle:', error);
                
                if (error.errors) {
                    Object.entries(error.errors).forEach(([field, messages]) => {
                        const messageArray = Array.isArray(messages) ? messages : [String(messages)];
                        errorFields.value[field] = messageArray[0];
                        messageArray.forEach(msg => {
                            validationErrors.value.push(msg);
                        });
                    });
                } else {
                    validationErrors.value.push(error.message || "Une erreur est survenue lors de la mise à jour du rôle");
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } finally {
                isSubmitting.value = false;
            }
        };
        
        onMounted(() => {
            loadRole();
        });
        
        return {
            isLoading,
            isSubmitting,
            isEditMode,
            isSharedRole,
            error,
            errorFields,
            validationErrors,
            successMessage,
            formData,
            modulesList,
            hierarchyLevels,
            getFieldError,
            hasRoleAuthority,
            loadRole,
            enableEditMode,
            cancelEdit,
            submitForm
        };
    }
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>