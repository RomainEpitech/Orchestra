import { createRouter, createWebHistory } from 'vue-router';
import type { RouteRecordRaw } from 'vue-router';
import Dashboard from '@/components/core/Dashboard.vue';
import login from '@/components/auth/login.vue';
import register from '@/components/auth/register.vue';
import notFound from '@/components/errors/notFound.vue';
import unauthorizedAccess from '../components/errors/unauthorizedAccess.vue';
import Profile from '@/components/user/Profile.vue';
import DashboardLayout from '@/components/layouts/DashboardLayout.vue';
import UpdateUser from '@/components/user/updateUser.vue';
import CollaboratorsList from '@/components/modules/personnel/collaboratorsList.vue';
import AddCollaborator from '@/components/modules/personnel/AddCollaborator.vue';
import RolesList from '@/components/modules/roles/RolesList.vue';

declare module 'vue-router' {
    interface RouteMeta {
        requiresAuth?: boolean;
        module?: string;
        permission?: string;
    }
}

const routes: RouteRecordRaw[] = [
    // Routes publiques
    { 
        path: '/login', 
        component: login,
        name: 'login',
        meta: { requiresAuth: false }
    },
    { 
        path: '/register', 
        component: register,
        name: 'register',
        meta: { requiresAuth: false }
    },
    
    // Dashboard accessible à tous les utilisateurs authentifiés
    { 
        path: '/dashboard', 
        component: Dashboard,
        name: 'dashboard',
        meta: { requiresAuth: true }
    },
    {
        path: '/unauthorized',
        name: 'unauthorized',
        component: unauthorizedAccess,
        meta: { requiresAuth: true }
    },
    {
        path: '/profile',
        name: 'profile',
        component: Profile,
        meta: { requiresAuth: true }
    },
    {
        path: '/',
        component: DashboardLayout,
        children: [
            {
                path: 'dashboard',
                name: 'dashboard',
                component: Dashboard,
                meta: { requiresAuth: true }
            },
            {
                path: 'profile',
                name: 'profile',
                component: Profile,
                meta: { requiresAuth: true }
            },
            {
                path: 'profile/update',
                name: 'profileupdate',
                component: UpdateUser,
                meta: { requiresAuth: true }
            },
            // Module Personnel
            {
                path: 'collaborateurs',
                name: 'collaborators',
                component: CollaboratorsList,
                meta: { requiresAuth: true }
            },
            {
                path: 'collaborateurs/ajouter',
                name: 'collaboratorsNewLicense',
                component: AddCollaborator,
                meta: { requiresAuth: true }
            },
            // Module Roles
            {
                path: 'roles',
                name: 'roles',
                component: RolesList,
                meta: { requiresAuth: true }
            }
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: notFound,
        meta: { requiresAuth: false }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, _from, next) => {
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
    const hasToken = !!localStorage.getItem('token');
    
    if (requiresAuth && !hasToken) {
        return next({ name: 'login' });
    }
    
    if (to.path === '/login' && hasToken) {
        return next({ name: 'dashboard' });
    }
    
    if (requiresAuth && hasToken && to.meta.module && to.meta.permission) {
        const module = to.meta.module;
        const requiredPermission = to.meta.permission;
        
        try {
            const userString = localStorage.getItem('user');
            if (!userString) {
                return next({ name: 'unauthorized' });
            }
            
            const userData = JSON.parse(userString);
            const authority = userData.role?.authority || {};
            
            if (!authority[module] || !authority[module][requiredPermission]) {
                console.warn(`Accès refusé: ${module}.${requiredPermission} requis`);
                return next({ name: 'unauthorized' });
            }
        } catch (error) {
            console.error('Erreur lors de la vérification des permissions:', error);
            return next({ name: 'unauthorized' });
        }
    }
    
    next();
});

export default router;