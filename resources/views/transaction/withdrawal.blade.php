@extends('layouts.app')

@section('content')
<div class="form-card" style="max-width: 900px; padding: 0; overflow: hidden; animation: slideUp 0.6s ease-out;">
    
    <!-- Account Info Header (Red Theme) -->
    <div style="background: var(--bg-gradient); padding: 30px; border-bottom: 1px solid rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="width: 60px; height: 60px; background: white; border-radius: 15px; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-soft);">
                    <i class="fa-solid fa-hand-holding-dollar" style="font-size: 1.8rem; color: #ef4444;"></i>
                </div>
                <div>
                    <h5 style="margin: 0; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase;">Tarik Tunai Dari Rekening</h5>
                    <h2 style="margin: 5px 0 0; color: var(--primary-navy);">{{ $data['nama'] }}</h2>
                    <p style="margin: 0; font-family: monospace; font-size: 1.1rem; color: var(--text-primary);">{{ $data['no_rek'] }}</p>
                </div>
            </div>
            
            <div style="text-align: right; background: rgba(239, 68, 68, 0.1); padding: 10px 20px; border-radius: 12px; border: 1px solid rgba(239, 68, 68, 0.2);">
                <span style="font-size: 0.85rem; color: #ef4444; font-weight: 600;">SALDO SAAT INI</span>
                <div style="font-size: 1.2rem; font-weight: 700; color: var(--primary-navy);">
                    Rp {{ number_format($data['saldo'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('transaction.withdrawal.store') }}" method="POST" style="padding: 40px;">
        @csrf
        <input type="hidden" name="nama" value="{{ $data['nama'] }}">
        <input type="hidden" name="no_rek" value="{{ $data['no_rek'] }}">
        <input type="hidden" name="tgl" value="{{ date('Y-m-d') }}">

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 40px;">
            
            <!-- Nominal Section -->
            <div>
                <h4 style="color: var(--primary-navy); border-bottom: 2px solid #ef4444; padding-bottom: 10px; margin-bottom: 20px; display: inline-block;">
                    Detail Penarikan
                </h4>

                <div class="form-group">
                    <label class="form-label">Nominal Penarikan (Rp)</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 20px; top: 15px; font-weight: 600; color: var(--text-muted);">Rp</span>
                        <input type="text" 
                               name="nominal_display" 
                               class="form-control focus-withdrawal" 
                               style="padding-left: 50px; font-size: 1.5rem; font-weight: 700; color: #ef4444;" 
                               placeholder="0" 
                               required 
                               onkeyup="formatRupiah(this); updateTerbilang(this.value);">
                        <input type="hidden" name="nominal" id="nominal" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Terbilang</label>
                    <textarea name="terbilang" 
                              id="terbilang" 
                              class="form-control" 
                              rows="3" 
                              readonly 
                              style="background: #f8fafc; color: var(--text-muted); font-style: italic; resize: none; border-color: transparent;">Nol Rupiah</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Tujuan Transaksi</label>
                    <input type="text" name="tujuan" class="form-control" placeholder="Contoh: Belanja Bulanan" required>
                </div>
            </div>
            
            <!-- Penarik Section -->
            <div>
                <h4 style="color: var(--primary-navy); border-bottom: 2px solid #64748b; padding-bottom: 10px; margin-bottom: 20px; display: inline-block;">
                    Data Penarik
                </h4>

                <div class="form-group">
                    <label class="form-label">Nama Penarik</label>
                    <input type="text" name="nama_penarik" class="form-control" required value="{{ $data['nama'] }}">
                </div>

                <div class="form-group">
                    <label class="form-label">No HP Penarik</label>
                    <input type="tel" name="hp_penarik" class="form-control" required value="{{ $data['hp'] ?? '' }}">
                </div>

                <div class="form-group">
                    <label class="form-label">No Identitas (KTP/SIM)</label>
                    <input type="text" name="noid_penarik" class="form-control" value="{{ $data['noid'] ?? '' }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Penarik</label>
                    <textarea name="alamat_penarik" class="form-control" rows="2">{{ $data['alamat'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <div style="margin-top: 40px; text-align: right; display: flex; justify-content: space-between; align-items: center;">
            <a href="{{ url('/') }}" class="btn-back" style="margin: 0;">&larr; Batal</a>
            <button type="submit" class="btn-submit accent-withdrawal" style="width: auto; padding-left: 50px; padding-right: 50px;">
                Proses Penarikan <i class="fa-solid fa-arrow-right" style="margin-left: 10px;"></i>
            </button>
        </div>
    </form>
</div>

<!-- Reusing JS Helpers (Ideally moves to separate file, but kept inline for simplicity) -->
<script>
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
