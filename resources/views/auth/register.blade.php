<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Register for ChamaHub — Start managing your Chama digitally.">
    <title>ChamaHub — Register</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            background: linear-gradient(160deg, #0f0e17 0%, #1e1b4b 40%, #312e81 70%, #4f46e5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 40px 0;
        }

        /* ── Animated Orbs ── */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
        }
        .orb-1 { width: 600px; height: 600px; background: #7c3aed; opacity: 0.3;  top: -180px; right: -150px; animation: pulse 8s ease-in-out infinite; }
        .orb-2 { width: 450px; height: 450px; background: #4f46e5; opacity: 0.25; bottom: -180px; left: -100px; animation: pulse 10s ease-in-out infinite reverse; }
        .orb-3 { width: 300px; height: 300px; background: #a855f7; opacity: 0.2;  top: 35%; right: 15%; animation: pulse 12s ease-in-out infinite; }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.25; }
            50%       { transform: scale(1.18); opacity: 0.4; }
        }

        /* ── Particles ── */
        .particles { position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
        .particle {
            position: absolute;
            width: 3px; height: 3px;
            background: rgba(255,255,255,0.35);
            border-radius: 50%;
            animation: floatParticle linear infinite;
        }
        @keyframes floatParticle {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { transform: translateY(-20px) scale(1.5); opacity: 0; }
        }

        /* ── Floating stat cards ── */
        .floating-card {
            position: fixed;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px;
            padding: 18px 24px;
            color: white;
            z-index: 1;
            pointer-events: none;
        }
        .floating-card .stat-num { font-size: 1.6rem; font-weight: 800; color: #fde68a; }
        .floating-card .stat-lbl { font-size: 0.75rem; opacity: 0.75; margin-top: 2px; }

        .fc-1 { top: 10%; left: 4%; animation: floatY 7s ease-in-out infinite; }
        .fc-2 { bottom: 12%; right: 3%; animation: floatY 9s ease-in-out infinite reverse; }

        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-20px); }
        }

        @media (max-width: 768px) {
            .fc-1, .fc-2 { display: none !important; }
        }

        /* ── Auth Container ── */
        .auth-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            padding: 20px;
            animation: slideUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Brand Header ── */
        .brand-header { text-align: center; margin-bottom: 24px; }
        .brand-header a {
            display: inline-flex; align-items: center; gap: 12px;
            text-decoration: none; color: white;
            font-size: 1.8rem; font-weight: 800; letter-spacing: -0.5px;
        }
        .brand-icon-wrap {
            width: 48px; height: 48px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            backdrop-filter: blur(8px);
        }
        .brand-tagline {
            color: rgba(255,255,255,0.6);
            font-size: 0.88rem; font-weight: 400; margin-top: 6px;
        }

        /* ── Card ── */
        .auth-card {
            background: rgba(255,255,255,0.09);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 24px;
            padding: 36px 32px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35),
                        inset 0 1px 0 rgba(255,255,255,0.15);
        }

        .auth-card-title {
            color: white; font-size: 1.15rem; font-weight: 600;
            text-align: center; margin-bottom: 28px; opacity: 0.9;
        }

        /* ── Form ── */
        .form-group { margin-bottom: 16px; }

        .input-wrap {
            position: relative;
            display: flex; align-items: center;
        }
        .input-wrap i {
            position: absolute; right: 16px;
            color: rgba(255,255,255,0.4);
            font-size: 1rem; pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 13px 44px 13px 16px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'Outfit', sans-serif;
            color: white;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.4); }
        .form-control:focus {
            outline: none;
            border-color: rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.13);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.35);
        }

        .invalid-feedback {
            color: #fca5a5; font-size: 0.8rem; margin-top: 6px; display: block;
        }

        /* ── Submit Button ── */
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white; border: none;
            padding: 14px; border-radius: 12px;
            font-size: 1rem; font-weight: 700;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            transition: transform 0.25s, box-shadow 0.25s;
            letter-spacing: 0.3px;
            margin-top: 8px;
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(79,70,229,0.55);
        }
        .btn-submit:active { transform: translateY(-1px); }

        /* ── Footer ── */
        .auth-footer {
            text-align: center; margin-top: 22px;
            font-size: 0.88rem; color: rgba(255,255,255,0.55);
        }
        .auth-footer a {
            color: #a5b4fc; text-decoration: none; font-weight: 600;
            transition: color 0.2s;
        }
        .auth-footer a:hover { color: #c7d2fe; }

        .back-home {
            display: inline-flex; align-items: center; gap: 6px;
            color: rgba(255,255,255,0.5); text-decoration: none;
            font-size: 0.82rem; margin-bottom: 18px;
            transition: color 0.2s;
        }
        .back-home:hover { color: white; }

        /* ── Two-col row ── */
        .form-row {
            display: flex; gap: 12px;
        }
        .form-row .form-group { flex: 1; }

        @media (max-width: 500px) {
            .form-row { flex-direction: column; gap: 0; }
        }
    </style>
</head>
<body>

    <!-- Animated Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Floating particles -->
    <div class="particles" id="particles"></div>

    <!-- Floating stat cards -->
    <div class="floating-card fc-1">
        <div class="stat-num">2,400+</div>
        <div class="stat-lbl">Active Members</div>
    </div>
    <div class="floating-card fc-2">
        <div class="stat-num">Ksh 4.8M</div>
        <div class="stat-lbl">Total Savings</div>
    </div>

    <!-- Auth form -->
    <div class="auth-wrapper">
        <div class="brand-header">
            <a href="/">
                <div class="brand-icon-wrap">
                    <i class="bi bi-hexagon-fill" style="color:#a5b4fc;"></i>
                </div>
                ChamaHub
            </a>
            <p class="brand-tagline">Create your account and start saving</p>
        </div>

        <div class="auth-card">
            <p class="auth-card-title">Register a new membership</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Full Name -->
                <div class="form-group">
                    <div class="input-wrap">
                        <input id="name" type="text" name="name"
                               class="form-control"
                               value="{{ old('name') }}"
                               placeholder="Full name"
                               required autofocus autocomplete="name">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <div class="input-wrap">
                        <input id="email" type="email" name="email"
                               class="form-control"
                               value="{{ old('email') }}"
                               placeholder="Email address"
                               required autocomplete="username">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password + Confirm -->
                <div class="form-row">
                    <div class="form-group">
                        <div class="input-wrap">
                            <input id="password" type="password" name="password"
                                   class="form-control"
                                   placeholder="Password"
                                   required autocomplete="new-password">
                            <i class="bi bi-lock-fill"></i>
                        </div>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="input-wrap">
                            <input id="password_confirmation" type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   placeholder="Confirm password"
                                   required autocomplete="new-password">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Create Account &nbsp;<i class="bi bi-person-plus-fill"></i>
                </button>
            </form>

            <div class="auth-footer">
                Already have an account?
                <a href="{{ route('login') }}">Sign in here</a>
            </div>
        </div>

        <div class="auth-footer" style="margin-top: 18px;">
            <a href="/" class="back-home"><i class="bi bi-arrow-left"></i> Back to home</a>
        </div>
    </div>

    <script>
        const container = document.getElementById('particles');
        for (let i = 0; i < 40; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.left   = Math.random() * 100 + 'vw';
            p.style.width  = p.style.height = (Math.random() * 3 + 1.5) + 'px';
            p.style.animationDuration = (Math.random() * 18 + 10) + 's';
            p.style.animationDelay    = (Math.random() * 15) + 's';
            p.style.opacity = Math.random() * 0.5 + 0.1;
            container.appendChild(p);
        }
    </script>
</body>
</html>
