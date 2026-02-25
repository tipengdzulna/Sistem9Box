<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pemetaan Talenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box}
        body{font-family:'DM Sans',system-ui,sans-serif;margin:0;min-height:100vh;display:flex;background:#0f2040;-webkit-font-smoothing:antialiased}
        .login-left{flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:40px;background:linear-gradient(135deg,#0b1527,#142c55,#1a3d73);position:relative;overflow:hidden}
        .login-left::before{content:'';position:absolute;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(232,173,66,.08),transparent 70%);top:-100px;right:-100px}
        .brand{position:relative;z-index:1;text-align:center;max-width:380px}
        .brand-icon{width:64px;height:64px;background:linear-gradient(135deg,#e8ad42,#b8860b);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:1.6rem;color:#0b1527;margin-bottom:20px;box-shadow:0 8px 32px rgba(232,173,66,.25)}
        .brand h1{color:#fff;font-size:1.5rem;font-weight:800;margin:0 0 6px;letter-spacing:-.03em}
        .brand p{color:rgba(255,255,255,.5);font-size:.82rem;line-height:1.5;margin:0 0 32px}
        .brand .feat{display:flex;align-items:center;gap:10px;color:rgba(255,255,255,.6);font-size:.78rem;margin-bottom:10px}
        .brand .feat i{color:#e8ad42;font-size:.9rem;width:20px;text-align:center}
        .login-right{width:480px;display:flex;flex-direction:column;justify-content:center;padding:48px;background:#fff}
        .login-right h2{font-size:1.4rem;font-weight:800;color:#0f172a;margin:0 0 4px}
        .login-right .sub{font-size:.82rem;color:#64748b;margin-bottom:28px}
        .fg{margin-bottom:18px}
        .fg label{display:block;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px}
        .fg .iw{position:relative}
        .fg .iw i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.95rem}
        .fg input{width:100%;padding:11px 14px 11px 42px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.88rem;font-family:inherit;transition:all .15s;background:#f8fafc}
        .fg input:focus{outline:none;border-color:#2968b8;box-shadow:0 0 0 3px rgba(41,104,184,.1);background:#fff}
        .rr{display:flex;align-items:center;margin-bottom:24px}
        .rr label{font-size:.8rem;color:#475569;display:flex;align-items:center;gap:6px;cursor:pointer}
        .rr input[type=checkbox]{accent-color:#1a3d73;width:16px;height:16px}
        .btn-login{width:100%;padding:12px;background:linear-gradient(135deg,#1a3d73,#225599);color:#fff;border:none;border-radius:10px;font-size:.88rem;font-weight:700;font-family:inherit;cursor:pointer;transition:all .2s;box-shadow:0 2px 8px rgba(26,61,115,.2)}
        .btn-login:hover{background:linear-gradient(135deg,#225599,#2968b8);transform:translateY(-1px);box-shadow:0 4px 16px rgba(26,61,115,.3)}
        .err-box{background:#fef2f3;border:1px solid #f5b8bc;border-radius:10px;padding:10px 14px;margin-bottom:18px;font-size:.82rem;color:#b72f3a;display:flex;align-items:center;gap:8px}
        .login-ft{text-align:center;margin-top:28px;font-size:.7rem;color:#94a3b8}
        @media(max-width:900px){body{flex-direction:column}.login-left{min-height:180px;padding:28px}.brand h1{font-size:1.2rem}.brand p,.brand .feat{display:none}.login-right{width:100%;flex:1;padding:32px 24px}}
    </style>
</head>
<body>
    <div class="login-left">
        <div class="brand">
            
                <img src="{{ asset('images/logo-kemenkeu.png') }}" 
                     alt="Logo Kemenkeu" 
                     style="width:80px;height:80px;margin-bottom:8px;object-fit:contain">
            
            <h1>Pemetaan PermenPAN</h1>
            <p>Sistem Pemetaan Talenta<br>Kementerian Keuangan Republik Indonesia</p>
            <div class="feat"><i class="bi bi-grid-3x3-gap"></i>Pemetaan 9-Box Grid</div>
            <div class="feat"><i class="bi bi-building"></i>14 Unit Eselon I</div>
            <div class="feat"><i class="bi bi-file-earmark-excel"></i>Import & Export Excel</div>
            <div class="feat"><i class="bi bi-shield-check"></i>Multi-role Access Control</div>
        </div>
    </div>
    <div class="login-right">
        <h2>Masuk ke Sistem</h2>
        <p class="sub">Silakan login dengan akun yang sudah terdaftar</p>
        @if($errors->any())<div class="err-box"><i class="bi bi-exclamation-circle-fill"></i>{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="fg"><label>Username</label><div class="iw"><i class="bi bi-person"></i><input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username" autofocus required></div></div>
            <div class="fg"><label>Password</label><div class="iw"><i class="bi bi-lock"></i><input type="password" name="password" placeholder="Masukkan password" required></div></div>
            <div class="rr"><label><input type="checkbox" name="remember"> Ingat saya</label></div>
            <button type="submit" class="btn-login"><i class="bi bi-box-arrow-in-right me-1"></i>Masuk</button>
        </form>
        <div class="login-ft">Pemetaan Versi Permen PAN Baru &middot; Kemenkeu RI</div>
    </div>
</body>
</html>