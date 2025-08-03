@extends('layouts.main')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800 fw-bold">Dashboard Établissement</h1>
                <p class="text-muted mb-0">Gérez vos établissements et suivez vos performances</p>
            </div>
        </div>

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
                <div class="card border-0 shadow-sm h-100">
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
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fas fa-chart-pie text-warning me-2"></i>Répartition par service
                        </h5>
                    </div>
                    <div class="card-body pt-0">
                        <canvas id="serviceChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts Graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Graphique Linéaire - Évolution Mensuelle
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json($monthlyData['labels']),
                datasets: [{
                        label: 'Encaissements ($)',
                        data: @json($monthlyData['paiements']),
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Nouveaux Services',
                        data: @json($monthlyData['services']),
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Publicités',
                        data: @json($monthlyData['publicites']),
                        borderColor: '#f6c23e',
                        backgroundColor: 'rgba(246, 194, 62, 0.1)',
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
                                    return label + ': ' + context.parsed.y.toLocaleString('fr-FR') + ' €';
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

        // Graphique Circulaire - Répartition par Service
        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        new Chart(serviceCtx, {
            type: 'doughnut',
            data: {
                labels: @json($paiementsParService->keys()),
                datasets: [{
                    data: @json($paiementsParService->values()),
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#f6c23e', '#e74a3b',
                        '#36b9cc', '#5a5c69', '#858796', '#b7b7b7'
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
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value.toLocaleString('fr-FR')} € (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>

    <style>
        .stats-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 12px;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .card {
            border-radius: 0.5rem;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        canvas {
            width: 100% !important;
            height: 250px !important;
        }
    </style>
@endsection
