@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="crm-header mb-5">
        <div>
            <h2 class="text-white font-bold mb-0">{{ $greeting }}, {{ explode(' ', auth()->user()->name)[0] }}</h2>
            <p class="text-secondary">Operations Portal for {{ $company->name }}</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="dashboard-card" style="background: rgba(15,23,42,0.9); border-radius: 14px; border: 1px solid rgba(255,255,255,0.05); padding: 30px;">
                <h4 class="text-white mb-4"><i class="fa-solid fa-list-check me-2 text-sky-400"></i> Recent Quotes</h4>
                
                @if($recentQuotes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr class="text-secondary small uppercase">
                                    <th>Quote #</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentQuotes as $quote)
                                <tr>
                                    <td class="text-sky-400">#{{ $quote->id }}</td>
                                    <td>{{ $quote->customer_name }}</td>
                                    <td><span class="badge bg-info">{{ strtoupper($quote->status) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-secondary opacity-50 italic">No recent activity found.</p>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card" style="background: rgba(15,23,42,0.9); border-radius: 14px; border: 1px solid rgba(255,255,255,0.05); padding: 30px;">
                <h4 class="text-white mb-3">Quick Actions</h4>
                <div class="d-grid gap-2">
                    <a href="{{ route('quotes.index') }}" class="btn btn-outline-info text-start">
                        <i class="fa-solid fa-plus me-2"></i> View All Quotes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
