<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white transition-colors flex items-center gap-2">
                    Market Dashboard <span class="text-[10px] font-normal opacity-30">v5.0.2</span>
                </h2>
                <p class="text-gray-500 dark:text-slate-400 text-sm mt-1 transition-colors">Real-time transaction monitoring and analysis.</p>
            </div>
            
            <!-- Timeframe Controls -->
            <div class="mt-4 md:mt-0 flex flex-col md:flex-row gap-2">

                <!-- Timeframe -->
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-1 flex space-x-1">
                    <button 
                        @click="setFilter('daily')" 
                        :class="['px-4 py-1.5 text-xs md:text-sm font-bold rounded-md transition-colors', filter === 'daily' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700']"
                    >
                        Daily (90D)
                    </button>
                    <button 
                        @click="setFilter('weekly')" 
                        :class="['px-4 py-1.5 text-xs md:text-sm font-bold rounded-md transition-colors', filter === 'weekly' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700']"
                    >
                        Weekly (1Y)
                    </button>
                    <button 
                        @click="setFilter('monthly')" 
                        :class="['px-4 py-1.5 text-xs md:text-sm font-bold rounded-md transition-colors', filter === 'monthly' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700']"
                    >
                        Monthly (3Y)
                    </button>
                </div>

                <!-- Y-Axis Scale Controls -->
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-1 flex items-center space-x-1">
                    <span class="text-xs text-gray-500 dark:text-slate-400 px-2 font-semibold">Price Scale:</span>
                    <button @click="zoomYAxis('out')" class="p-1.5 rounded-md text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors" title="Zoom Out (Smaller Candles)">
                        <i class="fa-solid fa-minus text-xs"></i>
                    </button>
                    <button @click="resetYAxis" class="p-1.5 rounded-md text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors" title="Auto Scale">
                        <i class="fa-solid fa-rotate-right text-xs"></i>
                    </button>
                    <button @click="zoomYAxis('in')" class="p-1.5 rounded-md text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors" title="Zoom In (Bigger Candles)">
                        <i class="fa-solid fa-plus text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Ticker -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
             <!-- Total Transactions -->
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-slate-700 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-chart-line text-6xl text-blue-600"></i>
                </div>
                <p class="text-sm font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Total Volume</p>
                <div class="flex items-baseline mt-2">
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white">{{ summary.total_tx }}</h3>
                    <span class="ml-2 text-xs font-bold text-green-500 bg-green-100 dark:bg-green-900/30 px-2 py-0.5 rounded-full">TXs</span>
                </div>
            </div>

            <!-- Total Nominal -->
             <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-slate-700 shadow-sm relative overflow-hidden group col-span-2">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-sack-dollar text-6xl text-emerald-600"></i>
                </div>
                <p class="text-sm font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Total Value (Nominal)</p>
                <div class="flex items-baseline mt-2">
                    <span class="text-gray-400 dark:text-slate-500 mr-1 text-lg">Rp</span>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ formatCurrency(summary.total_nominal) }}</h3>
                </div>
            </div>

             <!-- Status (Dummy) -->
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                <div>
                     <p class="text-sm font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Market Status</p>
                     <div class="flex items-center mt-2 gap-2">
                        <span class="relative flex h-3 w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        <span class="font-bold text-green-600 dark:text-green-400">OPEN</span>
                     </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Main Candle/Line Chart -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 relative min-h-[400px]">
                <div class="flex justify-between items-center mb-4">
                     <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i :class="['fa-solid', chartType === 'candlestick' ? 'fa-candlestick-chart text-orange-500' : 'fa-chart-area text-blue-500']"></i> 
                        {{ chartType === 'candlestick' ? 'Transaction Value Flow' : 'Nominal Trend' }}
                     </h3>
                     <span class="text-xs font-mono text-gray-400">{{ filter.toUpperCase() }} INTERVAL</span>
                </div>
                
                <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-slate-800/50 z-10 backdrop-blur-sm rounded-xl">
                    <i class="fa-solid fa-circle-notch fa-spin text-3xl text-blue-600"></i>
                </div>

                <div class="h-[350px] w-full">
                     <apexchart 
                        v-if="chartSeries.length > 0"
                        type="area" 
                        height="100%" 
                        :options="chartOptions" 
                        :series="chartSeries" 
                    ></apexchart>
                     <div v-else-if="!loading" class="h-full flex items-center justify-center text-gray-400">
                        No data available for this period.
                    </div>
                </div>
            </div>

            <!-- Volume Chart -->
             <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 relative min-h-[400px]">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-chart-area text-green-500"></i> 
                        Total Transaction Value
                    </h3>
                     <span class="text-xs font-mono text-gray-400">{{ filter.toUpperCase() }} TIMEFRAME</span>
                </div>

                <div class="h-[350px] w-full">
                     <apexchart 
                        v-if="volumeSeries.length > 0"
                        type="bar" 
                        height="100%" 
                        :options="volumeOptions" 
                        :series="volumeSeries"
                    ></apexchart>
                </div>
            </div>
        </div>

        <!-- Customer Satisfaction (IKM) Section (V5 ultra-compact) -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-stretch">
            <!-- Col 1: Master Score (25%) -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-3xl p-6 shadow-xl text-white relative overflow-hidden h-[500px] flex flex-col justify-center">
                    <div class="absolute -right-6 -top-6 opacity-10 rotate-12">
                        <i class="fa-solid fa-star text-[10rem]"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[10px] font-black opacity-70 mb-6 flex items-center gap-2 tracking-[0.2em] uppercase">
                            <i class="fa-solid fa-face-smile-beam"></i> Indeks Kepuasan
                        </h3>
                        <div class="flex items-baseline gap-2">
                            <div class="text-6xl font-black tracking-tighter">{{ surveyStats.average_rating || '0.0' }}</div>
                            <div class="text-lg font-bold opacity-40">/ 5.0</div>
                        </div>
                        <div class="flex text-yellow-500 gap-1 mt-3 mb-6">
                            <i v-for="i in 5" :key="i" :class="['fa-solid fa-star text-[12px]', i <= Math.round(surveyStats.average_rating) ? '' : 'opacity-20']"></i>
                        </div>
                        <div class="pt-4 border-t border-white/10 flex flex-col gap-0.5">
                            <span class="text-[9px] font-bold opacity-50 uppercase tracking-widest">Total Responden</span>
                            <span class="text-xl font-black">{{ surveyStats.total_surveys }}</span>
                        </div>
                    </div>
                    <div class="mt-auto opacity-20 text-[8px] font-black tracking-widest uppercase border-t border-white/5 pt-3">Real-time Monitoring</div>
                </div>
            </div>

            <!-- Col 2: Stacked Metrics (25%) -->
            <div class="lg:col-span-1 flex flex-col gap-6 h-[500px]">
                <!-- Sebaran Penilaian -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-gray-100 dark:border-slate-700 shadow-sm flex-1 flex flex-col justify-center">
                    <h3 class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-[0.15em] mb-4">Sebaran Penilaian</h3>
                    <div class="space-y-3">
                        <div v-for="r in [5,4,3,2,1]" :key="r">
                            <div class="flex justify-between items-center text-[9px] mb-1.5 font-black">
                                <span class="text-gray-600 dark:text-slate-400 flex items-center gap-2">
                                    <span :class="['w-1.5 h-1.5 rounded-full', r==5?'bg-emerald-500':(r==4?'bg-teal-500':(r==3?'bg-blue-500':(r==2?'bg-orange-500':'bg-rose-500')))]"></span>
                                    {{ r == 5 ? 'Sangat Puas' : (r == 4 ? 'Puas' : (r == 3 ? 'Cukup' : (r == 2 ? 'Tidak Puas' : 'Sgt Tdk Puas'))) }}
                                </span>
                                <span class="text-gray-400 font-black tabular-nums">{{ surveyStats.distribution[r] || 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-slate-900 h-1.5 rounded-full overflow-hidden">
                                <div class="h-full transition-all duration-1000"
                                    :class="r==5?'bg-emerald-500':(r==4?'bg-teal-500':(r==3?'bg-blue-500':(r==2?'bg-orange-500':'bg-rose-500')))"
                                    :style="{ width: surveyStats.total_surveys > 0 ? ((surveyStats.distribution[r] || 0) / surveyStats.total_surveys * 100) + '%' : '0%' }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Peringkat Staff (Compact) -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-gray-100 dark:border-slate-700 shadow-sm flex-1 overflow-hidden">
                    <h3 class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-[0.15em] mb-4">Peringkat Staff</h3>
                    <div class="space-y-3 overflow-y-auto max-h-[140px] pr-2 custom-scrollbar">
                        <div v-for="(staff, index) in surveyStats.staff_ranking" :key="index" class="flex items-center justify-between group">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5 h-5 rounded-md flex items-center justify-center text-[9px] font-black"
                                    :class="index==0?'bg-yellow-500 text-white':(index==1?'bg-slate-300 text-slate-700':(index==2?'bg-orange-400 text-white':'bg-slate-100 dark:bg-slate-900 text-slate-400'))">
                                    {{ index + 1 }}
                                </div>
                                <div class="max-w-[70px] truncate">
                                    <div class="text-[9px] font-black text-gray-800 dark:text-white truncate uppercase">{{ staff.name }}</div>
                                    <div class="text-[7px] uppercase text-gray-400 font-bold tracking-tighter">{{ staff.role }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] font-black text-blue-600 dark:text-blue-400 leading-tight">{{ staff.avg_rating }}</div>
                                <div class="text-[6px] text-gray-400 font-bold uppercase">{{ staff.total_surveys }} Srv</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Col 3-4: Activity Log (50%) -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-3xl border border-gray-100 dark:border-slate-700 shadow-sm flex flex-col h-[500px] overflow-hidden">
                <div class="p-6 pb-3">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 border border-blue-100 dark:border-blue-800/50">
                                <i class="fa-solid fa-bolt-lightning text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-md font-black text-gray-800 dark:text-white tracking-tight">Log Aktivitas</h3>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Update Real-time Terakhir</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800/50">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                            <span class="text-[8px] font-black text-green-600 uppercase tracking-widest text-[9px]">LIVE</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto px-6 pb-6 custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div v-for="fb in surveyStats.recent_feedback" :key="fb.id" 
                            class="p-3.5 bg-gray-50/50 dark:bg-slate-900/40 rounded-2xl border border-gray-100 dark:border-slate-800/50 hover:border-blue-500/40 transition-all duration-300 group">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xl shadow-sm z-10"
                                    :class="{
                                        'bg-emerald-500 text-white': fb.rating == 5,
                                        'bg-teal-500 text-white': fb.rating == 4,
                                        'bg-blue-500 text-white': fb.rating == 3,
                                        'bg-orange-500 text-white': fb.rating == 2,
                                        'bg-rose-500 text-white': fb.rating == 1
                                    }">
                                    {{ fb.rating }}
                                </div>
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-0.5">
                                        <span class="text-[9px] font-black text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/50 px-1.5 py-0.5 rounded-lg">
                                            #{{ fb.queue_no }}
                                        </span>
                                        <div class="flex text-yellow-500 text-[7px] gap-0.5">
                                            <i v-for="i in 5" :key="i" :class="['fa-solid fa-star', i <= fb.rating ? '' : 'opacity-20']"></i>
                                        </div>
                                    </div>
                                    <div class="text-[11px] font-black text-gray-800 dark:text-slate-100 truncate uppercase tracking-tight">{{ fb.staff_name }}</div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center text-[8px] text-gray-400 font-black border-t border-gray-100 dark:border-slate-800/50 pt-2.5 uppercase tracking-wider">
                                <span class="flex items-center gap-1.5"><i class="fa-regular fa-calendar-alt opacity-40"></i> {{ fb.date }}</span>
                                <span class="flex items-center gap-1.5"><i class="fa-regular fa-clock opacity-40"></i> {{ fb.time }}</span>
                            </div>
                        </div>

                        <div v-if="surveyStats.recent_feedback.length === 0" class="col-span-full h-96 flex flex-col items-center justify-center text-gray-400">
                            <i class="fa-solid fa-wind text-4xl mb-4 opacity-10"></i>
                            <p class="text-sm font-bold uppercase tracking-widest opacity-30">Belum ada aktivitas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import VueApexCharts from 'vue3-apexcharts';

export default {
    components: {
        apexchart: VueApexCharts,
    },
    data() {
        return {
            filter: 'daily',
            loading: false,
            error: null,
            summary: {
                total_tx: 0,
                total_nominal: 0
            },
            surveyStats: {
                total_surveys: 0,
                average_rating: 0,
                distribution: { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 },
                recent_feedback: [],
                staff_ranking: []
            },
            currentTime: '',
            yAxisMin: null, // Manual Y-axis minimum
            yAxisMax: null, // Manual Y-axis maximum
            yAxisZoomFactor: 1, // Zoom multiplier
            
            // Chart Data (Area chart showing total nominal)
            chartSeries: [],
            chartOptions: {
                chart: {
                    type: 'area', 
                    height: 350,
                    zoom: {
                        enabled: true,
                        type: 'xy', // Enable both X and Y zoom
                        autoScaleYaxis: true
                    },
                    toolbar: { 
                        show: true,
                        tools: {
                            download: false,
                            selection: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true
                        },
                        autoSelected: 'zoom' 
                    },
                    background: 'transparent'
                },
                plotOptions: {},
                colors: ['#10B981'], // Green gradient
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                xaxis: {
                    type: 'datetime',
                     labels: { style: { colors: '#94a3b8' } },
                     axisBorder: { show: false },
                     axisTicks: { show: false }
                },
                yaxis: {
                    tooltip: { enabled: true },
                    labels: { 
                        show: true, // Enable Y-axis labels
                        style: { colors: '#94a3b8', fontSize: '11px' },
                        formatter: (value) => {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                            }
                            return 'Rp ' + value;
                        }
                    },
                    decimalsInFloat: 0
                },
                grid: {
                    borderColor: '#334155',
                    strokeDashArray: 4,
                },
                dataLabels: { enabled: false },
                theme: { mode: 'dark' },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function (val) {
                            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(val);
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                        stops: [0, 90, 100]
                    }
                }
            },

            // Volume Data
            volumeSeries: [],
            volumeOptions: {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: false },
                    background: 'transparent'
                },
                plotOptions: {
                    bar: {
                        columnWidth: '50%',
                        borderRadius: 4,
                    }
                },
                colors: ['#3b82f6'],
                xaxis: {
                    type: 'datetime',
                    labels: { style: { colors: '#94a3b8' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: { 
                        show: true,
                        style: { colors: '#94a3b8', fontSize: '11px' }
                    }
                },
                grid: {
                    borderColor: '#334155',
                    strokeDashArray: 4,
                },
                dataLabels: { enabled: false },
                theme: { mode: 'dark' },
                tooltip: { theme: 'dark' }
            }
        }
    },
    mounted() {
        this.fetchData();
        this.updateTheme(); 
        
        // Listen for Theme Changes (MutationObserver)
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    this.updateTheme();
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });

        setInterval(() => {
            this.currentTime = new Date().toLocaleTimeString();
        }, 1000);
    },
    methods: {
        async fetchData() {
            this.loading = true;
            this.error = null;
            try {
                // Fetch Market Data
                const chartRes = await axios.get('/admin/chart-data', {
                    params: { filter: this.filter }
                });

                const data = chartRes.data.candles;
                this.summary = chartRes.data.summary;
                
                // Fetch Survey Data
                await this.fetchSurveyStats();
                
                // Update Main Chart with total nominal
                if (data && data.length > 0) {
                    const chartData = data.map(item => ({
                        x: new Date(item.x).getTime(),
                        y: item.y // Total nominal
                    }));

                    this.chartSeries = [{
                        name: 'Total Nominal',
                        data: chartData
                    }];
                    
                    // Update Volume Chart
                    const volumeData = data.map(item => ({
                        x: new Date(item.x).getTime(),
                        y: item.v // Volume count
                    }));

                    this.volumeSeries = [{
                        name: 'Transaction Count',
                        data: volumeData
                    }];
                }

            } catch (error) {
                console.error('Failed to fetch chart data:', error);
                this.error = 'Failed to load chart data. Please try again.';
                this.chartSeries = [];
                this.volumeSeries = [];
            } finally {
                this.loading = false;
            }
        },
        async fetchSurveyStats() {
            try {
                const res = await axios.get('/admin/survey-stats');
                this.surveyStats = res.data;
            } catch (err) {
                console.error("Survey Stats Fetch Error:", err);
            }
        },
        setFilter(val) {
            this.filter = val;
            this.fetchData();
        },
        formatCurrency(val) {
            return new Intl.NumberFormat('id-ID').format(val);
        },
        updateTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            const themeMode = isDark ? 'dark' : 'light';
            const gridColor = isDark ? '#334155' : '#e2e8f0';
            const labelColor = isDark ? '#94a3b8' : '#64748b';

            // Update Chart Options Reactively
            this.chartOptions = {
                ...this.chartOptions,
                theme: { mode: themeMode },
                tooltip: { theme: themeMode },
                grid: { borderColor: gridColor },
                xaxis: { ...this.chartOptions.xaxis, labels: { style: { colors: labelColor } } },
                yaxis: { ...this.chartOptions.yaxis, labels: { ...this.chartOptions.yaxis.labels, style: { colors: labelColor, fontSize: '11px' } } }
            };

            this.volumeOptions = {
                ...this.volumeOptions,
                theme: { mode: themeMode },
                tooltip: { ...this.volumeOptions.tooltip, theme: themeMode },
                grid: { borderColor: gridColor },
                xaxis: { ...this.volumeOptions.xaxis, labels: { style: { colors: labelColor } } },
                yaxis: { ...this.volumeOptions.yaxis, labels: { show: false } }
            };
        },
        zoomYAxis(direction) {
            if (!this.chartSeries || this.chartSeries.length === 0) return;

            // Calculate current min/max from chart data
            const allValues = this.chartSeries[0].data.map(d => d.y);
            const dataMin = Math.min(...allValues);
            const dataMax = Math.max(...allValues);
            const range = dataMax - dataMin;

            if (direction === 'in') {
                // Zoom in: Reduce visible range by 20%
                this.yAxisZoomFactor *= 0.8;
            } else if (direction === 'out') {
                // Zoom out: Increase visible range by 25%
                this.yAxisZoomFactor *= 1.25;
            }

            // Apply zoom factor to range
            const zoomedRange = range * this.yAxisZoomFactor;
            const center = (dataMin + dataMax) / 2;

            this.yAxisMin = Math.floor(center - zoomedRange / 2);
            this.yAxisMax = Math.ceil(center + zoomedRange / 2);

            // Update chart options
            this.chartOptions = {
                ...this.chartOptions,
                yaxis: {
                    ...this.chartOptions.yaxis,
                    min: this.yAxisMin,
                    max: this.yAxisMax
                }
            };
        },
        resetYAxis() {
            // Reset to auto-scale
            this.yAxisMin = null;
            this.yAxisMax = null;
            this.yAxisZoomFactor = 1;

            this.chartOptions = {
                ...this.chartOptions,
                yaxis: {
                    ...this.chartOptions.yaxis,
                    min: undefined,
                    max: undefined
                }
            };
        }
    }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #475569;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
