<!-- SECTION 1 OF 2 -->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">

<title>Medios Billing | Invoices, Payments & Business Growth</title>

<meta name="description" content="Medios Billing helps service businesses send invoices, collect payments, manage subscriptions, estimates, contracts and customer portals in one platform.">

<meta name="keywords" content="billing software,invoicing software,invoice platform,contractor software,payment software">

<link rel="canonical" href="{{ url('/') }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
*{
margin:0;
padding:0;
box-sizing:border-box;
}

html{
scroll-behavior:smooth;
}

body{
font-family:'Inter',sans-serif;
background:#ffffff;
color:#0f172a;
overflow-x:hidden;
}

a{
text-decoration:none;
color:inherit;
}

.container{
max-width:1320px;
margin:auto;
padding:0 24px;
}

/* NAV */
header{
position:sticky;
top:0;
z-index:999;
background:rgba(255,255,255,.92);
backdrop-filter:blur(14px);
border-bottom:1px solid #eef2f7;
}

.navbar{
height:84px;
display:flex;
justify-content:space-between;
align-items:center;
}

.logo{
font-size:34px;
font-weight:800;
letter-spacing:-1px;
}

.logo span{
color:#2563eb;
}

.nav-links{
display:flex;
gap:34px;
font-size:15px;
font-weight:600;
color:#475569;
}

.nav-right{
display:flex;
gap:14px;
align-items:center;
}

.btn{
display:inline-flex;
align-items:center;
justify-content:center;
padding:14px 24px;
border-radius:14px;
font-weight:700;
transition:.2s;
cursor:pointer;
}

.btn-outline{
border:1px solid #cbd5e1;
background:#fff;
}

.btn-primary{
background:#2563eb;
color:#fff;
box-shadow:0 16px 30px rgba(37,99,235,.18);
}

.btn-primary:hover{
transform:translateY(-2px);
}

/* HERO */
.hero{
padding:80px 0 70px;
background:
radial-gradient(circle at top right,#dbeafe 0,#eff6ff 30%,#ffffff 65%);
}

.hero-grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:55px;
align-items:center;
}

.badge{
display:inline-block;
padding:10px 18px;
border-radius:999px;
background:#dbeafe;
color:#2563eb;
font-size:13px;
font-weight:800;
margin-bottom:22px;
}

.hero h1{
font-size:72px;
line-height:1.02;
font-weight:800;
letter-spacing:-2px;
margin-bottom:22px;
}

.hero h1 span{
color:#2563eb;
}

.hero p{
font-size:22px;
color:#64748b;
line-height:1.6;
margin-bottom:30px;
max-width:620px;
}

.hero-actions{
display:flex;
gap:16px;
flex-wrap:wrap;
margin-bottom:34px;
}

.stats{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:14px;
}

.stat{
background:#fff;
border:1px solid #e8eef7;
border-radius:18px;
padding:20px;
text-align:center;
box-shadow:0 12px 28px rgba(15,23,42,.04);
}

.stat strong{
display:block;
font-size:28px;
color:#2563eb;
font-weight:800;
margin-bottom:6px;
}

.stat span{
font-size:14px;
color:#64748b;
}

/* DASHBOARD MOCKUP */
.hero-right{
position:relative;
}

.dashboard{
background:#fff;
border:1px solid #e6edf7;
border-radius:28px;
padding:24px;
box-shadow:0 40px 80px rgba(15,23,42,.08);
}

.top-row{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:14px;
margin-bottom:18px;
}

.mini-box{
background:#f8fbff;
border:1px solid #edf2f7;
padding:16px;
border-radius:16px;
}

.mini-box small{
display:block;
color:#64748b;
font-size:12px;
margin-bottom:8px;
}

.mini-box strong{
font-size:26px;
font-weight:800;
}

.chart{
height:260px;
border-radius:22px;
padding:24px;
background:linear-gradient(180deg,#eff6ff,#ffffff);
position:relative;
overflow:hidden;
border:1px solid #edf2f7;
}

.chart-line{
position:absolute;
left:40px;
bottom:70px;
width:72%;
height:4px;
background:#2563eb;
transform:skewY(-18deg);
border-radius:10px;
}

.chart-line.two{
bottom:105px;
width:58%;
background:#60a5fa;
transform:skewY(-12deg);
}

.chart-badge{
position:absolute;
top:22px;
right:22px;
padding:8px 12px;
border-radius:999px;
background:#dbeafe;
color:#2563eb;
font-size:12px;
font-weight:800;
}

.phone{
position:absolute;
left:-55px;
bottom:-28px;
width:180px;
background:#fff;
border-radius:28px;
padding:12px;
border:1px solid #eef2f7;
box-shadow:0 30px 60px rgba(15,23,42,.10);
}

.phone-screen{
border-radius:22px;
padding:16px;
background:#f8fbff;
text-align:center;
}

.phone-screen small{
color:#64748b;
font-size:12px;
}

.phone-screen strong{
display:block;
font-size:34px;
color:#16a34a;
font-weight:800;
margin:10px 0;
}

.phone-screen .pill{
padding:10px;
border-radius:12px;
background:#dbeafe;
color:#2563eb;
font-weight:700;
font-size:13px;
}

/* TRUST */
.trust{
padding:28px 0;
border-top:1px solid #eef2f7;
border-bottom:1px solid #eef2f7;
background:#fff;
}

.trust-row{
display:grid;
grid-template-columns:repeat(6,1fr);
gap:16px;
text-align:center;
font-size:15px;
font-weight:700;
color:#64748b;
}

/* MOBILE */
@media(max-width:1100px){

.hero-grid{
grid-template-columns:1fr;
}

.top-row{
grid-template-columns:1fr 1fr;
}

.trust-row{
grid-template-columns:repeat(3,1fr);
}

.phone{
display:none;
}
}

@media(max-width:768px){

.nav-links{
display:none;
}

.hero h1{
font-size:48px;
}

.stats,
.top-row,
.trust-row{
grid-template-columns:1fr 1fr;
}

.hero{
padding-top:40px;
}

.hero-actions{
flex-direction:column;
align-items:flex-start;
}
}
</style>
</head>

<body>

<header>
<div class="container navbar">

<div class="logo">Medios<span>Billing</span></div>

<nav class="nav-links">
<a href="#features">Features</a>
<a href="#solutions">Solutions</a>
<a href="#plans">Pricing</a>
<a href="/contact">Resources</a>
<a href="/contact">About</a>
</nav>

<div class="nav-right">
<a href="{{ route('login') }}" class="btn btn-outline">Login</a>
<a href="/pricing#plans" class="btn btn-primary">Start Free Trial</a>
</div>

</div>
</header>

<section id="features" class="hero">
<div class="container hero-grid">

<div>

<div class="badge">⚡ GET PAID FASTER. STAY ORGANIZED.</div>

<h1>
Run Your Business.<br>
<span>Collect Money.</span><br>
Automatically.
</h1>

<p>
Invoices, estimates, contracts, subscriptions, reminders,
customer portals and payments — all in one platform built for modern service businesses.
</p>

<div class="hero-actions">
<a href="/pricing#plans" class="btn btn-primary">Start Free Trial</a>
<a href="/contact" class="btn btn-outline">Book Demo</a>
</div>

<div class="stats">

<div class="stat">
<strong>5X</strong>
<span>Faster Payments</span>
</div>

<div class="stat">
<strong>24/7</strong>
<span>Customer Portal</span>
</div>

<div class="stat">
<strong>100+</strong>
<span>Automations</span>
</div>

<div class="stat">
<strong>$0</strong>
<span>Setup Hassle</span>
</div>

</div>

</div>

<div class="hero-right">

<div class="dashboard">

<div class="top-row">

<div class="mini-box">
<small>Revenue</small>
<strong>$28k</strong>
</div>

<div class="mini-box">
<small>Invoices</small>
<strong>156</strong>
</div>

<div class="mini-box">
<small>Paid</small>
<strong>$22k</strong>
</div>

<div class="mini-box">
<small>Growth</small>
<strong>+18%</strong>
</div>

</div>

<div class="chart">
<div class="chart-badge">+18.2%</div>
<div class="chart-line"></div>
<div class="chart-line two"></div>
</div>

</div>

<div class="phone">
<div class="phone-screen">
<small>Invoice Paid</small>
<strong>$1,250</strong>
<div class="pill">Download PDF</div>
</div>
</div>

</div>

</div>
</section>

<section id="solutions" class="trust">
<div class="container trust-row">
<div>McIntosh Cleaning</div>
<div>Pronto Painting</div>
<div>ProWork LLC</div>
<div>Bright Services</div>
<div>TopNotch HVAC</div>
<div>Elite Landscaping</div>
</div>
</section>

<section id="plans" class="pricing-section">

<div class="container">

<div class="pricing-header">
<h2>Simple Pricing That <span>Scales</span></h2>
<p>Choose the perfect plan for your business. Upgrade anytime as you grow.</p>
</div>

<div class="pricing-grid">

<div class="plan-card">
<h3>Starter</h3>
<p>$49/mo</p>
<a href="/register?plan=starter" class="btn-outline-plan">Get Started</a>
</div>

<div class="plan-card">
<h3>Growth</h3>
<p>$79/mo</p>
<a href="/register?plan=growth" class="btn-outline-plan">Get Started</a>
</div>

<div class="plan-card featured">
<h3>Pro</h3>
<p>$129/mo</p>
<a href="/register?plan=pro" class="btn-primary-plan">Start Scaling</a>
</div>

<div class="plan-card">
<h3>Enterprise</h3>
<p>$299/mo</p>
<a href="/contact?plan=enterprise" class="btn-outline-plan">Contact Sales</a>
</div>

</div>

<div class="trust-bar">
Trusted by service businesses nationwide • McIntosh Cleaning • Pronto Painting • HVAC • Roofing • Landscaping
</div>

<div class="pricing-cta">

<div class="cta-left">
<h3>Ready To Get Paid Faster?</h3>
<p>Automate invoices, reminders, subscriptions and contracts in one platform.</p>
</div>

<div class="cta-right">
<a href="/pricing#plans" class="btn-white">Start Free Trial</a>
<a href="/contact" class="btn-dark">Book Demo</a>
</div>

</div>

</div>

<style>
.pricing-section{
padding:100px 0;
background:linear-gradient(180deg,#ffffff,#f8fbff);
}

.pricing-grid{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:24px;
margin-bottom:35px;
}

.plan-card{
background:#fff;
border:1px solid #e6edf7;
border-radius:24px;
padding:34px;
text-align:center;
}

.featured{
border:2px solid #7c3aed;
}

.btn-outline-plan,
.btn-primary-plan,
.btn-white,
.btn-dark{
display:inline-block;
padding:16px 24px;
border-radius:14px;
font-weight:800;
margin-top:16px;
}

.btn-outline-plan{
border:1px solid #cbd5e1;
}

.btn-primary-plan{
background:#2563eb;
color:#fff;
}

.btn-white{
background:#fff;
color:#2563eb;
}

.btn-dark{
background:#0f172a;
color:#fff;
}

.trust-bar{
text-align:center;
padding:20px 0;
color:#64748b;
}

.pricing-cta{
background:linear-gradient(135deg,#2563eb,#60a5fa);
padding:55px;
border-radius:28px;
display:flex;
justify-content:space-between;
align-items:center;
gap:20px;
color:#fff;
}

@media(max-width:1100px){
.pricing-grid{
grid-template-columns:1fr 1fr;
}
}

@media(max-width:768px){
.pricing-grid{
grid-template-columns:1fr;
}

.pricing-cta{
flex-direction:column;
text-align:center;
}
}
</style>

</section>

<footer style="padding:40px 20px;text-align:center;color:#64748b;font-size:14px;border-top:1px solid #eef2f7;">
© {{ date('Y') }} Medios Billing • Powered by
<a href="https://medioscorporativos.com" target="_blank">Medios Corporativos</a>
</footer>

</body>
</html>
