@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-primary">
            🚖 Turnover Report <small class="text-muted">({{ $from }} - {{ $to }})</small>
        </h2>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2">← Go Back</a>
            <button onclick="window.print()" class="btn btn-outline-primary me-2">🖨️ Print</button>
            <!--<a href="{{ route('reports.turnover.download', ['from' => $from, 'to' => $to]) }}" class="btn btn-success">⬇️ Download PDF</a>-->
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">
            <canvas id="turnoverChart" height="120"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('turnoverChart').getContext('2d');

    // Create gradient background
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(54, 162, 235, 0.4)');
    gradient.addColorStop(1, 'rgba(54, 162, 235, 0)');

    const turnoverChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($dailyTurnover)) !!}, // dates
            datasets: [{
                label: 'Daily Turnover (£)',
                data: {!! json_encode(array_values($dailyTurnover)) !!}, // sums
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { 
                    display: true, 
                    position: 'bottom',
                    labels: { font: { size: 14, weight: 'bold' }, color: '#333' }
                },
                title: { 
                    display: true, 
                    text: '📊 Turnover Per Day',
                    font: { size: 18, weight: 'bold' },
                    color: '#444'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw || 0;
                            return ' £' + value.toLocaleString();
                        }
                    },
                    backgroundColor: 'rgba(0,0,0,0.7)',
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#555', font: { size: 12 } },
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { 
                        callback: value => '£' + value,
                        color: '#555',
                        font: { size: 12 }
                    },
                    grid: { color: 'rgba(200,200,200,0.2)' }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeOutQuart'
            }
        }
    });
</script>
@endsection
