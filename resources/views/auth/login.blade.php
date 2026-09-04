<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CrownCarz | Login</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --bg-dark: #0b0b0b;
    --gold: #c9a24d;
    --gold-soft: rgba(201,162,77,0.4);
    --glass-border: rgba(255,255,255,0.18);
    --text-muted: rgba(255,255,255,0.65);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background:
        radial-gradient(circle at top, rgba(201,162,77,0.18), transparent 45%),
        linear-gradient(135deg, #000000, #111111);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
}

/* ================= MAIN CARD ================= */
.login-wrapper {
    width: 100%;
    max-width: 900px;
    background: linear-gradient(
        180deg,
        rgba(255,255,255,0.12),
        rgba(255,255,255,0.04)
    );
    backdrop-filter: blur(25px);
    border-radius: 26px;
    border: 1px solid var(--glass-border);
    display: grid;
    grid-template-columns: 1fr 1fr;
    box-shadow: 0 40px 90px rgba(0,0,0,0.65);
    overflow: hidden;
}

/* ================= LEFT BRAND ================= */
.brand-panel {
    display: flex;
    flex-direction: column;
    /*justify-content: center;*/
    align-items: center;
    padding: 30px;
    background:
        radial-gradient(circle at top, rgba(201,162,77,0.25), transparent 55%);
}

.brand-panel img {
    width: 20px;
    /*margin-bottom: 20px;*/
    zoom: 12;
}

.brand-panel h2 {
    font-size: 1.7rem;
    letter-spacing: 0.6px;
}

.brand-panel p {
    margin-top: 10px;
    color: var(--text-muted);
    font-size: 0.95rem;
}

/* divider */
.brand-panel::after {
    content: "";
    position: absolute;
    right: 50%;
    top: 10%;
    bottom: 10%;
    width: 1px;
    background: rgba(255,255,255,0.15);
}

/* ================= RIGHT FORM ================= */
.form-panel {
    padding: 60px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.form-panel h3 {
    font-size: 1.2rem;
    margin-bottom: 8px;
    text-align: center;
}

.form-panel span {
    color: var(--text-muted);
    font-size: 0.9rem;
    margin-bottom: 30px;
    text-align: center;
}

/* ================= INPUTS ================= */
.form-group {
    margin-bottom: 22px;
    position: relative;
}

.input-wrapper {
    position: relative;
}

.input-wrapper input {
    width: 100%;
    padding: 16px;
    border-radius: 14px;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.25);
    color: white;
    outline: none;
    font-size: 14px;
}

.input-wrapper label {
    position: absolute;
    top: 50%;
    left: 16px;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 13px;
    padding: 0 6px;
    transition: 0.25s;
    pointer-events: none;
}

.input-wrapper input:focus {
    border-color: var(--gold);
    box-shadow: 0 0 0 2px var(--gold-soft);
}

.input-wrapper input:focus + label,
.input-wrapper input:not(:placeholder-shown) + label {
    top: -9px;
    font-size: 11px;
    color: var(--gold);
}

/* ================= BUTTON ================= */
.login-btn {
    margin-top: 10px;
    width: 100%;
    padding: 15px;
    border-radius: 14px;
    border: none;
    background: linear-gradient(135deg, #e1bb66, #b38a32);
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 15px 35px rgba(201,162,77,0.45);
    transition: 0.3s;
}

.login-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 25px 55px rgba(201,162,77,0.6);
}

.forgot {
    margin-top: 14px;
    text-align: center;
    font-size: 13px;
    color: var(--text-muted);
}

/* ================= MOBILE ================= */
@media (max-width: 768px) {
    .login-wrapper {
        grid-template-columns: 1fr;
    }

    .brand-panel::after {
        display: none;
    }

    .brand-panel {
        padding: 40px;
    }

    .form-panel {
        padding: 40px;
    }
}
</style>
</head>

<body>

<div class="login-wrapper">

    <!-- LEFT -->
    <div class="brand-panel">
        <img src="https://crowncarz.com/admin/public/images/logo.png" alt="CrownCarz">
        <h2>CrownCarz</h2>
    </div>

    <!-- RIGHT -->
    <div class="form-panel">
        <h3>Welcome</h3>
        <span>Please login to admin dashboard</span>

        <form method="POST" action="{{ route('login.save') }}">
            @csrf

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="email" name="email" required placeholder=" ">
                    <label>Username</label>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="password" name="password" required placeholder=" ">
                    <label>Password</label>
                </div>
            </div>

            <button class="login-btn">Login</button>

            <div class="forgot">
                Forgotten your password?
            </div>
        </form>
    </div>

</div>

</body>
</html>