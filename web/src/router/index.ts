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
import EditCollaborator from '@/components/modules/personnel/EditCollaborator.vue';
import EditRole from '@/components/modules/roles/EditRole.vue';
import { useUserStore } from '@/store/useUserStore';

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
                meta: { requiresAuth: true, module: 'personnel', permission: 'read' }
            },
            {
                path: 'collaborateurs/ajouter',
                name: 'collaboratorsNewLicense',
                component: AddCollaborator,
                meta: { requiresAuth: true, module: 'personnel', permission: 'create' }
            },
            {
                path: 'collaborateurs/modifier/:uuid',
                name: 'collaboratorsEdit',
                component: EditCollaborator,
                meta: { requiresAuth: true, module: 'personnel', permission: 'update' }
            },
            // Module Roles
            {
                path: 'roles',
                name: 'roles',
                component: RolesList,
                meta: { requiresAuth: true, module: 'roles', permission: 'read' }
            },
            {
                path: 'roles/:uuid',
                name: 'roleEdit',
                component: EditRole,
                meta: { requiresAuth: true, module: 'roles', permission: 'read' }
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
    const userStore = useUserStore();
    
    // Vérifier si l'utilisateur est authentifié
    if (requiresAuth && !userStore.isAuthenticated) {
        return next({ name: 'login' });
    }
    
    // Rediriger vers le dashboard si l'utilisateur est déjà connecté et essaie d'accéder à la page de login
    if (to.path === '/login' && userStore.isAuthenticated) {
        return next({ name: 'dashboard' });
    }
    
    // Vérifier les autorisations pour les routes protégées
    if (requiresAuth && userStore.isAuthenticated && to.meta.module && to.meta.permission) {
        const module = to.meta.module as string;
        const requiredPermission = to.meta.permission as string;
        
        if (!userStore.hasRoleAuthority(module, requiredPermission)) {
            console.warn(`Accès refusé: ${module}.${requiredPermission} requis`);
            return next({ name: 'unauthorized' });
        }
    }
    
    next();
});

export default router;