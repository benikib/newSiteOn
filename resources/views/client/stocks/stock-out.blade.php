@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-minus-circle me-2 text-danger"></i> Sortie de stock
            </h4>
            <p class="text-muted mb-0 small">
                Retirez des produits de votre stock
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
                        <i class="fas fa-box me-2 text-danger"></i> Formulaire de sortie
                    </h5>
                </div>
                <div class="card-body">

                    @if(isset($etablissements) && $etablissements->count() > 1)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Établissement</label>
                            <form action="{{ route('client.stocks.stock-out') }}" method="GET" class="d-flex gap-2">
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

                    <form action="{{ route('client.stocks.stock-out') }}" method="POST">
                        @csrf
                        @if(isset($selectedEtablissementId))
                            <input type="hidden" name="etablissement_id" value="{{ $selectedEtablissementId }}">
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Produit <span class="text-danger">*</span></label>
                            <select name="stock_id" class="form-select @error('stock_id') is-invalid @enderror" required>
                                <option value="">Sélectionnez un produit</option>
                                @foreach($stocks ?? [] as $stock)
                                    <option value="{{ $stock->id }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}
                                            data-max="{{ $stock->quantity }}">
                                        {{ $stock->product->name }} ({{ $stock->product->code }}) - 
                                        Stock: {{ number_format($stock->quantity) }} {{ $stock->product->unit->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('stock_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @if(isset($stocks) && $stocks->count() == 0)
                                <div class="alert alert-warning mt-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Aucun produit en stock. Faites une <a href="{{ route('client.stocks.stock-in') }}" class="alert-link">entrée de stock</a> d'abord.
                                </div>
                            @endif
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Quantité à retirer <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                                       value="{{ old('quantity') }}" required min="1" id="quantity">
                                @error('quantity')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="maxQuantity">Maximum: 0</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Note</label>
                                <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="3" placeholder="Motif de la sortie...">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-danger px-4" {{ isset($stocks) && $stocks->count() == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-minus me-2"></i> Retirer du stock
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
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger bg-opacity-10">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Attention</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Vérifiez les quantités</strong>
                            <br>
                            <small class="text-muted">Assurez-vous d'avoir suffisamment de stock</small>
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-clipboard-list text-primary me-2"></i>
                            <strong>Justifiez la sortie</strong>
                            <br>
                            <small class="text-muted">Ajoutez une note pour tracer l'historique</small>
                        </li>
                        <li>
                            <i class="fas fa-history text-warning me-2"></i>
                            <strong>Traçabilité</strong>
                            <br>
                            <small class="text-muted">Toutes les sorties sont enregistrées</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stockSelect = document.querySelector('select[name="stock_id"]');
        const quantityInput = document.getElementById('quantity');
        const maxQuantitySpan = document.getElementById('maxQuantity');

        if (stockSelect) {
            stockSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const max = parseInt(selectedOption.dataset.max) || 0;
                quantityInput.max = max;
                maxQuantitySpan.textContent = 'Maximum: ' + max;
            });

            // Déclencher le changement initial
            stockSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush