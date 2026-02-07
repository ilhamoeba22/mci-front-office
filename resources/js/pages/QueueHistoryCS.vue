<template>
    <div>
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white transition-colors">
                Riwayat Antrian <span class="text-blue-600 dark:text-blue-400">{{ type }}</span>
            </h2>
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                 <select v-if="type === 'Teller'" v-model="filters.tx_type" class="bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 focus:outline-none focus:border-blue-500 text-sm transition-colors">
                    <option value="">Semua Transaksi</option>
                    <option value="Setor Tunai">Setor Tunai</option>
                    <option value="Tarik Tunai">Tarik Tunai</option>
                    <option value="Transfer">Transfer</option>
                 </select>
                 <input v-model="filters.date" type="date" class="bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 focus:outline-none focus:border-blue-500 text-sm transition-colors">
                 <input v-model="filters.search" type="text" placeholder="Cari nama/antrian..." class="bg-white dark:bg-slate-700 text-gray-900 dark:text-white px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 focus:outline-none focus:border-blue-500 text-sm transition-colors">
            </div>
        </div>

        <!-- Summary Cards (Compact) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-gray-200 dark:border-slate-700 flex items-center justify-between shadow-sm dark:shadow-lg transition-colors">
                <div>
                    <span class="text-gray-500 dark:text-slate-400 text-[10px] uppercase font-bold tracking-wider">Total Antrian</span>
                    <p class="text-2xl font-black text-gray-800 dark:text-white transition-colors">{{ pagination.total || 0 }}</p>
                </div>
                <div class="bg-blue-500/10 p-2.5 rounded-lg text-blue-500 dark:text-blue-400"><i class="fa-solid fa-list-ol text-lg"></i></div>
            </div>
             <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-gray-200 dark:border-slate-700 flex items-center justify-between shadow-sm dark:shadow-lg transition-colors">
                <div>
                    <span class="text-gray-500 dark:text-slate-400 text-[10px] uppercase font-bold tracking-wider">Selesai</span>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 transition-colors">{{ stats.completed }}</p>
                </div>
                <div class="bg-emerald-500/10 p-2.5 rounded-lg text-emerald-600 dark:text-emerald-400"><i class="fa-solid fa-check-circle text-lg"></i></div>
            </div>
             <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-gray-200 dark:border-slate-700 flex items-center justify-between shadow-sm dark:shadow-lg transition-colors">
                <div>
                    <span class="text-gray-500 dark:text-slate-400 text-[10px] uppercase font-bold tracking-wider">Dilewati</span>
                    <p class="text-2xl font-black text-rose-600 dark:text-rose-400 transition-colors">{{ stats.skipped }}</p>
                </div>
                <div class="bg-rose-500/10 p-2.5 rounded-lg text-rose-600 dark:text-rose-400"><i class="fa-solid fa-ban text-lg"></i></div>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-gray-200 dark:border-slate-700 flex items-center justify-between shadow-sm dark:shadow-lg transition-colors">
                <div>
                    <span class="text-gray-500 dark:text-slate-400 text-[10px] uppercase font-bold tracking-wider">Export</span>
                    <button @click="downloadExcel" class="text-xs bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-white px-3 py-1.5 rounded mt-1 transition-colors border border-gray-300 dark:border-slate-600">
                         <i class="fa-solid fa-file-excel mr-1"></i> Excel
                    </button>
                </div>
                <div class="bg-slate-100 dark:bg-slate-700/50 p-2.5 rounded-lg text-gray-500 dark:text-slate-400"><i class="fa-solid fa-download text-lg"></i></div>
            </div>
        </div>

        <!-- Modern Table -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden shadow-sm dark:shadow-2xl relative transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-slate-400">
                    <thead class="bg-gray-50 dark:bg-slate-900/80 text-gray-500 dark:text-slate-300 uppercase text-[10px] font-bold tracking-wider backdrop-blur-sm sticky top-0 z-10 border-b border-gray-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">No. Antrian</th>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Nasabah</th>
                            <th class="px-6 py-4">Transaksi</th>
                            <th class="px-6 py-4 text-right">Nominal</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700/50 text-xs sm:text-sm">
                        <tr v-if="loading">
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-blue-500 mb-3"></i>
                                <p class="text-gray-500 dark:text-slate-500 animate-pulse">Memuat data...</p>
                            </td>
                        </tr>
                        <tr v-else-if="queues.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center italic text-gray-500 dark:text-slate-500">
                                <i class="fa-regular fa-folder-open text-4xl mb-2 opacity-50"></i>
                                <p>Tidak ada data antrian ditemukan.</p>
                            </td>
                        </tr>
                        <tr v-else v-for="q in queues" :key="q.id_antrian" class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-all duration-150 group">
                            <!-- No Antrian -->
                            <td class="px-6 py-4">
                                <span class="font-black text-gray-800 dark:text-white text-lg tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ q.antrian }}</span>
                            </td>
                            
                            <!-- Waktu -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-gray-700 dark:text-slate-300 font-medium">{{ formatDate(q.tgl_antri) }}</span>
                                    <span class="text-[10px] text-gray-500 dark:text-slate-500 font-mono">{{ formatTime(q.updated_at) }}</span>
                                </div>
                            </td>
                            
                            <!-- Nasabah -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col max-w-[150px]">
                                    <span class="text-gray-800 dark:text-white font-bold truncate">{{ q.nama || (q.transaction ? q.transaction.nama : '-') }}</span>
                                    <span class="text-[10px] text-gray-500 dark:text-slate-500 font-mono truncate">{{ q.no_kontak || '' }}</span>
                                </div>
                            </td>
                            
                            <!-- Tipe Transaksi -->
                            <td class="px-6 py-4">
                                <div v-if="q.tx_type" class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs shadow-sm"
                                        :class="{
                                            'bg-emerald-500': q.tx_type === 'Setor Tunai',
                                            'bg-rose-500': q.tx_type === 'Tarik Tunai',
                                            'bg-indigo-500': q.tx_type === 'Transfer',
                                            'bg-slate-600': q.tx_type === 'Layanan CS'
                                        }">
                                        <i class="fa-solid" :class="{
                                            'fa-arrow-down': q.tx_type === 'Setor Tunai',
                                            'fa-arrow-up': q.tx_type === 'Tarik Tunai',
                                            'fa-paper-plane': q.tx_type === 'Transfer',
                                            'fa-headset': q.tx_type === 'Layanan CS'
                                        }"></i>
                                    </div>
                                    <span class="text-gray-700 dark:text-slate-300 font-medium text-xs">{{ q.tx_type }}</span>
                                </div>
                                <span v-else class="text-gray-500 dark:text-slate-500 italic">-</span>
                            </td>
                            
                            <!-- Nominal (Right Aligned) -->
                            <td class="px-6 py-4 text-right">
                                <span v-if="q.transaction?.nominal" class="font-mono font-bold text-gray-700 dark:text-slate-200">
                                    Rp {{ Number(q.transaction.nominal).toLocaleString('id-ID') }}
                                </span>
                                <span v-else class="text-gray-500 dark:text-slate-600 text-xs">-</span>
                            </td>
                            
                            <!-- Status -->
                            <td class="px-6 py-4 text-center">
                                <span v-if="q.st_antrian == 3" class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/10 px-2.5 py-1 rounded-full text-[10px] font-bold border border-emerald-200 dark:border-emerald-500/20 uppercase tracking-wide">
                                    <i class="fa-solid fa-check"></i> Selesai
                                </span>
                                <span v-else class="inline-flex items-center gap-1.5 text-rose-600 dark:text-rose-400 bg-rose-100 dark:bg-rose-500/10 px-2.5 py-1 rounded-full text-[10px] font-bold border border-rose-200 dark:border-rose-500/20 uppercase tracking-wide">
                                     <i class="fa-solid fa-forward"></i> Dilewati
                                </span>
                            </td>
                            
                            <!-- Aksi -->
                            <td class="px-6 py-4 text-center relative">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="detailQueue(q)" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-lg shadow-blue-500/20 transition-all flex items-center gap-1.5 transform hover:scale-105">
                                        Detail
                                    </button>
                                    <button @click="printReceipt(q)" class="bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-600 dark:text-slate-200 hover:text-gray-900 dark:hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-300 dark:border-slate-600/50 transition-all flex items-center gap-1.5">
                                        <i class="fa-solid fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-slate-900/50 border-t border-gray-200 dark:border-slate-700 flex justify-between items-center" v-if="pagination.total > 0">
                <span class="text-xs text-gray-500 dark:text-slate-500 font-mono">
                    Show <b>{{ pagination.from }}-{{ pagination.to }}</b> of <b>{{ pagination.total }}</b>
                </span>
                <div class="flex gap-2">
                    <button 
                        @click="changePage(pagination.current_page - 1)" 
                        :disabled="!pagination.prev_page_url"
                        class="px-4 py-1.5 text-xs font-bold rounded-lg bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-300 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white hover:border-gray-400 dark:hover:border-slate-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        <i class="fa-solid fa-chevron-left mr-1"></i> Prev
                    </button>
                     <button 
                        @click="changePage(pagination.current_page + 1)" 
                        :disabled="!pagination.next_page_url"
                        class="px-4 py-1.5 text-xs font-bold rounded-lg bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-300 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white hover:border-gray-400 dark:hover:border-slate-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        Next <i class="fa-solid fa-chevron-right ml-1"></i> 
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden Print Template (Reused Structure) -->
        <div id="print-history-area" class="hidden print:block fixed top-0 left-0 w-full h-full bg-white z-[9999] p-0 text-black">
             <div v-if="printData" class="w-[80mm] mx-auto p-4 font-mono text-xs leading-tight">
                <div class="text-center mb-4 pb-2 border-b-2 border-black border-dashed">
                    <img src="/img/logo_mci.png" class="h-10 mx-auto mb-1" alt="BPRS HIK MCI" onerror="this.style.display='none'">
                    <p class="scale-90">Jl. Kaliurang KM 9, Yogyakarta</p>
                    <p class="scale-90">Telp: (0274) 123456</p>
                    <p class="mt-2 font-bold uppercase">COPY RECEIPT</p>
                </div>
                 <div class="mb-3 space-y-1">
                    <div class="flex justify-between"><span>Tgl: {{ new Date(printData.created_at).toLocaleDateString('id-ID') }}</span></div>
                    <div class="flex justify-between"><span>No. Ref:</span><span class="font-bold">{{ printData.transaction?.token || printData.kode }}</span></div>
                    <div class="flex justify-between"><span>Teller:</span><span>{{ printData.antrian }}</span></div>
                </div>
                <div class="text-center py-2 mb-3 border-y-2 border-black border-dashed font-bold text-sm uppercase">
                    {{ printData.tx_type }}
                </div>
                <!-- Details -->
                 <div v-if="printData.transaction" class="space-y-3 mb-4">
                     <div>
                        <div class="uppercase text-[9px] font-bold mb-0.5">Rekening:</div>
                        <div class="font-bold">{{ printData.transaction.no_rek }}</div>
                        <div class="truncate">{{ printData.transaction.nama }}</div>
                    </div>
                    <div>
                        <div class="uppercase text-[9px] font-bold mb-0.5">Nominal:</div>
                        <div class="font-bold text-lg">Rp {{ Number(printData.transaction.nominal).toLocaleString('id-ID') }}</div>
                         <div class="italic text-[9px]">({{ printData.transaction.terbilang }})</div>
                    </div>
                 </div>
                 <div class="text-center mt-6 pt-2 border-t border-black text-[9px]">
                    <p>** SALINAN TRANSAKSI **</p>
                </div>
             </div>
        </div>

    </div>
</template>

<style>
@media print {
    body * { visibility: hidden; }
    #print-history-area, #print-history-area * { visibility: visible; }
    #print-history-area { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; background: white; }
    @page { size: 80mm auto; margin: 0; }
}
</style>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    props: ['type'],
    data() {
        return {
            queues: [],
            pagination: {},
            stats: { completed: 0, skipped: 0 },
            loading: false,
            filters: { date: '', search: '', tx_type: '' },
            printData: null
        }
    },
    created() {
        // Create debounce function for search
        this.debouncedSearch = this.debounce((val) => {
            this.fetchCompletedQueues(1);
        }, 500);
    },
    watch: {
        type: {
            immediate: true,
            handler() { this.fetchCompletedQueues(); }
        },
        // Auto-filter watchers
        'filters.date'() { this.fetchCompletedQueues(1); },
        'filters.tx_type'() { this.fetchCompletedQueues(1); },
        'filters.search'(val) { this.debouncedSearch(val); }
    },
    methods: {
        // Simple Debounce Implementation
        debounce(func, wait) {
            let timeout;
            return function(...args) {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        },
        async fetchCompletedQueues(page = 1) {
            this.loading = true;
            try {
                const params = {
                    type: this.type,
                    date: this.filters.date,
                    search: this.filters.search,
                    tx_type: this.filters.tx_type, // Added missing filter
                    page: page
                };
                const response = await axios.get('/admin/queue-history', { params });
                this.queues = response.data.data;
                this.pagination = response.data;
                this.stats.completed = this.queues.filter(q => q.st_antrian == 3).length; // Rough count
                this.stats.skipped = this.queues.filter(q => q.st_antrian == 4).length;
            } catch (error) {
                console.error("Error fetching history", error);
            } finally {
                this.loading = false;
            }
        },
        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.fetchCompletedQueues(page);
            }
        },
        formatDate(dateString) { return dateString ? new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric'}) : '-'; },
        formatTime(dateString) { return dateString ? new Date(dateString).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) : '-'; },
        
        downloadExcel() {
             const params = new URLSearchParams({
                type: this.type,
                date: this.filters.date,
                search: this.filters.search,
                tx_type: this.filters.tx_type
            });
            window.location.href = `/queue-history/export?${params.toString()}`;
        },

        detailQueue(q) {
            const isFinished = q.st_antrian == 3;
            // Build Detail HTML - PREMIUM CARD LAYOUT
            
            // Helper colors
            const isSetor = q.tx_type === 'Setor Tunai';
            const isTarik = q.tx_type === 'Tarik Tunai';
            const isTransfer = q.tx_type === 'Transfer';
            
            const accentHex = isSetor ? '#10b981' : (isTarik ? '#f43f5e' : (isTransfer ? '#6366f1' : '#64748b')); // Tailwind colors
            const icon = isSetor ? 'fa-arrow-down' : (isTarik ? 'fa-arrow-up' : (isTransfer ? 'fa-paper-plane' : 'fa-headset'));

            let txDetailHtml = '';

            if (q.transaction) {
                // Determine Actor Name
                const actorName = isTransfer ? q.transaction.nama : (isTarik ? q.transaction.nama_penarik : (isSetor ? q.transaction.nama_penyetor : q.nama));
                const actorPhone = isTarik ? q.transaction.hp_penarik : (isSetor ? q.transaction.hp_penyetor : '-');
                const actorAddress = isTarik ? q.transaction.alamat_penarik : (isSetor ? q.transaction.alamat_penyetor : '-');

                txDetailHtml = `
                    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
                        
                        <!-- Header with Color Accent -->
                        <div style="background: ${accentHex}; padding: 16px; color: white;">
                             <div class="flex justify-between items-start">
                                <div class="flex items-center gap-3">
                                    <div style="background: rgba(255,255,255,0.2); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid ${icon} text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg leading-none">${q.tx_type}</h3>
                                        <p class="text-xs opacity-80 mt-1 font-mono">${q.transaction.token}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-black">Rp ${Number(q.transaction.nominal).toLocaleString('id-ID')}</div>
                                    <div class="text-[10px] italic opacity-80">${q.transaction.terbilang}</div>
                                </div>
                             </div>
                        </div>

                        <!-- Content Body -->
                        <div class="p-5 space-y-4 text-left">
                            
                            <!-- 1. Account Info -->
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-12">
                                     <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-1">
                                        ${isSetor ? 'Rekening Tujuan' : 'Rekening Sumber'}
                                     </label>
                                     <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                                        <div class="font-bold text-slate-800 text-lg">${q.transaction.nama}</div>
                                        <div class="flex items-center gap-2 text-slate-500 font-mono text-xs mt-1">
                                            <i class="fa-solid fa-credit-card"></i> ${q.transaction.no_rek}
                                        </div>
                                     </div>
                                </div>
                            </div>

                            <!-- 2. Actor Info -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-1">
                                        Data ${isTransfer ? 'Pengirim' : (isSetor ? 'Penyetor' : 'Penarik')}
                                    </label>
                                    <div class="font-bold text-slate-800 text-base">${actorName}</div>
                                    <div class="text-xs text-slate-500">${actorAddress}</div>
                                    <div class="text-xs text-slate-400 mt-1"><i class="fa-regular fa-id-card mr-1"></i> HP: ${actorPhone}</div>
                                </div>
                            </div>

                            <!-- 3. Transfer Detail (If Transfer) -->
                            ${isTransfer ? `
                                <div style="background: #e0e7ff; border: 1px dashed #6366f1; padding: 10px; border-radius: 8px;">
                                    <label class="text-[10px] uppercase font-bold text-indigo-500 tracking-wider block mb-1">Penerima Transfer</label>
                                    <div class="font-bold text-indigo-900">${q.transaction.nama_tujuan || '-'}</div>
                                    <div class="text-xs text-indigo-700">${q.transaction.bank_tujuan || '-'} - ${q.transaction.no_rek_tujuan || '-'}</div>
                                </div>
                            ` : ''}

                            <!-- 4. Footer Status -->
                            <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                                <div>
                                    <span class="text-xs text-slate-500">Waktu Selesai:</span>
                                    <div class="font-bold text-slate-700 text-sm">${this.formatTime(q.updated_at)}</div>
                                </div>
                                <div class="${isFinished ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50'} px-3 py-1 rounded-full text-xs font-bold uppercase border ${isFinished ? 'border-emerald-100' : 'border-rose-100'}">
                                    ${isFinished ? 'SELESAI' : 'DILEWATI'}
                                </div>
                            </div>

                        </div>
                    </div>
                `;
            } else {
                 txDetailHtml = `<div class="bg-yellow-50 p-4 rounded-xl text-yellow-800 text-sm border border-yellow-200 italic mb-4 text-center">
                    <i class="fa-solid fa-triangle-exclamation text-2xl mb-2"></i><br>
                    Detail transaksi tidak tersedia.<br>
                    <span class="text-xs text-yellow-600">Mungkin ini antrian CS atau data lama.</span>
                 </div>`
            }

            Swal.fire({
                html: txDetailHtml,
                width: '450px',
                showConfirmButton: false, 
                showCloseButton: true,
                background: 'transparent',
                padding: 0
            });
        },
        printReceipt(queue) {
            this.printData = queue;
            setTimeout(() => {
                window.print();
            }, 500);
        }
    }
}
</script>
