@extends('layouts.app')

@section('content')
<div class="form-card" style="max-width: 1200px; padding: 0; margin: 0 auto; animation: slideUp 0.6s ease-out; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
    
    <!-- Account Info Header (Compact) -->
    <div style="background: white; padding: 12px 25px; border-bottom: 1px solid #e2e8f0;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: nowrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 45px; height: 45px; background: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-soft);">
                    <i class="fa-solid fa-paper-plane" style="font-size: 1.4rem; color: #8b5cf6;"></i>
                </div>
                <div>
                    <h5 style="margin: 0; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Transfer Dari Rekening</h5>
                    <h4 style="margin: 2px 0 0; color: var(--primary-navy); font-size: 1.1rem; font-weight: 700;">{{ $data['nama'] }}</h4>
                    <p style="margin: 0; font-family: monospace; font-size: 0.9rem; color: var(--text-primary);">{{ $data['no_rek'] }}</p>
                </div>
            </div>
            
            <div style="text-align: right; background: rgba(139, 92, 246, 0.1); padding: 8px 15px; border-radius: 8px; border: 1px solid rgba(139, 92, 246, 0.2); white-space: nowrap;">
                <span style="font-size: 0.7rem; color: #8b5cf6; font-weight: 600; display: block;">SALDO SAAT INI</span>
                 <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                     <span id="balance_display" data-value="Rp {{ number_format($data['saldo'], 0, ',', '.') }}" style="font-size: 1rem; font-weight: 700; color: var(--primary-navy); letter-spacing: 1px;">
                        ••••••••
                    </span>
                    <button type="button" onclick="toggleBalance()" style="background: none; border: none; cursor: pointer; color: #8b5cf6; padding: 0;">
                        <i class="fa-solid fa-eye" id="eye_icon"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Alert -->
    @if ($errors->any())
        <div style="background: #fef2f2; color: #ef4444; padding: 15px 25px; border-bottom: 1px solid #fecaca; font-size: 0.95rem;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transaction.transfer.store') }}" method="POST" style="padding: 15px 20px;" hx-boost="false">
        @csrf
        <input type="hidden" name="nama" value="{{ $data['nama'] }}">
        <input type="hidden" name="no_rek" value="{{ $data['no_rek'] }}">
        <input type="hidden" name="tgl" value="{{ date('Y-m-d') }}">
        
        <!-- Legacy fields -->
        <input type="hidden" name="nama_penyetor" value="{{ $data['nama'] }}">
        <input type="hidden" name="hp_penyetor" value="{{ $data['hp'] ?? '' }}">
        <input type="hidden" name="alamat_penyetor" value="{{ $data['alamat'] ?? '' }}">

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 25px;">
            
            <!-- Column 1: Data Pengirim -->
            <div>
                <h6 style="color: var(--primary-navy); border-bottom: 2px solid #64748b; padding-bottom: 5px; margin-bottom: 15px; font-weight: 700;">
                    Data Pengirim
                </h6>
                
                <div class="form-group" style="margin-bottom: 12px;">
                     <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">Hari, Tanggal</label>
                     <input type="text" class="form-control form-control-sm" readonly value="{{ \Carbon\Carbon::now()->format('l, d F Y') }}" style="background-color: #e9ecef; cursor: not-allowed;">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">No HP Pengirim</label>
                    <input type="text" name="hp_penyetor_disp" class="form-control form-control-sm" readonly value="{{ $data['hp'] ?? '-' }}" style="background-color: #e9ecef; cursor: not-allowed;">
                </div>
                
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">Alamat Pengirim</label>
                    <textarea class="form-control form-control-sm" rows="3" readonly style="background-color: #e9ecef; cursor: not-allowed; resize: none;">{{ $data['alamat'] ?? '-' }}</textarea>
                </div>
            </div>

            <!-- Column 2: Detail Penerima -->
            <div>
                <h6 style="color: var(--primary-navy); border-bottom: 2px solid #64748b; padding-bottom: 5px; margin-bottom: 15px; font-weight: 700;">
                    Detail Penerima
                </h6>

                <div class="form-group" style="margin-bottom: 12px; position: relative;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">Negara Penerima</label>
                    <div style="position: relative;">
                        <!-- Country Input for Display/Search -->
                        <div style="display: flex; align-items: center; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
                             <span id="flag_icon" style="padding-left: 10px; font-size: 1.2rem; line-height: 1;">🇮🇩</span>
                            <input type="text" 
                                id="countrySearch" 
                                class="form-control form-control-sm" 
                                placeholder="Cari negara..." 
                                value="Indonesia"
                                autocomplete="off"
                                onkeyup="filterCountries()"
                                onfocus="showCountryList()"
                                style="border: none; box-shadow: none; padding-left: 10px;">
                            <i class="fa-solid fa-chevron-down" style="padding-right: 10px; color: #94a3b8; pointer-events: none; font-size: 0.8rem;"></i>
                        </div>
                        
                        <!-- Hidden Input for Form Submission -->
                        <input type="hidden" name="negara_tujuan" id="countryRealValue" value="Indonesia">

                        <!-- Country List Dropdown -->
                        <div id="countryList" class="custom-dropdown-list" style="display: none; position: absolute; z-index: 101; width: 100%; max-height: 250px; overflow-y: auto; background: white; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin-top: 5px;">
                            <!-- Populated by JS -->
                            <div style="padding: 10px; text-align: center; color: #64748b;">Memuat data negara...</div>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 12px; position: relative;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">Bank Tujuan</label>
                    
                    <!-- Searchable Input -->
                    <div style="position: relative;">
                        <input type="text" 
                               id="bankSearch" 
                               class="form-control form-control-sm" 
                               placeholder="Ketik nama bank..." 
                               autocomplete="off"
                               onkeyup="filterBanks()"
                               onfocus="showBankList()"
                               style="padding-right: 30px; cursor: text;">
                        <i class="fa-solid fa-chevron-down" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 0.8rem;"></i>
                    </div>

                    <!-- Hidden Input for Actual Value -->
                    <input type="hidden" name="bank_tujuan" id="bankRealValue">

                    <!-- Dropdown List -->
                    <div id="bankList" class="custom-dropdown-list" style="display: none; position: absolute; z-index: 100; width: 100%; max-height: 250px; overflow-y: auto; background: white; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin-top: 5px;">
                        @foreach($banks as $bank)
                            <div class="bank-option" 
                                 onclick="selectBank('{{ $bank->nama_bank }}', '{{ $bank->biaya_trf }}', '{{ $bank->nama_bank }}/{{ $bank->biaya_trf }}')"
                                 style="padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #f1f5f9; transition: background 0.1s;">
                                <div style="font-weight: 600; color: var(--primary-navy); font-size: 0.9rem;">{{ $bank->nama_bank }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); display: flex; justify-content: space-between;">
                                    <span>Biaya Admin:</span>
                                    <span style="font-weight: 600; color: #ef4444;">Rp {{ number_format($bank->biaya_trf, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">No Rekening Tujuan</label>
                    <input type="text" name="no_rek_tujuan" class="form-control form-control-sm" placeholder="Contoh: 1234567890" required>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">Nama Penerima</label>
                    <input type="text" name="nama_tujuan" class="form-control form-control-sm" placeholder="Nama Penerima" required>
                </div>

                <!-- Berita Untuk Penerima Removed -->
            </div>
            
            <!-- Column 3: Detail Transaksi -->
            <div>
                <h6 style="color: var(--primary-navy); border-bottom: 2px solid #8b5cf6; padding-bottom: 5px; margin-bottom: 15px; font-weight: 700;">
                    Detail Transaksi
                </h6>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">Nominal Transfer (Rp)</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 15px; top: 10px; font-weight: 600; color: var(--text-muted); font-size: 0.9rem;">Rp</span>
                        <input type="text" 
                               name="nominal_display" 
                               class="form-control focus-transfer" 
                               style="padding-left: 40px; font-size: 1.2rem; font-weight: 700; color: #8b5cf6; height: 45px;" 
                               placeholder="0" 
                               required 
                               onkeyup="formatRupiah(this); updateTerbilang(this.value); calculateTotal();">
                        <input type="hidden" name="nominal" id="nominal">
                    </div>
                    
                    <script>
                        // Validation Listener
                        document.querySelector('form').addEventListener('submit', function(e) {
                            let nominal = document.getElementById('nominal').value;
                            if(parseInt(nominal) < 10000) {
                                e.preventDefault();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Nominal Kurang',
                                    text: 'Mohon Maaf, Minimal Transfer adalah Rp 10.000',
                                    confirmButtonColor: '#8b5cf6',
                                    confirmButtonText: 'Tutup'
                                });
                            }
                        });
                    </script>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">Terbilang</label>
                    <textarea name="terbilang" 
                              id="terbilang" 
                              class="form-control" 
                              rows="2" 
                              readonly 
                              style="background: #f8fafc; color: var(--text-muted); font-style: italic; resize: none; border-color: transparent; font-size: 0.85rem; line-height: 1.2;">Nol Rupiah</textarea>
                </div>
                
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">Tujuan / Identitas Transaksi</label>
                    <input type="text" name="tujuan" class="form-control form-control-sm" placeholder="Contoh: Pembayaran Invoice" required>
                </div>

                <!-- Fee Display -->
                <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px dashed #cbd5e1; margin-top: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.85rem;">
                        <span>Biaya Admin Bank:</span>
                        <span id="fee_display" style="font-weight: 600;">Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1rem; font-weight: 700; color: var(--primary-navy); border-top: 1px solid #e2e8f0; padding-top: 8px;">
                        <span>Total Debet:</span>
                        <span id="total_display">Rp 0</span>
                    </div>
                </div>
                
                <!-- Insufficient Balance Warning -->
                <div id="balance_warning" style="display: none; margin-top: 10px; background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; padding: 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                    <i class="fa-solid fa-triangle-exclamation" style="margin-right: 5px;"></i> Saldo tidak mencukupi!
                </div>

                <!-- Min Amount Warning -->
                <div id="min_warning" style="display: none; margin-top: 10px; background: #fff7ed; border: 1px solid #fed7aa; color: #c2410c; padding: 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right: 5px;"></i> Minimal transfer Rp 10.000
                </div>
            </div>
            
        </div>

        <div style="margin-top: 20px; text-align: right; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 15px;">
            <a href="{{ url('/') }}" class="btn-back" style="margin: 0; font-size: 0.9rem;">&larr; Batal</a>
            <button type="submit" class="btn-submit accent-transfer" style="width: auto; padding: 10px 40px; font-size: 0.95rem;">
                Kirim Transfer <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i>
            </button>
        </div>
    </form>
</div>

<!-- JS Helpers -->
<script>
    let currentFee = 0;
    // Pass raw balance from PHP to JS (Safe fallback)
    const currentBalance = {{ (float) ($data['saldo'] ?? 0) }}; 

    function formatRupiah(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        document.getElementById('nominal').value = value; 
        
        let number_string = value.toString(),
            sisa 	= number_string.length % 3,
            rupiah 	= number_string.substr(0, sisa),
            ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
            
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        input.value = rupiah;
    }

    function updateTerbilang(val) {
        let nominal = val.replace(/[^0-9]/g, '');
        if(nominal === '') {
            document.getElementById('terbilang').value = 'Nol Rupiah';
            return;
        }
        document.getElementById('terbilang').value = terbilang(nominal) + ' Rupiah';
    }

    function updateFee() {
        const select = document.getElementById('bank_tujuan');
        // Handle searchable dropdown update (hidden select logic if strictly needed, but here we use the selectBank params)
        // Since we replaced the Select with Custom Dropdown, this function 'updateFee' is legacy or needs to be adapted if called from select.
        // Actually, selectBank calls calculateTotal directly. This function might be unused now or needs to be removed if no longer called.
        // We act on selectBank instead.
    }

    function calculateTotal() {
        let nominal = parseInt(document.getElementById('nominal').value) || 0;
        let total = nominal + currentFee;
        
        const totalEl = document.getElementById('total_display');
        const submitBtn = document.querySelector('.btn-submit');
        const balanceWarning = document.getElementById('balance_warning');
        const minWarning = document.getElementById('min_warning');
        const transferInput = document.querySelector('input[name="nominal_display"]');

        totalEl.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);

        let isValid = true;

        // Reset Warnings
        if(balanceWarning) balanceWarning.style.display = 'none';
        if(minWarning) minWarning.style.display = 'none';
        totalEl.style.color = 'var(--primary-navy)';
        if(transferInput) transferInput.style.color = '#8b5cf6';

        // 1. Min Amount Validation
        if (nominal > 0 && nominal < 10000) {
            if(minWarning) minWarning.style.display = 'block';
            if(transferInput) transferInput.style.color = '#ef4444';
            isValid = false;
        }

        // 2. Balance Validation
        if (total > currentBalance) {
            if(balanceWarning) balanceWarning.style.display = 'block';
            totalEl.style.color = '#ef4444';
            isValid = false;
        }

        // Button State
        if(submitBtn) {
            if (isValid) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
            } else {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
                submitBtn.style.cursor = 'not-allowed';
            }
        }
    }

    function terbilang(a){
        var bilangan = ['','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas'];
        var kalimat = "";
        a = parseFloat(a);
        if(a < 12) kalimat = bilangan[a];
        else if(a < 20) kalimat = terbilang(a - 10) + " Belas";
        else if(a < 100) kalimat = terbilang(Math.floor(a / 10)) + " Puluh " + terbilang(a % 10);
        else if(a < 200) kalimat = "Seratus " + terbilang(a - 100);
        else if(a < 1000) kalimat = terbilang(Math.floor(a / 100)) + " Ratus " + terbilang(a % 100);
        else if(a < 2000) kalimat = "Seribu " + terbilang(a - 1000);
        else if(a < 1000000) kalimat = terbilang(Math.floor(a / 1000)) + " Ribu " + terbilang(a % 1000);
        else if(a < 1000000000) kalimat = terbilang(Math.floor(a / 1000000)) + " Juta " + terbilang(a % 1000000);
        return kalimat;
    }
    // Searchable Dropdown Logic
    function showBankList() {
        document.getElementById('bankList').style.display = 'block';
    }

    function filterBanks() {
        let input = document.getElementById('bankSearch');
        let filter = input.value.toUpperCase();
        let div = document.getElementById('bankList');
        let options = div.getElementsByClassName('bank-option');

        for (let i = 0; i < options.length; i++) {
            let txtValue = options[i].textContent || options[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                options[i].style.display = "";
            } else {
                options[i].style.display = "none";
            }
        }
        document.getElementById('bankList').style.display = 'block';
    }

    function selectBank(name, fee, value) {
        document.getElementById('bankSearch').value = name + ' (Biaya: Rp ' + new Intl.NumberFormat('id-ID').format(fee) + ')';
        document.getElementById('bankRealValue').value = value;
        document.getElementById('bankList').style.display = 'none';
        
        // Update Fee Logic
        currentFee = parseInt(fee) || 0;
        document.getElementById('fee_display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(currentFee);
        calculateTotal();

        // Visual feedback
        document.getElementById('bankSearch').style.borderColor = '#10b981';
        document.getElementById('bankSearch').style.backgroundColor = '#f0fdf4';
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        // Bank Dropdown
        let box = document.getElementById('bankList');
        let input = document.getElementById('bankSearch');
        if (event.target !== box && event.target !== input && !box.contains(event.target)) {
            box.style.display = 'none';
        }
        
        // Country Dropdown
        let countryBox = document.getElementById('countryList');
        let countryInput = document.getElementById('countrySearch');
        if (event.target !== countryBox && event.target !== countryInput && !countryBox.contains(event.target)) {
            countryBox.style.display = 'none';
        }
    });

    // Country Fetching & Logic
    let allCountries = [];

    async function fetchCountries() {
        try {
            const response = await fetch('https://restcountries.com/v3.1/all?fields=name,flags,cca2');
            const data = await response.json();
            
            // Sort Alphabetically
            allCountries = data.sort((a, b) => a.name.common.localeCompare(b.name.common));
            
            // Initial Render
            renderCountries(allCountries);
            
            // Set Default Indonesia
            const indo = allCountries.find(c => c.cca2 === 'ID');
            if(indo) {
                selectCountry(indo.name.common, indo.flags.svg);
            }
        } catch (error) {
            console.error('Failed to fetch countries', error);
            document.getElementById('countryList').innerHTML = '<div style="padding: 10px; color: red;">Gagal memuat data negara</div>';
        }
    }

    function renderCountries(countries) {
        const list = document.getElementById('countryList');
        list.innerHTML = '';
        
        if(countries.length === 0) {
            list.innerHTML = '<div style="padding: 10px; text-align: center; color: #64748b;">Negara tidak ditemukan</div>';
            return;
        }

        countries.forEach(country => {
            const div = document.createElement('div');
            div.className = 'bank-option'; // Reuse style
            div.style.padding = '10px 15px';
            div.style.cursor = 'pointer';
            div.style.borderBottom = '1px solid #f1f5f9';
            div.style.display = 'flex';
            div.style.alignItems = 'center';
            div.style.gap = '10px';
            
            const flag = document.createElement('img');
            flag.src = country.flags.svg;
            flag.style.width = '20px';
            flag.style.borderRadius = '2px';
            
            const name = document.createElement('span');
            name.textContent = country.name.common;
            name.style.fontSize = '0.9rem';
            name.style.color = 'var(--primary-navy)';
            name.style.fontWeight = '500';

            div.appendChild(flag);
            div.appendChild(name);
            
            div.onclick = () => selectCountry(country.name.common, country.flags.svg);
            
            list.appendChild(div);
        });
    }

    function showCountryList() {
        document.getElementById('countryList').style.display = 'block';
        if(allCountries.length === 0) fetchCountries(); // Fetch if empty
    }

    function filterCountries() {
        const query = document.getElementById('countrySearch').value.toLowerCase();
        const filtered = allCountries.filter(c => c.name.common.toLowerCase().includes(query));
        renderCountries(filtered);
        document.getElementById('countryList').style.display = 'block';
    }

    function selectCountry(name, flagUrl) {
        document.getElementById('countrySearch').value = name;
        document.getElementById('countryRealValue').value = name;
        document.getElementById('flag_icon').innerHTML = `<img src="${flagUrl}" style="width: 20px; border-radius: 2px;">`;
        document.getElementById('countryList').style.display = 'none';
    }

    // Call on load
    document.addEventListener('DOMContentLoaded', fetchCountries);

    function toggleBalance() {
        const display = document.getElementById('balance_display');
        const icon = document.getElementById('eye_icon');
        const realValue = display.getAttribute('data-value');
        
        if (display.textContent.trim() === '••••••••') {
            display.textContent = realValue;
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            display.textContent = '••••••••';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

<style>
    @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    
    .custom-dropdown-list::-webkit-scrollbar {
        width: 6px;
    }
    .custom-dropdown-list::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .custom-dropdown-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .custom-dropdown-list::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .bank-option:hover {
        background-color: #f8fafc !important;
        padding-left: 20px !important;
    }
</style>
@endsection
