<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Portal Koperasi KOPIN</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-canvas, #F8FAFC);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 24px;
            box-sizing: border-box;
        }
        .auth-container {
            width: 100%;
            max-width: 420px;
        }
        .auth-card {
            background: #FFFFFF;
            padding: 40px 32px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-subtle, #E2E8F0);
        }
        .auth-brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-badge {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--emerald-600, #059669);
            color: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
        }
        .auth-brand h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            color: var(--emerald-700, #047857);
            letter-spacing: -0.02em;
        }
        .auth-brand p {
            color: var(--text-secondary, #64748B);
            margin: 4px 0 0 0;
            font-size: 13.5px;
        }
        .auth-links {
            text-align: center;
            margin-top: 24px;
            font-size: 13.5px;
            color: var(--text-secondary, #64748B);
        }
        .auth-links a {
            color: var(--emerald-600, #059669);
            font-weight: 600;
            text-decoration: none;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
        .back-home {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
        }
        .back-home a {
            color: var(--text-muted, #94A3B8);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .back-home a:hover {
            color: var(--emerald-600, #059669);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-brand">
                <a href="{{ route('home') }}" style="text-decoration: none;">
                    <div class="brand-badge">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <h1>KOPIN</h1>
                </a>
                <p>Masuk ke Portal Layanan Koperasi</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email:</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password" placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px; margin-top: 8px;">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Akun
                </button>
            </form>

            <div class="auth-links">
                Belum menjadi anggota? <a href="{{ route('register') }}">Daftar Sekarang</a>
            </div>
        </div>

        <div class="back-home">
            <a href="{{ route('home') }}">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda Utama KOPIN
            </a>
        </div>
    </div>
</body>
</html>
