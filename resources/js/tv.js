import './bootstrap';
import { createApp } from 'vue';
import TvDisplay from './pages/TvDisplay.vue';
import '../css/app.css';

// Mount Vue application
// We pass window.tvConfig directly as props to the root component
const app = createApp(TvDisplay, window.tvConfig || {});

app.mount('#app');
