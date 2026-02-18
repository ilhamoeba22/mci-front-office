<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white transition-colors">Market Dashboard</h2>
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

        <!-- Customer Satisfaction (IKM) Section -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- IKM Score Card -->
            <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl p-6 shadow-xl text-white relative overflow-hidden">
                <div class="absolute -right-4 -top-4 opacity-10">
                    <i class="fa-solid fa-star text-9xl"></i>
                </div>
                <h3 class="text-lg font-bold opacity-80 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-face-smile"></i> Indeks Kepuasan
                </h3>
                <div class="flex items-center gap-4">
                    <div class="text-6xl font-black">{{ surveyStats.average_rating }}</div>
                    <div>
                        <div class="flex text-yellow-400 gap-1 mb-1">
                            <i v-for="i in 4" :key="i" :class="['fa-solid fa-star', i <= Math.round(surveyStats.average_rating) ? '' : 'opacity-30']"></i>
                        </div>
                        <p class="text-xs font-bold opacity-70 uppercase tracking-widest">Skala 4.0</p>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t border-white/10 flex justify-between items-center">
                    <span class="text-sm opacity-80">Total Responden</span>
                    <span class="text-xl font-bold">{{ surveyStats.total_surveys }}</span>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-200 dark:border-slate-700 shadow-sm flex flex-col">
                <h3 class="text-sm font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-6">Sebaran Penilaian</h3>
                <div class="space-y-4 flex-grow">
                    <!-- Iterasi Rating (Sangat Puas to Tidak Puas) -->
                    <div v-for="(count, r) in surveyStats.distribution" :key="r" class="group">
                        <div class="flex justify-between items-center text-xs mb-1.5">
                            <span class="font-bold text-gray-700 dark:text-slate-300 flex items-center gap-2">
                                <span v-if="r == 4" class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span v-if="r == 3" class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <span v-if="r == 2" class="w-2 h-2 rounded-full bg-orange-500"></span>
                                <span v-if="r == 1" class="w-2 h-2 rounded-full bg-rose-500"></span>
                                {{ r == 4 ? 'Sangat Puas' : (r == 3 ? 'Puas' : (r == 2 ? 'Cukup' : 'Tidak Puas')) }}
                            </span>
                            <span class="text-gray-400 tabular-nums">{{ count }} Nasabah</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                            <div 
                                class="h-full transition-all duration-1000"
                                :class="{
                                    'bg-emerald-500': r == 4,
                                    'bg-blue-500': r == 3,
                                    'bg-orange-500': r == 2,
                                    'bg-rose-500': r == 1
                                }"
                                :style="{ width: surveyStats.total_surveys > 0 ? (count / surveyStats.total_surveys * 100) + '%' : '0%' }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Feedback -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-200 dark:border-slate-700 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Suara Nasabah Terbaru</h3>
                    <i class="fa-solid fa-comments text-blue-500 opacity-30"></i>
                </div>
                
                <div class="space-y-4 max-h-[250px] overflow-y-auto pr-2 custom-scrollbar">
                    <div v-for="fb in surveyStats.recent_feedback" :key="fb.id" class="p-4 bg-gray-50 dark:bg-slate-900/50 rounded-xl border border-gray-100 dark:border-slate-800 transition-all hover:border-blue-500/30 group">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span 
                                    class="text-xs font-bold px-2 py-0.5 rounded-full"
                                    :class="{
                                        'bg-emerald-100 text-emerald-600': fb.rating >= 4,
                                        'bg-blue-100 text-blue-600': fb.rating == 3,
                                        'bg-orange-100 text-orange-600': fb.rating == 2,
                                        'bg-rose-100 text-rose-600': fb.rating == 1
                                    }"
                                >
                                    {{ fb.rating }} Star
                                </span>
                                <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">{{ fb.staff_name }}</span>
                            </div>
                            <span class="text-[10px] text-gray-400 italic">{{ fb.time }}</span>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">
                            {{ fb.comment || '(Tanpa komentar)' }}
                        </p>
                    </div>

                    <div v-if="surveyStats.recent_feedback.length === 0" class="h-full flex items-center justify-center py-12 text-gray-400 text-sm italic">
                        Belum ada feedback nasabah.
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
                distribution: { 1: 0, 2: 0, 3: 0, 4: 0 },
                recent_feedback: []
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
