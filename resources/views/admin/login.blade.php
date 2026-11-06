
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Customer Service - NADI</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f7fb;
            color: #1e293b;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            padding: 36px 30px;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
        }
        .brand { text-align: center; margin-bottom: 28px; }
        .brand-icon {
            margin: auto auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: #e0f2fe;
            color: #0369a1;
            font-weight: 800;
            font-size: 24px;
        }
        h1 { font-size: 23px; margin: 0 0 8px; }
        .subtitle { color: #64748b; font-size: 13px; line-height: 1.6; }
        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .field { margin-bottom: 20px; }
        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-size: 14px;
            outline: none;
        }
        input:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px #e0f2fe;
        }
        .submit {
            width: 100%;
            background: #0369a1;
            color: white;
            border: none;
            border-radius: 9px;
            padding: 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }
        .submit:hover { background: #075985; }
        .alert {
            background: #fef2f2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 9px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand">
            <div class="brand-icon">N</div>
            <h1>Customer Service NADI</h1>
            <div class="subtitle">
                Dashboard layanan bantuan dan ulasan pasien
                <br>RSUD Bali Mandara
            </div>
        </div>

        @if ($errors->any())
            <div class="alert" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('nadi.cs.login.submit') }}">
            @csrf

            <div class="field">
                <label for="email">Email Petugas</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Email petugas CS"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit" class="submit">
                Masuk Dashboard CS
            </button>
        </form>

        <div class="footer">
            © {{ date('Y') }} NADI - RSUD Bali Mandara
        </div>
    </div>
</body>
</html>
