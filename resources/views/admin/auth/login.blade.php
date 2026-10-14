<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator — Backoffice KOPIN</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #064e3b 100%);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 24px;
            box-sizing: border-box;
            color: #1e293b;
        }
        .admin-auth-container {
            width: 100%;
            max-width: 440px;
        }
        .admin-auth-card {
            background: #FFFFFF;
            padding: 44px 36px;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        .admin-auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #4f46e5 0%, #059669 100%);
        }
        .auth-brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .admin-badge-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: linear-gradient(135deg, #3730a3 0%, #4f46e5 100%);
            color: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 12px;
            box-shadow: 0 8px 18px rgba(79, 70, 229, 0.35);
        }
        .auth-brand h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }
        .portal-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }
        .auth-brand p {
            color: #64748b;
            margin: 8px 0 0 0;
            font-size: 13.5px;
        }
        .back-links {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
        }
        .back-links a {
            color: #94a3b8;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }
        .back-links a:hover {
            color: #38bdf8;
        }
        .security-notice {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            margin-top: 20px;
            font-size: 11.5px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="admin-auth-container">
        <div class="admin-auth-card">
            <div class="auth-brand">
                <div class="admin-badge-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h1>KOPIN ENTERPRISE</h1>
                <div class="portal-tag">
                    <i class="fa-solid fa-lock"></i> Backoffice Ops Portal
                </div>
                <p>Khusus Administrator & Petugas Koperasi</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success" style="margin-bottom: 20px; font-size: 13px; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 10px 14px; border-radius: 8px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 20px; font-size: 13px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 10px 14px; border-radius: 8px;">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}">
                @csrf
                
                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="email" class="form-label" style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">
                        Email Administrator:
                    </label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@kopin.id" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="password" class="form-label" style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">
                        Password:
                    </label>
                    <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password" placeholder="••••••••" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 14.5px; font-weight: 700; background: #4f46e5; border-color: #4338ca; border-radius: 8px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); color: white; cursor: pointer;">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk Sebagai Administrator
                </button>
            </form>

            <div class="security-notice">
                <i class="fa-solid fa-fingerprint" style="font-size: 16px; color: #4f46e5;"></i>
                <span>Sesi ini terenkripsi dan terisolasi mandiri dari portal anggota.</span>
            </div>
        </div>

        <div class="back-links">
            <a href="{{ route('login') }}" style="margin-right: 16px; color: #94a3b8;">
                <i class="fa-solid fa-user"></i> Login Anggota
            </a>
            <span style="color: rgba(255,255,255,0.2);">|</span>
            <a href="{{ route('home') }}" style="margin-left: 16px; color: #94a3b8;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Website
            </a>
        </div>
    </div>
</body>
</html>
