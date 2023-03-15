@extends('admin.layouts.app')
@section('title', '- Add Subscription')

@section('content')
<section class="order-listing Invoice-listing">
    <div class="container">
        <input id="card-holder-name" type="text">
        
        <!-- Stripe Elements Placeholder -->
        <div id="card-element"></div>
        
        <button id="card-button" data-secret="{{ $intent->client_secret }}">
            Update Payment Method
        </button>

        <form method="post" action="{{ route('assign-subscription',['user'  =>  $user->id]) }}">
            @csrf
            <input name="payment_method" type="text">
            <select name="plan">
                @foreach( $plans as $singlePlan )
                    <option value="{{ $singlePlan->id }}">{{ $singlePlan->name }}</option>
                @endforeach
            </select>
            <input type="submit">
        </form>
    </div>
</section>

@endsection

@section('page-js')
<script src="https://js.stripe.com/v3/"></script>
 
<script>
    const stripe = Stripe('{{ config("cashier.key") }}');
 
    const elements = stripe.elements();
    const cardElement = elements.create('card');
 
    cardElement.mount('#card-element');

    const cardHolderName = document.getElementById('card-holder-name');
    const cardButton = document.getElementById('card-button');
    const clientSecret = cardButton.dataset.secret;
    
    cardButton.addEventListener('click', async (e) => {
        const { setupIntent, error } = await stripe.confirmCardSetup(
            clientSecret, {
                payment_method: {
                    card: cardElement,
                    billing_details: { name: cardHolderName.value }
                }
            }
        );
    
        if (error) {
            console.log(error);
        } else {
            $("input[name=payment_method]").val( setupIntent.payment_method );
            console.log(setupIntent.payment_method);
        }
    });

</script>
@endsection