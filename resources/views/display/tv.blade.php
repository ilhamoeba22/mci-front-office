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
        :root {
            /* Light Mode Defaults */
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(203, 213, 225, 0.4);
            --text-glow-shadow: 0 0 20px rgba(79, 70, 229, 0.2);
        }
        .dark {
            /* Dark Mode Overrides */
            --glass-bg: rgba(15, 23, 42, 0.6);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-glow-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
        }

        body { font-family: 'Outfit', sans-serif; transition: background-color 0.5s ease, color 0.5s ease; }
        
        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            transition: background 0.5s ease, border-color 0.5s ease;
        }
    </style>
    <script>
        // Check local storage or system preference on load
        // KEY FIXED: 'admin-theme' instead of 'theme'
        if (localStorage['admin-theme'] === 'dark' || (!('admin-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-white overflow-hidden font-outfit">
    <!-- Vue Mount Point -->
    <div id="app"></div>

    <!-- Pass Data to Vue -->
    <script>
        window.tvConfig = {
            initialVideoFile: "{{ $videoFile }}",
            initialRunningText: "{{ $runningText }}",
            initialMediaSource: "{{ $mediaSource }}",
            initialEmbedUrl: "{!! $embedUrl !!}"
        };
    </script>
    
    <!-- Load Vue Entry Point -->
    @vite(['resources/js/tv.js'])
</body>
</html>
