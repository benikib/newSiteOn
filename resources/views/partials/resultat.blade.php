@extends('layouts.public')

@section('styles')
    <style>
        .search-results-page { min-height: 65vh; padding: .5rem 0 4rem; color: #202923; }
        .search-results-hero { margin: .8rem auto 1.5rem; padding: clamp(1.4rem, 4vw, 2.6rem); border-radius: 1rem; background: #203b30; color: white; }
        .search-results-hero p { margin: 0 0 .45rem; color: #b9d9c6; font-size: .76rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .search-results-hero h1 { margin: 0; font: 600 clamp(1.9rem, 4vw, 2.8rem)/1.1 Georgia, serif; }
        .search-results-hero form { display: flex; max-width: 740px; gap: .55rem; margin-top: 1.25rem; padding: .35rem; border-radius: .75rem; background: #fff; }
        .search-results-hero input { min-width: 0; flex: 1; min-height: 46px; padding: 0 .8rem; border: 0; outline: 0; }
        .search-results-hero button { min-height: 46px; padding: 0 1rem; border: 0; border-radius: .55rem; background: #c45c36; color: #fff; font-weight: 700; }
        .search-results-heading { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: end; gap: 1rem; margin: 1.5rem 0 1rem; }
        .search-results-heading h2 { margin: 0; font: 600 1.55rem Georgia, serif; }
        .search-results-heading p { margin: .3rem 0 0; color: #697770; }
        .search-results-count { padding: .5rem .8rem; border-radius: 99px; background: #e6eee7; color: #245c48; font-size: .85rem; font-weight: 700; }
        .search-applied { display: flex; flex-wrap: wrap; align-items: center; gap: .45rem; margin: 0 0 1.25rem; }
        .search-applied-label { margin-right: .2rem; color: #697770; font-size: .84rem; }
        .search-applied span { padding: .35rem .65rem; border: 1px solid #dce4dc; border-radius: 99px; background: white; color: #34433a; font-size: .78rem; }
        .search-applied a { margin-left: auto; color: #a94628; font-size: .84rem; font-weight: 700; }
        .search-results-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .search-result-card { min-width: 0; overflow: hidden; border: 1px solid #e1e7e1; border-radius: .9rem; background: white; transition: transform .18s ease, box-shadow .18s ease; }
        .search-result-card:hover { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(29,45,39,.09); }
        .search-result-card > * { height: 100%; }
        .search-result-card .card { height: 100%; border: 0; border-radius: 0; box-shadow: none; }
        .search-result-card .card-img-top { height: 190px; object-fit: cover; }
        .search-result-card .card-body { padding: 1rem; }
        .search-result-card .btn { min-height: 42px; border-radius: .6rem; background: #245c48; border-color: #245c48; }
        .search-results-empty { padding: 3rem 1rem; border: 1px dashed #cbd5cc; border-radius: 1rem; background: white; text-align: center; }
        .search-results-empty i { color: #84958a; }
        .search-results-empty h2 { margin: .8rem 0 .4rem; font: 600 1.45rem Georgia, serif; }
        .search-results-empty p { color: #697770; }
        @media (max-width: 850px) { .search-results-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 575px) {
            .search-results-grid { grid-template-columns: 1fr; }
            .search-results-hero { margin-top: .3rem; }
            .search-results-hero form { flex-wrap: wrap; }
            .search-results-hero input { flex-basis: 100%; }
            .search-results-hero button { width: 100%; }
            .search-result-card .card-img-top { height: 210px; }
        }
        @media (prefers-reduced-motion: reduce) { .search-result-card { transition: none; } }
    </style>
@endsection

@section('content')
    <main class="search-results-page">
        <header class="search-results-hero">
            <p>Recherche BISIKA</p>
            <h1>Trouvez votre prochaine bonne adresse.</h1>
            <form action="{{ route('search') }}" method="GET" role="search">
                <label class="visually-hidden" for="results-query">Nom, service ou établissement</label>
                <input id="results-query" name="query" value="{{ request('query') }}" placeholder="Nom, service ou établissement" autocomplete="off">
                @foreach(request()->except('query', 'page') as $key => $value)
                    @if(is_scalar($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif
                @endforeach
                <button type="submit"><i class="fas fa-search me-2" aria-hidden="true"></i>Rechercher</button>
            </form>
        </header>

        <div class="search-results-heading">
            <div>
                <h2>Établissements trouvés</h2>
                <p>Parcourez les établissements et ouvrez leur fiche pour découvrir leurs services et produits.</p>
            </div>
            <span class="search-results-count">{{ $results->total() }} résultat{{ $results->total() === 1 ? '' : 's' }}</span>
        </div>

        @if(request()->anyFilled(['query', 'type_etablissement', 'ville', 'commune', 'quartier', 'budget_max', 'has_promotion']))
            <div class="search-applied" aria-label="Filtres appliqués">
                <span class="search-applied-label"><i class="fas fa-filter me-1" aria-hidden="true"></i>Critères :</span>
                @foreach(['query' => 'Recherche', 'type_etablissement' => 'Type', 'ville' => 'Ville', 'commune' => 'Commune', 'quartier' => 'Quartier', 'budget_max' => 'Budget max'] as $key => $label)
                    @if(request()->filled($key))<span>{{ $label }} : {{ request($key) }}</span>@endif
                @endforeach
                @if(request()->boolean('has_promotion'))<span>Promotions actives</span>@endif
                <a href="{{ route('search') }}">Effacer les critères</a>
            </div>
        @endif

        @if($results->isEmpty())
            <section class="search-results-empty">
                <i class="fas fa-magnifying-glass fa-2x" aria-hidden="true"></i>
                <h2>Aucun établissement trouvé</h2>
                <p>Essayez une autre recherche ou retirez un critère.</p>
                <a class="btn btn-outline-success" href="{{ route('search') }}">Réinitialiser la recherche</a>
            </section>
        @else
            <section class="search-results-grid" aria-label="Résultats de recherche">
                @foreach($results as $etablissement)
                    <article class="search-result-card">
                        @include('partials.etablissement-card', [
                            'etablissement' => $etablissement,
                            'show_category' => true,
                            'show_rating' => true,
                        ])
                    </article>
                @endforeach
            </section>
            <nav class="d-flex justify-content-center mt-4" aria-label="Pagination des résultats">
                {{ $results->appends(request()->query())->links('pagination::bootstrap-5') }}
            </nav>
        @endif
    </main>
@endsection
