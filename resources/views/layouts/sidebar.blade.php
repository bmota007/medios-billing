<div class="sidebar">

<div class="sidebar-header">
<h4 class="text-white">Medios Billing</h4>
</div>

<ul class="sidebar-menu">

<li>
<a href="{{ route('dashboard') }}">
<i class="fa-solid fa-chart-line"></i> Dashboard
</a>
</li>

@if(auth()->user()->is_admin)

<li>
<a href="/admin/companies">
<i class="fa-solid fa-building"></i> Companies
</a>
</li>

<li>
<a href="/admin/users">
<i class="fa-solid fa-users"></i> Users
</a>
</li>

@endif

<li>
<a href="/invoices">
<i class="fa-solid fa-file-invoice"></i> Invoices
</a>
</li>

<li>
<a href="/settings">
<i class="fa-solid fa-gear"></i> Settings
</a>
</li>

</ul>

</div>
