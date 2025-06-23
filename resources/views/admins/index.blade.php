@extends('layouts.base')
@section('title', 'Dashboard Admin')
@section('content')

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <!-- Statistiques Utilisateurs -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body p-3 text-center">
                        <div class="icon icon-shape bg-white shadow text-center border-radius-2xl mx-auto">
                            <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                        <h5 class="text-dark font-weight-bolder mb-0 mt-3">
                            {{ $users->count() }}
                        </h5>
                        <span class="text-dark text-sm">Utilisateurs</span>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="dropdown ms-auto">
                                <a href="javascript:;" class="cursor-pointer" id="dropdownUsers1" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fa fa-ellipsis-h text-dark"></i>
                                </a>
                                <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers1">
                                    <li><a class="dropdown-item border-radius-md" href="{{ route('users.index') }}">Voir
                                            tous</a></li>
                                </ul>
                            </div>
                            @php
                                $newUsersLastMonth = $users
                                    ->filter(function ($user) {
                                        return $user->created_at >= now()->subMonth();
                                    })
                                    ->count();
                                $percentage = $users->count() ? round(($newUsersLastMonth / $users->count()) * 100) : 0;
                            @endphp
                            <span class="badge bg-gradient-success ms-2">+{{ $percentage }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques Etablissements -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body p-3 text-center">
                        <div class="icon icon-shape bg-white shadow text-center border-radius-2xl mx-auto">
                            <i class="ni ni-building text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                        <h5 class="text-dark font-weight-bolder mb-0 mt-3">
                            {{ $etablissements->count() }}
                        </h5>
                        <span class="text-dark text-sm">Établissements</span>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="dropdown ms-auto">
                                <a href="javascript:;" class="cursor-pointer" id="dropdownUsers2" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fa fa-ellipsis-h text-dark"></i>
                                </a>
                                <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers2">
                                    <li><a class="dropdown-item border-radius-md"
                                            href="{{ route('etablissements.index') }}">Voir tous</a></li>
                                </ul>
                            </div>
                            @php
                                $newEtabLastMonth = $etablissements
                                    ->filter(function ($etab) {
                                        return $etab->created_at >= now()->subMonth();
                                    })
                                    ->count();
                                $percentage = $etablissements->count()
                                    ? round(($newEtabLastMonth / $etablissements->count()) * 100)
                                    : 0;
                            @endphp
                            <span class="badge bg-gradient-success ms-2">+{{ $percentage }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques Publicités -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body p-3 text-center">
                        <div class="icon icon-shape bg-white shadow text-center border-radius-2xl mx-auto">
                            <i class="ni ni-notification-70 text-dark text-gradient text-lg opacity-10"
                                aria-hidden="true"></i>
                        </div>
                        <h5 class="text-dark font-weight-bolder mb-0 mt-3">
                            {{ $publicites->count() }}
                        </h5>
                        <span class="text-dark text-sm">Publicités</span>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="dropdown ms-auto">
                                <a href="javascript:;" class="cursor-pointer" id="dropdownUsers3" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fa fa-ellipsis-h text-dark"></i>
                                </a>
                                <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers3">
                                    <li><a class="dropdown-item border-radius-md"
                                            href="{{ route('publicites.index') }}">Voir tous</a></li>
                                </ul>
                            </div>
                            @php
                                $activePub = $publicites
                                    ->filter(function ($pub) {
                                        if (!$pub->date || !$pub->dure) {
                                            return false;
                                        }
                                        $date = \Carbon\Carbon::parse($pub->date);
                                        return $date <= now() && $date->copy()->addDays($pub->dure) >= now();
                                    })
                                    ->count();
                                $percentage = $publicites->count()
                                    ? round(($activePub / $publicites->count()) * 100)
                                    : 0;
                            @endphp
                            <span class="badge bg-gradient-info ms-2">{{ $percentage }}% actives</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques Types Etablissements -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body p-3 text-center">
                        <div class="icon icon-shape bg-white shadow text-center border-radius-2xl mx-auto">
                            <i class="ni ni-tag text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                        <h5 class="text-dark font-weight-bolder mb-0 mt-3">
                            {{ $typeEtablissements->count() }}
                        </h5>
                        <span class="text-dark text-sm">Types d'Ets</span>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="dropdown ms-auto">
                                <a href="javascript:;" class="cursor-pointer" id="dropdownUsers4" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fa fa-ellipsis-h text-dark"></i>
                                </a>
                                <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers4">
                                    <li><a class="dropdown-item border-radius-md"
                                            href="{{ route('type_etablissements.index') }}">Voir tous</a></li>
                                </ul>
                            </div>
                            @php
                                $mostCommonType = $typeEtablissements
                                    ->sortByDesc(function ($type) use ($etablissements) {
                                        return $etablissements->where('type_etablissement_id', $type->id)->count();
                                    })
                                    ->first();
                                $count = $mostCommonType
                                    ? $etablissements->where('type_etablissement_id', $mostCommonType->id)->count()
                                    : 0;
                            @endphp
                            <span class="badge bg-gradient-warning ms-2">{{ $count }} top</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Graphique des inscriptions utilisateurs -->
            <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6><i class="fas fa-users me-2"></i> Inscriptions utilisateurs</h6>
                        <p class="text-sm mb-0">
                            <i class="fas fa-arrow-up text-success me-1"></i>
                            <span class="font-weight-bold">{{ $newUsersLastMonth }} nouveaux</span> ce mois-ci
                        </p>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="userRegistrationsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Répartition des établissements par type -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6><i class="fas fa-school me-2"></i> Répartition des établissements</h6>
                        <p class="text-sm">
                            <i class="fas fa-chart-pie me-1"></i> Par type d'établissement
                        </p>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="etablissementTypesChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Derniers établissements ajoutés -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Derniers établissements ajoutés</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nom
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Type</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Adresse</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Date création</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($etablissements->sortByDesc('created_at')->take(5) as $etablissement)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $etablissement->nom }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-xs font-weight-bold">{{ $etablissement->typeEtablissement->nom ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-xs font-weight-bold">{{ $etablissement->adresse }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-xs font-weight-bold">{{ $etablissement->created_at->format('d/m/Y') }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernières publicités ajoutées -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Dernières publicités</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Titre</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Établissement</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Date</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($publicites->sortByDesc('date')->take(5) as $publicite)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $publicite->titre }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-xs font-weight-bold">{{ $publicite->etablissement->nom }}</span>
                                            </td>
                                            <td>
                                                {{ $publicite->date ? \Carbon\Carbon::parse($publicite->date)->locale('fr')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                            </td>
                                            <td>
                                                @php
                                                    $date = \Carbon\Carbon::parse($publicite->date); // Conversion explicite
                                                    $dateExpiration = $date->copy()->addDays($publicite->dure);
                                                @endphp

                                                @if ($date <= now() && $dateExpiration >= now())
                                                    <span class="badge bg-gradient-success">Active</span>
                                                @elseif($date > now())
                                                    <span class="badge bg-gradient-info">Planifiée</span>
                                                @else
                                                    <span class="badge bg-gradient-secondary">Expirée</span>
                                                @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Graphique des inscriptions utilisateurs
        const userRegistrationsCtx = document.getElementById('userRegistrationsChart').getContext('2d');

        // Préparer les données pour les 6 derniers mois
        const months = [];
        const userCounts = [];

        for (let i = 5; i >= 0; i--) {
            const date = new Date();
            date.setMonth(date.getMonth() - i);
            months.push(date.toLocaleString('default', {
                month: 'short'
            }));

            const monthStart = new Date(date.getFullYear(), date.getMonth(), 1);
            const monthEnd = new Date(date.getFullYear(), date.getMonth() + 1, 0);

            const count = {{ Js::from($users) }}.filter(user => {
                const userDate = new Date(user.created_at);
                return userDate >= monthStart && userDate <= monthEnd;
            }).length;

            userCounts.push(count);
        }

        new Chart(userRegistrationsCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Inscriptions',
                    data: userCounts,
                    borderColor: '#3A416F',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: (context) => {
                        const ctx = context.chart.ctx;
                        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(58, 65, 111, 0.5)');
                        gradient.addColorStop(1, 'rgba(58, 65, 111, 0)');
                        return gradient;
                    }
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Graphique des types d'établissements
        const etablissementTypesCtx = document.getElementById('etablissementTypesChart').getContext('2d');

        const typeLabels = {{ Js::from($typeEtablissements->pluck('nom')) }};
        const typeCounts =
            {{ Js::from(
                $typeEtablissements->map(function ($type) use ($etablissements) {
                    return $etablissements->where('type_etablissement_id', $type->id)->count();
                }),
            ) }};

        const backgroundColors = [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)'
        ];

        new Chart(etablissementTypesCtx, {
            type: 'doughnut',
            data: {
                labels: typeLabels,
                datasets: [{
                    data: typeCounts,
                    backgroundColor: backgroundColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                },
                cutout: '70%'
            }
        });
    </script>
@endsection
