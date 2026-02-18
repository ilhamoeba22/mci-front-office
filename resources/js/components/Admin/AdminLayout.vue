<template>
    <div :class="{ 'dark': isDark }">
        <div class="flex h-screen bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-white font-sans transition-colors duration-300">
            <!-- Sidebar -->
            <aside class="w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 flex flex-col transition-all duration-300">
                <div class="h-16 flex items-center justify-center border-b border-gray-200 dark:border-slate-700">
                    <router-link to="/admin" class="flex items-center justify-center hover:opacity-80 transition-opacity">
                        <!-- Filter invert for light mode logo if needed, or assume logo works on both -->
                        <img src="/img/logo_mci.png" alt="MCI Admin" class="h-12 w-auto">
                    </router-link>
                </div>

                <nav class="flex-1 overflow-y-auto py-4">
                    <ul class="space-y-2 px-3">
                        <li>
                            <router-link to="/admin" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors" :class="{ 'bg-blue-50 dark:bg-blue-600 text-blue-600 dark:text-white': $route.name === 'dashboard' }">
                                <i class="fa-solid fa-chart-line w-6 text-center"></i>
                                <span class="ml-3 font-medium">Dashboard</span>
                            </router-link>
                        </li>

                        <!-- Customer Service Section -->
                        <template v-if="user?.role === 'admin' || user?.role === 'cs'">
                            <li class="pt-4 pb-2 px-3 text-xs uppercase text-gray-500 dark:text-slate-500 font-bold">Customer Service</li>
                            <li>
                                <router-link :to="{ name: 'queue', params: { type: 'CS' } }" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors" :class="{ 'bg-pink-50 dark:bg-pink-600 text-pink-600 dark:text-white': $route.params.type === 'CS' && $route.name === 'queue' }">
                                    <i class="fa-solid fa-headset w-6 text-center"></i>
                                    <span class="ml-3 font-medium">Manajemen Antrian</span>
                                </router-link>
                            </li>
                            <li>
                                <router-link :to="{ name: 'queue_history', params: { type: 'CS' } }" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors" :class="{ 'bg-pink-100 dark:bg-pink-700 text-pink-700 dark:text-white': $route.params.type === 'CS' && $route.name === 'queue_history' }">
                                    <i class="fa-solid fa-clock-rotate-left w-6 text-center"></i>
                                    <span class="ml-3 font-medium">Status Antrian</span>
                                </router-link>
                            </li>
                        </template>

                        <!-- Teller Section -->
                        <template v-if="user?.role === 'admin' || user?.role === 'teller'">
                            <li class="pt-4 pb-2 px-3 text-xs uppercase text-gray-500 dark:text-slate-500 font-bold">Teller</li>
                            <li>
                                <router-link :to="{ name: 'queue', params: { type: 'Teller' } }" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors" :class="{ 'bg-teal-50 dark:bg-teal-600 text-teal-600 dark:text-white': $route.params.type === 'Teller' && $route.name === 'queue' }">
                                    <i class="fa-solid fa-wallet w-6 text-center"></i>
                                    <span class="ml-3 font-medium">Manajemen Antrian</span>
                                </router-link>
                            </li>
                            <li>
                                <router-link :to="{ name: 'queue_history', params: { type: 'Teller' } }" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors" :class="{ 'bg-teal-100 dark:bg-teal-700 text-teal-700 dark:text-white': $route.params.type === 'Teller' && $route.name === 'queue_history' }">
                                    <i class="fa-solid fa-clock-rotate-left w-6 text-center"></i>
                                    <span class="ml-3 font-medium">Status Antrian</span>
                                </router-link>
                            </li>
                        </template>

                        <li class="pt-4 pb-2 px-3 text-xs uppercase text-gray-500 dark:text-slate-500 font-bold">Sistem</li>
                        <li>
                            <a href="/display" target="_blank" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-cyan-600 dark:text-cyan-400">
                                <i class="fa-solid fa-external-link-alt w-6 text-center"></i>
                                <span class="ml-3 font-medium">Buka TV Display</span>
                            </a>
                        </li>
                        
                        <!-- Admin Only Management -->
                        <template v-if="user?.role === 'admin'">
                            <li>
                                <router-link to="/admin/users" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors" :class="{ 'bg-blue-50 dark:bg-blue-600 text-blue-600 dark:text-white': $route.name === 'users' }">
                                    <i class="fa-solid fa-users-gear w-6 text-center"></i>
                                    <span class="ml-3 font-medium">Manajemen Staff</span>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/admin/settings" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors" :class="{ 'bg-purple-50 dark:bg-purple-600 text-purple-600 dark:text-white': $route.name === 'settings' }">
                                    <i class="fa-solid fa-sliders w-6 text-center"></i>
                                    <span class="ml-3 font-medium">Pengaturan</span>
                                </router-link>
                            </li>
                        </template>
                    </ul>
                </nav>

                <div class="px-4 py-3 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold shadow-lg">
                            {{ user?.name ? user.name.charAt(0) : '?' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-800 dark:text-white truncate">{{ user?.name || 'Loading...' }}</p>
                            <p class="text-[10px] text-gray-500 dark:text-slate-400 uppercase tracking-tighter">{{ user?.role || '...' }} {{ user?.counter_no ? '• LOKET ' + user.counter_no : '' }}</p>
                        </div>
                    </div>
                    <button @click="logout" class="flex w-full items-center justify-center p-2 rounded-lg bg-red-600 hover:bg-red-700 transition-colors text-xs font-bold text-white shadow-md">
                        <i class="fa-solid fa-sign-out-alt mr-2"></i> Keluar
                    </button>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
                <header class="h-16 flex items-center justify-between px-6 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 shadow-sm dark:shadow-md transition-colors duration-300">
                    <div class="text-gray-500 dark:text-slate-400 text-sm">
                        <span class="font-bold text-gray-800 dark:text-white capitalize">{{ user?.role }} Panel</span>
                        <span class="mx-2 text-gray-300">|</span>
                        <span class="text-gray-400">{{ currentPageTitle }}</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle Button -->
                         <button @click="toggleTheme" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-600 dark:text-yellow-400 transition-colors focus:outline-none" title="Ganti Tema">
                            <i class="fa-solid" :class="isDark ? 'fa-sun' : 'fa-moon'"></i>
                        </button>

                        <div class="bg-gray-100 dark:bg-slate-700 rounded-full px-4 py-1 text-sm text-gray-700 dark:text-slate-300 border border-gray-200 dark:border-slate-600">
                            <i class="fa-regular fa-clock mr-2"></i>
                            <span id="header-clock">{{ currentTime }}</span>
                        </div>
                    </div>
                </header>

                <div class="p-6">
                    <router-view v-if="user"></router-view>
                    <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 mt-20">
                         <i class="fa-solid fa-circle-notch fa-spin text-4xl mb-4"></i>
                         <p>Memuat data sesi...</p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            user: null,
            currentTime: '00:00:00',
            isDark: true
        }
    },
    computed: {
        currentPageTitle() {
            if (this.$route.name === 'dashboard') return 'Ikhtisar';
            if (this.$route.name === 'queue') return `Kelola Antrian ${this.$route.params.type}`;
            if (this.$route.name === 'users') return 'Manajemen Staff';
            if (this.$route.name === 'settings') return 'Konfigurasi Sistem';
            return 'Portal Admin';
        }
    },
    async mounted() {
        // Fetch User Profile
        await this.fetchProfile();

        // Clock
        setInterval(() => {
            this.currentTime = new Date().toLocaleTimeString('id-ID');
        }, 1000);

        // Theme logic
        const cachedTheme = localStorage.getItem('admin-theme');
        if (cachedTheme) {
            this.isDark = cachedTheme === 'dark';
        } else {
            this.isDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        this.applyTheme();
    },
    methods: {
        async fetchProfile() {
            try {
                const response = await axios.get('/admin/profile');
                this.user = response.data;
                // Store version in window for other components access if needed
                window.currentUser = this.user;
            } catch (error) {
                console.error('Failed to fetch profile', error);
                if (error.response?.status === 401) {
                    window.location.href = '/login';
                }
            }
        },
        toggleTheme() {
            this.isDark = !this.isDark;
            localStorage.setItem('admin-theme', this.isDark ? 'dark' : 'light');
            this.applyTheme();
        },
        applyTheme() {
            if (this.isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        async logout() {
            try {
                await axios.post('/logout');
                window.location.href = '/login';
            } catch (error) {
                console.error('Logout failed', error);
            }
        }
    }
}
</script>
