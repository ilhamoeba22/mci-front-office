@extends('layouts.app')

@section('content')
<div class="ticket-container" style="perspective: 1000px; display: flex; justify-content: center; align-items: center; min-height: 60vh;">
    
    <div class="ticket-card {{ $queue->type == 'CS' ? 'accent-cs' : 'accent-teller' }}" 
         style="width: 100%; max-width: 380px; border-radius: 25px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);">
        
        <!-- Header Colorful -->
        <div style="padding: 30px; text-align: center; color: white;">
            <p style="margin: 0; font-size: 0.9rem; opacity: 0.9; text-transform: uppercase; letter-spacing: 2px;">Nomor Antrian Anda</p>
            <h1 style="font-size: 5rem; font-weight: 800; margin: 10px 0; text-shadow: 0 4px 6px rgba(0,0,0,0.1);">{{ $queue->antrian }}</h1>
            <div style="background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; display: inline-block;">
                <i class="fa-solid {{ $queue->type == 'CS' ? 'fa-headset' : 'fa-money-bill-wave' }}"></i>
                <span style="font-weight: 600; margin-left: 5px;">{{ $queue->type == 'CS' ? 'Customer Service' : 'Teller' }}</span>
            </div>
        </div>

        <!-- Body White -->
        <div style="background: white; padding: 30px; text-align: center; position: relative;">
            <!-- Ticket Cutout Effect -->
            <div style="position: absolute; top: -15px; left: -15px; width: 30px; height: 30px; background: var(--light-bg); border-radius: 50%;"></div>
            <div style="position: absolute; top: -15px; right: -15px; width: 30px; height: 30px; background: var(--light-bg); border-radius: 50%;"></div>
            
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 5px;">Tanggal</p>
            <h4 style="color: var(--primary-navy); margin: 0; font-size: 1.1rem;">{{ Carbon\Carbon::parse($queue->tgl_antri)->translatedFormat('l, d F Y') }}</h4>
            
            <div style="margin-top: 30px; display: grid; gap: 10px;">
                <button onclick="window.print()" class="btn-action btn-print">
                    <i class="fa-solid fa-print"></i> Cetak Tiket
                </button>
                <a href="{{ url('/') }}" class="btn-action btn-home">
                    Kembali ke Menu
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes slideUp { from { transform: translateY(50px) rotateX(10deg); opacity: 0; } to { transform: translateY(0) rotateX(0); opacity: 1; } }
    
    .btn-action {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: transform 0.2s;
        text-decoration: none;
        display: inline-block;
        box-sizing: border-box;
    }
    
    .btn-print {
        background: var(--primary-navy);
        color: white;
    }
    
    .btn-home {
        background: #f1f5f9;
        color: var(--text-muted);
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        filter: brightness(1.1);
    }

    /* Print Styles */
    @media print {
        body * { visibility: hidden; }
        .ticket-card, .ticket-card * { visibility: visible; }
        .ticket-card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; border: 1px solid #ccc; }
        .btn-action { display: none; }
    }
</style>
@endsection
