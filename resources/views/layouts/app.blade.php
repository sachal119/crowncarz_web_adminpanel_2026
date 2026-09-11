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
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('live.map') ? 'active' : '' }}" href="{{ route('live.map') }}"><i class="bi bi-geo-alt-fill me-1"></i>Map</a></li>
          <li class="nav-item me-2"><a class="nav-link {{ request()->routeIs('booking.create') ? 'active' : '' }}" href="{{ route('booking.create') }}" target="_blank"><i class="bi bi-calendar-plus me-1"></i>Booking</a></li>
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
              <ul class="dropdown-menu shadow-lg border-0" aria-labelledby="historyDropdown" style="border-radius: 12px; min-width: 210px;">
                <li>
                  <a class="dropdown-item d-flex align-items-center {{ request()->routeIs('completed.jobs', 'bookings.search') ? 'active' : '' }}" href="{{ route('completed.jobs') }}">
                    <i class="bi bi-check-circle-fill me-2 text-success"></i>Completed Bookings
                  </a>
                </li>
                <li>
                  <a class="dropdown-item d-flex align-items-center {{ request()->routeIs('previous.bookings', 'previous.bookings.search') ? 'active' : '' }}" href="{{ route('previous.bookings') }}">
                    <i class="bi bi-calendar-check-fill me-2 text-warning"></i>Previous Bookings
                  </a>
                </li>
                <li>
                  <a class="dropdown-item d-flex align-items-center {{ request()->routeIs('bookings.cancelled', 'cancelled.bookings.search') ? 'active' : '' }}" href="{{ route('bookings.cancelled') }}">
                    <i class="bi bi-x-circle-fill me-2 text-danger"></i>Cancelled Bookings
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
          <!--<li class="nav-item me-3"><a class="nav-link" href="{{ route('system.settings') }}"><i class="bi bi-gear-wide-connected me-1"></i>Settings</a></li>-->
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

  @stack('scripts')
</body>
</html>
