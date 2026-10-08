<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <title>Crown Carz Panel</title>
  <link rel="icon" type="image/png" href="https://crowncarz.com/admin/public/images/logo.png">
  <link rel="shortcut icon" type="image/png" href="{{ asset('public/images/logo.png') }}">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />

  <!-- Early Theme Initialization to Prevent Flash -->
  <script>
    (function() {
      try {
        const savedTheme = localStorage.getItem('crowncarz_theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
        if (savedTheme === 'dark') {
          document.documentElement.classList.add('dark-mode');
        } else {
          document.documentElement.classList.remove('dark-mode');
        }
      } catch (e) {}
    })();
  </script>

  <style>
    /* ==========================================================
       🌟 CROWN CARZ THEME SYSTEM (LIGHT & DARK MODE)
       ========================================================== */
    :root {
      --cc-bg-main: #f8f9fa;
      --cc-bg-card: #ffffff;
      --cc-bg-card-subtle: #f8fafc;
      --cc-border: #e2e8f0;
      --cc-border-subtle: #eaedf1;
      --cc-text-primary: #1e293b;
      --cc-text-secondary: #64748b;
      --cc-text-muted: #94a3b8;
      --cc-input-bg: #ffffff;
      --cc-input-border: #ced4da;
      --cc-input-color: #1e293b;
      --cc-gold: #E6B04A;
      --cc-gold-hover: #d49a37;
      --cc-table-th-bg: #f8fafc;
      --cc-table-th-color: #475569;
      --cc-table-hover: #f1f5f9;
      --cc-modal-bg: #ffffff;
    }

    [data-bs-theme="dark"] {
      --cc-bg-main: #0b0f19;
      --cc-bg-card: #151d2c;
      --cc-bg-card-subtle: #1a2333;
      --cc-border: rgba(255, 255, 255, 0.08);
      --cc-border-subtle: rgba(255, 255, 255, 0.06);
      --cc-text-primary: #f8fafc;
      --cc-text-secondary: #cbd5e1;
      --cc-text-muted: #94a3b8;
      --cc-input-bg: #0d131f;
      --cc-input-border: rgba(255, 255, 255, 0.16);
      --cc-input-color: #f8fafc;
      --cc-gold: #E6B04A;
      --cc-gold-hover: #d49a37;
      --cc-table-th-bg: #0d131f;
      --cc-table-th-color: #94a3b8;
      --cc-table-hover: rgba(255, 255, 255, 0.04);
      --cc-modal-bg: #151d2c;
    }

    *, *::before, *::after {
      box-sizing: border-box;
    }
    html, body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      background-color: var(--cc-bg-main);
      color: var(--cc-text-primary);
      font-size: 14px;
      -webkit-font-smoothing: antialiased;
      transition: background-color 0.25s ease, color 0.25s ease;
    }
    body {
      display: flex;
      flex-direction: column;
    }
    .navbar-custom {
      background: #111827;
      min-height: 48px;
      padding-top: 2px;
      padding-bottom: 2px;
      position: sticky;
      top: 0;
      z-index: 1020 !important;
      width: 100%;
      flex-shrink: 0;
      box-shadow: 0 4px 16px rgba(0,0,0,0.25);
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .modal-backdrop {
      z-index: 1080 !important;
    }
    .modal {
      z-index: 1085 !important;
    }
    .navbar-custom .navbar-brand {
      color: #fff;
      padding-top: 0;
      padding-bottom: 0;
    }
    .navbar-custom .nav-link {
      color: rgba(255, 255, 255, 0.88) !important;
      font-size: 12.5px;
      font-weight: 500;
      padding: 5px 10px !important;
      border-radius: 7px;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      border: 1px solid transparent;
    }
    .navbar-custom .nav-link:hover {
      color: #E6B04A !important;
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(230, 176, 74, 0.25);
    }
    .navbar-custom .nav-link.active {
      color: #ffffff !important;
      background: rgba(230, 176, 74, 0.22) !important;
      border: 1px solid rgba(230, 176, 74, 0.6) !important;
      font-weight: 700;
      box-shadow: 0 0 12px rgba(230, 176, 74, 0.18);
    }
    .navbar-custom .nav-link.active i {
      color: #E6B04A !important;
    }
    .navbar-custom .dropdown-item.active,
    .navbar-custom .dropdown-item:active {
      background-color: #E6B04A !important;
      color: #111827 !important;
      font-weight: 600;
    }
    .navbar-custom .dropdown-menu {
      display: none;
      position: absolute !important;
      will-change: transform;
      z-index: 1070;
    }
    .navbar-custom .nav-item.dropdown:hover > .dropdown-menu,
    .navbar-custom .dropdown-menu.show {
      display: block !important;
      margin-top: 0;
    }
    .main-content {
      flex: 1 0 auto;
      padding: 16px 20px;
      width: 100%;
    }

    @if(request()->routeIs('live.map'))
    html, body {
      height: 100vh !important;
      overflow: hidden !important;
    }
    .main-content {
      padding: 0 !important;
      margin: 0 !important;
      height: calc(100vh - 48px) !important;
      overflow: hidden !important;
      width: 100% !important;
      max-width: 100% !important;
    }
    .main-content > .container-fluid {
      padding: 0 !important;
      margin: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
      height: 100% !important;
    }
    @endif

    /* Responsive font scaling */
    @media (max-width: 1200px) { html { font-size: 13.5px; } }
    @media (max-width: 768px) { html { font-size: 13px; } }

    table { font-size: 0.95rem; }
    .small { font-size: 0.9rem !important; }
    h1, h2, h3, h4, h5, h6 { line-height: 1.2; }
    th { font-size: 12px; }
    td { font-size: 12px; }

    /* Modern Dashboard Aesthetics */
    .dashboard-section {
      padding: 1.5rem;
      border-radius: 12px;
    }
    .dashboard-card {
      border: none;
      border-radius: 16px;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      position: relative;
      overflow: hidden;
    }
    .dashboard-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    .bg-success { background: linear-gradient(135deg, #16a085, #1abc9c) !important; }
    .bg-info { background: linear-gradient(135deg, #3498db, #5dade2) !important; }
    .bg-danger { background: linear-gradient(135deg, #e74c3c, #ff7675) !important; }
    .bg-secondary { background: linear-gradient(135deg, #7f8c8d, #95a5a6) !important; }
    .bg-primary { background: linear-gradient(135deg, #2e86de, #54a0ff) !important; }
    .bg-dark { background: linear-gradient(135deg, #2f3640, #353b48) !important; }
    .dashboard-card .card-body { padding: 1.3rem 1rem; }
    .dashboard-card h1 { font-size: 2rem; margin-bottom: 0.4rem; }
    .dashboard-card h4 { font-size: 1.6rem; font-weight: 700; }
    .dashboard-card p { font-size: 0.9rem; opacity: 0.9; }
    .card-header { border-bottom: 1px solid #e3e6f0; font-weight: 600; }
    #revenuePieChart { max-height: 240px; }

    /* Flight Tracker Badge Styling */
    .flight-track-badge {
      display: inline-flex !important;
      align-items: center !important;
      gap: 4px !important;
      background-color: #f1f5f9 !important;
      color: #0369a1 !important;
      border: 1px solid #bae6fd !important;
      border-radius: 6px !important;
      padding: 3px 8px !important;
      font-size: 11px !important;
      font-weight: 600 !important;
      text-decoration: none !important;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
      box-shadow: 0 1px 2px rgba(0,0,0,0.04) !important;
      cursor: pointer !important;
    }
    .flight-track-badge:hover {
      background-color: #e0f2fe !important;
      color: #0284c7 !important;
      border-color: #38bdf8 !important;
      box-shadow: 0 3px 8px rgba(14, 165, 233, 0.25) !important;
      transform: translateY(-1px);
    }
    .flight-track-badge .flight-icon {
      color: #0284c7;
      font-size: 10.5px;
      transition: transform 0.2s ease;
    }
    .flight-track-badge:hover .flight-icon {
      transform: rotate(-15deg);
    }
    .flight-track-badge .flight-ext-icon {
      font-size: 8.5px;
      color: #0284c7;
      opacity: 0.7;
      transition: opacity 0.2s ease;
    }
    .flight-track-badge:hover .flight-ext-icon {
      opacity: 1;
    }

    /* ==========================================================
       🌓 THEME TOGGLE SWITCH BUTTON STYLING
       ========================================================== */
    .theme-switch-wrapper {
      display: inline-flex;
      align-items: center;
    }
    .theme-toggle-btn {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.16);
      border-radius: 30px;
      padding: 3px 8px 3px 4px;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      cursor: pointer;
      transition: all 0.25s ease;
      color: rgba(255, 255, 255, 0.9);
      font-size: 11.5px;
      font-weight: 600;
      outline: none;
      user-select: none;
    }
    .theme-toggle-btn:hover {
      background: rgba(255, 255, 255, 0.14);
      border-color: #E6B04A;
      color: #E6B04A;
      box-shadow: 0 0 12px rgba(230, 176, 74, 0.25);
    }
    .theme-toggle-track {
      position: relative;
      width: 40px;
      height: 22px;
      background: #1f2937;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 5px;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.4);
      border: 1px solid rgba(255, 255, 255, 0.12);
      transition: all 0.25s ease;
    }
    .theme-icon-sun {
      font-size: 10.5px;
      color: #f59e0b;
      z-index: 1;
      transition: transform 0.25s ease, opacity 0.25s ease;
    }
    .theme-icon-moon {
      font-size: 10px;
      color: #93c5fd;
      z-index: 1;
      transition: transform 0.25s ease, opacity 0.25s ease;
    }
    .theme-toggle-thumb {
      position: absolute;
      top: 2px;
      left: 2px;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      background: linear-gradient(135deg, #E6B04A 0%, #d48b48 100%);
      box-shadow: 0 1px 3px rgba(0,0,0,0.3);
      transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1), background 0.25s ease;
    }

    /* Dark Mode Active States for Switch */
    [data-bs-theme="dark"] .theme-toggle-track {
      background: #090d16;
      border-color: rgba(230, 176, 74, 0.35);
    }
    [data-bs-theme="dark"] .theme-toggle-thumb {
      transform: translateX(18px);
      background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
    }
    [data-bs-theme="dark"] .theme-toggle-btn {
      border-color: rgba(96, 165, 250, 0.35);
    }
    [data-bs-theme="dark"] .theme-toggle-btn:hover {
      border-color: #60a5fa;
      color: #60a5fa;
      box-shadow: 0 0 12px rgba(96, 165, 250, 0.25);
    }

    /* ==========================================================
       🌑 DARK MODE SYSTEM-WIDE OVERRIDES
       ========================================================== */
    [data-bs-theme="dark"] {
      color-scheme: dark;
    }

    [data-bs-theme="dark"] body {
      background-color: #0b0f19 !important;
      color: #e2e8f0 !important;
    }

    /* Cards & Panels */
    [data-bs-theme="dark"] .card,
    [data-bs-theme="dark"] .glass-panel,
    [data-bs-theme="dark"] .dashboard-section > .card {
      background-color: #151d2c !important;
      border-color: rgba(255, 255, 255, 0.08) !important;
      color: #f8fafc !important;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35) !important;
    }

    [data-bs-theme="dark"] .card-header {
      background-color: rgba(13, 19, 31, 0.8) !important;
      border-bottom-color: rgba(255, 255, 255, 0.08) !important;
      color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .card-footer {
      background-color: rgba(13, 19, 31, 0.8) !important;
      border-top-color: rgba(255, 255, 255, 0.08) !important;
      color: #cbd5e1 !important;
    }

    /* Generic Light/White BG Overrides */
    [data-bs-theme="dark"] .bg-white,
    [data-bs-theme="dark"] [style*="background: #ffffff"],
    [data-bs-theme="dark"] [style*="background:#ffffff"],
    [data-bs-theme="dark"] [style*="background: white"],
    [data-bs-theme="dark"] [style*="background:white"] {
      background-color: #151d2c !important;
      color: #e2e8f0 !important;
    }

    [data-bs-theme="dark"] .bg-light,
    [data-bs-theme="dark"] [style*="background: #f8f9fa"],
    [data-bs-theme="dark"] [style*="background:#f8f9fa"],
    [data-bs-theme="dark"] [style*="background: #f8fafc"],
    [data-bs-theme="dark"] [style*="background:#f8fafc"],
    [data-bs-theme="dark"] [style*="background: #fbf7f2"] {
      background-color: #0d131f !important;
      color: #cbd5e1 !important;
    }

    /* Typography & Text */
    [data-bs-theme="dark"] .text-dark,
    [data-bs-theme="dark"] h1,
    [data-bs-theme="dark"] h2,
    [data-bs-theme="dark"] h3,
    [data-bs-theme="dark"] h4,
    [data-bs-theme="dark"] h5,
    [data-bs-theme="dark"] h6 {
      color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .text-muted,
    [data-bs-theme="dark"] .text-secondary {
      color: #94a3b8 !important;
    }

    [data-bs-theme="dark"] label,
    [data-bs-theme="dark"] .form-label {
      color: #cbd5e1 !important;
    }

    /* Form Controls & Inputs */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] select,
    [data-bs-theme="dark"] textarea {
      background-color: #0d131f !important;
      color: #f8fafc !important;
      border: 1px solid rgba(255, 255, 255, 0.16) !important;
    }

    [data-bs-theme="dark"] .form-control:focus,
    [data-bs-theme="dark"] .form-select:focus,
    [data-bs-theme="dark"] select:focus,
    [data-bs-theme="dark"] textarea:focus {
      background-color: #0d131f !important;
      color: #ffffff !important;
      border-color: #E6B04A !important;
      box-shadow: 0 0 0 0.25rem rgba(230, 176, 74, 0.22) !important;
    }

    [data-bs-theme="dark"] .form-control::placeholder {
      color: #64748b !important;
    }

    [data-bs-theme="dark"] .input-group-text {
      background-color: #090d16 !important;
      color: #cbd5e1 !important;
      border-color: rgba(255, 255, 255, 0.16) !important;
    }

    /* Tables */
    [data-bs-theme="dark"] table,
    [data-bs-theme="dark"] .table,
    [data-bs-theme="dark"] .custom-dashboard-table {
      background-color: #111827 !important;
      color: #f1f5f9 !important;
    }

    [data-bs-theme="dark"] table thead th,
    [data-bs-theme="dark"] .custom-dashboard-table thead th {
      background: #090d16 !important;
      color: #94a3b8 !important;
      border-bottom: 2px solid rgba(255, 255, 255, 0.1) !important;
      border-top: none !important;
      font-weight: 700 !important;
    }

    [data-bs-theme="dark"] table tbody tr,
    [data-bs-theme="dark"] .custom-dashboard-table tbody tr {
      background-color: #111827 !important;
      color: #f1f5f9 !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
    }

    [data-bs-theme="dark"] table tbody td,
    [data-bs-theme="dark"] .custom-dashboard-table td {
      background-color: transparent !important;
      color: #e2e8f0 !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
    }

    [data-bs-theme="dark"] table tbody tr:hover,
    [data-bs-theme="dark"] .custom-dashboard-table tbody tr:hover {
      background-color: #1a2333 !important;
    }

    /* Specific table cell texts in dark mode for maximum readability */
    [data-bs-theme="dark"] .col-ref span,
    [data-bs-theme="dark"] .col-ref {
      color: #f8fafc !important;
      font-weight: 700 !important;
    }

    [data-bs-theme="dark"] .col-passenger span,
    [data-bs-theme="dark"] .col-passenger {
      color: #ffffff !important;
      font-weight: 600 !important;
    }

    [data-bs-theme="dark"] .col-phone a,
    [data-bs-theme="dark"] .col-phone {
      color: #e2e8f0 !important;
    }
    [data-bs-theme="dark"] .col-phone a:hover {
      color: #38bdf8 !important;
    }

    [data-bs-theme="dark"] .col-pickup span,
    [data-bs-theme="dark"] .col-dropoff span {
      color: #e2e8f0 !important;
    }

    [data-bs-theme="dark"] .col-date {
      color: #e2e8f0 !important;
    }

    [data-bs-theme="dark"] .col-time {
      color: #38bdf8 !important;
      font-weight: 700 !important;
    }

    /* 💰 High Value (> £50) & Normal Price Styling (Light Mode Defaults) */
    .high-value-row,
    .high-value-row > td,
    tr.high-value-row td,
    .custom-dashboard-table tbody tr.high-value-row td {
      background-color: #fef08a !important; /* Prominent, vibrant, and clear golden-amber row highlight */
      border-bottom: 1px solid #fde047 !important;
    }
    .high-value-row:hover,
    .high-value-row:hover > td,
    tr.high-value-row:hover td,
    .custom-dashboard-table tbody tr.high-value-row:hover td {
      background-color: #fde047 !important;
    }
    .high-value-row td:first-child,
    tr.high-value-row td:first-child {
      border-left: 5px solid #d97706 !important;
    }
    .high-value-row .text-dark,
    .high-value-row a.text-dark {
      color: #78350f !important;
      font-weight: 600;
    }

    .high-price-badge {
      display: inline-flex;
      align-items: center;
      background: #f59e0b;
      color: #ffffff;
      border: 1px solid #d97706;
      border-radius: 6px;
      padding: 2px 8px;
      font-weight: 800;
      font-size: 11.5px;
      letter-spacing: 0.2px;
      box-shadow: 0 2px 4px rgba(180, 83, 9, 0.25);
    }

    .normal-price-text {
      font-weight: 700;
      color: #1e293b;
      font-size: 12px;
    }

    /* 💰 High Value (> £50) & Price Styling (Dark Mode) */
    [data-bs-theme="dark"] .high-value-row,
    [data-bs-theme="dark"] .high-value-row > td,
    [data-bs-theme="dark"] tr.high-value-row td,
    [data-bs-theme="dark"] .custom-dashboard-table tbody tr.high-value-row td {
      background-color: #382813 !important; /* Rich prominent dark bronze/amber highlight */
      border-bottom: 1px solid rgba(245, 158, 11, 0.3) !important;
    }
    [data-bs-theme="dark"] .high-value-row:hover,
    [data-bs-theme="dark"] .high-value-row:hover > td,
    [data-bs-theme="dark"] tr.high-value-row:hover td,
    [data-bs-theme="dark"] .custom-dashboard-table tbody tr.high-value-row:hover td {
      background-color: #483418 !important;
    }
    [data-bs-theme="dark"] .high-value-row td:first-child,
    [data-bs-theme="dark"] tr.high-value-row td:first-child {
      border-left: 5px solid #f59e0b !important;
    }
    [data-bs-theme="dark"] .high-value-row td,
    [data-bs-theme="dark"] .high-value-row .text-dark,
    [data-bs-theme="dark"] .high-value-row a.text-dark {
      color: #fef3c7 !important;
    }

    [data-bs-theme="dark"] .high-price-badge {
      background: rgba(245, 158, 11, 0.3) !important;
      color: #fcd34d !important;
      border: 1px solid rgba(245, 158, 11, 0.6) !important;
      box-shadow: 0 0 12px rgba(245, 158, 11, 0.25) !important;
    }

    [data-bs-theme="dark"] .normal-price-text {
      color: #34d399 !important; /* Crisp emerald for standard prices */
      font-weight: 700;
      font-size: 12px;
    }

    [data-bs-theme="dark"] .col-comment span,
    [data-bs-theme="dark"] .truncate-cell {
      color: #94a3b8 !important;
    }

    [data-bs-theme="dark"] .unassigned-driver {
      background-color: #1e293b !important;
      color: #94a3b8 !important;
      border-color: rgba(255, 255, 255, 0.12) !important;
    }

    [data-bs-theme="dark"] .badge.bg-light {
      background-color: #1e293b !important;
      color: #e2e8f0 !important;
      border-color: rgba(255, 255, 255, 0.15) !important;
    }

    [data-bs-theme="dark"] .statusSelect {
      border-radius: 6px !important;
      font-weight: 600 !important;
    }

    [data-bs-theme="dark"] .table-striped>tbody>tr:nth-of-type(odd)>* {
      background-color: rgba(255, 255, 255, 0.02) !important;
      color: #e2e8f0 !important;
    }

    [data-bs-theme="dark"] .table-bordered td,
    [data-bs-theme="dark"] .table-bordered th {
      border-color: rgba(255, 255, 255, 0.08) !important;
    }

    /* 📅 Modern Timeline Date Divider & Time Badges (Light Mode) */
    .timeline-date-divider-row {
      border: none !important;
    }
    .timeline-date-divider-row td,
    .custom-dashboard-table tbody tr.timeline-date-divider-row td {
      padding: 0 !important;
      background: transparent !important;
      border: none !important;
    }
    .timeline-date-bar {
      background: linear-gradient(90deg, #f1f5f9 0%, #e2e8f0 35%, #f8fafc 100%);
      border-top: 1px solid #cbd5e1;
      border-bottom: 1px solid #cbd5e1;
      box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
    }
    .timeline-dot-icon {
      width: 10px;
      height: 10px;
      background: #f59e0b;
      border-radius: 50%;
      display: inline-block;
      box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25);
    }
    .timeline-date-pill {
      background: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 20px;
      padding: 3px 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.06);
      font-size: 11.5px;
      color: #0f172a;
    }
    .timeline-track-line {
      height: 2px;
      background: linear-gradient(90deg, #cbd5e1 0%, rgba(203, 213, 225, 0.2) 100%);
      border-radius: 2px;
    }
    .timeline-badge-subtle {
      font-size: 10.5px;
      font-weight: 600;
      color: #64748b;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      padding: 2px 8px;
    }
    .timeline-time-badge {
      display: inline-flex;
      align-items: center;
      background: #f1f5f9;
      color: #0f172a;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      padding: 2px 7px;
      font-size: 11.5px;
      font-weight: 700;
      letter-spacing: 0.3px;
    }
    .high-value-row .timeline-time-badge {
      background: #fef08a;
      border-color: #fde047;
      color: #78350f;
    }

    /* 🌙 Timeline Styling (Dark Mode) */
    [data-bs-theme="dark"] .timeline-date-bar {
      background: linear-gradient(90deg, #0b1320 0%, #1e293b 40%, #0f172a 100%);
      border-top: 1px solid #334155;
      border-bottom: 1px solid #334155;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.4);
    }
    [data-bs-theme="dark"] .timeline-dot-icon {
      background: #38bdf8;
      box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.25);
    }
    [data-bs-theme="dark"] .timeline-date-pill {
      background: #1e293b;
      border: 1px solid #475569;
      color: #f8fafc;
      box-shadow: 0 1px 4px rgba(0,0,0,0.4);
    }
    [data-bs-theme="dark"] .timeline-track-line {
      background: linear-gradient(90deg, #475569 0%, rgba(71, 85, 105, 0.15) 100%);
    }
    [data-bs-theme="dark"] .timeline-badge-subtle {
      color: #94a3b8;
      background: #1e293b;
      border: 1px solid #334155;
    }
    [data-bs-theme="dark"] .timeline-time-badge {
      background: #0f172a;
      color: #38bdf8;
      border: 1px solid #334155;
    }
    [data-bs-theme="dark"] .high-value-row .timeline-time-badge {
      background: #2b1f0e;
      border-color: rgba(245, 158, 11, 0.5);
      color: #fcd34d;
    }

    /* 🏷️ Pricing & Setup Screens Custom Theme Overrides */
    .table-header-custom {
      background-color: #f1f5f9;
      color: #1e293b;
      font-weight: 700;
    }
    .table-header-custom th {
      font-weight: 700;
      color: inherit;
    }
    [data-bs-theme="dark"] .table-header-custom {
      background-color: #090d16 !important;
      color: #94a3b8 !important;
    }
    [data-bs-theme="dark"] .table-header-custom th {
      background-color: #090d16 !important;
      color: #94a3b8 !important;
    }
    [data-bs-theme="dark"] .table-body-custom tr,
    [data-bs-theme="dark"] .table-body-custom td {
      background-color: transparent !important;
      color: #f1f5f9 !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
    }
    [data-bs-theme="dark"] .table-body-custom tr:hover {
      background-color: #1a2333 !important;
    }

    /* Catch-all dark overrides for inline legacy styles */
    [data-bs-theme="dark"] [style*="background-color: #EAFDFD"],
    [data-bs-theme="dark"] [style*="background-color:#EAFDFD"],
    [data-bs-theme="dark"] [style*="background-color: #FFF9E5"],
    [data-bs-theme="dark"] [style*="background-color:#FFF9E5"],
    [data-bs-theme="dark"] [style*="background-color: #FFFBE6"],
    [data-bs-theme="dark"] [style*="background-color:#FFFBE6"],
    [data-bs-theme="dark"] [style*="background-color: #FAF0C3"],
    [data-bs-theme="dark"] [style*="background-color:#FAF0C3"] {
      background-color: #151d2c !important;
      color: #f1f5f9 !important;
    }
    [data-bs-theme="dark"] [style*="background-color: #D7E0E6"],
    [data-bs-theme="dark"] [style*="background-color:#D7E0E6"],
    [data-bs-theme="dark"] [style*="background-color: #FAD788"],
    [data-bs-theme="dark"] [style*="background-color:#FAD788"],
    [data-bs-theme="dark"] [style*="background-color: #F3D166"],
    [data-bs-theme="dark"] [style*="background-color:#F3D166"] {
      background-color: #090d16 !important;
      color: #94a3b8 !important;
    }
    [data-bs-theme="dark"] [style*="color: #6B3E26"],
    [data-bs-theme="dark"] [style*="color:#6B3E26"],
    [data-bs-theme="dark"] [style*="color: #4B3621"],
    [data-bs-theme="dark"] [style*="color:#4B3621"] {
      color: #f8fafc !important;
    }
    [data-bs-theme="dark"] [style*="background-color: #6B3E26"],
    [data-bs-theme="dark"] [style*="background-color:#6B3E26"],
    [data-bs-theme="dark"] [style*="background-color: #B87333"],
    [data-bs-theme="dark"] [style*="background-color:#B87333"] {
      background-color: #0284c7 !important;
      color: #ffffff !important;
      border-color: #0284c7 !important;
    }

    /* Modals */
    [data-bs-theme="dark"] .modal-content {
      background-color: #151d2c !important;
      color: #f8fafc !important;
      border: 1px solid rgba(255, 255, 255, 0.12) !important;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65) !important;
    }

    [data-bs-theme="dark"] .modal-header,
    [data-bs-theme="dark"] .modal-footer {
      background-color: #0d131f !important;
      border-color: rgba(255, 255, 255, 0.08) !important;
    }

    [data-bs-theme="dark"] .btn-close {
      filter: invert(1) grayscale(100%) brightness(200%);
    }

    /* Dropdowns */
    [data-bs-theme="dark"] .dropdown-menu {
      background-color: #151d2c !important;
      border: 1px solid rgba(255, 255, 255, 0.12) !important;
      box-shadow: 0 15px 35px rgba(0,0,0,0.55) !important;
      color: #e2e8f0 !important;
    }

    [data-bs-theme="dark"] .dropdown-item {
      color: #e2e8f0 !important;
    }

    [data-bs-theme="dark"] .dropdown-item:hover,
    [data-bs-theme="dark"] .dropdown-item:focus {
      background-color: rgba(230, 176, 74, 0.18) !important;
      color: #E6B04A !important;
    }

    [data-bs-theme="dark"] .dropdown-divider {
      border-color: rgba(255, 255, 255, 0.08) !important;
    }

    [data-bs-theme="dark"] .dropdown-header {
      color: #E6B04A !important;
    }

    /* Tabs & Navs */
    [data-bs-theme="dark"] .nav-tabs {
      border-bottom-color: rgba(255, 255, 255, 0.1) !important;
    }

    [data-bs-theme="dark"] .nav-tabs .nav-link {
      color: #94a3b8 !important;
      border-color: transparent !important;
    }

    [data-bs-theme="dark"] .nav-tabs .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.05) !important;
      color: #E6B04A !important;
    }

    [data-bs-theme="dark"] .nav-tabs .nav-link.active {
      background: linear-gradient(135deg, #b5651d, #cf7925) !important;
      color: #ffffff !important;
    }

    /* Pagination */
    [data-bs-theme="dark"] .page-link {
      background-color: #151d2c !important;
      border-color: rgba(255, 255, 255, 0.1) !important;
      color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .page-item.active .page-link {
      background-color: #E6B04A !important;
      border-color: #E6B04A !important;
      color: #111827 !important;
      font-weight: 700;
    }

    /* Flatpickr Date Picker Dark Overrides */
    [data-bs-theme="dark"] .flatpickr-calendar {
      background: #151d2c !important;
      border: 1px solid rgba(255, 255, 255, 0.12) !important;
      box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6) !important;
    }

    [data-bs-theme="dark"] .flatpickr-calendar .flatpickr-day {
      color: #e2e8f0 !important;
    }

    [data-bs-theme="dark"] .flatpickr-calendar .flatpickr-day:hover:not(.selected):not(.startRange):not(.endRange) {
      background: rgba(230, 176, 74, 0.25) !important;
      color: #E6B04A !important;
    }

    /* Flight Track Badge */
    [data-bs-theme="dark"] .flight-track-badge {
      background-color: #082f49 !important;
      color: #7dd3fc !important;
      border-color: #0369a1 !important;
    }

    [data-bs-theme="dark"] .flight-track-badge .flight-icon,
    [data-bs-theme="dark"] .flight-track-badge .flight-ext-icon {
      color: #38bdf8 !important;
    }

    [data-bs-theme="dark"] .flight-track-badge:hover {
      background-color: #0c4a6e !important;
      color: #bae6fd !important;
      border-color: #38bdf8 !important;
      box-shadow: 0 3px 8px rgba(56, 189, 248, 0.3) !important;
    }

    /* Borders & Dividers */
    [data-bs-theme="dark"] .border,
    [data-bs-theme="dark"] .border-top,
    [data-bs-theme="dark"] .border-bottom,
    [data-bs-theme="dark"] .border-start,
    [data-bs-theme="dark"] .border-end,
    [data-bs-theme="dark"] .border-end-lg {
      border-color: rgba(255, 255, 255, 0.08) !important;
    }

    /* List Groups & Accordions */
    [data-bs-theme="dark"] .list-group-item {
      background-color: #151d2c !important;
      border-color: rgba(255, 255, 255, 0.08) !important;
      color: #e2e8f0 !important;
    }

    [data-bs-theme="dark"] .accordion-item {
      background-color: #151d2c !important;
      border-color: rgba(255, 255, 255, 0.08) !important;
    }

    [data-bs-theme="dark"] .accordion-button {
      background-color: #151d2c !important;
      color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .accordion-button:not(.collapsed) {
      background-color: #0d131f !important;
      color: #E6B04A !important;
    }

    [data-bs-theme="dark"] .accordion-body {
      background-color: #151d2c !important;
      color: #cbd5e1 !important;
    }
  </style>
</head>

@php
    // --- Dummy Data for New Components ---
    // (In your controller, you would pass these variables)
    $totalUsers = $totalUsers ?? 120; // Example: Pass $totalUsers from controller
    $todaysRevenue = $todaysRevenue ?? 450.75; // Example: Pass $todaysRevenue from controller
    $revenueBreakdown = $revenueBreakdown ?? [
        'Cash' => 150.25,
        'Card' => 200.50,
        'Account' => 100.00,
    ];
    // --- End Dummy Data ---

    // Pre-calculate driver counts
    $availableDrivers = $drivers->whereIn('status', ['Available', 'available'])->count();
    $waitingDrivers = $drivers->where('status', 'waiting')->count();
    $engagedDrivers = $drivers->where('status', 'engaged')->count();
    $onBreakDrivers = $drivers->where('status', 'On Break')->count();
@endphp

<body>
  <!-- Top Navbar -->
  <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid" style="
    margin-left: 16px;
    margin-right: 16px;
">
      <a class="navbar-brand mx-auto py-0" href="{{ route('dashboard') }}">
        <img src="{{ asset('public/images/logo.png') }}" alt="Crown Carz Logo" style="height: 34px; width: auto;">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-center" id="navbarContent">
        <ul class="navbar-nav">
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid-fill me-1"></i>Dashboard</a></li>
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('live.map') ? 'active' : '' }}" href="{{ route('live.map') }}"><i class="bi bi-geo-alt-fill me-1"></i>Live Driver Map</a></li>
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('booking.create') ? 'active' : '' }}" href="{{ route('booking.create') }}" target="_blank"><i class="bi bi-calendar-plus me-1"></i>Create Booking</a></li>
          @if(session('staff_role', 'super_admin') !== 'collaborator')
          <li class="nav-item dropdown me-2">
  <a class="nav-link dropdown-toggle" href="#" id="statsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-bar-chart-fill me-1"></i>Stats
  </a>

  <div class="dropdown-menu p-3 shadow-lg border-0" aria-labelledby="statsDropdown" style="min-width: 800px; border-radius: 16px;">
    <div class="row g-3">
      <!-- Left Section: Driver Stats -->
      <div class="col-lg-8 col-md-7">
        <div class="row g-3 mb-3">
          <div class="col-6 col-md-4">
            <div class="card dashboard-card bg-success text-white text-center">
              <div class="card-body py-3">
                <h1><i class="bi bi-check-circle"></i></h1>
                <h4>{{ $availableDrivers }}</h4>
                <p>Available</p>
              </div>
            </div>
          </div>

          <div class="col-6 col-md-4">
            <div class="card dashboard-card bg-info text-white text-center">
              <div class="card-body py-3">
                <h1><i class="bi bi-clock"></i></h1>
                <h4>{{ $waitingDrivers }}</h4>
                <p>Waiting</p>
              </div>
            </div>
          </div>

          <div class="col-6 col-md-4">
            <div class="card dashboard-card bg-danger text-white text-center">
              <div class="card-body py-3">
                <h1><i class="bi bi-car-front"></i></h1>
                <h4>{{ $engagedDrivers }}</h4>
                <p>Engaged</p>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-6 col-md-4">
            <div class="card dashboard-card bg-secondary text-white text-center">
              <div class="card-body py-3">
                <h1><i class="bi bi-cup-hot"></i></h1>
                <h4>{{ $onBreakDrivers }}</h4>
                <p>On Break</p>
              </div>
            </div>
          </div>

          <div class="col-6 col-md-4">
            <div class="card dashboard-card bg-primary text-white text-center">
              <div class="card-body py-3">
                <h1><i class="bi bi-people"></i></h1>
                <!--<h4>{{ $totalUsers }}</h4>-->
                <h4 id="totalUsersDisplay">0</h4>
                <p>Total Users</p>
              </div>
            </div>
          </div>

          <div class="col-6 col-md-4">
            <div class="card dashboard-card bg-dark text-white text-center">
              <div class="card-body py-3">
                <h1><i class="bi bi-cash-stack"></i></h1>
                <!--<h4>£{{ number_format($todaysRevenue, 2) }}</h4>-->
                <h4 id="todaysRevenueDisplay">£0.00</h4>
                <p>Today's Revenue</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Section: Revenue Pie Chart -->
      <div class="col-lg-4 col-md-5">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 16px;">
          <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-dark">📊 Revenue Breakdown</h6>
            <i class="bi bi-pie-chart text-muted fs-5"></i>
          </div>
          <div class="card-body d-flex justify-content-center align-items-center">
            <canvas id="revenuePieChart" width="200" height="200"></canvas>
          </div>
        </div>
      </div>
    </div>
          </div>
</li>
          @endif
          
          <!-- History Dropdown -->
          <li class="nav-item dropdown me-2">
              <a class="nav-link dropdown-toggle {{ request()->routeIs('completed.jobs', 'bookings.search', 'previous.bookings', 'previous.bookings.search', 'bookings.cancelled', 'cancelled.bookings.search') ? 'active' : '' }}" href="#" id="historyDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-clock-history me-1"></i>History
              </a>
              <ul class="dropdown-menu shadow-lg border-0" aria-labelledby="historyDropdown" style="border-radius: 12px; min-width: 250px;">
                <li>
                  <a class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ request()->routeIs('completed.jobs', 'bookings.search') ? 'active' : '' }}" href="{{ route('completed.jobs') }}">
                    <span class="d-flex align-items-center"><i class="bi bi-check-circle-fill me-2 text-success"></i>Completed Bookings</span>
                    <span class="badge rounded-pill bg-success text-white ms-2 px-2 py-1" id="navHistoryCompletedCount" style="font-size: 11px; font-weight: 600;">-</span>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ request()->routeIs('previous.bookings', 'previous.bookings.search') ? 'active' : '' }}" href="{{ route('previous.bookings') }}">
                    <span class="d-flex align-items-center"><i class="bi bi-calendar-check-fill me-2 text-warning"></i>Previous Bookings</span>
                    <span class="badge rounded-pill bg-warning text-dark ms-2 px-2 py-1" id="navHistoryPreviousCount" style="font-size: 11px; font-weight: 600;">-</span>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ request()->routeIs('bookings.cancelled', 'cancelled.bookings.search') ? 'active' : '' }}" href="{{ route('bookings.cancelled') }}">
                    <span class="d-flex align-items-center"><i class="bi bi-x-circle-fill me-2 text-danger"></i>Cancelled Bookings</span>
                    <span class="badge rounded-pill bg-danger text-white ms-2 px-2 py-1" id="navHistoryCancelledCount" style="font-size: 11px; font-weight: 600;">-</span>
                  </a>
                </li>
              </ul>
          </li>
          
          <!-- Messages Dropdown -->
@if(session('staff_role', 'super_admin') !== 'collaborator')
          <li class="nav-item dropdown me-2">
              <a class="nav-link dropdown-toggle {{ request()->routeIs('messages.*', 'messages.all') ? 'active' : '' }}" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-chat-dots-fill me-1"></i>Messages
              </a>
              <ul class="dropdown-menu shadow-lg border-0" aria-labelledby="messagesDropdown" style="border-radius: 12px;">
                <li><h6 class="dropdown-header">Customer</h6></li>
                <li><a class="dropdown-item {{ request()->routeIs('messages.customer.booking') ? 'active' : '' }}" href="{{ route('messages.customer.booking') }}">Booking Confirmation SMS</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('messages.customer.onroute') ? 'active' : '' }}" href="{{ route('messages.customer.onroute') }}">Onroute SMS</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('messages.customer.arrival') ? 'active' : '' }}" href="{{ route('messages.customer.arrival') }}">Arrival SMS</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('messages.customer.complete') ? 'active' : '' }}" href="{{ route('messages.customer.complete') }}">Job Complete SMS</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header">Driver</h6></li>
                <li><a class="dropdown-item {{ request()->routeIs('messages.driver.details') ? 'active' : '' }}" href="{{ route('messages.driver.details') }}">Job Details SMS</a></li>
              </ul>
          </li>
          <li class="nav-item dropdown me-2">
              <a class="nav-link dropdown-toggle {{ (request()->routeIs('pricing.*') || request()->routeIs('locations.*')) ? 'active' : '' }}" href="#" id="pricingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-currency-pound me-1"></i>Pricing
              </a>
              <ul class="dropdown-menu shadow-lg border-0" aria-labelledby="pricingDropdown" style="border-radius: 12px;">
                <li><a class="dropdown-item {{ request()->routeIs('pricing.fixed') ? 'active' : '' }}" href="{{ route('pricing.fixed') }}">Fixed Pricing</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pricing.mileage') ? 'active' : '' }}" href="{{ route('pricing.mileage') }}">Mileage Pricing</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('locations.index') ? 'active' : '' }}" href="{{ route('locations.index') }}">Location Based Pricing</a></li>
              </ul>
          </li>

          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('reports', 'reports.*') ? 'active' : '' }}" href="{{ route('reports') }}"><i class="bi bi-graph-up me-1"></i>Reports</a></li>
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('setup', 'setup.*') ? 'active' : '' }}" href="{{ route('setup') }}"><i class="bi bi-gear-fill me-1"></i>Setup</a></li>
@endif
          @if (!session('admin_logged_in'))
    <!-- Show Login -->
    <li class="nav-item me-3">
        <a class="nav-link" href="{{ route('login') }}">
            <i class="bi bi-people-fill me-1"></i>Login
        </a>
    </li>
@endif
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('system.settings') ? 'active' : '' }}" href="{{ route('system.settings') }}"><i class="bi bi-gear-wide-connected me-1"></i>Settings</a></li>
        </ul>
      </div>
      

      <!-- Right Side Actions (Logout + Theme Switcher) -->
      <div class="d-flex align-items-center ms-auto my-auto gap-2">
        @if (session('admin_logged_in'))
          <form class="d-flex my-auto" method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Are you sure you want to log out of Crown Carz Admin Panel?');">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger py-1 px-2.5 d-flex align-items-center" style="font-size: 12px; border-radius: 6px;"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
          </form>
        @endif

        <!-- 🌓 Light/Dark Theme Switcher in Top Bar -->
        <div class="theme-switch-wrapper my-auto">
          <button id="themeToggleBtn" type="button" class="theme-toggle-btn" onclick="toggleCrowncarzTheme()" title="Switch Light / Dark Theme" aria-label="Toggle Light / Dark Theme">
            <span class="theme-toggle-track">
              <i class="bi bi-sun-fill theme-icon-sun"></i>
              <i class="bi bi-moon-stars-fill theme-icon-moon"></i>
              <span class="theme-toggle-thumb"></span>
            </span>
            <span class="theme-toggle-label d-none d-sm-inline" id="themeToggleLabel">Theme</span>
          </button>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="main-content">
    <div class="container-fluid">
      @yield('content')
    </div>
  </main>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- 🌓 Theme Switcher Logic -->
  <script>
  (function() {
      window.applyCrowncarzTheme = function(theme) {
          document.documentElement.setAttribute('data-bs-theme', theme);
          if (theme === 'dark') {
              document.documentElement.classList.add('dark-mode');
              if (document.body) document.body.classList.add('dark-mode');
          } else {
              document.documentElement.classList.remove('dark-mode');
              if (document.body) document.body.classList.remove('dark-mode');
          }
          try {
              localStorage.setItem('crowncarz_theme', theme);
          } catch (e) {}

          const labelEl = document.getElementById('themeToggleLabel');
          if (labelEl) {
              labelEl.textContent = theme === 'dark' ? 'Dark' : 'Light';
          }

          const btnEl = document.getElementById('themeToggleBtn');
          if (btnEl) {
              btnEl.setAttribute('title', theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode');
              btnEl.setAttribute('aria-label', theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode');
          }

          window.dispatchEvent(new CustomEvent('crowncarz-theme-changed', { detail: { theme: theme } }));
      };

      window.toggleCrowncarzTheme = function() {
          const currentTheme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
          const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
          window.applyCrowncarzTheme(nextTheme);
      };

      // Sync label on DOM ready
      function syncThemeUI() {
          const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
          const labelEl = document.getElementById('themeToggleLabel');
          if (labelEl) {
              labelEl.textContent = currentTheme === 'dark' ? 'Dark' : 'Light';
          }
      }

      if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', syncThemeUI);
      } else {
          syncThemeUI();
      }
  })();
  </script>
  <script>
  document.addEventListener("DOMContentLoaded", function() {
      try {
          const totalUsers = parseInt(localStorage.getItem("totalUsers")) || 0;
          const todaysRevenue = parseFloat(localStorage.getItem("todaysRevenue")) || 0.00;
          const rawBreakdown = localStorage.getItem("revenueBreakdown");
          const revenueBreakdown = rawBreakdown ? JSON.parse(rawBreakdown) : { Cash: 0, Card: 0, Account: 0 };

          const totalUsersEl = document.getElementById("totalUsersDisplay");
          if (totalUsersEl) totalUsersEl.textContent = totalUsers;

          const todaysRevenueEl = document.getElementById("todaysRevenueDisplay");
          if (todaysRevenueEl) todaysRevenueEl.textContent = `£${todaysRevenue.toFixed(2)}`;

          const ctx = document.getElementById('revenuePieChart');
          if (ctx && typeof Chart !== 'undefined') {
              new Chart(ctx, {
                  type: 'pie',
                  data: {
                      labels: Object.keys(revenueBreakdown),
                      datasets: [{
                          data: Object.values(revenueBreakdown),
                          backgroundColor: ['#1abc9c', '#3498db', '#f39c12'],
                          borderWidth: 1
                      }]
                  },
                  options: {
                      responsive: false,
                      animation: false,
                      plugins: {
                          legend: {
                              display: true,
                              position: 'bottom'
                          }
                      }
                  }
              });
          }
      } catch(e) {
          console.warn('Dashboard metrics init:', e);
      }
  });
  </script>

  <!-- 🛡️ DevTools & Source Code Protection -->
  <script>
  (function() {
      'use strict';

      // 1. Disable Right-Click Context Menu
      document.addEventListener('contextmenu', function(e) {
          e.preventDefault();
          return false;
      }, { capture: true });

      // 2. Block DevTools Shortcut Keys
      document.addEventListener('keydown', function(e) {
          // F12
          if (e.key === 'F12' || e.keyCode === 123) {
              e.preventDefault();
              e.stopPropagation();
              return false;
          }

          // Ctrl+Shift+I / Cmd+Option+I (Inspect)
          // Ctrl+Shift+J / Cmd+Option+J (Console)
          // Ctrl+Shift+C / Cmd+Option+C (Elements)
          // Ctrl+Shift+K / Cmd+Option+K (Firefox Console)
          if ((e.ctrlKey || e.metaKey) && e.shiftKey && (
              e.key === 'I' || e.key === 'i' || e.keyCode === 73 ||
              e.key === 'J' || e.key === 'j' || e.keyCode === 74 ||
              e.key === 'C' || e.key === 'c' || e.keyCode === 67 ||
              e.key === 'K' || e.key === 'k' || e.keyCode === 75
          )) {
              e.preventDefault();
              e.stopPropagation();
              return false;
          }

          // Cmd+Option+I / Cmd+Option+J / Cmd+Option+C (Mac Safari/Chrome shortcuts)
          if (e.metaKey && e.altKey && (
              e.key === 'I' || e.key === 'i' || e.keyCode === 73 ||
              e.key === 'J' || e.key === 'j' || e.keyCode === 74 ||
              e.key === 'C' || e.key === 'c' || e.keyCode === 67
          )) {
              e.preventDefault();
              e.stopPropagation();
              return false;
          }

          // Ctrl+U / Cmd+Option+U (View Page Source)
          if (((e.ctrlKey || e.metaKey) && (e.key === 'U' || e.key === 'u' || e.keyCode === 85)) ||
              (e.metaKey && e.altKey && (e.key === 'U' || e.key === 'u' || e.keyCode === 85))) {
              e.preventDefault();
              e.stopPropagation();
              return false;
          }

          // Ctrl+S / Cmd+S (Save Page)
          if ((e.ctrlKey || e.metaKey) && (e.key === 'S' || e.key === 's' || e.keyCode === 83)) {
              e.preventDefault();
              e.stopPropagation();
              return false;
          }
      }, { capture: true });

      // 3. Clear and Nullify Console
      try {
          const noop = function() {};
          const methods = ['log', 'debug', 'info', 'warn', 'error', 'table', 'trace', 'dir', 'dirxml', 'group', 'groupCollapsed', 'groupEnd', 'time', 'timeEnd', 'profile', 'profileEnd', 'count'];
          for (let i = 0; i < methods.length; i++) {
              console[methods[i]] = noop;
          }
      } catch(e) {}

      // 4. Continuous Debugger Trap (Freezes DevTools if forced open)
      function trapDebugger() {
          try {
              (function() {
                  Function('debugger')();
              })();
          } catch(e) {}
      }
      setInterval(trapDebugger, 400);
  })();
  </script>

  <script>
  (function() {
      function updateNavHistoryCounts() {
          try {
              const cached = JSON.parse(sessionStorage.getItem('nav_history_counts') || '{}');
              if (cached && typeof cached.completed !== 'undefined') {
                  setNavBadges(cached.completed, cached.previous, cached.cancelled);
              }
          } catch (e) {}

          fetch("{{ route('bookings.history.counts') }}")
              .then(res => res.json())
              .then(data => {
                  if (data && data.status) {
                      setNavBadges(data.completed, data.previous, data.cancelled);
                      try {
                          sessionStorage.setItem('nav_history_counts', JSON.stringify(data));
                      } catch (e) {}
                  }
              })
              .catch(err => {});
      }

      function setNavBadges(completed, previous, cancelled) {
          const compEl = document.getElementById('navHistoryCompletedCount');
          const prevEl = document.getElementById('navHistoryPreviousCount');
          const cancEl = document.getElementById('navHistoryCancelledCount');
          if (compEl) compEl.textContent = completed ?? 0;
          if (prevEl) prevEl.textContent = previous ?? 0;
          if (cancEl) cancEl.textContent = cancelled ?? 0;
      }

      if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', updateNavHistoryCounts);
      } else {
          updateNavHistoryCounts();
      }
  })();
  </script>

  <script>
  window.openFlightTracker = function(event, flightNo) {
    if (event) {
      event.stopPropagation();
      if (event.preventDefault) event.preventDefault();
    }
    if (!flightNo || flightNo === '-' || flightNo === 'undefined') return;
    const cleanFlight = String(flightNo).trim().toUpperCase();
    if (!cleanFlight) return;
    window.open('https://www.google.com/search?q=' + encodeURIComponent('flight ' + cleanFlight), '_blank');
  };
  </script>

  @stack('scripts')
</body>
</html>
