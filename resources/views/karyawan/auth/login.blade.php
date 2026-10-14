<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Anggota Karyawan — KOPIN Enterprise</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 24px;
            box-sizing: border-box;
            color: #1e293b;
        }
        .auth-container {
            width: 100%;
            max-width: 440px;
        }
        .auth-card {
            background: #FFFFFF;
            padding: 40px 32px;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
            position: relative;
            overflow: hidden;
        }
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(90deg, #6366f1 0%, #3b82f6 100%);
        }
        .brand-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .badge-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
            margin-bottom: 12px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 4px 0;
        }
        .brand-subtitle {
            font-size: 13px;
            color: #64748b;
            margin: 0;
        }
        .payroll-pill {
            display: inline-block;
            background: #eef2ff;
            color: #4338ca;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid #c7d2fe;
            margin-top: 8px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            box-sizing: border-box;
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #FFFFFF;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
        }
        .btn-submit:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }
        .alert {
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .demo-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 12px;
            margin-top: 20px;
            font-size: 12px;
            color: #475569;
        }
        .demo-fill-btn {
            background: #e2e8f0;
            border: none;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="brand-header">
                <div class="badge-icon">
                    <i class="fa-solid fa-id-badge"></i>
                </div>
                <h1 class="brand-title">Portal Anggota Karyawan</h1>
                <p class="brand-subtitle">KSP KOPIN Enterprise System</p>
                <div class="payroll-pill">
                    <i class="fa-solid fa-calculator"></i> Potong Gaji Simpanan Sukarela
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('karyawan.login.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Email Karyawan</label>
                    <input type="email" name="email" id="email" class="form-input" value="{{ old('email', 'karyawan@kopin.id') }}" required autofocus placeholder="karyawan@kopin.id">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-input" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk ke Portal Karyawan
                </button>
            </form>

            <div class="demo-box">
                <div style="font-weight: 700; margin-bottom: 4px;">Akun Demo Karyawan:</div>
                <div>Email: <code>karyawan@kopin.id</code></div>
                <div>Password: <code>password</code></div>
                <button type="button" class="demo-fill-btn" onclick="document.getElementById('email').value='karyawan@kopin.id'; document.getElementById('password').value='password';">
                    Isi Otomatis Kredensial Demo
                </button>
            </div>

            <div style="text-align: center; margin-top: 16px; font-size: 12px; color: #64748b;">
                <a href="{{ route('home') }}" style="color: #64748b; text-decoration: none;">&larr; Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>
