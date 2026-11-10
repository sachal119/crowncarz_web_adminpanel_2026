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
       🚗 LIVE QUEUE NOTIFICATIONS & DRAWER STYLING
       ========================================================== */
    .live-queue-nav-btn {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.16);
      color: rgba(255, 255, 255, 0.9);
      transition: all 0.2s ease;
      cursor: pointer;
    }
    .live-queue-nav-btn:hover {
      background: rgba(255, 255, 255, 0.14);
      border-color: #E6B04A;
      color: #E6B04A;
      box-shadow: 0 0 12px rgba(230, 176, 74, 0.25);
    }
    .live-queue-indicator-dot {
      display: inline-block;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background-color: #10b981;
      box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
      animation: liveQueuePulse 1.8s infinite;
      flex-shrink: 0;
    }
    @keyframes liveQueuePulse {
      0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
      70% { transform: scale(1.05); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
      100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .live-ride-card {
      border: 1px solid var(--cc-border);
      border-radius: 12px;
      background: var(--cc-bg-card);
      padding: 12px 14px;
      transition: all 0.2s ease;
      position: relative;
    }
    .live-ride-card:hover {
      border-color: #E6B04A;
      box-shadow: 0 4px 14px rgba(230, 176, 74, 0.15);
      transform: translateY(-2px);
    }
    .live-ride-card.new-arrival {
      border-left: 4px solid #10b981;
      background: rgba(16, 185, 129, 0.05);
    }
    .global-live-toast-container {
      position: fixed;
      top: 56px;
      right: 18px;
      z-index: 1099;
      display: flex;
      flex-direction: column;
      gap: 10px;
      pointer-events: none;
      max-width: 380px;
      width: calc(100vw - 36px);
    }
    .global-live-toast {
      pointer-events: auto;
      border-radius: 14px;
      padding: 14px 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
      animation: slideInRightToast 0.35s cubic-bezier(0.16, 1, 0.3, 1);
      transition: opacity 0.3s ease, transform 0.3s ease;
    }
    @keyframes slideInRightToast {
      from { transform: translateX(110%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    [data-bs-theme="dark"] .global-live-toast {
      background: #151d2c;
      color: #f8fafc;
      border: 1px solid rgba(230, 176, 74, 0.4);
    }
    [data-bs-theme="light"] .global-live-toast,
    :root:not([data-bs-theme="dark"]) .global-live-toast {
      background: #ffffff;
      color: #1e293b;
      border: 1px solid #E6B04A;
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
    if (!isset($drivers) || !($drivers instanceof \Illuminate\Support\Collection)) {
        $drivers = collect($drivers ?? []);
    }
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
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('pages.index', 'admin.pages.*') ? 'active' : '' }}" href="{{ route('pages.index') }}"><i class="bi bi-journal-richtext me-1"></i>Blogs</a></li>
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('advertisements.*') ? 'active' : '' }}" href="{{ route('advertisements.index') }}"><i class="bi bi-megaphone-fill me-1"></i>Ads</a></li>
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

        <!-- 🚗 Live Queue Pill Button -->
        <button type="button" 
                class="btn btn-sm live-queue-nav-btn d-flex align-items-center gap-1.5 px-2.5 py-1 rounded-3 my-auto shadow-xs text-decoration-none" 
                id="btnOpenLiveQueue" 
                data-bs-toggle="offcanvas" 
                data-bs-target="#liveQueueOffcanvas" 
                aria-controls="liveQueueOffcanvas" 
                title="Live Incoming Rides Queue (Auto-syncs live)">
          <span class="live-queue-indicator-dot me-1"></span>
          <i class="bi bi-broadcast text-warning me-1" style="font-size: 13px;"></i>
          <span class="fw-bold d-none d-sm-inline" style="font-size: 11.5px; color: #f8fafc;">Live Queue</span>
          <span class="badge bg-danger rounded-pill ms-1 font-monospace" id="globalLiveQueueBadge" style="display:none; font-size: 10px; padding: 2px 6px;">0</span>
        </button>

        <!-- 🕒 Live UK (London / Heathrow) Time Widget -->
        <div class="live-uk-clock-badge d-flex align-items-center gap-1.5 px-2.5 py-1 rounded-3 my-auto shadow-xs" 
             title="UK London / Heathrow Live Time (Auto Daylight Saving GMT/BST)"
             style="font-size: 11.5px;">
          <span class="live-clock-pulse-dot me-1"></span>
          <i class="bi bi-clock-fill text-warning me-1" style="font-size: 12px;"></i>
          <span class="fw-bold font-monospace text-nowrap" id="globalUkClockTime" style="letter-spacing: 0.3px; color: #f8fafc;">--:--:-- --</span>
          <span class="badge bg-dark text-warning px-1.5 py-0.5 rounded-1 font-monospace ms-1" id="globalUkClockTz" style="font-size: 9.5px; font-weight: 700;">GMT</span>
        </div>

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

  <!-- 🚗 Floating Real-Time New Ride Toast Container -->
  <div class="global-live-toast-container" id="globalLiveToastContainer"></div>

  <!-- 🚗 Live Queue Offcanvas Drawer -->
  <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="liveQueueOffcanvas" aria-labelledby="liveQueueOffcanvasLabel" style="width: 420px; max-width: 95vw; background-color: var(--cc-bg-card); color: var(--cc-text-primary);">
    <div class="offcanvas-header border-bottom py-3" style="border-color: var(--cc-border) !important; background: rgba(0,0,0,0.03);">
      <div>
        <div class="d-flex align-items-center gap-2">
          <span class="live-queue-indicator-dot"></span>
          <h6 class="offcanvas-title fw-bold mb-0" id="liveQueueOffcanvasLabel">Live Rides Queue</h6>
          <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 10px;">Live Feed</span>
        </div>
        <div class="text-muted small mt-1" style="font-size: 11px;">Real-time incoming rides without page refresh</div>
      </div>
      <div class="d-flex align-items-center gap-1.5">
        <button type="button" id="btnToggleQueueSound" class="btn btn-sm btn-outline-warning py-1 px-2 d-flex align-items-center" title="Toggle Ride Notification Sound" style="font-size: 12px; border-radius: 6px;">
          <i class="bi bi-volume-up-fill" id="queueSoundIcon"></i>
        </button>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
    </div>
    
    <div class="offcanvas-body p-3" id="liveQueueOffcanvasBody">
      <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color: var(--cc-border) !important;">
        <span class="small fw-bold text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Pending & Unassigned Rides</span>
        <span class="badge bg-warning bg-opacity-20 text-warning fw-bold px-2 py-1 font-monospace" id="liveQueueCountBadge" style="font-size: 11px;">0 Pending</span>
      </div>
      
      <div id="liveQueueListContainer" class="d-flex flex-column gap-2.5">
        <div class="text-center py-5 text-muted" id="liveQueueEmptyState">
          <i class="bi bi-check2-circle fs-1 text-success mb-2 d-block"></i>
          <div class="fw-semibold">Queue is clear!</div>
          <small class="text-secondary">Standing by for incoming customer rides...</small>
        </div>
      </div>
    </div>
  </div>

  <!-- ⚡ Real-Time Live Queue Engine -->
  <script>
  (function initGlobalLiveQueue() {
    const knownLiveRideIds = new Set();
    let isInitialSeeded = false;
    let isAudioMuted = localStorage.getItem('crowncarz_queue_sound') === 'muted';

    // 🔊 Melodious Web Audio API Sound Chime (No external mp3 file required)
    function playGlobalRideChime() {
      if (isAudioMuted) return;
      try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;
        const ctx = new AudioCtx();
        if (ctx.state === 'suspended') ctx.resume();

        const now = ctx.currentTime;
        // High tone 1 (E5 - 659.25Hz)
        const osc1 = ctx.createOscillator();
        const gain1 = ctx.createGain();
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(659.25, now);
        gain1.gain.setValueAtTime(0, now);
        gain1.gain.linearRampToValueAtTime(0.25, now + 0.03);
        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
        osc1.connect(gain1);
        gain1.connect(ctx.destination);
        osc1.start(now);
        osc1.stop(now + 0.35);

        // High tone 2 (A5 - 880Hz)
        const osc2 = ctx.createOscillator();
        const gain2 = ctx.createGain();
        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(880, now + 0.12);
        gain2.gain.setValueAtTime(0, now + 0.12);
        gain2.gain.linearRampToValueAtTime(0.35, now + 0.16);
        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.75);
        osc2.connect(gain2);
        gain2.connect(ctx.destination);
        osc2.start(now + 0.12);
        osc2.stop(now + 0.75);
      } catch (e) {
        console.warn('Audio chime notice:', e);
      }
    }

    // Toggle sound button
    const btnSound = document.getElementById('btnToggleQueueSound');
    const iconSound = document.getElementById('queueSoundIcon');
    if (btnSound && iconSound) {
      if (isAudioMuted) {
        iconSound.className = 'bi bi-volume-mute-fill';
        btnSound.classList.replace('btn-outline-warning', 'btn-outline-secondary');
      }
      btnSound.addEventListener('click', () => {
        isAudioMuted = !isAudioMuted;
        localStorage.setItem('crowncarz_queue_sound', isAudioMuted ? 'muted' : 'active');
        if (isAudioMuted) {
          iconSound.className = 'bi bi-volume-mute-fill';
          btnSound.classList.replace('btn-outline-warning', 'btn-outline-secondary');
        } else {
          iconSound.className = 'bi bi-volume-up-fill';
          btnSound.classList.replace('btn-outline-secondary', 'btn-outline-warning');
          playGlobalRideChime();
        }
      });
    }

    // 🔔 Floating Animated Notification Toast for New Incoming Ride
    function showGlobalNewRideToast(ride) {
      const container = document.getElementById('globalLiveToastContainer');
      if (!container) return;

      const toastId = 'toast-ride-' + (ride.id || Math.random().toString(36).substring(7));
      const refNo = ride.ref_no || ride.id || 'N/A';
      const passenger = ride.passenger_name || 'Customer';
      const phone = ride.phone_no ? `<small class="text-muted ms-1">(${ride.phone_no})</small>` : '';
      const pickup = ride.pickup_address ? (ride.pickup_address.length > 40 ? ride.pickup_address.substring(0, 40) + '...' : ride.pickup_address) : 'N/A';
      const dropoff = ride.dropoff_address ? (ride.dropoff_address.length > 40 ? ride.dropoff_address.substring(0, 40) + '...' : ride.dropoff_address) : 'N/A';
      const fare = ride.price ? `£${parseFloat(ride.price).toFixed(2)}` : '';
      const vehicle = ride.vehicle_make ? `&bull; ${ride.vehicle_make}` : '';
      const editUrl = `${window.location.origin}/admin/bookings/${encodeURIComponent(ride.id)}/edit`;

      const toastHtml = `
        <div class="global-live-toast" id="${toastId}">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="d-flex align-items-center gap-1.5">
              <span class="live-queue-indicator-dot"></span>
              <strong style="color: #E6B04A; font-size: 13px;">🚗 New Ride Received!</strong>
              <span class="badge bg-danger rounded-pill px-2 py-0.5 font-monospace" style="font-size: 10px;">${refNo}</span>
            </div>
            <button type="button" class="btn-close btn-close-sm text-reset ms-2" onclick="document.getElementById('${toastId}')?.remove()"></button>
          </div>
          
          <div class="small mb-2">
            <div class="fw-bold text-truncate">${passenger} ${phone}</div>
            <div class="text-truncate text-secondary" style="font-size: 11.5px;"><i class="bi bi-geo-alt text-success me-1"></i>${pickup}</div>
            <div class="text-truncate text-secondary" style="font-size: 11.5px;"><i class="bi bi-flag text-danger me-1"></i>${dropoff}</div>
            ${fare ? `<div class="fw-bold mt-1" style="color: #10b981; font-size: 12px;">${fare} <span class="text-muted fw-normal">${vehicle}</span></div>` : ''}
          </div>
          
          <div class="d-flex gap-2 mt-2">
            <a href="${editUrl}" class="btn btn-sm btn-warning fw-bold flex-grow-1 py-1" style="font-size: 11.5px; background: #E6B04A; border: none; color: #111;">
              <i class="bi bi-pencil-square me-1"></i> View & Dispatch
            </a>
            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5" style="font-size: 11.5px;" onclick="document.getElementById('${toastId}')?.remove()">
              Dismiss
            </button>
          </div>
        </div>
      `;

      container.insertAdjacentHTML('beforeend', toastHtml);

      // Auto-remove after 12 seconds
      setTimeout(() => {
        const el = document.getElementById(toastId);
        if (el) {
          el.style.opacity = '0';
          el.style.transform = 'translateX(110%)';
          setTimeout(() => el.remove(), 350);
        }
      }, 12000);
    }

    // Render cards in Live Queue offcanvas
    function renderLiveQueueCards(queue) {
      const container = document.getElementById('liveQueueListContainer');
      const emptyState = document.getElementById('liveQueueEmptyState');
      if (!container) return;

      if (!queue || queue.length === 0) {
        if (emptyState) emptyState.classList.remove('d-none');
        container.innerHTML = `
          <div class="text-center py-5 text-muted" id="liveQueueEmptyState">
            <i class="bi bi-check2-circle fs-1 text-success mb-2 d-block"></i>
            <div class="fw-semibold">Queue is clear!</div>
            <small class="text-secondary">Standing by for incoming customer rides...</small>
          </div>
        `;
        return;
      }

      let html = '';
      queue.forEach(ride => {
        const refNo = ride.ref_no || ride.id || 'N/A';
        const passenger = ride.passenger_name || 'Customer';
        const phone = ride.phone_no || '';
        const pickup = ride.pickup_address || 'N/A';
        const dropoff = ride.dropoff_address || 'N/A';
        const fare = ride.price ? `£${parseFloat(ride.price).toFixed(2)}` : 'N/A';
        const status = ride.status || 'Pending';
        const time = [ride.pickup_date, ride.pickup_time].filter(Boolean).join(' ') || 'ASAP';
        const isUnassigned = !ride.driver_id || ride.driver_id === '0';
        const editUrl = `${window.location.origin}/admin/bookings/${encodeURIComponent(ride.id)}/edit`;

        html += `
          <div class="live-ride-card ${isUnassigned ? 'new-arrival' : ''}" id="queue-card-${ride.id}">
            <div class="d-flex justify-content-between align-items-center mb-1.5">
              <span class="badge bg-dark text-warning font-monospace px-2 py-0.5" style="font-size: 10.5px;">${refNo}</span>
              <span class="badge ${isUnassigned ? 'bg-danger' : 'bg-info'} bg-opacity-75" style="font-size: 10px;">
                ${isUnassigned ? 'Unassigned' : status}
              </span>
            </div>

            <div class="fw-bold fs-6 mb-1 text-truncate">${passenger} ${phone ? `<small class="text-muted fw-normal ms-1">(${phone})</small>` : ''}</div>
            
            <div class="small text-secondary mb-1">
              <div class="text-truncate mb-0.5"><i class="bi bi-geo-alt-fill text-success me-1"></i>${pickup}</div>
              <div class="text-truncate"><i class="bi bi-flag-fill text-danger me-1"></i>${dropoff}</div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top" style="border-color: var(--cc-border-subtle) !important;">
              <div>
                <span class="fw-bold" style="color: #10b981; font-size: 13px;">${fare}</span>
                <span class="text-muted small ms-1">&bull; <i class="bi bi-clock me-0.5"></i>${time}</span>
              </div>
              <a href="${editUrl}" class="btn btn-sm btn-outline-warning py-0.5 px-2" style="font-size: 11px;">
                Dispatch <i class="bi bi-arrow-right-short"></i>
              </a>
            </div>
          </div>
        `;
      });

      container.innerHTML = html;
    }

    // ⚡ Poll Live Queue Endpoint (Pauses when tab is hidden to save bandwidth)
    async function pollLiveQueue() {
      if (document.hidden) return;
      try {
        const endpoint = `${window.location.origin}/admin/bookings/live-queue`;
        const res = await fetch(endpoint, {
          headers: { 'Accept': 'application/json' }
        });

        if (!res.ok) return;
        const data = await res.json();
        if (!data || !data.success) return;

        const queue = data.queue || [];
        const unassigned = data.unassigned_count ?? 0;
        const pending = data.pending_count ?? 0;

        // Update badge
        const badge = document.getElementById('globalLiveQueueBadge');
        if (badge) {
          const count = unassigned > 0 ? unassigned : pending;
          if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'inline-block';
          } else {
            badge.style.display = 'none';
          }
        }

        const countBadge = document.getElementById('liveQueueCountBadge');
        if (countBadge) {
          countBadge.textContent = `${unassigned > 0 ? unassigned : queue.length} Pending`;
        }

        renderLiveQueueCards(queue);

        // Check for new incoming rides
        if (isInitialSeeded) {
          for (const ride of queue) {
            if (!knownLiveRideIds.has(ride.id)) {
              knownLiveRideIds.add(ride.id);

              // 🌟 Trigger New Ride Chime & Toast!
              playGlobalRideChime();
              showGlobalNewRideToast(ride);

              // Dispatch event to page (e.g. Dashboard table)
              window.dispatchEvent(new CustomEvent('crowncarz-new-ride', { detail: ride }));
            }
          }
        } else {
          // Initial seed without alert
          queue.forEach(r => knownLiveRideIds.add(r.id));
          isInitialSeeded = true;
        }

      } catch (err) {
        console.warn('Live queue poll skipped:', err.message);
      }
    }

    // Start Real-Time polling every 10 seconds (Throttled & paused on inactive tab)
    setInterval(pollLiveQueue, 10000);
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', pollLiveQueue);
    } else {
      pollLiveQueue();
    }
  })();
  </script>

  @stack('scripts')
</body>
</html>
