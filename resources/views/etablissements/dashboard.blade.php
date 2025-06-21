@extends('layouts.main')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800 fw-bold">Dashboard Établissement</h1>
                <p class="text-muted mb-0">Gérez vos établissements et suivez vos performances</p>
            </div>
            <button class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#addEtablissementModal">
                <i class="fas fa-plus me-2"></i>Ajouter Établissement
            </button>
        </div>

        <!-- Cartes de Statistiques -->
        <div class="row g-4 mb-4">
            <!-- Carte Nombre d'Établissements -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 stats-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-muted text-uppercase small fw-semibold mb-1">
                                    Établissements</div>
                                <div class="h3 mb-0 fw-bold text-primary">{{ $stats['total_etablissements'] ?? 0 }}</div>
                                <div class="text-success small">
                                    <i class="fas fa-arrow-up me-1"></i>{{ $stats['etablissements_recents'] ?? 0 }} ce mois
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-building fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte Promotions Actives -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 stats-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-muted text-uppercase small fw-semibold mb-1">
                                    Promotions Actives</div>
                                <div class="h3 mb-0 fw-bold text-success">{{ $stats['promotions_actives'] ?? 0 }}</div>
                                <div class="text-success small">
                                    <i class="fas fa-arrow-up me-1"></i>{{ $stats['promotions_recents'] ?? 0 }} ce mois
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-tag fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte Services -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 stats-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-muted text-uppercase small fw-semibold mb-1">
                                    Services</div>
                                <div class="h3 mb-0 fw-bold text-info">{{ $stats['total_services'] ?? 0 }}</div>
                                <div class="text-info small">
                                    <i class="fas fa-arrow-up me-1"></i>{{ $stats['services_recents'] ?? 0 }} ce mois
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-cogs fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte Publicités -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 stats-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-muted text-uppercase small fw-semibold mb-1">
                                    Publicités</div>
                                <div class="h3 mb-0 fw-bold text-warning">{{ $stats['total_publicites'] ?? 0 }}</div>
                                <div class="text-warning small">
                                    <i class="fas fa-arrow-up me-1"></i>{{ $stats['publicites_recents'] ?? 0 }} ce mois
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-ad fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques par Type d'Établissement -->
        @if (isset($statsParType) && $statsParType->count() > 0)
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 fw-bold">Répartition par Type d'Établissement</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach ($statsParType as $type)
                                    <div class="col-md-4 col-lg-3">
                                        <div class="d-flex align-items-center p-3 bg-light rounded">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="fas fa-building text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $type->nom }}</div>
                                                <div class="text-muted small">{{ $type->etablissements_count }}
                                                    établissements</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        

        <!-- Modal Ajout Établissement -->
        {{-- @include('etablissements.modals.create') --}}
    </div>


    <script>
        // Initialisation DataTable
        $(document).ready(function() {
            $('#dataTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                },
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }]
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

        // Animation des cartes au survol
        document.querySelectorAll('.stats-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>

    <style>
        /* Styles personnalisés */
        .stats-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
        }

        .card {
            border-radius: 12px;
            overflow: hidden;
        }

        .badge {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
        }

        .btn-group .btn {
            border-radius: 6px;
            margin: 0 2px;
        }

        .table th {
            font-weight: 600;
            color: #495057;
        }

        .table td {
            vertical-align: middle;
        }

        .input-group-text {
            border-radius: 8px 0 0 8px;
        }

        .form-control {
            border-radius: 0 8px 8px 0;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        .btn-lg {
            padding: 12px 24px;
        }

        /* Animation pour les icônes */
        .fa-star,
        .fa-star-o {
            transition: color 0.2s ease;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-card {
                margin-bottom: 1rem;
            }

            .btn-group .btn {
                margin-bottom: 0.25rem;
            }

            .input-group {
                width: 100% !important;
                margin-bottom: 1rem;
            }

            .card-header .d-flex {
                flex-direction: column;
                gap: 1rem;
            }
        }

        /* Effet de focus amélioré */
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            border-color: #86b7fe;
        }

        /* Animation de chargement */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Styles pour les états vides */
        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
        }

        .empty-state i {
            opacity: 0.3;
        }
    </style>
@endsection
