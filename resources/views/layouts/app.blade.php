<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BPRS HIK MCI') }}</title>
    
    <link rel="icon" href="{{ asset('img/head_logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/htmx.org@1.9.6"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body hx-boost="true">

    <div class="htmx-indicator"></div>

    <!-- Header: Centered Logo with Glassmorphism -->
    <header class="main-header">
        <a href="{{ url('/') }}" class="logo-container">
            <img src="{{ asset('img/logo_mci.png') }}" alt="Logo MCI" class="logo-img">
        </a>
    </header>

    <!-- Main Content -->
    <main id="main-content">
        @if(session('success'))
            <div class="flash-message" style="background: rgba(16, 185, 129, 0.9); backdrop-filter: blur(5px); color: white; padding: 15px 30px; border-radius: 50px; margin-bottom: 30px; box-shadow: 0 10px 20px rgba(0,0,0,0.2); animation: fadeInDown 0.5s;">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="main-footer">
        &copy; {{ date('Y') }} BPRS HIK MCI. Mitra Cahaya Indonesia.
    </footer>

    <style>
        @keyframes fadeInDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        @media print {
            .main-header, .main-footer, .htmx-indicator, .flash-message { display: none !important; }
            body { background: white; }
            main { padding: 0; margin: 0; }
        }
    </style>
</body>
</html>
