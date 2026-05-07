@extends('layouts.app')

@section('content')

<div class="pricing-page">

    <section class="hero-wrap">
        <div class="hero-badge">MEDIOS BILLING • 7 DAY FREE TRIAL</div>

        <h1>Get Paid Faster.<br>Run Your Business Smarter.</h1>

        <p>
            Invoices, quotes, contracts, payments, customer portals and growth tools —
            built for real service businesses that want to scale.
        </p>

        <div class="trust-row">
            <span>✔ No Contracts</span>
            <span>✔ Cancel Anytime</span>
            <span>✔ Secure Checkout</span>
            <span>✔ Start Fast</span>
        </div>
    </section>

    <section class="pricing-shell">

        <div class="billing-switch">
            <button class="switch-btn active">Monthly</button>
            <button class="switch-btn">Yearly <small>Save 20%</small></button>
        </div>

        <div class="plans-grid">

            {{-- STARTER --}}
            <div class="plan-card">
                <div class="mini-pill">BEST START</div>

                <h2>Starter</h2>
                <p class="subtext">Perfect for solo owners launching fast.</p>

                <div class="price-line">
                    <span class="currency">$</span>
                    <span class="amount">49</span>
                    <span class="per">/mo</span>
                </div>

                <ul>
                    <li>✔ Invoices</li>
                    <li>✔ Quotes</li>
                    <li>✔ Customer Portal</li>
                    <li>✔ Payments</li>
                    <li>✔ Email Reminders</li>
                </ul>

                <a href="/register?plan=starter" class="btn-plan">
                    Start 7-Day Free Trial
                </a>
            </div>

            {{-- GROWTH --}}
            <div class="plan-card growth-card">
                <div class="mini-pill blue">POPULAR</div>

                <h2>Growth</h2>
                <p class="subtext">Best for teams ready to grow revenue.</p>

                <div class="price-line">
                    <span class="currency">$</span>
                    <span class="amount">79</span>
                    <span class="per">/mo</span>
                </div>

                <ul>
                    <li>✔ Everything in Starter</li>
                    <li>✔ Team Users</li>
                    <li>✔ Contracts + E-Sign</li>
                    <li>✔ Automations</li>
                    <li>✔ Smart Follow Ups</li>
                </ul>

                <a href="/register?plan=growth" class="btn-plan">
                    Start 7-Day Free Trial
                </a>
            </div>

            {{-- PRO --}}
            <div class="plan-card pro-card">
                <div class="mini-pill green">PRO</div>

                <h2>Pro</h2>
                <p class="subtext">For businesses scaling operations.</p>

                <div class="price-line">
                    <span class="currency">$</span>
                    <span class="amount">129</span>
                    <span class="per">/mo</span>
                </div>

                <ul>
                    <li>✔ Everything in Growth</li>
                    <li>✔ Advanced Automations</li>
                    <li>✔ Priority Email Support</li>
                    <li>✔ Team Permissions</li>
                    <li>✔ Performance Insights</li>
                </ul>

                <a href="/register?plan=pro" class="btn-plan">
                    Start 7-Day Free Trial
                </a>
            </div>
            {{-- PREMIUM --}}
            <div class="plan-card premium-card">
                <div class="mini-pill purple">BEST VALUE</div>

                <h2>Premium</h2>
                <p class="subtext">Advanced tools for serious operators.</p>

                <div class="price-line">
                    <span class="currency">$</span>
                    <span class="amount">249</span>
                    <span class="per">/mo</span>
                </div>

                <ul>
                    <li>✔ Everything in Growth</li>
                    <li>✔ Priority Support</li>
                    <li>✔ Revenue Dashboard</li>
                    <li>✔ Multi Staff Controls</li>
                    <li>✔ VIP Features First</li>
                </ul>

                <a href="/register?plan=premium" class="btn-plan premium-btn">
                    Start 7-Day Free Trial
                </a>
            </div>

        </div>
    </section>

    <section class="social-proof">
        <h3>Built For Real Businesses</h3>

        <div class="industry-grid">
            <span>Cleaning Companies</span>
            <span>Painters</span>
            <span>Contractors</span>
            <span>Roofing</span>
            <span>Landscaping</span>
            <span>Agencies</span>
        </div>
    </section>

    <section class="faq-wrap">
        <h3>Frequently Asked Questions</h3>

        <div class="faq-grid">
            <div class="faq-box">
                <strong>Do I need a contract?</strong>
                <p>No. Cancel anytime.</p>
            </div>

            <div class="faq-box">
                <strong>Can I accept card payments?</strong>
                <p>Yes. Secure Stripe checkout.</p>
            </div>

            <div class="faq-box">
                <strong>Can I upgrade later?</strong>
                <p>Yes. Change anytime.</p>
            </div>

            <div class="faq-box">
                <strong>How fast can I start?</strong>
                <p>Usually under 5 minutes.</p>
            </div>
        </div>
    </section>

    <section class="footer-cta">
        <h3>Ready To Grow?</h3>
        <p>Launch your account today and start collecting faster.</p>

        <a href="/register?plan=growth" class="footer-btn">
            Start Free Trial
        </a>
    </section>

</div>

<style>

body{
background:
radial-gradient(circle at top,#0b3a88 0%,#061427 35%,#030b17 100%);
color:#fff;
}

.pricing-page{
max-width:1450px;
margin:0 auto;
padding:60px 30px 90px;
}

/* HERO */

.hero-wrap{
text-align:center;
max-width:980px;
margin:0 auto 55px;
}

.hero-badge{
display:inline-block;
padding:10px 20px;
border-radius:999px;
background:rgba(37,99,235,.18);
color:#93c5fd;
font-size:13px;
font-weight:800;
margin-bottom:24px;
}

.hero-wrap h1{
font-size:74px;
line-height:1.05;
font-weight:900;
margin-bottom:20px;
}

.hero-wrap p{
font-size:22px;
color:#94a3b8;
line-height:1.7;
margin-bottom:24px;
}

.trust-row{
display:flex;
justify-content:center;
gap:24px;
flex-wrap:wrap;
font-size:14px;
font-weight:700;
color:#dbeafe;
}

/* SWITCH */

.billing-switch{
display:flex;
justify-content:center;
gap:12px;
margin-bottom:35px;
}

.switch-btn{
padding:14px 28px;
border-radius:14px;
border:none;
background:#0f172a;
color:#fff;
font-weight:800;
cursor:pointer;
}

.switch-btn.active{
background:#2563eb;
}

/* PLANS */

.plans-grid{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:28px;
align-items:stretch;
max-width:1320px;
margin:0 auto;
}

.plan-card{
background:rgba(7,18,38,.92);
border:1px solid rgba(255,255,255,.08);
border-radius:28px;
padding:34px;
box-shadow:0 20px 60px rgba(0,0,0,.30);
position:relative;
transition:.25s;
}

.plan-card:hover{
transform:translateY(-6px);
}

.growth-card{
border:1px solid #2563eb;
box-shadow:0 0 40px rgba(37,99,235,.20);
}

.premium-card{
border:1px solid #9333ea;
box-shadow:
0 0 45px rgba(147,51,234,.25),
0 0 90px rgba(37,99,235,.12);
transform:scale(1.04);
}

.mini-pill{
display:inline-block;
padding:8px 14px;
border-radius:999px;
font-size:12px;
font-weight:900;
background:#1d4ed8;
margin-bottom:18px;
}

.blue{background:#2563eb;}
.purple{
background:linear-gradient(135deg,#7c3aed,#9333ea);
}

.plan-card h2{
font-size:48px;
font-weight:900;
margin-bottom:10px;
}

.subtext{
color:#94a3b8;
margin-bottom:24px;
min-height:48px;
}

.price-line{
margin-bottom:26px;
display:flex;
align-items:flex-end;
gap:6px;
}

.currency{
font-size:28px;
font-weight:900;
margin-bottom:10px;
}

.amount{
font-size:72px;
line-height:1;
font-weight:900;
}

.per{
font-size:26px;
color:#94a3b8;
margin-bottom:10px;
}

.plan-card ul{
list-style:none;
padding:0;
margin:0 0 28px;
}

.plan-card li{
padding:11px 0;
border-bottom:1px solid rgba(255,255,255,.05);
font-size:18px;
color:#e2e8f0;
}

.btn-plan{
display:block;
text-align:center;
padding:17px;
border-radius:16px;
font-weight:900;
font-size:18px;
text-decoration:none;
color:#fff;
background:linear-gradient(135deg,#2563eb,#3b82f6);
margin-top:20px;
}

.premium-btn{
background:linear-gradient(135deg,#2563eb,#9333ea);
}

/* proof */

.social-proof{
text-align:center;
padding:80px 0 40px;
}

.social-proof h3,
.faq-wrap h3,
.footer-cta h3{
font-size:56px;
font-weight:900;
margin-bottom:28px;
}

.industry-grid{
display:grid;
grid-template-columns:repeat(6,1fr);
gap:18px;
}

.industry-grid span{
padding:18px;
border-radius:18px;
background:rgba(7,18,38,.8);
border:1px solid rgba(255,255,255,.06);
}

/* faq */

.faq-wrap{
padding:40px 0;
text-align:center;
}

.faq-grid{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:18px;
margin-top:25px;
}

.faq-box{
text-align:left;
padding:24px;
border-radius:20px;
background:rgba(7,18,38,.82);
border:1px solid rgba(255,255,255,.05);
}

.faq-box strong{
display:block;
font-size:20px;
margin-bottom:10px;
}

.faq-box p{
color:#94a3b8;
}

/* footer */

.footer-cta{
margin-top:60px;
padding:70px 30px;
text-align:center;
border-radius:28px;
background:linear-gradient(135deg,#071325,#0b1c3d);
border:1px solid rgba(255,255,255,.06);
}

.footer-cta p{
color:#94a3b8;
font-size:20px;
margin-bottom:24px;
}

.footer-btn{
display:inline-block;
padding:18px 34px;
border-radius:16px;
font-weight:900;
font-size:18px;
color:#fff;
text-decoration:none;
background:linear-gradient(135deg,#2563eb,#9333ea);
}

/* mobile */

@media(max-width:1200px){

.plans-grid,
.faq-grid{
grid-template-columns:1fr;
}

.industry-grid{
grid-template-columns:repeat(2,1fr);
}

.premium-card{
transform:none;
}

.hero-wrap h1{
font-size:56px;
}

.social-proof h3,
.faq-wrap h3,
.footer-cta h3{
font-size:42px;
}

}

@media(max-width:768px){

.hero-wrap h1{
font-size:42px;
}

.hero-wrap p{
font-size:18px;
}

.industry-grid{
grid-template-columns:1fr;
}

}

</style>

@endsection
