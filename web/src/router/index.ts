import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../components/dashboard.vue';
import HelloWorld from '../components/HelloWorld.vue';

const routes = [
    { path: '/', component: Dashboard },
    { path: '/personnel', component: HelloWorld }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
