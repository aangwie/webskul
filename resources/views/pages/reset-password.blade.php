<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SMP Negeri 6 Sudimoro</title>
    @php $schoolFavicon = \App\Models\SchoolProfile::first(); @endphp
    @if($schoolFavicon && $schoolFavicon->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $schoolFavicon->logo) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-light: #2c4f7c;
            --primary-dark: #0f2340;
            --secondary: #ffffff;
            --accent: #f8f9fa;
            --text: #333333;
            --text-light: #6c757d;
            --shadow-lg: 0 10px 40px rgba(30, 58, 95, 0.2);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container { width: 100%; max-width: 420px; }

        .card {
            background: var(--secondary);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            padding: 50px 40px;
            text-align: center;
        }

        .logo { margin-bottom: 30px; }
        .logo i { font-size: 4rem; color: var(--primary); }

        h1 { font-size: 1.5rem; font-weight: 700; color: var(--text); margin-bottom: 10px; }
        .subtitle { color: var(--text-light); font-size: 0.9rem; margin-bottom: 35px; }

        .form-group { margin-bottom: 20px; text-align: left; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--text); margin-bottom: 8px; }

        .form-input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--accent);
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
            background: var(--accent);
        }

        .form-input:focus { outline: none; border-color: var(--primary); background: var(--secondary); }

        .btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--secondary);
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
            margin-top: 10px;
        }

        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(30, 58, 95, 0.3); }

        .alert { padding: 12px 18px; border-radius: 10px; font-size: 0.85rem; margin-bottom: 20px; text-align: left; }
        .alert-error { background: #ffeaea; color: #e74c3c; }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.9rem;
            margin-top: 25px;
            transition: var(--transition);
        }

        .back-link:hover { color: var(--primary); }

        @media (max-width: 480px) { .card { padding: 40px 25px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <i class="fas fa-lock"></i>
            </div>
            <h1>Reset Password</h1>
            <p class="subtitle">Masukkan password baru Anda</p>

            @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-input" placeholder="Minimal 8 karakter" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password" required>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Reset Password
                </button>
            </form>

            <a href="{{ route('login') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Login
            </a>
        </div>
    </div>
</body>
</html>
