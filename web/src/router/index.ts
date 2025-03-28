import { createRouter, createWebHistory } from 'vue-router';
import dashboard from '@/components/dashboard.vue';
import login from '@/components/auth/login.vue';
import register from '@/components/auth/register.vue';
import notFound from '@/components/errors/notFound.vue';

const routes = [
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
    { 
        path: '/dashboard', 
        component: dashboard,
        name: 'dashboard',
        meta: { requiresAuth: true }
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
        next({ name: 'login' });
    } else if (to.path === '/login' && hasToken) {
        next({ name: 'dashboard' });
    } else {
        next();
    }
});

export default router;