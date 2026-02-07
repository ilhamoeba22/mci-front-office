<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian BPRS MCI Display</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/head_logo.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind & App CSS -->
    @vite(['resources/css/app.css'])
    
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .text-glow {
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
        }
        .animate-marquee {
            animation: marquee 15s linear infinite;
        }
        @keyframes marquee {
            0% { transform: translateX(100vw); }
            100% { transform: translateX(-100%); }
        }
    </style>
</head>
<body class="bg-slate-950 text-white overflow-hidden selection:bg-indigo-500 selection:text-white">

    <!-- Background Gradient Mesh -->
    <div class="fixed inset-0 z-[-1]">
        <div class="absolute top-0 right-0 w-[50vw] h-[50vw] bg-indigo-600/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 left-0 w-[40vw] h-[40vw] bg-emerald-600/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="flex flex-col h-screen w-full">
        
        <!-- HEADER: Logo Area -->
        <div class="h-28 flex items-center justify-between px-10 border-b border-white/10 bg-slate-900/60 backdrop-blur-md z-20 shadow-lg">
            <div class="flex items-center">
                <img src="{{ asset('img/logo_mci.png') }}" alt="Logo" class="h-20 w-auto object-contain drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]">
            </div>
            <!-- Clock in Header -->
            <div class="text-right">
                <div id="clock" class="text-3xl font-mono font-bold text-slate-200 tracking-[0.1em] drop-shadow-md">00:00:00</div>
                <div class="text-sm text-slate-400 uppercase font-semibold tracking-[0.15em]">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT: Split View (Queue + Video) -->
        <div class="flex-1 flex overflow-hidden">
            
            <!-- LEFT: Queue Cards (30%) -->
            <div class="w-[30%] h-full flex flex-col p-6 gap-6 justify-center z-10 glass-panel border-r-0 border-y-0 relative">
                
                <!-- CS Card -->
                <div class="relative group flex-1">
                    <div class="absolute -inset-1 bg-gradient-to-r from-yellow-600 to-amber-600 rounded-[2rem] blur opacity-30 group-hover:opacity-60 transition duration-1000"></div>
                    <div class="relative h-full bg-slate-900/90 rounded-[2rem] p-6 border border-white/10 shadow-xl flex flex-col justify-between overflow-hidden">
                        
                        <!-- Header -->
                        <div class="flex justify-between items-center z-10">
                            <span class="px-4 py-1.5 rounded-full bg-yellow-500/10 text-yellow-400 text-sm font-bold tracking-[0.15em] uppercase border border-yellow-500/20 shadow-[0_0_10px_rgba(234,179,8,0.2)]">
                                Customer Service
                            </span>
                            <div class="w-3 h-3 rounded-full bg-yellow-500 animate-pulse shadow-[0_0_15px_rgba(234,179,8,1)]"></div>
                        </div>

                        <!-- Number -->
                        <div class="flex-1 flex flex-col items-center justify-center z-10">
                             <!-- Reduced font size to text-6xl / 7xl / 8xl fit comfortably -->
                            <span id="cs-number" class="text-6xl xl:text-7xl 2xl:text-8xl leading-none font-black text-white tracking-tighter text-glow scale-100 transition-all duration-300 drop-shadow-2xl whitespace-nowrap">
                                ...
                            </span>
                            <span class="text-slate-500 text-xs font-light tracking-[0.3em] uppercase mt-2">NOMOR ANTRIAN</span>
                        </div>

                        <!-- Status -->
                        <div class="z-10 text-center">
                            <span class="text-yellow-500/80 text-sm font-medium flex items-center justify-center gap-2 bg-yellow-500/5 py-1.5 rounded-lg border border-yellow-500/10">
                                <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Sedang Melayani
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Teller Card -->
                <div class="relative group flex-1">
                    <div class="absolute -inset-1 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-[2rem] blur opacity-30 group-hover:opacity-60 transition duration-1000"></div>
                    <div class="relative h-full bg-slate-900/90 rounded-[2rem] p-6 border border-white/10 shadow-xl flex flex-col justify-between overflow-hidden">
                        
                        <!-- Header -->
                        <div class="flex justify-between items-center z-10">
                            <span class="px-4 py-1.5 rounded-full bg-emerald-500/10 text-emerald-400 text-sm font-bold tracking-[0.15em] uppercase border border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.2)]">
                                Teller
                            </span>
                            <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_15px_rgba(16,185,129,1)]"></div>
                        </div>

                        <!-- Number -->
                        <div class="flex-1 flex flex-col items-center justify-center z-10">
                            <!-- Reduced font size to text-6xl / 7xl / 8xl -->
                            <span id="tl-number" class="text-6xl xl:text-7xl 2xl:text-8xl leading-none font-black text-white tracking-tighter text-glow scale-100 transition-all duration-300 drop-shadow-2xl whitespace-nowrap">
                                ...
                            </span>
                            <span class="text-slate-500 text-xs font-light tracking-[0.3em] uppercase mt-2">NOMOR ANTRIAN</span>
                        </div>

                        <!-- Status -->
                        <div class="z-10 text-center">
                            <span class="text-emerald-500/80 text-sm font-medium flex items-center justify-center gap-2 bg-emerald-500/5 py-1.5 rounded-lg border border-emerald-500/10">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Sedang Melayani
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT: Video/Media (60%) -->
            <div class="flex-1 relative bg-black flex items-center justify-center overflow-hidden">
                @if($mediaSource === 'youtube' && $embedUrl)
                    <iframe 
                        class="w-full h-full"
                        src="{{ $embedUrl }}" 
                        title="YouTube video player" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen>
                    </iframe>
                @else
                    <video autoplay loop playsinline class="w-full h-full object-cover opacity-90">
                        <source src="{{ asset('assets/media/' . $videoFile) }}" type="video/mp4">
                        Browser Anda tidak mendukung tag video.
                    </video>
                @endif
                
                <!-- Gradients for smooth integration (Only for local video usually, but keeps style consistent) -->
                <div class="absolute inset-0 bg-gradient-to-l from-transparent via-transparent to-slate-950/80 w-32 pointer-events-none"></div>
            </div>

        </div>

        <!-- FOOTER: Running Text (Full Width) -->
        <div class="h-16 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border-t border-white/10 flex items-center shadow-[0_-5px_30px_rgba(0,0,0,0.6)] relative overflow-hidden z-30">
            <div class="absolute left-0 top-0 bottom-0 w-24 bg-gradient-to-r from-slate-950 to-transparent z-10"></div>
            <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-slate-950 to-transparent z-10"></div>
            
            <div class="whitespace-nowrap w-full overflow-hidden flex items-center">
                <div class="animate-marquee inline-block text-xl font-medium tracking-wide text-slate-200">
                    {{ $runningText }}
                </div>
            </div>
        </div>

    </div>

    <script>
        let lastCS = '';
        let lastTL = '';

        function updateQueue() {
            fetch('{{ route('api.queue.data') }}')
                .then(response => response.json())
                .then(data => {
                    const csEl = document.getElementById('cs-number');
                    const tlEl = document.getElementById('tl-number');

                    csEl.innerText = data.cs_antri;
                    tlEl.innerText = data.tl_antri;

                    if (data.cs_antri !== lastCS) {
                        lastCS = data.cs_antri;
                        highlightUpdate(csEl);
                    }
                    if (data.tl_antri !== lastTL) {
                        lastTL = data.tl_antri;
                        highlightUpdate(tlEl);
                    }
                })
                .catch(error => console.error('Error fetching queue data:', error));
        }

        function highlightUpdate(element) {
            element.classList.add('scale-110', 'text-yellow-300');
            setTimeout(() => {
                element.classList.remove('scale-110', 'text-yellow-300');
            }, 500);
        }

        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('en-GB', { hour12: false });
        }

        setInterval(updateQueue, 2000);
        setInterval(updateClock, 1000);
        updateQueue();
        updateClock();
    </script>
</body>
</html>
