<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Anggota Baru — KOPIN</title>
    
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
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            box-sizing: border-box;
        }
        .auth-container {
            width: 100%;
            max-width: 660px;
        }
        .auth-card {
            background: #FFFFFF;
            padding: 40px 36px;
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
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .full-width {
            grid-column: 1 / -1;
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
        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .auth-card {
                padding: 28px 20px;
            }
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
                <p>Formulir Pendaftaran Anggota Baru</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 24px;">
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
                        <label for="name" class="form-label">Nama Lengkap Sesuai KTP:</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso">
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email:</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required placeholder="nama@email.com">
                    </div>

                    <div class="form-group">
                        <label for="nik" class="form-label">NIK (16 Digit):</label>
                        <input type="text" name="nik" id="nik" class="form-control font-mono" value="{{ old('nik') }}" required maxlength="16" minlength="16" placeholder="3201xxxxxxxxxxxx">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Minimal 8 karakter">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password:</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Ulangi password">
                    </div>

                    <div class="form-group">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir:</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="telepon" class="form-label">Nomor Telepon/HP:</label>
                        <input type="text" name="telepon" id="telepon" class="form-control font-mono" value="{{ old('telepon') }}" required placeholder="0812xxxxxxxx">
                    </div>

                    <div class="form-group full-width">
                        <label for="alamat" class="form-label">Alamat Lengkap (Sesuai KTP):</label>
                        <textarea name="alamat" id="alamat" class="form-control" rows="3" required placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota/kabupaten">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="pekerjaan" class="form-label">Pekerjaan:</label>
                        <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" value="{{ old('pekerjaan') }}" required placeholder="Contoh: Karyawan Swasta / Wiraswasta">
                    </div>

                    <div class="form-group">
                        <label for="perusahaan" class="form-label">Nama Perusahaan / Tempat Usaha (Opsional):</label>
                        <input type="text" name="perusahaan" id="perusahaan" class="form-control" value="{{ old('perusahaan') }}" placeholder="PT Contoh / Toko Berkah">
                    </div>
                </div>

                <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 12px; margin-bottom: 20px; line-height: 1.5;">
                    <i class="fa-solid fa-circle-info" style="color: var(--emerald-600); margin-right: 4px;"></i>
                    Setelah mendaftar, data Anda akan diverifikasi oleh Admin. Anda akan diwajibkan menyetorkan Simpanan Pokok (Rp 55.000) untuk mengaktifkan keanggotaan.
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px;">
                    <i class="fa-solid fa-user-plus"></i> Kirim Formulir Pendaftaran
                </button>
            </form>

            <div class="auth-links">
                Sudah memiliki akun anggota? <a href="{{ route('login') }}">Masuk di sini</a>
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
