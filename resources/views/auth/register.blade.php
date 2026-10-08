<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - KOPIN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f3f4f6; margin: 0; padding: 40px 20px; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; box-sizing: border-box; }
        .auth-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 600px; box-sizing: border-box; }
        .auth-header { text-align: center; margin-bottom: 30px; }
        .auth-header h2 { margin: 0; color: #10b981; }
        .auth-header p { color: #6b7280; margin-top: 5px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 0.9em; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-family: inherit; }
        .btn { width: 100%; padding: 12px; background-color: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 1em; margin-top: 20px; }
        .btn:hover { background-color: #059669; }
        .auth-links { text-align: center; margin-top: 20px; font-size: 0.9em; }
        .auth-links a { color: #10b981; text-decoration: none; }
        .alert-danger { color: #b91c1c; background-color: #fee2e2; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9em; }
        .full-width { grid-column: 1 / -1; }
        @media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <h2>KOPIN</h2>
            <p>Pendaftaran Anggota Baru</p>
        </div>

        @if ($errors->any())
            <div class="alert-danger">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="name">Nama Lengkap Sesuai KTP</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="nik">NIK (16 Digit)</label>
                    <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik') }}" required maxlength="16" minlength="16">
                </div>

                <div class="form-group">
                    <label for="password">Password (Minimal 8 Karakter)</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                </div>

                <div class="form-group">
                    <label for="telepon">Nomor Telepon/HP</label>
                    <input type="text" name="telepon" id="telepon" class="form-control" value="{{ old('telepon') }}" required>
                </div>

                <div class="form-group full-width">
                    <label for="alamat">Alamat Lengkap</label>
                    <textarea name="alamat" id="alamat" class="form-control" rows="3" required>{{ old('alamat') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="pekerjaan">Pekerjaan</label>
                    <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" value="{{ old('pekerjaan') }}" required>
                </div>

                <div class="form-group">
                    <label for="perusahaan">Nama Perusahaan/Usaha (Opsional)</label>
                    <input type="text" name="perusahaan" id="perusahaan" class="form-control" value="{{ old('perusahaan') }}">
                </div>
            </div>

            <button type="submit" class="btn">Daftar Keanggotaan</button>
        </form>

        <div class="auth-links">
            <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
        </div>
    </div>
</body>
</html>
