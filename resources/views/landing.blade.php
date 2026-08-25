<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ChamaHub — The all-in-one digital platform for managing Chama savings, loans, and meetings in Kenya.">
    <meta name="theme-color" content="#4f46e5">
    <link rel="manifest" href="/manifest.json">
    <title>ChamaHub — Digital Chama Management</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script>
        (function() {
            if (localStorage.getItem('chamahub_dark_mode') === 'true') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --secondary: #7c3aed;
            --accent: #fde68a;
            --dark: #0f0e17;
            --text: #1e293b;
            --muted: #64748b;
            --bg-light: #f8fafc;
            --bg-body: white;
            --bg-card: white;
            --nav-bg: rgba(255,255,255,0.9);
            --border-color: #e2e8f0;
        }

        html.dark-mode {
            --text: #f1f5f9;
            --muted: #94a3b8;
            --bg-light: #0f172a;
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --nav-bg: rgba(15,23,42,0.9);
            --border-color: #334155;
        }

        html, body {
            overflow-x: hidden;
            width: 100%;
            max-width: 100%;
        }

        body {
            font-family: 'Outfit', sans-serif;
            color: var(--text);
            background: var(--bg-body);
            transition: background 0.3s ease, color 0.3s ease;
        }

        /* ─── NAVBAR ─── */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            padding: 1.25rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--nav-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            transition: box-shadow 0.3s, background 0.3s;
        }

        nav.scrolled { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .nav-logo-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .nav-logo-text {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--muted);
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .nav-links a:hover { color: var(--primary); }

        .nav-cta {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn-ghost {
            padding: 0.6rem 1.2rem;
            border: 2px solid var(--primary);
            border-radius: 8px;
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-ghost:hover {
            background: var(--primary);
            color: white;
        }

        .btn-solid {
            padding: 0.6rem 1.4rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 8px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .btn-solid:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79,70,229,0.35);
        }

        /* ─── HERO ─── */
        #home {
            min-height: 100vh;
            background: linear-gradient(160deg, #0f0e17 0%, #1e1b4b 40%, #312e81 70%, #4f46e5 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            padding: 8rem 5% 5rem;
        }

        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
        }

        .orb1 { width: 500px; height: 500px; background: #7c3aed; top: -100px; right: -100px; animation: pulse 8s ease-in-out infinite; }
        .orb2 { width: 400px; height: 400px; background: #4f46e5; bottom: -150px; left: 10%; animation: pulse 10s ease-in-out infinite reverse; }
        .orb3 { width: 300px; height: 300px; background: #a855f7; top: 30%; right: 20%; animation: pulse 12s ease-in-out infinite; }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.15); opacity: 0.45; }
        }

        /* ─── SUBTLE HERO BACKGROUND VIDEO ─── */
        .hero-video-bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
        }

        .hero-video-bg video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.22; /* Subtle opacity for high readability */
            filter: brightness(0.75) contrast(1.15) saturate(1.2);
        }

        .hero-video-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(15,14,23,0.85) 0%, rgba(30,27,75,0.72) 40%, rgba(49,46,129,0.7) 70%, rgba(79,70,229,0.82) 100%);
            z-index: 2;
        }

        .hero-content {
            position: relative;
            z-index: 3;
            max-width: 650px;
            flex: 1;
        }

        .hero-visual {
            flex: 1;
            position: relative;
            z-index: 3;
            height: 500px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .floating-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transition: translate 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            translate: 0px 0px;
        }

        .floating-element i {
            background: linear-gradient(135deg, #fde68a, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));
        }

        .coin-1 {
            width: 120px; height: 120px;
            top: 20%; left: 10%;
            animation: float-up-down 6s ease-in-out infinite;
        }
        .coin-1 i { font-size: 4rem; }

        .cash-1 {
            width: 160px; height: 120px;
            top: 50%; right: 5%;
            animation: float-up-down 8s ease-in-out infinite reverse;
        }
        .cash-1 i { font-size: 5rem; color: #a7f3d0; background: none; -webkit-text-fill-color: #a7f3d0; }

        .bank-1 {
            width: 140px; height: 140px;
            bottom: 10%; left: 30%;
            animation: float-up-down 7s ease-in-out infinite 1s;
        }
        .bank-1 i { font-size: 4.5rem; }

        .coin-2 {
            width: 80px; height: 80px;
            top: 10%; right: 30%;
            animation: float-up-down 5s ease-in-out infinite 2s;
            border-radius: 50%;
        }
        .coin-2 i { font-size: 2.5rem; }

        @keyframes float-up-down {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50px;
            padding: 0.4rem 1rem;
            color: var(--accent);
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4.2rem);
            font-weight: 900;
            color: white;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }

        .hero-title span {
            background: linear-gradient(135deg, #fde68a, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-sub {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.75);
            line-height: 1.8;
            margin-bottom: 3rem;
            max-width: 500px;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 4rem;
        }

        .btn-hero-primary {
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #fde68a, #f59e0b);
            color: #1e293b;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s;
            display: flex; align-items: center; gap: 8px;
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(245,158,11,0.4);
        }

        .btn-hero-secondary {
            padding: 1rem 2rem;
            border: 2px solid rgba(255,255,255,0.4);
            color: white;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s;
            display: flex; align-items: center; gap: 8px;
            backdrop-filter: blur(10px);
        }

        .btn-hero-secondary:hover {
            background: rgba(255,255,255,0.15);
            border-color: white;
        }

        .hero-stats {
            display: flex;
            gap: 3rem;
            flex-wrap: wrap;
        }

        .stat-item h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: white;
        }

        .stat-item p {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
        }

        /* ─── SECTIONS SHARED ─── */
        section { padding: 6rem 5%; }

        .section-label {
            display: inline-block;
            color: var(--primary);
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            line-height: 1.2;
            color: var(--text);
            margin-bottom: 1.25rem;
            letter-spacing: -0.5px;
        }

        .section-sub {
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.8;
            max-width: 550px;
        }

        .text-center { text-align: center; }
        .text-center .section-sub { margin: 0 auto; }

        /* ─── FEATURES ─── */
        #features { background: var(--bg-light); }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .feature-card {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            border: 1px solid var(--border-color);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(79,70,229,0.12);
            border-color: var(--primary);
        }

        .feature-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, rgba(79,70,229,0.1), rgba(124,58,237,0.1));
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.75rem;
        }

        .feature-card p {
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        /* ─── HOW IT WORKS ─── */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 3rem;
            margin-top: 4rem;
            position: relative;
        }

        .step-item {
            text-align: center;
        }

        .step-number {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            font-weight: 900;
            color: white;
            margin: 0 auto 1.5rem;
        }

        .step-item h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .step-item p {
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.7;
        }

        /* ─── ABOUT ─── */
        #about {
            background: var(--dark);
            color: white;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
            align-items: center;
        }

        .about-grid .section-title { color: white; }
        .about-grid .section-sub { color: rgba(255,255,255,0.65); }

        .about-points {
            list-style: none;
            margin-top: 2rem;
        }

        .about-points li {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 1.25rem;
            color: rgba(255,255,255,0.8);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .about-points li i {
            color: #a7f3d0;
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-top: 3px;
        }

        .about-visual {
            background: linear-gradient(135deg, rgba(79,70,229,0.2), rgba(124,58,237,0.2));
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 3rem;
            backdrop-filter: blur(10px);
        }

        .about-stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .about-stat {
            text-align: center;
            padding: 1.5rem;
            background: rgba(255,255,255,0.05);
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.08);
        }

        .about-stat h2 {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #fde68a, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .about-stat p {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        /* ─── TESTIMONIALS ─── */
        #testimonials { background: var(--bg-light); }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .testimonial-card {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }

        .testimonial-card:hover { transform: translateY(-5px); }

        .quote-icon {
            font-size: 2.5rem;
            color: var(--primary);
            opacity: 0.3;
            margin-bottom: 1rem;
        }

        .testimonial-card p {
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .author-avatar {
            width: 48px; height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex; align-items: center; justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
        }

        .author-info h4 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text);
        }

        .author-info p {
            font-size: 0.8rem;
            color: var(--muted);
            margin: 0;
        }

        /* ─── CTA ─── */
        #contact {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            text-align: center;
            padding: 7rem 5%;
        }

        #contact h2 {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 900;
            color: white;
            margin-bottom: 1rem;
        }

        #contact p {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
            margin-bottom: 3rem;
        }

        .btn-cta {
            padding: 1.1rem 2.5rem;
            background: var(--bg-card);
            color: var(--primary);
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
        }

        /* ─── FOOTER ─── */
        footer {
            background: var(--dark);
            color: rgba(255,255,255,0.5);
            text-align: center;
            padding: 2rem 5%;
            font-size: 0.85rem;
        }

        footer strong { color: white; }

        /* ─── RESPONSIVE ─── */
        html, body {
            overflow-x: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        #home, #features, #how-it-works, #about, #testimonials, #contact, footer, nav {
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        @media (max-width: 992px) {
            #home { flex-direction: column; padding-top: 8rem; padding-bottom: 4rem; }
            .hero-visual { display: none; }
            .hero-content { max-width: 100%; text-align: center; }
            .hero-stats { justify-content: center; }
            .hero-buttons { justify-content: center; }
        }

        @media (max-width: 768px) {
            nav { padding: 0.75rem 4% !important; }
            .nav-logo-text { font-size: 1.1rem !important; }
            .nav-logo-icon { width: 34px !important; height: 34px !important; font-size: 1rem !important; }
            .nav-links { display: none; }
            .nav-cta { gap: 0.4rem !important; }
            .btn-ghost, .btn-solid { padding: 0.45rem 0.75rem !important; font-size: 0.82rem !important; }
            
            .hero-badge {
                font-size: 0.75rem !important;
                padding: 0.4rem 0.75rem !important;
                max-width: 100% !important;
                white-space: normal !important;
                text-align: center !important;
                display: inline-flex !important;
            }

            .hero-title { font-size: 2.1rem !important; }
            .hero-sub { font-size: 0.95rem !important; margin-bottom: 2rem !important; }
            .btn-hero-primary, .btn-hero-secondary { padding: 0.75rem 1.25rem !important; font-size: 0.9rem !important; }

            .about-grid { grid-template-columns: 1fr; gap: 2.5rem; }
            .about-stat-grid { grid-template-columns: 1fr !important; }
            .features-grid { grid-template-columns: 1fr !important; }
            .testimonials-grid { grid-template-columns: 1fr !important; }
            .steps-grid { grid-template-columns: 1fr !important; gap: 2rem !important; }
            .feature-card, .testimonial-card { padding: 1.5rem !important; }
            .hero-stats { gap: 1.5rem; }
        }

        @media (max-width: 480px) {
            .nav-logo-text { font-size: 0.95rem !important; }
            .btn-ghost, .btn-solid { padding: 0.35rem 0.6rem !important; font-size: 0.75rem !important; }
            .btn-cta { padding: 0.85rem 1.5rem !important; font-size: 0.9rem !important; }
        }
    </style>
</head>
<body>

<!-- ─── NAVBAR ─── -->
<nav id="navbar">
    <a href="#home" class="nav-logo">
        <div class="nav-logo-icon"><i class="bi bi-hexagon-fill"></i></div>
        <span class="nav-logo-text">ChamaHub</span>
    </a>

    <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="#features">Features</a></li>
        <li><a href="#how-it-works">How It Works</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#contact">Contact</a></li>
        <li>
            <button id="themeToggle" style="background:none; border:none; color:var(--muted); cursor:pointer; font-size:1.1rem; padding-top:4px;" title="Toggle Dark Mode">
                <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
            </button>
        </li>
    </ul>

    <div class="nav-cta">
        @auth
            <a href="{{ url('/dashboard') }}" class="btn-solid">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline-block; margin:0;">
                @csrf
                <button type="submit" class="btn-ghost" style="font-family:inherit; cursor:pointer; padding:0.55rem 1rem; border-radius:8px;">Sign Out</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn-ghost">Login</a>
            <a href="{{ route('register') }}" class="btn-solid">Get Started</a>
        @endauth
    </div>
</nav>

<!-- ─── HERO ─── -->
<section id="home">
    <!-- Subtle Background Video (People sharing money / Chama financial growth) -->
    <div class="hero-video-bg">
        <video autoplay loop muted playsinline poster="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=1920&q=80">
            <source src="https://assets.mixkit.co/videos/preview/mixkit-hands-counting-and-sharing-money-42880-large.mp4" type="video/mp4">
            <source src="https://cdn.coverr.co/videos/coverr-hands-counting-money-5271/1080p.mp4" type="video/mp4">
        </video>
        <div class="hero-video-overlay"></div>
    </div>

    <div class="hero-orb orb1"></div>
    <div class="hero-orb orb2"></div>
    <div class="hero-orb orb3"></div>

    <div class="hero-content">
        <div class="hero-badge">
            <i class="bi bi-shield-check-fill"></i>
            Built for Kenyan Chamas — Powered by M-Pesa
        </div>

        <h1 class="hero-title">
            Manage Your<br>
            Chama <span>Smarter.</span><br>
            Grow Together.
        </h1>

        <p class="hero-sub">
            ChamaHub is a modern digital platform that simplifies contributions, loan management, meeting scheduling, and financial reporting — all in one place.
        </p>

        <div class="hero-buttons">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-hero-primary">
                    <i class="bi bi-speedometer2"></i> Go to Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="btn-hero-secondary" style="border:none; cursor:pointer; font-family:inherit;">
                        <i class="bi bi-box-arrow-right"></i> Sign Out
                    </button>
                </form>
            @else
                <a href="{{ route('register') }}" class="btn-hero-primary">
                    <i class="bi bi-rocket-takeoff-fill"></i> Start for Free
                </a>
                <a href="#features" class="btn-hero-secondary">
                    <i class="bi bi-play-circle-fill"></i> Learn More
                </a>
            @endauth
        </div>

        <div class="hero-stats">
            <div class="stat-item">
                <h3>100%</h3>
                <p>Transparent Records</p>
            </div>
            <div class="stat-item">
                <h3>M-Pesa</h3>
                <p>Integrated Payments</p>
            </div>
            <div class="stat-item">
                <h3>Real-Time</h3>
                <p>Financial Reports</p>
            </div>
        </div>
    </div>

    <!-- Floating Money Visuals -->
    <div class="hero-visual">
        <div class="floating-element coin-1">
            <i class="bi bi-coin"></i>
        </div>
        <div class="floating-element cash-1">
            <i class="bi bi-cash-stack"></i>
        </div>
        <div class="floating-element bank-1">
            <i class="bi bi-piggy-bank-fill"></i>
        </div>
        <div class="floating-element coin-2">
            <i class="bi bi-coin"></i>
        </div>
    </div>
</section>

<!-- ─── FEATURES ─── -->
<section id="features">
    <div class="text-center">
        <span class="section-label">Platform Features</span>
        <h2 class="section-title">Everything your Chama needs</h2>
        <p class="section-sub">From contributions to loan approvals — ChamaHub handles the complexity so your group can focus on growing.</p>
    </div>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">💳</div>
            <h3>Contribution Tracking</h3>
            <p>Record monthly contributions per member with real-time status (paid, partial, unpaid). Full payment history is always available.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📱</div>
            <h3>M-Pesa Integration</h3>
            <p>Members pay directly via M-Pesa STK Push. Payments are automatically matched and contribution records are updated instantly.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🏦</div>
            <h3>Loan Management</h3>
            <p>Submit, review, approve or reject loan applications with full interest calculation, repayment scheduling, and status tracking.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📅</div>
            <h3>Meeting Scheduling</h3>
            <p>Schedule group meetings, share the agenda beforehand, and record meeting minutes afterwards — all in one system.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Financial Reports</h3>
            <p>Generate real-time visual reports showing collection rates, loan performance, and overall group financial health.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔔</div>
            <h3>Smart Notifications</h3>
            <p>Automatic email reminders for unpaid contributions, loan approval alerts, and meeting notifications sent directly to members.</p>
        </div>
    </div>
</section>

<!-- ─── HOW IT WORKS ─── -->
<section id="how-it-works">
    <div class="text-center">
        <span class="section-label">How It Works</span>
        <h2 class="section-title">Get your Chama online in minutes</h2>
        <p class="section-sub">ChamaHub is designed to be simple for both admins and regular members.</p>
    </div>

    <div class="steps-grid">
        <div class="step-item">
            <div class="step-number">1</div>
            <h3>Register Your Group</h3>
            <p>The admin creates an account and sets up the ChamaHub workspace for your investment group.</p>
        </div>
        <div class="step-item">
            <div class="step-number">2</div>
            <h3>Add Members</h3>
            <p>Invite members by registering them with their name, email, and M-Pesa phone number.</p>
        </div>
        <div class="step-item">
            <div class="step-number">3</div>
            <h3>Track Contributions</h3>
            <p>Members pay via M-Pesa and records are updated automatically. No manual entry needed.</p>
        </div>
        <div class="step-item">
            <div class="step-number">4</div>
            <h3>Manage & Grow</h3>
            <p>Approve loans, schedule meetings, generate reports, and watch your chama's wealth grow.</p>
        </div>
    </div>
</section>

<!-- ─── ABOUT ─── -->
<section id="about">
    <div class="about-grid">
        <div>
            <span class="section-label" style="color: #a7f3d0;">About ChamaHub</span>
            <h2 class="section-title">Built for the Kenyan Community</h2>
            <p class="section-sub">ChamaHub was born from a real need — many chama groups were still managing their finances in WhatsApp groups and paper notebooks. We built a digital solution that respects the culture of community savings while bringing it into the modern age.</p>

            <ul class="about-points">
                <li><i class="bi bi-check-circle-fill"></i> Developed using Laravel, a world-class PHP framework trusted by enterprises globally.</li>
                <li><i class="bi bi-check-circle-fill"></i> Payments processed via Safaricom M-Pesa Daraja API — the most trusted payment platform in Kenya.</li>
                <li><i class="bi bi-check-circle-fill"></i> Role-based access: Admin, Treasurer, and Member roles with tailored dashboards.</li>
                <li><i class="bi bi-check-circle-fill"></i> Automated email notifications powered by Laravel's queuing system.</li>
                <li><i class="bi bi-check-circle-fill"></i> Data stored securely in a structured MySQL relational database.</li>
            </ul>
        </div>

        <div class="about-visual">
            <div class="about-stat-grid">
                <div class="about-stat">
                    <h2>100%</h2>
                    <p>Open Records</p>
                </div>
                <div class="about-stat">
                    <h2>M-Pesa</h2>
                    <p>Payment Gateway</p>
                </div>
                <div class="about-stat">
                    <h2>Laravel</h2>
                    <p>Powered Backend</p>
                </div>
                <div class="about-stat">
                    <h2>Secure</h2>
                    <p>Encrypted Data</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── TESTIMONIALS ─── -->
<section id="testimonials">
    <div class="text-center">
        <span class="section-label">Testimonials</span>
        <h2 class="section-title">Groups love ChamaHub</h2>
        <p class="section-sub">Hear what chama members and admins are saying about the platform.</p>
    </div>

    <div class="testimonials-grid">
        <div class="testimonial-card">
            <div class="quote-icon">"</div>
            <p>Before ChamaHub, we tracked everything in a notebook. Now we can see who has paid, who owes, and our loans are managed automatically. It has transformed our group!</p>
            <div class="testimonial-author">
                <img class="author-avatar" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Amina Kariuki" style="object-fit: cover;">
                <div class="author-info">
                    <h4>Amina Kariuki</h4>
                    <p style="margin-bottom: 2px;">Chama Treasurer</p>
                    <small style="color: var(--primary); font-weight: 700;">Group: Umoja Investment</small>
                </div>
            </div>
        </div>
        <div class="testimonial-card">
            <div class="quote-icon">"</div>
            <p>The M-Pesa integration is absolutely seamless. I get an STK Push on my phone, I enter my PIN, and my contribution is marked paid within seconds. No more manual transfers!</p>
            <div class="testimonial-author">
                <img class="author-avatar" src="https://randomuser.me/api/portraits/men/32.jpg" alt="James Omondi" style="object-fit: cover;">
                <div class="author-info">
                    <h4>James Omondi</h4>
                    <p style="margin-bottom: 2px;">Group Member</p>
                    <small style="color: var(--primary); font-weight: 700;">Group: Vision 2030 Chama</small>
                </div>
            </div>
        </div>
        <div class="testimonial-card">
            <div class="quote-icon">"</div>
            <p>As the admin of a 30-member chama, I used to spend hours reconciling payments. ChamaHub reduced that to minutes. The reports feature is world-class!</p>
            <div class="testimonial-author">
                <img class="author-avatar" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Faith Wanjiku" style="object-fit: cover;">
                <div class="author-info">
                    <h4>Faith Wanjiku</h4>
                    <p style="margin-bottom: 2px;">Chama Chairperson</p>
                    <small style="color: var(--primary); font-weight: 700;">Group: Baraka Women Group</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── CTA ─── -->
<section id="contact">
    <h2>Ready to digitize your Chama?</h2>
    <p>Join ChamaHub today and bring your group's finances into the 21st century — for free.</p>
    @auth
        <a href="{{ url('/dashboard') }}" class="btn-cta">
            <i class="bi bi-speedometer2"></i> Go to Dashboard
        </a>
    @else
        <a href="{{ route('register') }}" class="btn-cta">
            <i class="bi bi-rocket-takeoff-fill"></i> Create Free Account
        </a>
    @endauth
</section>

<!-- ─── FOOTER ─── -->
<footer>
    <p>&copy; {{ date('Y') }} <strong>ChamaHub</strong>. Built with ❤️ using Laravel &amp; M-Pesa Daraja API. All rights reserved.</p>
</footer>

<script>
    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 30);
    });

    // Smooth reveal on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.feature-card, .step-item, .testimonial-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });

    // Dark mode toggle logic
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const htmlRoot = document.documentElement;

    function updateThemeIcon(isDark) {
        if(themeIcon) {
            themeIcon.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
        }
    }

    updateThemeIcon(htmlRoot.classList.contains('dark-mode'));

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const isDark = htmlRoot.classList.toggle('dark-mode');
            localStorage.setItem('chamahub_dark_mode', isDark);
            updateThemeIcon(isDark);
        });
    }

    // Floating icons dodge effect
    const floatingElements = document.querySelectorAll('.floating-element');
    document.addEventListener('mousemove', (e) => {
        floatingElements.forEach(el => {
            const rect = el.getBoundingClientRect();
            // Center of the element
            const elCenterX = rect.left + rect.width / 2;
            const elCenterY = rect.top + rect.height / 2;
            
            // Distance between mouse and center
            const distX = e.clientX - elCenterX;
            const distY = e.clientY - elCenterY;
            const distance = Math.sqrt(distX * distX + distY * distY);
            
            const safeDistance = 150; // Distance at which they start dodging
            
            if (distance < safeDistance) {
                // Calculate push away
                const pushFactor = (safeDistance - distance) / safeDistance;
                const pushX = -(distX / distance) * pushFactor * 100;
                const pushY = -(distY / distance) * pushFactor * 100;
                
                el.style.translate = `${pushX}px ${pushY}px`;
            } else {
                el.style.translate = `0px 0px`;
            }
        });
    });
</script>

    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('SW registered: ', registration.scope);
                }).catch(err => console.log('SW registration failed: ', err));
            });
        }
    </script>
</body>
</html>
