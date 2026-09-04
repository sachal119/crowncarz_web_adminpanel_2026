<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <title>Crown Carz Panel</title>
  <link href="{{ asset('public/images/logo_black.png')}}"/ rel="icon" type="image/x-icon">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <style>
    .navbar-custom {
      background-color: #000; /* Top navbar black */
      height: 65px;
    }
    .navbar-custom .navbar-brand,
    .navbar-custom .nav-link {
      color: #fff;
      font-size: 12.5px;
    }
    .navbar-custom .nav-link:hover {
      color: #ffc107; /* Golden hover effect */
    }
    .main-content {
      padding: 20px;
    }
    .nav-item.dropdown:hover .dropdown-menu {
    display: block;
    margin-top: 0; /* Fix slight offset */
    }
    .leaflet-control-attribution {
    display: none !important;
  }
  </style>
  <style>
  /* Responsive font scaling */
  html {
    font-size: 16px; /* base for large screens */
  }

  @media (max-width: 1200px) {
    html {
      font-size: 15px;
    }
  }

  @media (max-width: 992px) {
    html {
      font-size: 14px;
    }
  }

  @media (max-width: 768px) {
    html {
      font-size: 13px;
    }
  }

  @media (max-width: 576px) {
    html {
      font-size: 12px;
    }
  }

  /* Optional: ensure table text doesn’t overflow */
  table {
    font-size: 0.95rem;
  }

  .small {
    font-size: 0.9rem !important;
  }

  h1, h2, h3, h4, h5, h6 {
    line-height: 1.2;
  }
  
 th {
  font-size: 12px ;
}

td{
    font-size: 12px;
}


/* Prevent dropdown from forcing page/table scroll */
.dropdown-menu {
  position: absolute !important;
  will-change: transform;
}
/* 🌟 Modern Dashboard Aesthetics */
.dashboard-section {
  padding: 1.5rem;
  border-radius: 12px;
}

/* === Card Styles === */
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

/* === Gradient backgrounds for cards === */
.bg-success {
  background: linear-gradient(135deg, #16a085, #1abc9c) !important;
}
.bg-info {
  background: linear-gradient(135deg, #3498db, #5dade2) !important;
}
.bg-danger {
  background: linear-gradient(135deg, #e74c3c, #ff7675) !important;
}
.bg-secondary {
  background: linear-gradient(135deg, #7f8c8d, #95a5a6) !important;
}
.bg-primary {
  background: linear-gradient(135deg, #2e86de, #54a0ff) !important;
}
.bg-dark {
  background: linear-gradient(135deg, #2f3640, #353b48) !important;
}

/* === Card content === */
.dashboard-card .card-body {
  padding: 1.3rem 1rem;
}
.dashboard-card h1 {
  font-size: 2rem;
  margin-bottom: 0.4rem;
}
.dashboard-card h4 {
  font-size: 1.6rem;
  font-weight: 700;
}
.dashboard-card p {
  font-size: 0.9rem;
  opacity: 0.9;
}

/* === Section headers === */
.card-header {
  border-bottom: 1px solid #e3e6f0;
  font-weight: 600;
}

/* === Map container === */
#map {
  border-radius: 0 0 16px 16px;
}

/* === Pie Chart === */
#revenuePieChart {
  max-height: 240px;
}

/* === Responsive tweaks === */
@media (max-width: 768px) {
  .dashboard-card h1 {
    font-size: 1.6rem;
  }
  .dashboard-card h4 {
    font-size: 1.3rem;
  }
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
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid" style="
    margin-left: 16px;
    margin-right: 16px;
">
      <a class="navbar-brand mx-auto" href="{{ route('dashboard') }}">
        <img src="{{ asset('public/images/logo.png') }}" alt="Crown Carz Logo" style="width: 75px;">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-center" id="navbarContent">
        <ul class="navbar-nav">
          <li class="nav-item me-3"><a class="nav-link" href="{{ route('dashboard') }}"><i class="bi bi-grid-fill me-1"></i>Dashboard</a></li>
          <li class="nav-item me-3"><a class="nav-link" href="{{ route('booking.create') }}"  target="_blank"><i class="bi bi-calendar-plus me-1"></i>Make Booking</a></li>
          @if(session('staff_role', 'super_admin') !== 'collaborator')
          <li class="nav-item dropdown me-3">
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
          
          <li class="nav-item me-3"><a class="nav-link" href="{{ route('completed.jobs') }}"><i class="bi bi-check-circle me-1"></i>Completed Jobs</a></li>
          <li class="nav-item me-3"><a class="nav-link" href="{{ route('previous.bookings') }}"><i class="bi bi-check-circle me-1"></i>Previous Jobs</a></li>
          <li class="nav-item me-3"><a class="nav-link" href="{{ route('bookings.cancelled') }}"><i class="bi bi-check-circle me-1"></i>Cancelled Jobs</a></li>
          
          
          
          
          
          <!-- Messages Dropdown -->
@if(session('staff_role', 'super_admin') !== 'collaborator')
<li class="nav-item dropdown me-3">

            
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-chat-dots-fill me-1"></i>Messages
              </a>
              <ul class="dropdown-menu" aria-labelledby="messagesDropdown">
                <li><h6 class="dropdown-header">Customer</h6></li>
                <li><a class="dropdown-item" href="{{ route('messages.customer.booking') }}">Booking Confirmation SMS</a></li>
                <li><a class="dropdown-item" href="{{ route('messages.customer.onroute') }}">Onroute SMS</a></li>
                <li><a class="dropdown-item" href="{{ route('messages.customer.arrival') }}">Arrival SMS</a></li>
                <li><a class="dropdown-item" href="{{ route('messages.customer.complete') }}">Job Complete SMS</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header">Driver</h6></li>
                <li><a class="dropdown-item" href="{{ route('messages.driver.details') }}">Job Details SMS</a></li>
                <!--<li><a class="dropdown-item" href="{{ route('messages.driver.change') }}">Job Change SMS</a></li>-->
                <!--<li><a class="dropdown-item" href="{{ route('messages.driver.office') }}">Office to Driver Messages</a></li>-->
              </ul>
          </li>
          <li class="nav-item dropdown me-3">
              <a class="nav-link dropdown-toggle" href="#" id="pricingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-currency-pound me-1"></i>Pricing
              </a>
              <ul class="dropdown-menu" aria-labelledby="pricingDropdown">
                <li><a class="dropdown-item" href="{{ route('pricing.fixed') }}">Fixed Pricing</a></li>
                <li><a class="dropdown-item" href="{{ route('pricing.mileage') }}">Mileage Pricing</a></li>
                <li><a class="dropdown-item" href="{{ route('locations.index') }}">Location Based Pricing</a></li>
              </ul>
          </li>
          

          <li class="nav-item me-3"><a class="nav-link" href="{{ route('reports') }}"><i class="bi bi-graph-up me-1"></i>Reports</a></li>
          <li class="nav-item me-3"><a class="nav-link" href="{{ route('setup') }}"><i class="bi bi-gear me-1"></i>Setup Office</a></li>
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
      <form class="d-flex ms-auto" method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-danger"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
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
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <script>
    // Initialize map
    var map = L.map('map').setView([31.5204, 74.3587], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Expand Map functionality
    var expandedMap;
    var mapModal = new bootstrap.Modal(document.getElementById('mapModal'));

    document.getElementById('expandMapBtn').addEventListener('click', function() {
      mapModal.show();
    });

    document.getElementById('mapModal').addEventListener('shown.bs.modal', function () {
      if (!expandedMap) {
        expandedMap = L.map('mapExpanded').setView([31.5204, 74.3587], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors'
        }).addTo(expandedMap);
      } else {
        expandedMap.invalidateSize();
        expandedMap.setView([31.5204, 74.3587], 12);
      }
    });

    // Auto-refresh bookings and map every 30 seconds
    setInterval(() => {
      location.reload();
    }, 30000);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Load metrics from LocalStorage
    const totalUsers = parseInt(localStorage.getItem("totalUsers")) || 0;
    const todaysRevenue = parseFloat(localStorage.getItem("todaysRevenue")) || 0.00;
    const revenueBreakdown = JSON.parse(localStorage.getItem("revenueBreakdown")) || { Cash: 0, Card: 0, Account: 0 };

    // Update display elements
    document.getElementById("totalUsersDisplay").textContent = totalUsers;
    document.getElementById("todaysRevenueDisplay").textContent = `£${todaysRevenue.toFixed(2)}`;

    // Create pie chart using LocalStorage data
    const ctx = document.getElementById('revenuePieChart');
    if (ctx) {
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
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
<!--  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>-->
<!--<script>-->
<!--document.addEventListener("DOMContentLoaded", function() {-->
<!--  const ctx = document.getElementById('revenuePieChart');-->
<!--  if (ctx) {-->
<!--    new Chart(ctx, {-->
<!--      type: 'pie',-->
<!--      data: {-->
<!--        labels: {!! json_encode(array_keys($revenueBreakdown)) !!},-->
<!--        datasets: [{-->
<!--          data: {!! json_encode(array_values($revenueBreakdown)) !!},-->
<!--          backgroundColor: ['#1abc9c', '#3498db', '#f39c12'],-->
<!--          borderWidth: 1-->
<!--        }]-->
<!--      },-->
<!--      options: {-->
<!--        plugins: {-->
<!--          legend: {-->
<!--            display: true,-->
<!--            position: 'bottom'-->
<!--          }-->
<!--        }-->
<!--      }-->
<!--    });-->
<!--  }-->
<!--});-->
<!--</script>-->

<!--<script>-->
    <!--// Load and display from LocalStorage-->
<!--    const totalUsers = parseInt(localStorage.getItem("totalUsers")) || 0;-->
<!--    const todaysRevenue = parseFloat(localStorage.getItem("todaysRevenue")) || 0.00;-->

<!--    document.getElementById("totalUsersDisplay").textContent = totalUsers;-->
<!--    document.getElementById("todaysRevenueDisplay").textContent = `£${todaysRevenue.toFixed(2)}`;-->

<!--</script>-->




  @stack('scripts')
</body>
</html>
