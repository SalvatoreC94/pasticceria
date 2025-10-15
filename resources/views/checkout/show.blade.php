<!doctype html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <title>Checkout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            margin: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px
        }

        .line {
            display: flex;
            justify-content: space-between;
            margin: 6px 0
        }

        .total {
            font-weight: 700;
            font-size: 18px
        }

        label {
            display: block;
            margin: 8px 0 4px
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 10px
        }

        button {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #111827;
            background: #111827;
            color: #fff
        }

        #error {
            color: #b91c1c;
            margin-top: 10px;
            min-height: 1.5em
        }
    </style>
    <script src="https://js.stripe.com/v3/"></script>
</head>

<body>
    <h1>Checkout</h1>

    <div class="grid">
        <div class="card">
            <h3>Dati di spedizione</h3>

            <form id="checkout-form" method="POST" action="{{ route('checkout.create') }}">
                @csrf

                <label>Nome e cognome</label>
                <input name="name" value="{{ auth()->user()->name ?? '' }}" required>

                <label>Email</label>
                <input name="email" type="email" value="{{ auth()->user()->email ?? '' }}" required>

                <label>Telefono (opz.)</label>
                <input name="phone" type="tel" value="{{ auth()->user()->phone ?? '' }}">

                <label>Via</label>
                <input name="address[via]" value="{{ auth()->user()->shipping_address['via'] ?? '' }}" required>

                <label>Civico</label>
                <input name="address[civico]" value="{{ auth()->user()->shipping_address['civico'] ?? '' }}" required>

                <label>CAP</label>
                <input name="address[cap]" value="{{ auth()->user()->shipping_address['cap'] ?? '' }}" required
                    pattern="\d{5}" title="CAP a 5 cifre">

                <label>Città</label>
                <input name="address[citta]" value="{{ auth()->user()->shipping_address['citta'] ?? '' }}" required>

                <label>Prov. (2 lettere, es. NA)</label>
                <input name="address[prov]" value="{{ auth()->user()->shipping_address['prov'] ?? '' }}" required
                    maxlength="2" pattern="[A-Za-z]{2}" title="Due lettere">

                <div id="payment-element" style="margin:16px 0;"></div>

                <button id="pay-btn" type="submit">Paga ora</button>
                <div id="error"></div>
            </form>
        </div>

        <div class="card">
            <h3>Riepilogo</h3>
            <ul>
                @foreach ($items as $it)
                    <li>
                        {{ $it->product->name }} × {{ $it->qty }}
                        <span style="float:right">{{ number_format($it->total_cents / 100, 2, ',', '.') }} €</span>
                    </li>
                @endforeach
            </ul>

            <div class="line">
                <span>Subtotale</span>
                <span>{{ number_format($subtotal / 100, 2, ',', '.') }} €</span>
            </div>

            <div class="line">
                <span>Spedizione
                    @if ($subtotal >= (int) env('FREE_SHIPPING_THRESHOLD_CENTS', 6900))
                        <small>(Gratis)</small>
                    @else
                        <small>(10,00 €)</small>
                    @endif
                </span>
                <span>{{ number_format($shipping / 100, 2, ',', '.') }} €</span>
            </div>

            <div class="line total">
                <span>Totale</span>
                <span>{{ number_format($total / 100, 2, ',', '.') }} €</span>
            </div>
        </div>
    </div>

   <script>
(function() {
    const stripe = Stripe("{{ config('services.stripe.key', env('STRIPE_KEY')) }}");
    const form = document.getElementById('checkout-form');
    const payBtn = document.getElementById('pay-btn');
    const errorEl = document.getElementById('error');
    const returnUrl = "{{ url('/checkout/thank-you') }}"; // assicurati che esista questa rotta/pagina

    let elements = null;
    let clientSecret = null;
    let phase = 'init'; // init -> mounted -> confirming

    function setBusy(b) {
        payBtn.disabled = b;
        payBtn.style.opacity = b ? .6 : 1;
    }
    function fail(e) {
        console.error(e);
        errorEl.textContent = (e && e.message) ? e.message : String(e || 'Errore');
        setBusy(false);
    }

    payBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        errorEl.textContent = '';

        try {
            // 1) PRIMO CLICK → crea ordine + monta Element, poi STOP (l'utente inserisce i dati)
            if (phase === 'init') {
                setBusy(true);

                const body = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body
                });

                let data;
                try {
                    data = await res.json();
                } catch {
                    const text = await res.text();
                    if (text && text.includes('Page Expired')) {
                        throw new Error('Sessione scaduta (419). Ricarica e riprova.');
                    }
                    throw new Error('Errore server. Riprova tra poco.');
                }

                if (!res.ok || !data?.clientSecret) {
                    const firstValidation = (data?.errors && Object.values(data.errors)[0]?.[0]) ||
                                            data?.message || data?.error || 'Errore in checkout.';
                    throw new Error(firstValidation);
                }

                clientSecret = data.clientSecret;

                elements = stripe.elements({ clientSecret, locale: 'it' });
                const paymentElement = elements.create('payment', { wallets: { applePay: 'never' } });
                paymentElement.mount('#payment-element');

                phase = 'mounted';
                payBtn.textContent = 'Paga ora';   // ora il secondo click conferma
                setBusy(false);
                return; // STOP qui: l'utente inserisce i dati carta
            }

            // 2) SECONDO CLICK → conferma pagamento
            if (phase === 'mounted') {
                setBusy(true);

                // valida i campi del Payment Element
                const { error: submitError } = await elements.submit();
                if (submitError) throw submitError;

                const { error } = await stripe.confirmPayment({
                    elements,
                    clientSecret,
                    confirmParams: { return_url: returnUrl }
                });

                if (error) throw error;

                phase = 'confirming';
                setBusy(false);
                return;
            }
        } catch (err) {
            fail(err);
        }
    });
})();
</script>

</body>

</html>
