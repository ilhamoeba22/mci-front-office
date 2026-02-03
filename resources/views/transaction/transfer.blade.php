@extends('layouts.app')

@section('content')
<div class="form-card" style="max-width: 950px; padding: 0; overflow: hidden; animation: slideUp 0.6s ease-out;">
    
    <!-- Account Info Header (Violet Theme) -->
    <div style="background: var(--bg-gradient); padding: 30px; border-bottom: 1px solid rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="width: 60px; height: 60px; background: white; border-radius: 15px; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-soft);">
                    <i class="fa-solid fa-paper-plane" style="font-size: 1.8rem; color: #8b5cf6;"></i>
                </div>
                <div>
                    <h5 style="margin: 0; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase;">Transfer Dari Rekening</h5>
                    <h2 style="margin: 5px 0 0; color: var(--primary-navy);">{{ $data['nama'] }}</h2>
                    <p style="margin: 0; font-family: monospace; font-size: 1.1rem; color: var(--text-primary);">{{ $data['no_rek'] }}</p>
                </div>
            </div>
            
            <div style="text-align: right; background: rgba(139, 92, 246, 0.1); padding: 10px 20px; border-radius: 12px; border: 1px solid rgba(139, 92, 246, 0.2);">
                <span style="font-size: 0.85rem; color: #8b5cf6; font-weight: 600;">SALDO SAAT INI</span>
                <div style="font-size: 1.2rem; font-weight: 700; color: var(--primary-navy);">
                    Rp {{ number_format($data['saldo'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('transaction.transfer.store') }}" method="POST" style="padding: 40px;">
        @csrf
        <input type="hidden" name="nama" value="{{ $data['nama'] }}">
        <input type="hidden" name="no_rek" value="{{ $data['no_rek'] }}">
        <input type="hidden" name="tgl" value="{{ date('Y-m-d') }}">
        
        <!-- Legacy fields -->
        <input type="hidden" name="nama_penyetor" value="{{ $data['nama'] }}">
        <input type="hidden" name="hp_penyetor" value="{{ $data['hp'] ?? '' }}">
        <input type="hidden" name="alamat_penyetor" value="{{ $data['alamat'] ?? '' }}">

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 50px;">
            
            <!-- Transfer Values -->
            <div>
                <h4 style="color: var(--primary-navy); border-bottom: 2px solid #8b5cf6; padding-bottom: 10px; margin-bottom: 20px; display: inline-block;">
                    Detail Transaksi
                </h4>

                <div class="form-group">
                    <label class="form-label">Nominal Transfer (Rp)</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 20px; top: 15px; font-weight: 600; color: var(--text-muted);">Rp</span>
                        <input type="text" 
                               name="nominal_display" 
                               class="form-control focus-transfer" 
                               style="padding-left: 50px; font-size: 1.5rem; font-weight: 700; color: #8b5cf6;" 
                               placeholder="0" 
                               required 
                               onkeyup="formatRupiah(this); updateTerbilang(this.value); calculateTotal();">
                        <input type="hidden" name="nominal" id="nominal" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Terbilang</label>
                    <textarea name="terbilang" 
                              id="terbilang" 
                              class="form-control" 
                              rows="2" 
                              readonly 
                              style="background: #f8fafc; color: var(--text-muted); font-style: italic; resize: none; border-color: transparent;">Nol Rupiah</textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tujuan / Identitas Transaksi</label>
                    <input type="text" name="tujuan" class="form-control" placeholder="Contoh: Pembayaran Invoice" required>
                </div>

                <!-- Fee Display -->
                <div style="background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px dashed #cbd5e1; margin-top: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.9rem;">
                        <span>Biaya Admin Bank:</span>
                        <span id="fee_display" style="font-weight: 600;">Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 700; color: var(--primary-navy); border-top: 1px solid #e2e8f0; padding-top: 5px;">
                        <span>Total Debet:</span>
                        <span id="total_display">Rp 0</span>
                    </div>
                </div>
            </div>
            
            <!-- Recipient -->
            <div>
                <h4 style="color: var(--primary-navy); border-bottom: 2px solid #64748b; padding-bottom: 10px; margin-bottom: 20px; display: inline-block;">
                    Detail Penerima
                </h4>

                <div class="form-group">
                    <label class="form-label">Bank Tujuan</label>
                    <select name="bank_tujuan" id="bank_tujuan" class="form-control" required onchange="updateFee()">
                        <option value="" data-fee="0">-- Pilih Bank --</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->nama_bank }}/{{ $bank->biaya_trf }}" data-fee="{{ $bank->biaya_trf }}">
                                {{ $bank->nama_bank }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Rekening Tujuan</label>
                    <input type="number" name="no_rek_tujuan" class="form-control" required placeholder="Masukkan No Rekening">
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Pemilik Rekening</label>
                    <input type="text" name="nama_tujuan" class="form-control" required placeholder="Nama Penerima">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Berita / Pesan (Opsional)</label>
                    <input type="text" name="berita_tujuan" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">No HP Penerima (Opsional)</label>
                    <input type="tel" name="hp_penerima" class="form-control">
                </div>
            </div>
        </div>

        <div style="margin-top: 40px; text-align: right; display: flex; justify-content: space-between; align-items: center;">
            <a href="{{ url('/') }}" class="btn-back" style="margin: 0;">&larr; Batal</a>
            <button type="submit" class="btn-submit accent-transfer" style="width: auto; padding-left: 50px; padding-right: 50px;">
                Kirim Transfer <i class="fa-solid fa-paper-plane" style="margin-left: 10px;"></i>
            </button>
        </div>
    </form>
</div>

<!-- JS Helpers -->
<script>
    let currentFee = 0;

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
        const option = select.options[select.selectedIndex];
        const fee = option.getAttribute('data-fee');
        
        currentFee = parseInt(fee) || 0;
        
        // Format Fee
        document.getElementById('fee_display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(currentFee);
        
        calculateTotal();
    }

    function calculateTotal() {
        let nominal = parseInt(document.getElementById('nominal').value) || 0;
        let total = nominal + currentFee;
        
        document.getElementById('total_display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
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
