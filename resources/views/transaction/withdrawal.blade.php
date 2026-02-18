@extends('layouts.app')

@section('content')
<div class="form-card" style="max-width: 1200px; padding: 0; margin: 0 auto; animation: slideUp 0.6s ease-out; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
    
    <!-- Account Info Header (Compact) -->
    <div style="background: white; padding: 12px 25px; border-bottom: 1px solid #e2e8f0;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: nowrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 45px; height: 45px; background: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-soft);">
                    <i class="fa-solid fa-hand-holding-dollar" style="font-size: 1.4rem; color: #ef4444;"></i>
                </div>
                <div>
                    <h5 style="margin: 0; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Tarik Tunai Dari Rekening</h5>
                    <h4 style="margin: 2px 0 0; color: var(--primary-navy); font-size: 1.1rem; font-weight: 700;">{{ $data['nama'] }}</h4>
                    <p style="margin: 0; font-family: monospace; font-size: 0.9rem; color: var(--text-primary);">{{ $data['no_rek'] }}</p>
                </div>
            </div>
            
            <div style="text-align: right; background: rgba(239, 68, 68, 0.1); padding: 8px 15px; border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.2); white-space: nowrap;">
                <span style="font-size: 0.7rem; color: #ef4444; font-weight: 600; display: block;">SALDO SAAT INI</span>
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                     <span id="balance_display" style="font-size: 1rem; font-weight: 700; color: var(--primary-navy); letter-spacing: 2px;">
                        @for($i = 0; $i < strlen((int)$data['saldo']); $i++)•@endfor
                    </span>
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

    <form action="{{ route('transaction.withdrawal.store') }}" method="POST" style="padding: 15px 20px;" hx-boost="false">
        @csrf
        <input type="hidden" name="nama" value="{{ $data['nama'] }}">
        <input type="hidden" name="no_rek" value="{{ $data['no_rek'] }}">
        <input type="hidden" name="tgl" value="{{ date('Y-m-d') }}">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            
            <!-- Nominal Section -->
            <div>
                <h6 style="color: var(--primary-navy); border-bottom: 2px solid #ef4444; padding-bottom: 5px; margin-bottom: 15px; font-weight: 700;">
                    Detail Penarikan
                </h6>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">Nominal Penarikan (Rp)</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 15px; top: 10px; font-weight: 600; color: var(--text-muted); font-size: 0.9rem;">Rp</span>
                        <input type="text" 
                               name="nominal_display" 
                               class="form-control focus-withdrawal" 
                               style="padding-left: 40px; font-size: 1.2rem; font-weight: 700; color: #ef4444; height: 45px;" 
                               placeholder="0" 
                               required 
                               onkeyup="formatRupiah(this); updateTerbilang(this.value);">
                        <input type="hidden" name="nominal" id="nominal" required>
                    </div>
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
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">Tujuan Transaksi</label>
                    <input type="text" name="tujuan" class="form-control form-control-sm" placeholder="Contoh: Belanja Bulanan" required>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 8px; display: block;">Sumber Dana Penarikan</label>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 0.9rem;">
                            <input type="radio" name="jenis_rekening" value="Tabungan" required checked> Tabungan
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 0.9rem;">
                            <input type="radio" name="jenis_rekening" value="Kredit"> Kredit
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 0.9rem;">
                            <input type="radio" name="jenis_rekening" value="Lainnya"> Lainnya
                        </label>
                    </div>
                </div>

                <!-- Insufficient Balance Warning -->
                <div id="balance_warning" style="display: none; margin-top: 10px; background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; padding: 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                    <i class="fa-solid fa-triangle-exclamation" style="margin-right: 5px;"></i> Saldo tidak mencukupi untuk melakukan penarikan!
                </div>
            </div>
            
            <!-- Penarik Section -->
            <div>
                <h6 style="color: var(--primary-navy); border-bottom: 2px solid #64748b; padding-bottom: 5px; margin-bottom: 15px; font-weight: 700;">
                    Data Penarik
                </h6>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">Nama Penarik</label>
                    <input type="text" name="nama_penarik" class="form-control form-control-sm" required value="{{ $data['nama'] }}" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">No HP Penarik</label>
                    <input type="tel" name="hp_penarik" class="form-control form-control-sm" required value="{{ $data['hp'] ?? '-' }}" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">No Identitas (KTP/SIM)</label>
                    <input type="text" name="noid_penarik" class="form-control form-control-sm" value="{{ $data['noid'] ?? '-' }}" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 0.85rem; margin-bottom: 4px;">Alamat Penarik</label>
                    <textarea name="alamat_penarik" class="form-control form-control-sm" rows="1" style="resize: none; height: 38px; background-color: #e9ecef; cursor: not-allowed;" readonly>{{ $data['alamat'] ?? '-' }}</textarea>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px; text-align: right; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 15px;">
            <a href="{{ url('/') }}" class="btn-back" style="margin: 0; font-size: 0.9rem;">&larr; Batal</a>
            <button type="submit" id="submitBtn" class="btn-submit accent-withdrawal" style="width: auto; padding: 10px 40px; font-size: 0.95rem;">
                Proses Penarikan <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </div>
    </form>
</div>

<!-- Reusing JS Helpers -->
<script>
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
        
        validateBalance(value);
    }

    function validateBalance(nominal) {
        const val = parseInt(nominal) || 0;
        const submitBtn = document.getElementById('submitBtn');
        const warning = document.getElementById('balance_warning');
        const inputDisp = document.querySelector('input[name="nominal_display"]');

        if (val > currentBalance) {
            warning.style.display = 'block';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
            inputDisp.style.color = '#dc2626'; // Red
        } else {
            warning.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
            inputDisp.style.color = '#ef4444'; // Original Red shade
        }
    }

    function updateTerbilang(val) {
        let nominal = val.replace(/[^0-9]/g, '');
        if(nominal === '') {
            document.getElementById('terbilang').value = 'Nol Rupiah';
            return;
        }
        document.getElementById('terbilang').value = terbilang(nominal) + ' Rupiah';
    }

    // Client-side Validation (Double check on submit)
    document.querySelector('form').addEventListener('submit', function(e) {
        let nominal = document.getElementById('nominal').value;
        const val = parseInt(nominal) || 0;

        if(val < 10000) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Nominal Kurang',
                text: 'Mohon Maaf, Minimal Penarikan adalah Rp 10.000',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Tutup'
            });
            return;
        }

        if(val > currentBalance) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Saldo Tidak Cukup',
                text: 'Mohon Maaf, Saldo Anda tidak mencukupi untuk penarikan ini.',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Tutup'
            });
        }
    });

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


</script>

<style>
    @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>
@endsection
