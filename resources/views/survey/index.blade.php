@extends('layouts.app')

@section('content')
<div id="survey-app" class="survey-container">
    <!-- Standby Mode (Promo Slides) -->
    <div id="standby-screen" class="screen active">
        <div class="slideshow">
            <div class="slide active" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                <div class="slide-content">
                    <img src="{{ asset('images/logo.png') }}" class="standby-logo" alt="Logo">
                    <h1>Selamat Datang di BPR SHI MCI</h1>
                    <p>Melayani dengan Sepenuh Hati</p>
                </div>
            </div>
            <!-- Additional slides can be added here -->
        </div>
        <div class="start-prompt">
            <button onclick="showSurvey()" class="btn-start">SENTUH UNTUK MEMULAI SURVEY</button>
        </div>
    </div>

    <!-- Survey Mode -->
    <div id="survey-screen" class="screen">
        <div class="survey-card">
            <header>
                <h2>Bagaimana Pelayanan Kami Hari Ini?</h2>
                <p>Penilaian Anda sangat berharga bagi kami</p>
            </header>

            <div class="emoji-grid">
                <button class="emoji-btn" onclick="submitRating(4, this)" data-rating="very-good">
                    <span class="emoji">🤩</span>
                    <span class="label">Sangat Puas</span>
                </button>
                <button class="emoji-btn" onclick="submitRating(3, this)" data-rating="good">
                    <span class="emoji">🙂</span>
                    <span class="label">Puas</span>
                </button>
                <button class="emoji-btn" onclick="submitRating(2, this)" data-rating="neutral">
                    <span class="emoji">😐</span>
                    <span class="label">Cukup</span>
                </button>
                <button class="emoji-btn" onclick="submitRating(1, this)" data-rating="bad">
                    <span class="emoji">😞</span>
                    <span class="label">Kurang</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Success Mode -->
    <div id="success-screen" class="screen">
        <div class="success-content">
            <div class="checkmark-wrapper">
                <div class="checkmark"></div>
            </div>
            <h2>Terima Kasih!</h2>
            <p>Penilaian Anda telah kami terima.</p>
            <div class="auto-reset-timer">Kembali ke menu dalam <span id="timer">3</span> detik...</div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #0ea5e9;
        --secondary: #64748b;
        --success: #10b981;
        --bg: #f8fafc;
        --text: #1e293b;
    }

    .survey-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: var(--bg);
        z-index: 9999;
        overflow: hidden;
        font-family: 'Inter', sans-serif;
    }

    .screen {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: all 0.5s ease;
    }

    .screen.active {
        display: flex;
    }

    /* Standby Screen */
    .slideshow {
        width: 100%;
        height: 100%;
    }

    .slide {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
    }

    .standby-logo {
        height: 120px;
        margin-bottom: 30px;
        filter: drop-shadow(0 0 20px rgba(255,255,255,0.2));
    }

    .slide-content h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .slide-content p {
        font-size: 1.5rem;
        opacity: 0.8;
    }

    .start-prompt {
        position: absolute;
        bottom: 10%;
    }

    .btn-start {
        padding: 20px 50px;
        font-size: 1.2rem;
        font-weight: 700;
        background: white;
        color: #0f172a;
        border: none;
        border-radius: 50px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        cursor: pointer;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255,255,255,0.4); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(255,255,255,0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255,255,255,0); }
    }

    /* Survey Screen */
    .survey-card {
        background: white;
        padding: 60px;
        border-radius: 40px;
        box-shadow: 0 40px 100px -20px rgba(0,0,0,0.1);
        text-align: center;
        max-width: 900px;
        width: 90%;
    }

    .survey-card h2 {
        font-size: 2.5rem;
        color: #0f172a;
        margin-bottom: 15px;
    }

    .survey-card p {
        font-size: 1.2rem;
        color: #64748b;
        margin-bottom: 50px;
    }

    .emoji-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
    }

    .emoji-btn {
        background: #f1f5f9;
        border: none;
        padding: 40px 20px;
        border-radius: 30px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    .emoji-btn .emoji {
        font-size: 4.5rem;
    }

    .emoji-btn .label {
        font-weight: 700;
        font-size: 1.1rem;
        color: #334155;
    }

    .emoji-btn:hover {
        transform: translateY(-15px);
        background: var(--primary);
    }

    .emoji-btn:hover .label {
        color: white;
    }

    /* Success Screen */
    .success-content {
        text-align: center;
    }

    .success-content h2 {
        font-size: 3rem;
        margin-top: 30px;
    }

    .auto-reset-timer {
        margin-top: 30px;
        color: #64748b;
        font-weight: 500;
    }

    /* Animation */
    .checkmark {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: block;
        stroke-width: 2;
        stroke: #fff;
        stroke-miterlimit: 10;
        box-shadow: inset 0px 0px 0px #7ac142;
        animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        background: #7ac142;
        margin: 0 auto;
    }
</style>

<script>
    let pollInterval = null;
    let staffId = null;

    // Ambil staffId dari URL atau localStorage
    window.onload = () => {
        const urlParams = new URLSearchParams(window.location.search);
        staffId = urlParams.get('staff') || localStorage.getItem('survey_staff_id');
        
        if (staffId) {
            localStorage.setItem('survey_staff_id', staffId);
            startPolling();
        } else {
            // Jika tidak ada staff_id, tampilkan input sederhana untuk setup awal tablet
            const id = prompt("Masukkan ID Staff/Nomor Counter untuk tablet ini:");
            if (id) {
                staffId = id;
                localStorage.setItem('survey_staff_id', id);
                startPolling();
            }
        }
    };

    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(checkSurveyStatus, 3000); // Cek tiap 3 detik
    }

    function checkSurveyStatus() {
        // Hanya cek jika sedang di standby screen
        if (!document.getElementById('standby-screen').classList.contains('active')) return;

        fetch(`{{ url('/api/survey/status') }}?staff_id=${staffId}`)
            .then(res => res.json())
            .then(data => {
                if (data.is_active) {
                    showSurvey();
                }
            });
    }

    function showSurvey() {
        switchScreen('survey-screen');
    }

    function switchScreen(screenId) {
        document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
        document.getElementById(screenId).classList.add('active');
    }

    function submitRating(rating, btn) {
        // Feedback visual
        btn.style.transform = 'scale(1.2)';
        btn.style.background = '#10b981';

        // Submit via AJAX
        fetch('{{ route("survey.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                rating: rating,
                user_id: staffId,
                staff_id: staffId // Untuk reset cache di backend
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                showSuccess();
            }
        });
    }

    function showSuccess() {
        switchScreen('success-screen');
        
        let timeLeft = 3;
        const timerEl = document.getElementById('timer');
        
        const interval = setInterval(() => {
            timeLeft--;
            timerEl.innerText = timeLeft;
            if(timeLeft <= 0) {
                clearInterval(interval);
                switchScreen('standby-screen');
            }
        }, 1000);
    }
</script>
@endsection
