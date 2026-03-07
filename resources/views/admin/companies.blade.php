@extends('layouts.admin')

@section('content')

<style>

.container{
    max-width:1200px;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.card{
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,.05);
}

table{
    width:100%;
    border-collapse:collapse;
}

th, td{
    padding:12px;
    text-align:left;
    border-bottom:1px solid #eee;
}

th{
    background:#fafafa;
}

tr:hover{
    background:#f9fafb;
}

.status-active{
    background:#d1fae5;
    color:#065f46;
    padding:4px 10px;
    border-radius:6px;
    font-size:12px;
}

.btn{
    padding:6px 12px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-size:12px;
}

.btn-suspend{
    background:#ef4444;
    color:white;
}

.btn-activate{
    background:#10b981;
    color:white;
}

.btn-delete{
    background:#111827;
    color:white;
}

.back{
    text-decoration:none;
    color:#2563eb;
}

</style>

<div class="container">

<div class="header">

<h2>🏢 Manage Companies</h2>

<a class="back" href="/admin">← Back to Dashboard</a>

</div>

<div class="card">

<table>

<thead>
<tr>
<th>ID</th>
<th>Company</th>
<th>Owner Email</th>
<th>Users</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

@foreach($companies as $company)

<tr>

<td>{{ $company->id }}</td>

<td>
<strong>{{ $company->name }}</strong>
</td>

<td>
{{ optional($company->users->first())->email }}
</td>

<td>
{{ $company->users_count }}
</td>

<td>
<span class="status-active">Active</span>
</td>

<td>

<a href="/admin/company/{{ $company->id }}">
<button class="btn btn-activate">Manage</button>
</a>

<button class="btn btn-suspend">Suspend</button>

<button class="btn btn-delete">Delete</button>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

@endsection
