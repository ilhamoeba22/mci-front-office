import { createRouter, createWebHistory } from 'vue-router';
import AdminLayout from './components/Admin/AdminLayout.vue';
import Dashboard from './pages/AdminDashboard.vue';
import QueueManagement from './pages/QueueManagement.vue';
import QueueHistory from './pages/QueueHistory.vue';
import Settings from './pages/Settings.vue';
import UserManagement from './pages/UserManagement.vue';

const routes = [
    {
        path: '/admin',
        component: AdminLayout,
        children: [
            { path: '', name: 'dashboard', component: Dashboard },
            { path: 'queue/:type', name: 'queue', component: QueueManagement, props: true },
            { path: 'queue/:type/history', name: 'queue_history', component: QueueHistory, props: true },
            { path: 'settings', name: 'settings', component: Settings, meta: { role: 'admin' } },
            { path: 'users', name: 'users', component: UserManagement, meta: { role: 'admin' } },
        ]
    },
    { path: '/:pathMatch(.*)*', redirect: '/admin' }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    // Check if route requires a specific role
    if (to.meta.role) {
        // Wait for user profile to be loaded if not already
        // This is a simple way: we assume AdminLayout handles profile fetch,
        // but for deep links we might need to check window.currentUser
        const user = window.currentUser;
        
        if (user) {
            if (user.role === to.meta.role || user.role === 'admin') {
                next();
            } else {
                next({ name: 'dashboard' }); // Redirect to dashboard if not authorized
            }
        } else {
            // If user not loaded, let it pass for now, AdminLayout will handle the redirect if 401
            next();
        }
    } else {
        next();
    }
});

export default router;
