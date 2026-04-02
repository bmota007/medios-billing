@extends('layouts.admin')

@section('content')

<div class="container" style="max-width:500px;">

<h2 class="mb-4">Add Employee</h2>

<form method="POST" action="{{ route('company.users.store') }}">
@csrf

<div class="mb-3">
<label>Name</label>
<input type="text" name="name" class="form-control" required>
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<button class="btn btn-success">
Create User
</button>

</form>

</div>

@endsection≈y

