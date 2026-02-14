<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}</title>
    @php $schoolFavicon = \App\Models\SchoolProfile::first(); @endphp
    @if($schoolFavicon && $schoolFavicon->logo)
        <link rel="icon" type="image/png" href="{{ route('public.storage.view', ['path' => $schoolFavicon->logo]) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-light: #2c4f7c;
            --primary-dark: #0f2340;
            --secondary: #ffffff;
            --accent: #f8f9fa;
            --accent-gold: #d4af37;
            --text: #333333;
            --text-light: #6c757d;
            --shadow: 0 4px 20px rgba(30, 58, 95, 0.15);
            --shadow-lg: 0 10px 40px rgba(30, 58, 95, 0.2);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: var(--secondary);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            padding: 50px 40px;
            text-align: center;
        }

        .login-logo {
            margin-bottom: 30px;
        }

        .login-logo i {
            font-size: 4rem;
            color: var(--primary);
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 10px;
        }

        .login-subtitle {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 35px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

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

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--secondary);
        }

        .form-input.error {
            border-color: #e74c3c;
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-checkbox input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        .form-checkbox label {
            font-size: 0.85rem;
            color: var(--text-light);
        }

        .btn-login {
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

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(30, 58, 95, 0.3);
        }

        .error-message {
            background: #ffeaea;
            color: #e74c3c;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            text-align: left;
        }

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

        .back-link:hover {
            color: var(--primary);
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 40px 25px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <i class="fas fa-school"></i>
            </div>
            <h1 class="login-title">Login Admin</h1>
            <p class="login-subtitle">Masuk ke panel administrasi sekolah</p>

            @if(session('success'))
                <div
                    style="background: #e8f5e9; color: #28a745; padding: 12px 18px; border-radius: 10px; font-size: 0.85rem; margin-bottom: 20px; text-align: left;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input @error('email') error @enderror"
                        value="{{ old('email') }}" placeholder="admin@email.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password"
                            class="form-input @error('password') error @enderror" placeholder="••••••••" required>
                        <i class="fas fa-eye" id="togglePassword"
                            style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-light);"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-checkbox">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Ingat saya</label>
                    </div>
                </div>

                <div style="text-align: right; margin-bottom: 15px;">
                    <a href="{{ route('password.request') }}"
                        style="color: var(--primary); font-size: 0.85rem; text-decoration: none;">Lupa password?</a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>

            <a href="{{ route('home') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>