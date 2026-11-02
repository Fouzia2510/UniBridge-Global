<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login — UniBridge</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|manrope:400,500,600,700,800" rel="stylesheet">

    {{-- Laravel Passkey JavaScript --}}
    @vite('resources/js/app.js')

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at 85% 15%, rgba(37,99,235,.10), transparent 30%),
                radial-gradient(circle at 10% 85%, rgba(37,99,235,.06), transparent 28%),
                #f7faff;
            color: #14213d;
        }

        a {
            text-decoration: none;
        }

        button,
        input {
            font-family: inherit;
        }

        .page {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 35px 18px;
        }

        .wrapper {
            width: 100%;
            max-width: 980px;
        }

        /* BRAND */

        .brand {
            text-align: center;
            margin-bottom: 25px;
        }

        .brand a {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            text-align: left;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: linear-gradient(135deg, #2563eb, #0b1736);
            color: #fff;
            font-size: 20px;
            font-weight: 900;
            box-shadow: 0 8px 20px rgba(37,99,235,.22);
        }

        .brand-name {
            margin-top: 0;
            font-size: 25px;
            font-weight: 900;
            font-family: 'Manrope', sans-serif;
            color: #0b1736;
        }

        .brand-name span {
            color: #2563eb;
        }

        .brand-subtitle {
            margin-top: 4px;
            color: #64748b;
            font-size: 13px;
        }

        /* MAIN CARD */

        .main-card {
            display: grid;
            grid-template-columns: 42% 58%;
            overflow: hidden;
            border: 1px solid #e5eaf2;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 25px 70px rgba(15,23,42,.13);
        }

        /* LEFT */

        .portal-side {
            padding: 35px;
            background:
                linear-gradient(160deg,#f7faff,#eff6ff);
            border-right: 1px solid #e5eaf2;
        }

        .side-title {
            font-size: 22px;
            font-weight: 900;
            line-height: 1.3;
        }

        .side-text {
            margin-top: 10px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }

        .portal-list {
            margin-top: 28px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .portal-option {
            width: 100%;
            border: 1px solid #dce6f5;
            border-radius: 14px;
            padding: 15px;
            background: #fff;
            color: #334155;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 13px;
            text-align: left;
            transition: .2s ease;
        }

        .portal-option:hover {
            border-color: #93c5fd;
            transform: translateY(-1px);
        }

        .portal-option.active {
            border-color: #2563eb;
            background: #eff6ff;
            box-shadow: 0 0 0 1px rgba(37,99,235,.10);
        }

        .portal-icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #eff6ff;
            font-size: 20px;
        }

        .portal-option.active .portal-icon {
            background: #dbeafe;
        }

        .portal-name {
            font-size: 14px;
            font-weight: 800;
        }

        .portal-description {
            margin-top: 3px;
            color: #64748b;
            font-size: 11px;
        }

        .security-box {
            margin-top: 28px;
            padding: 14px;
            border: 1px solid #e5eaf2;
            border-radius: 13px;
            background: #fff;
        }

        .security-title {
            font-size: 12px;
            font-weight: 800;
            color: #0b1736;
        }

        .security-text {
            margin-top: 5px;
            color: #64748b;
            font-size: 11px;
            line-height: 1.5;
        }

        /* RIGHT */

        .form-side {
            padding: 38px;
            background: #fff;
        }

        .portal-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 11px;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .title {
            margin-top: 15px;
            font-size: 31px;
            font-weight: 900;
        }

        .description {
            margin-top: 7px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }

        .errors {
            margin-top: 20px;
            padding: 13px 15px;
            border-radius: 12px;
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #1d4ed8;
            font-size: 13px;
        }

        .errors ul {
            margin: 7px 0 0;
            padding-left: 18px;
        }

        .status {
            margin-top: 20px;
            padding: 13px 15px;
            border-radius: 12px;
            border: 1px solid rgba(34,197,94,.25);
            background: rgba(34,197,94,.08);
            color: #166534;
            font-size: 13px;
        }

        .field {
            margin-top: 20px;
        }

        .label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        label {
            color: #334155;
            font-size: 13px;
            font-weight: 800;
        }

        .forgot {
            color: #2563eb;
            font-size: 12px;
            font-weight: 700;
        }

        .input {
            width: 100%;
            height: 49px;
            border: 1px solid #dce3ed;
            border-radius: 12px;
            background: #fbfcfe;
            color: #14213d;
            padding: 0 14px;
            outline: none;
            font-size: 14px;
        }

        .input::placeholder {
            color: #64748b;
        }

        .input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }

        .password-wrap {
            position: relative;
            margin-top: 8px;
        }

        .password-wrap .input {
            padding-right: 60px;
        }

        .show-password {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            font-size: 12px;
            font-weight: 800;
        }

        .show-password:hover {
            color: #2563eb;
        }

        .remember {
            margin-top: 17px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 12px;
        }

        .remember input {
            accent-color: #2563eb;
        }

        /* PASSWORD LOGIN */

        .login-button {
            width: 100%;
            height: 50px;
            margin-top: 22px;
            border: 0;
            border-radius: 12px;
            background:
                linear-gradient(135deg,#2563eb,#1d4ed8);
            color: white;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            box-shadow:
                0 12px 25px rgba(37,99,235,.20);
            transition: .2s ease;
        }

        .login-button:hover {
            transform: translateY(-1px);
            background:
                linear-gradient(135deg,#3b82f6,#2563eb);
        }

        .login-button:disabled {
            opacity: .65;
            cursor: wait;
            transform: none;
        }

        /* PASSKEY */

        .passkey-section {
            margin-top: 12px;
        }

        .passkey-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 13px 0;
            color: #475569;
            font-size: 11px;
        }

        .passkey-divider::before,
        .passkey-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e5eaf2;
        }

        .passkey-button {
            width: 100%;
            height: 50px;
            border: 1px solid #475569;
            border-radius: 12px;
            background: #fff;
            color: #14213d;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: .2s ease;
        }

        .passkey-button:hover {
            border-color: #2563eb;
            background: #eff6ff;
            transform: translateY(-1px);
        }

        .passkey-button:disabled {
            opacity: .55;
            cursor: wait;
            transform: none;
        }

        .passkey-icon {
            font-size: 18px;
        }

        .passkey-status {
            display: none;
            margin-top: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(255,255,255,.04);
            border: 1px solid #e5eaf2;
            color: #64748b;
            text-align: center;
            font-size: 12px;
            line-height: 1.5;
        }

        .passkey-status.visible {
            display: block;
        }

        .passkey-status.error {
            color: #1d4ed8;
            border-color: #fecaca;
            background: #fef2f2;
        }

        .register {
            margin-top: 20px;
            text-align: center;
            color: #64748b;
            font-size: 12px;
        }

        .register a {
            color: #2563eb;
            font-weight: 800;
        }

        .change {
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid #e5eaf2;
            text-align: center;
        }

        .change a {
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .footer {
            margin-top: 18px;
            text-align: center;
            color: #64748b;
            font-size: 11px;
        }

        @media (max-width: 760px) {

            .main-card {
                grid-template-columns: 1fr;
            }

            .portal-side {
                border-right: 0;
                border-bottom: 1px solid #e5eaf2;
            }

            .form-side {
                padding: 28px 22px;
            }
        }

        @media (max-width: 450px) {

            .page {
                padding: 20px 12px;
            }

            .portal-side {
                padding: 25px 20px;
            }

            .title {
                font-size: 27px;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <div class="wrapper">

        {{-- BRAND --}}
        <div class="brand">

            <a href="{{ url('/') }}" style="color:inherit;">

                    <div class="brand-logo">
                    🎓
                </div>

                <div class="brand-name">
                    Uni<span>Bridge</span>
                </div>

                <div class="brand-subtitle">
                    Global Education
                </div>

            </a>

        </div>

        <div class="main-card">

            {{-- LEFT PORTAL SELECTOR --}}
            <div class="portal-side">

                <div class="side-title">
                    Choose your portal
                </div>

                <div class="side-text">
                    Select the account type you want to sign in with.
                    Your selected portal is securely checked against your account.
                </div>

                <div class="portal-list">

                    <button
                        type="button"
                        class="portal-option"
                        data-role="student"
                    >

                        <div class="portal-icon">
                            🎓
                        </div>

                        <div>

                            <div class="portal-name">
                                Student Portal
                            </div>

                            <div class="portal-description">
                                Applications, universities & profile
                            </div>

                        </div>

                    </button>

                    <button
                        type="button"
                        class="portal-option"
                        data-role="consultant"
                    >

                        <div class="portal-icon">
                            💼
                        </div>

                        <div>

                            <div class="portal-name">
                                Consultant Portal
                            </div>

                            <div class="portal-description">
                                Students, meetings & applications
                            </div>

                        </div>

                    </button>

                    <button
                        type="button"
                        class="portal-option"
                        data-role="admin"
                    >

                        <div class="portal-icon">
                            🛡️
                        </div>

                        <div>

                            <div class="portal-name">
                                Admin Portal
                            </div>

                            <div class="portal-description">
                                Users, approvals & system control
                            </div>

                        </div>

                    </button>

                </div>

                <div class="security-box">

                    <div class="security-title">
                        🔐 Secure UniBridge Authentication
                    </div>

                    <div class="security-text">
                        UniBridge supports password login and
                        Passkey/Fingerprint authentication.
                        Additional email verification can be required
                        before dashboard access.
                    </div>

                </div>

            </div>

            {{-- RIGHT LOGIN --}}
            <div class="form-side">

                <div class="portal-badge">

                    <span id="portal-icon-text">
                        🎓
                    </span>

                    <span id="portal-label">
                        Student Portal
                    </span>

                </div>

                <div class="title">
                    Welcome back
                </div>

                <div class="description">
                    Sign in to continue to your UniBridge account.
                </div>

                {{-- ERRORS --}}
                @if ($errors->any())

                    <div class="errors">

                        <strong>
                            Login failed.
                        </strong>

                        <ul>

                            @foreach ($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif

                {{-- STATUS --}}
                @if (session('status'))

                    <div class="status">
                        {{ session('status') }}
                    </div>

                @endif

                {{-- NORMAL LOGIN FORM --}}
                <form
                    method="POST"
                    action="{{ route('login') }}"
                    id="login-form"
                >

                    @csrf

                    {{-- ROLE REQUIRED BY AUTHENTICATED SESSION CONTROLLER --}}
                    <input
                        type="hidden"
                        name="role"
                        id="portal-role"
                        value="{{ $portalRole ?? 'student' }}"
                    >

                    {{-- EMAIL --}}
                    <div class="field">

                        <div class="label-row">

                            <label for="email">
                                Email Address
                            </label>

                            <a
                                class="forgot"
                                href="{{ route('password.request') }}"
                            >
                                Forgot password?
                            </a>

                        </div>

                        <input
                            class="input"
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your email"
                            autocomplete="username"
                            required
                            autofocus
                        >

                    </div>

                    {{-- PASSWORD --}}
                    <div class="field">

                        <label for="password">
                            Password
                        </label>

                        <div class="password-wrap">

                            <input
                                class="input"
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="show-password"
                                id="show-password"
                            >
                                Show
                            </button>

                        </div>

                    </div>

                    {{-- REMEMBER --}}
                    <label class="remember">

                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            value="1"
                        >

                        <span>
                            Remember me
                        </span>

                    </label>

                    {{-- PASSWORD SIGN IN --}}
                    <button
                        type="submit"
                        class="login-button"
                        id="login-submit"
                    >
                        Sign In
                    </button>

                    {{-- PASSKEY INSIDE SAME LOGIN AREA --}}
                    <div class="passkey-section">

                        <div class="passkey-divider">
                            OR
                        </div>

                        <button
                            type="button"
                            class="passkey-button"
                            id="passkey-login-button"
                        >

                            <span class="passkey-icon">
                                🔐
                            </span>

                            <span id="passkey-button-text">
                                Sign in with Passkey / Fingerprint
                            </span>

                        </button>

                        <div
                            id="passkey-status"
                            class="passkey-status"
                            aria-live="polite"
                        ></div>

                    </div>

                </form>

                {{-- REGISTER --}}
                <div
                    class="register"
                    id="register-area"
                >

                    Don't have an account?

                    <a href="{{ route('register') }}">
                        Create account
                    </a>

                </div>

                {{-- CHANGE PORTAL --}}
                <div class="change">

                    <a href="{{ route('login') }}">
                        ← Change portal
                    </a>

                </div>

            </div>

        </div>

        <div class="footer">
            © {{ date('Y') }} UniBridge · Global Education Platform
        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const roleInput =
        document.getElementById('portal-role');

    const portalButtons =
        document.querySelectorAll('.portal-option');

    const portalLabel =
        document.getElementById('portal-label');

    const portalIcon =
        document.getElementById('portal-icon-text');

    const registerArea =
        document.getElementById('register-area');

    const password =
        document.getElementById('password');

    const showPassword =
        document.getElementById('show-password');

    const remember =
        document.getElementById('remember');

    const passkeyButton =
        document.getElementById('passkey-login-button');

    const passkeyButtonText =
        document.getElementById('passkey-button-text');

    const passkeyStatus =
        document.getElementById('passkey-status');

    const loginSubmit =
        document.getElementById('login-submit');

    const portalData = {

        student: {
            label: 'Student Portal',
            icon: '🎓',
            register: true
        },

        consultant: {
            label: 'Consultant Portal',
            icon: '💼',
            register: true
        },

        admin: {
            label: 'Admin Portal',
            icon: '🛡️',
            register: false
        }

    };

    function selectPortal(role) {

        if (!portalData[role]) {
            role = 'student';
        }

        const data =
            portalData[role];

        roleInput.value =
            role;

        portalButtons.forEach(function (button) {

            button.classList.toggle(
                'active',
                button.dataset.role === role
            );

        });

        portalLabel.textContent =
            data.label;

        portalIcon.textContent =
            data.icon;

        if (registerArea) {

            registerArea.style.display =
                data.register
                    ? 'block'
                    : 'none';

        }

    }

    portalButtons.forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                selectPortal(
                    button.dataset.role
                );

            }
        );

    });

    selectPortal(
        roleInput.value || 'student'
    );

    /* PASSWORD SHOW / HIDE */

    if (password && showPassword) {

        showPassword.addEventListener(
            'click',
            function () {

                if (
                    password.type === 'password'
                ) {

                    password.type = 'text';

                    showPassword.textContent =
                        'Hide';

                } else {

                    password.type = 'password';

                    showPassword.textContent =
                        'Show';

                }

            }
        );

    }

    /* REAL PASSKEY LOGIN */

    if (passkeyButton) {

        passkeyButton.addEventListener(
            'click',
            async function () {

                if (
                    !window.Passkeys ||
                    typeof window.Passkeys.verify !== 'function'
                ) {

                    showPasskeyStatus(
                        'Passkey system is not loaded. Please refresh the page and try again.',
                        true
                    );

                    return;
                }

                passkeyButton.disabled =
                    true;

                if (loginSubmit) {
                    loginSubmit.disabled =
                        true;
                }

                passkeyButtonText.textContent =
                    'Waiting for fingerprint / Passkey...';

                showPasskeyStatus(
                    'Follow the Windows Hello / fingerprint prompt.',
                    false
                );

                try {

                    const response =
                        await window.Passkeys.verify({
                            remember: function () {

                                return remember
                                    ? remember.checked
                                    : false;

                            }
                        });

                    /*
                     * Laravel Passkeys returns a redirect
                     * after successful authentication.
                     */

                    if (
                        response &&
                        response.redirect
                    ) {

                        window.location.href =
                            response.redirect;

                        return;
                    }

                    /*
                     * Some successful responses may not
                     * include a redirect. Refresh the page
                     * so Laravel can determine the new state.
                     */

                    window.location.reload();

                } catch (error) {

                    console.error(
                        'UniBridge Passkey Error:',
                        error
                    );

                    let message =
                        'Passkey verification was cancelled or failed.';

                    if (
                        error &&
                        error.message
                    ) {

                        message =
                            error.message;

                    }

                    showPasskeyStatus(
                        message,
                        true
                    );

                    passkeyButton.disabled =
                        false;

                    if (loginSubmit) {
                        loginSubmit.disabled =
                            false;
                    }

                    passkeyButtonText.textContent =
                        'Sign in with Passkey / Fingerprint';

                }

            }
        );

    }

    function showPasskeyStatus(
        message,
        isError
    ) {

        if (!passkeyStatus) {
            return;
        }

        passkeyStatus.textContent =
            message;

        passkeyStatus.classList.add(
            'visible'
        );

        passkeyStatus.classList.toggle(
            'error',
            isError
        );

    }

});
</script>

</body>
</html>