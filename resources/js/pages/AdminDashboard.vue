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
                const response = await axios.get('/admin/chart-data', {
                    params: { filter: this.filter }
                });

                const data = response.data.candles;
                this.summary = response.data.summary;
                
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
