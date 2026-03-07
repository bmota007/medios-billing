<!DOCTYPE html>
<html>
<head>

<title>Medios Billing Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>

body{
    margin:0;
    background:#f5f7fb;
    font-family:system-ui;
}

/* Sidebar */

.sidebar{
    width:240px;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    background:#0f172a;
    color:white;
    padding-top:30px;
}

.sidebar h3{
    text-align:center;
    margin-bottom:30px;
    font-weight:600;
}

.sidebar a{
    display:block;
    padding:14px 25px;
    color:#cbd5f1;
    text-decoration:none;
    font-size:15px;
}

.sidebar a:hover{
    background:#1e293b;
    color:white;
}

/* Content */

.main{
    margin-left:240px;
    padding:40px;
    min-height:100vh;
}

/* Topbar */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

/* Logout Button */

.logout-btn{
    background:#ef4444;
    border:none;
    color:white;
    padding:10px 20px;
    border-radius:8px;
    font-weight:600;
}

/* Cards */

.dashboard-card{
    background:white;
    border-radius:12px;
    padding:25px;
    box-shadow:0 8px 25px rgba(0,0,0,.05);
}

.dashboard-card h2{
    font-size:34px;
    margin-top:10px;
}

.dashboard-card i{
    font-size:22px;
    color:#6366f1;
}

</style>

</head>

<body>

<div class="sidebar">

<h3>Medios Billing</h3>

<a href="/admin">
<i class="fa-solid fa-chart-line"></i> Dashboard
</a>

<a href="/admin/companies">
<i class="fa-solid fa-building"></i> Companies
</a>

<a href="#">
<i class="fa-solid fa-file-invoice"></i> Invoices
</a>

<a href="#">
<i class="fa-solid fa-users"></i> Users
</a>

<a href="#">
<i class="fa-solid fa-gear"></i> Settings
</a>

</div>


<div class="main">

<div class="topbar">

<h4 class="mb-0">Super Admin Dashboard</h4>

<form method="POST" action="/logout">
@csrf
<button type="submit" class="btn btn-danger">
<i class="fa-solid fa-right-from-bracket"></i> Logout
</button>
</form>

</div>

@yield('content')

</div>

</body>
</html>
