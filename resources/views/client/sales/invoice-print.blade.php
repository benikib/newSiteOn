<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
            background: #fff;
        }

        @media print {
            body {
                padding: 10px;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
        }

        /* ===== EN-TÊTE ===== */
        .header {
            text-align: center;
            border-bottom: 3px double #4A6CF7;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #4A6CF7;
            font-size: 28px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .header .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #222;
        }

        .header .company-info {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }

        /* ===== INFOS ===== */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .info-box {
            flex: 1;
        }

        .info-box:last-child {
            text-align: right;
        }

        .info-box h4 {
            font-size: 12px;
            color: #4A6CF7;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-box p {
            font-size: 11px;
            margin: 2px 0;
        }

        .info-box .label {
            color: #888;
            font-size: 10px;
        }

        .info-box .value {
            font-weight: bold;
            font-size: 12px;
        }

        /* ===== TABLEAU ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table thead th {
            background: #4A6CF7;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }

        table tbody tr:last-child td {
            border-bottom: none;
        }

        table tbody tr:hover {
            background: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .fw-bold {
            font-weight: bold;
        }

        /* ===== TOTAUX ===== */
        .totals {
            float: right;
            width: 300px;
            margin-top: 10px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .totals .row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 12px;
        }

        .totals .row.total {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #333;
            padding-top: 8px;
            margin-top: 5px;
        }

        .totals .row.tva-row {
            color: #666;
            font-size: 11px;
        }

        /* ===== BAS DE PAGE ===== */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
        }

        .footer p {
            margin: 2px 0;
        }

        .payment-info {
            margin-top: 15px;
            padding: 10px;
            background: #f0f4ff;
            border-radius: 5px;
            font-size: 11px;
        }

        .payment-info strong {
            color: #4A6CF7;
        }

        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-warning {
            background: #ffc107;
            color: #333;
        }

        .badge-danger {
            background: #dc3545;
            color: white;
        }

        .badge-info {
            background: #17a2b8;
            color: white;
        }

        /* ===== BOUTONS D'IMPRESSION ===== */
        .print-actions {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
        }

        .print-actions button {
            padding: 10px 30px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-print {
            background: #4A6CF7;
            color: white;
        }

        .btn-print:hover {
            background: #3a56c7;
        }

        .btn-close {
            background: #6c757d;
            color: white;
        }

        .btn-close:hover {
            background: #5a6268;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 600px) {
            .info-section {
                flex-direction: column;
                gap: 10px;
            }

            .info-box:last-child {
                text-align: left;
            }

            .totals {
                width: 100%;
            }

            table {
                font-size: 10px;
            }

            table thead th,
            table tbody td {
                padding: 5px;
            }
        }
    </style>
</head>
<body>

    <!-- ===== BOUTONS D'IMPRESSION ===== -->
    <div class="no-print print-actions">
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <button class="btn-close" onclick="window.close()">
            <i class="fas fa-times"></i> Fermer
        </button>
    </div>

    <div class="invoice-container">

        <!-- ===== EN-TÊTE ===== -->
        <div class="header">
            <h1>FACTURE</h1>
            <div class="company-name">{{ $order->etablissement->name ?? 'Mon établissement' }}</div>
            <div class="company-info">
                {{ $order->etablissement->address ?? '' }}
                @if($order->etablissement->phone)
                    - Tél: {{ $order->etablissement->phone }}
                @endif
                @if($order->etablissement->email)
                    - Email: {{ $order->etablissement->email }}
                @endif
            </div>
            <div class="company-info" style="margin-top: 5px;">
                <strong>N° Facture:</strong> {{ $order->order_number }}
                &nbsp;|&nbsp;
                <strong>Date:</strong> {{ $order->created_at->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- ===== INFOS CLIENT ===== -->
        <div class="info-section">
            <div class="info-box">
                <h4>🧑‍💼 Client</h4>
                <p class="value">{{ $order->customer_name ?? 'Client physique' }}</p>
                @if($order->customer_phone)
                    <p><span class="label">Tél:</span> {{ $order->customer_phone }}</p>
                @endif
                @if($order->customer_email)
                    <p><span class="label">Email:</span> {{ $order->customer_email }}</p>
                @endif
                @if($order->customer_address)
                    <p><span class="label">Adresse:</span> {{ $order->customer_address }}</p>
                @endif
            </div>
            <div class="info-box">
                <h4>🏢 Établissement</h4>
                <p class="value">{{ $order->etablissement->name ?? 'N/A' }}</p>
                <p><span class="label">Adresse:</span> {{ $order->etablissement->address ?? 'N/A' }}</p>
                <p><span class="label">Tél:</span> {{ $order->etablissement->phone ?? 'N/A' }}</p>
                @if($order->etablissement->email)
                    <p><span class="label">Email:</span> {{ $order->etablissement->email }}</p>
                @endif
            </div>
        </div>

        <!-- ===== TABLEAU DES PRODUITS ===== -->
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Produit</th>
                    <th style="width: 80px;">Code</th>
                    <th style="width: 50px; text-align: center;">Qté</th>
                    <th style="width: 90px; text-align: right;">Prix HT</th>
                    <th style="width: 60px; text-align: center;">TVA</th>
                    <th style="width: 90px; text-align: right;">Total HT</th>
                    <th style="width: 90px; text-align: right;">Total TTC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->product->code }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->price_ht, 2, ',', ' ') }} Fc</td>
                        <td class="text-center">{{ $item->tva_rate }}%</td>
                        <td class="text-right">{{ number_format($item->subtotal_ht, 2, ',', ' ') }} Fc</td>
                        <td class="text-right">{{ number_format($item->subtotal_ttc, 2, ',', ' ') }} Fc</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ===== TOTAUX ===== -->
        <div class="totals">
            <div class="row">
                <span>Total HT</span>
                <span>{{ number_format($order->total_ht, 2, ',', ' ') }} Fc</span>
            </div>
            <div class="row tva-row">
                <span>TVA</span>
                <span>{{ number_format($order->total_tva, 2, ',', ' ') }} Fc</span>
            </div>
            <div class="row total">
                <span>Total TTC</span>
                <span>{{ number_format($order->total_amount, 2, ',', ' ') }} Fc</span>
            </div>
        </div>

        <div style="clear: both;"></div>

        <!-- ===== NOTES ===== -->
        @if($order->notes)
            <div style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px; border-left: 3px solid #4A6CF7;">
                <strong style="color: #4A6CF7;">📝 Notes:</strong>
                <p style="margin: 5px 0 0 0; font-size: 11px;">{{ $order->notes }}</p>
            </div>
        @endif

        <!-- ===== PAIEMENT ===== -->
        <div class="payment-info">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                <div>
                    <strong>Méthode de paiement:</strong>
                    <span>{{ $order->payment_method_label ?? 'Non spécifié' }}</span>
                </div>
                <div>
                    <strong>Statut:</strong>
                    <span class="badge badge-{{ $order->status == 'completed' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div>
                    <strong>Paiement:</strong>
                    <span class="badge badge-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                        {{ $order->payment_status_label }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ===== PIED DE PAGE ===== -->
        <div class="footer">
            <p>Merci pour votre confiance !</p>
            <p>Cette facture est établie par {{ $order->etablissement->name ?? 'Mon établissement' }}</p>
            <p>
                <strong>N° Facture:</strong> {{ $order->order_number }} &nbsp;|&nbsp;
                <strong>Date d'émission:</strong> {{ now()->format('d/m/Y H:i:s') }}
            </p>
            <p style="margin-top: 5px; font-size: 9px; color: #bbb;">
                Document généré automatiquement - Tous droits réservés
            </p>
        </div>

    </div>

</body>
</html>