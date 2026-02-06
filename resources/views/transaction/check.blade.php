@extends('layouts.app')

@section('content')
<div class="form-card" style="animation: slideUp 0.5s ease-out;">
    
    <!-- Header with Dynamic Icon & Color -->
    <div class="text-center" style="margin-bottom: 30px;">
        @php
            $icon = 'fa-search';
            $color = 'var(--primary-navy)';
            $text = 'Cek Rekening';
            $bgClass = '';
            
            if($type == 'deposit') { 
                $icon = 'fa-wallet'; $color = '#0ea5e9'; $text = 'Setor Tunai'; $bgClass = 'accent-deposit';
            } elseif($type == 'withdrawal') { 
                $icon = 'fa-hand-holding-dollar'; $color = '#ef4444'; $text = 'Tarik Tunai'; $bgClass = 'accent-withdrawal';
            } elseif($type == 'transfer') { 
                $icon = 'fa-paper-plane'; $color = '#8b5cf6'; $text = 'Transfer Online'; $bgClass = 'accent-transfer';
            }
        @endphp

        <div style="width: 80px; height: 80px; background: {{ $color }}15; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: {{ $color }}; font-size: 2.5rem;">
            <i class="fa-solid {{ $icon }}"></i>
        </div>
        
        <h2 style="margin: 0; color: var(--primary-navy);">{{ $text }}</h2>
        <p style="color: var(--text-muted);">Masukkan nomor rekening nasabah</p>
    </div>

    <form action="{{ route('transaction.check') }}" method="POST" hx-boost="false">
        @csrf
        @if($errors->any())
        <div style="background: #fee2e2; border: 1px solid #fecaca; color: #ef4444; padding: 15px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.2rem;"></i>
            <div style="text-align: left;">
                <strong style="display: block; font-size: 0.9rem;">Terjadi Kesalahan!</strong>
                <span style="font-size: 0.85rem;">{{ $errors->first() }}</span>
            </div>
        </div>
        @endif
        
        <input type="hidden" name="type" value="{{ $type }}">
        
        <div class="form-group">
            <label for="no_rek" class="form-label">Nomor Rekening</label>
            <input type="number" 
                   name="no_rek" 
                   id="no_rek" 
                   class="form-control focus-{{ $type }}" 
                   required 
                   placeholder="Contoh: 1020304050" 
                   autofocus
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        </div>

        <button type="submit" class="btn-submit {{ $bgClass }}" 
                onclick="this.innerHTML='<i class=\'fa-solid fa-spinner fa-spin\'></i> Memproses...'; this.disabled=true; this.form.submit();">
            Cek Rekening <i class="fa-solid fa-arrow-right" style="margin-left: 10px;"></i>
        </button>
        
        <div class="text-center">
            <a href="{{ url('/') }}" class="btn-back">
                <i class="fa-solid fa-chevron-left"></i> Kembali ke Menu
            </a>
        </div>
    </form>
</div>

<style>
    @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>
@endsection
