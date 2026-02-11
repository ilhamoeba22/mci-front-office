<template>
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white transition-colors">
                Manajemen Antrian: <span class="text-blue-600 dark:text-blue-400">{{ type }}</span>
            </h2>
            <button @click="fetchQueues" class="bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-white px-4 py-2 rounded-lg transition-colors border border-gray-300 dark:border-slate-600">
                <i class="fa-solid fa-sync mr-2"></i> Segarkan
            </button>
        </div>

        <!-- Current Active Queue or Idle State -->
        <div class="bg-white dark:bg-gradient-to-r dark:from-blue-900 dark:to-slate-900 border border-gray-200 dark:border-blue-500/30 rounded-xl p-6 mb-8 shadow-sm dark:shadow-xl min-h-[250px] flex flex-col justify-center transition-all">
            
            <!-- CASE 1: Currently Serving -->
            <div v-if="currentQueue">
                <div class="flex flex-col md:flex-row gap-4 items-stretch h-full">
                    
                    <!-- LEFT COLUMN: Queue Info + Actions -->
                    <div class="w-full md:w-1/4 flex flex-col gap-3 min-w-[240px]">
                        <!-- Queue Info -->
                        <div class="bg-gray-50 dark:bg-black/20 p-5 rounded-xl text-center border border-gray-200 dark:border-white/10 transition-colors">
                            <p class="text-blue-600 dark:text-blue-300 text-xs font-bold uppercase tracking-wider mb-1">Sedang Melayani</p>
                            <h1 class="text-6xl font-extrabold text-gray-800 dark:text-white tracking-tight">{{ currentQueue.antrian }}</h1>
                            <div class="mt-3 inline-block bg-yellow-100 dark:bg-yellow-500/20 text-yellow-600 dark:text-yellow-400 px-3 py-0.5 rounded-full text-[10px] font-bold border border-yellow-200 dark:border-yellow-500/30">
                                STATUS: DIPANGGIL
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-2 flex-grow justify-end">
                            <button @click="callQueue(currentQueue.id_antrian)" class="bg-gradient-to-r from-yellow-500 to-yellow-400 hover:from-yellow-400 hover:to-yellow-300 text-white p-3 rounded-xl font-bold shadow-lg transform hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-bullhorn text-lg"></i> 
                                <span class="text-left leading-none text-sm">
                                    PANGGIL ULANG<br>
                                    <span class="text-[9px] font-normal opacity-80">Bunyikan suara</span>
                                </span>
                            </button>
                            
                            <button @click="finishQueue(currentQueue.id_antrian)" class="bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white p-3 rounded-xl font-bold shadow-lg transform hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-circle-check text-lg"></i>
                                <span class="text-left leading-none text-sm">
                                    SELESAI<br>
                                    <span class="text-[9px] font-normal opacity-80">Transaksi Beres</span>
                                </span>
                            </button>

                            <button @click="skipQueue(currentQueue.id_antrian)" class="bg-rose-100 hover:bg-rose-200 dark:bg-rose-900/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-200 border border-rose-200 dark:border-rose-800 p-2 rounded-xl font-bold transition-all text-[10px] flex items-center justify-center gap-1 mt-1">
                                <i class="fa-solid fa-forward"></i> Lewati
                            </button>
                        </div>
                    </div>
                    
                    <!-- RIGHT COLUMN: Transaction Detail -->
                    <div class="w-full md:w-3/4 flex-grow">
                        
                        <!-- CS Form (Conditional) -->
                        <div v-if="type === 'CS'" class="h-full bg-gray-50 dark:bg-slate-800/50 p-6 rounded-xl border border-gray-200 dark:border-slate-700 transition-colors">
                             <h4 class="text-blue-600 dark:text-blue-400 font-bold uppercase text-sm mb-4"><i class="fa-solid fa-pen-to-square mr-2"></i> Input Data Pelayanan</h4>
                             <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs uppercase text-gray-500 dark:text-slate-400 font-bold mb-1">Nama Nasabah</label>
                                        <input v-model="form.nama" type="text" class="w-full bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded p-3 text-sm border border-gray-300 dark:border-slate-600 focus:border-blue-500 focus:outline-none transition-colors" placeholder="Isi nama nasabah...">
                                    </div>
                                    <div>
                                        <label class="block text-xs uppercase text-gray-500 dark:text-slate-400 font-bold mb-1">No Kontak</label>
                                        <input v-model="form.no_kontak" type="text" class="w-full bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded p-3 text-sm border border-gray-300 dark:border-slate-600 focus:border-blue-500 focus:outline-none transition-colors" placeholder="08...">
                                    </div>
                                </div>
                                <div>
                                     <label class="block text-xs uppercase text-gray-500 dark:text-slate-400 font-bold mb-1">Tujuan Datang</label>
                                     <textarea v-model="form.tujuan_datang" class="w-full bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded p-3 text-sm border border-gray-300 dark:border-slate-600 focus:border-blue-500 focus:outline-none transition-colors" rows="3" placeholder="Keperluan..."></textarea>
                                </div>
                                <div>
                                     <label class="block text-xs uppercase text-gray-500 dark:text-slate-400 font-bold mb-1">Solusi / Tindakan</label>
                                     <textarea v-model="form.solusi" class="w-full bg-white dark:bg-slate-700 text-gray-900 dark:text-white rounded p-3 text-sm border border-gray-300 dark:border-slate-600 focus:border-blue-500 focus:outline-none transition-colors" rows="3" placeholder="Tindakan..."></textarea>
                                </div>
                             </div>
                        </div>

                        <!-- Teller Transaction Detail -->
                        <div v-if="isValidTellerTransaction" class="h-full bg-gray-50 dark:bg-slate-800/80 p-5 rounded-xl border border-gray-200 dark:border-slate-600 shadow-sm dark:shadow-2xl backdrop-blur-sm relative overflow-hidden flex flex-col transition-colors">
                            
                            <!-- Dynamic Accent -->
                            <div class="absolute top-0 left-0 w-1.5 h-full"
                                 :class="{
                                    'bg-emerald-500': isSetor,
                                    'bg-rose-500': isTarik,
                                    'bg-indigo-500': isTransfer
                                 }"></div>

                            <!-- Header -->
                            <div class="flex justify-between items-start border-b border-gray-200 dark:border-slate-700 pb-3 mb-4 pl-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg transform rotate-3"
                                         :class="{
                                            'bg-emerald-500 text-white': isSetor,
                                            'bg-rose-500 text-white': isTarik,
                                            'bg-indigo-500 text-white': isTransfer
                                         }">
                                        <i class="fa-solid text-xl"
                                           :class="{
                                            'fa-arrow-down': isSetor,
                                            'fa-arrow-up': isTarik,
                                            'fa-paper-plane': isTransfer
                                           }"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 dark:text-white text-lg leading-tight">{{ transactionData.tx_type }}</h4>
                                        <div class="flex gap-3 text-xs font-mono text-gray-500 dark:text-slate-400 mt-0.5">
                                            <span><i class="fa-solid fa-barcode mr-1"></i> {{ transactionData.transaction?.token }}</span>
                                            <span><i class="fa-regular fa-calendar mr-1"></i> {{ new Date(transactionData.transaction?.created).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-3xl font-black tracking-tighter"
                                          :class="{
                                            'text-emerald-600 dark:text-emerald-400': isSetor,
                                            'text-rose-600 dark:text-rose-400': isTarik,
                                            'text-indigo-600 dark:text-indigo-400': isTransfer
                                          }">
                                        Rp {{ Number(transactionData.transaction?.nominal).toLocaleString('id-ID') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400 italic max-w-[300px] ml-auto leading-tight">{{ transactionData.transaction?.terbilang }}</div>
                                </div>
                            </div>
                            
                            <!-- Main Content Grid -->
                            <div class="grid grid-cols-12 gap-5 pl-4 flex-grow">
                                
                                <!-- LEFT PANE: Account Info -->
                                <div class="col-span-12 md:col-span-5 space-y-4 border-r border-gray-200 dark:border-slate-700/50 pr-5">
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-gray-500 dark:text-slate-500 tracking-wider mb-2 block">
                                            {{ isSetor ? 'Rekening Tujuan' : 'Rekening Sumber' }}
                                        </label>
                                        <div class="bg-white dark:bg-slate-700/30 p-3 rounded-xl border border-gray-200 dark:border-slate-600/30 shadow-sm dark:shadow-none">
                                            <div class="text-gray-800 dark:text-white font-bold text-lg mb-1 leading-tight">{{ transactionData.transaction?.nama || '-' }}</div>
                                            <div class="flex items-center gap-2 text-gray-600 dark:text-slate-300 font-mono text-sm mt-1">
                                                <i class="fa-solid fa-credit-card opacity-50"></i>
                                                {{ transactionData.transaction?.no_rek || '-' }}
                                            </div>
                                            <div v-if="transactionData.transaction?.jenis_rekening" class="mt-2">
                                                <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-slate-400 tracking-wider mr-2">Jenis:</span>
                                                <span class="bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-300 text-[10px] px-2 py-0.5 rounded border border-blue-200 dark:border-blue-500/30 font-bold uppercase">
                                                    {{ transactionData.transaction?.jenis_rekening }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="isTransfer">
                                        <label class="text-[10px] uppercase font-bold text-indigo-500 dark:text-indigo-300 tracking-wider mb-2 block">
                                            <i class="fa-solid fa-bullseye mr-1"></i> Tujuan Transfer
                                        </label>
                                        <div class="bg-indigo-50 dark:bg-indigo-500/10 p-3 rounded-xl border border-indigo-200 dark:border-indigo-500/20">
                                            <div class="text-gray-800 dark:text-white font-bold text-base mb-1">{{ transactionData.transaction?.nama_tujuan }}</div>
                                            <div class="text-indigo-700 dark:text-indigo-200 text-sm mb-1">{{ transactionData.transaction?.bank_tujuan }}</div>
                                            <div class="font-mono text-xs text-indigo-600 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-500/10 px-2 py-0.5 rounded inline-block">
                                                {{ transactionData.transaction?.no_rek_tujuan }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Logics -->
                                    <div v-if="transactionData.transaction?.tujuan">
                                        <label class="text-[10px] uppercase font-bold text-gray-500 dark:text-slate-500 tracking-wider mb-2 block">Tujuan Transaksi</label>
                                        <div class="text-sm text-gray-700 dark:text-white font-medium border-l-2 border-gray-300 dark:border-slate-600 pl-3 py-1">
                                            {{ transactionData.transaction?.tujuan }}
                                        </div>
                                    </div>
                                    <div v-else-if="transactionData.transaction?.berita || transactionData.transaction?.berita_tujuan">
                                        <label class="text-[10px] uppercase font-bold text-gray-500 dark:text-slate-500 tracking-wider mb-2 block">Keterangan</label>
                                        <div class="text-sm text-gray-600 dark:text-slate-300 italic border-l-2 border-gray-300 dark:border-slate-600 pl-3 py-1">
                                            "{{ transactionData.transaction?.berita || transactionData.transaction?.berita_tujuan }}"
                                        </div>
                                    </div>
                                    
                                    <!-- TELLER INPUTS (TRANSFER) -->
                                    <div v-if="isTransfer" class="mt-4 bg-gray-50 dark:bg-slate-700/30 p-3 rounded-xl border border-dashed border-gray-300 dark:border-slate-600">
                                        <label class="text-[10px] uppercase font-bold text-yellow-600 dark:text-yellow-400 tracking-wider mb-2 block">
                                            <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Nominal (Khusus Transfer)
                                        </label>
                                        
                                        <div class="space-y-3">
                                            <div>
                                                <label class="text-[10px] text-gray-500 dark:text-slate-400 block mb-1">Nominal Transfer</label>
                                                <div class="relative">
                                                    <span class="absolute left-2 top-2 text-xs text-gray-400">Rp</span>
                                                    <input type="number" v-model="form.nominal" class="w-full bg-white dark:bg-slate-800 text-gray-800 dark:text-white text-xs rounded p-2 pl-8 border border-gray-300 dark:border-slate-600 focus:border-blue-500 outline-none transition-colors" />
                                                </div>
                                            </div>
                                            <div>
                                                <label class="text-[10px] text-gray-500 dark:text-slate-400 block mb-1">Biaya Transfer</label>
                                                <div class="relative">
                                                    <span class="absolute left-2 top-2 text-xs text-gray-400">Rp</span>
                                                    <input type="number" v-model="form.biaya_trf" class="w-full bg-white dark:bg-slate-800 text-gray-800 dark:text-white text-xs rounded p-2 pl-8 border border-gray-300 dark:border-slate-600 focus:border-blue-500 outline-none transition-colors" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TELLER INPUTS (TARIK TUNAI / WITHDRAWAL) -->
                                    <div v-if="isTarik" class="mt-4 bg-gray-50 dark:bg-slate-700/30 p-3 rounded-xl border border-dashed border-gray-300 dark:border-slate-600">
                                        <label class="text-[10px] uppercase font-bold text-blue-600 dark:text-blue-400 tracking-wider mb-2 block">
                                            <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Data Penerima (Tarik Tunai)
                                        </label>
                                        
                                        <div class="space-y-3">
                                            <div>
                                                <label class="text-[10px] text-gray-500 dark:text-slate-400 block mb-1">Nama Penerima</label>
                                                <input type="text" v-model="form.nama_penarik" class="w-full bg-white dark:bg-slate-800 text-gray-800 dark:text-white text-xs rounded p-2 border border-gray-300 dark:border-slate-600 focus:border-blue-500 outline-none transition-colors" />
                                            </div>
                                            <div>
                                                <label class="text-[10px] text-gray-500 dark:text-slate-400 block mb-1">No. Identitas</label>
                                                <input type="text" v-model="form.noid_penarik" class="w-full bg-white dark:bg-slate-800 text-gray-800 dark:text-white text-xs rounded p-2 border border-gray-300 dark:border-slate-600 focus:border-blue-500 outline-none transition-colors" />
                                            </div>
                                            <div>
                                                <label class="text-[10px] text-gray-500 dark:text-slate-400 block mb-1">No. WhatsApp / HP</label>
                                                <input type="text" v-model="form.hp_penarik" class="w-full bg-white dark:bg-slate-800 text-gray-800 dark:text-white text-xs rounded p-2 border border-gray-300 dark:border-slate-600 focus:border-blue-500 outline-none transition-colors" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT PANE: Actor -->
                                <div class="col-span-12 md:col-span-7 space-y-4">
                                     <label class="text-[10px] uppercase font-bold text-gray-500 dark:text-slate-500 tracking-wider block border-b border-gray-200 dark:border-slate-700/50 pb-1 mb-2">
                                        Data {{ isTransfer ? 'Pengirim' : (isSetor ? 'Penyetor' : 'Penarik') }}
                                    </label>
                                    
                                    <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                                        <div class="col-span-2">
                                            <span class="text-gray-500 dark:text-slate-500 text-xs block mb-0.5">Nama {{ isTransfer ? 'Pengirim' : (isSetor ? 'Penyetor' : 'Penarik') }}</span>
                                            <span class="text-gray-800 dark:text-white font-bold text-xl border-b border-gray-200 dark:border-slate-700 pb-1 block w-full tracking-wide">{{ activeActorName }}</span>
                                        </div>
                                        
                                        <div>
                                            <span class="text-gray-500 dark:text-slate-500 text-xs block mb-0.5">Nomor Handphone</span>
                                            <span class="text-gray-700 dark:text-white font-mono bg-gray-100 dark:bg-slate-900/30 px-2 py-1 rounded border border-gray-200 dark:border-slate-700/50 text-sm">{{ activeActorPhone }}</span>
                                        </div>

                                        <div v-if="activeActorIdentity">
                                            <span class="text-gray-500 dark:text-slate-500 text-xs block mb-0.5">No. Identitas</span>
                                             <span class="text-gray-700 dark:text-white font-mono bg-gray-100 dark:bg-slate-900/30 px-2 py-1 rounded border border-gray-200 dark:border-slate-700/50 text-sm">{{ activeActorIdentity }}</span>
                                        </div>
                                        
                                        <div class="col-span-2">
                                            <span class="text-gray-500 dark:text-slate-500 text-xs block mb-0.5">Alamat Lengkap</span>
                                            <div class="text-gray-600 dark:text-slate-300 text-sm leading-snug bg-gray-50 dark:bg-slate-900/20 p-2.5 rounded border border-gray-200 dark:border-slate-700/30">
                                                {{ activeActorAddress }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="pt-2 mt-auto">
                                        <button @click="printSlip()" class="w-full bg-white hover:bg-gray-50 dark:hover:bg-slate-200 text-slate-900 py-3 rounded-lg font-bold text-sm transition-all shadow-md group-hover:shadow-lg border border-gray-200 flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-print"></i> CETAK / VALIDASI SLIP
                                        </button>
                                        <p class="text-center text-[10px] text-gray-400 dark:text-slate-500 mt-2">{{ isTarik ? '*Print 2 halaman landscape' : '*Mencetak struk untuk printer 80mm' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CASE 2: IDLE -->
            <div v-else class="flex flex-col items-center justify-center text-center py-12">
                <div class="bg-gray-50 dark:bg-slate-800/50 rounded-full p-6 mb-4 transition-colors">
                    <i class="fa-solid fa-user-clock text-4xl text-blue-500 dark:text-blue-400"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2 transition-colors">Siap Melayani</h2>
                <p class="text-gray-500 dark:text-slate-400 mb-6 transition-colors">Silakan panggil antrian berikutnya.</p>
                
                <button v-if="queues.length > 0" @click="callNext" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-4 rounded-2xl font-bold shadow-lg transform hover:scale-105 transition-all text-lg animate-pulse">
                    <i class="fa-solid fa-bullhorn mr-2"></i> PANGGIL ANTRIAN BERIKUTNYA
                </button>
                 <p v-else class="text-gray-400 dark:text-slate-500 italic transition-colors">Belum ada antrian menunggu.</p>
            </div>
            
        </div>

        <!-- Waiting List -->
        <h3 class="text-xl font-bold text-gray-700 dark:text-slate-300 mb-4 flex items-center transition-colors">
            <i class="fa-regular fa-clock mr-2"></i> Daftar Antrian
        </h3>
        
        <div v-if="loading" class="text-center py-12">
            <i class="fa-solid fa-circle-notch fa-spin text-4xl text-blue-500"></i>
        </div>

        <div v-else-if="queues.length === 0" class="bg-white dark:bg-slate-800 rounded-xl p-12 text-center border border-gray-200 dark:border-slate-700 mb-8 transition-colors">
            <i class="fa-solid fa-mug-hot text-6xl text-gray-300 dark:text-slate-600 mb-4 transition-colors"></i>
            <p class="text-gray-500 dark:text-slate-400 text-lg transition-colors">Tidak ada antrian menunggu.</p>
        </div>

        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-10">
            <div v-for="q in queues" :key="q.id_antrian" 
                 class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-gray-200 dark:border-slate-700 relative overflow-hidden opacity-90 hover:opacity-100 transition-all shadow-sm">

                <div class="flex flex-col items-center text-center">
                    <span class="text-3xl font-bold text-gray-800 dark:text-white mb-2 transition-colors">{{ q.antrian }}</span>
                    <span v-if="getTxType(q.kode)" class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-2 transition-colors">{{ getTxType(q.kode) }}</span>
                    <span class="text-sm text-blue-600 dark:text-blue-200 font-medium truncate w-full transition-colors">{{ q.nama_antrian }}</span>
                    <span class="text-xs text-gray-400 dark:text-slate-500 mt-2 transition-colors"><i class="fa-regular fa-clock mr-1"></i> {{ formatTime(q.created_at) }}</span>
                </div>
            </div>
        </div>

        <!-- PRINT AREA (Wrapper) -->
        <div id="print-wrapper" class="hidden print:block fixed top-0 left-0 w-full h-full bg-white z-[9999] p-0 text-black">
            
            <!-- 1. STANDARD THERMAL (80mm) -->
            <div id="print-area-standard" v-if="!isSetor && !isTarik && !isTransfer" class="w-[80mm] mx-auto p-4 font-mono text-xs leading-tight">
                <div class="text-center mb-4 pb-2 border-b-2 border-black border-dashed">
                    <img src="/img/logo_mci.png" class="h-10 mx-auto mb-1" alt="BPRS HIK MCI">
                    <p class="scale-90">Jl. Kaliurang KM 9, Yogyakarta</p>
                    <p class="scale-90">Telp: (0274) 123456</p>
                </div>

                <div v-if="transactionData && transactionData.transaction">
                    <div class="mb-3 space-y-1">
                        <div class="flex justify-between font-bold">
                            <span>{{ transactionData.tx_type }}</span>
                            <span>{{ transactionData.transaction.token }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ new Date().toLocaleDateString('id-ID') }}</span>
                            <span>{{ new Date().toLocaleTimeString('id-ID') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Print styles with complete isolation from Tailwind/global CSS */
@media print {
    /* Hide everything except print wrapper */
    body * { 
        visibility: hidden !important; 
    }
    
    #print-wrapper, 
    #print-wrapper * { 
        visibility: visible !important; 
    }
    
    /* Position print wrapper */
    #print-wrapper {
        position: fixed !important; 
        left: 0 !important; 
        top: 0 !important; 
        margin: 0 !important; 
        padding: 0 !important; 
        background: white !important;
        width: auto !important;
        height: auto !important;
    }
    
    /* CRITICAL: Reset ALL styles that might interfere */
    #print-area-setor,
    #print-area-setor *,
    #print-area-tarik,
    #print-area-tarik *,
    #print-area-transfer,
    #print-area-transfer *,
    .print-slip,
    .print-slip * {
        /* Reset box model */
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        outline: none !important;
        
        /* Reset text styles */
        color: #000 !important; /* Ensure black text */
        text-decoration: none !important;
        text-transform: none !important;
        letter-spacing: normal !important;
        word-spacing: normal !important;
        line-height: normal !important;
        
        /* Reset display/positioning - will be overridden by inline styles */
        display: block !important;
        position: static !important;
        
        /* Disable Tailwind utilities */
        transform: none !important;
        transition: none !important;
        animation: none !important;
        filter: none !important;
        opacity: 1 !important;
    }
    
    /* Restore positioning for fields with inline position: absolute */
    #print-area-setor > div > div[style*="position: absolute"],
    #print-area-tarik > div > div[style*="position: absolute"],
    #print-area-transfer > div > div[style*="position: absolute"] {
        position: absolute !important;
    }
    
    /* Ensure parent containers use position: relative for absolute children */
    #print-area-setor > div,
    #print-area-tarik > div,
    #print-area-transfer > div {
        position: relative !important;
    }
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
            currentQueue: null,
            loading: false,
            // Form Data for CS details
            form: {
                nama: '',
                no_kontak: '',
                tujuan_datang: '',
                solusi: '',
                // Transfer specific
                metode_transfer: '',
                mata_uang: 'IDR',
                sumber_dana: 'Tunai',
                nominal: 0,
                biaya_trf: 0,
                nama_penarik: '',
                noid_penarik: '',
                hp_penarik: ''
            },
            transactionData: null
        }
    },
    watch: {
        type: {
            immediate: true,
            handler() {
                this.fetchQueues();
            }
        }
    },
    computed: {
        isValidTellerTransaction() {
            return this.type && this.type.toLowerCase() === 'teller' && this.transactionData;
        },
        // Helper to determine type safely
        isSetor() { return this.isValidTellerTransaction && this.transactionData.tx_type === 'Setor Tunai'; },
        isTarik() { return this.isValidTellerTransaction && this.transactionData.tx_type === 'Tarik Tunai'; },
        isTransfer() { return this.isValidTellerTransaction && this.transactionData.tx_type === 'Transfer'; },
        
        // Helper to get Actor Name (Depositor/Withdrawer/Sender)
        activeActorName() {
            if(!this.transactionData?.transaction) return '-';
            const t = this.transactionData.transaction;
            if (this.isTransfer) return t.nama; // Pengirim
            if (this.isTarik) return t.nama_penarik;
            return t.nama_penyetor || '-';
        },
        // Helper to get Actor Phone
        activeActorPhone() {
            if(!this.transactionData?.transaction) return '-';
            const t = this.transactionData.transaction;
            if (this.isTarik) return t.hp_penarik;
            return t.hp_penyetor || '-';
        },
        // Helper to get Actor Address
        activeActorAddress() {
            if(!this.transactionData?.transaction) return '-';
            const t = this.transactionData.transaction;
            if (this.isTarik) return t.alamat_penarik;
            return t.alamat_penyetor || '-';
        },
        // Helper to get Actor ID (KTP)
        activeActorIdentity() {
            if(!this.transactionData?.transaction) return null;
            const t = this.transactionData.transaction;
            return t.noid_penyetor || t.noid_penarik || null;
        }
    },
    methods: {
        printTarikTunai() {
            // NATIVE LARAVEL PDF REDIRECT (2-PAGE DUPLEX)
            if (!this.transactionData || !this.transactionData.transaction) {
                Swal.fire({
                    icon: 'error',
                    title: 'Data Tidak Tersedia',
                    text: 'Tidak ada data transaksi untuk dicetak'
                });
                return;
            }

            const token = this.transactionData.transaction.token;
            // Native Laravel route defined in web.php
            const printUrl = `/admin/print/tarik/${token}`;
            
            // Open in a new tab (PDF will open in Adobe/built-in viewer for reliable 2-page print)
            window.open(printUrl, '_blank');
        },
        async printSlip() {
            console.log("Print Slip Debug:");
            console.log("Transaction Data:", this.transactionData);

            if (!this.transactionData || !this.transactionData.transaction) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Data transaksi tidak lengkap' });
                return;
            }

            const token = this.transactionData.transaction.token;

            // FOR SLIP TYPES (Setor & Transfer), USE NATIVE LARAVEL PDF
            if (this.isTransfer) {
                try {
                    await axios.post(`/admin/queue/update-transfer/${token}`, {
                        nominal: this.form.nominal,
                        biaya_trf: this.form.biaya_trf
                    });
                } catch (error) {
                    console.error("Error saving transfer changes:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Update',
                        text: 'Terjadi kesalahan saat menyimpan perubahan nominal.',
                    });
                    return;
                }
                
                const printUrl = `/admin/print/transfer/${token}`;
                window.open(printUrl, '_blank');
                return;
            } else if (this.isTarik) {
                try {
                    await axios.post(`/admin/queue/update-withdrawal/${token}`, {
                        nama_penarik: this.form.nama_penarik,
                        noid_penarik: this.form.noid_penarik,
                        hp_penarik: this.form.hp_penarik
                    });
                } catch (error) {
                    console.error("Error saving withdrawal changes:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Update',
                        text: 'Terjadi kesalahan saat menyimpan perubahan data penarik.',
                    });
                    return;
                }
                const printUrl = `/admin/print/tarik/${token}`;
                window.open(printUrl, '_blank');
                return;
            } else if (this.isSetor) {
                const printUrl = `/admin/print/setor/${token}`;
                window.open(printUrl, '_blank');
                return;
            }


            // FALLBACK TO STANDARD THERMAL FOR OTHERS (Informasi, etc)
            // 1. Determine Global Print Style for Page Size
            const styleId = 'dynamic-print-style';
            let styleEl = document.getElementById(styleId);
            
            if (!styleEl) {
                styleEl = document.createElement('style');
                styleEl.id = styleId;
                document.head.appendChild(styleEl);
            }

            // STANDARD THERMAL (80mm x 100mm Absolute Top-Left Optimized)
            styleEl.innerHTML = `
                @page {
                    size: 80mm 100mm;
                    margin: 0 !important;
                }
                #print-area-standard { 
                    display: block !important; 
                    width: 72mm !important; /* Printable area */
                    margin: 0 !important; 
                    padding: 0 !important;
                    text-align: center !important;
                    position: absolute !important;
                    top: 0 !important;
                    left: 0 !important;
                }
                #print-wrapper { 
                    width: 80mm !important; 
                    height: 100mm !important; 
                    display: block !important; 
                    margin: 0 !important;
                    padding: 0 !important;
                    position: absolute !important;
                    top: 0 !important;
                    left: 0 !important;
                }
            `;

            // 3. Print
            setTimeout(() => {
                const el = document.getElementById('print-area-standard');
                if(el) el.style.display = 'block';

                window.print();
                
                setTimeout(() => {
                    if (styleEl) styleEl.innerHTML = ''; // Reset CSS
                }, 1000);
            }, 200);
        },
        async callNext() {
            if (this.queues.length > 0) {
                // Call the first one in the list (FIFO)
                this.callQueue(this.queues[0].id_antrian);
            }
        },
        async confirmCall(queue) {
             const result = await Swal.fire({
                title: `Panggil ${queue.antrian}?`,
                text: `Melompati antrian dan memanggil nasabah ini?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'Ya, Panggil!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                this.callQueue(queue.id_antrian);
            }
        },
        async fetchQueues() {
            this.loading = true;
            try {
                // Correct URL: /admin/queue-v2 (defined in web.php, typical for Laravel 11 web-auth SPA)
                const response = await axios.get(`/admin/queue-v2?type=${this.type}`);
                const allQueues = response.data;
                
                // Filter: Status 2 (Called) goes to 'currentServing', Status 0 (Waiting) goes to list
                const serving = allQueues.find(q => q.st_antrian == 2);
                
                // If serving changed (or initializing), reset form unless it's the same queue
                if (serving && (!this.currentQueue || this.currentQueue.id_antrian !== serving.id_antrian)) {
                    this.form = { nama: '', no_kontak: '', tujuan_datang: '', solusi: '' };
                }
                
                this.currentQueue = serving || null;
                this.queues = allQueues.filter(q => q.st_antrian == 0);

                // If Teller and Serving, fetch FULL Details (Case Insensitive)
                if (this.currentQueue && this.type && this.type.toLowerCase() === 'teller') {
                    this.fetchQueueDetail(this.currentQueue.id_antrian);
                } else {
                    this.transactionData = null; 
                }
                
            } catch (error) {
                console.error("Error fetching queues", error);
            } finally {
                this.loading = false;
            }
        },
        async fetchQueueDetail(id) {
            try {
                const response = await axios.get(`/admin/queue/detail/${id}`);
                this.transactionData = response.data; // { queue, transaction, tx_type }
                
                // If it's a transfer, initialize the form with current values
                if (this.isTransfer && this.transactionData.transaction) {
                    this.form.nominal = this.transactionData.transaction.nominal;
                    this.form.biaya_trf = this.transactionData.transaction.biaya_trf || 0;
                }
                // If it's a withdrawal, initialize the form with current values
                if (this.isTarik && this.transactionData.transaction) {
                    this.form.nama_penarik = this.transactionData.transaction.nama_penarik;
                    this.form.noid_penarik = this.transactionData.transaction.noid_penarik;
                    this.form.hp_penarik = this.transactionData.transaction.hp_penarik;
                }
            } catch (error) {
                console.error("Error fetching detail:", error);
            }
        },
        async callQueue(id) {
            try {
                await axios.post(`/admin/queue/call/${id}`);
                this.fetchQueues(); // Refresh list
            } catch (error) {
                console.error("Error call:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Panggil',
                    text: error.response?.data?.message || error.message || 'Terjadi kesalahan sistem',
                    confirmButtonText: 'Tutup'
                });
            }
        },
        async finishQueue(id) {
             const result = await Swal.fire({
                title: 'Selesaikan Antrian?',
                text: "Pastikan data pelayanan telah terisi.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                try {
                    // Send form data if CS OR Transfer
                    const payload = { ...this.form }; // Send everything, backend will filter/use what's needed
                    
                    console.log("Sending Finish Payload:", payload);
                    
                    await axios.post(`/admin/queue/finish/${id}`, payload);
                    
                    Swal.fire({
                        title: 'Selesai!',
                        text: 'Antrian telah diselesaikan.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Reset form
                    this.form = { 
                        nama: '', no_kontak: '', tujuan_datang: '', solusi: '',
                        metode_transfer: '', mata_uang: 'IDR', sumber_dana: 'Tunai'
                    };
                    
                    // Auto Call Next Logic
                    if (this.queues.length > 0) {
                         const nextId = this.queues[0].id_antrian;
                         
                         const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                         });
                         Toast.fire({ icon: 'info', title: 'Antrian selesai. Memanggil selanjutnya...' });

                         this.callQueue(nextId);
                    } else {
                        this.fetchQueues(); // Just refresh if empty
                    }
                } catch (error) {
                     console.error("Error finish:", error);
                     Swal.fire({
                        icon: 'error',
                        title: 'Gagal Selesai',
                        text: error.response?.data?.message || error.message || 'Terjadi kesalahan sistem',
                        confirmButtonText: 'Tutup'
                    });
                }
            }
        },
        async skipQueue(id) {
             const result = await Swal.fire({
                title: 'Lewati Antrian?',
                text: "Berikan alasan mengapa antrian dilewati ",
                input: 'text',
                inputPlaceholder: 'Alasan (Opsional)',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Lewati!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                try {
                    await axios.post(`/admin/queue/skip/${id}`, {
                        solusi: result.value // Send reason as 'solusi'
                    });
                    
                    Swal.fire({
                        title: 'Dilewati!',
                        text: 'Antrian telah dilewati.',
                        icon: 'info',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Reset form just in case
                    this.form = { nama: '', no_kontak: '', tujuan_datang: '', solusi: '' };

                    // Auto Call Next Logic
                    if (this.queues.length > 0) {
                         const nextId = this.queues[0].id_antrian;
                         
                         const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                         });
                         Toast.fire({ icon: 'info', title: 'Antrian dilewati. Memanggil selanjutnya...' });
                         
                         this.callQueue(nextId);
                    } else {
                        this.fetchQueues(); // Just refresh if empty
                    }
                } catch (error) {
                    console.error("Error skip:", error);
                     Swal.fire({
                        icon: 'error',
                        title: 'Gagal Lewati',
                        text: error.response?.data?.message || error.message || 'Terjadi kesalahan sistem',
                        confirmButtonText: 'Tutup'
                    });
                }
            }
        },
        getTxType(kode) {
            if (!kode) return null;
            if (kode.startsWith('ST-')) return 'Setor Tunai';
            if (kode.startsWith('TT-')) return 'Tarik Tunai';
            if (kode.startsWith('ON-')) return 'Transfer';
            if (kode.startsWith('CS-')) return 'Customer Service';
            return null;
        },

        formatTime(dateString) {
            if(!dateString) return '-';
            return new Date(dateString).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
    }
}
</script>
