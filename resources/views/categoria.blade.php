<!doctype html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <title>{{ $category->name }} • Catalogo</title>
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

        nav.breadcrumb a {
            color: #2563eb
        }
    </style>
</head>

<body>
    <header style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <h1 style="margin:0;">Catalogo</h1>

        <nav style="display:flex; gap:10px; align-items:center;">
            @guest
                <a href="{{ route('login') }}"
                    style="padding:10px 16px; border:1px solid #e5e7eb; border-radius:10px; text-decoration:none;">
                    Accedi
                </a>
                <a href="{{ route('register') }}"
                    style="padding:10px 16px; border:1px solid #111827; background:#111827; color:#fff; border-radius:10px; text-decoration:none;">
                    Registrati
                </a>
            @endguest

            @auth
                <span style="font-size:14px; color:#6b7280;">Ciao, {{ auth()->user()->name }}</span>
                <a href="{{ route('dashboard') }}"
                    style="padding:8px 12px; border:1px solid #e5e7eb; border-radius:10px; text-decoration:none;">
                    Account
                </a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit"
                        style="padding:8px 12px; border:1px solid #ef4444; color:#ef4444; border-radius:10px; background:#fff;">
                        Esci
                    </button>
                </form>
            @endauth
        </nav>
    </header>

    <nav class="breadcrumb" style="margin-bottom:12px;">
        <a href="{{ route('catalogo') }}">Home</a> › <span class="muted">{{ $category->name }}</span>
    </nav>

    <h1>{{ $category->name }}</h1>
    @if ($category->description)
        <p class="muted">{{ $category->description }}</p>
    @endif

    <div class="grid" style="margin-top:16px;">
        @forelse($products as $p)
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
            </a>
        @empty
            <p class="muted">Nessun prodotto in questa categoria.</p>
        @endforelse
    </div>

    <div style="margin-top:16px;">
        {{ $products->links() }}
    </div>
</body>

</html>
