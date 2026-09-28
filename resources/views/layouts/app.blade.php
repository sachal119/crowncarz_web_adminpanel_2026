<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <title>Crown Carz Panel</title>
  <link href="{{ asset('public/images/logo_black.png') }}" rel="icon" type="image/x-icon">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    *, *::before, *::after {
      box-sizing: border-box;
    }
    html, body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      background-color: #f8f9fa;
      font-size: 14px;
      -webkit-font-smoothing: antialiased;
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
      

@if (session('admin_logged_in'))
      <form class="d-flex ms-auto my-auto" method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Are you sure you want to log out of Crown Carz Admin Panel?');">
        @csrf
        <button type="submit" class="btn btn-sm btn-danger py-1 px-2.5 d-flex align-items-center" style="font-size: 12px; border-radius: 6px;"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
      </form>
@endif
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

  @include('partials.flight_tracker_modal')

  <script>
  (function() {
    const AIRLINES_MAP = {
      'BA': 'British Airways',
      'BAW': 'British Airways',
      'VS': 'Virgin Atlantic',
      'VIR': 'Virgin Atlantic',
      'EK': 'Emirates',
      'UAE': 'Emirates',
      'QR': 'Qatar Airways',
      'QTR': 'Qatar Airways',
      'EY': 'Etihad Airways',
      'ETD': 'Etihad Airways',
      'AA': 'American Airlines',
      'AAL': 'American Airlines',
      'UA': 'United Airlines',
      'UAL': 'United Airlines',
      'DL': 'Delta Air Lines',
      'DAL': 'Delta Air Lines',
      'LH': 'Lufthansa',
      'DLH': 'Lufthansa',
      'AF': 'Air France',
      'AFR': 'Air France',
      'KL': 'KLM Royal Dutch',
      'KLM': 'KLM Royal Dutch',
      'TK': 'Turkish Airlines',
      'THY': 'Turkish Airlines',
      'PK': 'Pakistan Int. Airlines',
      'PIA': 'Pakistan Int. Airlines',
      'AI': 'Air India',
      'AIC': 'Air India',
      'SV': 'Saudia',
      'SVA': 'Saudia',
      'AC': 'Air Canada',
      'ACA': 'Air Canada',
      'QF': 'Qantas',
      'QFA': 'Qantas',
      'EI': 'Aer Lingus',
      'EIN': 'Aer Lingus',
      'IB': 'Iberia',
      'IBE': 'Iberia',
      'LX': 'Swiss Int. Air Lines',
      'SWR': 'Swiss Int. Air Lines',
      'OS': 'Austrian Airlines',
      'AUA': 'Austrian Airlines',
      'FR': 'Ryanair',
      'RYR': 'Ryanair',
      'U2': 'easyJet',
      'EZY': 'easyJet',
      'EZS': 'easyJet',
      'W9': 'Wizz Air UK',
      'W6': 'Wizz Air',
      'WZZ': 'Wizz Air',
      'LS': 'Jet2',
      'EXS': 'Jet2',
      'BY': 'TUI Airways',
      'TOM': 'TUI Airways',
      'SQ': 'Singapore Airlines',
      'SIA': 'Singapore Airlines',
      'CX': 'Cathay Pacific',
      'CPA': 'Cathay Pacific',
      'MH': 'Malaysia Airlines',
      'MAS': 'Malaysia Airlines',
      'TG': 'Thai Airways',
      'THA': 'Thai Airways',
      'JL': 'Japan Airlines',
      'JAL': 'Japan Airlines',
      'NH': 'All Nippon Airways',
      'ANA': 'All Nippon Airways',
      'KU': 'Kuwait Airways',
      'KAC': 'Kuwait Airways',
      'GF': 'Gulf Air',
      'GFA': 'Gulf Air',
      'WY': 'Oman Air',
      'OMA': 'Oman Air',
      'MS': 'EgyptAir',
      'MSR': 'EgyptAir',
      'ET': 'Ethiopian Airlines',
      'ETH': 'Ethiopian Airlines',
      'KQ': 'Kenya Airways',
      'KQA': 'Kenya Airways',
      'AT': 'Royal Air Maroc',
      'RAM': 'Royal Air Maroc',
      'RJ': 'Royal Jordanian',
      'RJA': 'Royal Jordanian',
      'ME': 'Middle East Airlines',
      'MEA': 'Middle East Airlines',
      'PC': 'Pegasus Airlines',
      'PGT': 'Pegasus Airlines',
      'FZ': 'flydubai',
      'FDB': 'flydubai',
      'G9': 'Air Arabia',
      'ABY': 'Air Arabia'
    };

    const KNOWN_ROUTES = {
      'BA158': { originCode: 'BDA', originCity: 'Bermuda', originName: 'L.F. Wade International Airport', destCode: 'LHR', destCity: 'London', destTerminal: '5', duration: '6h 50m', durationMin: 410, depTerminal: '1' },
      'BA327': { originCode: 'NCE', originCity: 'Nice', originName: "Nice Côte d'Azur Airport", destCode: 'LHR', destCity: 'London', destTerminal: '5', duration: '2h 15m', durationMin: 135, depTerminal: '1' },
      'BA159': { originCode: 'LHR', originCity: 'London', originName: 'London Heathrow Airport', destCode: 'BDA', destCity: 'Bermuda', destTerminal: '1', duration: '7h 25m', durationMin: 445, depTerminal: '5' },
      'AC860': { originCode: 'YHZ', originCity: 'Halifax', originName: 'Halifax Stanfield International', destCode: 'LHR', destCity: 'London', destTerminal: '2', duration: '5h 45m', durationMin: 345, depTerminal: 'Main' },
      'AC861': { originCode: 'LHR', originCity: 'London', originName: 'London Heathrow Airport', destCode: 'YHZ', destCity: 'Halifax', destTerminal: 'Main', duration: '6h 50m', durationMin: 410, depTerminal: '2' },
      'EK001': { originCode: 'DXB', originCity: 'Dubai', originName: 'Dubai International Airport', destCode: 'LHR', destCity: 'London', destTerminal: '3', duration: '7h 45m', durationMin: 465, depTerminal: '3' },
      'EK003': { originCode: 'DXB', originCity: 'Dubai', originName: 'Dubai International Airport', destCode: 'LHR', destCity: 'London', destTerminal: '3', duration: '7h 40m', durationMin: 460, depTerminal: '3' },
      'EK005': { originCode: 'DXB', originCity: 'Dubai', originName: 'Dubai International Airport', destCode: 'LHR', destCity: 'London', destTerminal: '3', duration: '7h 45m', durationMin: 465, depTerminal: '3' },
      'QR001': { originCode: 'DOH', originCity: 'Doha', originName: 'Hamad International Airport', destCode: 'LHR', destCity: 'London', destTerminal: '4', duration: '7h 15m', durationMin: 435, depTerminal: 'Main' },
      'QR003': { originCode: 'DOH', originCity: 'Doha', originName: 'Hamad International Airport', destCode: 'LHR', destCity: 'London', destTerminal: '4', duration: '7h 10m', durationMin: 430, depTerminal: 'Main' },
      'PK785': { originCode: 'ISB', originCity: 'Islamabad', originName: 'Islamabad International Airport', destCode: 'LHR', destCity: 'London', destTerminal: '2', duration: '8h 30m', durationMin: 510, depTerminal: '1' },
      'PK701': { originCode: 'ISB', originCity: 'Islamabad', originName: 'Islamabad International Airport', destCode: 'MAN', destCity: 'Manchester', destTerminal: '2', duration: '8h 45m', durationMin: 525, depTerminal: '1' },
      'PK702': { originCode: 'MAN', originCity: 'Manchester', originName: 'Manchester Airport', destCode: 'ISB', destCity: 'Islamabad', destTerminal: '1', duration: '7h 55m', durationMin: 475, depTerminal: '2' },
      'VS004': { originCode: 'JFK', originCity: 'New York', originName: 'John F. Kennedy International', destCode: 'LHR', destCity: 'London', destTerminal: '3', duration: '6h 55m', durationMin: 415, depTerminal: '4' },
      'VS045': { originCode: 'LHR', originCity: 'London', originName: 'London Heathrow Airport', destCode: 'JFK', destCity: 'New York', destTerminal: '4', duration: '8h 05m', durationMin: 485, depTerminal: '3' },
      'AA100': { originCode: 'JFK', originCity: 'New York', originName: 'John F. Kennedy International', destCode: 'LHR', destCity: 'London', destTerminal: '3', duration: '7h 00m', durationMin: 420, depTerminal: '8' },
      'DL001': { originCode: 'JFK', originCity: 'New York', originName: 'John F. Kennedy International', destCode: 'LHR', destCity: 'London', destTerminal: '3', duration: '7h 10m', durationMin: 430, depTerminal: '4' },
      'UA901': { originCode: 'SFO', originCity: 'San Francisco', originName: 'San Francisco International', destCode: 'LHR', destCity: 'London', destTerminal: '2', duration: '10h 30m', durationMin: 630, depTerminal: 'I' },
      'AF1680': { originCode: 'CDG', originCity: 'Paris', originName: 'Charles de Gaulle Airport', destCode: 'LHR', destCity: 'London', destTerminal: '4', duration: '1h 20m', durationMin: 80, depTerminal: '2E' },
      'KL1001': { originCode: 'AMS', originCity: 'Amsterdam', originName: 'Amsterdam Schiphol Airport', destCode: 'LHR', destCity: 'London', destTerminal: '4', duration: '1h 15m', durationMin: 75, depTerminal: '1' },
      'LH900': { originCode: 'FRA', originCity: 'Frankfurt', originName: 'Frankfurt Airport', destCode: 'LHR', destCity: 'London', destTerminal: '2', duration: '1h 35m', durationMin: 95, depTerminal: '1' },
      'TK1985': { originCode: 'IST', originCity: 'Istanbul', originName: 'Istanbul Airport', destCode: 'LHR', destCity: 'London', destTerminal: '2', duration: '3h 50m', durationMin: 230, depTerminal: 'I' }
    };

    const AIRLINE_DEFAULT_HUBS = {
      'BA': { originCode: 'INT', originCity: 'International', name: 'International Airport', duration: '3h 30m', durationMin: 210, destTerminal: '5' },
      'VS': { originCode: 'JFK', originCity: 'New York', name: 'John F. Kennedy International', duration: '7h 00m', durationMin: 420, destTerminal: '3' },
      'EK': { originCode: 'DXB', originCity: 'Dubai', name: 'Dubai International Airport', duration: '7h 45m', durationMin: 465, destTerminal: '3' },
      'QR': { originCode: 'DOH', originCity: 'Doha', name: 'Hamad International Airport', duration: '7h 15m', durationMin: 435, destTerminal: '4' },
      'EY': { originCode: 'AUH', originCity: 'Abu Dhabi', name: 'Zayed International Airport', duration: '7h 30m', durationMin: 450, destTerminal: '4' },
      'PK': { originCode: 'ISB', originCity: 'Islamabad', name: 'Islamabad International Airport', duration: '8h 30m', durationMin: 510, destTerminal: '2' },
      'AC': { originCode: 'YYZ', originCity: 'Toronto', name: 'Toronto Pearson International', duration: '7h 35m', durationMin: 455, destTerminal: '2' },
      'AA': { originCode: 'JFK', originCity: 'New York', name: 'John F. Kennedy International', duration: '7h 00m', durationMin: 420, destTerminal: '3' },
      'DL': { originCode: 'ATL', originCity: 'Atlanta', name: 'Hartsfield-Jackson Atlanta', duration: '8h 30m', durationMin: 510, destTerminal: '3' },
      'UA': { originCode: 'ORD', originCity: 'Chicago', name: "O'Hare International Airport", duration: '8h 15m', durationMin: 495, destTerminal: '2' },
      'AF': { originCode: 'CDG', originCity: 'Paris', name: 'Charles de Gaulle Airport', duration: '1h 20m', durationMin: 80, destTerminal: '4' },
      'KL': { originCode: 'AMS', originCity: 'Amsterdam', name: 'Amsterdam Schiphol Airport', duration: '1h 15m', durationMin: 75, destTerminal: '4' },
      'LH': { originCode: 'FRA', originCity: 'Frankfurt', name: 'Frankfurt Airport', duration: '1h 35m', durationMin: 95, destTerminal: '2' },
      'TK': { originCode: 'IST', originCity: 'Istanbul', name: 'Istanbul Airport', duration: '3h 50m', durationMin: 230, destTerminal: '2' },
      'SV': { originCode: 'JED', originCity: 'Jeddah', name: 'King Abdulaziz International', duration: '6h 30m', durationMin: 390, destTerminal: '4' },
      'AI': { originCode: 'DEL', originCity: 'Delhi', name: 'Indira Gandhi International', duration: '9h 00m', durationMin: 540, destTerminal: '2' },
      'FR': { originCode: 'DUB', originCity: 'Dublin', name: 'Dublin Airport', duration: '1h 15m', durationMin: 75, destTerminal: 'Main' },
      'U2': { originCode: 'GVA', originCity: 'Geneva', name: 'Geneva Airport', duration: '1h 40m', durationMin: 100, destTerminal: 'North' },
      'W6': { originCode: 'BUD', originCity: 'Budapest', name: 'Budapest Ferenc Liszt', duration: '2h 30m', durationMin: 150, destTerminal: 'Main' },
      'EI': { originCode: 'DUB', originCity: 'Dublin', name: 'Dublin Airport', duration: '1h 15m', durationMin: 75, destTerminal: '2' }
    };

    function calculateDepartureTime(arrTimeStr, durationMinutes) {
      if (!arrTimeStr || arrTimeStr === '-' || arrTimeStr === 'Scheduled') return 'Scheduled';
      let hours = 0, mins = 0;
      const match = arrTimeStr.match(/(\d{1,2})[:.](\d{2})\s*(am|pm)?/i);
      if (!match) return 'Scheduled';
      hours = parseInt(match[1], 10);
      mins = parseInt(match[2], 10);
      const meridiem = match[3] ? match[3].toLowerCase() : null;
      if (meridiem === 'pm' && hours < 12) hours += 12;
      if (meridiem === 'am' && hours === 12) hours = 0;

      let totalMinutes = hours * 60 + mins - (durationMinutes || 120);
      while (totalMinutes < 0) totalMinutes += 24 * 60;
      totalMinutes = totalMinutes % (24 * 60);

      let depH = Math.floor(totalMinutes / 60);
      let depM = totalMinutes % 60;
      let ampm = depH >= 12 ? 'pm' : 'am';
      depH = depH % 12;
      if (depH === 0) depH = 12;
      let depMStr = depM < 10 ? '0' + depM : depM;
      return `${depH}:${depMStr} ${ampm}`;
    }

    function formatTimeDisplay(timeStr) {
      if (!timeStr) return 'Scheduled';
      const match = timeStr.match(/(\d{1,2})[:.](\d{2})\s*(am|pm)?/i);
      if (!match) return timeStr;
      let h = parseInt(match[1], 10);
      let m = match[2];
      let p = match[3] ? match[3].toLowerCase() : '';
      if (!p) {
        p = h >= 12 ? 'pm' : 'am';
        h = h % 12;
        if (h === 0) h = 12;
      }
      return `${h}:${m} ${p}`;
    }

    window.openFlightTracker = function(event, flightNo, extra) {
      if (event) {
        event.stopPropagation();
        if (event.preventDefault) event.preventDefault();
      }
      if (!flightNo || flightNo === '-' || flightNo === 'undefined') return;

      const cleanFlight = String(flightNo).trim().toUpperCase();
      if (!cleanFlight) return;

      // Extract Airline Prefix (letters at start)
      const match = cleanFlight.match(/^([A-Z0-9]{2,3})/);
      const prefix = match ? match[1] : '';
      const fallbackAirlineName = AIRLINES_MAP[prefix] || AIRLINES_MAP[cleanFlight.slice(0, 2)] || 'Airline Flight';

      const modalEl = document.getElementById('flightTrackerModal');
      if (!modalEl) {
        window.open('https://www.google.com/search?q=' + encodeURIComponent('flight ' + cleanFlight), '_blank');
        return;
      }

      // Try to read contextual row data if event originated from a table row
      let passengerName = '';
      let pickupTime = '';
      let pickupDate = '';
      let pickupAddr = '';
      let dropoffAddr = '';
      let airportCode = 'LHR';
      let airportName = 'London Heathrow (LHR)';
      let terminalInfo = 'Terminal -';

      if (event && event.target) {
        const row = event.target.closest('tr');
        if (row) {
          const passEl = row.querySelector('.col-passenger, .passenger-cell');
          const dateEl = row.querySelector('.col-date');
          const timeEl = row.querySelector('.col-time');
          const pickEl = row.querySelector('.col-pickup');
          const dropEl = row.querySelector('.col-dropoff');

          passengerName = passEl ? passEl.textContent.trim() : '';
          pickupDate = dateEl ? dateEl.textContent.trim() : '';
          pickupTime = timeEl ? timeEl.textContent.trim() : '';
          pickupAddr = pickEl ? pickEl.textContent.trim() : '';
          dropoffAddr = dropEl ? dropEl.textContent.trim() : '';
        }
      }

      const fullRouteText = (pickupAddr && dropoffAddr) ? `${pickupAddr} ➔ ${dropoffAddr}` : (pickupAddr || dropoffAddr || '');
      const routeCheck = (pickupAddr + ' ' + dropoffAddr).toLowerCase();

      // Detect Destination Airport & Terminal from booking pickup/dropoff
      let airportArrivalsUrl = 'https://www.heathrow.com/arrivals';
      let airportBtnTitle = 'Heathrow Arrivals';

      if (routeCheck.includes('gatwick') || routeCheck.includes('lgw')) {
        airportCode = 'LGW';
        airportName = 'London Gatwick (LGW)';
        airportArrivalsUrl = 'https://www.gatwickairport.com/flights/arrivals/';
        airportBtnTitle = 'Gatwick Arrivals';
      } else if (routeCheck.includes('stansted') || routeCheck.includes('stn')) {
        airportCode = 'STN';
        airportName = 'London Stansted (STN)';
        airportArrivalsUrl = 'https://www.stanstedairport.com/flight-information/arrivals/';
        airportBtnTitle = 'Stansted Arrivals';
      } else if (routeCheck.includes('luton') || routeCheck.includes('ltn')) {
        airportCode = 'LTN';
        airportName = 'London Luton (LTN)';
        airportArrivalsUrl = 'https://www.london-luton.co.uk/flights';
        airportBtnTitle = 'Luton Arrivals';
      } else if (routeCheck.includes('city') || routeCheck.includes('lcy')) {
        airportCode = 'LCY';
        airportName = 'London City Airport';
        airportArrivalsUrl = 'https://www.londoncityairport.com/flight-status';
        airportBtnTitle = 'London City Arrivals';
      } else if (routeCheck.includes('manchester') || routeCheck.includes('man')) {
        airportCode = 'MAN';
        airportName = 'Manchester Airport (MAN)';
        airportArrivalsUrl = 'https://www.manchesterairport.co.uk/flight-information/arrivals/';
        airportBtnTitle = 'Manchester Arrivals';
      } else if (routeCheck.includes('birmingham') || routeCheck.includes('bhx')) {
        airportCode = 'BHX';
        airportName = 'Birmingham Airport';
        airportArrivalsUrl = 'https://www.birminghamairport.co.uk/flight-information/live-arrivals/';
        airportBtnTitle = 'Birmingham Arrivals';
      }

      // Check terminal from address
      const tMatch = routeCheck.match(/terminal\s*([0-9]|north|south)/i);
      let detectedTerminal = tMatch ? tMatch[1].toUpperCase() : '';

      // 🌟 Resolve Flight Route Data (Immediate Exact Sync Lookup) 🌟
      const known = KNOWN_ROUTES[cleanFlight] || null;
      const hub = AIRLINE_DEFAULT_HUBS[prefix] || AIRLINE_DEFAULT_HUBS[cleanFlight.slice(0, 2)] || null;

      let resolvedOriginCode = 'DEP';
      let resolvedOriginCity = 'International';
      let resolvedOriginName = 'Departure Airport';
      let resolvedDepTerminal = '1';
      let resolvedDuration = 'Direct Flight';
      let resolvedDurationMin = 180;
      let resolvedDestCode = airportCode;
      let resolvedDestCity = 'London';
      let resolvedDestTerminal = detectedTerminal || '5';

      if (known) {
        resolvedOriginCode = known.originCode;
        resolvedOriginCity = known.originCity;
        resolvedOriginName = known.originName;
        resolvedDepTerminal = known.depTerminal || '1';
        resolvedDuration = known.duration;
        resolvedDurationMin = known.durationMin;
        if (!detectedTerminal && known.destTerminal) {
          resolvedDestTerminal = known.destTerminal;
        }
        if (known.destCity) resolvedDestCity = known.destCity;
      } else if (hub) {
        resolvedOriginCode = hub.originCode;
        resolvedOriginCity = hub.originCity;
        resolvedOriginName = hub.name;
        resolvedDepTerminal = '1';
        resolvedDuration = hub.duration;
        resolvedDurationMin = hub.durationMin;
        if (!detectedTerminal && hub.destTerminal) {
          resolvedDestTerminal = hub.destTerminal;
        }
      }

      // Format Times
      const formattedArrTime = pickupTime ? formatTimeDisplay(pickupTime) : 'Scheduled';
      const calculatedDepTime = calculateDepartureTime(pickupTime, resolvedDurationMin);

      // Bind Google Flight Card Elements
      const googleTitle = document.getElementById('ftm-google-title');
      const googleSubtitle = document.getElementById('ftm-google-subtitle');
      const summaryTime = document.getElementById('ftm-summary-time');
      const summaryCode = document.getElementById('ftm-summary-code');
      const summaryDest = document.getElementById('ftm-summary-dest');
      const googleStatusPill = document.getElementById('ftm-google-status-pill');

      const gOriginCode = document.getElementById('ftm-g-origin-code');
      const originLink = document.getElementById('ftm-origin-link');
      const gDuration = document.getElementById('ftm-g-duration');
      const gDestCode = document.getElementById('ftm-g-dest-code');
      const destLink = document.getElementById('ftm-dest-link');

      const gDepHeader = document.getElementById('ftm-g-dep-header');
      const gDepTime = document.getElementById('ftm-g-dep-time');
      const gDepTerminal = document.getElementById('ftm-g-dep-terminal');
      const gDepGate = document.getElementById('ftm-g-dep-gate');

      const gArrHeader = document.getElementById('ftm-g-arr-header');
      const gArrTime = document.getElementById('ftm-g-arr-time');
      const gArrTerminal = document.getElementById('ftm-g-arr-terminal');
      const gArrGate = document.getElementById('ftm-g-arr-gate');

      const bookingContext = document.getElementById('ftm-booking-context');
      const passNameEl = document.getElementById('ftm-passenger-name');
      const pickupTimeEl = document.getElementById('ftm-pickup-time');
      const routeTextEl = document.getElementById('ftm-route-text');

      const fullFlightTitle = `${fallbackAirlineName} ${cleanFlight}`;
      if (googleTitle) googleTitle.textContent = fullFlightTitle;
      if (googleSubtitle) googleSubtitle.textContent = `${resolvedOriginCity} to ${resolvedDestCity}`;
      if (summaryCode) summaryCode.textContent = cleanFlight;
      if (summaryTime) summaryTime.textContent = formattedArrTime;
      if (summaryDest) summaryDest.textContent = `${airportName}`;
      if (googleStatusPill) googleStatusPill.textContent = 'ON TIME';

      if (gOriginCode) gOriginCode.textContent = resolvedOriginCode;
      if (originLink) originLink.href = 'https://www.google.com/search?q=' + encodeURIComponent(resolvedOriginName);
      if (gDuration) gDuration.textContent = resolvedDuration;
      if (gDestCode) gDestCode.textContent = resolvedDestCode;
      if (destLink) destLink.href = airportArrivalsUrl;

      // Setup Dates for Headers
      const today = new Date();
      const dateOptions = { weekday: 'short', day: 'numeric', month: 'short' };
      const currentDayStr = pickupDate || today.toLocaleDateString('en-GB', dateOptions);

      if (gDepHeader) gDepHeader.textContent = `${resolvedOriginCity} • ${currentDayStr}`;
      if (gDepTime) gDepTime.textContent = calculatedDepTime;
      if (gDepTerminal) gDepTerminal.textContent = resolvedDepTerminal;
      if (gDepGate) gDepGate.textContent = '-';

      if (gArrHeader) gArrHeader.textContent = `${resolvedDestCity} • ${currentDayStr}`;
      if (gArrTime) gArrTime.textContent = formattedArrTime;
      if (gArrTerminal) gArrTerminal.textContent = resolvedDestTerminal;
      if (gArrGate) gArrGate.textContent = '-';

      if (bookingContext) {
        if (passengerName || fullRouteText) {
          bookingContext.classList.remove('d-none');
          bookingContext.classList.add('d-flex');
          if (passNameEl) passNameEl.textContent = passengerName || 'Passenger';
          if (pickupTimeEl) pickupTimeEl.textContent = (pickupDate ? `${pickupDate}, ` : '') + (pickupTime || '-');
          if (routeTextEl) routeTextEl.textContent = fullRouteText || '';
        } else {
          bookingContext.classList.add('d-none');
          bookingContext.classList.remove('d-flex');
        }
      }

      // External Provider URLs
      const googleUrl = 'https://www.google.com/search?q=' + encodeURIComponent('flight ' + cleanFlight);
      const openGoogleBtn = document.getElementById('ftm-btn-open-google');
      if (openGoogleBtn) openGoogleBtn.href = googleUrl;

      // Background Fetch for Real-time Telemetry API
      const telemetryUrl = `/api/flight-telemetry?flight=${encodeURIComponent(cleanFlight)}&pickup_date=${encodeURIComponent(pickupDate)}&pickup_time=${encodeURIComponent(pickupTime)}`;
      fetch(telemetryUrl)
        .then(res => res.json())
        .then(data => {
          if (data && data.success) {
            const airlineDisplay = data.airline ? `${data.airline} ${cleanFlight}` : fullFlightTitle;
            if (googleTitle) googleTitle.textContent = airlineDisplay;
            
            const originCityName = data.origin && data.origin.city ? data.origin.city.split(',')[0] : resolvedOriginCity;
            const destCityName = data.destination && data.destination.city ? data.destination.city.split(',')[0] : resolvedDestCity;
            if (googleSubtitle) googleSubtitle.textContent = `${originCityName} to ${destCityName}`;

            if (summaryDest && data.destination) {
              summaryDest.textContent = `${data.destination.city || 'London'} ${data.destination.code || 'LHR'}`;
            }

            if (googleStatusPill && data.status_badge) {
              googleStatusPill.textContent = data.status_badge;
            }

            if (data.origin) {
              if (gOriginCode && data.origin.code) gOriginCode.textContent = data.origin.code;
              if (originLink) originLink.href = 'https://www.google.com/search?q=' + encodeURIComponent((data.origin.name || '') + ' airport');
              if (gDepHeader) gDepHeader.textContent = `${originCityName} • ${currentDayStr}`;
              if (gDepTerminal && data.origin.terminal) gDepTerminal.textContent = data.origin.terminal;
              if (gDepGate && data.origin.gate) gDepGate.textContent = data.origin.gate;
            }

            if (data.destination) {
              if (gDestCode && data.destination.code) gDestCode.textContent = data.destination.code;
              if (destLink && data.destination.code === 'LHR') destLink.href = 'https://www.heathrow.com/arrivals';
              if (gArrHeader) gArrHeader.textContent = `${destCityName} • ${currentDayStr}`;
              if (gArrTerminal && data.destination.terminal) gArrTerminal.textContent = data.destination.terminal;
              if (gArrGate && data.destination.gate) gArrGate.textContent = data.destination.gate;
            }

            if (data.duration && gDuration) gDuration.textContent = data.duration;
            if (data.dep_time) {
              if (gDepTime) gDepTime.textContent = data.dep_time;
            }
            if (data.arr_time) {
              if (summaryTime) summaryTime.textContent = data.arr_time;
              if (gArrTime) gArrTime.textContent = data.arr_time;
            }

            if (data.links && openGoogleBtn && data.links.google) {
              openGoogleBtn.href = data.links.google;
            }
          }
        })
        .catch(err => {
          console.warn('Flight telemetry background fetch notice:', err);
        });

      // Open Modal on the same screen
      const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.show();
    };
  })();
  </script>

  @stack('scripts')
</body>
</html>
