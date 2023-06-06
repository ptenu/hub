@extends('layouts.default')

@section('pageTitle', 'Update your payment method')

@push('head-js')
    <style>
        #error-message {
            margin-bottom: var(--layout-gap);
        }
        #error-message:empty {
            display: none;
        }
    </style>
@endpush

@section('content')
    <ptu-page-header headline="Update your payment method" topic="Back to membership details" topic-href="/account/membership"></ptu-page-header>

    <ptu-section sidebar="right">
        <p style="margin-bottom: var(--layout-gap)">
            You won't be charged now.
        </p>
        <form id="payment-form">
            {{ csrf_field() }}
            <div id="error-message" class="card danger"></div>

            <div id="payment-element">
                <!-- Stripe Elements will create form elements here -->
            </div>

            <footer class="top-padding">
                <button id="submit">Save</button>
            </footer>
        </form>
    </ptu-section>
@endsection

@push('head-js')
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@push('body-js')
    <script>
        const stripe = Stripe('{{ $stripe_pub_key }}');
        const options = {
            clientSecret: '{{ $client_secret }}',
            appearance: {
                theme: 'none',
                fonts: {
                    cssSrc: 'https://fonts.bunny.net/css?family=atkinson-hyperlegible:400,400i,700,700i'
                },
                variables: {
                    fontFamily: 'Atkinson Hyperlegible',
                    borderRadius: 0,
                    backgroundColor: '#FAFAFAFF',
                    colorText: '#18181B',
                    fontSizeBase: '13pt',
                    colorDanger: '#E60049FF'
                },
                rules:{
                    '.Input': {
                        border: '2px solid #18181B',
                        outline: '3px solid transparent',
                        padding: '.77ch 1ch'
                    },
                    '.Input:hover': {
                        boxShadow: '0 0 0 2px inset #E4E4E7FF'
                    },
                    '.Input:focus': {
                        outline: '3px solid #E6A85CFF',
                        boxShadow: '0 0 0 1px inset #18181B'
                    },
                    '.Input--invalid': {
                        outline: '3px solid #E60049FF'
                    },
                    '.Label': {
                        fontSize: '14pt',
                        fontWeight: 'bold'
                    }
                }
            },
        };

        // Set up Stripe.js and Elements to use in checkout form, passing the client secret obtained in step 3
        const elements = stripe.elements(options);

        // Create and mount the Payment Element
        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');

        const form = document.getElementById('payment-form');

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const {error} = await stripe.confirmSetup({
                elements,
                confirmParams: {
                    return_url: '{{request()->schemeAndHttpHost()}}/account/membership',
                }
            });

            if (error) {
                // This point will only be reached if there is an immediate error when
                // confirming the payment. Show error to your customer (for example, payment
                // details incomplete)
                const messageContainer = document.querySelector('#error-message');
                messageContainer.textContent = error.message;
            } else {
                // Your customer will be redirected to your `return_url`. For some payment
                // methods like iDEAL, your customer will be redirected to an intermediate
                // site first to authorize the payment, then redirected to the `return_url`.
            }
        });

    </script>
@endpush
