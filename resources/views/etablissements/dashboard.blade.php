@extends('layouts.main')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4 establishment-dashboard">
        <header class="dashboard-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
            <div>
                <p class="dashboard-eyebrow mb-2">APERÇU OPÉRATIONNEL</p>
                <h1 class="h3 mb-1 fw-bold">Dashboard établissement</h1>
                <p class="text-muted mb-0">Ventes, services et stocks de vos établissements réunis au même endroit.</p>
            </div>
            <div class="dashboard-scope">
                <i class="fas fa-building me-2" aria-hidden="true"></i>
                {{ $etablissements->count() }} établissement{{ $etablissements->count() === 1 ? '' : 's' }} suivi{{ $etablissements->count() === 1 ? '' : 's' }}
                <span class="scope-dot"></span> Mis à jour {{ now()->format('d/m/Y à H:i') }}
            </div>
        </header>

        <section class="mb-4" aria-labelledby="quick-actions-title">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h2 id="quick-actions-title" class="section-title mb-0">Accès rapides</h2>
            </div>
            <nav class="quick-actions" aria-label="Accès rapides aux modules">
                <a href="{{ route('client.sales.pos') }}" class="quick-action quick-action-primary">
                    <i class="fas fa-cash-register" aria-hidden="true"></i><span>Point de vente</span>
                </a>
                <a href="{{ route('client.sales.history') }}" class="quick-action">
                    <i class="fas fa-receipt" aria-hidden="true"></i><span>Ventes</span>
                </a>
                <a href="{{ route('client.stocks.index') }}" class="quick-action">
                    <i class="fas fa-boxes-stacked" aria-hidden="true"></i><span>Stock</span>
                </a>
                <a href="{{ route('etablissements.reservation.index') }}" class="quick-action">
                    <i class="fas fa-calendar-check" aria-hidden="true"></i><span>Réservations</span>
                </a>
                <a href="{{ route('etablissements.paiements.index') }}" class="quick-action">
                    <i class="fas fa-money-bill-wave" aria-hidden="true"></i><span>Paiements</span>
                </a>
                @if($selectedEtablissementId)
                    <a href="{{ route('etablissements.services.index', $selectedEtablissementId) }}" class="quick-action">
                        <i class="fas fa-concierge-bell" aria-hidden="true"></i><span>Services</span>
                    </a>
                    <a href="{{ route('etablissements.promotions.index', $selectedEtablissementId) }}" class="quick-action">
                        <i class="fas fa-tags" aria-hidden="true"></i><span>Promotions</span>
                    </a>
                @endif
            </nav>
        </section>

        <section class="mb-4" aria-labelledby="today-metrics-title">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 id="today-metrics-title" class="section-title mb-0">Activité opérationnelle</h2>
                <span class="text-muted small">Données consolidées</span>
            </div>
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-xl-3">
                    <a href="{{ route('client.sales.history') }}" class="metric-card metric-sales h-100 text-decoration-none">
                        <div class="metric-top"><span>Ventes du jour</span><span class="metric-icon"><i class="fas fa-cash-register" aria-hidden="true"></i></span></div>
                        <div class="metric-value">{{ number_format($stats['ventes_jour'], 2, ',', ' ') }} <small>CDF</small></div>
                        <div class="metric-note">{{ $stats['nombre_ventes_jour'] }} transaction{{ $stats['nombre_ventes_jour'] === 1 ? '' : 's' }} POS</div>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <a href="{{ route('client.stocks.alerts') }}" class="metric-card metric-low-stock h-100 text-decoration-none">
                        <div class="metric-top"><span>Stock faible</span><span class="metric-icon"><i class="fas fa-triangle-exclamation" aria-hidden="true"></i></span></div>
                        <div class="metric-value">{{ $stats['stock_faible'] }}</div>
                        <div class="metric-note">Produit{{ $stats['stock_faible'] === 1 ? '' : 's' }} à réapprovisionner <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></div>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <a href="{{ route('client.stocks.out-of-stock') }}" class="metric-card metric-out-stock h-100 text-decoration-none">
                        <div class="metric-top"><span>Stock épuisé</span><span class="metric-icon"><i class="fas fa-box-open" aria-hidden="true"></i></span></div>
                        <div class="metric-value">{{ $stats['stock_epuise'] }}</div>
                        <div class="metric-note">Produit{{ $stats['stock_epuise'] === 1 ? '' : 's' }} indisponible{{ $stats['stock_epuise'] === 1 ? '' : 's' }} <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></div>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    @if($selectedEtablissementId)
                    <a href="{{ route('etablissements.services.index', $selectedEtablissementId) }}" class="metric-card metric-services h-100 text-decoration-none">
                    @else
                    <article class="metric-card metric-services h-100">
                    @endif
                        <div class="metric-top"><span>Services</span><span class="metric-icon"><i class="fas fa-concierge-bell" aria-hidden="true"></i></span></div>
                        <div class="metric-value">{{ $stats['total_services'] }}</div>
                        <div class="metric-note">{{ $stats['promotions_actives'] }} promotion{{ $stats['promotions_actives'] === 1 ? '' : 's' }} active{{ $stats['promotions_actives'] === 1 ? '' : 's' }}</div>
                    @if($selectedEtablissementId)
                    </a>
                    @else
                    </article>
                    @endif
                </div>
            </div>
        </section>

        <!-- Cartes de Statistiques -->
        <div class="row g-3 mb-4">
            <!-- Carte Encaissements Totaux -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 text-center py-4 stats-card">
                    <div class="card-body">
                        <i class="fas fa-cash-register fa-2x text-primary mb-3"></i>
                        <div class="display-5 fw-bold text-dark mb-1">{{ number_format($stats['total_paiements'], 2) }} $
                        </div>
                        <div class="text-muted text-uppercase small fw-semibold">Encaissements Totaux</div>
                    </div>
                </div>
            </div>

            <!-- Carte Encaissements du Mois -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 text-center py-4 stats-card">
                    <div class="card-body">
                        <i class="fas fa-calendar-alt fa-2x text-success mb-3"></i>
                        <div class="display-5 fw-bold text-dark mb-1">
                            {{ number_format($stats['paiements_mois_courant'], 2) }} $</div>
                        <div class="text-muted text-uppercase small fw-semibold">Ce mois</div>
                    </div>
                </div>
            </div>

            <!-- Carte Services -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 text-center py-4 stats-card">
                    <div class="card-body">
                        <i class="fas fa-cogs fa-2x text-info mb-3"></i>
                        <div class="display-5 fw-bold text-dark mb-1">{{ $stats['total_publicites'] }}</div>
                        <div class="text-muted text-uppercase small fw-semibold">Publicités</div>
                    </div>
                </div>
            </div>

            <!-- Carte Promotions Actives -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 text-center py-4 stats-card">
                    <div class="card-body">
                        <i class="fas fa-tag fa-2x text-warning mb-3"></i>
                        <div class="display-5 fw-bold text-dark mb-1">{{ $stats['promotions_actives'] }}</div>
                        <div class="text-muted text-uppercase small fw-semibold">Promotions Actives</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Graphiques -->
        <div class="row mt-4">
            <!-- Graphique Évolution Mensuelle -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100 chart-panel">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fas fa-chart-line text-primary me-2"></i>Évolution mensuelle
                        </h5>
                    </div>
                    <div class="card-body pt-0">
                        <canvas id="monthlyChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Graphique Répartition par Service -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100 chart-panel">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fas fa-chart-pie text-warning me-2"></i>Répartition par service
                        </h5>
                    </div>
                    <div class="card-body pt-0">
                        @if($paiementsParService->isNotEmpty())
                            <canvas id="serviceChart" height="250"></canvas>
                        @else
                            <div class="chart-empty">
                                <i class="fas fa-chart-pie" aria-hidden="true"></i>
                                <span>Aucun paiement à représenter pour le moment.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts Graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Graphique Linéaire - Évolution Mensuelle
        const monthlyCanvas = document.getElementById('monthlyChart');
        if (monthlyCanvas && window.Chart) {
        const monthlyCtx = monthlyCanvas.getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json($monthlyData['labels']),
                datasets: [{
                        label: 'Encaissements ($)',
                        data: @json($monthlyData['paiements']),
                        borderColor: '#187c78',
                        backgroundColor: 'rgba(24, 124, 120, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Nouveaux Services',
                        data: @json($monthlyData['services']),
                        borderColor: '#d97850',
                        backgroundColor: 'rgba(217, 120, 80, 0.08)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Publicités',
                        data: @json($monthlyData['publicites']),
                        borderColor: '#c59a32',
                        backgroundColor: 'rgba(197, 154, 50, 0.08)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label.includes('Encaissements')) {
                                    return label + ': ' + context.parsed.y.toLocaleString('fr-FR') + ' Fc';
                                }
                                return label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000) {
                                    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                                }
                                return value;
                            }
                        }
                    }
                }
            }
        });
        }

        // Graphique Circulaire - Répartition par Service
        const serviceCanvas = document.getElementById('serviceChart');
        if (serviceCanvas && window.Chart) {
        const serviceCtx = serviceCanvas.getContext('2d');
        new Chart(serviceCtx, {
            type: 'doughnut',
            data: {
                labels: @json($paiementsParService->keys()),
                datasets: [{
                    data: @json($paiementsParService->values()),
                    backgroundColor: [
                        '#187c78', '#d97850', '#c59a32', '#4c7790',
                        '#81a7a3', '#a16e87', '#6c8b62', '#bf8c79'
                    ],
                    hoverOffset: 10,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value.toLocaleString('fr-FR')} Fc (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        }
    </script>

    <style>
        .establishment-dashboard { --dash-ink: #24363b; --dash-muted: #6d7d82; --dash-line: #e5ebea; --dash-teal: #187c78; --dash-coral: #d97850; --dash-gold: #c59a32; }
        .dashboard-heading { border-bottom: 1px solid var(--dash-line); padding-bottom: 1.1rem; }
        .dashboard-heading h1 { color: var(--dash-ink); }
        .dashboard-eyebrow { color: var(--dash-teal); font-size: .7rem; font-weight: 800; letter-spacing: .08em; }
        .dashboard-scope { color: var(--dash-muted); font-size: .8rem; }
        .scope-dot { display: inline-block; width: 6px; height: 6px; margin: 0 .4rem; border-radius: 50%; background: #75a78d; vertical-align: middle; }
        .section-title { color: var(--dash-ink); font-size: 1rem; font-weight: 700; }
        .quick-actions { display: flex; flex-wrap: wrap; gap: .55rem; }
        .quick-action { display: inline-flex; align-items: center; gap: .55rem; min-height: 42px; padding: .55rem .85rem; border: 1px solid var(--dash-line); border-radius: 6px; color: #40565b; background: #fff; font-size: .83rem; font-weight: 600; text-decoration: none; transition: border-color .15s ease, background .15s ease; }
        .quick-action:hover, .quick-action:focus-visible { border-color: #9bc5c0; background: #f5faf9; color: var(--dash-teal); }
        .quick-action-primary { border-color: var(--dash-teal); background: var(--dash-teal); color: #fff; }
        .quick-action-primary:hover, .quick-action-primary:focus-visible { border-color: #126966; background: #126966; color: #fff; }
        .metric-card { display: block; padding: 1rem 1.05rem; border: 1px solid var(--dash-line); border-top: 3px solid var(--metric-tone, var(--dash-teal)); border-radius: 6px; background: #fff; color: inherit; }
        a.metric-card { transition: box-shadow .15s ease, transform .15s ease; }
        a.metric-card:hover, a.metric-card:focus-visible { box-shadow: 0 5px 16px rgba(30,55,58,.08); transform: translateY(-2px); }
        .metric-top { display: flex; justify-content: space-between; align-items: center; gap: .5rem; color: var(--dash-muted); font-size: .78rem; font-weight: 650; }
        .metric-icon { display: grid; place-items: center; width: 32px; height: 32px; border-radius: 6px; color: var(--metric-tone, var(--dash-teal)); background: color-mix(in srgb, var(--metric-tone, var(--dash-teal)) 11%, white); }
        .metric-value { margin-top: .85rem; color: var(--dash-ink); font-size: 1.8rem; font-weight: 750; line-height: 1.1; }
        .metric-value small { color: var(--dash-muted); font-size: .8rem; font-weight: 600; }
        .metric-note { margin-top: .45rem; color: var(--dash-muted); font-size: .75rem; }
        .metric-sales { --metric-tone: #187c78; }
        .metric-low-stock { --metric-tone: #c59a32; }
        .metric-out-stock { --metric-tone: #c55f50; }
        .metric-services { --metric-tone: #6c8b62; }
        .stats-card { border: 1px solid var(--dash-line) !important; border-radius: 6px !important; transition: box-shadow .15s ease; }
        .stats-card:hover { box-shadow: 0 5px 16px rgba(30,55,58,.08) !important; }
        .stats-card .display-5 { font-size: 1.7rem; }
        .chart-panel { border: 1px solid var(--dash-line) !important; border-radius: 6px !important; }
        .chart-panel .card-header { border-radius: 6px 6px 0 0; }
        .chart-panel:hover { transform: none !important; }
        .chart-empty { display: grid; justify-items: center; align-content: center; gap: .65rem; min-height: 250px; color: var(--dash-muted); text-align: center; font-size: .85rem; }
        .chart-empty i { color: var(--dash-gold); font-size: 1.5rem; }
        canvas { width: 100% !important; height: 250px !important; }
        @media (max-width: 576px) {
            .dashboard-scope { width: 100%; }
            .quick-actions { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .quick-action { justify-content: flex-start; padding-inline: .65rem; }
            .metric-value { font-size: 1.6rem; }
        }
    </style>
@endsection
