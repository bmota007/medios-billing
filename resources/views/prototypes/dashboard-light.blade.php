@extends('layouts.app')

@section('content')

<style>
body{
    background:
        radial-gradient(circle at top right, rgba(37,99,235,.08), transparent 30%),
        radial-gradient(circle at bottom left, rgba(14,165,233,.06), transparent 30%),
        #f8fafc;
}

.v3-shell{
    max-width:1700px;
    margin:0 auto;
    padding:10px 0 40px;
}

.v3-grid-top,
.v3-grid-kpi,
.v3-main,
.v3-bottom{
    display:grid;
    gap:22px;
    margin-bottom:22px;
}

.v3-grid-top{grid-template-columns:2fr 1fr 1fr;}
.v3-grid-kpi{grid-template-columns:repeat(4,1fr);}
.v3-main{grid-template-columns:2fr 1fr;}
.v3-bottom{grid-template-columns:1fr 1fr 1fr;}

.v3-card{
    background:#ffffff;
    border:1px solid rgba(15,23,42,.06);
    border-radius:26px;
    padding:24px;
    box-shadow:0 18px 40px rgba(15,23,42,.08);
}

.v3-title{
    font-size:14px;
    color:#64748b;
    margin-bottom:10px;
}

.v3-metric{
    font-size:42px;
    font-weight:800;
    color:#0f172a;
    line-height:1;
}

.v3-small{
    color:#64748b;
    font-size:13px;
}

.v3-green{color:#16a34a;}
.v3-blue{color:#0284c7;}
.v3-yellow{color:#ca8a04;}
.v3-purple{color:#9333ea;}

.quick-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
}

.quick-btn{
    display:block;
    padding:18px;
    border-radius:18px;
    background:#f8fafc;
    border:1px solid rgba(15,23,42,.06);
    color:#0f172a;
    text-decoration:none;
    transition:.2s;
}

.quick-btn:hover{
    background:#eff6ff;
    color:#0f172a;
}

.line{
    padding:14px 0;
    border-bottom:1px solid rgba(15,23,42,.06);
}

.line:last-child{
    border-bottom:none;
}

@media(max-width:1400px){
    .v3-grid-top,
    .v3-grid-kpi,
    .v3-main,
    .v3-bottom{
        grid-template-columns:1fr;
    }

    .quick-grid{
        grid-template-columns:1fr;
    }
}
</style>

<div class="v3-shell">

<div class="v3-grid-top">

<div class="v3-card">
    <div class="text-dark text-4xl fw-bold">
        Good Morning, {{ auth()->user()->name }} ☀️
    </div>

    <div class="v3-small mt-3">
        Welcome back to MediosBilling Business Center
    </div>

    <div class="mt-4">
        <span class="badge bg-primary px-3 py-2">
            Business Growth Suite
        </span>
    </div>
</div>

<div class="v3-card">
    <div class="v3-title">Current Plan</div>
    <div class="v3-metric">Elite</div>
    <div class="v3-green mt-3 fw-bold">ACTIVE</div>
</div>

<div class="v3-card">
    <div class="v3-title">Theme Mode</div>
    <div class="v3-metric">Light</div>
    <div class="v3-small mt-3">Auto-switch ready</div>
</div>

</div>

<div class="v3-grid-kpi">

<div class="v3-card">
    <div class="v3-title">Revenue</div>
    <div class="v3-metric">$24,880</div>
    <div class="v3-green mt-3">+18%</div>
</div>

<div class="v3-card">
    <div class="v3-title">Invoices</div>
    <div class="v3-metric">92</div>
    <div class="v3-blue mt-3">This Month</div>
</div>

<div class="v3-card">
    <div class="v3-title">Paid</div>
    <div class="v3-metric">77</div>
    <div class="v3-green mt-3">Collected</div>
</div>

<div class="v3-card">
    <div class="v3-title">Pending</div>
    <div class="v3-metric">15</div>
    <div class="v3-yellow mt-3">Attention</div>
</div>

</div>

<div class="v3-main">

<div class="v3-card">
    <div class="d-flex justify-content-between mb-4">
        <div class="text-dark h4 m-0">Revenue Performance</div>
        <div class="v3-small">Last 12 Months</div>
    </div>

    <canvas id="chartMain" height="120"></canvas>
</div>

<div class="v3-card">
    <div class="text-dark h4 mb-4">Quick Actions</div>

    <div class="quick-grid">
        <a href="#" class="quick-btn">Create Invoice</a>
        <a href="#" class="quick-btn">Manage Quotes</a>
        <a href="#" class="quick-btn">Customers</a>
        <a href="#" class="quick-btn">Tax Center</a>
        <a href="#" class="quick-btn">AI Coach</a>
        <a href="#" class="quick-btn">Settings</a>
    </div>
</div>

</div>

<div class="v3-bottom">

<div class="v3-card">
    <div class="text-dark h5 mb-3">Recent Invoices</div>

    <div class="line d-flex justify-content-between">
        <span>INV-1001</span><span class="v3-green">$480</span>
    </div>

    <div class="line d-flex justify-content-between">
        <span>INV-1002</span><span class="v3-yellow">$920</span>
    </div>

    <div class="line d-flex justify-content-between">
        <span>INV-1003</span><span class="v3-green">$1,200</span>
    </div>
</div>

<div class="v3-card">
    <div class="text-dark h5 mb-3">AI Business Coach</div>

    <div class="line">Follow up unpaid invoices today.</div>
    <div class="line">Your quote close rate can improve 19%.</div>
    <div class="line">Revenue trend is positive this month.</div>
</div>

<div class="v3-card">
    <div class="text-dark h5 mb-3">Tax Snapshot</div>

    <div class="line d-flex justify-content-between">
        <span>YTD Revenue</span><span>$84,400</span>
    </div>

    <div class="line d-flex justify-content-between">
        <span>Expenses</span><span>$21,100</span>
    </div>

    <div class="line d-flex justify-content-between">
        <span>Estimated Tax</span><span class="v3-yellow">$9,840</span>
    </div>
</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('chartMain'),{
type:'line',
data:{
labels:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
datasets:[{
data:[8,12,10,14,18,16,21,19,24,26,28,31],
borderColor:'#0284c7',
backgroundColor:'rgba(2,132,199,.10)',
fill:true,
tension:.4,
borderWidth:3,
pointRadius:3
}]
},
options:{
responsive:true,
plugins:{legend:{display:false}},
scales:{
x:{ticks:{color:'#64748b'},grid:{display:false}},
y:{ticks:{color:'#64748b'},grid:{color:'rgba(15,23,42,.05)'}}
}
}
});
</script>

@endsection
