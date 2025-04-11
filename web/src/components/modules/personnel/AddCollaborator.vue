<template>
    <div class="space-y-6">
        <!-- En-tête avec titre -->
        <div>
            <h1 class="text-2xl font-bold text-white">Ajouter un collaborateur</h1>
            <p class="text-gray-400 mt-1">Créez un compte pour un nouveau membre de votre équipe</p>
        </div>

        <!-- Formulaire -->
        <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700 rounded-lg p-6">
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Messages d'erreur -->
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

                <!-- Informations personnelles -->
                <div>
                    <h2 class="text-lg font-medium text-white mb-4">Informations personnelles</h2>
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

                <!-- Informations de compte -->
                <div>
                    <h2 class="text-lg font-medium text-white mb-4">Informations de compte</h2>
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
                            <p class="mt-1 text-sm text-gray-400">Un email d'invitation sera envoyé à cette adresse.</p>
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
                                    <option value="" disabled selected>Sélectionner un rôle</option>
                                    <option v-for="role in roles" :key="role.uuid" :value="role.uuid">
                                        {{ role.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
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
                        {{ isSubmitting ? 'Création en cours...' : 'Créer le collaborateur' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import apiFetch from '@/utils/apiFetch';

interface Role {
    uuid: string;
    name: string;
    color_hex: string;
}

export default defineComponent({
    name: 'AddCollaborator',
    
    setup() {
        const router = useRouter();
        const roles = ref<Role[]>([]);
        const isLoadingRoles = ref(true);
        const isSubmitting = ref(false);
        const errorFields = ref<Record<string, boolean>>({});
        const validationErrors = ref<string[]>([]);
        
        const formData = ref({
            firstname: '',
            lastname: '',
            email: '',
            role_uuid: ''
        });
        
        // Charger les rôles disponibles
        const loadRoles = async () => {
            isLoadingRoles.value = true;
            
            try {
                const response = await apiFetch.get('/personnel/roles');
                
                if (response && response.success && response.roles) {
                    roles.value = response.roles;
                } else {
                    throw new Error("Format de réponse inattendu");
                }
            } catch (error: any) {
                console.error('Erreur lors du chargement des rôles:', error);
                validationErrors.value.push("Impossible de charger les rôles. Veuillez réessayer.");
            } finally {
                isLoadingRoles.value = false;
            }
        };
        
        onMounted(() => {
            loadRoles();
        });
        
        const hasFieldError = (fieldName: string) => {
            return errorFields.value[fieldName] === true;
        };
        
        const validateForm = () => {
            validationErrors.value = [];
            errorFields.value = {};
            
            // Validation du prénom
            if (!formData.value.firstname.trim()) {
                validationErrors.value.push("Le prénom est requis");
                errorFields.value.firstname = true;
            }
            
            // Validation du nom
            if (!formData.value.lastname.trim()) {
                validationErrors.value.push("Le nom est requis");
                errorFields.value.lastname = true;
            }
            
            // Validation de l'email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!formData.value.email.trim()) {
                validationErrors.value.push("L'email est requis");
                errorFields.value.email = true;
            } else if (!emailRegex.test(formData.value.email)) {
                validationErrors.value.push("Veuillez entrer une adresse email valide");
                errorFields.value.email = true;
            }
            
            // Validation du rôle
            if (!formData.value.role_uuid) {
                validationErrors.value.push("Veuillez sélectionner un rôle");
                errorFields.value.role_uuid = true;
            }
            
            return validationErrors.value.length === 0;
        };
        
        const submitForm = async () => {
            if (!validateForm()) return;
            
            isSubmitting.value = true;
            
            try {
                const response = await apiFetch.post('/personnel/licence', formData.value);
                
                if (response && response.success) {
                    // Redirection vers la liste des collaborateurs avec un message de succès
                    router.push({ 
                        path: '/collaborateurs',
                        query: { 
                            notification: 'success',
                            message: `Le collaborateur ${formData.value.firstname} ${formData.value.lastname} a été ajouté avec succès.`
                        }
                    });
                } else {
                    throw new Error(response.message || "Une erreur est survenue lors de la création du collaborateur");
                }
            } catch (error: any) {
                console.error('Erreur lors de la création du collaborateur:', error);
                
                if (error.errors && Object.keys(error.errors).length > 0) {
                    // Traiter les erreurs de validation spécifiques aux champs
                    Object.entries(error.errors).forEach(([field, messages]) => {
                        errorFields.value[field] = true;
                        const messageArray = Array.isArray(messages) ? messages : [String(messages)];
                        messageArray.forEach(msg => {
                            validationErrors.value.push(msg);
                        });
                    });
                } else {
                    // Erreur générale
                    validationErrors.value.push(error.message || "Une erreur est survenue lors de la création du collaborateur");
                }
            } finally {
                isSubmitting.value = false;
            }
        };
        
        return {
            roles,
            formData,
            isLoadingRoles,
            isSubmitting,
            validationErrors,
            hasFieldError,
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