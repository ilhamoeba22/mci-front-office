<template>
    <div class="flex h-screen bg-gray-900 text-white font-sans">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-800 border-r border-slate-700 flex flex-col transition-all duration-300">
            <div class="h-16 flex items-center justify-center border-b border-slate-700">
                <router-link to="/admin" class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent hover:opacity-80 transition-opacity">
                    MCI Admin
                </router-link>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-2 px-3">
                    <li>
                        <router-link to="/admin" class="flex items-center p-3 rounded-lg hover:bg-slate-700 transition-colors" :class="{ 'bg-blue-600': $route.name === 'dashboard' }">
                            <i class="fa-solid fa-chart-line w-6 text-center"></i>
                            <span class="ml-3 font-medium">Dashboard</span>
                        </router-link>
                    </li>
                    <li class="pt-4 pb-2 px-3 text-xs uppercase text-slate-500 font-bold">Customer Service</li>
                    <li>
                        <router-link :to="{ name: 'queue', params: { type: 'CS' } }" class="flex items-center p-3 rounded-lg hover:bg-slate-700 transition-colors" :class="{ 'bg-pink-600': $route.params.type === 'CS' && $route.name === 'queue' }">
                            <i class="fa-solid fa-headset w-6 text-center"></i>
                            <span class="ml-3 font-medium">Manajemen Antrian</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'queue_history', params: { type: 'CS' } }" class="flex items-center p-3 rounded-lg hover:bg-slate-700 transition-colors" :class="{ 'bg-pink-700': $route.params.type === 'CS' && $route.name === 'queue_history' }">
                            <i class="fa-solid fa-clock-rotate-left w-6 text-center"></i>
                            <span class="ml-3 font-medium">Status Antrian</span>
                        </router-link>
                    </li>

                    <li class="pt-4 pb-2 px-3 text-xs uppercase text-slate-500 font-bold">Teller</li>
                    <li>
                        <router-link :to="{ name: 'queue', params: { type: 'Teller' } }" class="flex items-center p-3 rounded-lg hover:bg-slate-700 transition-colors" :class="{ 'bg-teal-600': $route.params.type === 'Teller' && $route.name === 'queue' }">
                            <i class="fa-solid fa-wallet w-6 text-center"></i>
                            <span class="ml-3 font-medium">Manajemen Antrian</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'queue_history', params: { type: 'Teller' } }" class="flex items-center p-3 rounded-lg hover:bg-slate-700 transition-colors" :class="{ 'bg-teal-700': $route.params.type === 'Teller' && $route.name === 'queue_history' }">
                            <i class="fa-solid fa-clock-rotate-left w-6 text-center"></i>
                            <span class="ml-3 font-medium">Status Antrian</span>
                        </router-link>
                    </li>
                    <li class="pt-4 pb-2 px-3 text-xs uppercase text-slate-500 font-bold">Sistem</li>
                    <li>
                        <a href="/display" target="_blank" class="flex items-center p-3 rounded-lg hover:bg-slate-700 transition-colors text-cyan-400">
                            <i class="fa-solid fa-external-link-alt w-6 text-center"></i>
                            <span class="ml-3 font-medium">Buka TV Display</span>
                        </a>
                    </li>
                    <li>
                        <router-link to="/admin/settings" class="flex items-center p-3 rounded-lg hover:bg-slate-700 transition-colors" :class="{ 'bg-purple-600': $route.name === 'settings' }">
                            <i class="fa-solid fa-sliders w-6 text-center"></i>
                            <span class="ml-3 font-medium">Pengaturan</span>
                        </router-link>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-slate-700">
                <button @click="logout" class="flex w-full items-center justify-center p-2 rounded-lg bg-red-600 hover:bg-red-700 transition-colors text-sm font-bold text-white">
                    <i class="fa-solid fa-sign-out-alt mr-2"></i> Keluar
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-900">
            <header class="h-16 flex items-center justify-between px-6 bg-slate-800 border-b border-slate-700 shadow-md">
                <div class="text-slate-400 text-sm">
                    <span class="font-bold text-white">{{ currentPageTitle }}</span>
                </div>
                <div class="flex items-center">
                    <div class="bg-slate-700 rounded-full px-4 py-1 text-sm text-slate-300 border border-slate-600">
                        <i class="fa-regular fa-clock mr-2"></i>
                        <span id="header-clock">{{ currentTime }}</span>
                    </div>
                </div>
            </header>

            <div class="p-6">
                <router-view></router-view>
            </div>
        </main>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            currentTime: '00:00:00'
        }
    },
    computed: {
        currentPageTitle() {
            if (this.$route.name === 'dashboard') return 'Ikhtisar';
            if (this.$route.name === 'queue') return `Kelola Antrian ${this.$route.params.type}`;
            if (this.$route.name === 'settings') return 'Konfigurasi Sistem';
            return 'Portal Admin';
        }
    },
    mounted() {
        setInterval(() => {
            this.currentTime = new Date().toLocaleTimeString('id-ID');
        }, 1000);
    },
    methods: {
        async logout() {
            try {
                await axios.post('/logout');
                window.location.href = '/login';
            } catch (error) {
                console.error('Logout failed:', error);
                window.location.href = '/login'; // Fallback redirect
            }
        }
    }
}
</script>
