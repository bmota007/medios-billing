@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    {{-- Header Section with Personalized Greeting --}}
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h2 class="text-white font-bold mb-0">
                {{ $greeting }}, <span class="text-sky-400">{{ explode(' ', auth()->user()->name)[0] }}</span>
            </h2>
            <p class="text-secondary mb-0">Welcome back to the <strong>{{ $brandName }}</strong> portal.</p>
        </div>

        {{-- WEATHER WIDGET --}}
        <div id="weather-widget" class="glass-card py-2 px-3 d-flex align-items-center gap-3 mb-0" style="min-width: 220px; border: 1px solid rgba(56, 189, 248, 0.2) !important;">
            <div id="weather-icon"><i class="fa-solid fa-cloud-sun text-info fa-xl"></i></div>
            <div>
                <div id="weather-temp" class="text-white fw-bold">--°F</div>
                <div id="weather-city" class="text-white-50" style="font-size: 11px;">Fetching Weather...</div>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="dashboard-card h-100 border-start border-success border-4">
                <div class="text-white-50 small uppercase fw-bold mb-2">Total Revenue</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-success fw-bold">${{ number_format($revenue ?? 0, 2) }}</h2>
                    <i class="fa-solid fa-dollar-sign text-success opacity-25 fa-2x"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card h-100 border-start border-primary border-4">
                <div class="text-white-50 small uppercase fw-bold mb-2">Total Invoices</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-white fw-bold">{{ $invoicesCount ?? 0 }}</h2>
                    <i class="fa-solid fa-file-invoice text-primary opacity-25 fa-2x"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card h-100 border-start border-info border-4">
                <div class="text-white-50 small uppercase fw-bold mb-2">Paid Invoices</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-info fw-bold">{{ $paidInvoices ?? 0 }}</h2>
                    <i class="fa-solid fa-check-double text-info opacity-25 fa-2x"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card h-100 border-start border-warning border-4">
                <div class="text-white-50 small uppercase fw-bold mb-2">Pending Invoices</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-warning fw-bold">{{ $pendingInvoices ?? 0 }}</h2>
                    <i class="fa-solid fa-clock text-warning opacity-25 fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue Overview Chart --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="dashboard-card">
                <h3 class="text-lg font-semibold text-white mb-4">Revenue Overview</h3>
                <div id="revenueChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>

    {{-- Recent Invoices Table --}}
    <div class="row g-4">
        <div class="col-12">
            <div class="dashboard-card">
                <h3 class="text-lg font-semibold text-white mb-4">Recent Invoices</h3>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr class="text-secondary small uppercase">
                                <th class="pb-3 border-0">Invoice</th>
                                <th class="pb-3 border-0">Customer</th>
                                <th class="pb-3 border-0">Amount</th>
                                <th class="pb-3 border-0">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-light">
                            @foreach($recentInvoices as $invoice)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05)">
                                <td class="py-3 font-mono text-sky-400">#{{ $invoice->invoice_no ?? $invoice->id }}</td>
                                <td>{{ $invoice->customer_name }}</td>
                                <td class="text-white fw-bold">${{ number_format($invoice->total, 2) }}</td>
                                <td>
                                    @if($invoice->status == 'paid')
                                        <span class="badge bg-success px-3">PAID</span>
                                    @else
                                        <span class="badge bg-warning text-dark px-3">PENDING</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    async function getWeather() {
        try {
            const locRes = await fetch('https://ipapi.co/json/');
            const locData = await locRes.json();
            const weatherRes = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${locData.latitude}&longitude=${locData.longitude}&current_weather=true&temperature_unit=fahrenheit`);
            const weatherData = await weatherRes.json();
            document.getElementById('weather-temp').innerText = Math.round(weatherData.current_weather.temperature) + '°F';
            document.getElementById('weather-city').innerText = locData.city;
        } catch (e) { 
            document.getElementById('weather-city').innerText = "Weather Unavailable"; 
        }
    }
    getWeather();

    document.addEventListener("DOMContentLoaded", function() {
        var options = {
            series: [{ name: 'Revenue', data: @json(array_values($revenueTrend)) }],
            chart: { type: 'area', height: 350, toolbar: { show: false }, foreColor: '#94a3b8', background: 'transparent' },
            colors: ['#38bdf8'],
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.1 } },
            xaxis: { categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"] },
            grid: { borderColor: 'rgba(255,255,255,0.05)' },
            dataLabels: { enabled: false }
        };
        new ApexCharts(document.querySelector("#revenueChart"), options).render();
    });
</script>

<style>
    .font-bold { font-weight: 700; }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
    .dashboard-card { transition: transform 0.2s; background: rgba(15,23,42,0.9); padding: 20px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.05); }
    .dashboard-card:hover { transform: translateY(-5px); }
    .glass-card { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(10px); border-radius: 12px; }
</style>
@endsection
