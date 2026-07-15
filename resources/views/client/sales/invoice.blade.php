@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-file-invoice me-2 text-primary"></i> Facture
            </h4>
            <p class="text-muted mb-0 small">
                N° {{ $order->order_number }} - {{ $order->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('client.sales.print', $order->id) }}" class="btn btn-primary btn-sm" target="_blank">
                <i class="fas fa-print me-1"></i> Imprimer
            </a>
            <a href="{{ route('client.sales.pdf', $order->id) }}" class="btn btn-success btn-sm" target="_blank">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('client.sales.history') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <!-- ===== EN-TÊTE FACTURE ===== -->
                    <div class="text-center border-bottom pb-3 mb-4">
                        <h2 class="fw-bold text-primary">FACTURE</h2>
                        <h5 class="mb-0">{{ $order->etablissement->name ?? 'Mon établissement' }}</h5>
                        <p class="text-muted mb-0 small">
                            {{ $order->etablissement->address ?? '' }}
                            @if($order->etablissement->phone)
                                - Tél: {{ $order->etablissement->phone }}
                            @endif
                        </p>
                        <p class="mb-0">
                            <strong>N° Facture:</strong> {{ $order->order_number }}
                            &nbsp;|&nbsp;
                            <strong>Date:</strong> {{ $order->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <!-- ===== INFOS CLIENT ===== -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded-3">
                                <h6 class="fw-bold text-primary mb-2">
                                    <i class="fas fa-user me-2"></i> Client
                                </h6>
                                <p class="mb-1 fw-semibold">{{ $order->customer_name ?? 'Client physique' }}</p>
                                @if($order->customer_phone)
                                    <p class="mb-1"><span class="text-muted">Tél:</span> {{ $order->customer_phone }}</p>
                                @endif
                                @if($order->customer_email)
                                    <p class="mb-1"><span class="text-muted">Email:</span> {{ $order->customer_email }}</p>
                                @endif
                                @if($order->customer_address)
                                    <p class="mb-0"><span class="text-muted">Adresse:</span> {{ $order->customer_address }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded-3">
                                <h6 class="fw-bold text-primary mb-2">
                                    <i class="fas fa-store me-2"></i> Établissement
                                </h6>
                                <p class="mb-1 fw-semibold">{{ $order->etablissement->name ?? 'N/A' }}</p>
                                <p class="mb-1"><span class="text-muted">Adresse:</span> {{ $order->etablissement->address ?? 'N/A' }}</p>
                                <p class="mb-0"><span class="text-muted">Tél:</span> {{ $order->etablissement->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- ===== TABLEAU DES PRODUITS ===== -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Produit</th>
                                    <th style="width: 80px;">Code</th>
                                    <th style="width: 50px; text-align: center;">Qté</th>
                                    <th style="width: 100px; text-align: right;">Prix HT</th>
                                    <th style="width: 60px; text-align: center;">TVA</th>
                                    <th style="width: 100px; text-align: right;">Total HT</th>
                                    <th style="width: 100px; text-align: right;">Total TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->product->name }}</strong>
                                            @if($item->product->description)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($item->product->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->product->code }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-right">{{ number_format($item->price_ht, 2, ',', ' ') }} Fc</td>
                                        <td class="text-center">{{ $item->tva_rate }}%</td>
                                        <td class="text-right">{{ number_format($item->subtotal_ht, 2, ',', ' ') }} Fc</td>
                                        <td class="text-right fw-bold">{{ number_format($item->subtotal_ttc, 2, ',', ' ') }} Fc</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="6" class="text-end">Total HT</td>
                                    <td class="text-right">{{ number_format($order->total_ht, 2, ',', ' ') }} Fc</td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end">TVA</td>
                                    <td class="text-right">{{ number_format($order->total_tva, 2, ',', ' ') }} Fc</td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr class="table-success">
                                    <td colspan="6" class="text-end fs-5">Total TTC</td>
                                    <td class="text-right fs-5"></td>
                                    <td class="text-right fs-5">{{ number_format($order->total_amount, 2, ',', ' ') }} Fc</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- ===== PAIEMENT ET STATUT ===== -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <h6 class="fw-bold text-primary mb-2">
                                    <i class="fas fa-credit-card me-2"></i> Paiement
                                </h6>
                                <p class="mb-0">
                                    <span class="text-muted">Méthode:</span>
                                    <span class="fw-semibold">{{ $order->payment_method_label ?? 'Non spécifié' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <h6 class="fw-bold text-primary mb-2">
                                    <i class="fas fa-info-circle me-2"></i> Statut
                                </h6>
                                <p class="mb-0">
                                    <span class="badge {{ $order->status_badge }} fs-6">
                                        {{ $order->status_label }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <h6 class="fw-bold text-primary mb-2">
                                    <i class="fas fa-check-circle me-2"></i> Paiement
                                </h6>
                                <p class="mb-0">
                                    <span class="badge {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning text-dark' }} fs-6">
                                        {{ $order->payment_status_label }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ===== NOTES ===== -->
                    @if($order->notes)
                        <div class="mt-4 p-3 bg-light rounded-3">
                            <h6 class="fw-bold text-primary mb-2">
                                <i class="fas fa-pencil-alt me-2"></i> Notes
                            </h6>
                            <p class="mb-0">{{ $order->notes }}</p>
                        </div>
                    @endif

                    <!-- ===== PIED DE PAGE ===== -->
                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="text-muted small mb-0">
                            Merci pour votre confiance !
                        </p>
                        <p class="text-muted small mb-0">
                            Cette facture est générée automatiquement par {{ config('app.name') }}
                        </p>
                        <p class="text-muted small mb-0">
                            {{ now()->format('d/m/Y H:i:s') }}
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>



<style>
    .table-primary {
        --bs-table-bg: #4A6CF7;
        --bs-table-color: #fff;
    }
    .table-success {
        --bs-table-bg: #d1e7dd;
        --bs-table-color: #0f5132;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .badge.fs-6 {
        font-size: 1rem !important;
        padding: 0.4rem 0.8rem;
    }
    @media print {
        .btn, .btn-sm, .d-flex.gap-2 {
            display: none !important;
        }
        .card {
            box-shadow: none !important;
            border: none !important;
        }
        .table-bordered {
            border: 1px solid #dee2e6 !important;
        }
    }
</style>
@endsection