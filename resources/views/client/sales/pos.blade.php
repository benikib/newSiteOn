@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-cash-register me-2 text-success"></i> Point de vente
            </h4>
            <p class="text-muted mb-0 small">
                Enregistrez vos ventes en physique avec facturation
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('client.sales.history') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-history me-1"></i> Historique
            </a>
        </div>
    </div>

    @if(isset($etablissements) && $etablissements->count() > 1)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('client.sales.pos') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Établissement</label>
                        <select name="etablissement_id" class="form-select" onchange="this.form.submit()">
                            @foreach($etablissements as $etab)
                                <option value="{{ $etab->id }}" {{ ($selectedEtablissementId ?? $etab->id) == $etab->id ? 'selected' : '' }}>
                                    {{ $etab->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="row g-3">
        <!-- Produits -->
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex gap-2">
                        <div class="flex-grow-1">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" 
                                       id="productSearch" 
                                       class="form-control" 
                                       placeholder="Rechercher un produit..."
                                       onkeyup="window.searchProducts(this.value)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="row g-2 p-3" id="productGrid">
                        @foreach($products as $product)
                            <div class="col-xl-3 col-lg-4 col-md-6 product-col" 
                                 onclick="window.openProductModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock->selling_price ?? 0 }}, {{ $product->stock->quantity ?? 0 }}, {{ $product->tva_rate ?? 20 }})">
                                <div class="product-item card h-100 cursor-pointer">
                                    <div class="card-body text-center">
                                        <img src="{{ $product->image_url ?? asset('assets/img/default-product.png') }}" 
                                             alt="{{ $product->name }}" 
                                             class="img-fluid rounded mb-2"
                                             style="height: 60px; object-fit: contain;">
                                        <h6 class="mb-0 text-truncate">{{ $product->name }}</h6>
                                        <small class="text-muted">{{ $product->code }}</small>
                                        <div class="mt-1">
                                            <span class="badge bg-primary">{{ number_format($product->stock->selling_price ?? 0, 2, ',', ' ') }} Fc</span>
                                            <span class="badge {{ ($product->stock->quantity ?? 0) > 10 ? 'bg-success' : 'bg-warning text-dark' }}">
                                                Stock: {{ number_format($product->stock->quantity ?? 0) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Panier -->
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-shopping-cart me-2 text-primary"></i> Panier</h6>
                </div>
                <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                    <div id="cartItems" class="list-group list-group-flush">
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="fas fa-cart-plus fa-2x mb-2 d-block"></i>
                            Aucun produit
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="border-bottom pb-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total HT</span>
                            <span id="cartTotalHT">0,00 Fc</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">TVA</span>
                            <span id="cartTotalTVA">0,00 Fc</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total TTC</span>
                            <span id="cartTotalTTC">0,00 Fc</span>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-12">
                            <select id="customerSelect" class="form-select form-select-sm">
                                <option value="">Client (optionnel)</option>
                                @foreach($customers ?? [] as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <input type="text" id="customerName" class="form-control form-control-sm" placeholder="Nom client">
                        </div>
                        <div class="col-6">
                            <input type="text" id="customerPhone" class="form-control form-control-sm" placeholder="Téléphone">
                        </div>
                        <div class="col-12">
                            <select id="paymentMethod" class="form-select form-select-sm">
                                <option value="cash">💰 Espèces</option>
                                <option value="card">💳 Carte bancaire</option>
                                <option value="transfer">🏦 Virement</option>
                                <option value="other">📱 Autre</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <textarea id="notes" class="form-control form-control-sm" rows="1" placeholder="Notes..."></textarea>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-success" id="validateSaleBtn" onclick="window.validateSale()">
                            <i class="fas fa-check me-2"></i> Valider la vente
                        </button>
                        <button class="btn btn-outline-danger btn-sm" id="clearCartBtn" onclick="window.clearCart()">
                            <i class="fas fa-trash me-1"></i> Vider le panier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal pour la quantité et TVA -->
<div class="modal fade" id="quantityModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter au panier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="productInfo"></p>
                <div class="row g-2">
                    <div class="col-8">
                        <label class="form-label fw-semibold">Quantité</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" onclick="window.updateQuantity(-1)">-</button>
                            <input type="number" id="qtyInput" class="form-control text-center" value="1" min="1">
                            <button class="btn btn-outline-secondary" onclick="window.updateQuantity(1)">+</button>
                        </div>
                        <small class="text-muted" id="qtyMax">Stock disponible: 0</small>
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-semibold">TVA (%)</label>
                        <select id="tvaSelect" class="form-select">
                            <option value="0">0%</option>
                            <option value="5.5">5.5%</option>
                            <option value="10">10%</option>
                            <option value="20" selected>20%</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="window.addToCart()">Ajouter au panier</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de succès -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white">
                    <i class="fas fa-check-circle me-2"></i> Vente enregistrée
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-receipt fa-4x text-success mb-3 d-block"></i>
                <h5>Commande #<span id="orderNumber"></span></h5>
                <p class="text-muted">La vente a été enregistrée avec succès</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="window.printInvoice()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
                <button type="button" class="btn btn-success" onclick="window.pdfInvoice()">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>
            </div>
        </div>
    </div>
</div>


<style>
    .product-item {
        cursor: pointer;
        transition: all 0.2s;
    }
    .product-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-color: #4A6CF7;
    }
    .product-col {
        cursor: pointer;
    }
</style>

<script>
// ===== VARIABLES GLOBALES =====
window.cart = [];
window.currentOrderId = null;
window.selectedProductData = null;

// ===== OUVRE LA MODALE PRODUIT =====
window.openProductModal = function(id, name, price, stock, tva) {
    console.log('🛒 Clic sur produit:', { id, name, price, stock, tva });
    
    if (stock <= 0) {
        alert('Ce produit est en rupture de stock !');
        return;
    }

    window.selectedProductData = { id, name, price, stock, tva };

    document.getElementById('productInfo').innerHTML = `
        <strong>${name}</strong><br>
        Prix HT: ${price.toFixed(2)} Fc<br>
        Stock disponible: ${stock}
    `;
    document.getElementById('qtyInput').value = 1;
    document.getElementById('qtyInput').max = stock;
    document.getElementById('qtyMax').textContent = `Stock disponible: ${stock}`;
    document.getElementById('tvaSelect').value = tva;

    new bootstrap.Modal(document.getElementById('quantityModal')).show();
};

// ===== METTRE À JOUR LA QUANTITÉ =====
window.updateQuantity = function(delta) {
    const input = document.getElementById('qtyInput');
    let val = parseInt(input.value) || 1;
    const max = parseInt(input.max) || 999;
    val = Math.max(1, Math.min(max, val + delta));
    input.value = val;
};

// ===== AJOUTER AU PANIER =====
window.addToCart = function() {
    if (!window.selectedProductData) {
        alert('Aucun produit sélectionné');
        return;
    }

    const quantity = parseInt(document.getElementById('qtyInput').value) || 1;
    const tva = parseFloat(document.getElementById('tvaSelect').value) || 0;

    const product = window.selectedProductData;

    if (quantity > product.stock) {
        alert('Stock insuffisant !');
        return;
    }

    // Vérifier si le produit est déjà dans le panier
    const existing = window.cart.find(item => item.id === product.id);
    if (existing) {
        if (existing.quantity + quantity > product.stock) {
            alert('Stock insuffisant !');
            return;
        }
        existing.quantity += quantity;
    } else {
        window.cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            quantity: quantity,
            stock: product.stock,
            tva: tva
        });
    }

    // Fermer la modale
    bootstrap.Modal.getInstance(document.getElementById('quantityModal')).hide();
    window.selectedProductData = null;

    window.updateCartDisplay();
    console.log('📦 Panier:', window.cart);
};

// ===== METTRE À JOUR L'AFFICHAGE DU PANIER =====
window.updateCartDisplay = function() {
    const container = document.getElementById('cartItems');
    const totalHTSpan = document.getElementById('cartTotalHT');
    const totalTVASpan = document.getElementById('cartTotalTVA');
    const totalTTCSpan = document.getElementById('cartTotalTTC');

    if (window.cart.length === 0) {
        container.innerHTML = `
            <div class="list-group-item text-center text-muted py-4">
                <i class="fas fa-cart-plus fa-2x mb-2 d-block"></i>
                Aucun produit
            </div>
        `;
        totalHTSpan.textContent = '0,00 Fc';
        totalTVASpan.textContent = '0,00 Fc';
        totalTTCSpan.textContent = '0,00 Fc';
        return;
    }

    let html = '';
    let totalHT = 0;
    let totalTVA = 0;
    let totalTTC = 0;

    window.cart.forEach((item, index) => {
        const subtotalHT = item.price * item.quantity;
        const tvaAmount = subtotalHT * (item.tva / 100);
        const subtotalTTC = subtotalHT + tvaAmount;

        totalHT += subtotalHT;
        totalTVA += tvaAmount;
        totalTTC += subtotalTTC;

        html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${item.name}</strong>
                        <br>
                        <small class="text-muted">${item.quantity} x ${item.price.toFixed(2)} Fc</small>
                    </div>
                    <div>
                        <span class="fw-bold">${subtotalTTC.toFixed(2)} Fc</span>
                        <button class="btn btn-sm btn-outline-danger ms-2" onclick="window.removeItem(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    totalHTSpan.textContent = totalHT.toFixed(2) + ' Fc';
    totalTVASpan.textContent = totalTVA.toFixed(2) + ' Fc';
    totalTTCSpan.textContent = totalTTC.toFixed(2) + ' Fc';
};

// ===== SUPPRIMER UN ARTICLE =====
window.removeItem = function(index) {
    window.cart.splice(index, 1);
    window.updateCartDisplay();
};

// ===== VIDER LE PANIER =====
window.clearCart = function() {
    if (window.cart.length === 0) return;
    if (confirm('Vider le panier ?')) {
        window.cart = [];
        window.updateCartDisplay();
    }
};

// ===== RECHERCHE DE PRODUITS =====
window.searchTimeout = null;

window.searchProducts = function(search) {
    clearTimeout(window.searchTimeout);
    const grid = document.getElementById('productGrid');

    if (search.length < 2) {
        if (search.length === 0) {
            location.reload();
        }
        return;
    }

    window.searchTimeout = setTimeout(() => {
        fetch(`{{ route('client.sales.search') }}?search=${search}&etablissement_id={{ $selectedEtablissementId ?? '' }}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    grid.innerHTML = '';
                    
                    if (data.data.length === 0) {
                        grid.innerHTML = `
                            <div class="col-12 text-center py-4">
                                <i class="fas fa-search fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">Aucun produit trouvé</p>
                            </div>
                        `;
                    } else {
                        data.data.forEach(product => {
                            grid.innerHTML += `
                                <div class="col-xl-3 col-lg-4 col-md-6 product-col" 
                                     onclick="window.openProductModal(${product.id}, '${product.name}', ${product.stock.selling_price || 0}, ${product.stock.quantity || 0}, ${product.tva_rate || 20})">
                                    <div class="product-item card h-100 cursor-pointer">
                                        <div class="card-body text-center">
                                            <img src="${product.image_url || '{{ asset("assets/img/default-product.png") }}'}" 
                                                 alt="${product.name}" 
                                                 class="img-fluid rounded mb-2"
                                                 style="height: 60px; object-fit: contain;">
                                            <h6 class="mb-0 text-truncate">${product.name}</h6>
                                            <small class="text-muted">${product.code}</small>
                                            <div class="mt-1">
                                                <span class="badge bg-primary">${(product.stock.selling_price || 0).toFixed(2)} Fc</span>
                                                <span class="badge ${(product.stock.quantity || 0) > 10 ? 'bg-success' : 'bg-warning text-dark'}">
                                                    Stock: ${product.stock.quantity || 0}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Erreur de recherche:', error);
            });
    }, 500);
};

// ===== VALIDER LA VENTE =====
window.validateSale = function() {
    if (window.cart.length === 0) {
        alert('Le panier est vide');
        return;
    }

    const items = window.cart.map(item => ({
        product_id: parseInt(item.id),
        quantity: item.quantity,
        price: item.price,
        tva_rate: item.tva
    }));

    const data = {
        etablissement_id: {{ $selectedEtablissementId ?? 'null' }},
        customer_id: document.getElementById('customerSelect').value || null,
        customer_name: document.getElementById('customerName').value || 'Client physique',
        customer_phone: document.getElementById('customerPhone').value,
        payment_method: document.getElementById('paymentMethod').value,
        notes: document.getElementById('notes')?.value || '',
        items: items
    };

    console.log('📤 Envoi de la commande:', data);

    const btn = document.getElementById('validateSaleBtn');
    
    if (!btn) {
        console.error('❌ Bouton validateSaleBtn non trouvé !');
        alert('Erreur: bouton de validation non trouvé');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Traitement...';

    fetch('{{ route("client.sales.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        console.log('📥 Réponse:', data);
        if (data.success) {
            window.currentOrderId = data.order_id;
            document.getElementById('orderNumber').textContent = data.order_number;
            
            new bootstrap.Modal(document.getElementById('successModal')).show();
            
            // Réinitialiser
            window.cart = [];
            window.updateCartDisplay();
            document.getElementById('customerSelect').value = '';
            document.getElementById('customerName').value = '';
            document.getElementById('customerPhone').value = '';
            document.getElementById('notes').value = '';
        } else {
            alert(data.message || 'Erreur lors de l\'enregistrement');
        }
    })
    .catch(error => {
        console.error('❌ Erreur:', error);
        alert('Erreur: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check me-2"></i> Valider la vente';
    });
};

// ===== IMPRIMER LA FACTURE =====
window.printInvoice = function() {
    if (window.currentOrderId) {
        window.open('/client/sales/print/' + window.currentOrderId, '_blank');
    } else {
        alert('Aucune commande à imprimer');
    }
};

// ===== PDF FACTURE =====
window.pdfInvoice = function() {
    if (window.currentOrderId) {
        window.open('/client/sales/pdf/' + window.currentOrderId, '_blank');
    } else {
        alert('Aucune commande à générer en PDF');
    }
};

// ===== INITIALISATION =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ POS chargé !');
    console.log('📦 Nombre de produits:', document.querySelectorAll('.product-col').length);
    
    // Vérifier que le bouton existe
    const btn = document.getElementById('validateSaleBtn');
    if (btn) {
        console.log('✅ Bouton validateSaleBtn trouvé');
    } else {
        console.error('❌ Bouton validateSaleBtn non trouvé !');
    }
});

// ===== RECHERCHE EN TEMPS RÉEL =====
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            window.searchProducts(this.value);
        });
    }
});
</script>
@endsection