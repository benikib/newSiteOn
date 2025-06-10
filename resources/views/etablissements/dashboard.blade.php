@extends('layouts.main')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard Établissement</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEtablissementModal">
                <i class="fas fa-plus me-2"></i>Ajouter Établissement
            </button>
        </div>

        <!-- Cartes de Statistiques -->
        <div class="row">
            <!-- Carte Nombre d'Établissements -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Établissements</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_etablissements'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-building fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte Promotions Actives -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Promotions Actives</div>
                                {{-- <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['promotions_actives'] }}</div> --}}
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tag fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte Réservations -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Réservations (30j)</div>
                                {{-- <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['reservations'] }}</div> --}}
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte Revenus -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Revenus (30j)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{-- {{ number_format($stats['revenus'], 2) }} €</div> --}}
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des Établissements -->
            
            </div>
        </div>

        <!-- Modal Ajout Établissement -->
        {{-- @include('etablissements.modals.create') --}}
    @endsection

    @section('scripts')
        <script>
            // Initialisation DataTable
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                    }
                });
            });

            // Recherche en temps réel
            document.getElementById('searchInput').addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('#dataTable tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });
        </script>
        <style>
            /* styles.css */
            .card {
                transition: all 0.3s ease;
                border-radius: 10px;
            }

            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }

            .badge {
                font-weight: 500;
                padding: 5px 10px;
            }

            #dataTable_filter {
                display: none;
                /* On utilise notre propre champ de recherche */
            }

            @media (max-width: 768px) {
                .stats-card {
                    margin-bottom: 20px;
                }

                .btn-group .btn {
                    margin-bottom: 5px;
                }
            }
        </style>
    @endsection
