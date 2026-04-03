@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    {{-- Header with Live Weather --}}
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h2 class="text-white font-bold mb-0">
                @if(auth()->user()->is_admin)
                    Super Admin <span style="color: #38bdf8">Dashboard</span>
                @else
                    {{ auth()->user()->company->name ?? 'Company' }} <span style="color: #38bdf8">Overview</span>
                @endif
            </h2>
            <p class="text-secondary small mb-0">Real-time platform metrics and activity</p>
        </div>
        
        {{-- WEATHER WIDGET --}}
        <div id="weather-widget" class="glass-card py-2 px-3 d-flex align-items-center gap-3 mb-0" style="min-width: 220px; border: 1px solid rgba(56, 189, 248, 0.2) !important;">
            <div id="weather-icon"><i class="fa-solid fa-temperature-half text-info fa-xl"></i></div>
            <div>
                <div id="weather-temp" class="text-white fw-bold">--°F</div>
                <div id="weather-city" class="text-white-50" style="font-size: 11px;">Locating...</div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-green-500/10 border-green-500/20 text-green-500 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger bg-red-500/10 border-red-500/20 text-red-500 mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats Row --}}
    <div class="row g-4 mb-4">
        @if(auth()->user()->is_admin)
        <div class="col-md-3">
            <div class="dashboard-card h-100 border-start border-primary border-4">
                <div class="text-white-50 small uppercase fw-bold mb-2" style="letter-spacing: 1px;">Total Companies</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-white fw-bold">{{ $companies ?? 0 }}</h2>
                    <i class="fa-solid fa-building text-primary opacity-25 fa-2x"></i>
                </div>
            </div>
        </div>
        @endif

        <div class="col-md-3">
            <div class="dashboard-card h-100 border-start border-success border-4">
                <div class="text-white-50 small uppercase fw-bold mb-2" style="letter-spacing: 1px;">Platform Revenue</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-success fw-bold">${{ number_format($revenue ?? 0, 2) }}</h2>
                    <i class="fa-solid fa-money-bill-trend-up text-success opacity-25 fa-2x"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card h-100 border-start border-info border-4">
                <div class="text-white-50 small uppercase fw-bold mb-2" style="letter-spacing: 1px;">SaaS MRR</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-info fw-bold">${{ number_format($mrr ?? 0, 2) }}</h2>
                    <i class="fa-solid fa-rotate text-info opacity-25 fa-2x"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card h-100 border-start border-warning border-4">
                <div class="text-white-50 small uppercase fw-bold mb-2" style="letter-spacing: 1px;">Active Tenants</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-white fw-bold">{{ $activeCompanies ?? 0 }}</h2>
                    <i class="fa-solid fa-users-gear text-warning opacity-25 fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity and Manual Charge Row --}}
    <div class="row g-4">
        {{-- Recent Activity --}}
        <div class="col-md-8">
            <div class="dashboard-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-white mb-0">Global Recent Invoices</h5>
                    <a href="{{ route('admin.billing') }}" class="btn btn-sm btn-outline-info">View All Billing</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-dark table-hover border-transparent align-middle">
                        <thead class="text-secondary small uppercase">
                            <tr>
                                <th>Company</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices as $invoice)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05)">
                                <td>
                                    <span class="text-info fw-bold">{{ $invoice->company->name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $invoice->customer_name ?? 'Subscription' }}</td>
                                <td class="fw-bold text-white">${{ number_format($invoice->amount ?? $invoice->total, 2) }}</td>
                                <td>
                                    <span class="badge {{ ($invoice->status === 'paid') ? 'bg-success' : 'bg-warning text-dark' }} px-3">
                                        {{ strtoupper($invoice->status) }}
                                    </span>
                                </td>
                                <td class="text-end text-secondary small">
                                    {{ $invoice->created_at->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-secondary">No recent platform activity.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MANUAL CHARGE AREA --}}
        <div class="col-md-4">
            <div class="dashboard-card border border-warning/20">
                <h5 class="text-white mb-3"><i class="fa-solid fa-hand-holding-dollar text-warning me-2"></i> Quick Manual Charge</h5>
                <p class="text-secondary small">Initiate a custom charge for any customer on the platform.</p>
                
                <form action="{{ route('admin.manual-charge') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="text-secondary small uppercase fw-bold">Amount ($)</label>
                        <input type="number" name="amount" step="0.01" class="form-control bg-transparent border-slate-700 text-white" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-secondary small uppercase fw-bold">Customer Email</label>
                        <input type="email" name="customer_email" class="form-control bg-transparent border-slate-700 text-white" placeholder="client@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-secondary small uppercase fw-bold">Description</label>
                        <input type="text" name="description" class="form-control bg-transparent border-slate-700 text-white" placeholder="Custom Service Fee">
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold uppercase">Process Charge</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Weather Script --}}
<script>
    async function getWeather() {
        try {
            const locRes = await fetch('https://ipapi.co/json/');
            const locData = await locRes.json();
            const weatherRes = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${locData.latitude}&longitude=${locData.longitude}&current_weather=true&temperature_unit=fahrenheit`);
            const weatherData = await weatherRes.json();
            
            document.getElementById('weather-temp').innerText = Math.round(weatherData.current_weather.temperature) + '°F';
            document.getElementById('weather-city').innerText = locData.city + ', ' + locData.region_code;
            
            const code = weatherData.current_weather.weathercode;
            const icon = document.querySelector('#weather-icon i');
            if(code === 0) icon.className = "fa-solid fa-sun text-warning fa-xl";
            else if(code < 4) icon.className = "fa-solid fa-cloud-sun text-info fa-xl";
            else icon.className = "fa-solid fa-cloud text-secondary fa-xl";
        } catch (e) {
            document.getElementById('weather-city').innerText = "Weather Offline";
        }
    }
    getWeather();
</script>

<style>
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
    .dashboard-card { 
        background: rgba(30, 41, 59, 0.6); 
        backdrop-filter: blur(12px); 
        border: 1px solid rgba(255, 255, 255, 0.1); 
        border-radius: 16px; 
        padding: 24px;
        transition: transform 0.2s; 
    }
    .dashboard-card:hover { transform: translateY(-5px); }
    .badge { font-size: 10px; font-weight: 800; }
</style>
@endsection
