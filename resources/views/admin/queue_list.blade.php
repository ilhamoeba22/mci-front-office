@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 900px; margin: 30px auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3>Antrian {{ $type }} ({{ date('d-m-Y') }})</h3>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">Kembali ke Dashboard</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 10px; text-align: left;">No Antrian</th>
                    <th style="padding: 10px; text-align: left;">Nama Nasabah</th>
                    <th style="padding: 10px; text-align: left;">Status</th>
                    <th style="padding: 10px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($queues as $q)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px; font-weight: bold;">{{ $q->antrian }}</td>
                    <td style="padding: 10px;">{{ $q->nama_antrian }}</td>
                    <td style="padding: 10px;">
                        @if($q->st_antrian == '0') <span style="background: #e2e3e5; padding: 3px 8px; border-radius: 5px; font-size: 0.85em;">Menunggu</span>
                        @elseif($q->st_antrian == '2') <span style="background: #c3e6cb; color: #155724; padding: 3px 8px; border-radius: 5px; font-size: 0.85em;">Dipanggil</span>
                        @endif
                    </td>
                    <td style="padding: 10px; text-align: center;">
                        <form action="{{ route('admin.queue.call', $q->id_antrian) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm" style="background: #007bff; color: white;">Panggil</button>
                        </form>
                        
                        <form action="{{ route('admin.queue.finish', $q->id_antrian) }}" method="POST" style="display: inline; margin-left: 5px;">
                            @csrf
                            <button type="submit" class="btn btn-sm" style="background: #28a745; color: white;">Selesai</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #888;">Belum ada antrian hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
