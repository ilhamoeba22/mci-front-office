@extends('layouts.app')

@section('content')

<!-- SCREEN VIEW (Modern Colorful Card) -->
<div class="screen-only ticket-container" style="perspective: 1000px; display: flex; justify-content: center; align-items: center; min-height: 60vh;">
    
    <div class="ticket-card-modern {{ $queue->type == 'CS' ? 'accent-cs' : 'accent-teller' }}" 
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

<!-- PRINT VIEW (Thermal B&W) -->
<div class="print-only ticket-wrapper">
    <div class="ticket-card">
        <!-- Header -->
        <div class="ticket-header">
            <img src="{{ asset('img/logo_mci.png') }}" alt="MCI Logo" class="ticket-logo">
            <p class="company-name">PT. BPRS HIK MCI</p>
            <p class="company-info">Jl. Kaliurang KM 9, Yogyakarta</p>
            <p class="company-info">Telp: (0274) 123456</p>
        </div>

        <div class="divider">================================</div>

        <!-- Content -->
        <div class="ticket-body">
            <p class="queue-title">NOMOR ANTRIAN ANDA</p>
            <h1 class="queue-number">{{ $queue->antrian }}</h1>
            
            <div class="service-type {{ $queue->type == 'CS' ? 'type-cs' : 'type-teller' }}">
                {{ $queue->type == 'CS' ? 'CUSTOMER SERVICE' : 'TELLER' }}
            </div>

            <p class="queue-date">
                {{ Carbon\Carbon::parse($queue->tgl_antri)->translatedFormat('l, d F Y') }}<br>
                {{ Carbon\Carbon::parse($queue->updated_at)->format('H:i') }}
            </p>
        </div>

        <div class="divider">================================</div>

        <!-- Footer -->
        <div class="ticket-footer">
            <p>Silakan menunggu nomor antrian<br>anda dipanggil.</p>
            <p style="margin-top: 10px; font-weight: bold;">Terima Kasih</p>
            <p style="font-size: 10px; margin-top: 5px;">www.bprshikmci.co.id</p>
        </div>
    </div>
</div>

<style>
    /* SHARED STYLES */
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
    
    .btn-print { background: var(--primary-navy); color: white; }
    .btn-home { background: #f1f5f9; color: var(--text-muted); }
    .btn-action:hover { transform: translateY(-2px); filter: brightness(1.1); }

    /* SCREEN ONLY STYLES */
    @media screen {
        .print-only { display: none !important; }
        .screen-only { display: flex !important; }
        
        /* Modern Card Styles */
        .accent-cs { background: linear-gradient(135deg, #6366f1 0%, #3b82f6 100%); }
        .accent-teller { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    }

    /* PRINT ONLY STYLES */
    /* PRINT SPECIFIC STYLES - @page must be top level */
    @page {
        size: 80mm auto; /* Force 80mm width, auto height */
        margin: 0mm; /* No margins from browser */
    }

    @media print {
        .screen-only { display: none !important; }
        .print-only { display: block !important; }

        html, body {
            width: 80mm;
            max-width: 80mm;
            min-width: 80mm;
            margin: 0;
            padding: 0;
            background-color: white;
            /* Ensure fonts are crisp for thermal */
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }

        .ticket-wrapper { 
            padding: 0; 
            width: 100%; 
            display: block;
        }

        .ticket-card { 
            width: 100%; 
            /* 78mm allows 1mm breathing room on each side */
            max-width: 78mm; 
            margin: 0 auto;
            padding: 2mm 0;
            text-align: center; 
            box-shadow: none;
            border: none;
        }
        
        .ticket-logo { height: 45px; margin-bottom: 2px; }
        .company-name { font-size: 11px; }
        .company-info { font-size: 8px; }
        
        .divider { margin: 2px 0; font-size: 9px; letter-spacing: 2px; border-bottom: 1px dashed black; height: 1px; color: transparent; overflow: hidden; }
        /* Alternative divider using characters if image/border fails: */
        /* .divider { font-weight: bold; margin: 3px 0; white-space: nowrap; overflow: hidden; color: #000; font-size: 8px; } */
        
        .queue-title { font-size: 10px; font-weight: 800; text-transform: uppercase; margin-top: 5px; }
        .queue-number { font-size: 52px; font-weight: 900; margin: 0; line-height: 1.1; letter-spacing: -2px; }
        
        .service-type { 
            padding: 3px 10px; 
            font-size: 12px; 
            border: 2px solid #000;
            border-radius: 6px;
            margin-bottom: 5px;
            margin-top: 2px;
            display: inline-block;
        }
        
        .queue-date { font-size: 9px; margin: 5px 0 0 0; }
        .ticket-footer p { font-size: 9px; margin: 1px 0; }
        
        /* Force Black Text & High Contrast */
        * { color: black !important; text-shadow: none !important; box-shadow: none !important; filter: none !important; }
    }
</style>
@endsection
