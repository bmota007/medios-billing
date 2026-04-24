<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Medios Billing</title>

@vite(['resources/css/app.css', 'resources/js/app.js'])

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
    margin:0;
    background:linear-gradient(135deg,#08111f,#0d1b33,#0b1220);
    color:#ffffff;
    min-height:100vh;
    overflow-x:hidden;
    font-family:Inter,Arial,sans-serif;
}

/* SUPPORT BAR */
.support-mode-bar{
    background:#facc15;
    color:#000;
    padding:10px;
    text-align:center;
    font-weight:700;
    position:fixed;
    top:0;
    left:0;
    right:0;
    z-index:4000;
}

.support-mode-bar a{
    color:#000;
    text-decoration:underline;
}

/* MOBILE TOP BAR */
.mobile-topbar{
    display:none;
    position:fixed;
    top:{{ session()->has('impersonator_id') ? '42px':'0' }};
    left:0;
    right:0;
    height:64px;
    background:rgba(8,15,30,.98);
    border-bottom:1px solid rgba(255,255,255,.08);
    z-index:3500;
    align-items:center;
    justify-content:space-between;
    padding:0 16px;
    box-sizing:border-box;
    backdrop-filter:blur(14px);
}

.mobile-menu-btn{
    width:44px;
    height:44px;
    border-radius:14px;
    border:1px solid rgba(255,255,255,.10);
    background:rgba(255,255,255,.05);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    cursor:pointer;
}

.mobile-brand{
    display:flex;
    align-items:center;
    gap:10px;
    min-width:0;
}

.mobile-brand-icon{
    width:38px;
    height:38px;
    border-radius:12px;
    background:linear-gradient(135deg,#2563eb,#06b6d4);
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:800;
    color:#fff;
    box-shadow:0 10px 25px rgba(37,99,235,.35);
    flex-shrink:0;
}

.mobile-brand-text{
    font-size:15px;
    font-weight:800;
    letter-spacing:.3px;
    color:#fff;
    white-space:nowrap;
}

.mobile-brand-text span{
    color:#38bdf8;
}

.mobile-user-dot{
    width:38px;
    height:38px;
    border-radius:999px;
    background:linear-gradient(135deg,#1d4ed8,#7c3aed);
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:800;
    color:#fff;
    border:1px solid rgba(255,255,255,.08);
}

/* OVERLAY */
.mobile-sidebar-overlay{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(2,6,23,.72);
    z-index:3200;
    opacity:0;
    transition:opacity .22s ease;
}

.mobile-sidebar-overlay.active{
    opacity:1;
}

/* SIDEBAR */
.sidebar{
    width:290px;
    min-width:290px;
    max-width:290px;
    position:fixed;
    top:{{ session()->has('impersonator_id') ? '42px':'0' }};
    left:0;
    bottom:0;
    overflow-y:auto;
    overflow-x:hidden;
    background:rgba(8,15,30,.97);
    border-right:1px solid rgba(255,255,255,.06);
    z-index:3300;
    padding:0;
}

.sidebar::-webkit-scrollbar{
    width:6px;
}

.sidebar::-webkit-scrollbar-track{
    background:transparent;
}

.sidebar::-webkit-scrollbar-thumb{
    background:rgba(255,255,255,.10);
    border-radius:20px;
}

/* MAIN CONTENT */
.main-content{
    margin-left:290px;
    width:calc(100% - 290px);
    padding:34px;
    min-height:100vh;
    margin-top:{{ session()->has('impersonator_id') ? '42px':'0' }};
    box-sizing:border-box;
}

.container-fluid{
    width:100%;
    max-width:100%;
    padding:0;
    margin:0;
}
/* SIDEBAR LINKS */
.sidebar a{
    display:flex;
    align-items:center;
    gap:10px;
    padding:8px 18px;
    margin:0 10px 2px;
    border-radius:12px;
    color:rgba(255,255,255,.92);
    text-decoration:none;
    font-size:15px;
    line-height:1.25;
    font-weight:500;
    transition:all .18s ease;
}

.sidebar a:hover{
    background:rgba(255,255,255,.04);
    color:#ffffff;
}

.sidebar a.active,
.sidebar .active > a{
    background:linear-gradient(90deg,#2563eb,#7c3aed);
    color:#ffffff;
    box-shadow:0 8px 20px rgba(124,58,237,.18);
}

.sidebar h1,
.sidebar h2,
.sidebar h3,
.sidebar h4,
.sidebar h5,
.sidebar h6,
.sidebar .menu-title,
.sidebar .sidebar-title{
    margin:18px 18px 10px;
    font-size:12px;
    font-weight:700;
    letter-spacing:1.4px;
    text-transform:uppercase;
    color:rgba(255,255,255,.58);
}

.sidebar .brand,
.sidebar .logo,
.sidebar .sidebar-brand{
    padding:20px 18px 16px;
    border-bottom:1px solid rgba(255,255,255,.05);
    margin-bottom:10px;
}

.sidebar .sidebar-footer,
.sidebar .profile-box,
.sidebar .user-box{
    margin-top:auto;
    padding:18px;
}

/* RESPONSIVE */
@media(max-width:1100px){

    .sidebar{
        width:250px;
        min-width:250px;
        max-width:250px;
    }

    .main-content{
        margin-left:250px;
        width:calc(100% - 250px);
        padding:26px;
    }
}

@media(max-width:900px){

    .mobile-topbar{
        display:flex;
    }

    .mobile-sidebar-overlay{
        display:block;
        pointer-events:none;
    }

    .mobile-sidebar-overlay.active{
        pointer-events:auto;
    }

    .sidebar{
        width:290px;
        min-width:290px;
        max-width:290px;
        top:{{ session()->has('impersonator_id') ? '106px':'64px' }};
        left:0;
        bottom:0;
        transform:translateX(-105%);
        transition:transform .25s ease;
        box-shadow:24px 0 60px rgba(0,0,0,.45);
        border-right:1px solid rgba(255,255,255,.10);
    }

    .sidebar.mobile-open{
        transform:translateX(0);
    }

    .main-content{
        margin-left:0;
        width:100%;
        padding:88px 20px 24px;
        margin-top:{{ session()->has('impersonator_id') ? '42px':'0' }};
    }
}

@media(max-width:480px){

    .main-content{
        padding-left:14px;
        padding-right:14px;
    }

    .mobile-brand-text{
        font-size:14px;
    }

    .sidebar{
        width:86vw;
        min-width:86vw;
        max-width:86vw;
    }
}
</style>

@stack('page_styles')
</head>

<body>

@if(session()->has('impersonator_id'))
<div class="support-mode-bar">
    You are in SUPPORT MODE —
    <a href="{{ route('admin.stopImpersonating') }}">Return to Admin</a>
</div>
@endif

@if(!isset($hideSidebar) || !$hideSidebar)

<div class="mobile-topbar">
    <button type="button" class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div class="mobile-brand">
        <div class="mobile-brand-icon">M</div>
        <div class="mobile-brand-text">
            Medios<span>Billing</span>
        </div>
    </div>

    <div class="mobile-user-dot">
        {{ strtoupper(substr(auth()->user()->name ?? 'M',0,1)) }}
    </div>
</div>

<div class="mobile-sidebar-overlay" id="mobileSidebarOverlay"></div>

<div class="sidebar" id="appSidebar">
    @include('layouts.sidebar')
</div>
@endif
<div class="main-content" style="{{ (isset($hideSidebar) && $hideSidebar) ? 'margin-left:0;width:100%;padding-top:34px;' : '' }}">
    <div class="container-fluid">
        @yield('content')
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const btn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.getElementById('mobileSidebarOverlay');

    if (!btn || !sidebar || !overlay) {
        return;
    }

    function openMenu() {
        sidebar.classList.add('mobile-open');
        overlay.classList.add('active');
        btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        btn.innerHTML = '<i class="fa-solid fa-bars"></i>';
        document.body.style.overflow = '';
    }

    btn.addEventListener('click', function () {
        if (sidebar.classList.contains('mobile-open')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    overlay.addEventListener('click', closeMenu);

    sidebar.querySelectorAll('a').forEach(function(link){
        link.addEventListener('click', function(){
            if(window.innerWidth <= 900){
                closeMenu();
            }
        });
    });

    window.addEventListener('resize', function(){
        if(window.innerWidth > 900){
            closeMenu();
        }
    });

});
</script>

@stack('page_scripts')

</body>
</html>
