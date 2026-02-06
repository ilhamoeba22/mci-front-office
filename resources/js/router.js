import { createRouter, createWebHistory } from 'vue-router';
import AdminLayout from './components/Admin/AdminLayout.vue';
import Dashboard from './pages/AdminDashboard.vue';
import QueueManagement from './pages/QueueManagement.vue';
import QueueHistory from './pages/QueueHistory.vue';
import Settings from './pages/Settings.vue';

const routes = [
    {
        path: '/admin',
        component: AdminLayout,
        children: [
            { path: '', name: 'dashboard', component: Dashboard },
            { path: 'queue/:type', name: 'queue', component: QueueManagement, props: true },
            { path: 'queue/:type/history', name: 'queue_history', component: QueueHistory, props: true },
            { path: 'settings', name: 'settings', component: Settings },
        ]
    },
    { path: '/:pathMatch(.*)*', redirect: '/admin' }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
