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
  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />

  <!-- FullCalendar 6 for Calendar View -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

  <!-- Early Theme Initialization to Prevent Flash -->
  <script>
    (function() {
      try {
        document.documentElement.dataset.sidebarCollapsed = localStorage.getItem('crowncarz_sidebar_collapsed') === '1' ? 'true' : 'false';
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
      --cc-autocomplete-hover: #e0f2fe;
      --cc-autocomplete-error: #b91c1c;
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
      --cc-autocomplete-hover: #26364d;
      --cc-autocomplete-error: #fca5a5;
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
      margin-top: 0 !important;
      margin-bottom: 0 !important;
      height: 100dvh !important;
      overflow: hidden !important;
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
    [data-bs-theme="dark"] table tbody tr:hover > td,
    [data-bs-theme="dark"] .custom-dashboard-table tbody tr:hover,
    [data-bs-theme="dark"] .custom-dashboard-table tbody tr:hover > td,
    [data-bs-theme="dark"] .custom-dashboard-table.table-hover > tbody > tr:hover > * {
      background-color: #1a2333 !important;
      --bs-table-hover-bg: #1a2333 !important;
      --bs-table-accent-bg: #1a2333 !important;
    }

    /* Specific table cell texts in dark mode for maximum readability */
    [data-bs-theme="dark"] .custom-dashboard-table .text-dark,
    [data-bs-theme="dark"] .custom-dashboard-table .text-dark span {
      color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .custom-dashboard-table .text-muted {
      color: #94a3b8 !important;
    }

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

    [data-bs-theme="dark"] .col-passenger a,
    [data-bs-theme="dark"] .col-phone a,
    [data-bs-theme="dark"] .col-phone {
      color: #94a3b8 !important;
    }
    [data-bs-theme="dark"] .col-passenger a:hover,
    [data-bs-theme="dark"] .col-phone a:hover {
      color: #38bdf8 !important;
    }

    [data-bs-theme="dark"] .col-route span,
    [data-bs-theme="dark"] .col-pickup span,
    [data-bs-theme="dark"] .col-dropoff span {
      color: #e2e8f0 !important;
    }

    [data-bs-theme="dark"] .col-date,
    [data-bs-theme="dark"] .col-datetime span,
    [data-bs-theme="dark"] .col-datetime div,
    [data-bs-theme="dark"] .col-datetime {
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
      background-color: #fff9ea !important; /* Soft, warm, elegant champagne-gold tint */
      border-bottom: 1px solid #feeac1 !important;
    }
    .high-value-row:hover,
    .high-value-row:hover > td,
    tr.high-value-row:hover td,
    .custom-dashboard-table tbody tr.high-value-row:hover td {
      background-color: #fef2d3 !important;
    }
    .high-value-row td:first-child,
    tr.high-value-row td:first-child {
      border-left: 4px solid #f59e0b !important;
    }
    .high-value-row .text-dark,
    .high-value-row a.text-dark {
      color: #1e293b !important;
      font-weight: 600;
    }

    .high-price-badge {
      display: inline-flex;
      align-items: center;
      background: #fef3c7;
      color: #b45309;
      border: 1px solid #f59e0b;
      border-radius: 6px;
      padding: 1.5px 6px;
      font-weight: 800;
      font-size: 11px;
      font-family: var(--bs-font-monospace);
      letter-spacing: 0.2px;
      box-shadow: 0 1px 2px rgba(180, 83, 9, 0.15);
    }

    .normal-price-badge {
      display: inline-flex;
      align-items: center;
      background: #f8fafc;
      color: #334155;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      padding: 1.5px 6px;
      font-weight: 700;
      font-size: 11px;
      font-family: var(--bs-font-monospace);
      letter-spacing: 0.2px;
    }

    .normal-price-text {
      font-weight: 700;
      color: #1e293b;
      font-size: 12px;
    }

    /* 💰 High Value (> £50) & Price Styling (Dark Mode) */
    [data-bs-theme="dark"] .high-price-badge {
      background: #451a03 !important;
      color: #fde68a !important;
      border-color: #d97706 !important;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
    }
    [data-bs-theme="dark"] .normal-price-badge {
      background: #1e293b !important;
      color: #e2e8f0 !important;
      border-color: rgba(255, 255, 255, 0.2) !important;
    }
    [data-bs-theme="dark"] .high-value-row,
    [data-bs-theme="dark"] .high-value-row > td,
    [data-bs-theme="dark"] tr.high-value-row td,
    [data-bs-theme="dark"] .custom-dashboard-table tbody tr.high-value-row td {
      background-color: #241c10 !important; /* Subtle, rich dark-amber glow */
      border-bottom: 1px solid rgba(245, 158, 11, 0.2) !important;
    }
    [data-bs-theme="dark"] .high-value-row:hover,
    [data-bs-theme="dark"] .high-value-row:hover > td,
    [data-bs-theme="dark"] tr.high-value-row:hover td,
    [data-bs-theme="dark"] .custom-dashboard-table tbody tr.high-value-row:hover td {
      background-color: #2f2516 !important;
    }
    [data-bs-theme="dark"] .high-value-row td:first-child,
    [data-bs-theme="dark"] tr.high-value-row td:first-child {
      border-left: 4px solid #f59e0b !important;
    }
    [data-bs-theme="dark"] .high-value-row td,
    [data-bs-theme="dark"] .high-value-row .text-dark,
    [data-bs-theme="dark"] .high-value-row a.text-dark {
      color: #fef3c7 !important;
    }

    [data-bs-theme="dark"] .high-price-badge {
      background: rgba(245, 158, 11, 0.25) !important;
      color: #fcd34d !important;
      border: 1px solid rgba(245, 158, 11, 0.5) !important;
      box-shadow: 0 0 10px rgba(245, 158, 11, 0.2) !important;
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

    /* 🚗 Vehicle Category Pills (Saloon: Blue, Executive: Black, MPV: Yellow, 8 Seater: Red, Estate: Sea Green) */
    .veh-pill-saloon, [data-bs-theme="dark"] .veh-pill-saloon {
      background-color: #0d6efd !important;
      color: #ffffff !important;
      border: 1px solid #0b5ed7 !important;
    }
    .veh-pill-executive, [data-bs-theme="dark"] .veh-pill-executive {
      background-color: #09090b !important;
      color: #ffffff !important;
      border: 1px solid rgba(255, 255, 255, 0.35) !important;
    }
    .veh-pill-mpv, [data-bs-theme="dark"] .veh-pill-mpv {
      background-color: #ffc107 !important;
      color: #000000 !important;
      border: 1px solid #d39e00 !important;
    }
    .veh-pill-8seater, [data-bs-theme="dark"] .veh-pill-8seater {
      background-color: #dc3545 !important;
      color: #ffffff !important;
      border: 1px solid #b02a37 !important;
    }
    .veh-pill-estate, [data-bs-theme="dark"] .veh-pill-estate {
      background-color: #2e8b57 !important;
      color: #ffffff !important;
      border: 1px solid #246e45 !important;
    }
    .veh-pill-default, [data-bs-theme="dark"] .veh-pill-default {
      background-color: #334155 !important;
      color: #ffffff !important;
      border: 1px solid #475569 !important;
    }

    [data-bs-theme="dark"] .statusSelect {
      border-radius: 6px !important;
      font-weight: 700 !important;
      color: #000000 !important;
    }
    .statusSelect option {
      background-color: #1e293b !important;
      color: #ffffff !important;
    }
    [data-bs-theme="dark"] #perPageSelect {
      background-color: #1e293b !important;
      color: #f1f5f9 !important;
      border-color: rgba(255, 255, 255, 0.15) !important;
    }

    [data-bs-theme="dark"] .quick-calc-btn,
    [data-bs-theme="dark"] #swapPickupDropoffBtn,
    [data-bs-theme="dark"] #addViaToggleBtn {
      background-color: #1e293b !important;
      color: #f1f5f9 !important;
      border-color: rgba(255, 255, 255, 0.16) !important;
    }
    [data-bs-theme="dark"] .quick-calc-btn:hover,
    [data-bs-theme="dark"] #swapPickupDropoffBtn:hover,
    [data-bs-theme="dark"] #addViaToggleBtn:hover {
      background-color: #334155 !important;
      color: #ffffff !important;
      border-color: rgba(255, 255, 255, 0.3) !important;
    }
    [data-bs-theme="dark"] .quick-calc-btn span,
    [data-bs-theme="dark"] #swapPickupDropoffBtn span,
    [data-bs-theme="dark"] #addViaToggleBtn span {
      color: #f1f5f9 !important;
    }

    [data-bs-theme="dark"] .trend-badge-today {
      background-color: #090d16 !important;
      color: #fbbf24 !important;
      border-color: rgba(251, 191, 36, 0.4) !important;
    }
    [data-bs-theme="dark"] .trend-badge-today i {
      color: #fbbf24 !important;
    }
    [data-bs-theme="dark"] .trend-badge-today strong {
      color: #ffffff !important;
    }

    [data-bs-theme="dark"] .trend-badge-15d {
      background-color: #1e293b !important;
      color: #f59e0b !important;
      border: 1px solid rgba(245, 158, 11, 0.35) !important;
    }
    [data-bs-theme="dark"] .trend-badge-15d i {
      color: #f59e0b !important;
    }
    [data-bs-theme="dark"] .trend-badge-15d strong {
      color: #f8fafc !important;
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
      background: #f8fafc !important;
      border-top: 2px solid #e2e8f0 !important;
      border-bottom: 1px solid #e2e8f0 !important;
    }
    .timeline-date-header-clean {
      padding: 8px 14px;
      background: #f8fafc;
    }
    .timeline-calendar-icon-box {
      width: 28px;
      height: 28px;
      border-radius: 7px;
      background: rgba(184, 115, 51, 0.14);
      color: #B87333;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
    }
    .timeline-date-heading {
      font-size: 12.5px;
      font-weight: 700;
      color: #0f172a;
      letter-spacing: 0.1px;
    }
    .timeline-count-badge {
      font-size: 11px;
      font-weight: 600;
      color: #64748b;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      padding: 3px 10px;
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
      background: #fef3c7;
      border-color: #fde68a;
      color: #92400e;
    }

    /* 🌙 Timeline Styling (Dark Mode) */
    [data-bs-theme="dark"] .timeline-date-divider-row td,
    [data-bs-theme="dark"] .custom-dashboard-table tbody tr.timeline-date-divider-row td {
      background: #0f172a !important;
      border-top: 2px solid #334155 !important;
      border-bottom: 1px solid #1e293b !important;
    }
    [data-bs-theme="dark"] .timeline-date-header-clean {
      background: #0f172a;
    }
    [data-bs-theme="dark"] .timeline-calendar-icon-box {
      background: rgba(245, 158, 11, 0.2);
      color: #fbbf24;
    }
    [data-bs-theme="dark"] .timeline-date-heading {
      color: #f8fafc;
    }
    [data-bs-theme="dark"] .timeline-count-badge {
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
      background: #1f180e;
      border-color: rgba(245, 158, 11, 0.4);
      color: #fcd34d;
    }

    /* 🗓️ FullCalendar Custom Styling */
    .fc {
      font-family: inherit !important;
    }
    .fc-theme-standard .fc-scrollgrid {
      border-color: #e2e8f0 !important;
      border-radius: 12px;
      overflow: hidden;
    }
    .fc-theme-standard th {
      background: #f8fafc !important;
      color: #475569 !important;
      font-weight: 700 !important;
      font-size: 11.5px !important;
      padding: 10px 4px !important;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .fc-theme-standard td {
      border-color: #f1f5f9 !important;
    }
    .fc .fc-toolbar-title {
      font-size: 15.5px !important;
      font-weight: 800 !important;
      color: #1e293b;
    }
    .fc .fc-button-group {
      gap: 5px !important;
      display: inline-flex !important;
    }
    .fc .fc-button-group > .fc-button {
      border-radius: 8px !important;
      margin: 0 !important;
    }
    .fc .fc-button-primary {
      background: #ffffff !important;
      border: 1px solid #cbd5e1 !important;
      color: #334155 !important;
      font-weight: 600 !important;
      font-size: 12px !important;
      border-radius: 8px !important;
      padding: 5px 12px !important;
      box-shadow: 0 1px 2px rgba(0,0,0,0.05);
      text-transform: capitalize !important;
      transition: all 0.15s ease-in-out !important;
    }
    .fc .fc-button-primary:hover,
    .fc .fc-button-primary.fc-button-active {
      background: #0284c7 !important;
      border-color: #0284c7 !important;
      color: #ffffff !important;
      box-shadow: 0 2px 4px rgba(2, 132, 199, 0.3) !important;
    }
    .fc-event {
      cursor: pointer !important;
      border-radius: 6px !important;
      padding: 3px 6px !important;
      font-size: 11px !important;
      font-weight: 600 !important;
      border: none !important;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      transition: transform 0.15s ease, box-shadow 0.15s ease !important;
    }
    .fc-event:hover {
      transform: translateY(-1px) scale(1.02);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
    }
    .fc-daygrid-day-number {
      font-weight: 700 !important;
      font-size: 12px !important;
      color: #475569 !important;
      padding: 6px 8px !important;
    }
    .fc-day-today {
      background: rgba(2, 132, 199, 0.04) !important;
    }

    /* 🌙 FullCalendar Dark Mode */
    [data-bs-theme="dark"] .fc-theme-standard .fc-scrollgrid {
      border-color: #334155 !important;
    }
    [data-bs-theme="dark"] .fc-theme-standard th {
      background: #111827 !important;
      color: #94a3b8 !important;
    }
    [data-bs-theme="dark"] .fc-theme-standard td {
      border-color: #1e293b !important;
    }
    [data-bs-theme="dark"] .fc .fc-toolbar-title {
      color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .fc .fc-button-primary {
      background: #1e293b !important;
      border-color: #475569 !important;
      color: #e2e8f0 !important;
    }
    [data-bs-theme="dark"] .fc .fc-button-primary:hover,
    [data-bs-theme="dark"] .fc .fc-button-primary.fc-button-active {
      background: #0284c7 !important;
      border-color: #0284c7 !important;
      color: #ffffff !important;
    }
    [data-bs-theme="dark"] .fc-daygrid-day {
      background: #0f172a !important;
    }
    [data-bs-theme="dark"] .fc-daygrid-day-number {
      color: #94a3b8 !important;
    }
    [data-bs-theme="dark"] .fc-day-today {
      background: rgba(56, 189, 248, 0.08) !important;
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

    /* 🕒 Live UK Clock Styling */
    .live-clock-pulse-dot {
      width: 7px;
      height: 7px;
      background-color: #10b981;
      border-radius: 50%;
      display: inline-block;
      box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
      animation: pulseDotLiveClock 2s infinite;
      flex-shrink: 0;
    }
    @keyframes pulseDotLiveClock {
      0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
      70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
      100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .live-uk-clock-badge {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.18);
      color: #f8fafc;
      transition: all 0.2s ease;
    }
    .live-uk-clock-badge:hover {
      background: rgba(255, 255, 255, 0.14);
      border-color: rgba(230, 176, 74, 0.45);
    }
    [data-bs-theme="dark"] .live-uk-clock-badge {
      background: rgba(230, 176, 74, 0.12) !important;
      border-color: rgba(230, 176, 74, 0.3) !important;
    }
    #globalUkClockTime {
      color: #f8fafc !important;
    }

    /* Fixed desktop navigation, with its own scroll area and inline submenus. */
    :root { --cc-sidebar-width: 220px; }
    .app-sidebar {
      --bs-offcanvas-width: var(--cc-sidebar-width);
      position: fixed;
      inset: 0 auto 0 0;
      width: var(--cc-sidebar-width);
      height: 100vh;
      height: 100dvh;
      display: flex;
      flex-direction: column;
      padding: 0;
      border-right: 1px solid rgba(255,255,255,.08);
      border-bottom: 0;
      background: #111827 !important;
    }
    .sidebar-brand {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
      padding: 16px 12px;
      border-bottom: 1px solid rgba(255,255,255,.09);
    }
    .sidebar-brand a { text-decoration: none; gap: 8px !important; }
    .sidebar-brand img { height: 32px !important; }
    .sidebar-brand strong { font-size: 14px; letter-spacing: .3px; }
    .sidebar-brand small { color: #94a3b8; font-size: 11px; margin-top: 3px; }
    .app-sidebar .sidebar-scroll {
      display: flex;
      flex-direction: column;
      flex: 1 1 auto;
      min-height: 0;
      padding: 10px 8px;
      overflow-y: auto;
      overflow-x: hidden;
      overscroll-behavior-y: contain;
      scrollbar-width: thin;
      scrollbar-color: #64748b #111827;
    }
    .sidebar-scroll::-webkit-scrollbar { width: 6px; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: #64748b; border-radius: 6px; }
    .app-sidebar #navbarContent { flex-shrink: 0; }
    .app-sidebar .navbar-nav { flex-direction: column; gap: 3px; }
    .app-sidebar .nav-item { margin-right: 0 !important; }
    .app-sidebar .nav-link {
      display: flex;
      width: 100%;
      padding: 9px 10px !important;
      font-size: 12px;
      min-height: 42px;
    }
    .app-sidebar .nav-link > i { width: 22px; flex-shrink: 0; font-size: 16px; }
    .app-sidebar .dropdown-toggle::after { margin-left: auto; }
    .app-sidebar .dropdown-toggle[aria-expanded="true"]::after { transform: rotate(180deg); }
    .app-sidebar .nav-link:focus-visible { outline: 2px solid #E6B04A; outline-offset: -2px; }
    .app-sidebar .dropdown-menu {
      position: static !important;
      transform: none !important;
      min-width: 0 !important;
      width: 100%;
      margin: 4px 0 8px !important;
      box-shadow: none !important;
      will-change: auto;
    }
    .app-sidebar .dropdown-item { white-space: normal; font-size: 12px; padding: 8px 10px; }
    .app-sidebar .dropdown-item > span:first-child { min-width: 0; }
    .sidebar-actions {
      flex-wrap: wrap;
      flex-shrink: 0;
      margin-top: auto;
      padding: 12px 10px max(12px, env(safe-area-inset-bottom));
      background: #111827;
      border-top: 1px solid rgba(255,255,255,.09);
    }
    .sidebar-actions .live-uk-clock-badge { order: -1; width: 100%; justify-content: center; margin-bottom: 8px !important; }
    .sidebar-actions .theme-switch-wrapper { margin-left: auto; }
    .sidebar-actions .theme-toggle-label { display: inline !important; }
    .sidebar-mobile-header {
      position: sticky;
      top: 0;
      z-index: 1020;
      min-height: 56px;
      padding: 10px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #111827;
    }
    @media (min-width: 992px) {
      .app-sidebar.offcanvas-lg { position: fixed; }
      .main-content { margin-left: var(--cc-sidebar-width); width: calc(100% - var(--cc-sidebar-width)); min-width: 0; }
    }
    @media (max-width: 991.98px) {
      .app-sidebar { z-index: 1045 !important; width: min(var(--cc-sidebar-width), 90vw); }
      .main-content { margin-left: 0; width: 100%; padding: 14px 8px; }
      @if(request()->routeIs('live.map'))
      .main-content { height: calc(100dvh - 56px) !important; }
      @endif
    }
    .sidebar-collapse-btn {
      align-items: center; justify-content: center;
      flex-shrink: 0; width: 28px; height: 28px;
      border: 1px solid #475569; border-radius: 6px;
      background: transparent; color: #e2e8f0;
    }
    .sidebar-collapse-btn:hover, .sidebar-collapse-btn:focus-visible { color: #E6B04A; border-color: #E6B04A; }
    @media (min-width: 992px) {
      html[data-sidebar-collapsed="true"] { --cc-sidebar-width: 80px; }
      [data-sidebar-collapsed="true"] .sidebar-brand { flex-direction: column; gap: 10px; padding: 12px 6px; }
      [data-sidebar-collapsed="true"] .sidebar-brand-label,
      [data-sidebar-collapsed="true"] .sidebar-link-label,
      [data-sidebar-collapsed="true"] .app-sidebar .dropdown-toggle::after,
      [data-sidebar-collapsed="true"] .app-sidebar .dropdown-menu,
      [data-sidebar-collapsed="true"] .sidebar-actions .theme-toggle-label { display: none !important; }
      [data-sidebar-collapsed="true"] .sidebar-collapse-btn i { transform: rotate(180deg); }
      [data-sidebar-collapsed="true"] .app-sidebar .nav-link { justify-content: center; }
      [data-sidebar-collapsed="true"] .app-sidebar .nav-link > i { margin: 0 !important; width: auto; }
      [data-sidebar-collapsed="true"] .sidebar-actions { flex-direction: column; padding: 10px 4px; gap: 10px !important; }
      [data-sidebar-collapsed="true"] .sidebar-actions .theme-switch-wrapper { margin-left: 0; }
      [data-sidebar-collapsed="true"] .sidebar-actions .theme-toggle-btn { padding: 4px; }
      [data-sidebar-collapsed="true"] .sidebar-actions .live-uk-clock-badge { padding: 4px 1px !important; margin-bottom: 0 !important; }
      [data-sidebar-collapsed="true"] .live-uk-clock-badge > :not(#globalUkClockTime) { display: none; }
      [data-sidebar-collapsed="true"] #globalUkClockTime { font-size: 8px; letter-spacing: 0 !important; }
    }
    .email-live-dot {
      width: 8px;
      height: 8px;
      background-color: #ef4444 !important;
      border: 1.5px solid #111827;
      box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.8);
      animation: ccEmailDotPulse 1.8s infinite;
      z-index: 3;
    }
    @keyframes ccEmailDotPulse {
      0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.8); transform: translate(-50%, -50%) scale(0.95); }
      70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); transform: translate(-50%, -50%) scale(1.15); }
      100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); transform: translate(-50%, -50%) scale(0.95); }
    }
    @media print {
      .app-sidebar, .sidebar-mobile-header { display: none !important; }
      .main-content { margin-left: 0 !important; width: 100% !important; }
    }
  </style>
</head>



<body>
  <!-- Mobile navigation trigger -->
  <header class="sidebar-mobile-header d-lg-none">
    <a href="{{ route('dashboard') }}" class="text-white text-decoration-none fw-semibold">Crown Carz</a>
    <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Open navigation menu">
      <i class="bi bi-list me-1" aria-hidden="true"></i>Menu
    </button>
  </header>

  <nav class="offcanvas-lg offcanvas-start navbar-custom app-sidebar" tabindex="-1" id="adminSidebar" aria-labelledby="sidebarTitle">
    <div class="sidebar-brand">
      <a class="navbar-brand d-flex align-items-center gap-3" href="{{ route('dashboard') }}">
        <img src="{{ asset('public/images/logo.png') }}" alt="" style="height: 38px; width: auto;">
        <span class="sidebar-brand-label"><strong id="sidebarTitle">Crown Carz</strong><small class="d-block">Admin Panel</small></span>
      </a>
      <button type="button" id="sidebarCollapseBtn" class="sidebar-collapse-btn d-none d-lg-inline-flex" aria-controls="navbarContent" aria-expanded="true" aria-label="Collapse sidebar" title="Collapse sidebar">
        <i class="bi bi-chevron-bar-left" aria-hidden="true"></i>
      </button>
      <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Close navigation menu"></button>
    </div>
    <div class="offcanvas-body sidebar-scroll">
      <div id="navbarContent">
        <ul class="navbar-nav">
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid-fill me-1"></i><span class="sidebar-link-label">Dashboard</span></a></li>
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('live.map') ? 'active' : '' }}" href="{{ route('live.map') }}"><i class="bi bi-geo-alt-fill me-1"></i><span class="sidebar-link-label">Live Driver Map</span></a></li>
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('booking.create') ? 'active' : '' }}" href="{{ route('booking.create') }}" target="_blank"><i class="bi bi-calendar-plus me-1"></i><span class="sidebar-link-label">Create Booking</span></a></li>
          <!-- History Dropdown -->
          <li class="nav-item dropdown me-2">
              <a class="nav-link dropdown-toggle {{ request()->routeIs('completed.jobs', 'bookings.search', 'previous.bookings', 'previous.bookings.search', 'bookings.cancelled', 'cancelled.bookings.search') ? 'active' : '' }}" href="#" id="historyDropdown" role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                <i class="bi bi-clock-history me-1"></i><span class="sidebar-link-label">History</span></a>
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
              <a class="nav-link dropdown-toggle {{ request()->routeIs('messages.*', 'messages.all') ? 'active' : '' }}" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                <i class="bi bi-chat-dots-fill me-1"></i><span class="sidebar-link-label">Messages</span></a>
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
              <a class="nav-link dropdown-toggle {{ (request()->routeIs('pricing.*') || request()->routeIs('locations.*')) ? 'active' : '' }}" href="#" id="pricingDropdown" role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                <i class="bi bi-currency-pound me-1"></i><span class="sidebar-link-label">Pricing</span></a>
              <ul class="dropdown-menu shadow-lg border-0" aria-labelledby="pricingDropdown" style="border-radius: 12px;">
                <li><a class="dropdown-item {{ request()->routeIs('pricing.fixed') ? 'active' : '' }}" href="{{ route('pricing.fixed') }}">Fixed Pricing</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pricing.mileage') ? 'active' : '' }}" href="{{ route('pricing.mileage') }}">Mileage Pricing</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('locations.index') ? 'active' : '' }}" href="{{ route('locations.index') }}">Location Based Pricing</a></li>
              </ul>
          </li>

          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('reports', 'reports.*') ? 'active' : '' }}" href="{{ route('reports') }}"><i class="bi bi-graph-up me-1"></i><span class="sidebar-link-label">Reports</span></a></li>
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('setup', 'setup.*') ? 'active' : '' }}" href="{{ route('setup') }}"><i class="bi bi-gear-fill me-1"></i><span class="sidebar-link-label">Setup</span></a></li>
@endif
          @if (!session('admin_logged_in'))
    <!-- Show Login -->
    <li class="nav-item me-3">
        <a class="nav-link" href="{{ route('login') }}">
            <i class="bi bi-people-fill me-1"></i><span class="sidebar-link-label">Login</span></a>
    </li>
@endif
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('pages.index', 'admin.pages.*') ? 'active' : '' }}" href="{{ route('pages.index') }}"><i class="bi bi-journal-richtext me-1"></i><span class="sidebar-link-label">Blogs</span></a></li>
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('advertisements.*') ? 'active' : '' }}" href="{{ route('advertisements.index') }}"><i class="bi bi-megaphone-fill me-1"></i><span class="sidebar-link-label">Ads</span></a></li>
          <li class="nav-item me-2 position-relative">
            <a class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('emails.*') ? 'active' : '' }}" href="{{ route('emails.index') }}" title="Email (info@crowncarz.com)" id="sidebarEmailInfoLink">
              <div class="d-flex align-items-center flex-nowrap text-truncate">
                <span class="position-relative d-inline-block me-1">
                  <i class="bi bi-envelope-at-fill text-warning"></i>
                  <span class="email-live-dot d-none position-absolute top-0 start-100 translate-middle rounded-circle" id="sidebarEmailInfoDot" title="New unread email in info@crowncarz.com"></span>
                </span>
                <span class="sidebar-link-label d-inline-flex align-items-center flex-nowrap" style="white-space: nowrap; max-width: 145px;">
                  <span>Email</span>
                  <span class="ms-1 opacity-75 fw-normal text-truncate" style="font-size: 9.5px; max-width: 100px; letter-spacing: -0.2px;">(info@crowncarz.com)</span>
                </span>
              </div>
              <span class="badge rounded-pill bg-danger d-none email-live-badge sidebar-link-label ms-1" id="sidebarEmailInfoBadge" style="font-size: 9.5px; padding: 2px 6px;">0</span>
            </a>
          </li>

          <li class="nav-item me-2 position-relative">
            <a class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('accounts_emails.*') ? 'active' : '' }}" href="{{ route('accounts_emails.index') }}" title="Email (accounts@crowncarz.com)" id="sidebarEmailAccountsLink">
              <div class="d-flex align-items-center flex-nowrap text-truncate">
                <span class="position-relative d-inline-block me-1">
                  <i class="bi bi-envelope-check-fill text-info"></i>
                  <span class="email-live-dot d-none position-absolute top-0 start-100 translate-middle rounded-circle" id="sidebarEmailAccountsDot" title="New unread email in accounts@crowncarz.com"></span>
                </span>
                <span class="sidebar-link-label d-inline-flex align-items-center flex-nowrap" style="white-space: nowrap; max-width: 145px;">
                  <span>Email</span>
                  <span class="ms-1 opacity-75 fw-normal text-truncate" style="font-size: 9.5px; max-width: 100px; letter-spacing: -0.2px;">(accounts@crowncarz.com)</span>
                </span>
              </div>
              <span class="badge rounded-pill bg-danger d-none email-live-badge sidebar-link-label ms-1" id="sidebarEmailAccountsBadge" style="font-size: 9.5px; padding: 2px 6px;">0</span>
            </a>
          </li>
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('system.settings') ? 'active' : '' }}" href="{{ route('system.settings') }}"><i class="bi bi-gear-wide-connected me-1"></i><span class="sidebar-link-label">Settings</span></a></li>
        </ul>
      </div>
      

    </div>

      <!-- Fixed sidebar account and display controls -->
      <div class="sidebar-actions d-flex align-items-center gap-2">
        @if (session('admin_logged_in'))
          <form id="globalLogoutForm" class="d-flex my-auto" method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="button" onclick="confirmAdminLogout(event)" title="Logout" aria-label="Logout" class="btn btn-sm btn-danger py-1 px-2.5 d-flex align-items-center" style="font-size: 12px; border-radius: 6px;"><i class="bi bi-box-arrow-right me-1"></i><span class="sidebar-link-label">Logout</span></button>
          </form>
          <script>
            function confirmAdminLogout(e) {
              if (e) e.preventDefault();
              const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark' || document.documentElement.classList.contains('dark-mode');
              
              if (typeof Swal !== 'undefined') {
                Swal.fire({
                  title: 'Are you sure?',
                  text: 'You will be logged out of Crown Carz Admin Panel.',
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#dc3545',
                  cancelButtonColor: isDark ? '#334155' : '#64748b',
                  confirmButtonText: '<i class="bi bi-box-arrow-right me-1"></i> Yes, Logout',
                  cancelButtonText: 'Cancel',
                  background: isDark ? '#151d2c' : '#ffffff',
                  color: isDark ? '#f8fafc' : '#1e293b',
                  reverseButtons: true,
                  focusCancel: true
                }).then((result) => {
                  if (result.isConfirmed) {
                    document.getElementById('globalLogoutForm').submit();
                  }
                });
              } else if (confirm('Are you sure you want to log out of Crown Carz Admin Panel?')) {
                document.getElementById('globalLogoutForm').submit();
              }
            }
          </script>
        @endif

        <!-- 🕒 Live UK (London / Heathrow) Time Widget -->
        <div class="live-uk-clock-badge d-flex align-items-center gap-1.5 px-2.5 py-1 rounded-3 my-auto shadow-xs" 
             title="UK London / Heathrow Live Time (Auto Daylight Saving GMT/BST)"
             style="font-size: 11.5px;">
          <span class="live-clock-pulse-dot me-1"></span>
          <i class="bi bi-clock-fill text-warning me-1" style="font-size: 12px;"></i>
          <span class="fw-bold font-monospace text-nowrap" id="globalUkClockTime" style="letter-spacing: 0.3px; color: #f8fafc;">--:--:-- --</span>
          <span class="badge bg-dark text-warning px-1.5 py-0.5 rounded-1 font-monospace ms-1" id="globalUkClockTz" style="font-size: 9.5px; font-weight: 700;">GMT</span>
        </div>

        <!-- Light/Dark Theme Switcher -->
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

  <!-- Sidebar width preference applies only on desktop; mobile keeps its drawer. -->
  <script>
  (() => {
    const sidebar = document.getElementById('adminSidebar');
    const toggle = document.getElementById('sidebarCollapseBtn');
    const desktop = window.matchMedia('(min-width: 992px)');
    const isCollapsed = () => document.documentElement.dataset.sidebarCollapsed === 'true';
    const updateToggle = () => {
      const label = isCollapsed() ? 'Expand sidebar' : 'Collapse sidebar';
      toggle.setAttribute('aria-expanded', String(!isCollapsed()));
      toggle.setAttribute('aria-label', label);
      toggle.title = label;
    };
    const setCollapsed = collapsed => {
      // Close existing submenus before hiding their labels and content.
      if (collapsed) {
        sidebar.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
          bootstrap.Dropdown.getInstance(el)?.hide();
        });
      }
      document.documentElement.dataset.sidebarCollapsed = String(collapsed);
      try { localStorage.setItem('crowncarz_sidebar_collapsed', collapsed ? '1' : '0'); } catch (e) {}
      updateToggle();
      window.dispatchEvent(new Event('resize'));
    };
    sidebar.querySelectorAll('.nav-link').forEach(link => {
      const label = link.textContent.trim();
      link.title = label;
      link.setAttribute('aria-label', label);
    });
    toggle.addEventListener('click', () => setCollapsed(!isCollapsed()));
    // Expand before Bootstrap opens a submenu, including keyboard-triggered clicks.
    sidebar.addEventListener('show.bs.dropdown', () => {
      if (desktop.matches && isCollapsed()) setCollapsed(false);
    });
    updateToggle();
  })();
  </script>

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
          const now = Date.now();
          const CACHE_TTL_MS = 5 * 60 * 1000; // 5 minutes client cache across page transitions
          try {
              const cached = JSON.parse(sessionStorage.getItem('nav_history_counts') || '{}');
              if (cached && typeof cached.completed !== 'undefined') {
                  setNavBadges(cached.completed, cached.previous, cached.cancelled);
                  if (cached._ts && (now - cached._ts < CACHE_TTL_MS)) {
                      // Recent data exists, skip network request entirely
                      return;
                  }
              }
          } catch (e) {}

          fetch("{{ route('bookings.history.counts') }}")
              .then(res => res.json())
              .then(data => {
                  if (data && data.status) {
                      setNavBadges(data.completed, data.previous, data.cancelled);
                      try {
                          data._ts = Date.now();
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

  <!-- 🕒 Live UK Heathrow Clock Real-Time Ticker -->
  <script>
  (function initLiveUKClock() {
    function tick() {
      try {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('en-GB', {
          timeZone: 'Europe/London',
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit',
          hour12: true
        });
        const dateStr = now.toLocaleDateString('en-GB', {
          timeZone: 'Europe/London',
          weekday: 'short',
          day: '2-digit',
          month: 'short',
          year: 'numeric'
        });

        const parts = new Intl.DateTimeFormat('en-GB', { timeZone: 'Europe/London', timeZoneName: 'short' }).formatToParts(now);
        const tzName = (parts.find(p => p.type === 'timeZoneName')?.value || 'GMT').toUpperCase();

        const navTime = document.getElementById('globalUkClockTime');
        const navTz = document.getElementById('globalUkClockTz');

        if (navTime) navTime.textContent = timeStr;
        if (navTz) navTz.textContent = tzName;
      } catch (e) {
        console.error('UK Clock tick error:', e);
      }
    }
    setInterval(tick, 1000);
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', tick);
    } else {
      tick();
    }
  })();
  </script>

  <!-- SweetAlert2 JS & Crown Carz Global Alert Helpers -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  (function() {
    function getSwalTheme() {
      const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark' || document.documentElement.classList.contains('dark-mode');
      return {
        background: isDark ? '#151d2c' : '#ffffff',
        color: isDark ? '#f8fafc' : '#1e293b',
        confirmButtonColor: '#E6B04A',
        cancelButtonColor: isDark ? '#334155' : '#64748b'
      };
    }

    window.ccAlert = {
      fire: function(opts) {
        const theme = getSwalTheme();
        return Swal.fire({
          ...theme,
          ...opts
        });
      },
      success: function(title, text = '') {
        return this.fire({
          icon: 'success',
          title: title,
          text: text,
          confirmButtonText: 'OK'
        });
      },
      error: function(title, text = '') {
        return this.fire({
          icon: 'error',
          title: title,
          text: text,
          confirmButtonText: 'OK'
        });
      },
      warning: function(title, text = '') {
        return this.fire({
          icon: 'warning',
          title: title,
          text: text,
          confirmButtonText: 'OK'
        });
      },
      info: function(title, text = '') {
        return this.fire({
          icon: 'info',
          title: title,
          text: text,
          confirmButtonText: 'OK'
        });
      },
      loading: function(title = 'Please wait...', text = '') {
        const theme = getSwalTheme();
        return Swal.fire({
          ...theme,
          title: title,
          text: text,
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
      },
      close: function() {
        Swal.close();
      }
    };

    // Polyfill window.alert to automatically upgrade all legacy alerts to SweetAlert2
    const _origAlert = window.alert;
    window.alert = function(msg) {
      if (window.Swal) {
        const str = String(msg || '');
        const lower = str.toLowerCase();
        const isErr = lower.includes('error') || lower.includes('failed') || lower.includes('invalid') || lower.includes('cannot');
        const isSucc = lower.includes('success') || lower.includes('sent') || lower.includes('saved') || lower.includes('updated');
        const isWarn = lower.includes('please') || lower.includes('select') || lower.includes('enter') || lower.includes('warning');

        const icon = isErr ? 'error' : (isSucc ? 'success' : (isWarn ? 'warning' : 'info'));
        const title = isErr ? 'Attention' : (isSucc ? 'Success' : 'Notice');

        return window.ccAlert.fire({
          icon: icon,
          title: title,
          text: str,
          confirmButtonText: 'OK'
        });
      }
      _origAlert(msg);
    };
  })();
  </script>

  @if(session('admin_logged_in'))
  <!-- 🔔 Realtime Webmail Unread Status, Pulsing Dot & Alert Chime -->
  <script>
  (function() {
    let audioUnlocked = false;
    function unlockAudioContext() {
      if (audioUnlocked) return;
      try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (AudioCtx) {
          const testCtx = new AudioCtx();
          if (testCtx.state === 'suspended') {
            testCtx.resume();
          }
          audioUnlocked = true;
        }
      } catch(e) {}
    }
    document.addEventListener('click', unlockAudioContext, { once: true, passive: true });
    document.addEventListener('keydown', unlockAudioContext, { once: true, passive: true });

    // 🎵 Crystal-clear luxury notification chime (E5 -> B5 pure synth)
    function playEmailAlertSound() {
      try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;
        const ctx = new AudioCtx();
        if (ctx.state === 'suspended') {
          ctx.resume();
        }
        const now = ctx.currentTime;

        // Tone 1: E5 (659.25Hz)
        const osc1 = ctx.createOscillator();
        const gain1 = ctx.createGain();
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(659.25, now);
        gain1.gain.setValueAtTime(0.18, now);
        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.32);
        osc1.connect(gain1);
        gain1.connect(ctx.destination);
        osc1.start(now);
        osc1.stop(now + 0.32);

        // Tone 2: B5 (987.77Hz)
        const osc2 = ctx.createOscillator();
        const gain2 = ctx.createGain();
        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(987.77, now + 0.12);
        gain2.gain.setValueAtTime(0.24, now + 0.12);
        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.58);
        osc2.connect(gain2);
        gain2.connect(ctx.destination);
        osc2.start(now + 0.12);
        osc2.stop(now + 0.58);
      } catch(e) {
        console.warn('Audio chime notice:', e);
      }
    }

    // Unread count cache in memory
    const unreadTracker = {
      info: null,
      accounts: null
    };

    function updateSidebarMailboxUI(accountKey, count, email) {
      const isAccounts = (accountKey === 'accounts');
      const dotEl = document.getElementById(isAccounts ? 'sidebarEmailAccountsDot' : 'sidebarEmailInfoDot');
      const badgeEl = document.getElementById(isAccounts ? 'sidebarEmailAccountsBadge' : 'sidebarEmailInfoBadge');

      if (count > 0) {
        if (dotEl) dotEl.classList.remove('d-none');
        if (badgeEl) {
          badgeEl.textContent = count > 99 ? '99+' : count;
          badgeEl.classList.remove('d-none');
        }
      } else {
        if (dotEl) dotEl.classList.add('d-none');
        if (badgeEl) badgeEl.classList.add('d-none');
      }
    }

    let isFetchingUnread = false;
    async function fetchEmailUnreadStatus() {
      if (isFetchingUnread) return;
      isFetchingUnread = true;

      try {
        const response = await fetch("{{ route('emails.unread.status') }}", {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) return;
        const result = await response.json();
        if (!result.success || !result.accounts) return;

        let chimePlayed = false;

        for (const [key, data] of Object.entries(result.accounts)) {
          const currentCount = parseInt(data.unread || 0, 10);
          const previousCount = unreadTracker[key];

          updateSidebarMailboxUI(key, currentCount, data.email);

          // If count increased and not initial page load, trigger alert chime & toast
          if (previousCount !== null && currentCount > previousCount && !chimePlayed) {
            playEmailAlertSound();
            chimePlayed = true;

            if (typeof Swal !== 'undefined' && Swal.mixin) {
              const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark' || document.documentElement.classList.contains('dark-mode');
              Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 6000,
                timerProgressBar: true,
                background: isDark ? '#1e293b' : '#ffffff',
                color: isDark ? '#f8fafc' : '#1e293b',
                didOpen: (toast) => {
                  toast.addEventListener('mouseenter', Swal.stopTimer);
                  toast.addEventListener('mouseleave', Swal.resumeTimer);
                  toast.style.cursor = 'pointer';
                  toast.addEventListener('click', () => {
                    window.location.href = (key === 'accounts') ? "{{ route('accounts_emails.index') }}" : "{{ route('emails.index') }}";
                  });
                }
              }).fire({
                icon: 'info',
                title: 'New Email Received',
                html: `New message received in <b>${data.email}</b>`
              });
            }
          }

          unreadTracker[key] = currentCount;
        }
      } catch (err) {
        // Graceful fail on background hiccups
      } finally {
        isFetchingUnread = false;
      }
    }

    // Initial check shortly after load
    setTimeout(fetchEmailUnreadStatus, 1500);

    // Polling interval (every 30 seconds)
    setInterval(fetchEmailUnreadStatus, 30000);

    // Fast local event when email is read or marked
    window.addEventListener('cc-email-read-updated', () => {
      setTimeout(fetchEmailUnreadStatus, 400);
    });
  })();
  </script>
  @endif

  @stack('scripts')
</body>
</html>
