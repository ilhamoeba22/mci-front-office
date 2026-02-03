@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 600px; margin: 40px auto;">
    <h3>Pengaturan Media Display</h3>
    
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <form action="{{ route('admin.settings.media.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label>Video Saat Ini</label>
                <div style="margin-bottom: 10px; font-weight: bold; color: #555;">
                    {{ $video ? $video->value : 'Belum ada video (Menggunakan Default)' }}
                </div>
            </div>

            <div class="form-group">
                <label>Upload Video Baru (mp4, max 20MB)</label>
                <input type="file" name="video" class="form-control" accept="video/mp4,video/x-m4v,video/*" required>
            </div>

            <div class="text-right">
                <button type="submit" class="btn">Simpan & Update Display</button>
            </div>
        </form>
    </div>
    
    <div style="margin-top: 20px; text-align: center;">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">Kembali ke Dashboard</a>
    </div>
</div>
@endsection
