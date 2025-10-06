<!doctype html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Checkout</title>
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
    </style>
    <script src="https://js.stripe.com/v3/"></script>
</head>

<body>
    <h1>Checkout</h1>

    <div class="grid">
        <div class="card">
            <h3>Dati di spedizione</h3>
            <form id="checkout-form">
                @csrf
                <label>Nome e cognome</label>
                <input name="name" required>
                <label>Email</label>
                <input name="email" type="email" required>
                <label>Telefono</label>
                <input name="phone" type="tel">
                <label>Via</label>
                <input name="address[via]" required>
                <label>Civico</label>
                <input name="address[civico]" required>
                <label>CAP</label>
                <input name="address[cap]" required pattern="\d{5}">
                <label>Città</label>
                <input name="address[citta]" required>
                <label>Prov.</label>
                <input name="address[prov]" required maxlength="2">

                <div id="payment-element" style="margin:16px 0;"></div>
                <button id="pay-btn" type="submit">Paga ora</button>
                <div id="error" style="color:#b91c1c;margin-top:10px;"></div>
            </form>
        </div>

        <div class="card">
            <h3>Riepilogo</h3>
            <ul>
                @foreach ($items as $it)
                    <li>{{ $it->product->name }} × {{ $it->qty }}
                        <span style="float:right">{{ number_format($it->total_cents / 100, 2, ',', '.') }} €</span>
                    </li>
                @endforeach
            </ul>
            <div class="line"><span>Subtotale</span><span>{{ number_format($subtotal / 100, 2, ',', '.') }} €</span>
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
            <div class="line total"><span>Totale</span><span>{{ number_format($total / 100, 2, ',', '.') }} €</span></div>
        </div>
    </div>

    <script>
        (async () => {
            const stripe = Stripe("{{ env('STRIPE_KEY') }}");
            const form = document.getElementById('checkout-form');
            const payBtn = document.getElementById('pay-btn');
            const errorEl = document.getElementById('error');

            let elements, clientSecret;

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                payBtn.disabled = true;

                // 1) crea l'ordine + PI
                const body = new FormData(form);
                const res = await fetch("{{ route('checkout.create') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body
                });

                if (!res.ok) {
                    const err = await res.json().catch(() => ({
                        error: 'Errore'
                    }));
                    errorEl.textContent = err.error || 'Errore in checkout.';
                    payBtn.disabled = false;
                    return;
                }

                const data = await res.json();
                clientSecret = data.clientSecret;

                // 2) monta Payment Element la prima volta
                if (!elements) {
                    elements = stripe.elements({
                        clientSecret
                    });
                    const paymentElement = elements.create('payment');
                    paymentElement.mount('#payment-element');
                }

                // 3) conferma pagamento
                const {
                    error
                } = await stripe.confirmPayment({
                    elements,
                    clientSecret,
                    confirmParams: {
                        return_url: "{{ route('order.thankyou') }}"
                    }
                });

                if (error) {
                    errorEl.textContent = error.message;
                    payBtn.disabled = false;
                }
            });
        })();
    </script>
</body>

</html>
