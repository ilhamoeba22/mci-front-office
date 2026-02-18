@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div id="survey-app" class="survey-container theme-default">
    <!-- Background Elements -->
    <div class="glow-orb" id="orb-1"></div>
    <div class="glow-orb" id="orb-2"></div>

    <!-- Standby Mode -->
    <div id="standby-screen" class="screen active">
        <div class="standby-glass">
            <div class="brand-section">
                <img src="{{ asset('img/logo_mci.png') }}" class="main-logo animate__animated animate__fadeInDown" alt="Logo">
                <h1 class="animate__animated animate__fadeInUp">Selamat Datang</h1>
                <p class="animate__animated animate__fadeInUp animate__delay-1s">PT BPRS HIK MCI</p>
            </div>
            
            <div class="promo-box animate__animated animate__fadeInUp animate__delay-2s">
                <div class="promo-text">"Melayani dengan Amanah & Penuh Keberkahan"</div>
            </div>

            <div class="start-instruction animate__animated animate__pulse animate__infinite">
                <button onclick="showSurvey()" class="btn-premium-start">
                    <span>Mulai Survey</span>
                    <i class="fa-solid fa-chevron-right ml-2"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Survey Mode -->
    <div id="survey-screen" class="screen">
        <div class="survey-card-modern animate__animated animate__zoomIn">
            <div class="card-header">
                <div class="staff-profile-badge" id="staff-info">
                    <div class="staff-avatar">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div class="staff-details">
                        <span class="staff-role-tag" id="staff-role">STAFF</span>
                        <div class="staff-name-text" id="staff-name">Mitra Kami</div>
                        <div class="staff-loket-text">LOKET <span id="staff-counter">0</span></div>
                    </div>
                </div>
                <h2>Bagaimana Pelayanan Kami?</h2>
                <p>Klik salah satu icon untuk memberikan penilaian Anda</p>
            </div>

            <div class="modern-emoji-grid">
                <button class="m-emoji-btn" onclick="submitRating(4, this)" data-rating="4">
                    <div class="m-emoji-box">🤩</div>
                    <span class="m-label">Sangat Puas</span>
                </button>
                <button class="m-emoji-btn" onclick="submitRating(3, this)" data-rating="3">
                    <div class="m-emoji-box">🙂</div>
                    <span class="m-label">Puas</span>
                </button>
                <button class="m-emoji-btn" onclick="submitRating(2, this)" data-rating="2">
                    <div class="m-emoji-box">😐</div>
                    <span class="m-label">Cukup</span>
                </button>
                <button class="m-emoji-btn" onclick="submitRating(1, this)" data-rating="1">
                    <div class="m-emoji-box">😞</div>
                    <span class="m-label">Kurang</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Success Mode -->
    <div id="success-screen" class="screen">
        <div class="success-glass animate__animated animate__bounceIn">
            <div class="success-icon-wrapper">
                <div class="success-checkmark">
                    <svg class="checkmark-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
            </div>
            <h2>Terima Kasih!</h2>
            <p>Penilaian Anda sangat berharga bagi peningkatan layanan kami.</p>
            <div class="reset-progress">
                <div class="progress-bar" id="success-progress"></div>
                <span>Kembali otomatis dalam <b id="timer">3</b>s</span>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --p-primary: #0ea5e9;
        --p-primary-light: rgba(14, 165, 233, 0.1);
        --p-accent: #38bdf8;
        --p-bg: #0f172a;
        --p-card: rgba(30, 41, 59, 0.7);
        --p-glass: rgba(255, 255, 255, 0.05);
        --p-border: rgba(255, 255, 255, 0.1);
    }

    /* Role Theming */
    .theme-teller {
        --p-primary: #0d9488;
        --p-primary-light: rgba(13, 148, 136, 0.1);
        --p-accent: #2dd4bf;
    }

    .theme-cs {
        --p-primary: #e11d48;
        --p-primary-light: rgba(225, 29, 72, 0.1);
        --p-accent: #fb7185;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        overflow: hidden;
        background: var(--p-bg);
        font-family: 'Outfit', sans-serif;
    }

    .survey-container {
        position: fixed;
        inset: 0;
        background: var(--p-bg);
        color: white;
        z-index: 10000;
        display: flex;
        overflow: hidden;
    }

    /* Decorative Orbs */
    .glow-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.3;
        z-index: 1;
        transition: all 1s ease;
    }
    #orb-1 { width: 400px; height: 400px; background: var(--p-primary); top: -100px; right: -100px; }
    #orb-2 { width: 300px; height: 300px; background: var(--p-accent); bottom: -50px; left: -50px; }

    .screen {
        position: absolute;
        inset: 0;
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px;
        z-index: 2;
    }
    .screen.active { display: flex; }

    /* Standby Glass */
    .standby-glass {
        background: var(--p-glass);
        backdrop-filter: blur(20px);
        border: 1px solid var(--p-border);
        padding: 60px;
        border-radius: 40px;
        text-align: center;
        max-width: 800px;
        width: 100%;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
    }

    .main-logo {
        height: 120px;
        margin-bottom: 25px;
        filter: drop-shadow(0 0 10px rgba(0,0,0,0.3));
    }

    .brand-section h1 {
        font-size: 3.5rem;
        font-weight: 800;
        background: linear-gradient(to right, #fff, rgba(255,255,255,0.7));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 5px;
    }

    .brand-section p {
        font-size: 1.25rem;
        font-weight: 400;
        color: var(--p-accent);
        letter-spacing: 4px;
        text-transform: uppercase;
        margin-bottom: 40px;
    }

    .promo-box {
        background: rgba(0,0,0,0.2);
        padding: 20px;
        border-radius: 20px;
        font-style: italic;
        color: rgba(255,255,255,0.6);
        margin-bottom: 50px;
    }

    .start-instruction {
        display: flex;
        justify-content: center;
    }

    .btn-premium-start {
        background: var(--p-primary);
        color: white;
        border: none;
        padding: 20px 60px;
        font-size: 1.5rem;
        font-weight: 600;
        border-radius: 100px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        align-items: center;
        box-shadow: 0 10px 20px -5px var(--p-primary);
    }
    .btn-premium-start:hover {
        transform: scale(1.05) translateY(-5px);
        box-shadow: 0 15px 30px -10px var(--p-primary);
    }

    /* Modern Survey Card */
    .survey-card-modern {
        background: var(--p-card);
        backdrop-filter: blur(30px);
        border: 1px solid var(--p-border);
        padding: 50px;
        border-radius: 50px;
        width: 100%;
        max-width: 1000px;
        text-align: center;
        box-shadow: 0 50px 100px -20px rgba(0,0,0,0.6);
    }

    .card-header h2 { font-size: 2.8rem; font-weight: 800; margin-bottom: 10px; }
    .card-header p { font-size: 1.1rem; opacity: 0.6; margin-bottom: 50px; }

    /* Staff Badge */
    .staff-profile-badge {
        display: inline-flex;
        align-items: center;
        text-align: left;
        background: rgba(255,255,255,0.03);
        padding: 15px 30px;
        border-radius: 30px;
        gap: 20px;
        margin-bottom: 30px;
        border: 1px solid var(--p-border);
    }

    .staff-avatar {
        width: 60px;
        height: 60px;
        background: var(--p-primary);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .staff-role-tag {
        background: var(--p-primary);
        font-size: 0.7rem;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 50px;
        display: inline-block;
        margin-bottom: 5px;
    }

    .staff-name-text { font-size: 1.2rem; font-weight: 700; color: white; }
    .staff-loket-text { font-size: 0.8rem; opacity: 0.5; font-weight: 600; }

    /* Modern Emoji Grid */
    .modern-emoji-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
    }

    .m-emoji-btn {
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--p-border);
        border-radius: 40px;
        padding: 40px 20px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        color: white;
    }

    .m-emoji-box {
        font-size: 5rem;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }

    .m-label { font-size: 1.2rem; font-weight: 700; opacity: 0.8; }

    .m-emoji-btn:hover {
        background: var(--p-primary-light);
        border-color: var(--p-primary);
        transform: translateY(-15px);
    }
    .m-emoji-btn:hover .m-emoji-box { transform: scale(1.2) rotate(5deg); }
    .m-emoji-btn:hover .m-label { opacity: 1; color: var(--p-accent); }

    /* Success Glass */
    .success-glass {
        background: var(--p-glass);
        backdrop-filter: blur(40px);
        padding: 60px;
        border-radius: 50px;
        text-align: center;
        max-width: 600px;
        width: 100%;
        border: 1px solid var(--p-border);
    }
    .success-icon-wrapper { margin-bottom: 30px; }

    /* Success Checkmark Animation */
    .success-checkmark { width: 120px; margin: 0 auto; }
    .checkmark-svg { display: block; }
    .checkmark-circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #10b981;
        fill: none;
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
    .checkmark-check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        stroke: #10b981;
        stroke-width: 3;
        animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    }

    @keyframes stroke { 100% { stroke-dashoffset: 0; } }

    .reset-progress {
        margin-top: 40px;
        background: rgba(255,255,255,0.05);
        height: 40px;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255,255,255,0.5);
        font-size: 0.9rem;
    }

    .progress-bar {
        position: absolute;
        inset: 0;
        background: var(--p-primary);
        opacity: 0.3;
        width: 100%;
        transition: width 1s linear;
    }

    .reset-progress span { position: relative; z-index: 2; font-weight: 600; }
</style>

<script>
    let pollInterval = null;
    let currentStaff = null;
    let staffId = null;
    let config = { role: null, counter: null };

    window.onload = () => {
        const urlParams = new URLSearchParams(window.location.search);
        staffId = urlParams.get('staff') || localStorage.getItem('survey_staff_id');
        config.role = urlParams.get('role') || localStorage.getItem('survey_role');
        config.counter = urlParams.get('counter') || localStorage.getItem('survey_counter');
        
        if (staffId || (config.role && config.counter)) {
            if (staffId) localStorage.setItem('survey_staff_id', staffId);
            // Fetch awal untuk data staff (tanpa nunggu polling)
            checkSurveyStatus(); 
            startPolling();
        } else {
            initSetup();
        }
    };

    function initSetup() {
        // Simple prompt for now, could be a custom UI
        const role = prompt("Role (TELLER/CS):", "TELLER");
        const counter = prompt("Nomor Loket:", "1");
        if(role && counter) {
            config.role = role.toLowerCase();
            config.counter = counter;
            localStorage.setItem('survey_role', config.role);
            localStorage.setItem('survey_counter', config.counter);
            applyTheme(config.role);
            startPolling();
        }
    }

    function applyTheme(role) {
        const app = document.getElementById('survey-app');
        app.classList.remove('theme-teller', 'theme-cs');
        if (role === 'teller') app.classList.add('theme-teller');
        if (role === 'cs') app.classList.add('theme-cs');
    }

    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(checkSurveyStatus, 3000);
    }

    function checkSurveyStatus() {
        let query = staffId ? `staff_id=${staffId}` : `role=${config.role}&counter=${config.counter}`;

        fetch(`{{ url('/api/survey/status') }}?${query}`)
            .then(res => res.json())
            .then(data => {
                if (data.staff) {
                    currentStaff = data.staff;
                    populateStaffInfo(data.staff);
                    applyTheme(data.staff.role.toLowerCase());
                }

                // Otomatis pindah layar jika di-trigger dari dashboard (is_active: true)
                // DAN hanya jika kita masih di standby screen
                if (data.is_active && document.getElementById('standby-screen').classList.contains('active')) {
                    showSurvey();
                }
            });
    }

    function populateStaffInfo(staff) {
        document.getElementById('staff-name').innerText = staff.name;
        document.getElementById('staff-counter').innerText = staff.counter_no || '-';
        document.getElementById('staff-role').innerText = staff.role || 'STAFF';
    }

    function showSurvey() {
        if (!currentStaff) {
            console.error("Staff data not loaded yet.");
            // Re-attempt fetch
            checkSurveyStatus();
            return;
        }
        switchScreen('survey-screen');
    }

    function switchScreen(screenId) {
        document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
        document.getElementById(screenId).classList.add('active');
    }

    function submitRating(rating, btn) {
        if (!currentStaff) return;
        
        // Disable other buttons
        document.querySelectorAll('.m-emoji-btn').forEach(b => b.style.pointerEvents = 'none');
        
        btn.classList.add('animate__animated', 'animate__pulse');

        fetch('{{ route("survey.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                rating: rating,
                user_id: currentStaff.id,
                staff_id: currentStaff.id
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') showSuccess();
        });
    }

    function showSuccess() {
        switchScreen('success-screen');
        
        let timeLeft = 3;
        const timerEl = document.getElementById('timer');
        const progressEl = document.getElementById('success-progress');
        
        progressEl.style.width = '100%';

        const interval = setInterval(() => {
            timeLeft--;
            timerEl.innerText = timeLeft;
            progressEl.style.width = (timeLeft / 3 * 100) + '%';

            if(timeLeft <= 0) {
                clearInterval(interval);
                // Reset state
                document.querySelectorAll('.m-emoji-btn').forEach(b => {
                    b.style.pointerEvents = 'auto';
                    b.classList.remove('animate__animated', 'animate__pulse');
                });
                switchScreen('standby-screen');
            }
        }, 1000);
    }
</script>
@endsection
