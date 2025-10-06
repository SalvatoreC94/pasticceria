<!doctype html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <title>{{ $product->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        :root {
            --b: #e5e7eb;
            --mut: #6b7280
        }

        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, sans-serif;
            margin: 20px;
            max-width: 960px;
        }

        img {
            max-width: 600px;
            width: 100%;
            border-radius: 12px;
            background: #fafafa
        }

        .muted {
            color: #6b7280
        }

        .btn {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--b);
            background: #fff;
            cursor: not-allowed
        }

        .price {
            font-weight: 700;
            font-size: 20px
        }
    </style>
</head>

<body>
    <p style="margin-bottom:10px;"><a href="{{ url()->previous() }}" style="text-decoration:none;">← Indietro</a></p>

    <h1>{{ $product->name }}</h1>
    @if ($product->image_path)
        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
    @endif

    <p class="muted" style="margin:8px 0;">{{ $product->category?->name }}</p>

    @if ($product->description)
        <p>{{ $product->description }}</p>
    @endif

    <p class="price">
        {{ number_format($product->price_cents / 100, 2, ',', '.') }} €
    </p>

    @if (!empty($product->allergens))
        <p class="muted">Allergeni: {{ implode(', ', $product->allergens) }}</p>
    @endif

    <!-- Placeholder carrello: lo agganciamo nel prossimo step -->
    <button class="btn" title="In arrivo">Aggiungi al carrello (coming soon)</button>
</body>

</html>
