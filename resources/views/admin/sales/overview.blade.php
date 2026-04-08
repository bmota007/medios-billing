@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h2 class="text-white mb-4">Sales Overview</h2>

    <div class="dashboard-card">
        <p>Total MRR: ${{ number_format($stats['total_mrr'] ?? 0, 2) }}</p>
        <p>Active Tenants: {{ $stats['active_tenants'] ?? 0 }}</p>
        <p>New Leads: {{ $stats['new_leads'] ?? 0 }}</p>
        <p>Total Revenue: ${{ number_format($stats['total_revenue_collected'] ?? 0, 2) }}</p>
    </div>

</div>
@endsection
