@extends('layouts.app')

@section('content')

<div class="container">

<h2 style="margin-bottom:25px;">Company Settings</h2>

@if(session('success'))
<div style="background:#d1fae5;padding:10px;border-radius:6px;margin-bottom:20px;">
{{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('company.update') }}" enctype="multipart/form-data">

@csrf

<div style="margin-bottom:20px;">

<label>Company Name</label>

<input type="text"
name="name"
value="{{ $company->name }}"
class="form-control"
required>

</div>


<div style="margin-bottom:20px;">

<label>Email</label>

<input type="email"
name="email"
value="{{ $company->email }}"
class="form-control">

</div>


<div style="margin-bottom:20px;">

<label>Phone</label>

<input type="text"
name="phone"
value="{{ $company->phone }}"
class="form-control">

</div>


<div style="margin-bottom:20px;">

<label>Address</label>

<input type="text"
name="address"
value="{{ $company->address }}"
class="form-control">

</div>


<div style="margin-bottom:20px;">

<label>Company Logo</label>

<br>

@if($company->logo)

<img src="{{ $company->logo }}" style="max-height:80px;margin-bottom:10px;">

@endif

<input type="file" name="logo">

</div>


<button type="submit"
style="background:#2563eb;color:white;padding:10px 20px;border:none;border-radius:6px;">
Save Settings
</button>

</form>

</div>

@endsection
