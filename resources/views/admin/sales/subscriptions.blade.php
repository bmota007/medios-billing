@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h2 class="text-white mb-4">Active Subscriptions</h2>

    <div class="dashboard-card">
        <p>Total Companies: {{ isset($companies) ? count($companies) : 0 }}</p>
    </div>

</div>
@endsection
