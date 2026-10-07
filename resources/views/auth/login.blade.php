<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrownCarz Admin — Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg: #090b0e;
            --card-bg: #11141a;
            --card-border: #1f242e;
            --input-bg: #161a22;
            --input-border: #262c38;
            --input-focus: #c9a24d;
            --text-primary: #f1f5f9;
            --text-secondary: #8590a2;
            --text-muted: #576071;
            --gold: #c9a24d;
            --gold-hover: #dbb35d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg);
            background-image: radial-gradient(circle at 50% 0%, #171c26 0%, transparent 60%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--text-primary);
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            padding: 36px 32px;
            box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.6);
        }

        .brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-logo {
            width: 52px;
            height: 52px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #171b24;
            border: 1px solid #232a38;
            border-radius: 14px;
            padding: 8px;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-title {
            font-size: 20px;
            font-weight: 600;
            letter-spacing: -0.3px;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .brand-sub {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .alert-msg {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #f87171;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.25);
            color: #4ade80;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 7px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .forgot-link {
            font-size: 12px;
            color: var(--gold);
            text-decoration: none;
            transition: color 0.15s;
        }

        .forgot-link:hover {
            color: var(--gold-hover);
            text-decoration: underline;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input {
            width: 100%;
            height: 44px;
            padding: 0 40px 0 14px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 10px;
            color: var(--text-primary);
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-input:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px rgba(201, 162, 77, 0.15);
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .toggle-btn {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: color 0.15s;
        }

        .toggle-btn:hover {
            color: var(--text-secondary);
        }

        .submit-btn {
            width: 100%;
            height: 44px;
            margin-top: 6px;
            background: var(--gold);
            color: #0b0d12;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.15s, transform 0.1s;
        }

        .submit-btn:hover {
            background: var(--gold-hover);
        }

        .submit-btn:active {
            transform: scale(0.99);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .footer-note {
            margin-top: 24px;
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand">
            <div class="brand-logo">
                <img src="https://crowncarz.com/admin/public/images/logo.png" 
                     alt="CrownCarz"
                     onerror="this.onerror=null; this.src='{{ asset('public/images/logo.png') }}';">
            </div>
            <h1 class="brand-title">Crown Carz</h1>
            <p class="brand-sub">Sign in to Admin Dashboard</p>
        </div>

        @if(session('error'))
            <div class="alert-msg alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="alert-msg alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert-msg alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.save') }}" id="loginForm">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email address</label>
                <div class="input-wrapper" style="margin-top: 7px;">
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-input" 
                           style="padding-right: 14px;"
                           placeholder="admin@crowncarz.com" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="email" 
                           autofocus>
                </div>
            </div>

            <div class="form-group">
                <div class="form-label-row">
                    <label class="form-label" for="password">Password</label>
                    <a href="mailto:info@crowncarz.com?subject=Admin%20Password%20Reset" class="forgot-link">Forgot?</a>
                </div>
                <div class="input-wrapper">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-input" 
                           placeholder="••••••••" 
                           required 
                           autocomplete="current-password">
                    <button type="button" class="toggle-btn" id="togglePwd" title="Show/Hide password">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <span id="btnText">Sign in</span>
            </button>
        </form>

        <div class="footer-note">
            Crown Carz Ltd &copy; {{ date('Y') }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const togglePwd = document.getElementById('togglePwd');
            const pwdInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');

            if (togglePwd && pwdInput && toggleIcon) {
                togglePwd.addEventListener('click', () => {
                    const isPassword = pwdInput.type === 'password';
                    pwdInput.type = isPassword ? 'text' : 'password';
                    toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            }

            if (form && submitBtn) {
                form.addEventListener('submit', () => {
                    submitBtn.disabled = true;
                    btnText.textContent = 'Signing in...';
                });
            }
        });
    </script>
</body>
</html>