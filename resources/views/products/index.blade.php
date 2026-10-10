@extends('layouts.public')

@section('styles')
    <style>
        .products-page {
            --catalog-ink: #1d2d27;
            --catalog-muted: #697770;
            --catalog-green: #245c48;
            --catalog-coral: #c45c36;
            --catalog-paper: #f5f6f1;
            --catalog-line: #e1e7e1;
            color: var(--catalog-ink);
            padding-bottom: 4rem;
        }
        .products-hero {
            position: relative;
            overflow: hidden;
            margin: 1.5rem auto 2.25rem;
            padding: clamp(1.5rem, 5vw, 3.5rem);
            border-radius: 1.25rem;
            background: #203b30;
            color: #fff;
        }
        .products-hero::after {
            position: absolute;
            top: -6rem;
            right: -3rem;
            width: 18rem;
            height: 18rem;
            border: 1px solid rgba(255,255,255,.16);
            border-radius: 50%;
            content: "";
            pointer-events: none;
        }
        .products-hero-copy { position: relative; z-index: 1; max-width: 680px; }
        .products-kicker { margin: 0 0 .7rem; color: #b9d9c6; font-size: .78rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .products-hero h1 { margin: 0; font: 600 clamp(2rem, 4vw, 3.5rem)/1.08 Georgia, serif; }
        .products-hero p:not(.products-kicker) { max-width: 560px; margin: 1rem 0 1.4rem; color: #dce8df; line-height: 1.7; }
        .products-search { display: flex; gap: .6rem; max-width: 630px; padding: .45rem; border-radius: .85rem; background: white; }
        .products-search input { flex: 1; min-width: 0; min-height: 46px; padding: 0 .8rem; border: 0; color: var(--catalog-ink); outline: none; }
        .products-search button { min-height: 46px; padding: 0 1.1rem; border: 0; border-radius: .6rem; background: var(--catalog-coral); color: white; font-weight: 700; }
        .products-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin: 0 0 1.2rem; }
        .products-toolbar h2 { margin: 0; font: 600 1.75rem/1.2 Georgia, serif; }
        .products-toolbar p { margin: .35rem 0 0; color: var(--catalog-muted); }
        .products-sort { display: flex; align-items: center; gap: .6rem; color: var(--catalog-muted); font-size: .9rem; }
        .products-sort select, .products-stock-filter { min-height: 44px; padding: 0 .8rem; border: 1px solid var(--catalog-line); border-radius: .65rem; background: #fff; color: var(--catalog-ink); }
        .products-category-list { display: flex; gap: .5rem; overflow-x: auto; margin: 0 0 1.5rem; padding: .15rem .1rem .6rem; scrollbar-width: thin; }
        .products-category { flex: 0 0 auto; min-height: 42px; padding: 0 1rem; border: 1px solid var(--catalog-line); border-radius: 99px; background: white; color: var(--catalog-muted); font-weight: 700; }
        .products-category[aria-pressed="true"], .products-category:hover { border-color: var(--catalog-green); background: var(--catalog-green); color: white; }
        .products-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .85rem; }
        .catalog-card { min-width: 0; overflow: hidden; border: 1px solid var(--catalog-line); border-radius: .9rem; background: white; transition: transform .18s ease, box-shadow .18s ease; }
        .catalog-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(29,45,39,.1); }
        .catalog-image-wrap { position: relative; display: block; overflow: hidden; aspect-ratio: 1.22; background: var(--catalog-paper); }
        .catalog-image-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .catalog-image-fallback { display: grid; width: 100%; height: 100%; place-items: center; color: #9aa79e; font-size: 2rem; }
        .catalog-stock { position: absolute; top: .65rem; left: .65rem; padding: .35rem .6rem; border-radius: 99px; background: #e4f3e8; color: #245c48; font-size: .72rem; font-weight: 700; }
        .catalog-stock-low { background: #fff0dc; color: #7c561e; }
        .catalog-stock-none { background: #f8e8e5; color: #8e382d; }
        .catalog-info { padding: .85rem .9rem .35rem; }
        .catalog-category { color: var(--catalog-muted); font-size: .74rem; }
        .catalog-name { display: -webkit-box; overflow: hidden; min-height: 2.7rem; margin: .3rem 0 .2rem; font-size: .98rem; font-weight: 700; line-height: 1.35; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
        .catalog-establishment { display: block; overflow: hidden; color: var(--catalog-muted); font-size: .78rem; text-overflow: ellipsis; white-space: nowrap; }
        .catalog-price { margin: .55rem 0 .15rem; color: var(--catalog-green); font-size: 1rem; font-weight: 800; }
        .catalog-availability { color: var(--catalog-muted); font-size: .76rem; }
        .catalog-action { display: flex; align-items: center; justify-content: space-between; gap: .6rem; padding: .65rem .9rem .9rem; }
        .catalog-action a { min-height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 0 .8rem; border-radius: .6rem; background: var(--catalog-green); color: white; font-size: .82rem; font-weight: 700; text-decoration: none; }
        .catalog-action a:hover { background: #194532; color: white; }
        .catalog-empty { grid-column: 1 / -1; padding: 2.5rem 1rem; border: 1px dashed #cbd5cc; border-radius: 1rem; background: white; text-align: center; }
        .catalog-empty h3 { font: 600 1.4rem Georgia, serif; }
        .catalog-empty p { margin: .4rem 0 1rem; color: var(--catalog-muted); }
        .catalog-hidden { display: none !important; }
        @media (min-width: 680px) { .products-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; } }
        @media (min-width: 1024px) { .products-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1.15rem; } }
        @media (max-width: 575px) {
            .products-page { padding-bottom: 2.5rem; }
            .products-hero { margin-top: .8rem; border-radius: 1rem; }
            .products-search { flex-wrap: wrap; }
            .products-search input { flex-basis: 100%; }
            .products-search button { width: 100%; }
            .products-toolbar { align-items: flex-start; }
            .products-sort { width: 100%; }
            .products-sort select { flex: 1; }
            .catalog-info { padding: .7rem .7rem .25rem; }
            .catalog-action { padding: .55rem .7rem .7rem; }
            .catalog-action a { padding: 0 .55rem; font-size: .76rem; }
        }
        @media (prefers-reduced-motion: reduce) { .catalog-card { transition: none; } }
    </style>
@endsection

@section('content')
    <div class="products-page">
        <section class="products-hero" aria-labelledby="products-title">
            <div class="products-hero-copy">
                <p class="products-kicker">Le catalogue BISIKA</p>
                <h1 id="products-title">Des produits à découvrir, près de chez vous.</h1>
                <p>Parcourez les articles proposés par les établissements actifs et vérifiez leur disponibilité avant de réserver.</p>
                <form class="products-search" method="GET" action="{{ route('products.index') }}" role="search">
                    <label class="visually-hidden" for="catalog-query">Rechercher un produit</label>
                    <input id="catalog-query" name="q" type="search" value="{{ $query }}" placeholder="Nom du produit, catégorie ou établissement" autocomplete="off">
                    <button type="submit"><i class="fas fa-search me-2" aria-hidden="true"></i>Rechercher</button>
                </form>
            </div>
        </section>

        <section aria-labelledby="catalog-results-title">
            <div class="products-toolbar">
                <div>
                    <h2 id="catalog-results-title">Catalogue produits</h2>
                    <p><span id="catalog-count">{{ $products->count() }}</span> article{{ $products->count() === 1 ? '' : 's' }} proposé{{ $products->count() === 1 ? '' : 's' }}</p>
                </div>
                <div class="products-sort">
                    <label for="catalog-stock">Disponibilité</label>
                    <select class="products-stock-filter" id="catalog-stock">
                        <option value="all">Tous les stocks</option>
                        <option value="available">En stock</option>
                        <option value="low">Stock faible</option>
                        <option value="none">Indisponible</option>
                    </select>
                    <label for="catalog-sort">Trier</label>
                    <select id="catalog-sort">
                        <option value="newest">Nouveautés</option>
                        <option value="name">Nom</option>
                        <option value="price-asc">Prix croissant</option>
                        <option value="price-desc">Prix décroissant</option>
                    </select>
                </div>
            </div>

            <div class="products-category-list" role="group" aria-label="Filtrer par catégorie">
                <button class="products-category" type="button" data-category="all" aria-pressed="true">Toutes les catégories</button>
                @foreach($categories as $category)
                    <button class="products-category" type="button" data-category="{{ $category->id }}" aria-pressed="false">{{ $category->nom }}</button>
                @endforeach
            </div>

            <div class="products-grid" id="catalog-grid" aria-live="polite">
                @forelse($products as $product)
                    @php
                        $available = (int) $product->available_quantity;
                        $minimum = (int) ($product->stock->minimum_stock ?? 0);
                        $stockState = $available <= 0 ? 'none' : ($minimum > 0 && $available <= $minimum ? 'low' : 'available');
                    @endphp
                    <article class="catalog-card" data-name="{{ $product->name }}" data-price="{{ $product->stock->selling_price ?? 0 }}" data-created="{{ $product->created_at?->timestamp ?? 0 }}" data-category="{{ $product->category_id }}" data-stock="{{ $stockState }}">
                        <div class="catalog-image-wrap">
                            @if($product->image)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" onerror="this.remove()">
                            @else
                                <span class="catalog-image-fallback" aria-hidden="true"><i class="fas fa-box-open"></i></span>
                            @endif
                            <span class="catalog-stock {{ $stockState === 'low' ? 'catalog-stock-low' : ($stockState === 'none' ? 'catalog-stock-none' : '') }}">
                                {{ $stockState === 'none' ? 'Indisponible' : ($stockState === 'low' ? 'Stock faible' : 'En stock') }}
                            </span>
                        </div>
                        <div class="catalog-info">
                            <span class="catalog-category">{{ $product->category->nom ?? 'Autres produits' }}</span>
                            <h3 class="catalog-name">{{ $product->name }}</h3>
                            <span class="catalog-establishment">{{ $product->etablissement->nom }}</span>
                            <p class="catalog-price">{{ number_format($product->stock->selling_price ?? 0, 0, ',', ' ') }} CDF</p>
                            <span class="catalog-availability">{{ $available }} disponible{{ $available === 1 ? '' : 's' }} {{ $product->unit->symbol ?? '' }}</span>
                        </div>
                        <div class="catalog-action">
                            <span class="small text-muted">{{ $product->code }}</span>
                            <a href="{{ route('ets.info', $product->etablissement_id) }}#produits" aria-label="Voir {{ $product->name }} chez {{ $product->etablissement->nom }}">Voir / réserver</a>
                        </div>
                    </article>
                @empty
                    <div class="catalog-empty">
                        <h3>{{ $query ? 'Aucun produit trouvé' : 'Le catalogue se prépare' }}</h3>
                        <p>{{ $query ? 'Essayez un autre nom, une catégorie ou un établissement.' : 'Les produits publiés par les établissements apparaîtront ici.' }}</p>
                        @if($query)<a class="btn btn-outline-success" href="{{ route('products.index') }}">Afficher tout le catalogue</a>@endif
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <script>
        (() => {
            const grid = document.getElementById('catalog-grid');
            const cards = [...grid.querySelectorAll('.catalog-card')];
            const categoryButtons = [...document.querySelectorAll('[data-category]')];
            const stockFilter = document.getElementById('catalog-stock');
            const sortSelect = document.getElementById('catalog-sort');
            let activeCategory = 'all';

            const render = () => {
                const visibleCards = cards.filter(card =>
                    (activeCategory === 'all' || card.dataset.category === activeCategory)
                    && (stockFilter.value === 'all' || card.dataset.stock === stockFilter.value)
                );
                visibleCards.sort((first, second) => {
                    switch (sortSelect.value) {
                        case 'name': return first.dataset.name.localeCompare(second.dataset.name, 'fr');
                        case 'price-asc': return Number(first.dataset.price) - Number(second.dataset.price);
                        case 'price-desc': return Number(second.dataset.price) - Number(first.dataset.price);
                        default: return Number(second.dataset.created) - Number(first.dataset.created);
                    }
                });
                visibleCards.forEach(card => grid.appendChild(card));
                cards.filter(card => !visibleCards.includes(card)).forEach(card => card.classList.add('catalog-hidden'));
                visibleCards.forEach(card => card.classList.remove('catalog-hidden'));
                document.getElementById('catalog-count').textContent = visibleCards.length;
            };

            categoryButtons.forEach(button => button.addEventListener('click', () => {
                activeCategory = button.dataset.category;
                categoryButtons.forEach(chip => {
                    const selected = chip === button;
                    chip.setAttribute('aria-pressed', String(selected));
                });
                render();
            }));
            stockFilter.addEventListener('change', render);
            sortSelect.addEventListener('change', render);
        })();
    </script>
@endsection