<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UniBridge — Your Global Education Journey Starts Here</title>

    <meta
        name="description"
        content="UniBridge helps students discover universities, check eligibility, explore study destinations and connect with education consultants."
    >

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|manrope:400,500,600,700,800"
        rel="stylesheet"
    >

    <style>
        :root {
            --navy: #0b1736;
            --blue: #2563eb;
            --blue-dark: #1d4ed8;
            --red: #ef4444;
            --text: #14213d;
            --muted: #64748b;
            --light: #f5f8fc;
            --border: #e5eaf2;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            color: var(--text);
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .container {
            width: min(1180px, calc(100% - 40px));
            margin: auto;
        }

        /* NAVBAR */

        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255,255,255,.96);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid #edf0f5;
        }

        .nav-inner {
            height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            font-family: 'Manrope', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: var(--navy);
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            display: grid;
            place-items: center;
            color: white;
            font-size: 21px;
            background: linear-gradient(135deg, #2563eb, #0b1736);
            box-shadow: 0 8px 20px rgba(37,99,235,.22);
        }

        .brand span {
            color: var(--blue);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 27px;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
        }

        .nav-links a:hover {
            color: var(--blue);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            border: 0;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            transition: .25s ease;
        }

        .btn-primary {
            background: var(--blue);
            color: white;
            box-shadow: 0 8px 20px rgba(37,99,235,.18);
        }

        .btn-primary:hover {
            background: var(--blue-dark);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: white;
            color: var(--navy);
            border: 1px solid #d9e0eb;
        }

        .btn-outline:hover {
            border-color: var(--blue);
            color: var(--blue);
        }

        /* HERO */

        .hero {
            position: relative;
            overflow: hidden;
            padding: 80px 0 90px;
            background:
                radial-gradient(circle at 85% 15%, rgba(37,99,235,.13), transparent 28%),
                radial-gradient(circle at 10% 80%, rgba(239,68,68,.07), transparent 25%),
                #f7faff;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.08fr .92fr;
            gap: 65px;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 13px;
            border-radius: 999px;
            background: white;
            border: 1px solid #dce6f5;
            color: var(--blue);
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .hero h1 {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            font-size: clamp(42px, 5vw, 67px);
            line-height: 1.03;
            letter-spacing: -2.8px;
            color: var(--navy);
        }

        .hero h1 span {
            color: var(--blue);
        }

        .hero-text {
            max-width: 640px;
            margin: 23px 0 30px;
            color: #64748b;
            font-size: 17px;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .hero-trust {
            margin-top: 28px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
        }

        .hero-trust span {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .check {
            color: #16a34a;
            font-weight: 900;
        }

        /* SEARCH CARD */

        .search-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 25px 70px rgba(15,23,42,.13);
            border: 1px solid #e8edf5;
        }

        .search-card h3 {
            margin: 0 0 7px;
            font-size: 23px;
            color: var(--navy);
        }

        .search-card p {
            margin: 0 0 22px;
            color: var(--muted);
            font-size: 14px;
        }

        .search-field {
            margin-bottom: 14px;
        }

        .search-field label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 7px;
            color: #475569;
        }

        .search-field select {
            width: 100%;
            border: 1px solid #dce3ed;
            border-radius: 10px;
            padding: 13px;
            background: #fbfcfe;
            font-family: inherit;
            color: #334155;
            outline: none;
        }

        .search-field select:focus {
            border-color: var(--blue);
        }

        .search-submit {
            width: 100%;
            margin-top: 7px;
        }

        .search-note {
            text-align: center;
            margin-top: 14px !important;
            margin-bottom: 0 !important;
            font-size: 11px !important;
        }

        /* STATS */

        .stats {
            background: var(--navy);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }

        .stat {
            padding: 28px 20px;
            text-align: center;
            border-right: 1px solid rgba(255,255,255,.1);
        }

        .stat:last-child {
            border-right: 0;
        }

        .stat strong {
            display: block;
            font-size: 29px;
            margin-bottom: 5px;
        }

        .stat span {
            color: #b9c4da;
            font-size: 13px;
        }

        /* SECTION */

        .section {
            padding: 90px 0;
        }

        .section-light {
            background: var(--light);
        }

        .section-heading {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 48px;
        }

        .eyebrow {
            color: var(--blue);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 11px;
        }

        .section-heading h2 {
            margin: 0 0 13px;
            font-family: 'Manrope', sans-serif;
            font-size: 38px;
            line-height: 1.15;
            color: var(--navy);
        }

        .section-heading p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
            font-size: 15px;
        }

        /* DESTINATIONS */

        .destination-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }

        .destination {
            min-height: 175px;
            border-radius: 18px;
            padding: 23px;
            background: white;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
            transition: .25s ease;
        }

        .destination:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 40px rgba(15,23,42,.09);
            border-color: #bfd3f8;
        }

        .destination-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: #eff6ff;
            font-size: 24px;
            margin-bottom: 18px;
        }

        .destination h3 {
            margin: 0 0 5px;
            color: var(--navy);
            font-size: 18px;
        }

        .destination p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
        }

        /* SERVICES */

        .service-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .service {
            background: white;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 28px;
            transition: .25s ease;
        }

        .service:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 45px rgba(15,23,42,.08);
        }

        .service-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: #eff6ff;
            display: grid;
            place-items: center;
            font-size: 24px;
            margin-bottom: 19px;
        }

        .service h3 {
            margin: 0 0 9px;
            color: var(--navy);
            font-size: 18px;
        }

        .service p {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
            margin: 0;
        }

        /* FINDER */

        .finder {
            background: linear-gradient(135deg, #0b1736, #162d63);
            border-radius: 28px;
            padding: 48px;
            color: white;
            display: grid;
            grid-template-columns: 1fr .8fr;
            gap: 50px;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        .finder:after {
            content: "";
            position: absolute;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: rgba(37,99,235,.18);
            right: -100px;
            top: -100px;
        }

        .finder h2 {
            font-family: 'Manrope', sans-serif;
            font-size: 38px;
            line-height: 1.15;
            margin: 0 0 15px;
        }

        .finder p {
            color: #c5d0e5;
            line-height: 1.7;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .finder-list {
            display: grid;
            gap: 12px;
        }

        .finder-list div {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #e8eef9;
            font-size: 13px;
        }

        .finder-box {
            background: white;
            color: var(--text);
            padding: 26px;
            border-radius: 19px;
            position: relative;
            z-index: 2;
        }

        .finder-box h3 {
            margin: 0 0 18px;
            color: var(--navy);
        }

        .finder-result {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 13px;
            border: 1px solid #e6ebf2;
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .university-logo {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: #eff6ff;
            font-size: 18px;
        }

        .finder-result strong {
            display: block;
            font-size: 13px;
        }

        .finder-result small {
            color: var(--muted);
            font-size: 11px;
        }

        /* HOW IT WORKS */

        .steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .step {
            position: relative;
            padding: 27px;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: white;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: var(--blue);
            color: white;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .step h3 {
            margin: 0 0 8px;
            font-size: 17px;
            color: var(--navy);
        }

        .step p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.65;
        }

        /* UNIVERSITY CARDS */

        .university-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .university-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            transition: .25s ease;
        }

        .university-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 45px rgba(15,23,42,.09);
        }

        .university-top {
            padding: 28px;
            background: linear-gradient(135deg,#eff6ff,#f8fbff);
        }

        .university-top .uni-symbol {
            width: 58px;
            height: 58px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            background: white;
            font-size: 27px;
            box-shadow: 0 8px 20px rgba(15,23,42,.07);
            margin-bottom: 18px;
        }

        .university-top h3 {
            margin: 0 0 5px;
            color: var(--navy);
            font-size: 19px;
        }

        .university-top p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
        }

        .university-info {
            padding: 22px 28px;
        }

        .uni-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 9px 0;
            border-bottom: 1px solid #eef1f5;
            font-size: 12px;
        }

        .uni-row:last-child {
            border-bottom: 0;
        }

        .uni-row span:first-child {
            color: var(--muted);
        }

        .uni-row strong {
            color: var(--navy);
        }

        /* CONSULTANTS */

        .consultant-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .consultant {
            background: white;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 27px;
            text-align: center;
        }

        .consultant-avatar {
            width: 76px;
            height: 76px;
            margin: 0 auto 15px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg,#dbeafe,#eff6ff);
            font-size: 31px;
        }

        .consultant h3 {
            margin: 0 0 5px;
            color: var(--navy);
            font-size: 18px;
        }

        .consultant p {
            margin: 0 0 16px;
            color: var(--muted);
            font-size: 12px;
        }

        .consultant-tag {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 999px;
            background: #eff6ff;
            color: var(--blue);
            font-size: 11px;
            font-weight: 700;
            margin: 3px;
        }

        /* MAP */

        .location-section {
            background: #f7f9fc;
        }

        .location-grid {
            display: grid;
            grid-template-columns: .75fr 1.25fr;
            gap: 25px;
            align-items: stretch;
        }

        .location-info {
            background: var(--navy);
            color: white;
            padding: 38px;
            border-radius: 22px;
        }

        .location-info h2 {
            font-family: 'Manrope', sans-serif;
            font-size: 32px;
            margin: 0 0 13px;
        }

        .location-info > p {
            color: #c2cee4;
            line-height: 1.7;
            font-size: 14px;
            margin-bottom: 28px;
        }

        .location-item {
            display: flex;
            gap: 13px;
            margin-bottom: 22px;
        }

        .location-item-icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            background: rgba(255,255,255,.09);
        }

        .location-item strong {
            display: block;
            margin-bottom: 4px;
            font-size: 13px;
        }

        .location-item span {
            color: #b7c3d9;
            font-size: 12px;
            line-height: 1.5;
        }

        .map-wrap {
            min-height: 430px;
            background: white;
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 15px 40px rgba(15,23,42,.07);
        }

        .map-wrap iframe {
            width: 100%;
            height: 100%;
            min-height: 430px;
            border: 0;
        }

        /* CTA */

        .cta {
            padding: 85px 0;
            background: white;
        }

        .cta-box {
            background: linear-gradient(135deg,#2563eb,#1e40af);
            color: white;
            border-radius: 26px;
            padding: 55px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
        }

        .cta-box h2 {
            margin: 0 0 10px;
            font-family: 'Manrope', sans-serif;
            font-size: 36px;
        }

        .cta-box p {
            margin: 0;
            color: #dbeafe;
            font-size: 14px;
        }

        .cta-box .btn {
            background: white;
            color: var(--blue);
            white-space: nowrap;
        }

        .cta-box .btn:hover {
            transform: translateY(-2px);
        }

        .whatsapp-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            color: #fff;
            background: #16a34a;
            box-shadow: 0 8px 20px rgba(22,163,74,.18);
        }

        .whatsapp-link:hover {
            color: #fff;
            background: #15803d;
            transform: translateY(-2px);
        }

        .whatsapp-float {
            position: fixed;
            right: 22px;
            bottom: 94px;
            z-index: 9998;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 13px 18px;
            border-radius: 999px;
            background: #16a34a;
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 8px 24px rgba(15,23,42,.22);
            transition: .2s ease;
        }

        .whatsapp-float:hover {
            color: #fff;
            background: #15803d;
            transform: translateY(-2px);
        }

        .whatsapp-float svg {
            width: 20px;
            height: 20px;
            flex: 0 0 20px;
        }

        /* FOOTER */

        footer {
            background: #071126;
            color: white;
            padding: 60px 0 25px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr 1fr 1fr;
            gap: 45px;
            padding-bottom: 40px;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }

        .footer-brand {
            font-family: 'Manrope', sans-serif;
            font-size: 23px;
            font-weight: 800;
            margin-bottom: 13px;
        }

        .footer-brand span {
            color: #60a5fa;
        }

        .footer-description {
            color: #9aa8c0;
            font-size: 13px;
            line-height: 1.7;
            max-width: 330px;
        }

        footer h4 {
            margin: 0 0 16px;
            font-size: 13px;
        }

        .footer-links {
            display: grid;
            gap: 10px;
        }

        .footer-links a {
            color: #9aa8c0;
            font-size: 12px;
        }

        .footer-links a:hover {
            color: white;
        }

        .copyright {
            padding-top: 22px;
            color: #71809b;
            font-size: 11px;
            text-align: center;
        }

        /* MOBILE */

        .mobile-menu {
            display: none;
        }

        @media(max-width: 1000px) {

            .nav-links {
                display: none;
            }

            .mobile-menu {
                display: block;
                border: 0;
                background: transparent;
                font-size: 25px;
                cursor: pointer;
            }

            .hero-grid,
            .finder,
            .location-grid {
                grid-template-columns: 1fr;
            }

            .destination-grid {
                grid-template-columns: repeat(2,1fr);
            }

            .service-grid {
                grid-template-columns: repeat(2,1fr);
            }

            .steps {
                grid-template-columns: repeat(2,1fr);
            }

            .university-grid,
            .consultant-grid {
                grid-template-columns: 1fr;
            }

            .footer-grid {
                grid-template-columns: repeat(2,1fr);
            }
        }

        @media(max-width: 650px) {

            .container {
                width: min(100% - 28px, 1180px);
            }

            .nav-inner {
                height: 68px;
            }

            .nav-actions .btn-outline {
                display: none;
            }

            .hero {
                padding: 55px 0 65px;
            }

            .hero h1 {
                font-size: 42px;
                letter-spacing: -1.8px;
            }

            .hero-text {
                font-size: 15px;
            }

            .search-card {
                padding: 23px;
            }

            .stats-grid {
                grid-template-columns: repeat(2,1fr);
            }

            .stat {
                border-bottom: 1px solid rgba(255,255,255,.1);
            }

            .section {
                padding: 65px 0;
            }

            .section-heading h2 {
                font-size: 30px;
            }

            .destination-grid,
            .service-grid,
            .steps {
                grid-template-columns: 1fr;
            }

            .finder {
                padding: 28px;
            }

            .finder h2 {
                font-size: 29px;
            }

            .cta-box {
                padding: 32px;
                flex-direction: column;
                align-items: flex-start;
            }

            .cta-box h2 {
                font-size: 29px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .whatsapp-float {
                right: 14px;
                bottom: 88px;
                padding: 12px 15px;
            }

            .brand {
                font-size: 20px;
            }

            .brand-mark {
                width: 37px;
                height: 37px;
            }
        }
        [x-cloak] {
    display: none !important;
}
    </style>
    @vite(['resources/js/app.js'])
</head>

<body>

<!-- NAVBAR -->
<header class="navbar">
    <div class="container nav-inner">

        <a href="{{ url('/') }}" class="brand">
            <div class="brand-mark">🎓</div>
            Uni<span>Bridge</span>
        </a>

        <nav class="nav-links">
            <a href="#home">Home</a>
            <a href="#destinations">Destinations</a>
            <a href="#universities">Universities</a>
            <a href="#services">Services</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#location">Contact</a>
        </nav>

        <div class="nav-actions">

            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">
                    Login
                </a>

                <a href="{{ route('register') }}" class="btn btn-primary">
                    Get Started
                </a>
            @endauth

            <button class="mobile-menu" type="button">
                ☰
            </button>
        </div>

    </div>
</header>


<!-- HERO -->
<section class="hero" id="home">

    <div class="container hero-grid">

        <div>

            <div class="hero-badge">
                ✦ Smart Study Abroad Platform
            </div>

            <h1>
                Find the right
                <span>university</span>
                for your future.
            </h1>

            <p class="hero-text">
                UniBridge connects students with suitable universities,
                study destinations and professional consultants — all in
                one simple platform.
            </p>

            <div class="hero-buttons">

                @auth

                    <a
                        href="{{ route('student.universities.index') }}"
                        class="btn btn-primary"
                    >
                        🔎 Explore Universities
                    </a>

                    <a
                        href="{{ route('student.bookings.create') }}"
                        class="btn btn-outline"
                    >
                        👨‍💼 Book Consultant
                    </a>

                @else

                    <a
                        href="{{ route('register') }}"
                        class="btn btn-primary"
                    >
                        🚀 Start Your Journey
                    </a>

                    <a
                        href="#destinations"
                        class="btn btn-outline"
                    >
                        Explore Destinations
                    </a>

                @endauth

                <a
                    href="https://wa.me/923034253277?text=Assalam%20o%20Alaikum%20UniBridge%2C%20mujhe%20admission%20ya%20visa%20guidance%20chahiye."
                    class="btn whatsapp-link"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Contact UniBridge on WhatsApp"
                >
                    <span aria-hidden="true">☏</span>
                    WhatsApp Support
                </a>

            </div>

            <div class="hero-trust">
                <span><b class="check">✓</b> University discovery</span>
                <span><b class="check">✓</b> Eligibility guidance</span>
                <span><b class="check">✓</b> Consultant support</span>
            </div>

        </div>


        <!-- SEARCH CARD -->

        <div class="search-card">

            <h3>Find your study destination</h3>

            <p>
                Start with your preferred country, study level and field.
            </p>

            <form
                method="GET"
                action="{{ auth()->check() ? route('student.universities.index') : route('login') }}"
            >

                <div class="search-field">
                    <label>DESTINATION</label>

                    <select name="country">
                        <option value="">Select country</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="Canada">Canada</option>
                        <option value="Australia">Australia</option>
                        <option value="United States">United States</option>
                        <option value="Germany">Germany</option>
                        <option value="Italy">Italy</option>
                        <option value="Malaysia">Malaysia</option>
                        <option value="New Zealand">New Zealand</option>
                    </select>
                </div>

                <div class="search-field">
                    <label>STUDY LEVEL</label>

                    <select name="degree_level">
                        <option value="">Select level</option>
                        <option value="Bachelor">Bachelor</option>
                        <option value="Master">Master</option>
                        <option value="PhD">PhD</option>
                    </select>
                </div>

                <div class="search-field">
                    <label>FIELD OF STUDY</label>

                    <select name="subject">
                        <option value="">Select field</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Business">Business</option>
                        <option value="Engineering">Engineering</option>
                        <option value="Medicine">Medicine</option>
                        <option value="Law">Law</option>
                        <option value="Arts">Arts & Design</option>
                    </select>
                </div>

                <button class="btn btn-primary search-submit" type="submit">
                    🔎 Search Universities
                </button>

            </form>

            <p class="search-note">
                Smart matching based on your academic profile
            </p>

        </div>

    </div>

</section>


<!-- STATS -->
<section class="stats">

    <div class="container stats-grid">

        <div class="stat">
            <strong>🌍</strong>
            <span>Global Destinations</span>
        </div>

        <div class="stat">
            <strong>🎓</strong>
            <span>University Discovery</span>
        </div>

        <div class="stat">
            <strong>🤝</strong>
            <span>Consultant Support</span>
        </div>

        <div class="stat">
            <strong>⚡</strong>
            <span>Smart Recommendations</span>
        </div>

    </div>

</section>


<!-- DESTINATIONS -->
<section class="section" id="destinations">

    <div class="container">

        <div class="section-heading">

            <div class="eyebrow">
                Study Destinations
            </div>

            <h2>
                Choose where your future takes you
            </h2>

            <p>
                Explore popular international destinations and discover
                universities that match your academic goals.
            </p>

        </div>


        <div class="destination-grid">

            <a href="#universities" class="destination">
                <div class="destination-icon">🇬🇧</div>
                <h3>United Kingdom</h3>
                <p>Top universities & career-focused programs</p>
            </a>

            <a href="#universities" class="destination">
                <div class="destination-icon">🇨🇦</div>
                <h3>Canada</h3>
                <p>Quality education and global opportunities</p>
            </a>

            <a href="#universities" class="destination">
                <div class="destination-icon">🇦🇺</div>
                <h3>Australia</h3>
                <p>World-class universities and diverse programs</p>
            </a>

            <a href="#universities" class="destination">
                <div class="destination-icon">🇺🇸</div>
                <h3>United States</h3>
                <p>Innovative education and research opportunities</p>
            </a>

            <a href="#universities" class="destination">
                <div class="destination-icon">🇩🇪</div>
                <h3>Germany</h3>
                <p>Strong academic and technical programs</p>
            </a>

            <a href="#universities" class="destination">
                <div class="destination-icon">🇮🇹</div>
                <h3>Italy</h3>
                <p>International programs and cultural experience</p>
            </a>

            <a href="#universities" class="destination">
                <div class="destination-icon">🇲🇾</div>
                <h3>Malaysia</h3>
                <p>Affordable international education options</p>
            </a>

            <a href="#universities" class="destination">
                <div class="destination-icon">🇳🇿</div>
                <h3>New Zealand</h3>
                <p>Excellent education and student lifestyle</p>
            </a>

        </div>

    </div>

</section>


<!-- SMART FINDER -->
<section class="section section-light">

    <div class="container">

        <div class="finder">

            <div>

                <div class="eyebrow" style="color:#60a5fa;">
                    UniBridge Smart Match
                </div>

                <h2>
                    Stop searching blindly.
                    Find universities that fit you.
                </h2>

                <p>
                    Your academic profile, degree level, English score,
                    destination preference and budget can all be used to
                    guide your university search.
                </p>

                <div class="finder-list">

                    <div>
                        <span>✓</span>
                        Academic profile based matching
                    </div>

                    <div>
                        <span>✓</span>
                        Country and degree filters
                    </div>

                    <div>
                        <span>✓</span>
                        Tuition and living cost comparison
                    </div>

                    <div>
                        <span>✓</span>
                        English language requirement checks
                    </div>

                </div>

            </div>


            <div class="finder-box">

                <h3>Example University Matches</h3>

                <div class="finder-result">
                    <div class="university-logo">🎓</div>
                    <div>
                        <strong>University Match</strong>
                        <small>Canada · Computer Science</small>
                    </div>
                </div>

                <div class="finder-result">
                    <div class="university-logo">🏛️</div>
                    <div>
                        <strong>University Match</strong>
                        <small>United Kingdom · Business</small>
                    </div>
                </div>

                <div class="finder-result">
                    <div class="university-logo">🌏</div>
                    <div>
                        <strong>University Match</strong>
                        <small>Australia · Engineering</small>
                    </div>
                </div>

                @auth
                    <a
                        href="{{ route('student.universities.index') }}"
                        class="btn btn-primary"
                        style="width:100%;margin-top:8px;"
                    >
                        View University Finder
                    </a>
                @else
                    <a
                        href="{{ route('register') }}"
                        class="btn btn-primary"
                        style="width:100%;margin-top:8px;"
                    >
                        Create Student Profile
                    </a>
                @endauth

            </div>

        </div>

    </div>

</section>


<!-- SERVICES -->
<section class="section" id="services">

    <div class="container">

        <div class="section-heading">

            <div class="eyebrow">
                What UniBridge Offers
            </div>

            <h2>
                Everything you need for your study journey
            </h2>

            <p>
                From choosing a university to getting professional
                guidance, UniBridge brings the important steps together.
            </p>

        </div>


        <div class="service-grid">

            <div class="service">
                <div class="service-icon">🔎</div>
                <h3>University Finder</h3>
                <p>
                    Search and compare universities by country,
                    subject, degree level, fees and entry requirements.
                </p>
            </div>

            <div class="service">
                <div class="service-icon">📊</div>
                <h3>Eligibility Assessment</h3>
                <p>
                    Understand your academic readiness and identify
                    universities that may suit your profile.
                </p>
            </div>

            <div class="service">
                <div class="service-icon">🤖</div>
                <h3>Smart Recommendations</h3>
                <p>
                    Get structured guidance based on your academic
                    information and study preferences.
                </p>
            </div>

            <div class="service">
                <div class="service-icon">👨‍💼</div>
                <h3>Consultant Guidance</h3>
                <p>
                    Connect with consultants for personalized advice
                    and application guidance.
                </p>
            </div>

            <div class="service">
                <div class="service-icon">📋</div>
                <h3>Application Tracking</h3>
                <p>
                    Keep your university applications organized and
                    follow their progress from one dashboard.
                </p>
            </div>

            <div class="service">
                <div class="service-icon">📚</div>
                <h3>Preparation Courses</h3>
                <p>
                    Build your skills with preparation courses,
                    assessments and progress tracking.
                </p>
            </div>

        </div>

    </div>

</section>


<!-- HOW IT WORKS -->
<section class="section section-light" id="how-it-works">

    <div class="container">

        <div class="section-heading">

            <div class="eyebrow">
                Simple Process
            </div>

            <h2>
                How UniBridge works
            </h2>

            <p>
                A clear four-step journey from student profile to
                university application.
            </p>

        </div>


        <div class="steps">

            <div class="step">
                <div class="step-number">01</div>
                <h3>Create Your Profile</h3>
                <p>
                    Add your education, CGPA, English score, interests
                    and study preferences.
                </p>
            </div>

            <div class="step">
                <div class="step-number">02</div>
                <h3>Check Eligibility</h3>
                <p>
                    Review your academic readiness and understand
                    suitable study options.
                </p>
            </div>

            <div class="step">
                <div class="step-number">03</div>
                <h3>Find Universities</h3>
                <p>
                    Explore universities and compare important
                    requirements, costs and programs.
                </p>
            </div>

            <div class="step">
                <div class="step-number">04</div>
                <h3>Get Expert Support</h3>
                <p>
                    Book a consultant and move forward with your
                    application journey.
                </p>
            </div>

        </div>

    </div>

</section>


<!-- FEATURED UNIVERSITIES -->
<section class="section" id="universities">

    <div class="container">

        <div class="section-heading">

            <div class="eyebrow">
                University Discovery
            </div>

            <h2>
                Explore your next university
            </h2>

            <p>
                UniBridge is designed to make university discovery
                easier, clearer and more personalized.
            </p>

        </div>


        <div class="university-grid">

            <div class="university-card">

                <div class="university-top">
                    <div class="uni-symbol">🇨🇦</div>

                    <h3>University of Toronto</h3>

                    <p>
                        Toronto, Canada
                    </p>
                </div>

                <div class="university-info">

                    <div class="uni-row">
                        <span>Study Level</span>
                        <strong>Undergraduate</strong>
                    </div>

                    <div class="uni-row">
                        <span>Field</span>
                        <strong>Multiple Programs</strong>
                    </div>

                    <div class="uni-row">
                        <span>Location</span>
                        <strong>Toronto</strong>
                    </div>

                </div>

            </div>


            <div class="university-card">

                <div class="university-top">
                    <div class="uni-symbol">🇬🇧</div>

                    <h3>University of Manchester</h3>

                    <p>
                        Manchester, UK
                    </p>
                </div>

                <div class="university-info">

                    <div class="uni-row">
                        <span>Study Level</span>
                        <strong>Undergraduate</strong>
                    </div>

                    <div class="uni-row">
                        <span>Field</span>
                        <strong>Multiple Programs</strong>
                    </div>

                    <div class="uni-row">
                        <span>Location</span>
                        <strong>Manchester</strong>
                    </div>

                </div>

            </div>


            <div class="university-card">

                <div class="university-top">
                    <div class="uni-symbol">🇦🇺</div>

                    <h3>University of Melbourne</h3>

                    <p>
                        Melbourne, Australia
                    </p>
                </div>

                <div class="university-info">

                    <div class="uni-row">
                        <span>Study Level</span>
                        <strong>Undergraduate</strong>
                    </div>

                    <div class="uni-row">
                        <span>Field</span>
                        <strong>Multiple Programs</strong>
                    </div>

                    <div class="uni-row">
                        <span>Location</span>
                        <strong>Melbourne</strong>
                    </div>

                </div>

            </div>

        </div>


        <div style="text-align:center;margin-top:30px;">

            @auth

                <a
                    href="{{ route('student.universities.index') }}"
                    class="btn btn-primary"
                >
                    Explore All Universities →
                </a>

            @else

                <a
                    href="{{ route('login') }}"
                    class="btn btn-primary"
                >
                    Login to Explore Universities →
                </a>

            @endauth

        </div>

    </div>

</section>


<!-- CONSULTANTS -->
<section class="section section-light">

    <div class="container">

        <div class="section-heading">

            <div class="eyebrow">
                Human Guidance
            </div>

            <h2>
                Get guidance from a consultant
            </h2>

            <p>
                Sometimes technology gives you options. An experienced
                consultant helps you choose the right one.
            </p>

        </div>


        <div class="consultant-grid">

            <div class="consultant">

                <div class="consultant-avatar">
                    👨‍💼
                </div>

                <h3>Education Consultant</h3>

                <p>
                    University & Application Guidance
                </p>

                <span class="consultant-tag">
                    University Selection
                </span>

                <span class="consultant-tag">
                    Applications
                </span>

                <div style="margin-top:20px;">

                    @auth

                        <a
                            href="{{ route('student.bookings.create') }}"
                            class="btn btn-primary"
                        >
                            Book Consultation
                        </a>

                    @else

                        <a
                            href="{{ route('login') }}"
                            class="btn btn-primary"
                        >
                            Login to Book
                        </a>

                    @endauth

                </div>

            </div>


            <div class="consultant">

                <div class="consultant-avatar">
                    👩‍💼
                </div>

                <h3>Admissions Consultant</h3>

                <p>
                    Profile & Application Support
                </p>

                <span class="consultant-tag">
                    Profile Review
                </span>

                <span class="consultant-tag">
                    Application Help
                </span>

                <div style="margin-top:20px;">

                    @auth

                        <a
                            href="{{ route('student.bookings.create') }}"
                            class="btn btn-primary"
                        >
                            Book Consultation
                        </a>

                    @else

                        <a
                            href="{{ route('login') }}"
                            class="btn btn-primary"
                        >
                            Login to Book
                        </a>

                    @endauth

                </div>

            </div>


            <div class="consultant">

                <div class="consultant-avatar">
                    🌎
                </div>

                <h3>Study Abroad Advisor</h3>

                <p>
                    Destination & Study Planning
                </p>

                <span class="consultant-tag">
                    Destinations
                </span>

                <span class="consultant-tag">
                    Study Planning
                </span>

                <div style="margin-top:20px;">

                    @auth

                        <a
                            href="{{ route('student.bookings.create') }}"
                            class="btn btn-primary"
                        >
                            Book Consultation
                        </a>

                    @else

                        <a
                            href="{{ route('login') }}"
                            class="btn btn-primary"
                        >
                            Login to Book
                        </a>

                    @endauth

                </div>

            </div>

        </div>

    </div>

</section>


<!-- LOCATION / MAP -->
<section class="section location-section" id="location">

    <div class="container">

        <div class="section-heading">

            <div class="eyebrow">
                Our Location
            </div>

            <h2>
                UniBridge — Lahore
            </h2>

            <p>
                Find UniBridge and explore our location in Lahore,
                Pakistan.
            </p>

        </div>


        <div class="location-grid">

            <div class="location-info">

                <h2>
                    Let's connect.
                </h2>

                <p>
                    Whether you need help finding a university,
                    understanding eligibility or planning your next
                    academic step, UniBridge is here to guide you.
                </p>


                <div class="location-item">

                    <div class="location-item-icon">
                        📍
                    </div>

                    <div>
                        <strong>Location</strong>
                        <span>
                            Lahore, Punjab, Pakistan
                        </span>
                    </div>

                </div>


                <div class="location-item">

                    <div class="location-item-icon">
                        🌍
                    </div>

                    <div>
                        <strong>Study Destinations</strong>
                        <span>
                            UK · Canada · Australia · USA · Germany
                            and more
                        </span>
                    </div>

                </div>


                <div class="location-item">

                    <div class="location-item-icon">
                        🎓
                    </div>

                    <div>
                        <strong>Student Support</strong>
                        <span>
                            University discovery, eligibility,
                            applications and consultant guidance.
                        </span>
                    </div>

                </div>


                @auth

                    <a
                        href="{{ route('student.bookings.create') }}"
                        class="btn btn-primary"
                        style="background:#ffffff;color:#1d4ed8;margin-top:5px;"
                    >
                        Book a Consultant →
                    </a>

                @else

                    <a
                        href="{{ route('register') }}"
                        class="btn btn-primary"
                        style="background:#ffffff;color:#1d4ed8;margin-top:5px;"
                    >
                        Start Your Journey →
                    </a>

                @endauth

            </div>


            <!-- GOOGLE MAP -->
            <div class="map-wrap">

                <iframe
                    src="https://www.google.com/maps?q=Lahore%2C%20Punjab%2C%20Pakistan&output=embed"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    allowfullscreen>
                </iframe>

            </div>

        </div>

    </div>

</section>


<!-- FINAL CTA -->
<section class="cta">

    <div class="container">

        <div class="cta-box">

            <div>

                <h2>
                    Your global education journey starts here.
                </h2>

                <p>
                    Create your profile and discover the possibilities
                    waiting for you.
                </p>

            </div>


            @auth

                <a
                    href="{{ route('student.universities.index') }}"
                    class="btn"
                >
                    Explore Universities →
                </a>

            @else

                <a
                    href="{{ route('register') }}"
                    class="btn"
                >
                    Create Free Profile →
                </a>

            @endauth

        </div>

    </div>

</section>


<!-- FOOTER -->
<footer>

    <div class="container">

        <div class="footer-grid">

            <div>

                <div class="footer-brand">
                    Uni<span>Bridge</span>
                </div>

                <p class="footer-description">
                    A modern study-abroad platform helping students
                    discover universities, understand eligibility and
                    connect with professional consultants.
                </p>

            </div>


            <div>

                <h4>Platform</h4>

                <div class="footer-links">
                    <a href="#home">Home</a>
                    <a href="#universities">Universities</a>
                    <a href="#destinations">Destinations</a>
                    <a href="#services">Services</a>
                </div>

            </div>


            <div>

                <h4>Student</h4>

                <div class="footer-links">

                    @auth
                        <a href="{{ route('student.profile.edit') }}">
                            My Profile
                        </a>

                        <a href="{{ route('student.eligibility') }}">
                            Eligibility
                        </a>

                        <a href="{{ route('student.bookings.index') }}">
                            My Bookings
                        </a>
                    @else
                        <a href="{{ route('register') }}">
                            Create Account
                        </a>

                        <a href="{{ route('login') }}">
                            Student Login
                        </a>
                    @endauth

                </div>

            </div>


            <div>

                <h4>Contact</h4>

                <div class="footer-links">
                    <a href="#location">Lahore, Pakistan</a>
                    <a href="#location">View Map</a>
                    <a href="#how-it-works">How It Works</a>
                    <a href="https://wa.me/923034253277?text=Assalam%20o%20Alaikum%20UniBridge%2C%20mujhe%20admission%20ya%20visa%20guidance%20chahiye."
                       target="_blank" rel="noopener noreferrer">WhatsApp Support</a>
                </div>

            </div>

        </div>


        <div class="copyright">
            © {{ date('Y') }} UniBridge. All rights reserved.
        </div>

    </div>

</footer>

<a
    href="https://wa.me/923034253277?text=Assalam%20o%20Alaikum%20UniBridge%2C%20mujhe%20admission%20ya%20visa%20guidance%20chahiye."
    class="whatsapp-float"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="Open UniBridge WhatsApp support"
>
    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M12.04 2a9.87 9.87 0 0 0-8.48 14.92L2 22l5.25-1.38A9.93 9.93 0 1 0 12.04 2Zm0 18.05a8.1 8.1 0 0 1-4.13-1.13l-.3-.18-3.12.82.83-3.04-.2-.31a8.1 8.1 0 1 1 6.92 3.84Zm4.44-6.07c-.24-.12-1.42-.7-1.64-.78-.22-.08-.38-.12-.54.12-.16.24-.62.78-.76.94-.14.16-.28.18-.52.06-.24-.12-1.01-.37-1.93-1.18-.71-.63-1.19-1.42-1.33-1.66-.14-.24-.02-.37.1-.49.1-.1.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.2-.47-.4-.4-.54-.4h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.69 2.58 4.1 3.62.57.25 1.02.4 1.37.5.58.18 1.1.15 1.51.09.46-.07 1.42-.58 1.62-1.14.2-.56.2-1.04.14-1.14-.06-.1-.22-.16-.46-.28Z"/>
    </svg>
    Chat on WhatsApp
</a>

{{-- Public UniBridge AI Chatbot --}}
<div
    x-data="{
        open: false,
        prompt: '',
        loading: false,
        messages: [{
            role: 'assistant',
            text: 'Welcome to UniBridge! Ask me about universities, courses, admissions or studying abroad.'
        }],

        async sendMessage() {
            const question = this.prompt.trim();

            if (!question || this.loading) return;

            this.messages.push({ role: 'user', text: question });
            this.prompt = '';
            this.loading = true;

            this.$nextTick(() => {
                this.$refs.chatMessages.scrollTop =
                    this.$refs.chatMessages.scrollHeight;
            });

            try {
                const response = await fetch(@js(route('api.gemini.public')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name=csrf-token]'
                        ).content
                    },
                    body: JSON.stringify({ prompt: question })
                });

                const rawResponse = await response.text();
const cleanResponse = rawResponse.replace(/^\uFEFF/, '');
const data = JSON.parse(cleanResponse)

                if (!response.ok) {
                    throw new Error(data.message || 'AI service unavailable.');
                }

                if (typeof data.text !== 'string' || !data.text.trim()) {
                    throw new Error('AI returned an empty response.');
                }

                this.messages.push({
                    role: 'assistant',
                    text: data.text
                });
            } catch (error) {
                this.messages.push({
                    role: 'assistant',
                    text: 'Sorry, the AI service is temporarily unavailable. Please try again later.'
                });

                console.error('Public UniBridge AI:', error);
            } finally {
                this.loading = false;

                this.$nextTick(() => {
                    this.$refs.chatMessages.scrollTop =
                        this.$refs.chatMessages.scrollHeight;
                });
            }
        }
    }"
    x-cloak
    style="position:fixed;right:22px;bottom:22px;z-index:9999;font-family:Arial,sans-serif;"
>
    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open.toString()"
        style="border:0;border-radius:30px;background:#4338ca;color:white;padding:15px 23px;font-size:16px;font-weight:bold;box-shadow:0 5px 20px #0003;cursor:pointer;"
    >
        ✦ UniBridge AI
    </button>

    <section
        x-show="open"
        x-transition
        style="position:absolute;right:0;bottom:72px;width:min(430px,calc(100vw - 32px));height:min(620px,calc(100vh - 120px));min-height:360px;background:white;border:1px solid #dbe1ea;border-radius:18px;box-shadow:0 12px 40px #0003;display:flex;flex-direction:column;overflow:hidden;"
    >
        <div style="flex-shrink:0;background:#1e1b4b;color:white;padding:18px;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <div style="font-size:18px;font-weight:bold;">UniBridge AI Assistant</div>
                <div style="font-size:13px;color:#c7d2fe;margin-top:5px;">Study-abroad support</div>
            </div>

            <button
                type="button"
                @click="open = false"
                aria-label="Close chat"
                style="border:0;background:transparent;color:white;font-size:28px;cursor:pointer;"
            >&times;</button>
        </div>

        <div
            x-ref="chatMessages"
            role="log"
            aria-live="polite"
            style="flex:1;min-height:0;overflow-y:auto;padding:18px;background:#f8fafc;display:flex;flex-direction:column;gap:14px;"
        >
            <template x-for="(message, index) in messages" :key="index">
                <div
                    style="max-width:88%;padding:12px 15px;border-radius:14px;font-size:15px;line-height:1.6;white-space:pre-wrap;overflow-wrap:anywhere;"
                    :style="message.role === 'user'
                        ? 'align-self:flex-end;background:#4338ca;color:white;border-bottom-right-radius:4px;'
                        : 'align-self:flex-start;background:white;color:#1e293b;border:1px solid #e2e8f0;border-bottom-left-radius:4px;'"
                    x-text="message.text"
                ></div>
            </template>

            <div
                x-show="loading"
                style="align-self:flex-start;background:white;border:1px solid #e2e8f0;border-radius:12px;padding:12px;color:#64748b;font-size:14px;"
            >
                AI is responding...
            </div>
        </div>

        <form
            @submit.prevent="sendMessage()"
            style="flex-shrink:0;display:flex;align-items:flex-end;gap:9px;padding:13px;background:white;border-top:1px solid #e2e8f0;"
        >
            <textarea
                x-model="prompt"
                @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                maxlength="2000"
                rows="2"
                required
                placeholder="Ask about studying abroad..."
                aria-label="Type your message"
                style="display:block;box-sizing:border-box;flex:1;width:0;min-width:0;min-height:52px;max-height:100px;resize:vertical;border:1px solid #cbd5e1;border-radius:11px;padding:12px;font-size:15px;line-height:1.5;color:#0f172a;background:white;outline-color:#4f46e5;"
            ></textarea>

            <button
                type="submit"
                :disabled="loading || !prompt.trim()"
                style="flex-shrink:0;min-height:48px;border:0;border-radius:11px;background:#4338ca;color:white;padding:0 17px;font-size:15px;font-weight:bold;cursor:pointer;"
                :style="loading || !prompt.trim() ? 'opacity:0.55;cursor:not-allowed;' : ''"
            >
                <span x-show="!loading">Send</span>
                <span x-show="loading">...</span>
            </button>
        </form>

        <div style="flex-shrink:0;padding:0 10px 10px;text-align:center;font-size:11px;color:#64748b;background:white;">
            Please verify important admission information.
        </div>
    </section>
</div>
{{-- End Public UniBridge AI Chatbot --}}

</body>
</html>