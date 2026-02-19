@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div id="survey-app" class="survey-container theme-default">
    <!-- Background Mesh Elements -->
    <div class="mesh-gradient"></div>

    <!-- Standby Mode -->
    <div id="standby-screen" class="screen active">
        <div class="premium-card animate__animated animate__fadeIn">
            <div class="brand-section">
                <img src="{{ asset('img/logo_mci.png') }}" class="brand-logo animate__animated animate__fadeInDown" alt="Logo">
                <h1 class="brand-title animate__animated animate__fadeInUp">Selamat Datang</h1>
                <p class="brand-subtitle animate__animated animate__fadeInUp animate__delay-1s">PT BPRS HIK MCI</p>
            </div>
            
            <div id="start-btn-container" class="animate__animated animate__pulse animate__infinite" style="display: none;">
                <button onclick="showSurvey()" class="btn-start-luxury">
                    <span>Mulai Survey</span>
                    <i class="fa-solid fa-chevron-right ml-4"></i>
                </button>
            </div>

            <div id="standby-msg" class="animate__animated animate__fadeIn">
                <div class="auto-return-pill">
                    <div class="luxury-progress-ring"></div>
                    <span>Menunggu Antrian Berikutnya...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Survey Mode -->
    <div id="survey-screen" class="screen">
        <div class="premium-card animate__animated animate__zoomIn">
            <div class="survey-header">
                <div class="staff-badge-luxury" id="staff-info">
                    <div class="staff-avatar-mini">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div class="staff-info-text">
                        <div class="staff-info-role" id="staff-role">STAFF</div>
                        <div class="staff-info-name" id="staff-name">Mitra Kami</div>
                    </div>
                </div>
                <h2 class="survey-title">Bagaimana Pelayanan Kami?</h2>
                <p class="survey-desc">Sentuh salah satu icon untuk memberikan penilaian Anda</p>
            </div>

            <div class="luxury-rating-grid">
                <div class="rating-item">
                    <button class="btn-rating-luxury" onclick="submitRating(5, this)">🤩</button>
                    <span class="rating-label-luxury">Sangat Puas</span>
                </div>
                <div class="rating-item">
                    <button class="btn-rating-luxury" onclick="submitRating(4, this)">🙂</button>
                    <span class="rating-label-luxury">Puas</span>
                </div>
                <div class="rating-item">
                    <button class="btn-rating-luxury" onclick="submitRating(3, this)">😐</button>
                    <span class="rating-label-luxury">Cukup</span>
                </div>
                <div class="rating-item">
                    <button class="btn-rating-luxury" onclick="submitRating(2, this)">☹️</button>
                    <span class="rating-label-luxury">Kurang</span>
                </div>
                <div class="rating-item">
                    <button class="btn-rating-luxury" onclick="submitRating(1, this)">😞</button>
                    <span class="rating-label-luxury">Sgt Kurang</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Mode -->
    <div id="success-screen" class="screen">
        <div class="premium-card animate__animated animate__bounceIn">
            <div class="success-icon-luxury">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="success-msg-container">
                <h2>Terima Kasih!</h2>
                <p>Penilaian Anda sangat berharga untuk terus memberikan layanan terbaik bagi Anda.</p>
                <div class="auto-return-pill">
                    <span>Kembali otomatis dalam <b id="timer">3</b>s</span>
                    <div class="luxury-progress-ring"></div>
                </div>
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
        --p-card: rgba(15, 23, 42, 0.6);
        --p-glass: rgba(255, 255, 255, 0.03);
        --p-border: rgba(255, 255, 255, 0.08);
        --p-shadow: 0 50px 100px -20px rgba(0,0,0,0.5);
    }

    /* Role Theming - Premium Colors */
    .theme-teller {
        --p-primary: #10b981;
        --p-primary-rgb: 16, 185, 129;
        --p-accent: #34d399;
    }

    .theme-cs {
        --p-primary: #f43f5e;
        --p-primary-rgb: 244, 63, 94;
        --p-accent: #fb7185;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        overflow: hidden;
        background: var(--p-bg);
        font-family: 'Outfit', sans-serif;
        color: white;
    }

    /* Animated Mesh Background */
    .survey-container {
        position: fixed;
        inset: 0;
        background: radial-gradient(at 0% 0%, rgba(var(--p-primary-rgb, 14, 165, 233), 0.15) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(var(--p-primary-rgb, 14, 165, 233), 0.1) 0px, transparent 50%),
                    #0f172a;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    /* Modern Mesh Gradients */
    .mesh-gradient {
        position: absolute;
        inset: -50%;
        background: radial-gradient(circle at 50% 50%, rgba(var(--p-primary-rgb, 14, 165, 233), 0.08) 0%, transparent 40%);
        filter: blur(80px);
        z-index: 1;
        animation: drift 20s infinite alternate linear;
    }

    @keyframes drift {
        from { transform: translate(-10%, -10%) rotate(0deg); }
        to { transform: translate(10%, 10%) rotate(360deg); }
    }

    .screen {
        position: relative;
        z-index: 10;
        width: 100%;
        height: 100%;
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .screen.active { display: flex; }

    /* Glassmorphism Card Premium */
    .premium-card {
        background: var(--p-card);
        backdrop-filter: blur(60px);
        -webkit-backdrop-filter: blur(60px);
        border: 1px solid var(--p-border);
        border-radius: 60px;
        box-shadow: var(--p-shadow);
        padding: 60px;
        text-align: center;
        width: 100%;
        max-width: 1100px;
        position: relative;
        overflow: hidden;
    }

    /* Dynamic Border Glow */
    .premium-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(255,255,255,0.05) 0%, transparent 50%);
        pointer-events: none;
    }

    /* Standby Interface */
    .brand-logo {
        height: 140px;
        margin-bottom: 30px;
        filter: drop-shadow(0 0 20px rgba(var(--p-primary-rgb, 14, 165, 233), 0.3));
    }

    .brand-title {
        font-size: 4.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 10px;
        background: linear-gradient(to bottom, #fff 40%, rgba(255,255,255,0.5));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .brand-subtitle {
        font-size: 1.5rem;
        font-weight: 400;
        text-transform: uppercase;
        letter-spacing: 12px;
        color: var(--p-primary);
        margin-bottom: 60px;
    }

    .btn-start-luxury {
        background: linear-gradient(135deg, var(--p-primary), var(--p-accent));
        padding: 24px 80px;
        font-size: 1.8rem;
        font-weight: 700;
        border-radius: 100px;
        border: none;
        color: white;
        cursor: pointer;
        box-shadow: 0 20px 40px -10px rgba(var(--p-primary-rgb, 14, 165, 233), 0.5);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .btn-start-luxury:hover {
        transform: scale(1.05) translateY(-5px);
        box-shadow: 0 30px 60px -15px rgba(var(--p-primary-rgb, 14, 165, 233), 0.6);
    }

    /* Survey Screen Header */
    .survey-header { margin-bottom: 60px; }
    
    .staff-badge-luxury {
        display: inline-flex;
        align-items: center;
        gap: 20px;
        background: rgba(255,255,255,0.03);
        padding: 12px 24px;
        border-radius: 24px;
        border: 1px solid var(--p-border);
        margin-bottom: 40px;
    }

    .staff-avatar-mini {
        width: 50px;
        height: 50px;
        background: var(--p-primary);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: 0 10px 20px -5px rgba(var(--p-primary-rgb, 14, 165, 233), 0.4);
    }

    .staff-info-text { text-align: left; }
    .staff-info-role { font-size: 0.6rem; font-weight: 800; color: var(--p-primary); text-transform: uppercase; letter-spacing: 2px; }
    .staff-info-name { font-size: 1.1rem; font-weight: 700; color: white; }

    .survey-title { font-size: 3.5rem; font-weight: 800; margin-bottom: 10px; letter-spacing: -2px; }
    .survey-desc { font-size: 1.2rem; opacity: 0.5; font-weight: 400; margin-bottom: 50px; }

    /* Emoji Buttons Luxury */
    .luxury-rating-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 30px;
        max-width: 900px;
        margin: 0 auto;
    }

    .rating-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 25px;
    }

    .btn-rating-luxury {
        width: 140px;
        height: 140px;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--p-border);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 5.5rem;
        cursor: pointer;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
    }

    .btn-rating-luxury::after {
        content: '';
        position: absolute;
        inset: -10px;
        border-radius: 50%;
        border: 2px solid var(--p-primary);
        opacity: 0;
        transform: scale(0.8);
        transition: all 0.4s ease;
    }

    .btn-rating-luxury:hover {
        background: rgba(255,255,255,0.05);
        transform: translateY(-20px) scale(1.1);
        border-color: var(--p-primary);
        box-shadow: 0 40px 80px -20px rgba(var(--p-primary-rgb, 14, 165, 233), 0.3);
    }

    .btn-rating-luxury:hover::after {
        opacity: 0.2;
        transform: scale(1.1);
    }

    .rating-label-luxury {
        font-size: 1rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.4;
        transition: all 0.3s ease;
    }

    .rating-item:hover .rating-label-luxury {
        opacity: 1;
        color: var(--p-primary);
        transform: translateY(-5px);
    }

    /* Success Luxury Screen */
    .success-icon-luxury {
        width: 160px;
        height: 160px;
        background: rgba(16, 185, 129, 0.1);
        border: 2px solid rgba(16, 185, 129, 0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 5rem;
        color: #10b981;
        margin: 0 auto 40px;
        box-shadow: 0 0 50px rgba(16, 185, 129, 0.2);
    }

    .success-msg-container h2 { font-size: 4rem; font-weight: 800; margin-bottom: 10px; }
    .success-msg-container p { font-size: 1.4rem; opacity: 0.5; max-width: 500px; margin: 0 auto 60px; }

    .auto-return-pill {
        display: inline-flex;
        align-items: center;
        background: rgba(0,0,0,0.3);
        padding: 12px 30px;
        border-radius: 100px;
        font-size: 0.9rem;
        font-weight: 600;
        color: rgba(255,255,255,0.6);
        gap: 15px;
        border: 1px solid var(--p-border);
    }

    .luxury-progress-ring {
        width: 24px;
        height: 24px;
        border: 2px solid rgba(255,255,255,0.1);
        border-top-color: var(--p-primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    /* Responsive Scaling */
    @media (max-width: 1024px) {
        .brand-title { font-size: 3.5rem; }
        .survey-title { font-size: 2.8rem; }
        .premium-card { padding: 40px; }
        .luxury-rating-grid { gap: 15px; }
        .btn-rating-luxury { width: 110px; height: 110px; font-size: 4rem; }
    }

    @media (max-height: 700px) {
        .premium-card { border-radius: 40px; padding: 30px; }
        .brand-logo { height: 80px; }
        .survey-header { margin-bottom: 30px; }
        .luxury-rating-grid { gap: 10px; }
        .btn-rating-luxury { width: 90px; height: 90px; font-size: 3.5rem; }
    }
</style>

<script>
    let pollInterval = null;
    let currentStaff = null;
    let currentReference = null;
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
                    currentReference = data.reference_no; // Ambil token antrian aktif
                    populateStaffInfo(data.staff);
                    applyTheme(data.staff.role.toLowerCase());
                }

                // Update Visibility Tombol Mulai Survey
                const btnContainer = document.getElementById('start-btn-container');
                const standbyMsg = document.getElementById('standby-msg');
                
                if (data.reference_no) {
                    btnContainer.style.display = 'flex';
                    standbyMsg.style.display = 'none';
                } else {
                    btnContainer.style.display = 'none';
                    standbyMsg.style.display = 'block';
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
        document.querySelectorAll('.btn-rating-luxury').forEach(b => b.style.pointerEvents = 'none');
        
        btn.classList.add('animate__animated', 'animate__pulse');

        fetch('{{ route("survey.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                rating: rating,
                user_id: currentStaff.id,
                reference_no: currentReference // Kirim token antrian
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') showSuccess();
            else {
                // Jika sudah diisi atau error
                console.error(data.message);
                showSuccess(); // Tetap ke success agar user tidak bingung, atau kembali standby
            }
        });
    }

    function showSuccess() {
        switchScreen('success-screen');
        
        let timeLeft = 3;
        const timerEl = document.getElementById('timer');
        
        const interval = setInterval(() => {
            timeLeft--;
            if (timerEl) timerEl.innerText = timeLeft;

            if(timeLeft <= 0) {
                clearInterval(interval);
                // Reset state
                document.querySelectorAll('.btn-rating-luxury').forEach(b => {
                    b.style.pointerEvents = 'auto';
                    b.classList.remove('animate__animated', 'animate__pulse');
                });
                switchScreen('standby-screen');
            }
        }, 1000);
    }
</script>
@endsection
