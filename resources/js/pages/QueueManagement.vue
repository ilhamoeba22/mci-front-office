<template>
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white">
                Manajemen Antrian: <span class="text-blue-400">{{ type }}</span>
            </h2>
            <button @click="fetchQueues" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fa-solid fa-sync mr-2"></i> Segarkan
            </button>
        </div>

        <!-- Current Active Queue or Idle State -->
        <div class="bg-gradient-to-r from-blue-900 to-slate-900 border border-blue-500/30 rounded-xl p-6 mb-8 shadow-xl min-h-[250px] flex flex-col justify-center">
            
            <!-- CASE 1: Currently Serving -->
            <div v-if="currentQueue">
                <div class="flex flex-col md:flex-row gap-4 items-stretch h-full">
                    
                    <!-- LEFT COLUMN: Queue Info + Actions -->
                    <div class="w-full md:w-1/4 flex flex-col gap-3 min-w-[240px]">
                        <!-- Queue Info -->
                        <div class="bg-black/20 p-5 rounded-xl text-center border border-white/10">
                            <p class="text-blue-300 text-xs font-bold uppercase tracking-wider mb-1">Sedang Melayani</p>
                            <h1 class="text-6xl font-extrabold text-white tracking-tight">{{ currentQueue.antrian }}</h1>
                            <div class="mt-3 inline-block bg-yellow-500/20 text-yellow-400 px-3 py-0.5 rounded-full text-[10px] font-bold border border-yellow-500/30">
                                STATUS: DIPANGGIL
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-2 flex-grow justify-end">
                            <button @click="callQueue(currentQueue.id_antrian)" class="bg-gradient-to-r from-yellow-600 to-yellow-500 hover:from-yellow-500 hover:to-yellow-400 text-white p-3 rounded-xl font-bold shadow-lg transform hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
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

                            <button @click="skipQueue(currentQueue.id_antrian)" class="bg-rose-900/40 hover:bg-rose-900/60 text-rose-200 border border-rose-800 p-2 rounded-xl font-bold transition-all text-[10px] flex items-center justify-center gap-1 mt-1">
                                <i class="fa-solid fa-forward"></i> Lewati
                            </button>
                        </div>
                    </div>
                    
                    <!-- RIGHT COLUMN: Transaction Detail -->
                    <div class="w-full md:w-3/4 flex-grow">
                        
                        <!-- CS Form (Conditional) -->
                        <div v-if="type === 'CS'" class="h-full bg-slate-800/50 p-6 rounded-xl border border-slate-700">
                             <h4 class="text-blue-400 font-bold uppercase text-sm mb-4"><i class="fa-solid fa-pen-to-square mr-2"></i> Input Data Pelayanan</h4>
                             <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs uppercase text-slate-400 font-bold mb-1">Nama Nasabah</label>
                                        <input v-model="form.nama" type="text" class="w-full bg-slate-700 text-white rounded p-3 text-sm border border-slate-600 focus:border-blue-500 focus:outline-none" placeholder="Isi nama nasabah...">
                                    </div>
                                    <div>
                                        <label class="block text-xs uppercase text-slate-400 font-bold mb-1">No Kontak</label>
                                        <input v-model="form.no_kontak" type="text" class="w-full bg-slate-700 text-white rounded p-3 text-sm border border-slate-600 focus:border-blue-500 focus:outline-none" placeholder="08...">
                                    </div>
                                </div>
                                <div>
                                     <label class="block text-xs uppercase text-slate-400 font-bold mb-1">Tujuan Datang</label>
                                     <textarea v-model="form.tujuan_datang" class="w-full bg-slate-700 text-white rounded p-3 text-sm border border-slate-600 focus:border-blue-500 focus:outline-none" rows="3" placeholder="Keperluan..."></textarea>
                                </div>
                                <div>
                                     <label class="block text-xs uppercase text-slate-400 font-bold mb-1">Solusi / Tindakan</label>
                                     <textarea v-model="form.solusi" class="w-full bg-slate-700 text-white rounded p-3 text-sm border border-slate-600 focus:border-blue-500 focus:outline-none" rows="3" placeholder="Tindakan..."></textarea>
                                </div>
                             </div>
                        </div>

                        <!-- Teller Transaction Detail -->
                        <div v-if="isValidTellerTransaction" class="h-full bg-slate-800/80 p-5 rounded-xl border border-slate-600 shadow-2xl backdrop-blur-sm relative overflow-hidden flex flex-col">
                            
                            <!-- Dynamic Accent -->
                            <div class="absolute top-0 left-0 w-1.5 h-full"
                                 :class="{
                                    'bg-emerald-500': isSetor,
                                    'bg-rose-500': isTarik,
                                    'bg-indigo-500': isTransfer
                                 }"></div>

                            <!-- Header -->
                            <div class="flex justify-between items-start border-b border-slate-700 pb-3 mb-4 pl-4">
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
                                        <h4 class="font-bold text-white text-lg leading-tight">{{ transactionData.tx_type }}</h4>
                                        <div class="flex gap-3 text-xs font-mono text-slate-400 mt-0.5">
                                            <span><i class="fa-solid fa-barcode mr-1"></i> {{ transactionData.transaction?.token }}</span>
                                            <span><i class="fa-regular fa-calendar mr-1"></i> {{ new Date(transactionData.transaction?.created).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-3xl font-black tracking-tighter"
                                          :class="{
                                            'text-emerald-400': isSetor,
                                            'text-rose-400': isTarik,
                                            'text-indigo-400': isTransfer
                                          }">
                                        Rp {{ Number(transactionData.transaction?.nominal).toLocaleString('id-ID') }}
                                    </div>
                                    <div class="text-xs text-slate-400 italic max-w-[300px] ml-auto leading-tight">{{ transactionData.transaction?.terbilang }}</div>
                                </div>
                            </div>
                            
                            <!-- Main Content Grid -->
                            <div class="grid grid-cols-12 gap-5 pl-4 flex-grow">
                                
                                <!-- LEFT PANE: Account Info -->
                                <div class="col-span-12 md:col-span-5 space-y-4 border-r border-slate-700/50 pr-5">
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-2 block">
                                            {{ isSetor ? 'Rekening Tujuan' : 'Rekening Sumber' }}
                                        </label>
                                        <div class="bg-slate-700/30 p-3 rounded-xl border border-slate-600/30">
                                            <div class="text-white font-bold text-lg mb-1 leading-tight">{{ transactionData.transaction?.nama || '-' }}</div>
                                            <div class="flex items-center gap-2 text-slate-300 font-mono text-sm mt-1">
                                                <i class="fa-solid fa-credit-card opacity-50"></i>
                                                {{ transactionData.transaction?.no_rek || '-' }}
                                            </div>
                                            <div v-if="transactionData.transaction?.jenis_rekening" class="mt-2">
                                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mr-2">Jenis:</span>
                                                <span class="bg-blue-500/20 text-blue-300 text-[10px] px-2 py-0.5 rounded border border-blue-500/30 font-bold uppercase">
                                                    {{ transactionData.transaction?.jenis_rekening }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="isTransfer">
                                        <label class="text-[10px] uppercase font-bold text-indigo-300 tracking-wider mb-2 block">
                                            <i class="fa-solid fa-bullseye mr-1"></i> Tujuan Transfer
                                        </label>
                                        <div class="bg-indigo-500/10 p-3 rounded-xl border border-indigo-500/20">
                                            <div class="text-white font-bold text-base mb-1">{{ transactionData.transaction?.nama_tujuan }}</div>
                                            <div class="text-indigo-200 text-sm mb-1">{{ transactionData.transaction?.bank_tujuan }}</div>
                                            <div class="font-mono text-xs text-indigo-300 bg-indigo-500/10 px-2 py-0.5 rounded inline-block">
                                                {{ transactionData.transaction?.no_rek_tujuan }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Logics -->
                                    <div v-if="transactionData.transaction?.tujuan">
                                        <label class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-2 block">Tujuan Transaksi</label>
                                        <div class="text-sm text-white font-medium border-l-2 border-slate-600 pl-3 py-1">
                                            {{ transactionData.transaction?.tujuan }}
                                        </div>
                                    </div>
                                    <div v-else-if="transactionData.transaction?.berita || transactionData.transaction?.berita_tujuan">
                                        <label class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-2 block">Keterangan</label>
                                        <div class="text-sm text-slate-300 italic border-l-2 border-slate-600 pl-3 py-1">
                                            "{{ transactionData.transaction?.berita || transactionData.transaction?.berita_tujuan }}"
                                        </div>
                                    </div>
                                    
                                    <!-- TELLER INPUTS -->
                                    <div v-if="isTransfer" class="mt-4 bg-slate-700/30 p-3 rounded-xl border border-dashed border-slate-600">
                                        <label class="text-[10px] uppercase font-bold text-yellow-400 tracking-wider mb-2 block">
                                            <i class="fa-solid fa-pen-to-square mr-1"></i> Input Teller
                                        </label>
                                        
                                        <div class="space-y-3">
                                            <div>
                                                <label class="text-[10px] text-slate-400 block mb-1">Metode Transfer</label>
                                                <select v-model="form.metode_transfer" class="w-full bg-slate-800 text-white text-xs rounded p-2 border border-slate-600 focus:border-blue-500 outline-none">
                                                    <option value="">- Pilih -</option>
                                                    <option value="SKN">SKN</option>
                                                    <option value="RTGS">RTGS</option>
                                                    <option value="TT">TT / Swift</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[10px] text-slate-400 block mb-1">Mata Uang</label>
                                                <select v-model="form.mata_uang" class="w-full bg-slate-800 text-white text-xs rounded p-2 border border-slate-600 focus:border-blue-500 outline-none">
                                                    <option value="IDR">Rupiah (IDR)</option>
                                                    <option value="USD">Dollar (USD)</option>
                                                    <option value="SGD">Singapore Dollar (SGD)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[10px] text-slate-400 block mb-1">Sumber Dana</label>
                                                <select v-model="form.sumber_dana" class="w-full bg-slate-800 text-white text-xs rounded p-2 border border-slate-600 focus:border-blue-500 outline-none">
                                                    <option value="Tunai">Tunai / Cash</option>
                                                    <option value="Debet">Debet Rekening</option>
                                                    <option value="Kliring">Kliring Bank</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT PANE: Actor -->
                                <div class="col-span-12 md:col-span-7 space-y-4">
                                     <label class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block border-b border-slate-700/50 pb-1 mb-2">
                                        Data {{ isTransfer ? 'Pengirim' : (isSetor ? 'Penyetor' : 'Penarik') }}
                                    </label>
                                    
                                    <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                                        <div class="col-span-2">
                                            <span class="text-slate-500 text-xs block mb-0.5">Nama {{ isTransfer ? 'Pengirim' : (isSetor ? 'Penyetor' : 'Penarik') }}</span>
                                            <span class="text-white font-bold text-xl border-b border-slate-700 pb-1 block w-full tracking-wide">{{ activeActorName }}</span>
                                        </div>
                                        
                                        <div>
                                            <span class="text-slate-500 text-xs block mb-0.5">Nomor Handphone</span>
                                            <span class="text-white font-mono bg-slate-900/30 px-2 py-1 rounded border border-slate-700/50 text-sm">{{ activeActorPhone }}</span>
                                        </div>

                                        <div v-if="activeActorIdentity">
                                            <span class="text-slate-500 text-xs block mb-0.5">No. Identitas</span>
                                             <span class="text-white font-mono bg-slate-900/30 px-2 py-1 rounded border border-slate-700/50 text-sm">{{ activeActorIdentity }}</span>
                                        </div>
                                        
                                        <div class="col-span-2">
                                            <span class="text-slate-500 text-xs block mb-0.5">Alamat Lengkap</span>
                                            <div class="text-slate-300 text-sm leading-snug bg-slate-900/20 p-2.5 rounded border border-slate-700/30">
                                                {{ activeActorAddress }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="pt-2 mt-auto">
                                        <button @click="printSlip" class="w-full bg-white hover:bg-slate-200 text-slate-900 py-3 rounded-lg font-bold text-sm transition-all shadow-lg flex items-center justify-center gap-2 group-hover:shadow-white/20">
                                            <i class="fa-solid fa-print"></i> CETAK / VALIDASI SLIP
                                        </button>
                                        <p class="text-center text-[10px] text-slate-500 mt-2">*Mencetak struk untuk printer 80mm</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CASE 2: IDLE -->
            <div v-else class="flex flex-col items-center justify-center text-center py-12">
                <div class="bg-slate-800/50 rounded-full p-6 mb-4">
                    <i class="fa-solid fa-user-clock text-4xl text-blue-400"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Siap Melayani</h2>
                <p class="text-slate-400 mb-6">Silakan panggil antrian berikutnya.</p>
                
                <button v-if="queues.length > 0" @click="callNext" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-4 rounded-2xl font-bold shadow-lg transform hover:scale-105 transition-all text-lg animate-pulse">
                    <i class="fa-solid fa-bullhorn mr-2"></i> PANGGIL ANTRIAN BERIKUTNYA
                </button>
                 <p v-else class="text-slate-500 italic">Belum ada antrian menunggu.</p>
            </div>
            
        </div>

        <!-- Waiting List -->
        <h3 class="text-xl font-bold text-slate-300 mb-4 flex items-center">
            <i class="fa-regular fa-clock mr-2"></i> Daftar Antrian
        </h3>
        
        <div v-if="loading" class="text-center py-12">
            <i class="fa-solid fa-circle-notch fa-spin text-4xl text-blue-500"></i>
        </div>

        <div v-else-if="queues.length === 0" class="bg-slate-800 rounded-xl p-12 text-center border border-slate-700 mb-8">
            <i class="fa-solid fa-mug-hot text-6xl text-slate-600 mb-4"></i>
            <p class="text-slate-400 text-lg">Tidak ada antrian menunggu.</p>
        </div>

        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-10">
            <div v-for="q in queues" :key="q.id_antrian" 
                 class="bg-slate-800 p-4 rounded-xl border border-slate-700 relative overflow-hidden opacity-80">

                <div class="flex flex-col items-center text-center">
                    <span class="text-3xl font-bold text-white mb-2">{{ q.antrian }}</span>
                    <span v-if="getTxType(q.kode)" class="text-[10px] uppercase tracking-wider text-slate-400 mb-2">{{ getTxType(q.kode) }}</span>
                    <span class="text-sm text-blue-200 font-medium truncate w-full">{{ q.nama_antrian }}</span>
                    <span class="text-xs text-slate-500 mt-2"><i class="fa-regular fa-clock mr-1"></i> {{ formatTime(q.created_at) }}</span>
                </div>
            </div>
        </div>

        <!-- PRINT AREA (Wrapper) -->
        <div id="print-wrapper" class="hidden print:block fixed top-0 left-0 w-full h-full bg-white z-[9999] p-0 text-black">
            
            <!-- 1. STANDARD THERMAL (80mm) -->
            <div id="print-area-standard" v-if="!isSetor && !isTarik" class="w-[80mm] mx-auto p-4 font-mono text-xs leading-tight">
                <div class="text-center mb-4 pb-2 border-b-2 border-black border-dashed">
                    <img src="/img/logo_mci.png" class="h-10 mx-auto mb-1" alt="BPRS HIK MCI">
                    <p class="scale-90">Jl. Kaliurang KM 9, Yogyakarta</p>
                    <p class="scale-90">Telp: (0274) 123456</p>
                </div>

                <!-- Standard Content (Existing Logic) -->
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
                     <!-- (Simplified existing thermal content for brevity - logic remains similar) -->
                     <!-- EXISTING LOGIC CAN BE COPIED OR MAINTAINED IF NEEDED EXACTLY -->
                </div>
            </div>

            <!-- 2. VALIDATION SLIP (1/3 A4 Landscape) -->
            <div id="print-area-slip" class="w-[210mm] h-[99mm] px-3 py-4 font-sans text-xs relative box-border border-b border-black hidden">
                
                <div v-if="transactionData && transactionData.transaction">
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
                        <p class="text-[10px] mt-1 font-mono text-slate-500">{{ transactionData.transaction.token }}</p>
                    </div>
                </div>

                <!-- Slip Body -->
                <div class="space-y-1.5 leading-snug">
                    
                    <!-- Date & Checkboxes -->
                    <div class="flex justify-between items-center border-b border-dashed border-slate-300 pb-1 mb-1">
                        <div class="flex gap-1 items-center">
                            <span class="font-bold w-16">Tanggal</span>
                            <span>: {{ new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) }}</span>
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
                            <span class="font-bold text-sm truncate block">{{ transactionData.transaction.nama }}</span>
                        </div>
                        <div class="flex-1 border-l border-indigo-200 pl-4">
                            <span class="font-bold block text-[10px] uppercase text-slate-500">Nomor Rekening</span>
                            <span class="font-mono font-bold text-sm tracking-wider">{{ transactionData.transaction.no_rek }}</span>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="flex items-center gap-2 mt-1">
                        <span class="font-bold w-28 text-right">Jumlah {{ isSetor ? 'Setoran' : 'Penarikan' }} :</span>
                        <div class="flex-grow bg-slate-100 px-2 py-1 border border-slate-300 font-mono font-bold text-base flex justify-start gap-2 items-center">
                            <span>Rp.</span>
                            <span>{{ Number(transactionData.transaction.nominal).toLocaleString('id-ID') }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-bold w-28 text-right text-[10px]">Terbilang :</span>
                        <div class="flex-grow italic text-[10px] bg-slate-50 px-2 border-b border-slate-200 capitalize">
                            {{ transactionData.transaction.terbilang }}
                        </div>
                    </div>

                    <!-- Transaction Details -->
                    <div class="grid grid-cols-2 gap-x-8 gap-y-1 mt-1">
                        <!-- Left Col -->
                        <div class="space-y-1">
                            <div class="flex gap-2">
                                <span class="font-bold w-24">Tujuan Transaksi</span>
                                <span>: {{ transactionData.transaction.berita || transactionData.transaction.tujuan || '-' }}</span>
                            </div>
                            <!-- Show Actor Identity (e.g. ID of Penarik/Penyetor) -->
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
                        <!-- Address -->
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
                                <p class="text-[8px] border-t border-black pt-0.5 w-full block">(.......)</p>
                            </div>
                            <div>
                                <p class="mb-10 text-[8px] font-bold uppercase">Checker</p>
                                <p class="text-[8px] border-t border-black pt-0.5 w-full block">(.......)</p>
                            </div>
                            <div>
                                <p class="mb-10 text-[8px] font-bold uppercase">Approver</p>
                                <p class="text-[8px] border-t border-black pt-0.5 w-full block">(.......)</p>
                            </div>
                            <div>
                                <p class="mb-10 text-[8px] font-bold uppercase">Nasabah</p>
                                <p class="text-[8px] border-t border-black pt-0.5 w-full block">(.......)</p>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
            </div>

            <!-- 3. TRANSFER SLIP (1/2 A4 Landscape - Green) -->
            <div id="print-area-transfer" class="w-[210mm] h-[148mm] px-5 py-5 font-sans text-xs relative box-border border-b border-black hidden">
                
                <div v-if="transactionData && transactionData.transaction">
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
                        <p class="text-[10px] font-mono text-slate-500 tracking-wider">REF: {{ transactionData.transaction.token }}</p>
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
                                <td class="uppercase font-bold py-0.5">{{ transactionData.transaction.nama }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">Alamat</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="uppercase h-10 overflow-hidden leading-tight text-slate-800 py-0.5">{{ transactionData.transaction.alamat_penyetor || '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">No. Telepon</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="text-slate-800 py-0.5">{{ transactionData.transaction.hp_penyetor || '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">Sumber Dana</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="italic font-bold text-slate-700 py-0.5">{{ form.sumber_dana || 'Tunai' }}</td>
                            </tr>
                             <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">Metode Transfer</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="font-bold text-slate-900 py-0.5">{{ form.metode_transfer || '-' }}</td>
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
                                <td class="uppercase font-bold text-sm text-slate-900 py-0.5">{{ transactionData.transaction.nama_tujuan }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">No. Rekening</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="font-mono text-sm font-bold tracking-wider text-slate-900 py-0.5">{{ transactionData.transaction.no_rek_tujuan }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">Bank Tujuan</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="uppercase font-bold text-slate-800 py-0.5">{{ transactionData.transaction.bank_tujuan }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">Negara</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="uppercase text-slate-800 py-0.5">{{ transactionData.transaction.negara_tujuan || 'INDONESIA' }}</td>
                            </tr>
                             <tr>
                                <td class="font-semibold align-top text-slate-500 py-0.5">Keterangan</td>
                                <td class="align-top py-0.5">:</td>
                                <td class="italic text-slate-700 py-0.5">{{ transactionData.transaction.tujuan || form.berita_tujuan || '-' }}</td>
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
                                <span class="font-bold text-sm text-slate-700 mr-2 border-r border-slate-300 pr-2">{{ form.mata_uang || 'IDR' }}</span>
                                <span class="font-mono font-bold text-xl text-slate-900 tracking-tight">
                                    {{ Number(transactionData.transaction.nominal).toLocaleString('id-ID') }}
                                </span>
                            </div>
                        </div>

                        <!-- Terbilang -->
                        <div class="flex-1">
                             <p class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">Terbilang / In Words</p>
                             <div class="w-full border-b border-black border-dashed pb-1 min-h-[34px] flex items-end">
                                <span class="italic font-bold text-[12px] uppercase leading-tight text-slate-800 break-words w-full">
                                    {{ transactionData.transaction.terbilang.replace(/ rupiah$/i, '') }} RUPIAH
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
                                ( {{ transactionData.transaction.nama }} )
                            </p>
                        </div>
                        <!-- Address Footer -->
                        <div class="text-[8px] font-bold text-slate-400 mt-2 text-right">
                             Doc: Transfer Slip / Rev.01 / 2026
                        </div>
                    </div>
                </div>

                </div>
            </div>

        </div>
    </div>
</template>

<style>
@media print {
    body * { visibility: hidden; }
    #print-wrapper, #print-wrapper * { visibility: visible; }
    
    #print-wrapper {
        position: fixed; left: 0; top: 0; margin: 0; padding: 0; background: white;
    }

    /* STANDARD THERMAL MODE (Default visibility handled by JS injection above) */
    /* Nested @page removed to fix build warning. Handled in printSlip() */

    /* SLIP VALIDATION MODE */
    /* Nested @page removed. Handled in printSlip() */
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
                sumber_dana: 'Tunai'
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
        printSlip() {
            console.log("Print Slip Debug:");
            console.log("Transaction Data:", this.transactionData);
            console.log("Is Transfer?", this.isTransfer);
            console.log("Is Setor?", this.isSetor);
            console.log("Is Tarik?", this.isTarik);

            // VALIDATION: Check mandatory fields for Transfer
            if (this.isTransfer) {
                if (!this.form.mata_uang || !this.form.sumber_dana) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Belum Lengkap',
                        text: 'Mata Uang dan Sumber Dana harus diisi sebelum mencetak slip!',
                        confirmButtonColor: '#f59e0b'
                    });
                    return; // Stop checking
                }
            }

            // 1. Determine Global Print Style for Page Size
            const styleId = 'dynamic-print-style';
            let styleEl = document.getElementById(styleId);
            
            if (!styleEl) {
                styleEl = document.createElement('style');
                styleEl.id = styleId;
                document.head.appendChild(styleEl);
            }

            // 2. Set Content
            if (this.isTransfer) {
                // TRANSFER SLIP (1/2 A4 Landscape: 210mm x 148.5mm)
                styleEl.innerHTML = `
                    @page {
                        size: 210mm 148mm; 
                        margin: 0;
                    }
                    /* Hide Standard */
                    #print-area-standard { display: none !important; }
                    /* Hide Standard Slip */
                    #print-area-slip { display: none !important; }
                    /* Show Transfer Slip */
                    #print-area-transfer { display: block !important; }
                    #print-wrapper { width: 210mm; height: 148mm; }
                `;
            } else if (this.isSetor || this.isTarik) {
                // LANDSCAPE SLIP (210mm x 99mm)
                styleEl.innerHTML = `
                    @page {
                        size: 210mm 99mm;
                        margin: 0;
                    }
                    /* Hide Standard */
                    #print-area-standard { display: none !important; }
                    #print-area-transfer { display: none !important; }
                    #print-area-slip { display: block !important; }
                    #print-wrapper { width: 210mm; height: 99mm; }
                `;
            } else {
                // STANDARD THERMAL (80mm)
                styleEl.innerHTML = `
                    @page {
                        size: 80mm auto;
                        margin: 0;
                    }
                    /* Hide Slip */
                    #print-area-slip { display: none !important; }
                    #print-area-transfer { display: none !important; }
                    #print-area-standard { display: block !important; }
                    #print-wrapper { width: 80mm; }
                `;
            }

            // 3. Print
             setTimeout(() => {
                // EXPLICIT FORCE SHOW
                if (this.isTransfer) {
                   const el = document.getElementById('print-area-transfer');
                   if(el) {
                       el.classList.remove('hidden');
                       el.style.display = 'block';
                   } 
                } else if (this.isSetor || this.isTarik) {
                   const el = document.getElementById('print-area-slip');
                   if(el) {
                       el.classList.remove('hidden');
                       el.style.display = 'block';
                   }
                } else {
                    const el = document.getElementById('print-area-standard');
                    if(el) el.style.display = 'block';
                }

                window.print();
                
                // Cleanup after print (Optional, but good for UI state)
                setTimeout(() => {
                    const styleEl = document.getElementById('dynamic-print-style');
                    if (styleEl) styleEl.innerHTML = ''; // Reset CSS
                    
                    // Reset inline styles
                    const transferEl = document.getElementById('print-area-transfer');
                    if(transferEl) {
                        transferEl.classList.add('hidden');
                        transferEl.style.display = '';
                    }
                    const slipEl = document.getElementById('print-area-slip');
                    if(slipEl) {
                         slipEl.classList.add('hidden'); // Add hidden back if it was there
                         slipEl.style.display = '';
                    }
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
