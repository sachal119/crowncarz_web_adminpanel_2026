<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Crown Carz | Executive Admin Portal</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --gold-primary: #d4af37;
            --gold-light: #f3e5ab;
            --gold-dark: #997819;
            --gold-gradient: linear-gradient(135deg, #f3e5ab 0%, #d4af37 50%, #aa7c11 100%);
            --gold-glow: rgba(212, 175, 55, 0.35);
            
            --bg-base: #06080c;
            --card-bg: rgba(15, 20, 28, 0.78);
            --card-border: rgba(212, 175, 55, 0.2);
            --input-bg: rgba(255, 255, 255, 0.04);
            --input-border: rgba(255, 255, 255, 0.12);
            
            --text-main: #ffffff;
            --text-muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-base);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            overflow-x: hidden;
            position: relative;
            padding: 24px;
        }

        /* Ambient luxury background effects */
        .ambient-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .glow-sphere {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.45;
            animation: pulseGlow 10s ease-in-out infinite alternate;
        }

        .sphere-1 {
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.28) 0%, transparent 70%);
            top: -120px;
            left: 15%;
        }

        .sphere-2 {
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(170, 124, 17, 0.2) 0%, transparent 70%);
            bottom: -150px;
            right: 15%;
            animation-delay: -5s;
        }

        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(circle at center, black 40%, transparent 80%);
            -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 80%);
        }

        @keyframes pulseGlow {
            0% { transform: scale(1) translate(0, 0); opacity: 0.35; }
            50% { transform: scale(1.1) translate(20px, 15px); opacity: 0.55; }
            100% { transform: scale(0.95) translate(-15px, -10px); opacity: 0.35; }
        }

        /* Main Container Card */
        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 960px;
            min-height: 580px;
            background: var(--card-bg);
            backdrop-filter: blur(32px);
            -webkit-backdrop-filter: blur(32px);
            border-radius: 28px;
            border: 1px solid var(--card-border);
            box-shadow: 
                0 0 0 1px rgba(255, 255, 255, 0.05) inset,
                0 30px 90px -15px rgba(0, 0, 0, 0.85),
                0 0 60px -15px rgba(212, 175, 55, 0.15);
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            animation: cardAppear 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Left Branding Panel */
        .brand-panel {
            background: linear-gradient(165deg, rgba(212, 175, 55, 0.12) 0%, rgba(10, 14, 22, 0.8) 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.07);
            padding: 50px 42px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--gold-gradient);
            opacity: 0.8;
        }

        .brand-top {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 10px #10b981;
            animation: statusBlink 2s infinite;
        }

        @keyframes statusBlink {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .brand-center {
            text-align: center;
            padding: 30px 0;
        }

        .logo-box {
            width: 96px;
            height: 96px;
            margin: 0 auto 24px;
            background: radial-gradient(circle at 35% 30%, #1e2638, #0a0d14);
            border-radius: 24px;
            border: 1.5px solid rgba(212, 175, 55, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                0 16px 35px -8px rgba(0, 0, 0, 0.7),
                0 0 35px -5px rgba(212, 175, 55, 0.3);
            position: relative;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .logo-box:hover {
            transform: translateY(-4px) scale(1.03);
        }

        .logo-box img {
            max-width: 62px;
            max-height: 62px;
            object-fit: contain;
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.5));
        }

        .brand-title {
            font-family: 'Outfit', sans-serif;
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 6px;
        }

        .brand-subtitle {
            color: var(--text-muted);
            font-size: 13.5px;
            font-weight: 500;
            line-height: 1.5;
            max-width: 280px;
            margin: 0 auto;
        }

        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12.5px;
            color: #cbd5e1;
            font-weight: 500;
        }

        .feature-item i {
            color: var(--gold-primary);
            font-size: 15px;
        }

        /* Right Form Panel */
        .form-panel {
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.3px;
            margin-bottom: 6px;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 13.5px;
        }

        /* Alert notifications */
        .alert-box {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
            animation: fadeIn 0.3s ease;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        /* Input Controls */
        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 8px;
            letter-spacing: 0.2px;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: #64748b;
            font-size: 16px;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .input-field {
            width: 100%;
            height: 50px;
            padding: 0 46px 0 46px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 14px;
            color: #ffffff;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: all 0.25s ease;
        }

        .input-field:focus {
            background: rgba(255, 255, 255, 0.07);
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.18);
        }

        .input-field:focus ~ .input-icon {
            color: var(--gold-primary);
        }

        .input-toggle-pwd {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            color: #64748b;
            font-size: 16px;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .input-toggle-pwd:hover {
            color: #cbd5e1;
        }

        /* Options Row */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 26px;
            font-size: 13px;
        }

        .remember-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            color: #94a3b8;
        }

        .remember-wrap input[type="checkbox"] {
            accent-color: var(--gold-primary);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .forgot-link {
            color: var(--gold-primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s, text-decoration 0.2s;
        }

        .forgot-link:hover {
            color: var(--gold-light);
            text-decoration: underline;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            height: 52px;
            border-radius: 14px;
            border: none;
            background: var(--gold-gradient);
            color: #0b0f17;
            font-family: 'Outfit', sans-serif;
            font-size: 15.5px;
            font-weight: 700;
            letter-spacing: 0.3px;
            cursor: pointer;
            box-shadow: 0 10px 28px -4px var(--gold-glow);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 36px -4px rgba(212, 175, 55, 0.55);
        }

        .submit-btn:hover::after {
            left: 100%;
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Footer Info */
        .form-footer {
            margin-top: 26px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .form-footer i {
            color: #10b981;
            font-size: 13px;
        }

        /* Responsive Breakpoints */
        @media (max-width: 820px) {
            .login-card {
                grid-template-columns: 1fr;
                max-width: 480px;
                min-height: auto;
            }

            .brand-panel {
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
                padding: 36px 28px;
            }

            .brand-features {
                display: none;
            }

            .form-panel {
                padding: 36px 28px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 14px;
            }

            .login-card {
                border-radius: 22px;
            }

            .brand-panel, .form-panel {
                padding: 28px 20px;
            }

            .brand-title {
                font-size: 24px;
            }

            .form-header h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    <!-- Ambient Glow Effects -->
    <div class="ambient-bg">
        <div class="glow-sphere sphere-1"></div>
        <div class="glow-sphere sphere-2"></div>
        <div class="grid-pattern"></div>
    </div>

    <!-- Main Card -->
    <div class="login-card">

        <!-- Left Brand Presentation -->
        <div class="brand-panel">
            <div class="brand-top">
                <div class="status-pill">
                    <div class="status-dot"></div>
                    <span>System Active</span>
                </div>
            </div>

            <div class="brand-center">
                <div class="logo-box">
                    <img src="https://crowncarz.com/admin/public/images/logo.png" 
                         alt="Crown Carz" 
                         onerror="this.onerror=null; this.src='{{ asset('public/images/logo.png') }}';">
                </div>
                <h2 class="brand-title">Crown Carz</h2>
                <p class="brand-subtitle">Executive Fleet & Private Hire Dispatch Management System</p>
            </div>

            <div class="brand-features">
                <div class="feature-item">
                    <i class="bi bi-shield-check"></i>
                    <span>Authorized Administrative Personnel Only</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-speedometer2"></i>
                    <span>Real-time Live Dispatch & Fleet Radar</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-lock"></i>
                    <span>Protected with End-to-End Encryption</span>
                </div>
            </div>
        </div>

        <!-- Right Login Form -->
        <div class="form-panel">
            <div class="form-header">
                <h1>Welcome Back</h1>
                <p>Sign in to access your administrative dashboard</p>
            </div>

            <!-- Error / Status Alerts -->
            @if(session('error'))
                <div class="alert-box alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="alert-box alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-box alert-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.save') }}" id="loginForm">
                @csrf

                <!-- Email Input -->
                <div class="input-group">
                    <label class="input-label" for="adminEmail">Email Address or Username</label>
                    <div class="input-box">
                        <i class="bi bi-envelope-at input-icon"></i>
                        <input type="email" 
                               id="adminEmail" 
                               name="email" 
                               class="input-field" 
                               placeholder="admin@crowncarz.com" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="email" 
                               autofocus>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="input-group">
                    <label class="input-label" for="adminPassword">Password</label>
                    <div class="input-box">
                        <i class="bi bi-shield-lock input-icon"></i>
                        <input type="password" 
                               id="adminPassword" 
                               name="password" 
                               class="input-field" 
                               placeholder="••••••••••••" 
                               required 
                               autocomplete="current-password">
                        <button type="button" class="input-toggle-pwd" id="togglePasswordBtn" title="Show / Hide Password" aria-label="Toggle password visibility">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Options: Remember & Forgot -->
                <div class="form-options">
                    <label class="remember-wrap">
                        <input type="checkbox" name="remember" id="rememberMe">
                        <span>Remember me</span>
                    </label>
                    <a href="mailto:info@crowncarz.com?subject=CrownCarz%20Admin%20Password%20Reset%20Request" class="forgot-link">
                        Forgotten password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn" id="submitBtn">
                    <span id="btnText">Sign In to Dashboard</span>
                    <i class="bi bi-arrow-right" id="btnIcon"></i>
                </button>

                <div class="form-footer">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>256-bit SSL Secure Enterprise Gateway</span>
                </div>
            </form>
        </div>

    </div>

    <!-- Script for Interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const pwdInput = document.getElementById('adminPassword');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');

            // Toggle password visibility
            if (toggleBtn && pwdInput && toggleIcon) {
                toggleBtn.addEventListener('click', () => {
                    const isPwd = pwdInput.type === 'password';
                    pwdInput.type = isPwd ? 'text' : 'password';
                    toggleIcon.className = isPwd ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            }

            // Button loading feedback on form submit
            if (form && submitBtn) {
                form.addEventListener('submit', () => {
                    submitBtn.disabled = true;
                    btnText.textContent = 'Authenticating...';
                    btnIcon.className = 'spinner-border spinner-border-sm';
                });
            }
        });
    </script>
</body>
</html>