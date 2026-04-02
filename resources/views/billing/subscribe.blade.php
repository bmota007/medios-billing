<!DOCTYPE html>
<html>
<head>
    <title>Subscribe - Medios Billing</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body style="font-family: Arial; text-align:center; padding:50px;">

    <h1>Medios Billing Subscription</h1>
    <h2>$35/month</h2>

    @if(session('error'))
        <p style="color:red;">{{ session('error') }}</p>
    @endif

    <form action="{{ route('process.subscription') }}" method="POST" id="payment-form">
        @csrf

        <div id="card-element" style="margin:20px auto; max-width:400px;"></div>

        <button type="submit" style="padding:10px 20px;">
            Subscribe Now
        </button>
    </form>

    <script>
        const stripe = Stripe("{{ env('STRIPE_KEY') }}");
        const elements = stripe.elements();

        const card = elements.create('card');
        card.mount('#card-element');

        const form = document.getElementById('payment-form');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const {token, error} = await stripe.createToken(card);

            if (error) {
                alert(error.message);
                return;
            }

            let hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);

            form.appendChild(hiddenInput);
            form.submit();
        });
    </script>

</body>
</html>
