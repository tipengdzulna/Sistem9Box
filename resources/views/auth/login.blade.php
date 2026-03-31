<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — MT Kemenkeu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,300;12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=Geist+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --navy:    #071A35;
            --blue:    #0F3460;
            --cobalt:  #1648A0;
            --gold:    #C8973D;
            --gold-lt: #E8BE72;
            --gold-dk: #7A5A22;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%; width: 100%;
            font-family: 'Bricolage Grotesque', sans-serif;
            overflow: hidden;
            background: var(--navy);
        }

        /* ══════════════════════════════
           FULL-SCREEN PHOTO
        ══════════════════════════════ */
        .bg {
            position: fixed; inset: 0; z-index: 0;
            background: url('{{ asset("images/gedung_djuanda1.jpeg") }}') center 15% / cover no-repeat;
            animation: bgDrift 30s ease-in-out infinite alternate;
            filter: brightness(0.45) saturate(0.65);
        }
        @keyframes bgDrift {
            from { transform: scale(1.0) translateX(0); }
            to   { transform: scale(1.06) translateX(-12px); }
        }

        /* Multi-layer gradient overlay */
        .bg-overlay {
            position: fixed; inset: 0; z-index: 1;
            background:
                radial-gradient(ellipse 80% 80% at 50% 100%, rgba(7,26,53,0.95) 0%, transparent 70%),
                radial-gradient(ellipse 60% 50% at 50% 0%,   rgba(7,26,53,0.7) 0%, transparent 60%),
                linear-gradient(180deg, rgba(7,26,53,0.3) 0%, rgba(7,26,53,0.0) 40%, rgba(7,26,53,0.5) 70%, rgba(7,26,53,0.92) 100%);
        }

        /* ══════════════════════════════
           TOP BAR
        ══════════════════════════════ */
        .topbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 10;
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.1rem 2.5rem;
            background: rgba(7,26,53,0.4);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            opacity: 0; animation: fadeDown 0.5s 0.1s ease-out forwards;
        }
        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .topbar-brand {
            display: flex; align-items: center; gap: 0.75rem;
        }
        .topbar-brand img {
            width: 32px; height: 32px; object-fit: contain;
            filter: drop-shadow(0 1px 3px rgba(0,0,0,0.5));
        }
        .topbar-brand-name {
            font-size: 0.82rem; font-weight: 700; color: white;
            letter-spacing: 0.03em;
        }
        .topbar-brand-sub {
            font-size: 0.62rem; color: rgba(255,255,255,0.38);
            letter-spacing: 0.04em; margin-top: 1px;
        }
        .topbar-right {
            display: flex; align-items: center; gap: 0.5rem;
        }
        .status-pill {
            display: flex; align-items: center; gap: 0.4rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 100px;
            padding: 0.28rem 0.75rem;
        }
        .status-dot {
            width: 6px; height: 6px; border-radius: 50%;
            animation: blink 2s ease-in-out infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.35; }
        }
        .status-pill span {
            font-family: 'Geist Mono', monospace;
            font-size: 0.62rem; color: rgba(255,255,255,0.4);
            letter-spacing: 0.06em;
        }

        /* ══════════════════════════════
           CENTER STAGE
        ══════════════════════════════ */
        .stage {
            position: relative; z-index: 5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 5rem 1.5rem 6rem;
            gap: 2rem;
        }

        /* ── System title above card ── */
        .stage-header {
            text-align: center;
            opacity: 0; transform: translateY(20px);
            animation: fadeUp 0.65s 0.25s ease-out forwards;
        }
        .sys-label {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: rgba(200,151,61,0.12);
            border: 1px solid rgba(200,151,61,0.3);
            border-radius: 100px;
            padding: 0.3rem 0.85rem;
            margin-bottom: 1rem;
        }
        .sys-label-dot {
            width: 6px; height: 6px; border-radius: 50%; background: var(--gold);
            animation: pulseDot 2s ease-in-out infinite;
        }
        @keyframes pulseDot {
            0%, 100% { box-shadow: 0 0 0 0 rgba(200,151,61,0.5); }
            50%       { box-shadow: 0 0 0 5px rgba(200,151,61,0); }
        }
        .sys-label span {
            font-family: 'Geist Mono', monospace;
            font-size: 0.65rem; color: var(--gold-lt);
            letter-spacing: 0.1em; text-transform: uppercase; font-weight: 600;
        }
        .stage-title {
            font-size: clamp(1.9rem, 4vw, 2.8rem);
            font-weight: 800; color: white; line-height: 1.1;
            letter-spacing: -0.02em;
        }
        .stage-title em {
            font-style: normal;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-lt) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stage-sub {
            font-size: 0.82rem; color: rgba(255,255,255,0.38);
            margin-top: 0.5rem; letter-spacing: 0.02em;
        }

        /* ── Login Card ── */
        .card {
            width: 100%; max-width: 420px;
            background: rgba(7,26,53,0.55);
            backdrop-filter: blur(28px) saturate(1.4);
            -webkit-backdrop-filter: blur(28px) saturate(1.4);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 24px;
            padding: 2.25rem 2rem 1.75rem;
            position: relative; overflow: hidden;
            opacity: 0; transform: translateY(28px) scale(0.97);
            animation: cardIn 0.7s 0.4s cubic-bezier(0.22,1,0.36,1) forwards;
            box-shadow:
                0 0 0 1px rgba(200,151,61,0.07),
                0 24px 60px rgba(0,0,0,0.5),
                inset 0 1px 0 rgba(255,255,255,0.07);
        }
        @keyframes cardIn {
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        /* Gold shimmer top border */
        .card::before {
            content: '';
            position: absolute; top: 0; left: 15%; right: 15%; height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }
        /* Glow inside card top */
        .card::after {
            content: '';
            position: absolute; top: -40px; left: 50%; transform: translateX(-50%);
            width: 160px; height: 120px;
            background: radial-gradient(circle, rgba(200,151,61,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Feature row inside card */
        .card-features {
            display: flex; gap: 0.5rem; margin-bottom: 1.5rem;
        }
        .feat-chip {
            flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.35rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 12px;
            padding: 0.65rem 0.5rem;
            transition: background 0.2s;
        }
        .feat-chip:hover { background: rgba(255,255,255,0.07); }
        .feat-chip i {
            font-size: 1.1rem; color: var(--gold);
        }
        .feat-chip span {
            font-size: 0.58rem; color: rgba(255,255,255,0.38);
            text-align: center; line-height: 1.3; letter-spacing: 0.02em;
        }

        /* Divider */
        .card-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.07), transparent);
            margin-bottom: 1.5rem;
        }

        /* Greet */
        .card-greet { margin-bottom: 1.25rem; }
        .card-greet h2 {
            font-size: 1.15rem; font-weight: 700; color: white;
        }
        .card-greet p {
            font-size: 0.73rem; color: rgba(255,255,255,0.35); margin-top: 0.18rem;
        }

        /* Error */
        #err-box {
            display: flex; align-items: center; gap: 0.6rem;
            background: rgba(220,38,38,0.12);
            border: 1px solid rgba(220,38,38,0.25);
            border-radius: 10px;
            padding: 0.65rem 0.85rem;
            margin-bottom: 1rem;
            font-size: 0.76rem; color: #FCA5A5;
            animation: alertPop 0.3s cubic-bezier(0.34,1.56,0.64,1);
        }
        @keyframes alertPop {
            from { opacity: 0; transform: scale(0.96) translateY(-4px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        #err-box i { font-size: 0.9rem; flex-shrink: 0; }
        .hidden { display: none !important; }

        /* Fields */
        .field { margin-bottom: 0.9rem; }
        .field-label {
            display: block;
            font-size: 0.68rem; font-weight: 600;
            color: rgba(255,255,255,0.4);
            margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.06em;
            transition: color 0.2s;
        }
        .field:focus-within .field-label { color: var(--gold-lt); }

        .iw { position: relative; }
        .iw .ico {
            position: absolute; left: 0.9rem; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.2); pointer-events: none;
            font-size: 0.95rem; transition: color 0.2s;
        }
        .iw:focus-within .ico { color: var(--gold); }
        .iw input {
            width: 100%;
            padding: 0.78rem 1rem 0.78rem 2.45rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 11px;
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 0.84rem; font-weight: 500; color: white;
            outline: none;
            transition: border-color 0.25s, background 0.25s, box-shadow 0.25s;
            caret-color: var(--gold);
        }
        .iw input::placeholder { color: rgba(255,255,255,0.18); font-weight: 400; }
        .iw input:focus {
            background: rgba(255,255,255,0.08);
            border-color: rgba(200,151,61,0.4);
            box-shadow: 0 0 0 3px rgba(200,151,61,0.09);
        }
        .iw input.err { border-color: rgba(220,38,38,0.4); }
        .input-shake { animation: shake 0.4s ease-out; }
        @keyframes shake {
            15%, 85% { transform: translateX(-3px); }
            30%, 70% { transform: translateX(5px); }
            45%, 55% { transform: translateX(-5px); }
        }
        .toggle-pw {
            position: absolute; right: 0.75rem; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: rgba(255,255,255,0.22); padding: 4px; border-radius: 6px;
            display: flex; align-items: center; transition: color 0.2s;
        }
        .toggle-pw:hover { color: var(--gold-lt); }

        /* Remember row */
        .opt-row {
            display: flex; align-items: center;
            margin: 0.25rem 0 1.25rem;
        }
        .remember {
            display: flex; align-items: center; gap: 0.55rem;
            cursor: pointer; user-select: none;
        }
        .remember input[type=checkbox] {
            width: 15px; height: 15px;
            accent-color: var(--gold);
            cursor: pointer;
        }
        .remember span {
            font-size: 0.74rem; color: rgba(255,255,255,0.35); font-weight: 500;
        }

        /* Submit */
        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            border: none; border-radius: 11px;
            background: linear-gradient(135deg, var(--cobalt) 0%, var(--blue) 100%);
            color: white;
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 0.88rem; font-weight: 700;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            position: relative; overflow: hidden;
            transition: transform 0.22s, box-shadow 0.22s;
            box-shadow: 0 4px 20px rgba(22,72,160,0.4);
            letter-spacing: 0.01em;
        }
        .btn-submit::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, var(--gold-dk) 0%, var(--gold) 100%);
            opacity: 0; transition: opacity 0.3s;
        }
        .btn-submit:hover:not(:disabled)::before { opacity: 1; }
        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 26px rgba(200,151,61,0.35);
        }
        .btn-submit:active:not(:disabled) { transform: translateY(0); }
        .btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-submit > * { position: relative; z-index: 1; }
        .btn-submit i { font-size: 1.1rem; margin-left: -2px; }
        .spinner { animation: spin 0.85s linear infinite; font-size: 0.9rem; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Card footer */
        .card-foot {
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.05);
            display: flex; align-items: center; justify-content: center;
            gap: 0.5rem;
        }
        .card-foot-text {
            font-family: 'Geist Mono', monospace;
            font-size: 0.62rem; color: rgba(255,255,255,0.2);
            letter-spacing: 0.04em;
        }

        /* ── Feature pills below card ── */
        .stage-pills {
            display: flex; gap: 0.65rem; flex-wrap: wrap; justify-content: center;
            opacity: 0; transform: translateY(12px);
            animation: fadeUp 0.6s 0.75s ease-out forwards;
        }
        .stage-pill {
            display: flex; align-items: center; gap: 0.45rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 100px;
            padding: 0.3rem 0.85rem;
            backdrop-filter: blur(8px);
            transition: background 0.2s, border-color 0.2s;
        }
        .stage-pill:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(200,151,61,0.2);
        }
        .stage-pill i { font-size: 0.75rem; color: var(--gold); }
        .stage-pill span {
            font-size: 0.68rem; color: rgba(255,255,255,0.38);
            font-weight: 500; letter-spacing: 0.02em;
        }

        /* ══════════════════════════════
           BOTTOM BAR
        ══════════════════════════════ */
        .bottombar {
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 10;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.6rem 2.5rem;
            background: rgba(7,26,53,0.5);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(255,255,255,0.05);
            opacity: 0; animation: fadeUp 0.5s 1s ease-out forwards;
        }
        .bottombar-left {
            font-family: 'Geist Mono', monospace;
            font-size: 0.62rem; color: rgba(255,255,255,0.22);
            letter-spacing: 0.04em;
        }
        .bottombar-right {
            font-size: 0.65rem; color: rgba(255,255,255,0.2);
        }

        /* ══════════════════════════════
           PHOTO CAPTION (absolute)
        ══════════════════════════════ */
        .photo-caption {
            position: fixed; left: 1.5rem; bottom: 3rem; z-index: 6;
            writing-mode: vertical-rl; text-orientation: mixed;
            transform: rotate(180deg);
            font-family: 'Geist Mono', monospace;
            font-size: 0.6rem; color: rgba(255,255,255,0.18);
            letter-spacing: 0.1em; text-transform: uppercase;
            opacity: 0; animation: fadeUp 0.5s 1.2s ease-out forwards;
        }

        /* ══════════════════════════════
           ANIMATIONS
        ══════════════════════════════ */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ══════════════════════════════
           RESPONSIVE
        ══════════════════════════════ */
        @media (max-width: 640px) {
            .topbar { padding: 0.9rem 1.25rem; }
            .topbar-brand-name { font-size: 0.75rem; }
            .stage { padding: 4.5rem 1rem 5rem; }
            .stage-title { font-size: 1.75rem; }
            .card { padding: 1.75rem 1.5rem 1.5rem; border-radius: 20px; }
            .card-features { gap: 0.4rem; }
            .photo-caption { display: none; }
            .bottombar { padding: 0.6rem 1.25rem; }
        }

        @media (max-width: 380px) {
            .stage-title { font-size: 1.5rem; }
            .card { padding: 1.5rem 1.25rem 1.25rem; }
            .card-features .feat-chip span { font-size: 0.54rem; }
            .stage-pills { gap: 0.4rem; }
            .stage-pill span { font-size: 0.62rem; }
        }

        @media (max-height: 640px) and (orientation: landscape) {
            .stage { flex-direction: row; align-items: center; gap: 1.5rem; padding: 4rem 2rem 3.5rem; }
            .stage-header { flex-shrink: 0; text-align: left; max-width: 260px; }
            .stage-title { font-size: 1.6rem; }
            .stage-sub { font-size: 0.72rem; }
            .sys-label { margin-bottom: 0.75rem; }
            .stage-pills { display: none; }
            .card { max-width: 360px; }
            .card-features { display: none; }
        }
    </style>
</head>
<body>

<!-- Photo Background -->
<div class="bg"></div>
<div class="bg-overlay"></div>

<!-- Top Bar -->
<div class="topbar">
    <div class="topbar-brand">
        <img src="{{ asset('images/logo-kemenkeu.png') }}" alt="Logo Kemenkeu">
        <div>
            <div class="topbar-brand-name">Kementerian Keuangan RI</div>
            <div class="topbar-brand-sub">Biro Sumber Daya Manusia</div>
        </div>
    </div>
    <div class="topbar-right">
        <div class="status-pill">
            <div class="status-dot" style="background:#22c55e"></div>
            <span>SISTEM AKTIF</span>
        </div>
        <div class="status-pill">
            <div class="status-dot" style="background:#3b82f6"></div>
            <span>PAN RB 2025</span>
        </div>
    </div>
</div>

<!-- Center Stage -->
<div class="stage">

    <!-- Header above card -->
    <div class="stage-header">
        <div class="sys-label">
            <div class="sys-label-dot"></div>
            <span>Portal Manajemen Talenta</span>
        </div>
        <h1 class="stage-title">MT&nbsp;<em>Kemenkeu</em></h1>
        <p class="stage-sub">Sistem Pemetaan Talenta — 9-Box Grid · Berbasis PMK dan Permen PAN RB 2025</p>
    </div>

    <!-- Login Card -->
    <div class="card" id="loginCard">

        <!-- Feature chips -->
        <div class="card-features">
            <div class="feat-chip">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <span>9-Box Grid</span>
            </div>
            <div class="feat-chip">
                <i class="bi bi-people-fill"></i>
                <span>Multi-Level SDM</span>
            </div>
            <div class="feat-chip">
                <i class="bi bi-bar-chart-fill"></i>
                <span>Dashboard</span>
            </div>
            <div class="feat-chip">
                <i class="bi bi-shield-check"></i>
                <span>Role-Based</span>
            </div>
        </div>

        <div class="card-divider"></div>

        <div class="card-greet">
            <h2>Masuk ke Akun</h2>
            <p>Gunakan NIP atau username Anda</p>
        </div>

        <!-- Error -->
        @if($errors->any())
        <div id="err-box">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif
        <div id="err-box-js" class="hidden">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span id="err-text-js"></span>
        </div>

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <!-- Username -->
            <div class="field">
                <label class="field-label" for="username">Username / NIP</label>
                <div class="iw">
                    <i class="bi bi-person ico"></i>
                    <input type="text" id="username" name="username"
                           value="{{ old('username') }}"
                           placeholder="Contoh: 19801234567890"
                           autofocus required>
                </div>
            </div>

            <!-- Password -->
            <div class="field">
                <label class="field-label" for="password">Kata Sandi</label>
                <div class="iw">
                    <i class="bi bi-lock ico"></i>
                    <input type="password" id="password" name="password"
                           placeholder="Masukkan kata sandi Anda"
                           style="padding-right:2.8rem" required>
                    <button type="button" class="toggle-pw" onclick="togglePw()" aria-label="Toggle password">
                        <i class="bi bi-eye" id="eye-on"></i>
                        <i class="bi bi-eye-slash hidden" id="eye-off"></i>
                    </button>
                </div>
            </div>

            <!-- Remember -->
            <div class="opt-row">
                <label class="remember">
                    <input type="checkbox" name="remember">
                    <span>Ingat sesi saya</span>
                </label>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-submit" id="btn-submit">
                <span id="btn-text">Masuk Sekarang</span>
                <i class="bi bi-arrow-right" id="btn-arrow"></i>
                <i class="bi bi-arrow-repeat spinner hidden" id="btn-spin"></i>
            </button>

        </form>

        <div class="card-foot">
            <span class="card-foot-text">&copy; {{ date('Y') }} &nbsp;·&nbsp; Biro SDM Kemenkeu RI &nbsp;·&nbsp; v2.0</span>
        </div>
    </div>

    <!-- Pills below card -->
    <div class="stage-pills">
        <div class="stage-pill"><i class="bi bi-building"></i><span>Gedung Djuanda I, Jakarta</span></div>
        <div class="stage-pill"><i class="bi bi-patch-check-fill"></i><span>Akuntabel & Transparan</span></div>
        <div class="stage-pill"><i class="bi bi-lightning-charge-fill"></i><span>Pengembangan ASN</span></div>
    </div>

</div>

<!-- Photo Caption vertical -->
<div class="photo-caption">Gedung Djuanda I — Kementerian Keuangan RI, Jakarta Pusat</div>

<!-- Bottom Bar -->
<div class="bottombar">
    <span class="bottombar-left">MT-KEMENKEU &nbsp;·&nbsp; MANAJEMEN TALENTA &nbsp;·&nbsp; PERMEN PAN RB 2025</span>
    <span class="bottombar-right">Biro Sumber Daya Manusia</span>
</div>

<script>
    /* ── Toggle password ── */
    function togglePw() {
        const inp = document.getElementById('password');
        const on  = document.getElementById('eye-on');
        const off = document.getElementById('eye-off');
        const show = inp.type === 'password';
        inp.type = show ? 'text' : 'password';
        on.classList.toggle('hidden', show);
        off.classList.toggle('hidden', !show);
    }

    /* ── Card 3D tilt ── */
    const card = document.getElementById('loginCard');
    card.addEventListener('mousemove', (e) => {
        const r  = card.getBoundingClientRect();
        const dx = (e.clientX - r.left - r.width  / 2) / (r.width  / 2);
        const dy = (e.clientY - r.top  - r.height / 2) / (r.height / 2);
        card.style.transform = `perspective(1000px) rotateY(${dx * 4}deg) rotateX(${-dy * 2.5}deg)`;
        card.style.transition = 'transform 0.08s linear';
    });
    card.addEventListener('mouseleave', () => {
        card.style.transform = '';
        card.style.transition = 'transform 0.7s cubic-bezier(0.22,1,0.36,1)';
    });

    /* ── Subtle parallax on bg ── */
    document.addEventListener('mousemove', (e) => {
        const x = (e.clientX / window.innerWidth  - 0.5) * 8;
        const y = (e.clientY / window.innerHeight - 0.5) * 5;
        document.querySelector('.bg').style.transform = `scale(1.04) translate(${x * 0.4}px, ${y * 0.4}px)`;
    });

    /* ── Submit loading state ── */
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn   = document.getElementById('btn-submit');
        const text  = document.getElementById('btn-text');
        const arrow = document.getElementById('btn-arrow');
        const spin  = document.getElementById('btn-spin');
        btn.disabled = true;
        text.textContent = 'Memproses...';
        arrow.classList.add('hidden');
        spin.classList.remove('hidden');
    });

    /* ── Shake on error (Blade) ── */
    @if($errors->any())
    document.addEventListener('DOMContentLoaded', () => {
        ['username', 'password'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('err', 'input-shake');
                el.addEventListener('input', () => el.classList.remove('err'), { once: true });
            }
        });
    });
    @endif
</script>
</body>
</html>