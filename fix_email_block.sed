/Plan:/,/Monthly:/c\
<div style="margin-top:25px; padding:20px; border-radius:12px; border:1px solid rgba(255,255,255,0.1);">\
\
    <p><strong>Plan:</strong> {{ ucfirst($company->plan) }}</p>\
\
    <p><strong>Trial:</strong> 5 Days Free</p>\
\
    <p><strong>Billing Starts:</strong> After Trial Ends</p>\
\
    <p><strong>Monthly:</strong>\
        @if($company->plan === 'starter')\
            $49\
        @elseif($company->plan === 'growth')\
            $79\
        @elseif($company->plan === 'pro')\
            $129\
        @elseif($company->plan === 'premium')\
            $249\
        @else\
            $0\
        @endif\
    </p>\
\
</div>
