@extends('layouts.dashboard-v3')

@section('content')

<div class="dash-grid">

    <!-- TOP KPI ROW -->
    <div class="top-cards">

        <div class="stat-card purple">
            <div class="stat-title">Monthly Revenue</div>
            <div class="stat-value">$24,920</div>
            <div class="stat-growth positive">▲ 12.5% from last month</div>
        </div>

        <div class="stat-card blue">
            <div class="stat-title">Active Companies</div>
            <div class="stat-value">18</div>
            <div class="stat-growth positive">▲ 5.9% from last month</div>
        </div>

        <div class="stat-card green">
            <div class="stat-title">Subscriptions</div>
            <div class="stat-value">12</div>
            <div class="stat-growth positive">▲ 20% from last month</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-title">Platform Health</div>
            <div class="stat-value">97%</div>
            <div class="stat-growth neutral">Stable this month</div>
        </div>

        <div class="stat-card cyan">
            <div class="stat-title">All Time Revenue</div>
            <div class="stat-value">$248K</div>
            <div class="stat-growth positive">▲ 18% yearly growth</div>
        </div>

    </div>

    <!-- KEEP EXISTING BODY BELOW -->

    @include('admin.partials.dashboard-body')

</div>

@endsection
