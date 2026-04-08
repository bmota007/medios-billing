@extends('layouts.app')

@section('content')
<div style="max-width:1200px; margin:auto; padding:30px;">

    <!-- HEADER -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
        <div>
            <h2 style="color:white; font-weight:800;">
                {{ $greeting }}, {{ auth()->user()->name }}
            </h2>
            <p style="color:#94a3b8;">
                Welcome back to {{ $company->name }}
            </p>
        </div>

        <div style="
            padding:8px 14px;
            border-radius:10px;
            font-weight:bold;
            background: {{ $stripeStatus == 'LIVE' ? 'rgba(34,197,94,0.15)' : 'rgba(245,158,11,0.15)' }};
            color: {{ $stripeStatus == 'LIVE' ? '#22c55e' : '#f59e0b' }};
        ">
            ⚡ STRIPE: {{ $stripeStatus }}
        </div>
    </div>

    <!-- 🔥 STATS -->
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:20px; margin-bottom:40px;">

        <div style="padding:25px; border-radius:16px; border:2px solid #22c55e; background:rgba(15,23,42,0.6);">
            <div style="color:#94a3b8; font-size:13px;">Total Revenue</div>
            <div style="font-size:28px; font-weight:800; color:#22c55e;">
                ${{ number_format($stats['total_revenue'], 2) }}
            </div>
        </div>

        <div style="padding:25px; border-radius:16px; border:2px solid #3b82f6; background:rgba(15,23,42,0.6);">
            <div style="color:#94a3b8; font-size:13px;">Total Invoices</div>
            <div style="font-size:28px; font-weight:800; color:white;">
                {{ $stats['total_invoices'] }}
            </div>
        </div>

        <div style="padding:25px; border-radius:16px; border:2px solid #06b6d4; background:rgba(15,23,42,0.6);">
            <div style="color:#94a3b8; font-size:13px;">Paid</div>
            <div style="font-size:28px; font-weight:800; color:#06b6d4;">
                {{ $stats['paid_invoices'] }}
            </div>
        </div>

        <div style="padding:25px; border-radius:16px; border:2px solid #f59e0b; background:rgba(15,23,42,0.6);">
            <div style="color:#94a3b8; font-size:13px;">Pending</div>
            <div style="font-size:28px; font-weight:800; color:#f59e0b;">
                {{ $stats['pending_invoices'] }}
            </div>
        </div>

    </div>

    <!-- 📊 CHART -->
    <div style="background:rgba(15,23,42,0.6); padding:25px; border-radius:16px; margin-bottom:40px;">
        <h4 style="color:white; margin-bottom:20px;">Revenue Overview</h4>

        <div style="height:320px;">
            <canvas id="chart"></canvas>
        </div>
    </div>

    <!-- 💎 ACTIVITY -->
    <div style="background:rgba(15,23,42,0.6); padding:25px; border-radius:16px;">

        <h4 style="color:white; margin-bottom:20px;">Recent Activity</h4>

        @forelse($recentInvoices as $inv)

        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:12px;
            border-radius:10px;
            margin-bottom:10px;
            transition:0.2s;
        "
        onmouseover="this.style.background='rgba(255,255,255,0.05)'"
        onmouseout="this.style.background='transparent'">

            <div style="display:flex; gap:12px; align-items:center;">
                <div style="
                    width:40px;
                    height:40px;
                    border-radius:10px;
                    background:rgba(56,189,248,0.2);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    color:#38bdf8;
                ">
                    📄
                </div>

                <div>
                    <strong style="color:white;">#{{ $inv->invoice_no }}</strong>
                    <div style="color:#94a3b8; font-size:12px;">
                        {{ $inv->customer_name }}
                    </div>
                </div>
            </div>

            <div style="text-align:right;">
                <div style="color:white; font-weight:700;">
                    ${{ number_format($inv->total, 2) }}
                </div>

                <div style="
                    font-size:11px;
                    padding:3px 10px;
                    border-radius:999px;
                    margin-top:5px;
                    display:inline-block;
                    background:
                    {{ $inv->status == 'paid' ? '#dcfce7' :
                       ($inv->status == 'pending' ? '#fef3c7' : '#fee2e2') }};
                    color:
                    {{ $inv->status == 'paid' ? '#166534' :
                       ($inv->status == 'pending' ? '#92400e' : '#991b1b') }};
                ">
                    {{ strtoupper($inv->status) }}
                </div>
            </div>

        </div>

        @empty
            <p style="color:#94a3b8;">No activity yet</p>
        @endforelse

    </div>

</div>

<!-- CHART -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chart'), {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{
            data: @json($chartData),
            borderColor: '#38bdf8',
            backgroundColor: 'rgba(56,189,248,0.15)',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 10
        }]
    },
    options: {
        responsive:true,
        maintainAspectRatio:false,
        interaction:{ mode:'index', intersect:false },
        plugins:{ legend:{ display:false }},
        scales:{
            y:{
                ticks:{
                    color:'#94a3b8',
                    callback:v=>'$'+v
                },
                grid:{ color:'rgba(255,255,255,0.05)' }
            },
            x:{
                ticks:{ color:'#94a3b8' },
                grid:{ display:false }
            }
        }
    }
});
</script>
@endsection
