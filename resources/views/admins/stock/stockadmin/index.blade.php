@extends('layouts.stockadmin')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-warehouse me-2 text-primary"></i> Gestion des Stocks
            </h4>
            <p class="text-muted mb-0 small">
                Gérez les stocks de vos établissements
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('admin.stocks.dashboard') }}" class="btn btn-info btn-sm">
                <i class="fas fa-chart-pie me-1"></i> Tableau de bord
            </a>
            <a href="{{ route('admin.stocks.export') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-export me-1"></i> Exporter
            </a>
            <a href="{{ route('admin.stocks.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Ajouter un stock
            </a>
        </div>
    </div>

    <!-- ===== STATISTIQUES ===== -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total produits</span>
                            <h3 class="mb-0">{{ number_format($stats['total_products']) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-boxes text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Valeur du stock</span>
                            <h3 class="mb-0 text-success">{{ number_format($stats['total_value'], 0, ',', ' ') }} Fc</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-euro-sign text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Stock bas</span>
                            <h3 class="mb-0 text-warning">{{ $stats['low_stock'] }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">En rupture</span>
                            <h3 class="mb-0 text-danger">{{ $stats['out_of_stock'] }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-times-circle text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== FILTRES ===== -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.stocks.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Rechercher..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="etablissement_id" class="form-select">
                        <option value="">Tous les établissements</option>
                        @foreach($etablissements as $etab)
                            <option value="{{ $etab->id }}" {{ request('etablissement_id') == $etab->id ? 'selected' : '' }}>
                                {{ $etab->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="product_id" class="form-select">
                        <option value="">Tous les produits</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="stock_status" class="form-select">
                        <option value="">Tous les stocks</option>
                        <option value="in" {{ request('stock_status') == 'in' ? 'selected' : '' }}>Normal</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stock bas</option>
                        <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Rupture</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== LISTE ===== -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Établissement</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix achat</th>
                            <th>Prix vente</th>
                            <th>Min</th>
                            <th style="width: 100px">status</th>
                            <th style="width: 200px" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                            <tr>
                                <td>{{ $stock->id }}</td>
                                <td>
                                    <strong>{{ $stock->etablissement->name ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $stock->product->name ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">Code: {{ $stock->product->code ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $stock->quantity <= $stock->minimum_stock ? 'text-danger' : '' }}">
                                        {{ number_format($stock->quantity) }}
                                    </span>
                                </td>
                                <td>{{ number_format($stock->purchase_price, 2, ',', ' ') }} Fc</td>
                                <td>{{ number_format($stock->selling_price, 2, ',', ' ') }} Fc</td>
                                <td>{{ $stock->minimum_stock }}</td>
                                <td>
                                    @php $status = $stock->status; @endphp
                                    <span class="badge {{ $status['badge'] }}">
                                        <i class="fas {{ $status['icon'] }} me-1"></i>
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.stocks.show', $stock) }}" 
                                           class="btn btn-sm btn-outline-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.stocks.edit', $stock) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!-- Bouton Ajouter quantité -->
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#addQuantityModal{{ $stock->id }}"
                                                title="Ajouter quantité">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <!-- Bouton Retirer quantité -->
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#removeQuantityModal{{ $stock->id }}"
                                                title="Retirer quantité"
                                                {{ $stock->quantity <= 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <form action="{{ route('admin.stocks.destroy', $stock) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Supprimer ce stock ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-warehouse fa-3x text-muted mb-3 d-block"></i>
                                    <h5>Aucun stock</h5>
                                    <a href="{{ route('admin.stocks.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Ajouter un stock
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $stocks->firstItem() ?? 0 }} à {{ $stocks->lastItem() ?? 0 }} 
                    sur {{ $stocks->total() }}
                </small>
                {{ $stocks->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- ===== MODALS AJOUT/RETRAIT QUANTITÉ ===== -->
    @foreach($stocks as $stock)
        <!-- Modal Ajouter quantité -->
        <div class="modal fade" id="addQuantityModal{{ $stock->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('admin.stocks.add-quantity', $stock) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-plus-circle text-success me-2"></i>
                                Ajouter quantité - {{ $stock->product->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Stock actuel :</strong> {{ number_format($stock->quantity) }}</p>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Quantité à ajouter <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" required min="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Prix d'achat (optionnel)</label>
                                <input type="number" name="purchase_price" class="form-control" step="0.01" min="0">
                                <small class="text-muted">Laissez vide pour utiliser le prix actuel</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Note</label>
                                <textarea name="note" class="form-control" rows="2" placeholder="Motif de l'ajout..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Ajouter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Retirer quantité -->
        <div class="modal fade" id="removeQuantityModal{{ $stock->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('admin.stocks.remove-quantity', $stock) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-minus-circle text-danger me-2"></i>
                                Retirer quantité - {{ $stock->product->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Stock actuel :</strong> {{ number_format($stock->quantity) }}</p>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Quantité à retirer <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" required min="1" max="{{ $stock->quantity }}">
                                <small class="text-muted">Maximum: {{ number_format($stock->quantity) }}</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Note</label>
                                <textarea name="note" class="form-control" rows="2" placeholder="Motif du retrait..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-minus me-1"></i> Retirer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-fermeture des alertes
        setTimeout(() => {
            document.querySelectorAll('.alert .btn-close').forEach(btn => btn?.click());
        }, 5000);
    });
</script>
@endsection