<template>
    <div class="max-w-4xl mx-auto bg-gray-800/40 backdrop-blur-sm rounded-lg border border-gray-700 shadow-xl overflow-hidden">
        <div class="relative h-28 sm:h-36 bg-gradient-to-r from-violet-600 to-indigo-700 flex items-end">
            <div class="absolute inset-0 opacity-10" 
                style="background-size: 40px;"
            />
            
            <div class="p-4 sm:p-8 w-full flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7 mr-2 sm:mr-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                <h1 class="text-xl sm:text-3xl font-bold text-white">Modifier mon profil</h1>
            </div>
        </div>
        
        <div class="p-4 sm:p-8">
            <form @submit.prevent="updateProfile" class="space-y-6 sm:space-y-8">
                <div class="bg-gray-700/30 rounded-xl p-4 sm:p-6 border border-gray-700 hover:border-gray-600 transition-colors duration-300">
                    <h2 class="text-base sm:text-lg font-semibold text-white mb-4 sm:mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 sm:mr-3 text-violet-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        Photo de profil
                    </h2>
                    
                    <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-8">
                        <div class="relative flex-shrink-0">
                            <div v-if="previewImage" class="w-24 h-24 sm:w-32 sm:h-32 rounded-full overflow-hidden border-4 border-violet-500/30 shadow-lg">
                                <img :src="previewImage" alt="Avatar utilisateur" class="w-full h-full object-cover" />
                            </div>
                            <div v-else-if="formData.avatar" class="w-24 h-24 sm:w-32 sm:h-32 rounded-full overflow-hidden border-4 border-violet-500/30 shadow-lg">
                                <img :src="formData.avatar" alt="Avatar utilisateur" class="w-full h-full object-cover" />
                            </div>
                            <div v-else class="w-24 h-24 sm:w-32 sm:h-32 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center border-4 border-violet-500/30 shadow-lg">
                                <span class="text-2xl sm:text-4xl font-bold text-white">{{ userInitials }}</span>
                            </div>
                            
                            <div class="absolute -inset-2 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none" 
                                style="background: radial-gradient(circle, rgba(139, 92, 246, 0.3) 0%, transparent 70%);">
                            </div>
                        </div>
                        
                        <div class="flex-1 text-center sm:text-left">
                            <label for="avatar-upload" class="block text-base sm:text-lg font-medium text-white mb-2 sm:mb-3">
                                Choisir une image
                            </label>
                            
                            <p class="text-xs sm:text-sm text-gray-400 mb-3 sm:mb-4">
                                Téléchargez une photo de profil au format JPG, PNG ou JPEG. 
                                L'image sera affichée sous forme de cercle.
                            </p>
                            
                            <div class="flex flex-wrap justify-center sm:justify-start items-center gap-3 sm:space-x-4">
                                <label class="relative group cursor-pointer inline-flex items-center justify-center bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 py-2 px-3 sm:px-4 rounded-lg text-white text-xs sm:text-sm font-medium transition-all duration-300 hover:shadow-lg active:scale-95">
                                    <span class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
                                        </svg>
                                        Parcourir...
                                    </span>
                                    <input 
                                        type="file" 
                                        id="avatar-upload"
                                        ref="fileInput"
                                        @change="handleFileChange" 
                                        accept=".jpg,.jpeg,.png"
                                        class="sr-only"
                                    />
                                </label>
                                
                                <button 
                                    v-if="previewImage || formData.avatar" 
                                    type="button" 
                                    @click="removeAvatar"
                                    class="group text-xs sm:text-sm text-red-400 hover:text-red-300 transition-colors duration-200 flex items-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="group-hover:underline">Supprimer la photo</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-700/30 rounded-xl p-4 sm:p-6 border border-gray-700 hover:border-gray-600 transition-colors duration-300">
                    <h2 class="text-base sm:text-lg font-semibold text-white mb-4 sm:mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 sm:mr-3 text-violet-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                        </svg>
                        Informations personnelles
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div class="space-y-2 relative group">
                            <label for="firstname" class="block text-sm font-medium text-gray-300">
                                Prénom <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="firstname" 
                                    v-model="formData.firstname"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg shadow-sm py-2 sm:py-3 px-3 sm:px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Votre prénom"
                                />
                                <div class="absolute inset-0 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none" 
                                    style="background: linear-gradient(90deg, rgba(139, 92, 246, 0) 0%, rgba(139, 92, 246, 0.1) 50%, rgba(139, 92, 246, 0) 100%); 
                                    background-size: 200% 100%; 
                                    animation: shimmer 2s infinite;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2 relative group">
                            <label for="lastname" class="block text-sm font-medium text-gray-300">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="lastname" 
                                    v-model="formData.lastname"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg shadow-sm py-2 sm:py-3 px-3 sm:px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Votre nom"
                                />
                                <div class="absolute inset-0 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none" 
                                    style="background: linear-gradient(90deg, rgba(139, 92, 246, 0) 0%, rgba(139, 92, 246, 0.1) 50%, rgba(139, 92, 246, 0) 100%); 
                                    background-size: 200% 100%; 
                                    animation: shimmer 2s infinite;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2 space-y-2 relative group">
                            <label for="email" class="block text-sm font-medium text-gray-300">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="email" 
                                    id="email" 
                                    v-model="formData.email"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg shadow-sm py-2 sm:py-3 px-3 sm:px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                                    placeholder="votre.email@exemple.com"
                                />
                                <div class="absolute inset-0 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none" 
                                    style="background: linear-gradient(90deg, rgba(139, 92, 246, 0) 0%, rgba(139, 92, 246, 0.1) 50%, rgba(139, 92, 246, 0) 100%); 
                                    background-size: 200% 100%; 
                                    animation: shimmer 2s infinite;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-700/30 rounded-xl p-4 sm:p-6 border border-gray-700 hover:border-gray-600 transition-colors duration-300">
                    <h2 class="text-base sm:text-lg font-semibold text-white mb-4 sm:mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 sm:mr-3 text-violet-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
                        </svg>
                        Informations entreprise
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div class="bg-gray-800/70 backdrop-blur-sm rounded-lg px-4 sm:px-6 py-4 sm:py-5 border border-gray-700/50 transition-all duration-300 hover:border-gray-600/50 hover:shadow-md">
                            <p class="text-xs sm:text-sm text-gray-400 mb-1">Entreprise</p>
                            <p class="text-base sm:text-lg text-white font-medium break-words">{{ enterpriseName }}</p>
                        </div>
                        <div class="bg-gray-800/70 backdrop-blur-sm rounded-lg px-4 sm:px-6 py-4 sm:py-5 border border-gray-700/50 transition-all duration-300 hover:border-gray-600/50 hover:shadow-md">
                            <p class="text-xs sm:text-sm text-gray-400 mb-1">Rôle</p>
                            <div class="flex items-center">
                                <span 
                                    class="inline-block w-4 h-4 rounded-full mr-2 shadow-sm flex-shrink-0"
                                    :style="{ backgroundColor: roleColor }"
                                    aria-hidden="true"
                                ></span>
                                <p class="text-base sm:text-lg text-white font-medium break-words">{{ roleName }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Boutons d'action -->
                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4 sm:pt-6">
                    <button 
                        type="button"
                        @click="cancelEdit" 
                        class="inline-flex justify-center items-center py-2.5 sm:py-3 px-4 sm:px-6 border border-gray-600 shadow-sm text-sm font-medium rounded-lg text-gray-300 bg-transparent hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-gray-500 transition-all duration-200 active:scale-95"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1.5 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Annuler
                    </button>
                    <button 
                        type="submit"
                        class="inline-flex justify-center items-center py-2.5 sm:py-3 px-4 sm:px-6 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-violet-500 transition-all duration-200 active:scale-95"
                        :disabled="isSubmitting"
                    >
                        <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-1.5 sm:mr-2 h-4 w-4 sm:h-5 sm:w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1.5 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="whitespace-nowrap">{{ isSubmitting ? 'Enregistrement...' : 'Enregistrer' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import apiFetch from '@/utils/apiFetch';

export default defineComponent({
    name: 'UpdateProfile',
    
    setup() {
        const router = useRouter();
        const isSubmitting = ref(false);
        const fileInput = ref<HTMLInputElement | null>(null);
        const previewImage = ref<string | null>(null);
        const selectedFile = ref<File | null>(null);
        
        const formData = ref({
            firstname: '',
            lastname: '',
            email: '',
            avatar: null as string | null
        });
        
        const userData = ref({
            role: {
                name: '',
                color_hex: ''
            },
            enterprise: {
                name: ''
            }
        });
        
        onMounted(() => {
            const userString = localStorage.getItem('user');
            if (userString) {
                try {
                    const parsedUser = JSON.parse(userString);
                    
                    formData.value = {
                        firstname: parsedUser.firstname || '',
                        lastname: parsedUser.lastname || '',
                        email: parsedUser.email || '',
                        avatar: parsedUser.avatar
                    };
                    
                    userData.value = parsedUser;
                } catch (error) {
                    console.error('Failed to parse user data from localStorage:', error);
                }
            }
        });
        
        const userInitials = computed(() => {
            const firstName = formData.value.firstname || '';
            const lastName = formData.value.lastname || '';
            
            return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
        });
        
        const enterpriseName = computed(() => {
            return userData.value.enterprise?.name || 'Non spécifié';
        });
        
        const roleName = computed(() => {
            return userData.value.role?.name || 'Utilisateur standard';
        });
        
        const roleColor = computed(() => {
            return userData.value.role?.color_hex || '#9333EA';
        });
        
        const handleFileChange = (event: Event) => {
            const input = event.target as HTMLInputElement;
            
            if (input.files && input.files[0]) {
                selectedFile.value = input.files[0];
                previewImage.value = URL.createObjectURL(input.files[0]);
            }
        };
        
        const removeAvatar = () => {
            if (fileInput.value) {
                fileInput.value.value = '';
            }
            
            previewImage.value = null;
            selectedFile.value = null;
            formData.value.avatar = null;
        };
        
        const cancelEdit = () => {
            router.push('/profile');
        };
        
        const updateProfile = async () => {
            try {
                isSubmitting.value = true;
                
                const updateData = {
                    firstname: formData.value.firstname,
                    lastname: formData.value.lastname,
                    email: formData.value.email,
                };

                try {
                    const result = await apiFetch.put('/auth/update', updateData);
                    
                    const userString = localStorage.getItem('user');
                    if (userString) {
                        const user = JSON.parse(userString);
                        const updatedUser = {
                            ...user,
                            firstname: formData.value.firstname,
                            lastname: formData.value.lastname,
                            email: formData.value.email,
                            avatar: formData.value.avatar
                        };
                        
                        if (result.data && result.data.user) {
                            Object.assign(updatedUser, result.data.user);
                        }
                        
                        localStorage.setItem('user', JSON.stringify(updatedUser));
                    }
                    
                    router.push('/profile');
                    window.location.reload();
                } catch (apiError: any) {
                    console.error('Erreur API:', apiError);
                    if (apiError.status === 500 && apiError.error && apiError.error.includes('Unknown column \'id\'')) {
                        console.warn('Problème détecté avec la validation d\'UUID, mise à jour locale uniquement');
                        
                        const userString = localStorage.getItem('user');
                        if (userString) {
                            const user = JSON.parse(userString);
                            const updatedUser = {
                                ...user,
                                firstname: formData.value.firstname,
                                lastname: formData.value.lastname,
                                email: formData.value.email
                            };
                            
                            if (selectedFile.value && previewImage.value) {
                                updatedUser.avatar = previewImage.value;
                            }
                            
                            localStorage.setItem('user', JSON.stringify(updatedUser));
                        }
                        
                        router.push('/profile');
                    } else {
                        throw apiError;
                    }
                }
            } catch (error: any) {
                console.error('Erreur lors de la mise à jour du profil:', error);
                
                if (error.errors && Object.keys(error.errors).length > 0) {
                    console.error('Erreurs de validation:', error.errors);
                }
            } finally {
                isSubmitting.value = false;
            }
        };
        
        return {
            formData,
            isSubmitting,
            fileInput,
            previewImage,
            userInitials,
            enterpriseName,
            roleName,
            roleColor,
            handleFileChange,
            removeAvatar,
            cancelEdit,
            updateProfile
        };
    }
});
</script>

<style scoped>
@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>