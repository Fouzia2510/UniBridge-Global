<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Verify Login — UniBridge</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system,
                BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, .10), transparent 34%),
                radial-gradient(circle at bottom right, rgba(37, 99, 235, .07), transparent 30%),
                #f7faff;
            color: #14213d;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 18px;
            position: relative;
            overflow: hidden;
        }

        .glow {
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 999px;
            filter: blur(90px);
            opacity: .18;
            pointer-events: none;
        }

        .glow-red {
            background: #2563eb;
            top: -180px;
            left: -160px;
        }

        .glow-yellow {
            background: #93c5fd;
            bottom: -220px;
            right: -150px;
        }

        .card {
            width: 100%;
            max-width: 470px;
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, .96);
            border: 1px solid #e5eaf2;
            border-radius: 28px;
            padding: 34px;
            box-shadow:
                0 25px 70px rgba(15, 23, 42, .13),
                0 0 0 1px rgba(15, 23, 42, .02);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
        }

        .brand-mark {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb, #0b1736);
            color: white;
            font-weight: 900;
            font-size: 19px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, .22);
        }

        .brand-name {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -.5px;
            color: #0b1736;
        }

        .brand-subtitle {
            color: #94a3b8;
            font-size: 12px;
            margin-top: 2px;
        }

        .icon-wrap {
            width: 72px;
            height: 72px;
            margin: 0 auto 22px;
            display: grid;
            place-items: center;
            border-radius: 22px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
        }

        .icon-wrap svg {
            width: 34px;
            height: 34px;
        }

        .heading {
            text-align: center;
            margin-bottom: 10px;
        }

        .heading h1 {
            margin: 0;
            color: #fff;
            font-size: 28px;
            line-height: 1.2;
            font-weight: 900;
            letter-spacing: -.7px;
        }

        .heading p {
            margin: 10px auto 0;
            max-width: 390px;
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.65;
        }

        .email {
            color: #2563eb;
            font-weight: 700;
            word-break: break-word;
        }

        .alert {
            margin-top: 20px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-size: 13px;
        }

        .form {
            margin-top: 26px;
        }

        .label {
            display: block;
            margin-bottom: 9px;
            color: #e2e8f0;
            font-size: 13px;
            font-weight: 800;
        }

        .otp {
            width: 100%;
            height: 62px;
            border-radius: 15px;
            border: 1px solid #334155;
            background: #fbfcfe;
            color: #14213d;
            outline: none;
            text-align: center;
            font-size: 27px;
            font-weight: 800;
            letter-spacing: 9px;
            padding: 0 16px;
            transition: .2s ease;
        }

        .otp::placeholder {
            color: #475569;
            letter-spacing: 7px;
        }

        .otp:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
        }

        .verify-btn {
            width: 100%;
            height: 54px;
            margin-top: 16px;
            border: 0;
            border-radius: 14px;
            cursor: pointer;
            color: white;
            font-size: 15px;
            font-weight: 900;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .verify-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 32px rgba(37, 99, 235, .28);
        }

        .verify-btn:active {
            transform: translateY(0);
        }

        .resend-section {
            margin-top: 22px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, .08);
            text-align: center;
        }

        .resend-text {
            color: #94a3b8;
            font-size: 13px;
        }

        .resend-btn {
            margin-top: 8px;
            background: transparent;
            border: 0;
            color: #2563eb;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
        }

        .resend-btn:hover {
            text-decoration: underline;
        }

        .cancel-section {
            margin-top: 16px;
            text-align: center;
        }

        .cancel-btn {
            color: #94a3b8;
            background: transparent;
            border: 0;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
        }

        .cancel-btn:hover {
            color: #0b1736;
        }

        .security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            margin-top: 25px;
            color: #64748b;
            font-size: 11px;
        }

        .security-note svg {
            width: 14px;
            height: 14px;
        }

        .footer {
            position: absolute;
            bottom: 18px;
            left: 0;
            right: 0;
            text-align: center;
            color: #64748b;
            font-size: 11px;
        }

        @media (max-width: 520px) {
            .page {
                padding: 18px;
                align-items: flex-start;
                padding-top: 30px;
            }

            .card {
                padding: 25px 20px;
                border-radius: 22px;
            }

            .heading h1 {
                font-size: 24px;
            }

            .otp {
                height: 58px;
                font-size: 23px;
                letter-spacing: 7px;
            }

            .footer {
                position: static;
                margin-top: 18px;
            }
        }
    </style>
</head>

<body>
    <main class="page">

        <div class="glow glow-red"></div>
        <div class="glow glow-yellow"></div>

        <section class="card">

            <div class="brand">
                <div class="brand-mark">🎓</div>

                <div>
                    <div class="brand-name">UniBridge</div>
                    <div class="brand-subtitle">Global Education Platform</div>
                </div>
            </div>

            <div class="icon-wrap">
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="10" width="16" height="10" rx="2"></rect>
                    <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                    <circle cx="12" cy="15" r="1"></circle>
                    <path d="M12 16v1"></path>
                </svg>
            </div>

            <div class="heading">
                <h1>Verify your login</h1>

                <p>
                    We sent a 6-digit verification code to
                    <span class="email">
                        {{ auth()->user()->email }}
                    </span>
                </p>
            </div>

            @if ($errors->any())
                <div class="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST"
                  action="{{ route('two-factor.verify.store') }}"
                  class="form">

                @csrf

                <label for="code" class="label">
                    Verification Code
                </label>

                <input
                    id="code"
                    name="otp"
                    type="text"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    class="otp"
                    placeholder="••••••"
                    required
                    autofocus
                >

                <button type="submit" class="verify-btn">
                    Verify &amp; Continue
                </button>
            </form>

            <div class="resend-section">
                <div class="resend-text">
                    Didn't receive the code?
                </div>

                <form method="POST" action="{{ route('two-factor.resend') }}">
                    @csrf

                    <button type="submit" class="resend-btn">
                        Send New Code
                    </button>
                </form>
            </div>

            <div class="cancel-section">
                <form method="POST" action="{{ route('two-factor.cancel') }}">
                    @csrf

                    <button type="submit" class="cancel-btn">
                        Cancel and return to login
                    </button>
                </form>
            </div>

            <div class="security-note">
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"></path>
                    <path d="M9 12l2 2 4-4"></path>
                </svg>

                Your login verification is protected by UniBridge security.
            </div>

        </section>

        <div class="footer">
            © {{ date('Y') }} UniBridge · Global Education Platform
        </div>

    </main>
</body>
</html>
