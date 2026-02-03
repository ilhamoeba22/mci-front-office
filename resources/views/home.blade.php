@extends('layouts.app')

@section('content')

<!-- Welcome Text: Professional, Elegant, Centered -->
<div class="welcome-container" style="margin-bottom: 5px; text-align: center;">
    <h3 style="color: var(--text-muted); font-size: 1.2rem; font-weight: 500; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 10px;">
        Assalamualaikum Warahmatullahi Wabarakatuh
    </h3>
    <h1 style="color: var(--primary-navy); font-size: 2.8rem; font-weight: 800; margin: 0; line-height: 1.2;">
        Selamat Datang di <span style="color: #10b981;">BPRS HIK MCI</span>
    </h1>
    <p style="color: var(--text-muted); font-size: 1.15rem; margin-top: 15px; line-height: 1.6; max-width: 700px; margin-left: auto; margin-right: auto;">
        Solusi Perbankan Syariah Digital yang <strong>Cepat</strong>, <strong>Aman</strong>, dan <strong>Penuh Berkah</strong>.<br>
        Silakan pilih layanan transaksi Anda di bawah ini.
    </p>
</div>

<!-- Luxury Grid -->
<div class="luxury-grid">
    
    <!-- 1. Antrian CS -->
    <form action="{{ route('queue.store') }}" method="POST" style="display: contents;">
        @csrf
        <input type="hidden" name="jenis" value="CS">
        <button type="submit" class="luxury-card accent-cs">
            <div class="icon-wrapper">
                <i class="fa-solid fa-headset card-icon"></i>
            </div>
            <div class="card-title">Customer Service</div>
            <div class="card-desc">Pembukaan Rekening & Layanan Informasi</div>
        </button>
    </form>

    <!-- 2. Antrian Teller -->
    <form action="{{ route('queue.store') }}" method="POST" style="display: contents;">
        @csrf
        <input type="hidden" name="jenis" value="Teller">
        <button type="submit" class="luxury-card accent-teller">
            <div class="icon-wrapper">
                <i class="fa-solid fa-money-bill-wave card-icon"></i>
            </div>
            <div class="card-title">Teller</div>
            <div class="card-desc">Setor Tunai, Tarik Tunai & Pembayaran</div>
        </button>
    </form>

    <!-- 3. Setor Tunai -->
    <a href="{{ route('transaction.deposit.create') }}" class="luxury-card accent-deposit">
        <div class="icon-wrapper">
            <i class="fa-solid fa-wallet card-icon"></i>
        </div>
        <div class="card-title">Setor Tunai</div>
        <div class="card-desc">Formulir Setoran Tunai Cepat & Mudah</div>
    </a>

    <!-- 4. Tarik Tunai -->
    <a href="{{ route('transaction.withdrawal.create') }}" class="luxury-card accent-withdrawal">
        <div class="icon-wrapper">
            <i class="fa-solid fa-hand-holding-dollar card-icon"></i>
        </div>
        <div class="card-title">Tarik Tunai</div>
        <div class="card-desc">Formulir Penarikan Dana Tunai Instan</div>
    </a>

    <!-- 5. Transfer -->
    <a href="{{ route('transaction.transfer.create') }}" class="luxury-card accent-transfer">
        <div class="icon-wrapper">
            <i class="fa-solid fa-paper-plane card-icon"></i>
        </div>
        <div class="card-title">Transfer</div>
        <div class="card-desc">Layanan Kirim Uang Antar Bank Aman</div>
    </a>

</div>

@endsection
