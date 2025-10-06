<!doctype html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <title>Carrello</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            margin: 20px
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb
        }

        .actions form {
            display: inline
        }

        .total {
            font-weight: 700;
            text-align: right
        }

        .btn {
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
            text-decoration: none
        }
    </style>
</head>

<body>
    <h1>Carrello</h1>

    @if (session('ok'))
        <p style="color:green">{{ session('ok') }}</p>
    @endif
    @if ($errors->any())
        <p style="color:#b91c1c">{{ $errors->first() }}</p>
    @endif

    @if ($items->isEmpty())
        <p>Il carrello è vuoto.</p>
        <a href="{{ route('catalogo') }}" class="btn">Torna al catalogo</a>
    @else
        <table>
            <thead>
                <tr>
                    <th>Prodotto</th>
                    <th>Prezzo</th>
                    <th>Qtà</th>
                    <th>Totale</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $it)
                    <tr>
                        <td>{{ $it->product->name }}</td>
                        <td>{{ number_format($it->unit_price_cents / 100, 2, ',', '.') }} €</td>
                        <td>
                            <form method="POST" action="{{ route('cart.update', $it) }}">
                                @csrf @method('PATCH')
                                <input type="number" name="qty" value="{{ $it->qty }}" min="1"
                                    max="20" style="width:60px">
                                <button class="btn" type="submit">Aggiorna</button>
                            </form>
                        </td>
                        <td>{{ number_format($it->total_cents / 100, 2, ',', '.') }} €</td>
                        <td class="actions">
                            <form method="POST" action="{{ route('cart.remove', $it) }}">
                                @csrf @method('DELETE')
                                <button class="btn" type="submit">Rimuovi</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="total">Subtotale: {{ number_format($subtotal / 100, 2, ',', '.') }} €</p>

        <div style="display:flex;gap:8px;justify-content:flex-end">
            <a href="{{ route('catalogo') }}" class="btn">Continua lo shopping</a>
            <a href="{{ route('checkout.show') }}" class="btn"
                style="border-color:#111827;background:#111827;color:#fff">
                Vai al checkout
            </a>
        </div>
    @endif
</body>

</html>
