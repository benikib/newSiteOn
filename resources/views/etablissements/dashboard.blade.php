@extends('layouts.main')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800 fw-bold">Dashboard Établissement</h1>
                <p class="text-muted mb-0">Gérez vos établissements et suivez vos performances</p>
            </div>
            {{-- <button class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#addEtablissementModal">
                <i class="fas fa-plus me-2"></i>Ajouter Établissement
            </button> --}}
        </div>

        <!-- Cartes de Statistiques -->

        <div class="row g-3 mb-4">
            <div class="row g-3 mb-4">
                <!-- Carte Établissements -->
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 text-center py-4 stats-card">
                        <div class="card-body">
                            <i class="fas fa-building fa-2x text-primary mb-3"></i>
                            <div class="display-5 fw-bold text-dark mb-1">{{ $stats['total_etablissements'] ?? 0 }}</div>
                            <div class="text-muted text-uppercase small fw-semibold">Établissements</div>
                        </div>
                    </div>
                </div>

                <!-- Carte Promotions Actives -->
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 text-center py-4 stats-card">
                        <div class="card-body">
                            <i class="fas fa-tag fa-2x text-success mb-3"></i>
                            <div class="display-5 fw-bold text-dark mb-1">{{ $stats['promotions_actives'] ?? 0 }}</div>
                            <div class="text-muted text-uppercase small fw-semibold">Promotions</div>
                        </div>
                    </div>
                </div>

                <!-- Carte Services -->
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 text-center py-4 stats-card">
                        <div class="card-body">
                            <i class="fas fa-cogs fa-2x text-info mb-3"></i>
                            <div class="display-5 fw-bold text-dark mb-1">{{ $stats['total_services'] ?? 0 }}</div>
                            <div class="text-muted text-uppercase small fw-semibold">Services</div>
                        </div>
                    </div>
                </div>

                <!-- Carte Publicités -->
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 text-center py-4 stats-card">
                        <div class="card-body">
                            <i class="fas fa-ad fa-2x text-warning mb-3"></i>
                            <div class="display-5 fw-bold text-dark mb-1">{{ $stats['total_publicites'] ?? 0 }}</div>
                            <div class="text-muted text-uppercase small fw-semibold">Publicités</div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .stats-card {
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                    border-radius: 12px;
                }

                .stats-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
                }
            </style>
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

                <!-- Graphique Répartition -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 fw-semibold text-dark">
                                <i class="fas fa-chart-pie text-warning me-2"></i>Répartition par catégorie
                            </h5>
                        </div>
                        <div class="card-body pt-0">
                            <canvas id="categoryChart" height="250"></canvas>
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
                            label: 'Établissements',
                            data: @json($monthlyData['etablissements']),
                            borderColor: '#4e73df',
                            tension: 0.3,
                            fill: false
                        }, {
                            label: 'Services',
                            data: @json($monthlyData['services']),
                            borderColor: '#1cc88a',
                            tension: 0.3,
                            fill: false
                        }, {
                            label: 'Publicités',
                            data: @json($monthlyData['publicites']),
                            borderColor: '#f6c23e',
                            tension: 0.3,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        }
                    }
                });

                // Graphique Circulaire - Répartition
                const categoryCtx = document.getElementById('categoryChart').getContext('2d');
                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($categories->keys()),
                        datasets: [{
                            data: @json($categories->values()),
                            backgroundColor: [
                                '#4e73df', '#1cc88a', '#f6c23e', '#e74a3b',
                                '#36b9cc', '#5a5c69', '#858796', '#b7b7b7'
                            ],
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'right',
                            }
                        }
                    }
                });
            </script>
            <style>
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




        </div>
    @endsection
