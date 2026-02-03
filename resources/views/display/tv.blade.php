<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian BPRS MCI</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; background: #000; color: white; overflow: hidden; }
        .container { display: flex; height: 100vh; }
        .left-panel { width: 35%; display: flex; flex-direction: column; }
        .queue-box { flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; border-bottom: 1px solid #333; }
        .queue-title { font-size: 2vw; text-transform: uppercase; letter-spacing: 2px; color: #aaa; margin-bottom: 10px; }
        .queue-number { font-size: 8vw; font-weight: bold; color: #fff; }
        .cs-box { background: #222; border-right: 5px solid #ffc107; }
        .teller-box { background: #222; border-right: 5px solid #28a745; }
        
        .right-panel { width: 65%; display: flex; flex-direction: column; }
        .video-container { flex: 1; background: #000; position: relative; }
        video { width: 100%; height: 100%; object-fit: cover; }
        
        .running-text { height: 60px; background: #d00; display: flex; align-items: center; white-space: nowrap; overflow: hidden; }
        .marquee { display: inline-block; padding-left: 100%; animation: marquee 20s linear infinite; font-size: 24px; font-weight: bold; }
        
        @keyframes marquee { 0% { transform: translate(0, 0); } 100% { transform: translate(-100%, 0); } }
    </style>
</head>
<body>

<div class="container">
    <div class="left-panel">
        <div class="queue-box cs-box">
            <div class="queue-title">Antrian CS</div>
            <div class="queue-number" id="cs-number">Loading...</div>
        </div>
        <div class="queue-box teller-box">
            <div class="queue-title">Antrian Teller</div>
            <div class="queue-number" id="tl-number">Loading...</div>
        </div>
    </div>
    <div class="right-panel">
        <div class="video-container">
            <!-- Video diputar otomatis -->
            <video autoplay loop muted playsinline>
                <source src="{{ asset('assets/media/' . $videoFile) }}" type="video/mp4">
                Browser Anda tidak mendukung tag video.
            </video>
        </div>
        <div class="running-text">
            <div class="marquee">
                SELAMAT DATANG DI BPRS HIK MCI - PENAWARAN KHUSUS BULAN INI: DEPOSITO BERJANGKA NISBAH KOMPETITIF - HUBUNGI CS KAMI UNTUK INFO LEBIH LANJUT
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi Polling AJAX untuk update nomor antrian
    function updateQueue() {
        fetch('{{ route('api.queue.data') }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('cs-number').innerText = data.cs_antri;
                document.getElementById('tl-number').innerText = data.tl_antri;
            })
            .catch(error => console.error('Error fetching queue data:', error));
    }

    // Jalankan setiap 2 detik
    setInterval(updateQueue, 2000);
    updateQueue(); // Jalankan langsung saat load
</script>

</body>
</html>
