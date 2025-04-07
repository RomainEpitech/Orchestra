import { createRouter, createWebHistory } from 'vue-router';
import dashboard from '@/components/dashboard.vue';
import login from '@/components/auth/login.vue';
import register from '@/components/auth/register.vue';
import notFound from '@/components/errors/notFound.vue';
import unauthorizedAccess from '../components/errors/unauthorizedAccess.vue';

declare module 'vue-router' {
    interface RouteMeta {
        requiresAuth?: boolean;
        module?: string;
        permission?: string;
    }
}

const routes = [
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
        component: dashboard,
        name: 'dashboard',
        meta: { requiresAuth: true }
    },
    
    {
        path: '/unauthorized',
        name: 'unauthorized',
        component: unauthorizedAccess,
        meta: { requiresAuth: true }
    },
    
    // // Module Personnel
    {
        path: '/collaborateurs',
        name: 'collaborateurs',
        component: login,
        meta: { 
            requiresAuth: true,
            module: 'personnel',
            permission: 'attend'
        }
    },
    
    {
        path: '/',
        redirect: '/dashboard'
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