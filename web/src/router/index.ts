import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../components/dashboard.vue';
import Login from '../components/auth/login.vue';
import Register from '../components/auth/register.vue';

const routes = [
    { 
        path: '/login', 
        component: Login,
        name: 'login',
        meta: { requiresAuth: false }
    },
    { 
        path: '/register', 
        component: Register,
        name: 'register',
        meta: { requiresAuth: false }
    },
    { 
        path: '/dashboard', 
        component: Dashboard,
        name: 'dashboard',
        meta: { requiresAuth: true }
    },
    {
        path: '/',
        redirect: '/dashboard'
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
    } else if (to.path === '/login' || to.path === '/register' && hasToken) {
        next({ name: 'dashboard' });
    } else {
        next();
    }
});

export default router;