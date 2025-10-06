<!doctype html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <title>Pasticceria • Catalogo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        :root {
            --b: #e5e7eb;
            --mut: #6b7280
        }

        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, sans-serif;
            margin: 20px;
        }

        a {
            text-decoration: none;
            color: inherit
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
        }

        .card {
            border: 1px solid var(--b);
            border-radius: 12px;
            padding: 12px;
            background: #fff;
        }

        .badge {
            background: #f3f4f6;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 12px;
        }

        img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 10px;
            background: #fafafa;
        }

        .muted {
            color: var(--mut)
        }

        .price {
            font-weight: 700;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
    </style>
</head>

<body>
    <header>
        <h1>Catalogo</h1>
        <a href="/admin" class="badge">Admin</a>
    </header>

    <h2>Categorie</h2>
    <div class="grid">
        @foreach ($categories as $cat)
            <a href="{{ route('categoria.show', $cat) }}" class="card"
                aria-label="Vai alla categoria {{ $cat->name }}">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <strong>{{ $cat->name }}</strong>
                    <span class="badge">{{ $cat->products_count }} prod.</span>
                </div>
                @if ($cat->description)
                    <p class="muted" style="margin-top:6px">{{ $cat->description }}</p>
                @endif
            </a>
        @endforeach
    </div>

    <h2 style="margin-top:24px;">Ultimi prodotti</h2>
    <div class="grid">
        @foreach ($products as $p)
            <a href="{{ route('prodotto.show', $p) }}" class="card" aria-label="Apri {{ $p->name }}">
                @if ($p->image_path)
                    <img src="{{ asset('storage/' . $p->image_path) }}" alt="{{ $p->name }}">
                @else
                    <img src="https://placehold.co/600x400?text={{ urlencode($p->name) }}" alt="">
                @endif
                <h3 style="margin:10px 0 4px 0;">{{ $p->name }}</h3>
                <div class="muted" style="font-size:14px;">{{ $p->category?->name }}</div>
                <div class="price" style="margin-top:6px;">
                    {{ number_format($p->price_cents / 100, 2, ',', '.') }} €
                </div>
                @if (!empty($p->allergens))
                    <div class="muted" style="margin-top:8px; font-size:12px;">
                        Allergeni: {{ implode(', ', $p->allergens) }}
                    </div>
                @endif
            </a>
        @endforeach
    </div>
</body>

</html>
