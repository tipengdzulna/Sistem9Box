<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pemetaan Talenta - Kemenkeu')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
    :root{
        --navy-900:#0f2040;--navy-800:#142c55;--navy-700:#1a3d73;--navy-600:#1f5199;--navy-500:#2968b8;--navy-400:#5690d4;--navy-300:#8cb5e5;--navy-200:#bdd5f2;--navy-100:#dce9f7;--navy-50:#eef4fb;
        --gold-600:#b8860b;--gold-400:#e8ad42;
        --emerald-700:#0f6e4e;--emerald-600:#15885f;--emerald-500:#1a9e6f;--emerald-100:#d1f5e4;--emerald-50:#edfcf4;
        --rose-600:#b72f3a;--rose-500:#da3a47;--rose-100:#fde2e4;--rose-50:#fef2f3;
        --amber-600:#a16b16;--amber-500:#c48820;--amber-100:#fef5d8;--amber-50:#fffbeb;
        --slate-900:#0f172a;--slate-800:#1e293b;--slate-700:#334155;--slate-600:#475569;--slate-500:#64748b;--slate-400:#94a3b8;--slate-300:#cbd5e1;--slate-200:#e2e8f0;--slate-100:#f1f5f9;--slate-50:#f8fafc;
        --bg:#f4f6f9;--card:#fff;--sidebar-w:260px;
        --r-sm:8px;--r-md:12px;--r-lg:16px;
        --sh-xs:0 1px 3px rgba(15,32,64,.04);--sh-sm:0 2px 6px rgba(15,32,64,.06);--sh-md:0 4px 16px rgba(15,32,64,.08);--sh-lg:0 8px 30px rgba(15,32,64,.10);
    }
    *,*::before,*::after{box-sizing:border-box}
    body{font-family:'Inter',system-ui,-apple-system,sans-serif;background:var(--bg);color:var(--slate-800);margin:0;min-height:100vh;-webkit-font-smoothing:antialiased;font-size:14px;line-height:1.5}
    code,.mono{font-family:'JetBrains Mono',monospace}

    /* ── SIDEBAR ── */
    .sidebar{position:fixed;top:0;left:0;bottom:0;width:var(--sidebar-w);background:var(--navy-900);display:flex;flex-direction:column;z-index:1040;transition:transform .3s cubic-bezier(.4,0,.2,1)}
    .sidebar-brand{padding:22px 20px 18px;border-bottom:1px solid rgba(255,255,255,.07)}
    .sidebar-brand .bi-icon{width:40px;height:40px;background:linear-gradient(135deg,var(--gold-400),var(--gold-600));border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 12px rgba(212,149,43,.3);overflow:hidden;flex-shrink:0}
    .sidebar-brand h1{font-size:1rem;font-weight:700;color:#fff;margin:0;line-height:1.3}
    .sidebar-brand small{font-size:.7rem;color:var(--navy-400);font-weight:500;letter-spacing:.04em;text-transform:uppercase}
    .sidebar-nav{flex:1;padding:14px 12px;overflow-y:auto}
    .nav-section{font-size:.7rem;font-weight:700;color:var(--navy-400);text-transform:uppercase;letter-spacing:.08em;padding:18px 14px 8px}
    .sidebar-link{display:flex;align-items:center;gap:12px;padding:11px 16px;border-radius:var(--r-md);color:rgba(255,255,255,.65);font-size:.9rem;font-weight:500;text-decoration:none;transition:all .15s;margin-bottom:3px}
    .sidebar-link i{font-size:1.15rem;width:22px;text-align:center;opacity:.7}
    .sidebar-link:hover{color:#fff;background:rgba(255,255,255,.07)}.sidebar-link:hover i{opacity:1}
    .sidebar-link.active{color:#fff;background:linear-gradient(135deg,var(--navy-700),var(--navy-600));box-shadow:0 2px 10px rgba(31,81,153,.3)}
    .sidebar-link.active i{opacity:1;color:var(--gold-400)}
    .sidebar-user #userDropdown a:hover{background:rgba(255,255,255,.07);color:#fff}
    .sidebar-user #userDropdown button:hover{background:rgba(248,113,113,.08);color:#fca5a5}

    /* ── MAIN LAYOUT ── */
    .main-area{margin-left:var(--sidebar-w);min-height:100vh}
    .topbar{position:sticky;top:0;z-index:1030;background:rgba(244,246,249,.88);backdrop-filter:blur(12px);border-bottom:1px solid var(--slate-200);padding:14px 32px;display:flex;align-items:center;justify-content:space-between;gap:16px}
    .topbar-title{font-size:1.05rem;font-weight:700;color:var(--slate-800);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .topbar-bc{font-size:.78rem;color:var(--slate-500);font-weight:500}
    .main-content{padding:28px 32px 48px}

    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(11,21,39,.55);z-index:1035;backdrop-filter:blur(4px)}
    .sidebar-overlay.show{display:block}
    .mobile-toggle{display:none;background:none;border:none;font-size:1.4rem;color:var(--slate-700);padding:6px;cursor:pointer}

    /* ── CARDS ── */
    .card-custom{background:var(--card);border:1px solid var(--slate-200);border-radius:var(--r-lg);box-shadow:var(--sh-xs);transition:box-shadow .2s}
    .card-section-title{font-size:.9rem;font-weight:700;color:var(--slate-700);display:flex;align-items:center;gap:8px;margin-bottom:14px}
    .card-section-title i{color:var(--navy-500);font-size:1rem}

    /* ── SUMMARY ── */
    .summary-card{border-radius:var(--r-lg);padding:22px 20px;position:relative;overflow:hidden;border:1px solid var(--slate-200)}
    .summary-card::before{content:'';position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;opacity:.08}
    .summary-card .s-number{font-size:2rem;font-weight:800;line-height:1;letter-spacing:-.03em}
    .summary-card .s-label{font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:6px;opacity:.7}
    .summary-card .s-icon{position:absolute;top:18px;right:18px;font-size:1.3rem;opacity:.2}
    .sc-blue{background:linear-gradient(135deg,var(--navy-50),#e8f0fb);border-color:var(--navy-200)}.sc-blue .s-number{color:var(--navy-700)}.sc-blue::before{background:var(--navy-500)}
    .sc-green{background:linear-gradient(135deg,var(--emerald-50),#ddf6ea);border-color:#b4e8d0}.sc-green .s-number{color:var(--emerald-700)}.sc-green::before{background:var(--emerald-500)}
    .sc-amber{background:linear-gradient(135deg,var(--amber-50),var(--amber-100));border-color:#f5dfa0}.sc-amber .s-number{color:var(--amber-600)}.sc-amber::before{background:var(--amber-500)}
    .sc-rose{background:linear-gradient(135deg,var(--rose-50),var(--rose-100));border-color:#f5b8bc}.sc-rose .s-number{color:var(--rose-600)}.sc-rose::before{background:var(--rose-500)}

    /* ── 9-BOX GRID ── */
    .nine-box-grid{display:grid;grid-template-columns:40px 1fr 1fr 1fr;grid-template-rows:auto 1fr 1fr 1fr auto;gap:0;width:100%;max-width:720px;margin:0 auto}
    .grid-corner{background:transparent}
    .grid-header-col{text-align:center;font-weight:700;font-size:.8rem;text-transform:uppercase;letter-spacing:.06em;color:var(--navy-600);padding:8px 4px}
    .grid-header-row{writing-mode:vertical-lr;transform:rotate(180deg);text-align:center;font-weight:700;font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;color:var(--navy-600);display:flex;align-items:center;justify-content:center;padding:6px 2px}
    .grid-cell{border:1.5px solid var(--slate-200);padding:20px 12px;text-align:center;min-height:120px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;transition:all .2s;cursor:default;position:relative}
    .grid-cell:hover{transform:scale(1.03);z-index:2;box-shadow:var(--sh-lg)}
    .grid-cell .box-number{font-size:1.8rem;font-weight:800;line-height:1;letter-spacing:-.03em}
    .grid-cell .box-count{font-size:.82rem;font-weight:600;padding:3px 12px;border-radius:20px}
    .grid-cell .box-label{font-size:.68rem;line-height:1.3;max-width:140px;opacity:.55}
    .cell-danger{background:var(--rose-50);border-color:#f5c4c7}.cell-danger .box-number{color:var(--rose-600)}.cell-danger .box-count{color:var(--rose-600);background:var(--rose-100)}
    .cell-amber{background:var(--amber-50);border-color:#f0d89a}.cell-amber .box-number{color:var(--amber-600)}.cell-amber .box-count{color:var(--amber-600);background:var(--amber-100)}
    .cell-green{background:var(--emerald-50);border-color:#b0e5cc}.cell-green .box-number{color:var(--emerald-700)}.cell-green .box-count{color:var(--emerald-700);background:var(--emerald-100)}
    .cell-blue{background:var(--navy-50);border-color:var(--navy-200)}.cell-blue .box-number{color:var(--navy-600)}.cell-blue .box-count{color:var(--navy-600);background:var(--navy-100)}
    .cell-star{background:linear-gradient(135deg,var(--emerald-50),var(--navy-50));border-color:#8cd4b5}.cell-star .box-number{color:var(--emerald-700)}.cell-star .box-count{color:var(--emerald-700);background:var(--emerald-100)}
    .grid-axis-label{text-align:center;font-weight:800;font-size:.82rem;text-transform:uppercase;letter-spacing:.1em;color:var(--navy-600);padding:8px}

    /* ── TABLE ── */
    .table-custom{font-size:.88rem;margin-bottom:0}
    .table-custom thead th{background:var(--navy-800);color:#fff;font-weight:600;font-size:.78rem;text-transform:uppercase;letter-spacing:.03em;padding:12px 14px;white-space:nowrap;border:none;position:sticky;top:0;z-index:2}
    .table-custom thead th:first-child{border-radius:var(--r-md) 0 0 0}.table-custom thead th:last-child{border-radius:0 var(--r-md) 0 0}
    .table-custom td{padding:10px 14px;vertical-align:middle;border-color:var(--slate-100)}
    .table-custom tbody tr{transition:background .12s}.table-custom tbody tr:hover{background:var(--navy-50)}

    /* ── BADGES ── */
    .badge-box{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--r-sm);font-weight:700;font-size:.88rem;color:#fff}
    .badge-box-1{background:var(--rose-500)}.badge-box-2{background:#e07730}.badge-box-3{background:var(--amber-500)}
    .badge-box-4{background:#4aac6b}.badge-box-5{background:var(--navy-500)}.badge-box-6{background:#8562c6}
    .badge-box-7{background:var(--navy-600)}.badge-box-8{background:var(--emerald-600)}.badge-box-9{background:var(--emerald-700)}
    .badge-kinerja,.badge-potensial{font-size:.78rem;padding:4px 10px;border-radius:var(--r-sm);font-weight:600;white-space:nowrap}
    .badge-kinerja-atas{background:var(--emerald-100);color:var(--emerald-700)}.badge-kinerja-sesuai{background:var(--navy-100);color:var(--navy-700)}.badge-kinerja-bawah{background:var(--rose-100);color:var(--rose-600)}
    .badge-potensial-tinggi{background:var(--emerald-100);color:var(--emerald-700)}.badge-potensial-menengah{background:var(--amber-100);color:var(--amber-600)}.badge-potensial-rendah{background:var(--rose-100);color:var(--rose-600)}
    .badge-ue1{font-size:.75rem;padding:3px 10px;border-radius:var(--r-sm);font-weight:600;background:var(--slate-100);color:var(--slate-700);white-space:nowrap;border:1px solid var(--slate-200)}

    /* ── BUTTONS ── */
    .btn-primary-custom{background:var(--navy-700);color:#fff;border:none;font-weight:600;border-radius:var(--r-md);padding:9px 20px;font-size:.88rem;transition:all .2s;box-shadow:var(--sh-xs)}
    .btn-primary-custom:hover{background:var(--navy-600);color:#fff;transform:translateY(-1px);box-shadow:var(--sh-md)}
    .btn-outline-custom{border:1.5px solid var(--navy-500);color:var(--navy-600);background:transparent;font-weight:600;border-radius:var(--r-md);padding:8px 18px;font-size:.88rem;transition:all .2s}
    .btn-outline-custom:hover{background:var(--navy-700);color:#fff;border-color:var(--navy-700)}

    /* ── FORMS ── */
    .form-control,.form-select{border:1.5px solid var(--slate-200);border-radius:var(--r-md);padding:9px 14px;font-size:.9rem;transition:all .15s;background:var(--card)}
    .form-control:focus,.form-select:focus{border-color:var(--navy-400);box-shadow:0 0 0 3px rgba(41,104,184,.1)}
    .form-label{font-weight:600;font-size:.8rem;color:var(--slate-600);margin-bottom:4px;text-transform:uppercase;letter-spacing:.02em}
    .filter-bar{background:var(--slate-50);border:1px solid var(--slate-200);border-radius:var(--r-lg);padding:18px 20px}
    .upload-area{border:2px dashed var(--slate-300);border-radius:var(--r-lg);padding:40px 24px;text-align:center;transition:all .25s;cursor:pointer;background:var(--slate-50)}
    .upload-area:hover,.upload-area.dragover{border-color:var(--navy-400);background:var(--navy-50)}
    .upload-area i{font-size:2.4rem;color:var(--navy-400)}
    .alert-custom{border-radius:var(--r-md);border:none;font-size:.9rem;font-weight:500;padding:14px 18px}
    .alert-filter{background:var(--navy-50);border:1px solid var(--navy-200);color:var(--navy-700)}
    .ref-table{font-size:.85rem}.ref-table th{font-size:.75rem;text-transform:uppercase;letter-spacing:.03em;color:var(--slate-500);background:var(--slate-50);font-weight:600}
    .pagination{margin:0;gap:4px}
    .pagination .page-link{border:1px solid var(--slate-200);color:var(--navy-600);font-size:.85rem;font-weight:600;padding:7px 13px;border-radius:var(--r-sm);transition:all .15s}
    .pagination .page-link:hover{background:var(--navy-700);color:#fff;border-color:var(--navy-700)}
    .pagination .page-item.active .page-link{background:var(--navy-700);border-color:var(--navy-700);color:#fff}
    .pagination .page-item.disabled .page-link{color:var(--slate-400);background:var(--slate-50)}

    @keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
    .animate-in{animation:fadeUp .35s ease-out}

    /* ── RESPONSIVE ── */
    @media(max-width:991.98px){
        .sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0)}
        .main-area{margin-left:0}
        .topbar{padding:12px 18px}
        .main-content{padding:20px 18px 100px}
        .mobile-toggle{display:block}
    }
    @media(max-width:575.98px){
        .main-content{padding:16px 14px 100px}
        .topbar{padding:10px 14px}
        .topbar-title{font-size:.95rem}
        .summary-card{padding:16px 14px}
        .summary-card .s-number{font-size:1.6rem}
        .summary-card .s-label{font-size:.7rem}
        .nine-box-grid{grid-template-columns:32px 1fr 1fr 1fr}
        .grid-cell{min-height:80px;padding:12px 6px}
        .grid-cell .box-number{font-size:1.25rem}
        .grid-cell .box-label{display:none}
        .grid-cell .box-count{font-size:.7rem;padding:2px 8px}
        .grid-header-col{font-size:.62rem}
        .grid-header-row{font-size:.56rem;padding:3px}
        .card-custom{border-radius:var(--r-md)}
        .filter-bar{padding:14px}
        .filter-bar .row>div{flex:0 0 100%;max-width:100%}
    }

    /* ── BOTTOM NAV (mobile) ── */
    .bottom-nav{display:none;position:fixed;bottom:0;left:0;right:0;background:var(--card);border-top:1px solid var(--slate-200);z-index:1050;padding:8px 0 max(8px,env(safe-area-inset-bottom));box-shadow:0 -2px 14px rgba(15,32,64,.06)}
    .bottom-nav-inner{display:flex;justify-content:space-around;max-width:440px;margin:0 auto}
    .bnav-link{display:flex;flex-direction:column;align-items:center;gap:3px;padding:8px 14px;text-decoration:none;color:var(--slate-400);font-size:.7rem;font-weight:600;transition:color .15s;border-radius:var(--r-md)}
    .bnav-link i{font-size:1.25rem}
    .bnav-link.active{color:var(--navy-700)}.bnav-link.active i{color:var(--navy-600)}
    @media(max-width:991.98px){.bottom-nav{display:block}}
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center" style="gap:12px">
                <img src="{{ asset('images/logo-kemenkeu.png') }}" 
                     alt="Logo Kemenkeu" 
                     style="width:33px;height:33px;object-fit:contain">
            <div><h1>Talent Mapping</h1><small>Kementerian Keuangan</small></div>
        </div>
    </div>

    {{-- User info + dropdown --}}
    <div class="sidebar-user" style="padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.07)">
        <div class="position-relative">
            <button onclick="toggleUserMenu()" class="d-flex align-items-center w-100" style="background:none;border:none;cursor:pointer;padding:10px 12px;text-align:left;border-radius:var(--r-md);transition:background .12s;gap:12px" onmouseover="this.style.background='rgba(255,255,255,.05)'" onmouseout="this.style.background='none'">
                <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--navy-600),var(--navy-500));display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="bi bi-person-fill" style="font-size:1rem;color:#fff"></i>
                </div>
                <div style="min-width:0;flex:1">
                    <div style="font-size:.88rem;color:#fff;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ auth()->user()->name }}</div>
                    <div style="font-size:.72rem;color:var(--navy-400)">
                        {{ auth()->user()->role_label }}@if(auth()->user()->isOperator()) &middot; {{ auth()->user()->ue1_short }}@endif
                    </div>
                </div>
                <i class="bi bi-chevron-down" style="color:var(--navy-400);font-size:.75rem;transition:transform .2s;flex-shrink:0" id="userMenuChevron"></i>
            </button>
            {{-- Dropdown --}}
            <div id="userDropdown" style="display:none;position:absolute;top:100%;left:0;right:0;margin-top:6px;background:var(--navy-800);border:1px solid rgba(255,255,255,.1);border-radius:var(--r-md);overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.3);z-index:10">
                <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:10px;padding:12px 16px;color:rgba(255,255,255,.7);font-size:.88rem;font-weight:500;text-decoration:none;transition:all .12s">
                    <i class="bi bi-person-gear" style="font-size:1rem;width:20px;text-align:center"></i>Edit Profil
                </a>
                <div style="height:1px;background:rgba(255,255,255,.06)"></div>
                <button onclick="confirmLogout()" style="display:flex;align-items:center;gap:10px;padding:12px 16px;color:#f87171;font-size:.88rem;font-weight:500;background:none;border:none;cursor:pointer;width:100%;text-align:left;transition:all .12s">
                    <i class="bi bi-box-arrow-right" style="font-size:1rem;width:20px;text-align:center"></i>Keluar
                </button>
            </div>
        </div>
        <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Menu Utama</div>
        <a href="{{ route('pegawai.index') }}" class="sidebar-link {{ request()->routeIs('pegawai.index') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i>Dashboard
        </a>
        <a href="{{ route('pegawai.create') }}" class="sidebar-link {{ request()->routeIs('pegawai.create') ? 'active' : '' }}">
            <i class="bi bi-person-plus"></i>Input Manual
        </a>
        <a href="{{ route('pegawai.import.form') }}" class="sidebar-link {{ request()->routeIs('pegawai.import.form') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-arrow-up"></i>Import Excel
        </a>
        <div class="nav-section">Lainnya</div>
        <a href="{{ route('pegawai.template') }}" class="sidebar-link"><i class="bi bi-file-earmark-spreadsheet"></i>Download Template</a>
        <a href="{{ route('pegawai.export') }}" class="sidebar-link"><i class="bi bi-download"></i>Export Data</a>

        @if(auth()->user()->isSuperAdmin())
        <div class="nav-section">Administrasi</div>
        <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>Kelola User
        </a>
        @endif
    </nav>
</aside>

<div class="main-area">
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="mobile-toggle" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
            <div>
                <div class="topbar-bc">Pemetaan Talenta</div>
                <h2 class="topbar-title">@yield('topbar-title', 'Import dari Excel')</h2>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            @if(auth()->user()->isOperator())
            <span style="font-size:.8rem;padding:5px 12px;border-radius:8px;font-weight:600;background:var(--amber-100);color:var(--amber-600);white-space:nowrap">
                <i class="bi bi-building me-1"></i>{{ auth()->user()->ue1_short }}
            </span>
            @endif
            <span style="font-size:.82rem;color:var(--slate-400);font-weight:500;white-space:nowrap" class="d-none d-sm-inline">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('d M Y') }}
            </span>
        </div>
    </header>

    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-custom alert-dismissible fade show animate-in mb-3"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-custom alert-dismissible fade show animate-in mb-3"><i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('import_errors') && count(session('import_errors')) > 0)
            <div class="alert alert-warning alert-custom alert-dismissible fade show animate-in mb-3">
                <strong><i class="bi bi-exclamation-triangle-fill me-1"></i>Peringatan Import:</strong>
                <ul class="mb-0 mt-2" style="font-size:.88rem">@foreach(session('import_errors') as $err)<li>{{ $err }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </main>
</div>

<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="{{ route('pegawai.index') }}" class="bnav-link {{ request()->routeIs('pegawai.index') ? 'active' : '' }}"><i class="bi bi-bar-chart-line"></i>Dashboard</a>
        <a href="{{ route('pegawai.create') }}" class="bnav-link {{ request()->routeIs('pegawai.create') ? 'active' : '' }}"><i class="bi bi-person-plus"></i>Input</a>
        <a href="{{ route('pegawai.import.form') }}" class="bnav-link {{ request()->routeIs('pegawai.import.form') ? 'active' : '' }}"><i class="bi bi-file-earmark-arrow-up"></i>Import</a>
        <a href="{{ route('pegawai.export') }}" class="bnav-link"><i class="bi bi-download"></i>Export</a>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar(){document.getElementById('sidebar').classList.toggle('open');document.getElementById('sidebarOverlay').classList.toggle('show');document.body.style.overflow=document.getElementById('sidebar').classList.contains('open')?'hidden':''}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('show');document.body.style.overflow=''}

function toggleUserMenu(){
    const dd=document.getElementById('userDropdown');
    const ch=document.getElementById('userMenuChevron');
    const open=dd.style.display==='none';
    dd.style.display=open?'block':'none';
    ch.style.transform=open?'rotate(180deg)':'';
}
document.addEventListener('click',function(e){
    const dd=document.getElementById('userDropdown');
    const su=document.querySelector('.sidebar-user');
    if(dd && dd.style.display==='block' && su && !su.contains(e.target)){
        dd.style.display='none';
        document.getElementById('userMenuChevron').style.transform='';
    }
});
function confirmLogout(){
    if(confirm('Yakin akan keluar dari sistem?')){
        document.getElementById('logoutForm').submit();
    }
}
</script>
@stack('scripts')
</body>
</html>