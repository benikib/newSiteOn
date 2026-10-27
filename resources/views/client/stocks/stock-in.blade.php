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

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    @endif
                    <div id="stockInFeedback" class="alert d-none" role="alert" aria-live="polite"></div>

                    <form id="stockInForm" action="{{ route('client.stocks.stock-in') }}" method="POST">
                        @csrf
                        @if(isset($selectedEtablissementId))
                            <input type="hidden" name="etablissement_id" value="{{ $selectedEtablissementId }}">
                        @endif

                        <div id="stockInItems" class="d-grid gap-3">
                            <div class="stock-in-item border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Produit <span class="stock-in-item-number">1</span></h6>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-stock-in-item d-none" aria-label="Retirer ce produit">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Rechercher un produit</label>
                                        <input type="search" id="stockInProductSearch" class="form-control stock-in-product-search"
                                               placeholder="Nom ou code produit..." autocomplete="off">
                                        <select name="items[0][product_id]" class="form-select stock-in-product-select mt-2" required>
                                            <option value="">Sélectionnez un produit</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                        data-search="{{ $product->name }} {{ $product->code }}"
                                                        data-selling-price="{{ $product->stock->selling_price_original ?? $product->stock->selling_price ?? '' }}"
                                                        data-selling-currency="{{ $product->stock->selling_currency ?? 'CDF' }}">
                                                    {{ $product->name }} ({{ $product->code }}){{ $product->stock ? ' - Stock: ' . number_format($product->stock->quantity) : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted stock-in-product-no-results d-none">Aucun produit correspondant.</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Quantité <span class="text-danger">*</span></label>
                                        <input type="number" name="items[0][quantity]" class="form-control stock-in-quantity" min="1" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Prix d'achat <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="items[0][purchase_price]" class="form-control stock-in-purchase-price" step="0.01" min="0" required>
                                            <select name="items[0][purchase_currency]" class="form-select stock-in-purchase-currency" aria-label="Devise du prix d'achat">
                                                <option value="CDF" selected>CDF</option>
                                                <option value="USD">USD</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Prix de vente</label>
                                        <div class="input-group">
                                            <input type="number" name="items[0][selling_price]" class="form-control stock-in-selling-price" step="0.01" min="0">
                                            <select name="items[0][selling_currency]" class="form-select stock-in-selling-currency" aria-label="Devise du prix de vente">
                                                <option value="CDF" selected>CDF</option>
                                                <option value="USD">USD</option>
                                            </select>
                                        </div>
                                        <small class="text-muted">Le prix par défaut du produit est repris automatiquement; modifiable si besoin.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <template id="stockInItemTemplate">
                            <div class="stock-in-item border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Produit <span class="stock-in-item-number"></span></h6>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-stock-in-item" aria-label="Retirer ce produit">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Rechercher un produit</label>
                                        <input type="search" class="form-control stock-in-product-search"
                                               placeholder="Nom ou code produit..." autocomplete="off">
                                        <select class="form-select stock-in-product-select mt-2" required>
                                            <option value="">Sélectionnez un produit</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                        data-search="{{ $product->name }} {{ $product->code }}"
                                                        data-selling-price="{{ $product->stock->selling_price_original ?? $product->stock->selling_price ?? '' }}"
                                                        data-selling-currency="{{ $product->stock->selling_currency ?? 'CDF' }}">
                                                    {{ $product->name }} ({{ $product->code }}){{ $product->stock ? ' - Stock: ' . number_format($product->stock->quantity) : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted stock-in-product-no-results d-none">Aucun produit correspondant.</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Quantité <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control stock-in-quantity" min="1" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Prix d'achat <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" class="form-control stock-in-purchase-price" step="0.01" min="0" required>
                                            <select class="form-select stock-in-purchase-currency" aria-label="Devise du prix d'achat">
                                                <option value="CDF" selected>CDF</option>
                                                <option value="USD">USD</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Prix de vente</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control stock-in-selling-price" step="0.01" min="0">
                                            <select class="form-select stock-in-selling-currency" aria-label="Devise du prix de vente">
                                                <option value="CDF" selected>CDF</option>
                                                <option value="USD">USD</option>
                                            </select>
                                        </div>
                                        <small class="text-muted">Le prix par défaut du produit est repris automatiquement; modifiable si besoin.</small>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="mt-3">
                            <button type="button" id="addStockInItem" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-1"></i> Ajouter un autre produit
                            </button>
                        </div>
                        <div class="mt-3">
                            <label class="form-label fw-semibold">Note commune (facultatif)</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Motif de l'approvisionnement..."></textarea>
                        </div>
                        <small class="text-muted d-block mt-2">
                            Les prix d'achat sont convertis en CDF pour calculer le coût moyen.
                            Taux USD/CDF : {{ $usdCdfRate ? number_format($usdCdfRate, 2, ',', ' ') : 'non configuré' }}.
                        </small>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" id="submitStockIn" class="btn btn-success px-4">
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

            @if($products->contains(fn ($product) => $product->stock && $product->stock->quantity > 0))
                <div class="card shadow-sm border-success mt-3">
                    <div class="card-header bg-success bg-opacity-10">
                        <h6 class="mb-0"><i class="fas fa-boxes me-2"></i> Produits en stock</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($products->filter(fn ($product) => $product->stock && $product->stock->quantity > 0)->take(5) as $product)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $product->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $product->code }}</small>
                                    </div>
                                    <span class="badge bg-primary">{{ number_format($product->stock->quantity) }}</span>
                                </div>
                            @endforeach
                            @if($products->filter(fn ($product) => $product->stock && $product->stock->quantity > 0)->count() > 5)
                                <div class="list-group-item text-center text-muted">
                                    <small>+ {{ $products->filter(fn ($product) => $product->stock && $product->stock->quantity > 0)->count() - 5 }} autres produits</small>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('stockInForm');
        const itemList = document.getElementById('stockInItems');
        const itemTemplate = document.getElementById('stockInItemTemplate');
        const feedback = document.getElementById('stockInFeedback');
        const submitButton = document.getElementById('submitStockIn');
        let nextItemIndex = 1;

        const updateRows = () => {
            const rows = Array.from(itemList.querySelectorAll('.stock-in-item'));
            rows.forEach((row, index) => {
                row.querySelector('.stock-in-item-number').textContent = index + 1;
                row.querySelector('.remove-stock-in-item').classList.toggle('d-none', rows.length === 1);
            });
        };

        const updateProductOptions = () => {
            const selects = Array.from(itemList.querySelectorAll('.stock-in-product-select'));
            const selectedIds = selects.map(select => select.value).filter(Boolean);

            selects.forEach(select => {
                Array.from(select.options).forEach(option => {
                    option.disabled = Boolean(option.value && option.value !== select.value && selectedIds.includes(option.value));
                });
            });
        };

        const bindRow = row => {
            const search = row.querySelector('.stock-in-product-search');
            const select = row.querySelector('.stock-in-product-select');
            const noResults = row.querySelector('.stock-in-product-no-results');

            search.addEventListener('input', function() {
                const term = this.value.trim().toLocaleLowerCase();
                const options = Array.from(select.options).filter(option => option.value);
                options.forEach(option => {
                    const searchText = (option.dataset.search || option.textContent).toLocaleLowerCase();
                    option.hidden = term !== '' && !searchText.includes(term);
                });
                noResults.classList.toggle('d-none', term === '' || options.some(option => !option.hidden));

                if (select.selectedOptions[0]?.hidden) {
                    select.value = '';
                    updateProductOptions();
                }
            });

            select.addEventListener('change', function() {
                const option = this.selectedOptions[0];
                const sellingPrice = row.querySelector('.stock-in-selling-price');
                const sellingCurrency = row.querySelector('.stock-in-selling-currency');

                if (option?.value) {
                    sellingPrice.value = option.dataset.sellingPrice || '';
                    sellingCurrency.value = option.dataset.sellingCurrency || 'CDF';
                }
                updateProductOptions();
            });
        };

        const addRow = () => {
            const row = itemTemplate.content.firstElementChild.cloneNode(true);
            row.querySelector('.stock-in-product-select').name = `items[${nextItemIndex}][product_id]`;
            row.querySelector('.stock-in-quantity').name = `items[${nextItemIndex}][quantity]`;
            row.querySelector('.stock-in-purchase-price').name = `items[${nextItemIndex}][purchase_price]`;
            row.querySelector('.stock-in-purchase-currency').name = `items[${nextItemIndex}][purchase_currency]`;
            row.querySelector('.stock-in-selling-price').name = `items[${nextItemIndex}][selling_price]`;
            row.querySelector('.stock-in-selling-currency').name = `items[${nextItemIndex}][selling_currency]`;
            nextItemIndex++;
            itemList.appendChild(row);
            bindRow(row);
            updateRows();
            updateProductOptions();
        };

        document.getElementById('addStockInItem').addEventListener('click', addRow);
        itemList.addEventListener('click', function(event) {
            const removeButton = event.target.closest('.remove-stock-in-item');
            if (removeButton && itemList.querySelectorAll('.stock-in-item').length > 1) {
                removeButton.closest('.stock-in-item').remove();
                updateRows();
                updateProductOptions();
            }
        });

        bindRow(itemList.querySelector('.stock-in-item'));

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            feedback.classList.add('d-none');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Enregistrement...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    },
                    body: new FormData(form),
                });
                const result = await response.json();

                if (!response.ok || !result.success) {
                    const messages = result.errors
                        ? Object.values(result.errors).flat()
                        : [result.message || 'Impossible d’enregistrer ces entrées de stock.'];
                    throw new Error(messages.join(' '));
                }

                feedback.className = 'alert alert-success';
                feedback.textContent = result.message;
                feedback.classList.remove('d-none');
                itemList.innerHTML = '';
                nextItemIndex = 0;
                addRow();
                form.querySelector('textarea[name="note"]').value = '';
            } catch (error) {
                feedback.className = 'alert alert-danger';
                feedback.textContent = error.message || 'Une erreur est survenue pendant l’enregistrement.';
                feedback.classList.remove('d-none');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save me-2"></i> Ajouter au stock';
            }
        });
    });
</script>
@endsection