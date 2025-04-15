<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Modifier un collaborateur</h1>
            <p class="text-gray-400 mt-1">Mettre à jour les informations et les accès du collaborateur</p>
        </div>

        <div v-if="isLoading" class="bg-gray-800/40 backdrop-blur-sm border border-gray-700 rounded-lg p-6 flex justify-center items-center min-h-[300px]">
            <div class="flex flex-col items-center">
                <div class="spinner">
                    <div class="w-16 h-16 border-4 border-gray-600 border-t-violet-500 rounded-full animate-spin"></div>
                </div>
                <p class="mt-4 text-gray-400">Chargement des informations...</p>
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
                @click="loadCollaborator" 
                class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-violet-500 transition-colors mx-auto"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Réessayer
            </button>
        </div>

        <div v-else class="bg-gray-800/40 backdrop-blur-sm border border-gray-700 rounded-lg p-6">
            <div v-if="validationErrors.length > 0" class="bg-red-900/30 border border-red-800 rounded-lg p-4 mb-6">
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

            <div v-if="successMessage" class="bg-green-900/30 border border-green-800 rounded-lg p-4 mb-6">
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
            
            <form @submit.prevent="submitForm" class="space-y-6">
                <div>
                    <h2 class="text-lg font-medium text-white mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informations personnelles
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="firstname" class="block text-sm font-medium text-gray-300">
                                Prénom <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input
                                    type="text"
                                    id="firstname"
                                    v-model="formData.firstname"
                                    required
                                    class="block w-full bg-gray-700 border border-gray-600 rounded-lg py-3 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Prénom du collaborateur"
                                    :class="{'border-red-500': hasFieldError('firstname')}"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="lastname" class="block text-sm font-medium text-gray-300">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input
                                    type="text"
                                    id="lastname"
                                    v-model="formData.lastname"
                                    required
                                    class="block w-full bg-gray-700 border border-gray-600 rounded-lg py-3 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Nom du collaborateur"
                                    :class="{'border-red-500': hasFieldError('lastname')}"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-medium text-white mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Informations de compte
                    </h2>
                    <div class="space-y-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input
                                    type="email"
                                    id="email"
                                    v-model="formData.email"
                                    required
                                    class="block w-full bg-gray-700 border border-gray-600 rounded-lg py-3 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                                    placeholder="email@exemple.com"
                                    :class="{'border-red-500': hasFieldError('email')}"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-300">
                                Rôle <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <select
                                    id="role"
                                    v-model="formData.role_uuid"
                                    required
                                    class="block w-full bg-gray-700 border border-gray-600 rounded-lg py-3 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                                    :class="{'border-red-500': hasFieldError('role_uuid')}"
                                >
                                    <option value="" disabled>Sélectionner un rôle</option>
                                    <option v-for="role in roles" :key="role.uuid" :value="role.uuid">
                                        {{ role.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-medium text-white mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Mot de passe
                    </h2>
                    <div class="bg-gray-900/50 border border-gray-700 rounded-lg p-4">
                        <p class="text-gray-300 mb-3">
                            Vous pouvez regénérer un nouveau mot de passe pour ce collaborateur. Un email sera envoyé avec les nouvelles informations de connexion.
                        </p>
                        <button
                            type="button"
                            @click="resetPassword"
                            :disabled="isResettingPassword"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-yellow-500 transition-colors"
                        >
                            <svg v-if="isResettingPassword" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ isResettingPassword ? 'Réinitialisation...' : 'Réinitialiser le mot de passe' }}
                        </button>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6">
                    <router-link
                        to="/collaborateurs"
                        class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-violet-500 transition-colors"
                    >
                        Annuler
                    </router-link>
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-violet-500 transition-colors"
                    >
                        <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ isSubmitting ? 'Enregistrement...' : 'Enregistrer les modifications' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import apiFetch from '@/utils/apiFetch';

interface Role {
    uuid: string;
    name: string;
    color_hex: string;
    hierarchy_level: number;
    is_shared: boolean;
}

export default defineComponent({
    name: 'EditCollaborator',
    
    setup() {
        const route = useRoute();
        const roles = ref<Role[]>([]);
        const isLoading = ref(true);
        const isSubmitting = ref(false);
        const isResettingPassword = ref(false);
        const error = ref<string | null>(null);
        const errorFields = ref<Record<string, boolean>>({});
        const validationErrors = ref<string[]>([]);
        const successMessage = ref<string | null>(null);
        
        const formData = ref({
            firstname: '',
            lastname: '',
            email: '',
            role_uuid: ''
        });
        
        const loadRoles = async () => {
            try {
                const response = await apiFetch.get('/role/assignable');
                
                if (response && response.data) {
                    roles.value = response.data;
                } else {
                    throw new Error("Format de réponse inattendu");
                }
            } catch (error: any) {
                console.error('Erreur lors du chargement des rôles:', error);
                validationErrors.value.push("Impossible de charger les rôles. Veuillez réessayer.");
            }
        };
        
        const loadCollaborator = async () => {
            isLoading.value = true;
            error.value = null;
            
            const collaboratorUuid = route.params.uuid as string;
            try {
                await loadRoles();
                const response = await apiFetch.get(`/personnel/licence/${collaboratorUuid}`);
                
                if (response && response.success && response.license) {
                    const license = response.license;
                    
                    formData.value = {
                        firstname: license.firstname,
                        lastname: license.lastname,
                        email: license.email,
                        role_uuid: license.role.uuid
                    };
                } else {
                    throw new Error("Format de réponse inattendu");
                }
            } catch (err: any) {
                console.error('Erreur lors du chargement du collaborateur:', err);
                error.value = err.message || "Une erreur est survenue lors du chargement des données du collaborateur";
            } finally {
                isLoading.value = false;
            }
        };
        
        onMounted(() => {
            loadCollaborator();
        });
        
        const hasFieldError = (fieldName: string) => {
            return errorFields.value[fieldName] === true;
        };
        
        const validateForm = () => {
            validationErrors.value = [];
            errorFields.value = {};
            
            if (!formData.value.firstname.trim()) {
                validationErrors.value.push("Le prénom est requis");
                errorFields.value.firstname = true;
            }
            
            if (!formData.value.lastname.trim()) {
                validationErrors.value.push("Le nom est requis");
                errorFields.value.lastname = true;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!formData.value.email.trim()) {
                validationErrors.value.push("L'email est requis");
                errorFields.value.email = true;
            } else if (!emailRegex.test(formData.value.email)) {
                validationErrors.value.push("Veuillez entrer une adresse email valide");
                errorFields.value.email = true;
            }
            
            if (!formData.value.role_uuid) {
                validationErrors.value.push("Veuillez sélectionner un rôle");
                errorFields.value.role_uuid = true;
            }
            
            return validationErrors.value.length === 0;
        };
        
        const submitForm = async () => {
            if (!validateForm()) return;
            
            isSubmitting.value = true;
            successMessage.value = null;
            
            const collaboratorUuid = route.params.uuid as string;
            
            try {
                const response = await apiFetch.put(`/personnel/licence/${collaboratorUuid}`, formData.value);
                
                if (response && response.data) {
                    successMessage.value = `Les informations de ${formData.value.firstname} ${formData.value.lastname} ont été mises à jour avec succès.`;
                    
                    // Faire défiler vers le haut pour afficher le message de succès
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    throw new Error(response.message || "Une erreur est survenue lors de la mise à jour du collaborateur");
                }
            } catch (error: any) {
                console.error('Erreur lors de la mise à jour du collaborateur:', error);
                
                if (error.response && error.response.data && error.response.data.errors) {
                    Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                        errorFields.value[field] = true;
                        const messageArray = Array.isArray(messages) ? messages : [String(messages)];
                        messageArray.forEach(msg => {
                            validationErrors.value.push(msg);
                        });
                    });
                } else {
                    validationErrors.value.push(error.message || "Une erreur est survenue lors de la mise à jour du collaborateur");
                }
            } finally {
                isSubmitting.value = false;
            }
        };
        
        const resetPassword = async () => {
            const collaboratorUuid = route.params.uuid as string;
            isResettingPassword.value = true;
            successMessage.value = null;
            
            try {
                // Endpoint fictif, à adapter selon votre API
                const response = await apiFetch.post(`/personnel/licence/${collaboratorUuid}/reset-password`);
                
                if (response && response.success) {
                    successMessage.value = "Un nouveau mot de passe a été généré et envoyé par email au collaborateur.";
                    
                    // Faire défiler vers le haut pour afficher le message de succès
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    throw new Error(response.message || "Une erreur est survenue lors de la réinitialisation du mot de passe");
                }
            } catch (error: any) {
                console.error('Erreur lors de la réinitialisation du mot de passe:', error);
                validationErrors.value.push(error.message || "Une erreur est survenue lors de la réinitialisation du mot de passe");
            } finally {
                isResettingPassword.value = false;
            }
        };
        
        return {
            roles,
            formData,
            isLoading,
            isSubmitting,
            isResettingPassword,
            error,
            validationErrors,
            successMessage,
            hasFieldError,
            loadCollaborator,
            submitForm,
            resetPassword
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