@extends('layouts.admin')

@section('content')

<div class="container" style="max-width:500px;">

<h2 class="mb-4">Edit Employee</h2>

<form method="POST"
      action="{{ route('company.users.update',$user->id) }}">

@csrf
@method('PUT')

<div class="mb-3">
<label>Name</label>
<input type="text"
       name="name"
       value="{{ $user->name }}"
       class="form-control"
       required>
</div>

<div class="mb-3">
<label>Email</label>
<input type="email"
       name="email"
       value="{{ $user->email }}"
       class="form-control"
       required>
</div>

<div class="mb-3">
<label>New Password (optional)</label>
<input type="password"
       name="password"
       class="form-control">
</div>

<button class="btn btn-success">
Update User
</button>

</form>

</div>

@endsection
