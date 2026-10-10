@extends('layouts.public')

@section('title', 'BISIKA | Trouvez votre prochaine bonne adresse')

@section('styles')
    <style>
        .home-simple { min-height: 68vh; padding: clamp(2rem, 7vw, 5.5rem) 0 3.5rem; }
        .home-search-panel { width: min(790px, calc(100% - 2rem)); margin: 0 auto 3.5rem; text-align: center; }
        .home-search-kicker { margin: 0 0 .8rem; color: #245c48; font-size: .76rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
        .home-search-panel h1 { max-width: 760px; margin: 0 auto; color: #202923; font: 600 clamp(2.1rem, 5vw, 3.8rem)/1.06 Georgia, serif; }
        .home-search-panel > p:not(.home-search-kicker) { max-width: 580px; margin: 1rem auto 1.6rem; color: #68756d; font-size: 1.02rem; line-height: 1.7; }
        .home-search-form { display: flex; gap: .55rem; padding: .45rem; border: 1px solid #e0e6df; border-radius: .85rem; background: #fff; box-shadow: 0 12px 32px rgba(29,45,39,.1); text-align: left; }
        .home-search-form input { flex: 1; min-width: 0; min-height: 50px; padding: 0 .95rem; border: 0; border-radius: .55rem; background: transparent; color: #202923; font: inherit; outline: 0; }
        .home-search-form input:focus-visible { outline: 3px solid rgba(36,92,72,.25); outline-offset: -2px; }
        .home-search-form button { min-height: 50px; padding: 0 1.2rem; border: 0; border-radius: .6rem; background: #c45c36; color: white; font-weight: 700; }
        .home-search-form button:hover { background: #a94628; }
        .home-ads { width: min(1180px, calc(100% - 2rem)); margin: 0 auto; }
        .home-ads-heading { display: flex; align-items: end; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
        .home-ads-heading p { margin: 0 0 .3rem; color: #68756d; font-size: .75rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        .home-ads-heading h2 { margin: 0; color: #202923; font: 600 clamp(1.45rem, 3vw, 1.9rem) Georgia, serif; }
        .home-ads-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .home-ad { position: relative; min-width: 0; overflow: hidden; aspect-ratio: 1.6; border: 1px solid #e1e7e1; border-radius: .9rem; background: #e8ede7; color: white; text-decoration: none; }
        .home-ad img { width: 100%; height: 100%; object-fit: cover; transition: transform .2s ease; }
        .home-ad:hover img { transform: scale(1.035); }
        .home-ad::after { position: absolute; inset: 35% 0 0; background: linear-gradient(transparent, rgba(12,28,19,.78)); content: ""; pointer-events: none; }
        .home-ad-title { position: absolute; z-index: 1; right: 1rem; bottom: .9rem; left: 1rem; color: white; font-weight: 700; line-height: 1.35; text-shadow: 0 1px 3px rgba(0,0,0,.35); }
        .home-ads-empty { padding: 1.4rem; border: 1px dashed #cad4cb; border-radius: .85rem; background: #fff; color: #68756d; text-align: center; }
        @media (max-width: 767px) { .home-simple { padding-top: 2.3rem; } .home-ads-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .7rem; } .home-ad { aspect-ratio: 1.15; } }
        @media (max-width: 480px) { .home-search-form { flex-wrap: wrap; } .home-search-form input { flex-basis: 100%; } .home-search-form button { width: 100%; } .home-ads-grid { grid-template-columns: 1fr; } .home-ad { aspect-ratio: 1.7; } }
        @media (prefers-reduced-motion: reduce) { .home-ad img { transition: none; } }
    </style>
@endsection

@section('content')
    <main class="home-simple">
        <section class="home-search-panel" aria-labelledby="home-title">
            <p class="home-search-kicker">Établissements & services en RDC</p>
            <h1 id="home-title">Trouvez votre prochaine bonne adresse.</h1>
            <p>Recherchez un établissement, un service ou une ville et découvrez les offres disponibles sur BISIKA.</p>
            <form class="home-search-form" action="{{ route('search') }}" method="GET" role="search">
                <label class="visually-hidden" for="home-query">Nom, établissement ou service</label>
                <input id="home-query" type="search" name="query" value="{{ request('query') }}" placeholder="Nom, établissement ou service..." autocomplete="off">
                <button type="submit"><i class="fas fa-search me-2" aria-hidden="true"></i>Rechercher</button>
            </form>
        </section>

        <section class="home-ads" aria-labelledby="home-ads-title">
            <div class="home-ads-heading">
                <div><p>À la une</p><h2 id="home-ads-title">Publicités des établissements</h2></div>
            </div>
            @if($photos->isNotEmpty())
                <div class="home-ads-grid">
                    @foreach($photos as $photo)
                        <a class="home-ad" href="{{ route('ets.info', $photo->etablissement_id) }}">
                            <img src="{{ asset('storage/' . ltrim(str_replace('public/', '', $photo->image_path), '/')) }}" alt="{{ $photo->titre ?: 'Publicité d’un établissement BISIKA' }}" loading="lazy">
                            @if($photo->titre)<span class="home-ad-title">{{ $photo->titre }}</span>@endif
                        </a>
                    @endforeach
                </div>
            @else
                <p class="home-ads-empty">Les publicités des établissements apparaîtront ici.</p>
            @endif
        </section>
    </main>
@endsection