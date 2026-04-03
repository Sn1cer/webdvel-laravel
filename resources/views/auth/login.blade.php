<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - D'Vel Jeans</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <style>
        /* --- CSS IDENTITAS WARNA BRAND D'VEL --- */
        :root {
            --accent: #d97706;          /* Oranye/Emas (Sama seperti tombol Welcome) */
            --accent-dim: rgba(217,119,6,0.1);
            --text: #1e293b;            /* Teks utama (Slate tua) */
            --text2: #475569;           /* Teks label/keterangan */
            --border: #e2e8f0;          /* Batas abu-abu lembut */
            --red: #ef4444;             /* Merah error */
            --red-dim: rgba(239,68,68,0.05);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: #f8fafc; display: flex; justify-content: center; align-items: center; min-height: 100vh; color: var(--text); }
        
        .login-container { background: #ffffff; width: 100%; max-width: 440px; padding: 40px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 4px 10px rgba(0,0,0,0.03); text-align: center; }
        
        .brand-logo { font-family: 'DM Serif Display', serif; font-size: 32px; color: var(--text); margin-bottom: 10px; letter-spacing: 2px;}
        .title { font-size: 26px; font-weight: 700; margin-bottom: 8px; color: var(--text);}
        .subtitle { font-size: 14px; color: var(--text2); margin-bottom: 40px; } /* Tambah margin biar lega */

        /* Kotak Isian Form */
        .form-group { margin-bottom: 16px; text-align: left; }
        .form-control { width: 100%; padding: 14px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; transition: border-color 0.2s; outline: none; color: var(--text);}
        .form-control::placeholder { color: #94a3b8; }
        
        /* Penyesuaian warna fokus input mengikuti Brand */
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-dim); }

        /* Tombol Continue mengikuti Brand */
        .btn-submit { width: 100%; padding: 14px; background: var(--accent); color: white; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; transition: transform 0.2s; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px;}
        .btn-submit:hover { transform: translateY(-1px); filter: brightness(1.1); }

        /* Link Footer */
        .footer-text { margin-top: 24px; font-size: 14px; color: var(--text2); }
        .footer-text a { color: var(--accent); text-decoration: none; font-weight: 600; }
        .footer-text a:hover { text-decoration: underline; }

        /* Notifikasi Error mengikuti identitas Brand */
        .alert { background: var(--red-dim); color: var(--red); padding: 14px; border-radius: 8px; font-size: 13px; margin-bottom: 24px; text-align: left; border: 1px solid rgba(239,68,68,0.2); font-weight: 500;}
    </style>
</head>
<body>

<div class="login-container">
    <a href="/" style="text-decoration: none;"><div class="brand-logo">D'VEL</div></a>
    <h1 class="title">Masuk Ke Akun</h1>
    <p class="subtitle">Log in to D'Vel Jeans to manage your products or continue shopping.</p>

    @if ($errors->any())
        <div class="alert">
            Kredensial tidak cocok dengan data kami.
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <input type="email" name="email" class="form-control" placeholder="Email address *" value="{{ old('email') }}" required autofocus autocomplete="username">
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="Password *" required autocomplete="current-password">
        </div>

        <button type="submit" class="btn-submit">Continue</button>
    </form>

    <div class="footer-text">
        Don't have an account? <a href="{{ route('register') }}">Sign up</a>
    </div>
</div>

</body>
</html>