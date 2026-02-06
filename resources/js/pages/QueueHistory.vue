<template>
    <div>
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-white">
                Riwayat Antrian <span class="text-blue-400">{{ type }}</span>
            </h2>
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                 <select v-if="type === 'Teller'" v-model="filters.tx_type" class="bg-slate-700 text-white px-4 py-2 rounded-lg border border-slate-600 focus:outline-none focus:border-blue-500 text-sm">
                    <option value="">Semua Transaksi</option>
                    <option value="Setor Tunai">Setor Tunai</option>
                    <option value="Tarik Tunai">Tarik Tunai</option>
                    <option value="Transfer">Transfer</option>
                 </select>
                 <input v-model="filters.date" type="date" class="bg-slate-700 text-white px-4 py-2 rounded-lg border border-slate-600 focus:outline-none focus:border-blue-500 text-sm">
                 <input v-model="filters.search" type="text" placeholder="Cari nama/antrian..." class="bg-slate-700 text-white px-4 py-2 rounded-lg border border-slate-600 focus:outline-none focus:border-blue-500 text-sm">
                <!-- Auto-filter active, button removed -->
            </div>
        </div>

        <!-- Summary Cards (Compact) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex items-center justify-between shadow-lg">
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Total Antrian</span>
                    <p class="text-2xl font-black text-white">{{ pagination.total || 0 }}</p>
                </div>
                <div class="bg-blue-500/10 p-2.5 rounded-lg text-blue-400"><i class="fa-solid fa-list-ol text-lg"></i></div>
            </div>
             <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex items-center justify-between shadow-lg">
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Selesai</span>
                    <p class="text-2xl font-black text-emerald-400">{{ stats.completed }}</p>
                </div>
                <div class="bg-emerald-500/10 p-2.5 rounded-lg text-emerald-400"><i class="fa-solid fa-check-circle text-lg"></i></div>
            </div>
             <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex items-center justify-between shadow-lg">
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Dilewati</span>
                    <p class="text-2xl font-black text-rose-400">{{ stats.skipped }}</p>
                </div>
                <div class="bg-rose-500/10 p-2.5 rounded-lg text-rose-400"><i class="fa-solid fa-ban text-lg"></i></div>
            </div>
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 shadow-lg">
                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Export</span>
                <button @click="downloadExcel" class="w-full mt-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white text-xs font-bold py-2 px-3 rounded-lg shadow-lg shadow-emerald-500/30 transform transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 group border border-emerald-400/20">
                        <div class="bg-white/20 p-1 rounded-md group-hover:bg-white/30 transition-colors">
                        <i class="fa-solid fa-file-excel"></i>
                        </div>
                        <span>Download .CSV</span>
                </button>
            </div>
        </div>

        <!-- Modern Table -->
        <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden shadow-2xl relative">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-400">
                    <thead class="bg-slate-900/80 text-slate-300 uppercase text-[10px] font-bold tracking-wider backdrop-blur-sm sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4">No. Antrian</th>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Nasabah</th>
                            
                            <!-- Teller Specific Columns -->
                            <th v-if="type === 'Teller'" class="px-6 py-4">Transaksi</th>
                            <th v-if="type === 'Teller'" class="px-6 py-4 text-right">Nominal</th>
                            
                            <!-- CS columns removed as requested -->

                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50 text-xs sm:text-sm">
                        <tr v-if="loading">
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-blue-500 mb-3"></i>
                                <p class="text-slate-500 animate-pulse">Memuat data...</p>
                            </td>
                        </tr>
                        <tr v-else-if="queues.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center italic text-slate-500">
                                <i class="fa-regular fa-folder-open text-4xl mb-2 opacity-50"></i>
                                <p>Tidak ada data antrian ditemukan.</p>
                            </td>
                        </tr>
                        <tr v-else v-for="q in queues" :key="q.id_antrian" class="hover:bg-slate-700/30 transition-all duration-150 group">
                            <!-- No Antrian -->
                            <td class="px-6 py-4">
                                <span class="font-black text-white text-lg tracking-tight group-hover:text-blue-400 transition-colors">{{ q.antrian }}</span>
                            </td>
                            
                            <!-- Waktu -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-slate-300 font-medium">{{ formatDate(q.tgl_antri) }}</span>
                                    <span class="text-[10px] text-slate-500 font-mono">{{ formatTime(q.updated_at) }}</span>
                                </div>
                            </td>
                            
                            <!-- Nasabah -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col max-w-[150px]">
                                    <!-- FIX: Fallback to transaction name if queue.nama is empty -->
                                    <span class="text-white font-bold truncate">{{ q.nama || (q.transaction ? q.transaction.nama : '-') }}</span>
                                    <span class="text-[10px] text-slate-500 font-mono truncate">{{ q.no_kontak || '' }}</span>
                                </div>
                            </td>
                            
                            <!-- Tipe Transaksi (Teller Only) -->
                            <td v-if="type === 'Teller'" class="px-6 py-4">
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
                                    <span class="text-slate-300 font-medium text-xs">{{ q.tx_type }}</span>
                                </div>
                                <span v-else class="text-slate-500 italic">-</span>
                            </td>
                            
                            <!-- Nominal (Teller Only) -->
                            <td v-if="type === 'Teller'" class="px-6 py-4 text-right">
                                <span v-if="q.transaction?.nominal" class="font-mono font-bold text-slate-200">
                                    Rp {{ Number(q.transaction.nominal).toLocaleString('id-ID') }}
                                </span>
                                <span v-else class="text-slate-600 text-xs">-</span>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 text-center">
                                <span v-if="q.st_antrian == 3" class="inline-flex items-center gap-1.5 text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full text-[10px] font-bold border border-emerald-500/20 uppercase tracking-wide">
                                    <i class="fa-solid fa-check"></i> Selesai
                                </span>
                                <span v-else class="inline-flex items-center gap-1.5 text-rose-400 bg-rose-500/10 px-2.5 py-1 rounded-full text-[10px] font-bold border border-rose-500/20 uppercase tracking-wide">
                                     <i class="fa-solid fa-forward"></i> Dilewati
                                </span>
                            </td>
                            
                            <!-- Aksi -->
                            <td class="px-6 py-4 text-center relative">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="detailQueue(q)" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-lg shadow-blue-500/20 transition-all flex items-center gap-1.5 transform hover:scale-105">
                                        Detail
                                    </button>
                                    <button v-if="q.transaction && q.st_antrian == 3" @click="printReceipt(q)" class="bg-slate-700 hover:bg-slate-600 text-slate-200 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-600/50 transition-all flex items-center gap-1.5">
                                        <i class="fa-solid fa-print"></i>
                                    </button>
                                    <button v-else-if="q.transaction" disabled class="bg-slate-800 text-slate-600 px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-700/50 flex items-center gap-1.5 cursor-not-allowed opacity-50">
                                        <i class="fa-solid fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="px-6 py-4 bg-slate-900/50 border-t border-slate-700 flex justify-between items-center" v-if="pagination.total > 0">
                <span class="text-xs text-slate-500 font-mono">
                    Show <b>{{ pagination.from }}-{{ pagination.to }}</b> of <b>{{ pagination.total }}</b>
                </span>
                <div class="flex gap-2">
                    <button 
                        @click="changePage(pagination.current_page - 1)" 
                        :disabled="!pagination.prev_page_url"
                        class="px-4 py-1.5 text-xs font-bold rounded-lg bg-slate-800 text-slate-300 border border-slate-700 hover:bg-slate-700 hover:text-white hover:border-slate-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        <i class="fa-solid fa-chevron-left mr-1"></i> Prev
                    </button>
                     <button 
                        @click="changePage(pagination.current_page + 1)" 
                        :disabled="!pagination.next_page_url"
                        class="px-4 py-1.5 text-xs font-bold rounded-lg bg-slate-800 text-slate-300 border border-slate-700 hover:bg-slate-700 hover:text-white hover:border-slate-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        Next <i class="fa-solid fa-chevron-right ml-1"></i> 
                    </button>
                </div>
            </div>
        </div>

        <!-- PRINT AREA (Wrapper) -->
        <div id="print-wrapper" class="hidden print:block fixed top-0 left-0 w-full h-full bg-white z-[9999] p-0 text-black">
            
            <!-- 1. STANDARD THERMAL (80mm) -->
            <div id="print-area-standard" class="w-[80mm] mx-auto p-4 font-mono text-xs leading-tight hidden">
                <div class="text-center mb-4 pb-2 border-b-2 border-black border-dashed">
                    <img src="/img/logo_mci.png" class="h-10 mx-auto mb-1" alt="BPRS HIK MCI">
                    <p class="scale-90">Jl. Kaliurang KM 9, Yogyakarta</p>
                    <p class="scale-90">Telp: (0274) 123456</p>
                </div>

                <div v-if="printData && printData.transaction">
                    <div class="mb-3 space-y-1">
                        <div class="flex justify-between font-bold">
                            <span>{{ printData.tx_type }}</span>
                            <span>{{ printData.transaction.token }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ new Date(printData.created_at).toLocaleDateString('id-ID') }}</span>
                            <span>{{ new Date(printData.created_at).toLocaleTimeString('id-ID') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. VALIDATION SLIP (1/3 A4 Landscape) -->
            <div id="print-area-slip" class="w-[210mm] h-[99mm] px-3 py-4 font-sans text-xs relative box-border border-b border-black hidden">
                
                <div v-if="printData && printData.transaction">
                <!-- Slip Header -->
                <div class="flex justify-between items-end mb-2 pb-2 border-b-2 border-black">
                    <div class="flex items-center gap-3">
                        <img src="/img/logo_mci.png" class="h-12" alt="Logo MCI">
                        <img src="/img/logo_ib.png" class="h-10" alt="Logo iB" onerror="this.style.display='none'">
                    </div>
                    <div class="text-right">
                        <h1 class="font-extrabold text-xl uppercase tracking-widest px-3 py-1 border-2 inline-block rounded-sm"
                            :class="isSetor ? 'text-blue-600 border-blue-600' : 'text-red-600 border-red-600'">
                            {{ isSetor ? 'SETOR TUNAI' : 'TARIK TUNAI' }}
                        </h1>
                        <p class="text-[10px] mt-1 font-mono text-slate-500">{{ printData.transaction.token }}</p>
                    </div>
                </div>

                <!-- Slip Body -->
                <div class="space-y-1.5 leading-snug">
                    
                    <!-- Date & Checkboxes -->
                    <div class="flex justify-between items-center border-b border-dashed border-slate-300 pb-1 mb-1">
                        <div class="flex gap-1 items-center">
                            <span class="font-bold w-16">Tanggal</span>
                            <span>: {{ new Date(printData.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) }}</span>
                        </div>
                        <div class="flex gap-3 text-[10px]">
                            <!-- Setor Checkboxes -->
                            <template v-if="isSetor">
                                <span class="flex items-center gap-1"><div class="w-3 h-3 border border-black flex items-center justify-center text-[8px]"><i class="fa-solid fa-check"></i></div> Tabungan</span>
                                <span class="flex items-center gap-1"><div class="w-3 h-3 border border-black"></div> Deposito</span>
                                <span class="flex items-center gap-1"><div class="w-3 h-3 border border-black"></div> Pembiayaan</span>
                                <span class="flex items-center gap-1"><div class="w-3 h-3 border border-black"></div> Lainnya</span>
                            </template>
                            <!-- Tarik Checkboxes -->
                            <template v-else>
                                <span class="flex items-center gap-1"><div class="w-3 h-3 border border-black flex items-center justify-center text-[8px]"><i class="fa-solid fa-check"></i></div> Tabungan</span>
                                <span class="flex items-center gap-1"><div class="w-3 h-3 border border-black"></div> Kredit</span>
                                <span class="flex items-center gap-1"><div class="w-3 h-3 border border-black"></div> Lainnya</span>
                            </template>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div class="bg-indigo-50/50 p-1.5 border border-indigo-100/50 rounded flex gap-4">
                        <div class="flex-1">
                            <span class="font-bold block text-[10px] uppercase text-slate-500">Nama Rekening</span>
                            <span class="font-bold text-sm truncate block">{{ printData.transaction.nama }}</span>
                        </div>
                        <div class="flex-1 border-l border-indigo-200 pl-4">
                            <span class="font-bold block text-[10px] uppercase text-slate-500">Nomor Rekening</span>
                            <span class="font-mono font-bold text-sm tracking-wider">{{ printData.transaction.no_rek }}</span>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="flex items-center gap-2 mt-1">
                        <span class="font-bold w-28 text-right">Jumlah {{ isSetor ? 'Setoran' : 'Penarikan' }} :</span>
                        <div class="flex-grow bg-slate-100 px-2 py-1 border border-slate-300 font-mono font-bold text-base flex justify-start gap-2 items-center">
                            <span>Rp.</span>
                            <span>{{ Number(printData.transaction.nominal).toLocaleString('id-ID') }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-bold w-28 text-right text-[10px]">Terbilang :</span>
                        <div class="flex-grow italic text-[10px] bg-slate-50 px-2 border-b border-slate-200 capitalize">
                            {{ printData.transaction.terbilang }}
                        </div>
                    </div>

                    <!-- Transaction Details -->
                    <div class="grid grid-cols-2 gap-x-8 gap-y-1 mt-1">
                        <!-- Left Col -->
                        <div class="space-y-1">
                            <div class="flex gap-2">
                                <span class="font-bold w-24">Tujuan Transaksi</span>
                                <span>: {{ printData.transaction.berita || printData.transaction.tujuan || '-' }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="font-bold w-24">No. Identitas</span>
                                <span>: {{ activeActorIdentity || '-' }}</span>
                            </div>
                        </div>
                        <!-- Right Col -->
                        <div class="space-y-1">
                             <div class="flex gap-2">
                                <span class="font-bold w-20">Alamat</span>
                                <span class="text-[10px] leading-tight">: {{ activeActorAddress }}</span>
                            </div>
                             <div class="flex gap-2">
                                <span class="font-bold w-20">No. HP</span>
                                <span>: {{ activeActorPhone }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer Signatures -->
                <div class="absolute bottom-5 left-5 right-5 flex justify-between items-end">
                    
                    <!-- Disclaimer & Address -->
                    <div class="w-[45%] pr-4 flex flex-col justify-end h-full">
                        <div class="text-[7px] text-justify leading-tight opacity-70 italic mb-2">
                            *) {{ isSetor ? 'Setoran' : 'Penarikan' }} akan dibukukan secara efektif setelah dana {{ isSetor ? 'diterima' : 'diserahkan' }} dengan baik, valid setelah dibubuhi cap dan tanda tangan petugas bank.
                        </div>
                        <div class="text-[8px] font-bold text-slate-600 border-t border-slate-300 pt-1">
                            PT. BPRS HIK MCI<br>
                            Jl. Kaliurang KM 9, Yogyakarta. Telp: (0274) 123456
                        </div>
                    </div>

                    <!-- Signatures (Dynamic Grid) -->
                    <div class="w-[55%] pl-2">
                        <!-- SETOR: 2 Cols -->
                        <div v-if="isSetor" class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="mb-10 text-[9px] font-bold uppercase">Teller</p>
                                <p class="text-[9px] border-t border-black pt-0.5 w-full block">( .......................... )</p>
                            </div>
                            <div>
                                <p class="mb-10 text-[9px] font-bold uppercase">Penyetor</p>
                                <p class="text-[9px] border-t border-black pt-0.5 w-full block">( .......................... )</p>
                            </div>
                        </div>

                        <!-- TARIK: 4 Cols -->
                        <div v-else class="grid grid-cols-4 gap-2 text-center">
                            <div>
                                <p class="mb-10 text-[8px] font-bold uppercase">Teller</p>
                                <p class="mb-10 text-[8px] border-t border-black pt-0.5 w-full block">(.......)</p>
                            </div>
                            <div>
                                <p class="mb-10 text-[8px] font-bold uppercase">Checker</p>
                                <p class="mb-10 text-[8px] border-t border-black pt-0.5 w-full block">(.......)</p>
                            </div>
                            <div>
                                <p class="mb-10 text-[8px] font-bold uppercase">Approver</p>
                                <p class="mb-10 text-[8px] border-t border-black pt-0.5 w-full block">(.......)</p>
                            </div>
                            <div>
                                <p class="mb-10 text-[8px] font-bold uppercase">Nasabah</p>
                                <p class="mb-10 text-[8px] border-t border-black pt-0.5 w-full block">(.......)</p>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
            </div>

            <!-- 3. TRANSFER SLIP (1/2 A4 Landscape - Green) -->
            <div id="print-area-transfer" class="w-[210mm] h-[148mm] px-5 py-5 font-sans text-xs relative box-border border-b border-black hidden">
                
                <div v-if="printData && printData.transaction">
                <!-- Header -->
                <div class="flex justify-between items-end mb-4 pb-2 border-b-2 border-green-700">
                    <div class="flex items-center gap-3">
                        <img src="/img/logo_mci.png" class="h-14" alt="Logo MCI">
                        <img src="/img/logo_ib.png" class="h-10" alt="Logo iB" onerror="this.style.display='none'">
                    </div>
                    <div class="text-right">
                        <h1 class="font-bold text-xl text-green-700 uppercase tracking-widest px-4 py-1 border-2 border-green-700 inline-block rounded-sm mb-1">
                            APLIKASI TRANSFER
                        </h1>
                        <p class="text-[10px] font-mono text-slate-500 tracking-wider">REF: {{ printData.transaction.token }}</p>
                    </div>
                </div>

                <!-- Body Grid -->
                <div class="grid grid-cols-2 gap-x-8 gap-y-4 mb-4">
                    
                    <!-- Left: Pengirim -->
                    <div class="bg-slate-50 p-3 rounded border border-slate-200">
                        <h3 class="font-bold text-green-700 border-b border-green-700 mb-2 uppercase text-[11px] pb-1">
                            <i class="fa-solid fa-user mr-1"></i> Data Pengirim
                        </h3>
                        <table class="w-full text-[11px] leading-snug">
                            <tr>
                                <td class="w-28 font-semibold align-top text-slate-500 py-0.5">Nama Lengkap</td>
                                <td class="w-2 align-top py-0.5">:</td>
                                <td class="uppercase font-bold py-0.5">{{ printData.transaction.nama }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">Alamat</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="uppercase h-10 overflow-hidden leading-tight text-slate-800 py-0.5">{{ printData.transaction.alamat_penyetor || '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">No. Telepon</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="text-slate-800 py-0.5">{{ printData.transaction.hp_penyetor || '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">Sumber Dana</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="italic font-bold text-slate-700 py-0.5">{{ printData.transaction.sumber_dana || 'Tunai' }}</td>
                            </tr>
                             <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">Metode Transfer</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="font-bold text-slate-900 py-0.5">{{ printData.transaction.metode_transfer || '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Right: Penerima -->
                    <div class="bg-slate-50 p-3 rounded border border-slate-200">
                        <h3 class="font-bold text-green-700 border-b border-green-700 mb-2 uppercase text-[11px] pb-1">
                            <i class="fa-solid fa-bullseye mr-1"></i> Data Penerima
                        </h3>
                        <table class="w-full text-[11px] leading-snug">
                            <tr>
                                <td class="w-24 font-semibold align-top text-slate-500 py-0.5">Nama Penerima</td>
                                <td class="w-2 align-top py-0.5">:</td>
                                <td class="uppercase font-bold text-sm text-slate-900 py-0.5">{{ printData.transaction.nama_tujuan }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">No. Rekening</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="font-mono text-sm font-bold tracking-wider text-slate-900 py-0.5">{{ printData.transaction.no_rek_tujuan }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">Bank Tujuan</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="uppercase font-bold text-slate-800 py-0.5">{{ printData.transaction.bank_tujuan }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">Negara</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="uppercase text-slate-800 py-0.5">{{ printData.transaction.negara_tujuan || 'INDONESIA' }}</td>
                            </tr>
                             <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">Keterangan</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="italic text-slate-700 py-0.5">{{ printData.transaction.tujuan || printData.transaction.berita_tujuan || '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Transaction Details (Professional Financial Block) -->
                <div class="mb-5 border-y-2 border-black py-3 bg-slate-50/50">
                    <div class="flex items-start justify-between gap-6">
                        <!-- Nominal & Currency -->
                        <div class="w-1/3">
                            <p class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">Jumlah Setoran / Amount</p>
                            <div class="flex items-center border-2 border-slate-800 bg-white p-1 pl-2 rounded-sm shadow-sm">
                                <span class="font-bold text-sm text-slate-700 mr-2 border-r border-slate-300 pr-2">{{ printData.transaction.mata_uang || 'IDR' }}</span>
                                <span class="font-mono font-bold text-xl text-slate-900 tracking-tight">
                                    {{ Number(printData.transaction.nominal).toLocaleString('id-ID') }}
                                </span>
                            </div>
                        </div>

                        <!-- Terbilang -->
                        <div class="flex-1">
                             <p class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">Terbilang / In Words</p>
                             <div class="w-full border-b border-black border-dashed pb-1 min-h-[34px] flex items-end">
                                <span class="italic font-bold text-[12px] uppercase leading-tight text-slate-800 break-words w-full">
                                    {{ printData.transaction.terbilang.replace(/ rupiah$/i, '') }} RUPIAH
                                </span>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Signatures -->
                <div class="absolute bottom-6 left-5 right-5 flex justify-between items-end h-[35mm]">
                    
                    <!-- Left: Bank Process Box -->
                    <div class="w-[45%] h-full border-2 border-slate-400 rounded-lg p-2 flex flex-col justify-between bg-white relative">
                         <div class="absolute top-0 left-0 right-0 bg-slate-100 border-b border-slate-300 py-0.5 text-center">
                             <p class="text-[9px] font-bold uppercase text-slate-600">Diisi Oleh Bank (Process)</p>
                         </div>
                        <div class="grid grid-cols-2 gap-4 text-center text-[9px] h-full items-end pb-1 mt-4">
                            <div class="flex flex-col items-center">
                                <div class="h-10 w-full mb-1"></div>
                                <span class="block border-t border-slate-400 w-full pt-0.5 font-bold text-slate-700">Teller / Petugas</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="h-10 w-full mb-1"></div>
                                <span class="block border-t border-slate-400 w-full pt-0.5 font-bold text-slate-700">Validasi / Pejabat</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Applicant Signature -->
                     <div class="w-[45%] flex flex-col justify-end text-center pl-4">
                        <p class="text-[8px] text-justify leading-tight opacity-80 italic mb-4 font-serif text-slate-600">
                            "Saya menyetujui syarat-syarat yang tercantum pada halaman belakang formulir ini dan permohonan ini sah setelah validasi."
                        </p>
                        <div class="w-full">
                            <p class="mb-10 text-[10px] font-bold uppercase tracking-wide">Tanda Tangan Pemohon / Pengirim</p>
                            <p class="text-[11px] border-t-2 border-black pt-1 block font-bold uppercase">
                                ( {{ printData.transaction.nama }} )
                            </p>
                        </div>
                    </div>
                </div>

                </div>
            </div>

        </div>

    </div>
</template>

<style>
/* PRINT SPECIFIC STYLES - @page must be top level */
@page {
    size: 80mm auto; /* Standard 80mm Thermal Paper */
    margin: 0mm; /* Mandatory for thermal to remove headers/footers */
}

@media print {
    body * { visibility: hidden; }
    
    #print-wrapper, #print-wrapper * { 
        visibility: visible; 
    }

    /* Force the print wrapper to be the top-left */
    #print-wrapper { 
        visibility: visible;
        position: fixed; 
        left: 0; 
        top: 0; 
        margin: 0; 
        padding: 0; 
        background: white; 
        color: black;
        overflow: visible;
        z-index: 9999;
    }

    /* Ensure body is also constrained */
    html, body {
        width: 80mm;
        margin: 0;
        padding: 0;
        height: auto;
        background: white;
    }

    /* Utility to ensure background colors print (if browser supports) */
    * { 
        -webkit-print-color-adjust: exact !important; 
        print-color-adjust: exact !important; 
        box-shadow: none !important;
        text-shadow: none !important;
    }
}

/* Add Stamp Animation */
@keyframes stamp-scale {
    0% { transform: translate(-50%, -50%) scale(3) rotate(-15deg); opacity: 0; }
    100% { transform: translate(-50%, -50%) scale(1) rotate(-15deg); opacity: 0.2; }
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
    computed: {
        isSetor() {
            return this.printData && this.printData.tx_type === 'Setor Tunai';
        },
        isTarik() {
            return this.printData && this.printData.tx_type === 'Tarik Tunai';
        },
        isTransfer() {
            return this.printData && this.printData.tx_type === 'Transfer';
        },
        activeActorAddress() {
            if (!this.printData || !this.printData.transaction) return '-';
            if (this.isTransfer) return this.printData.transaction.alamat_penyetor || '-';
            if (this.isSetor) return this.printData.transaction.alamat_penyetor || '-';
            if (this.isTarik) return this.printData.transaction.alamat_penarik || '-';
            return '-';
        },
        activeActorPhone() {
            if (!this.printData || !this.printData.transaction) return '-';
            if (this.isTransfer) return this.printData.transaction.hp_penyetor || '-';
            if (this.isSetor) return this.printData.transaction.hp_penyetor || '-';
            if (this.isTarik) return this.printData.transaction.hp_penarik || '-';
            return '-';
        },
        activeActorIdentity() {
            if (!this.printData || !this.printData.transaction) return '-';
            if (this.isSetor) return this.printData.transaction.noid_penyetor || '-';
            if (this.isTarik) return this.printData.transaction.noid_penarik || '-';
            return '-';
        }
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
        
        async downloadExcel() {
            const today = new Date().toISOString().split('T')[0];
            
            const { value: formValues } = await Swal.fire({
                title: '', // Hide default title
                padding: 0,
                html: `
                    <div class="overflow-hidden rounded-t-lg">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 text-white text-center">
                            <div class="bg-white/20 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-3 backdrop-blur-sm border border-white/30">
                                <i class="fa-solid fa-file-csv text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold">Export Data Antrian</h3>
                            <p class="text-emerald-100 text-xs mt-1">Pilih rentang tanggal data yang ingin diunduh</p>
                        </div>
                        <div class="p-6 text-left space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                                        <i class="fa-regular fa-calendar mr-1 text-emerald-500"></i> Dari Tanggal
                                    </label>
                                    <input id="swal-start" type="date" value="${today}" class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block p-2.5">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                                        <i class="fa-solid fa-arrow-right-long mr-1 text-slate-400"></i> Sampai Tanggal
                                    </label>
                                    <input id="swal-end" type="date" value="${today}" class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block p-2.5">
                                </div>
                            </div>
                            <div class="bg-emerald-50 text-emerald-700 text-xs p-3 rounded-lg border border-emerald-100 flex gap-2 items-start">
                                <i class="fa-solid fa-circle-info mt-0.5"></i>
                                <span>Export ini akan mengunduh data dalam format CSV yang kompatibel dengan Microsoft Excel.</span>
                            </div>
                        </div>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-download mr-1"></i> Download File',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#94a3b8',
                customClass: {
                    popup: 'rounded-xl overflow-hidden',
                    confirmButton: 'px-6 py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-emerald-500/30',
                    cancelButton: 'px-6 py-2.5 rounded-lg text-sm font-bold'
                },
                preConfirm: () => {
                    return [
                        document.getElementById('swal-start').value,
                        document.getElementById('swal-end').value
                    ]
                }
            });

            if (formValues) {
                const [start, end] = formValues;
                if (!start || !end) return;

                const params = new URLSearchParams({
                    type: this.type,
                    start_date: start,
                    end_date: end,
                    search: this.filters.search, // Optional: Keep search filter if desired
                    tx_type: this.filters.tx_type
                });
                
                // Trigger Download
                window.location.href = `/admin/queue-history/export?${params.toString()}`;
            }
        },

        detailQueue(q) {
            const isFinished = q.st_antrian == 3;
            // q.st_antrian == 4 is Skipped
            const isSkipped = q.st_antrian == 4;
            
            let txDetailHtml = '';

            // --- TELLER DETAIL (Premium Card) ---
            if (this.type === 'Teller' && q.transaction) {
                const isSetor = q.tx_type === 'Setor Tunai';
                const isTarik = q.tx_type === 'Tarik Tunai';
                const isTransfer = q.tx_type === 'Transfer';
                
                const accentHex = isSetor ? '#10b981' : (isTarik ? '#f43f5e' : (isTransfer ? '#6366f1' : '#64748b')); // Tailwind colors
                const icon = isSetor ? 'fa-arrow-down' : (isTarik ? 'fa-arrow-up' : (isTransfer ? 'fa-paper-plane' : 'fa-headset'));

                // Determine Actor Name
                const actorName = isTransfer ? q.transaction.nama : (isTarik ? q.transaction.nama_penarik : (isSetor ? q.transaction.nama_penyetor : q.nama));
                const actorPhone = isTarik ? q.transaction.hp_penarik : (isSetor ? q.transaction.hp_penyetor : '-');
                const actorAddress = isTarik ? q.transaction.alamat_penarik : (isSetor ? q.transaction.alamat_penyetor : '-');
                
                // STAMP HTML
                const stampHtml = isSkipped ? `
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-15deg); border: 4px solid #f43f5e; color: #f43f5e; font-size: 3rem; font-weight: 900; padding: 10px 40px; text-transform: uppercase; letter-spacing: 5px; opacity: 0.2; z-index: 0; pointer-events: none; white-space: nowrap; border-radius: 10px; font-family: 'Courier New', monospace; animation: stamp-scale 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;">
                        DILEWATI
                    </div>
                ` : '';

                txDetailHtml = `
                    <div style="position: relative; padding-top: 50px;">
                        <!-- Floating Centered Close Button (Outside Card) -->
                        <div style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); z-index: 9999;">
                            <button id="close-modal-btn" class="bg-slate-800 hover:bg-slate-900 text-white w-10 h-10 rounded-full flex items-center justify-center transition-all border-2 border-white shadow-xl cursor-pointer hover:scale-110 active:scale-95">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <!-- Main Card -->
                        <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); position: relative;">
                            
                            ${stampHtml}

                            <!-- Header with Color Accent -->
                            <div style="background: ${isSkipped ? '#475569' : accentHex}; padding: 16px; color: white; position: relative; z-index: 10;">
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
                            <div class="p-5 space-y-4 text-left relative z-10">
                                
                                ${isSkipped ? `
                                    <div style="background: #fff1f2; border: 1px dashed #f43f5e; padding: 12px; border-radius: 8px; margin-bottom: 10px;">
                                        <label class="text-[10px] uppercase font-bold text-rose-500 tracking-wider block mb-1">
                                            <i class="fa-solid fa-triangle-exclamation mr-1"></i> Alasan Dilewati
                                        </label>
                                        <div class="font-bold text-rose-900 text-sm italic">
                                            "${q.solusi || 'Tidak ada alasan.'}"
                                        </div>
                                    </div>
                                ` : ''}

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
                                 ${isTransfer ? `
                                    <div style="background: #e0e7ff; border: 1px dashed #6366f1; padding: 10px; border-radius: 8px;">
                                        <label class="text-[10px] uppercase font-bold text-indigo-500 tracking-wider block mb-1">Penerima Transfer</label>
                                        <div class="font-bold text-indigo-900">${q.transaction.nama_tujuan || '-'}</div>
                                        <div class="text-xs text-indigo-700">${q.transaction.bank_tujuan || '-'} - ${q.transaction.no_rek_tujuan || '-'}</div>
                                    </div>
                                ` : ''}
                                
                                 <!-- Footer Status -->
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
                    </div>
                `;
            } 
            
            // --- CS DETAIL (Modern & Premium View) ---
            else if (this.type === 'CS') {
                const stampHtmlCS = isSkipped ? `
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-15deg); border: 4px solid #f43f5e; color: #f43f5e; font-size: 3rem; font-weight: 900; padding: 10px 40px; text-transform: uppercase; letter-spacing: 5px; opacity: 0.2; z-index: 0; pointer-events: none; white-space: nowrap; border-radius: 10px; font-family: 'Courier New', monospace; animation: stamp-scale 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;">
                        DILEWATI
                    </div>
                ` : '';

                 txDetailHtml = `
                    <div style="position: relative; padding-top: 50px;">
                        <!-- Floating Centered Close Button -->
                        <div style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); z-index: 9999;">
                            <button id="close-modal-btn" class="bg-slate-800 hover:bg-slate-900 text-white w-10 h-10 rounded-full flex items-center justify-center transition-all border-2 border-white shadow-xl cursor-pointer hover:scale-110 active:scale-95">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <!-- Main Card -->
                        <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); position: relative;">
                            
                            ${stampHtmlCS}

                            <!-- Premium CS Heder -->
                            <div style="background: ${isSkipped ? '#475569' : 'linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%)'}; padding: 24px; color: white; position: relative; z-index: 10;">
                                 <div style="position: absolute; top: -10px; right: -10px; opacity: 0.1; transform: rotate(15deg);">
                                    <i class="fa-solid fa-headset text-9xl"></i>
                                 </div>
                                 
                                 <div class="flex justify-between items-start relative z-10">
                                    <div>
                                        <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider mb-2 backdrop-blur-sm">
                                            Layanan Pelanggan
                                        </div>
                                        <h3 class="font-bold text-2xl leading-tight">Detail Pelayanan</h3>
                                        <p class="text-sm opacity-90 mt-1 font-mono">${q.antrian}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md border border-white/30">
                                        <i class="fa-solid fa-user-check text-xl"></i>
                                    </div>
                                 </div>
                            </div>

                            <!-- Content Body -->
                            <div class="p-6 space-y-6 text-left relative z-10">
                                
                                <!-- 1. Customer Profile -->
                                <div class="flex items-center gap-4 pb-6 border-b border-slate-100">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-xl border border-slate-200">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-0.5">Nasabah</label>
                                        <div class="font-bold text-slate-800 text-lg leading-none">${q.nama || 'Tanpa Nama'}</div>
                                        <div class="text-xs text-slate-500 mt-1 font-mono"><i class="fa-solid fa-phone mr-1"></i> ${q.no_kontak || '-'}</div>
                                    </div>
                                </div>

                                <!-- 2. The Case (Timeline Style) -->
                                <div class="relative pl-4 border-l-2 border-slate-100 space-y-6 ml-2">
                                    
                                    <!-- Masalah / Keperluan -->
                                    <div class="relative">
                                        <div class="absolute -left-[21px] top-1 w-3 h-3 rounded-full bg-indigo-500 border-2 border-white shadow-sm"></div>
                                        <label class="text-[10px] uppercase font-bold text-indigo-500 tracking-wider block mb-2">
                                            <i class="fa-regular fa-comment-dots mr-1"></i> Keperluan / Keluhan
                                        </label>
                                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 text-slate-700 text-sm leading-relaxed">
                                            ${q.tujuan_datang || '<span class="italic text-slate-400">Tidak ada catatan keperluan.</span>'}
                                        </div>
                                    </div>

                                    <!-- Solusi / Alasan Dilewati -->
                                    <div class="relative">
                                         <div class="absolute -left-[21px] top-1 w-3 h-3 rounded-full ${isSkipped ? 'bg-rose-500' : 'bg-emerald-500'} border-2 border-white shadow-sm"></div>
                                        <label class="text-[10px] uppercase font-bold ${isSkipped ? 'text-rose-500' : 'text-emerald-500'} tracking-wider block mb-2">
                                            <i class="fa-solid ${isSkipped ? 'fa-ban' : 'fa-check-double'} mr-1"></i> ${isSkipped ? 'Alasan Dilewati' : 'Solusi / Tindakan'}
                                        </label>
                                        <div class="${isSkipped ? 'bg-rose-50/50 border-rose-100/50' : 'bg-emerald-50/50 border-emerald-100/50'} p-3 rounded-lg border text-slate-800 font-medium text-sm leading-relaxed shadow-sm">
                                            ${q.solusi || '<span class="italic text-slate-400">Tidak ada catatan.</span>'}
                                        </div>
                                    </div>
                                </div>

                                 <!-- Footer Status -->
                                <div class="pt-2 flex justify-between items-center">
                                    <div>
                                        <span class="text-xs text-slate-400 block mb-0.5">Waktu Selesai</span>
                                        <div class="inline-flex items-center gap-2 bg-slate-100 px-2 py-1 rounded text-xs font-mono font-medium text-slate-600">
                                            <i class="fa-regular fa-clock"></i> ${this.formatTime(q.updated_at)}
                                        </div>
                                    </div>
                                    <div class="${isFinished ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50'} pl-2 pr-3 py-1 rounded-full text-xs font-bold uppercase border ${isFinished ? 'border-emerald-100' : 'border-rose-100'} flex items-center gap-1.5">
                                        <i class="fa-solid ${isFinished ? 'fa-circle-check' : 'fa-circle-xmark'}"></i>
                                        ${isFinished ? 'SELESAI' : 'DILEWATI'}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                `;
            }
            
            // --- FALLBACK ---
            else {
                 txDetailHtml = `<div class="bg-yellow-50 p-4 rounded-xl text-yellow-800 text-sm border border-yellow-200 italic mb-4 text-center">
                    <i class="fa-solid fa-triangle-exclamation text-2xl mb-2"></i><br>
                    Detail transaksi tidak tersedia.<br>
                    <span class="text-xs text-yellow-600">Data tidak ditemukan atau format lama.</span>
                 </div>`
            }

            Swal.fire({
                html: txDetailHtml,
                width: '450px',
                showConfirmButton: false, // Disabled default close button
                showCloseButton: false, // Explicitly disabled
                background: 'transparent',
                padding: 0,
                customClass: {
                    popup: 'overflow-visible' // Ensure button isn't clipped
                },
                didOpen: () => {
                    const closeBtn = document.getElementById('close-modal-btn');
                    if(closeBtn) {
                        closeBtn.addEventListener('click', () => {
                            Swal.close();
                        });
                    }
                }
            });
        },

        printReceipt(queue) {
            this.printData = queue;

            // Wait for Vue to render the printData
            setTimeout(() => {
                const style = document.createElement('style');
                style.id = 'dynamic-print-style';

                // Determine which slip to show based on type
                if (this.isTransfer) {
                     style.innerHTML = `
                         @page { size: 210mm 148mm; margin: 0; }
                         @media print { 
                             #print-area-transfer { display: block !important; } 
                             #print-area-slip, #print-area-standard { display: none !important; }
                             /* Hide other elements handled by global @media print */
                         }
                     `;
                     // Force show
                     const el = document.getElementById('print-area-transfer');
                     if(el) el.classList.remove('hidden');
                } else if (this.isSetor || this.isTarik) {
                     style.innerHTML = `
                         @page { size: 210mm 99mm; margin: 0; }
                         @media print { 
                             #print-area-slip { display: block !important; } 
                             #print-area-transfer, #print-area-standard { display: none !important; }
                         }
                     `;
                      const el = document.getElementById('print-area-slip');
                      if(el) el.classList.remove('hidden');
                } else {
                     // Standard Thermal
                      style.innerHTML = `
                         @page { size: 80mm auto; margin: 0; }
                         @media print { 
                             #print-area-standard { display: block !important; } 
                             #print-area-transfer, #print-area-slip { display: none !important; }
                         }
                     `;
                      const el = document.getElementById('print-area-standard');
                      if(el) el.classList.remove('hidden');
                }

                document.head.appendChild(style);

                window.print();

                // Cleanup
                setTimeout(() => {
                     if(style) document.head.removeChild(style);
                     // Hide all
                     document.querySelectorAll('#print-wrapper > div').forEach(el => el.classList.add('hidden'));
                }, 1000);

            }, 200);
        }
    }
}
</script>
