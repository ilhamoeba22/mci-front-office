@extends('layouts.app')

@section('content')
<div class="text-center" style="margin-bottom: 40px;">
    <h2 style="color: #fff;">Dashboard Admin</h2>
    <p style="color: rgba(255,255,255,0.7);">Panel Operasional Front Office</p>
</div>

<div class="luxury-grid">
    <!-- Card Antrian CS -->
    <a href="{{ route('admin.queue.list', ['type' => 'CS']) }}" class="luxury-card accent-cs">
        <div class="icon-wrapper">
            <i class="fa-solid fa-users card-icon"></i>
        </div>
        <div class="card-title">Antrian CS</div>
        <div class="card-desc">Kelola Layanan Nasabah</div>
    </a>

    <!-- Card Antrian Teller -->
    <a href="{{ route('admin.queue.list', ['type' => 'Teller']) }}" class="luxury-card accent-teller">
        <div class="icon-wrapper">
            <i class="fa-solid fa-money-check-dollar card-icon"></i>
        </div>
        <div class="card-title">Antrian Teller</div>
        <div class="card-desc">Kelola Layanan Kas</div>
    </a>

    <!-- Card TV Display -->
    <a href="{{ route('display.index') }}" target="_blank" class="luxury-card accent-deposit">
        <div class="icon-wrapper">
            <i class="fa-solid fa-tv card-icon"></i>
        </div>
        <div class="card-title">TV Display</div>
        <div class="card-desc">Tampilan Layar Antrian</div>
    </a>

    <!-- Card Settings -->
    <a href="{{ route('admin.settings') }}" class="luxury-card accent-transfer">
        <div class="icon-wrapper">
            <i class="fa-solid fa-gears card-icon"></i>
        </div>
        <div class="card-title">Pengaturan</div>
        <div class="card-desc">Media & Konfigurasi</div>
    </a>
</div>
@endsection
