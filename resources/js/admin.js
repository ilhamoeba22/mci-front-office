import './bootstrap'; // Load Axios & CSRF
import { createApp } from 'vue';
import router from './router';
import AdminApp from './components/Admin/AdminApp.vue';

import '../css/app.css'; 

const app = createApp(AdminApp);

app.use(router);
app.mount('#app');
