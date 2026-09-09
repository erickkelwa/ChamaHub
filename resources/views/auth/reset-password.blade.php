<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Set a new password for your ChamaHub account.">
    <title>ChamaHub — Reset Password</title>
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
            overflow-x: hidden;
            overflow-y: auto;
            padding: 40px 0;
        }

        /* Animated Orbs */
        .orb { position: fixed; border-radius: 50%; filter: blur(90px); pointer-events: none; z-index: 0; }
        .orb-1 { width: 600px; height: 600px; background: #7c3aed; opacity: 0.3; top: -180px; right: -150px; animation: pulse 8s ease-in-out infinite; }
        .orb-2 { width: 450px; height: 450px; background: #4f46e5; opacity: 0.25; bottom: -180px; left: -100px; animation: pulse 10s ease-in-out infinite reverse; }
        .orb-3 { width: 300px; height: 300px; background: #a855f7; opacity: 0.2; top: 35%; right: 15%; animation: pulse 12s ease-in-out infinite; }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.25; }
            50%       { transform: scale(1.18); opacity: 0.4; }
        }

        /* Particles */
        .particles { position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
        .particle {
            position: absolute; width: 3px; height: 3px;
            background: rgba(255,255,255,0.35); border-radius: 50%;
            animation: floatParticle linear infinite;
        }
        @keyframes floatParticle {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { transform: translateY(-20px) scale(1.5); opacity: 0; }
        }

        /* Auth wrapper */
        .auth-wrapper {
            position: relative; z-index: 10;
            width: 100%; max-width: 460px; padding: 20px;
            animation: slideUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Brand */
        .brand-header { text-align: center; margin-bottom: 28px; }
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
            font-size: 1.5rem; backdrop-filter: blur(8px);
        }
        .brand-tagline { color: rgba(255,255,255,0.6); font-size: 0.88rem; margin-top: 6px; }

        /* Card */
        .auth-card {
            background: rgba(255,255,255,0.09);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 24px;
            padding: 36px 32px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35), inset 0 1px 0 rgba(255,255,255,0.15);
        }

        /* Icon banner */
        .icon-banner { display: flex; justify-content: center; margin-bottom: 20px; }
        .icon-circle {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, rgba(16,185,129,0.3), rgba(52,211,153,0.25));
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #6ee7b7;
            backdrop-filter: blur(8px);
            animation: iconPulse 3s ease-in-out infinite;
        }
        @keyframes iconPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
            50%       { box-shadow: 0 0 0 14px rgba(16,185,129,0.12); }
        }

        .auth-card-title {
            color: white; font-size: 1.25rem; font-weight: 700;
            text-align: center; margin-bottom: 6px;
        }
        .auth-card-desc {
            color: rgba(255,255,255,0.5); font-size: 0.87rem;
            text-align: center; margin-bottom: 28px; line-height: 1.6;
        }

        /* Password strength bar */
        .strength-bar-wrap { margin-top: 8px; }
        .strength-bar {
            height: 4px; border-radius: 2px;
            background: rgba(255,255,255,0.1);
            overflow: hidden; margin-bottom: 4px;
        }
        .strength-fill {
            height: 100%; width: 0; border-radius: 2px;
            transition: width 0.4s ease, background 0.4s ease;
        }
        .strength-label { font-size: 0.75rem; color: rgba(255,255,255,0.45); }

        /* Form */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; color: rgba(255,255,255,0.75);
            font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;
        }
        .input-wrap { position: relative; display: flex; align-items: center; }
        .input-icon {
            position: absolute; right: 16px;
            color: rgba(255,255,255,0.4); font-size: 1rem;
            pointer-events: none; transition: color 0.2s;
        }
        .toggle-pw {
            position: absolute; right: 14px;
            background: none; border: none; cursor: pointer;
            color: rgba(255,255,255,0.4); font-size: 1rem;
            padding: 0; transition: color 0.2s;
        }
        .toggle-pw:hover { color: rgba(255,255,255,0.8); }

        .form-control {
            width: 100%; padding: 13px 44px 13px 16px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 12px; font-size: 0.95rem;
            font-family: 'Outfit', sans-serif; color: white;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.35); }
        .form-control:focus {
            outline: none;
            border-color: rgba(110,231,183,0.6);
            background: rgba(255,255,255,0.13);
            box-shadow: 0 0 0 3px rgba(16,185,129,0.25);
        }
        .form-control.match { border-color: rgba(110,231,183,0.6); }
        .form-control.no-match { border-color: rgba(252,165,165,0.6); }

        .invalid-feedback {
            color: #fca5a5; font-size: 0.8rem; margin-top: 6px;
            display: flex; align-items: center; gap: 5px;
        }
        .match-msg {
            font-size: 0.8rem; margin-top: 6px;
            display: flex; align-items: center; gap: 5px;
        }
        .match-msg.ok { color: #6ee7b7; }
        .match-msg.fail { color: #fca5a5; }

        /* Button */
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #059669, #10b981);
            color: white; border: none; padding: 14px; border-radius: 12px;
            font-size: 1rem; font-weight: 700; font-family: 'Outfit', sans-serif;
            cursor: pointer; transition: transform 0.25s, box-shadow 0.25s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-top: 6px; letter-spacing: 0.3px;
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(16,185,129,0.45);
        }
        .btn-submit:active { transform: translateY(-1px); }

        /* Footer */
        .auth-footer {
            text-align: center; margin-top: 20px;
            font-size: 0.88rem; color: rgba(255,255,255,0.55);
        }
        .auth-footer a {
            color: #a5b4fc; text-decoration: none;
            font-weight: 600; transition: color 0.2s;
        }
        .auth-footer a:hover { color: #c7d2fe; }

        @media (max-width: 480px) {
            .auth-card { padding: 28px 20px; }
        }
    </style>
</head>
<body>

    <!-- Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Particles -->
    <div class="particles" id="particles"></div>

    <div class="auth-wrapper">

        <!-- Brand -->
        <div class="brand-header">
            <a href="/">
                <div class="brand-icon-wrap">
                    <i class="bi bi-hexagon-fill" style="color:#a5b4fc;"></i>
                </div>
                ChamaHub
            </a>
            <p class="brand-tagline">Digital Chama Management Platform</p>
        </div>

        <div class="auth-card">

            <!-- Green lock icon -->
            <div class="icon-banner">
                <div class="icon-circle">
                    <i class="bi bi-key-fill"></i>
                </div>
            </div>

            <h1 class="auth-card-title">Set New Password</h1>
            <p class="auth-card-desc">
                Choose a strong new password for your ChamaHub account.
            </p>

            <form method="POST" action="{{ route('password.store') }}" id="resetForm">
                @csrf

                <!-- Hidden token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="bi bi-envelope" style="margin-right:5px;"></i> Email Address
                    </label>
                    <div class="input-wrap">
                        <input id="email" type="email" name="email"
                               class="form-control"
                               value="{{ old('email', $request->email) }}"
                               placeholder="your@email.com"
                               required autofocus autocomplete="username">
                        <i class="bi bi-envelope-fill input-icon"></i>
                    </div>
                    @error('email')
                        <span class="invalid-feedback">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="bi bi-lock" style="margin-right:5px;"></i> New Password
                    </label>
                    <div class="input-wrap">
                        <input id="password" type="password" name="password"
                               class="form-control"
                               placeholder="Min. 8 characters"
                               required autocomplete="new-password"
                               oninput="checkStrength(this.value); checkMatch()">
                        <button type="button" class="toggle-pw" onclick="togglePw('password', this)">
                            <i class="bi bi-eye-slash-fill"></i>
                        </button>
                    </div>
                    <!-- Strength bar -->
                    <div class="strength-bar-wrap">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="strength-label" id="strengthLabel">Enter a password</span>
                    </div>
                    @error('password')
                        <span class="invalid-feedback">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">
                        <i class="bi bi-shield-check" style="margin-right:5px;"></i> Confirm Password
                    </label>
                    <div class="input-wrap">
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Repeat your new password"
                               required autocomplete="new-password"
                               oninput="checkMatch()">
                        <button type="button" class="toggle-pw" onclick="togglePw('password_confirmation', this)">
                            <i class="bi bi-eye-slash-fill"></i>
                        </button>
                    </div>
                    <div id="matchMsg" class="match-msg" style="display:none;"></div>
                    @error('password_confirmation')
                        <span class="invalid-feedback">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="bi bi-check-circle-fill"></i>
                    Update Password
                </button>
            </form>
        </div>

        <div class="auth-footer">
            <a href="{{ route('login') }}">
                <i class="bi bi-arrow-left" style="margin-right:4px;"></i> Back to Sign In
            </a>
        </div>

    </div>

    <script>
        // Particles
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

        // Toggle password visibility
        function togglePw(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon  = btn.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'bi bi-eye-fill';
            } else {
                field.type = 'password';
                icon.className = 'bi bi-eye-slash-fill';
            }
        }

        // Password strength checker
        function checkStrength(val) {
            const fill  = document.getElementById('strengthFill');
            const label = document.getElementById('strengthLabel');
            let score = 0;
            if (val.length >= 8)  score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { pct: '0%',   color: 'transparent',                label: 'Enter a password' },
                { pct: '25%',  color: '#ef4444',                    label: 'Weak' },
                { pct: '50%',  color: '#f97316',                    label: 'Fair' },
                { pct: '75%',  color: '#eab308',                    label: 'Good' },
                { pct: '100%', color: '#10b981',                    label: 'Strong 💪' },
            ];

            const lvl = val.length === 0 ? levels[0] : levels[score] || levels[1];
            fill.style.width      = lvl.pct;
            fill.style.background = lvl.color;
            label.textContent     = val.length === 0 ? 'Enter a password' : lvl.label;
            label.style.color     = val.length === 0 ? 'rgba(255,255,255,0.4)' : lvl.color;
        }

        // Match checker
        function checkMatch() {
            const pw      = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            const msg     = document.getElementById('matchMsg');
            const confEl  = document.getElementById('password_confirmation');

            if (confirm.length === 0) {
                msg.style.display = 'none';
                confEl.classList.remove('match', 'no-match');
                return;
            }

            msg.style.display = 'flex';
            if (pw === confirm) {
                msg.className   = 'match-msg ok';
                msg.innerHTML   = '<i class="bi bi-check-circle-fill"></i> Passwords match';
                confEl.classList.add('match');
                confEl.classList.remove('no-match');
            } else {
                msg.className   = 'match-msg fail';
                msg.innerHTML   = '<i class="bi bi-x-circle-fill"></i> Passwords do not match';
                confEl.classList.add('no-match');
                confEl.classList.remove('match');
            }
        }
    </script>
</body>
</html>
