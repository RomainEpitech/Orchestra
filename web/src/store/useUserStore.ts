import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { User } from '@/interfaces/User';
import apiFetch from '../utils/apiFetch';
import type { Router } from 'vue-router';

interface LoginCredentials {
    email: string;
    password: string;
}

interface LoginResponse {
    message: string;
    data: { 
        token: string;
        user: User;
    };
}

interface RegisterData {
    enterprise_name: string;
    first_name: string;
    last_name: string;
    email: string;
    password: string;
    confirm_password: string;
}

export const useUserStore = defineStore('user', () => {
    // State
    const user = ref<User | null>(null);
    const token = ref<string | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    // Getters
    const isAuthenticated = computed(() => !!token.value);
    const userInitials = computed(() => {
        if (!user.value) return '';
        return (user.value.firstname.charAt(0) + user.value.lastname.charAt(0)).toUpperCase();
    });

    const hasRoleAuthority = (module: string, permission: string): boolean => {
        if (!user.value || !user.value.role || !user.value.role.authority) {
            return false;
        }

        // Si l'utilisateur a des droits admin ("*")
        if (user.value.role.authority['*']) {
            return true;
        }

        return !!(user.value.role.authority[module] && user.value.role.authority[module][permission]);
    };

    const initializeFromLocalStorage = () => {
        const storedToken = localStorage.getItem('token');
        const storedUser = localStorage.getItem('user');
        
        if (storedToken) {
            token.value = storedToken;
        }
        
        if (storedUser) {
            try {
                user.value = JSON.parse(storedUser);
            } catch (e) {
                console.error('Erreur de parsing des données utilisateur:', e);
                user.value = null;
            }
        }
    };

    const login = async (credentials: LoginCredentials) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await apiFetch.post<LoginResponse>('/auth/login', credentials);
            
            if (response && response.data && response.data.token) {
                token.value = response.data.token;
                user.value = response.data.user;
                
                // Stocker dans localStorage pour persistance
                localStorage.setItem('token', response.data.token);
                localStorage.setItem('user', JSON.stringify(response.data.user));
                
                return true;
            } else {
                throw new Error("Échec de l'authentification: Aucun token reçu");
            }
        } catch (err: any) {
            console.error('Login failed', err);
            error.value = err.message || "Une erreur est survenue lors de la connexion";
            return false;
        } finally {
            loading.value = false;
        }
    };

    const register = async (data: RegisterData) => {
        loading.value = true;
        error.value = null;
        
        try {
            await apiFetch.post('/enterprise/register', {
                enterprise_name: data.enterprise_name,
                first_name: data.first_name,
                last_name: data.last_name,
                email: data.email,
                password: data.password,
                confirm_password: data.confirm_password
            });
            
            return true;
        } catch (err: any) {
            console.error('Registration failed', err);
            error.value = err.message || "Une erreur est survenue lors de l'inscription";
        return false;
        } finally {
            loading.value = false;
        }
    };

    const logout = async (router?: Router) => {
        loading.value = true;
        
        try {
            try {
                await apiFetch.post('/auth/logout');
            } catch (e) {
                console.error('Erreur lors de la déconnexion côté serveur:', e);
            }
            
            // Nettoyer le state et le localStorage
            token.value = null;
            user.value = null;
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            
            if (router) {
                router.push('/login');
            }
        } finally {
            loading.value = false;
        }
    };

    const updateUserProfile = async (userData: Partial<User>) => {
        loading.value = true;
        error.value = null;
    
        try {
            const response = await apiFetch.put('/auth/update', userData);
            
            if (response && response.data && response.data.user) {
                user.value = { ...user.value, ...response.data.user } as User;
                localStorage.setItem('user', JSON.stringify(user.value));
            } else {
                if (user.value) {
                    user.value = { ...user.value, ...userData } as User;
                    localStorage.setItem('user', JSON.stringify(user.value));
                }
            }
            
            return true;
        } catch (err: any) {
            console.error('Update profile failed', err);
            error.value = err.message || "Une erreur est survenue lors de la mise à jour du profil";
            return false;
        } finally {
            loading.value = false;
        }
    };

    initializeFromLocalStorage();

    return { 
        user, 
        token, 
        loading, 
        error, 
        isAuthenticated,
        userInitials,
        hasRoleAuthority,
        login,
        register,
        logout,
        updateUserProfile
    };
});