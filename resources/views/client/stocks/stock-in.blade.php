@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-plus-circle me-2 text-success"></i> Entrée de stock
            </h4>
            <p class="text-muted mb-0 small">
                Approvisionnez votre stock
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('client.stocks.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2 text-success"></i> Formulaire d'entrée
                    </h5>
                </div>
                <div class="card-body">

                    <!-- Sélecteur d'établissement -->
                    @if(isset($etablissements) && $etablissements->count() > 1)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Établissement</label>
                            <form action="{{ route('client.stocks.stock-in') }}" method="GET" class="d-flex gap-2">
                                <select name="etablissement_id" class="form-select" onchange="this.form.submit()">
                                    @foreach($etablissements as $etab)
                                        <option value="{{ $etab->id }}" {{ ($selectedEtablissementId ?? $etab->id) == $etab->id ? 'selected' : '' }}>
                                            {{ $etab->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    @endif

                    <form action="{{ route('client.stocks.stock-in') }}" method="POST">
                        @csrf
                        @if(isset($selectedEtablissementId))
                            <input type="hidden" name="etablissement_id" value="{{ $selectedEtablissementId }}">
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Produit <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                <option value="">Sélectionnez un produit</option>
                                @if(isset($products) && $products->count() > 0)
                                    <optgroup label="Nouveaux produits">
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} ({{ $product->code }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if(isset($existingProducts) && $existingProducts->count() > 0)
                                    <optgroup label="Produits existants">
                                        @foreach($existingProducts as $stock)
                                            <option value="{{ $stock->product_id }}" {{ old('product_id') == $stock->product_id ? 'selected' : '' }}>
                                                {{ $stock->product->name }} ({{ $stock->product->code }}) - Stock: {{ $stock->quantity }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Sélectionnez un produit existant ou un nouveau produit
                            </small>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Quantité <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                                       value="{{ old('quantity') }}" required min="1">
                                @error('quantity')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Prix d'achat <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Fc</span>
                                    <input type="number" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" 
                                           value="{{ old('purchase_price') }}" required step="0.01" min="0">
                                </div>
                                @error('purchase_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Prix de vente <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Fc</span>
                                    <input type="number" name="selling_price" class="form-control @error('selling_price') is-invalid @enderror" 
                                           value="{{ old('selling_price') }}" required step="0.01" min="0">
                                </div>
                                @error('selling_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Note</label>
                                <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="2" placeholder="Motif de l'approvisionnement...">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-2"></i> Ajouter au stock
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i> Réinitialiser
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info bg-opacity-10">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informations</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Entrée de stock</strong>
                            <br>
                            <small class="text-muted">Permet d'ajouter des produits à votre stock</small>
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-calculator text-primary me-2"></i>
                            <strong>Prix moyen pondéré</strong>
                            <br>
                            <small class="text-muted">Le prix d'achat est calculé automatiquement si le produit existe</small>
                        </li>
                        <li>
                            <i class="fas fa-history text-warning me-2"></i>
                            <strong>Traçabilité</strong>
                            <br>
                            <small class="text-muted">Toutes les entrées sont enregistrées dans l'historique</small>
                        </li>
                    </ul>
                </div>
            </div>

            @if(isset($existingProducts) && $existingProducts->count() > 0)
                <div class="card shadow-sm border-success mt-3">
                    <div class="card-header bg-success bg-opacity-10">
                        <h6 class="mb-0"><i class="fas fa-boxes me-2"></i> Produits en stock</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($existingProducts->take(5) as $stock)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $stock->product->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $stock->product->code }}</small>
                                    </div>
                                    <span class="badge bg-primary">{{ number_format($stock->quantity) }}</span>
                                </div>
                            @endforeach
                            @if($existingProducts->count() > 5)
                                <div class="list-group-item text-center text-muted">
                                    <small>+ {{ $existingProducts->count() - 5 }} autres produits</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ===== AUTO-FERMETURE DES ALERTES =====
        setTimeout(() => {
            document.querySelectorAll('.alert .btn-close').forEach(btn => btn?.click());
        }, 5000);

        // ===== VALIDATION EN TEMPS RÉEL =====
        const form = document.querySelector('form');
        const inputs = form?.querySelectorAll('input, select, textarea');

        inputs?.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const feedback = this.closest('.mb-3, .col-md-4, .col-12')?.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.style.display = 'none';
                }
            });
        });

        // ===== AUTO-UPPERCASE DU CODE (si présent) =====
        const codeInput = document.querySelector('input[name="code"]');
        if (codeInput) {
            codeInput.addEventListener('blur', function() {
                this.value = this.value.toUpperCase().trim();
            });
        }

        // ===== CALCUL DE LA MARGE =====
        const purchasePrice = document.querySelector('input[name="purchase_price"]');
        const sellingPrice = document.querySelector('input[name="selling_price"]');

        if (purchasePrice && sellingPrice) {
            const showMargin = () => {
                const buy = parseFloat(purchasePrice.value) || 0;
                const sell = parseFloat(sellingPrice.value) || 0;
                const margin = sell - buy;
                const marginPercent = buy > 0 ? (margin / buy) * 100 : 0;

                let marginElement = document.getElementById('marginDisplay');
                if (!marginElement) {
                    marginElement = document.createElement('div');
                    marginElement.id = 'marginDisplay';
                    marginElement.className = 'mt-2 text-center';
                    sellingPrice.closest('.col-md-4')?.appendChild(marginElement);
                }

                if (sell > 0 && buy > 0) {
                    marginElement.innerHTML = `
                        <span class="badge ${margin >= 0 ? 'bg-success' : 'bg-danger'}">
                            Marge: ${margin.toFixed(2)} Fc (${marginPercent.toFixed(1)}%)
                        </span>
                    `;
                } else {
                    marginElement.innerHTML = '';
                }
            };

            purchasePrice.addEventListener('input', showMargin);
            sellingPrice.addEventListener('input', showMargin);
        }

    });
</script>
@endpush