<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KOPIN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f3f4f6; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .auth-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; box-sizing: border-box; }
        .auth-header { text-align: center; margin-bottom: 30px; }
        .auth-header h2 { margin: 0; color: #10b981; }
        .auth-header p { color: #6b7280; margin-top: 5px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 0.9em; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-family: inherit; }
        .btn { width: 100%; padding: 10px; background-color: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 1em; }
        .btn:hover { background-color: #059669; }
        .auth-links { text-align: center; margin-top: 20px; font-size: 0.9em; }
        .auth-links a { color: #10b981; text-decoration: none; }
        .alert-danger { color: #b91c1c; background-color: #fee2e2; padding: 10px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9em; }
        .error-message { color: #dc2626; font-size: 0.85em; margin-top: 5px; display: block; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <h2>KOPIN</h2>
            <p>Silakan masuk ke akun Anda</p>
        </div>

        @if ($errors->any())
            <div class="alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn">Masuk</button>
        </form>

        <div class="auth-links">
            <p>Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a></p>
        </div>
    </div>
</body>
</html>
