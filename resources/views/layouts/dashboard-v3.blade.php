<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Medios Billing</title>

@vite(['resources/css/app.css','resources/js/app.js'])

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
margin:0;
font-family:Inter,Arial,sans-serif;
background:linear-gradient(135deg,#08111f,#0d1b33,#0b1220);
color:#fff;
min-height:100vh;
overflow-x:hidden;
}

.sidebar{
position:fixed;
top:0;
left:0;
bottom:0;
width:290px;
background:rgba(8,15,30,.97);
border-right:1px solid rgba(255,255,255,.06);
overflow-y:auto;
z-index:2000;
}

.main-content{
margin-left:290px;
padding:34px;
min-height:100vh;
}

.container-fluid{
width:100%;
max-width:100%;
padding:0;
margin:0;
}

.sidebar a{
display:flex;
align-items:center;
gap:10px;
padding:10px 18px;
margin:0 10px 3px;
border-radius:12px;
color:#fff;
text-decoration:none;
font-size:15px;
transition:.2s;
}

.sidebar a:hover{
background:rgba(255,255,255,.04);
}

.sidebar a.active{
background:linear-gradient(90deg,#2563eb,#7c3aed);
}

.sidebar h6{
margin:18px;
font-size:11px;
letter-spacing:1px;
opacity:.55;
text-transform:uppercase;
}

/* PREMIUM CSS SAFE AREA */

@stack('page_styles')
</style>
</head>

<body>

<div class="sidebar">
@include('layouts.sidebar')
</div>

<div class="main-content">
<div class="container-fluid">
@yield('content')
</div>
</div>

@stack('page_scripts')

</body>
</html>
