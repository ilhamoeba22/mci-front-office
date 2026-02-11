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
        size: 80mm 100mm;
        margin: 0 !important;
    }
    
    @media print {
        .screen-only { display: none !important; }
        .print-only { display: block !important; }
    
        html, body {
            width: 80mm !important;
            height: 100mm !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: white !important;
            overflow: hidden !important;
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-weight: bold;
        }
    
        .ticket-wrapper { 
            margin: 0 !important;
            padding: 0 !important;
            width: 80mm !important; 
            height: 100mm !important;
            display: block !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
        }
    
        .ticket-card { 
            width: 72mm !important; /* Standard printable area for 80mm printers */
            margin: 0 !important; /* Anchored to left */
            padding: 0 !important; /* Anchored to top */
            text-align: center; 
            box-shadow: none !important;
            border: none !important;
            box-sizing: border-box !important;
        }
        
        .ticket-logo { height: 35px; margin-bottom: 2px; margin-top: 2mm; }
        .company-name { font-size: 10px; margin: 0; line-height: 1.1; }
        .company-info { font-size: 7px; margin: 0; line-height: 1.1; }
        
        .divider { 
            margin: 2mm 0; 
            font-size: 8px; 
            font-weight: bold;
            color: #000; 
            overflow: hidden; 
            white-space: nowrap;
            text-align: center;
        }
        
        .queue-title { font-size: 10px; font-weight: 800; text-transform: uppercase; margin-top: 1mm; }
        .queue-number { font-size: 48px; font-weight: 900; margin: 1mm 0; line-height: 1; letter-spacing: -1px; }
        
        .service-type { 
            padding: 2mm 8mm; 
            font-size: 13px; 
            border: 1.5mm solid #000;
            border-radius: 4px;
            margin-bottom: 2mm;
            margin-top: 1mm;
            display: inline-block;
        }
        
        .queue-date { font-size: 9px; margin: 2mm 0 0 0; line-height: 1.2; }
        .ticket-footer p { font-size: 9px; margin: 1px 0; line-height: 1.2; }
        
        /* Force Black Text & High Contrast */
        * { color: black !important; text-shadow: none !important; box-shadow: none !important; filter: none !important; }
    }
</style>
@endsection
