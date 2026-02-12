<template>
    <div :class="[theme, 'min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-white transition-colors duration-500 font-outfit']">
        
        <!-- Background Gradient Mesh -->
        <div class="fixed inset-0 z-0 opacity-30 dark:opacity-100 transition-opacity duration-1000">
            <div class="absolute top-0 right-0 w-[50vw] h-[50vw] bg-indigo-600/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 left-0 w-[40vw] h-[40vw] bg-emerald-600/10 rounded-full blur-[100px]"></div>
        </div>

        <div class="flex flex-col h-screen w-full relative z-10">
            
            <!-- HEADER -->
            <div class="h-20 flex items-center justify-between px-10 border-b border-slate-200 dark:border-white/10 bg-white/40 dark:bg-slate-900/60 backdrop-blur-md shadow-sm dark:shadow-lg transition-colors duration-500">
                <div class="flex items-center">
                    <img src="/img/logo_mci.png" alt="Logo" class="h-14 w-auto object-contain drop-shadow-md dark:drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]">
                </div>
                <!-- Clock -->
                <div class="text-right">
                    <div class="text-3xl font-mono font-bold text-slate-700 dark:text-slate-200 tracking-[0.1em] drop-shadow-sm dark:drop-shadow-md transition-colors">
                        {{ currentTime }}
                    </div>
                    <div class="text-sm text-slate-500 dark:text-slate-400 uppercase font-semibold tracking-[0.15em] transition-colors">
                        {{ currentDate }}
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <div class="flex-1 flex overflow-hidden">
                
                <!-- LEFT: Queue Cards (30%) -->
                <div class="w-[30%] h-full flex flex-col p-6 gap-6 justify-center relative">
                    
                    <!-- CS Card -->
                    <div class="relative group flex-1">
                        <div class="absolute -inset-1 bg-gradient-to-r from-yellow-400 to-amber-400 dark:from-yellow-600 dark:to-amber-600 rounded-[2rem] blur opacity-20 dark:opacity-30 group-hover:opacity-50 dark:group-hover:opacity-60 transition duration-1000"></div>
                        <div class="relative h-full bg-white/60 dark:bg-slate-900/90 rounded-[2rem] p-6 border border-slate-200 dark:border-white/10 shadow-lg dark:shadow-xl flex flex-col justify-between overflow-hidden transition-colors duration-500 backdrop-blur-md">
                            
                            <!-- Header -->
                            <div class="flex justify-between items-center z-10">
                                <span class="px-4 py-1.5 rounded-full bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 text-sm font-bold tracking-[0.15em] uppercase border border-yellow-200 dark:border-yellow-500/20 shadow-sm dark:shadow-[0_0_10px_rgba(234,179,8,0.2)]">
                                    Customer Service
                                </span>
                                <div class="w-3 h-3 rounded-full bg-yellow-500 animate-pulse shadow-[0_0_15px_rgba(234,179,8,1)]"></div>
                            </div>

                            <!-- Number -->
                            <div class="flex-1 flex flex-col items-center justify-center z-10">
                                <span :class="['text-6xl xl:text-7xl 2xl:text-8xl leading-none font-black tracking-tighter transition-all duration-300 drop-shadow-xl dark:drop-shadow-2xl whitespace-nowrap', highlightCS ? (theme === 'dark' ? 'text-yellow-300 scale-110' : 'text-yellow-600 scale-110') : 'text-slate-800 dark:text-white scale-100']">
                                    {{ queueData.cs_antri }}
                                </span>
                                <span class="text-slate-400 dark:text-slate-500 text-xs font-light tracking-[0.3em] uppercase mt-2">NOMOR ANTRIAN</span>
                            </div>

                            <!-- Status -->
                            <div class="z-10 text-center">
                                <span class="text-yellow-700 dark:text-yellow-500/80 text-sm font-medium flex items-center justify-center gap-2 bg-yellow-100/50 dark:bg-yellow-500/5 py-1.5 rounded-lg border border-yellow-200 dark:border-yellow-500/10">
                                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Sedang Melayani
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Teller Card -->
                    <div class="relative group flex-1">
                        <div class="absolute -inset-1 bg-gradient-to-r from-emerald-400 to-teal-400 dark:from-emerald-600 dark:to-teal-600 rounded-[2rem] blur opacity-20 dark:opacity-30 group-hover:opacity-50 dark:group-hover:opacity-60 transition duration-1000"></div>
                        <div class="relative h-full bg-white/60 dark:bg-slate-900/90 rounded-[2rem] p-6 border border-slate-200 dark:border-white/10 shadow-lg dark:shadow-xl flex flex-col justify-between overflow-hidden transition-colors duration-500 backdrop-blur-md">
                            
                            <!-- Header -->
                            <div class="flex justify-between items-center z-10">
                                <span class="px-4 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-sm font-bold tracking-[0.15em] uppercase border border-emerald-200 dark:border-emerald-500/20 shadow-sm dark:shadow-[0_0_10px_rgba(16,185,129,0.2)]">
                                    Teller
                                </span>
                                <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_15px_rgba(16,185,129,1)]"></div>
                            </div>

                            <!-- Number -->
                            <div class="flex-1 flex flex-col items-center justify-center z-10">
                                <span :class="['text-6xl xl:text-7xl 2xl:text-8xl leading-none font-black tracking-tighter transition-all duration-300 drop-shadow-xl dark:drop-shadow-2xl whitespace-nowrap', highlightTL ? (theme === 'dark' ? 'text-yellow-300 scale-110' : 'text-yellow-600 scale-110') : 'text-slate-800 dark:text-white scale-100']">
                                    {{ queueData.tl_antri }}
                                </span>
                                <span class="text-slate-400 dark:text-slate-500 text-xs font-light tracking-[0.3em] uppercase mt-2">NOMOR ANTRIAN</span>
                            </div>

                            <!-- Status -->
                            <div class="z-10 text-center">
                                <span class="text-emerald-700 dark:text-emerald-500/80 text-sm font-medium flex items-center justify-center gap-2 bg-emerald-100/50 dark:bg-emerald-500/5 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-500/10">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Sedang Melayani
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT: Video/Media -->
                <div class="flex-1 relative bg-slate-100 dark:bg-black flex items-center justify-center overflow-hidden transition-colors duration-500">
                    <template v-if="mediaSource === 'youtube' && embedUrl">
                        <iframe 
                            class="w-full h-full"
                            :src="embedUrl" 
                            title="YouTube video player" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen>
                        </iframe>
                    </template>
                    <template v-else>
                        <video autoplay loop playsinline class="w-full h-full object-cover opacity-100 dark:opacity-90">
                            <source :src="`/assets/media/${videoFile}`" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </template>
                    
                    <!-- Gradients -->
                    <div class="absolute inset-0 bg-gradient-to-l from-transparent via-transparent to-slate-50/80 dark:to-slate-950/80 w-32 pointer-events-none transition-colors duration-500"></div>
                </div>

            </div>

            <!-- FOOTER: Running Text -->
            <div class="h-12 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-white/10 flex items-center shadow-[0_-5px_30px_rgba(0,0,0,0.1)] dark:shadow-[0_-5px_30px_rgba(0,0,0,0.6)] relative overflow-hidden z-30 transition-colors duration-500">
                <!-- Fade masks -->
                <div class="absolute left-0 top-0 bottom-0 w-24 bg-gradient-to-r from-slate-50 dark:from-slate-950 to-transparent z-10 transition-colors duration-500"></div>
                <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-slate-50 dark:from-slate-950 to-transparent z-10 transition-colors duration-500"></div>
                
                <div class="whitespace-nowrap w-full overflow-hidden flex items-center">
                    <div class="animate-marquee inline-block text-xl font-medium tracking-wide text-slate-700 dark:text-slate-200 transition-colors">
                        {{ runningText }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        initialVideoFile: { type: String, default: 'default.mp4' },
        initialRunningText: { type: String, default: 'Welcome' },
        initialMediaSource: { type: String, default: 'local' },
        initialEmbedUrl: { type: String, default: '' },
    },
    data() {
        return {
            videoFile: this.initialVideoFile,
            runningText: this.initialRunningText,
            mediaSource: this.initialMediaSource,
            embedUrl: this.initialEmbedUrl,
            
            queueData: {
                cs_antri: '...',
                tl_antri: '...'
            },
            currentTime: '',
            currentDate: '',
            theme: 'light',
            
            highlightCS: false,
            highlightTL: false,
            
            intervals: []
        }
    },
    mounted() {
        this.updateClock();
        this.updateQueue();
        this.initThemeSync();
        
        // Start Intervals
        this.intervals.push(setInterval(this.updateClock, 1000));
        this.intervals.push(setInterval(this.updateQueue, 2000));
        
        // Poll for theme changes (backup for storage event)
        this.intervals.push(setInterval(this.checkTheme_poll, 1000));
    },
    beforeUnmount() {
        this.intervals.forEach(clearInterval);
        window.removeEventListener('storage', this.handleStorageEvent);
    },
    methods: {
        updateClock() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('en-GB', { hour12: false });
            this.currentDate = now.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        },
        async updateQueue() {
            try {
                const response = await axios.get('/api/queue-data');
                const data = response.data;
                
                if (data.cs_antri !== this.queueData.cs_antri && this.queueData.cs_antri !== '...') {
                    this.highlightCS = true;
                    setTimeout(() => this.highlightCS = false, 500);
                }
                if (data.tl_antri !== this.queueData.tl_antri && this.queueData.tl_antri !== '...') {
                    this.highlightTL = true;
                    setTimeout(() => this.highlightTL = false, 500);
                }
                
                this.queueData = data;
            } catch (error) {
                console.error('Queue fetch error:', error);
            }
        },
        initThemeSync() {
            // Initial check
            this.checkTheme();
            
            // Listen for changes in other tabs
            window.addEventListener('storage', this.handleStorageEvent);
        },
        handleStorageEvent(e) {
            if (e.key === 'admin-theme') {
                this.checkTheme();
            }
        },
        checkTheme() {
            if (localStorage['admin-theme'] === 'dark' || (!('admin-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                this.theme = 'dark';
                document.documentElement.classList.add('dark');
            } else {
                this.theme = 'light';
                document.documentElement.classList.remove('dark');
            }
        },
        checkTheme_poll() {
             // Polling useful if storage event not firing on same page actions if logic split
             this.checkTheme();
        }
    }
}
</script>

<style scoped>
.font-outfit {
    font-family: 'Outfit', sans-serif;
}
.animate-marquee {
    animation: marquee 15s linear infinite;
}
@keyframes marquee {
    0% { transform: translateX(100vw); }
    100% { transform: translateX(-100%); }
}
/* No manual glass classes needed using Tailwind opacity utilities, but can add if specific needed */
</style>
