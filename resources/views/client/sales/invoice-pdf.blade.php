<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4A6CF7;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #4A6CF7;
            margin: 0;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            width: 48%;
        }
        .info-box h4 {
            margin: 0 0 8px 0;
            color: #4A6CF7;
            font-size: 14px;
        }
        .info-box p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th {
            background: #4A6CF7;
            color: white;
            padding: 8px;
            text-align: left;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        table .text-right {
            text-align: right;
        }
        .totals {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .totals .row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .totals .row.total {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #333;
            border-bottom: none;
            padding-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            color: #999;
            font-size: 11px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-warning {
            background: #ffc107;
            color: #333;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>FACTURE</h1>
        <p>{{ $order->etablissement->name ?? 'Mon établissement' }}</p>
        <p>{{ $order->etablissement->address ?? '' }} - {{ $order->etablissement->phone ?? '' }}</p>
        <p><strong>N° Facture:</strong> {{ $order->order_number }}</p>
        <p><strong>Date:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info">
        <div class="info-box">
            <h4>🧑‍💼 Client</h4>
            <p><strong>{{ $order->customer_name }}</strong></p>
            @if($order->customer_phone)
                <p>Tél: {{ $order->customer_phone }}</p>
            @endif
            @if($order->customer_email)
                <p>Email: {{ $order->customer_email }}</p>
            @endif
            @if($order->customer_address)
                <p>Adresse: {{ $order->customer_address }}</p>
            @endif
        </div>
        <div class="info-box">
            <h4>🏢 Établissement</h4>
            <p><strong>{{ $order->etablissement->name ?? 'N/A' }}</strong></p>
            <p>Adresse: {{ $order->etablissement->address ?? 'N/A' }}</p>
            <p>Tél: {{ $order->etablissement->phone ?? 'N/A' }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Produit</th>
                <th>Code</th>
                <th>Qté</th>
                <th>Prix HT</th>
                <th>TVA %</th>
                <th>Total HT</th>
                <th>Total TTC</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->product->code }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price_ht, 2, ',', ' ') }} Fc</td>
                    <td class="text-right">{{ $item->tva_rate }}%</td>
                    <td class="text-right">{{ number_format($item->subtotal_ht, 2, ',', ' ') }} Fc</td>
                    <td class="text-right">{{ number_format($item->subtotal_ttc, 2, ',', ' ') }} Fc</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>Total HT</span>
            <span>{{ number_format($order->total_ht, 2, ',', ' ') }} Fc</span>
        </div>
        <div class="row">
            <span>TVA ({{ $order->total_tva > 0 ? '20%' : '0%' }})</span>
            <span>{{ number_format($order->total_tva, 2, ',', ' ') }} Fc</span>
        </div>
        <div class="row total">
            <span>Total TTC</span>
            <span>{{ number_format($order->total_amount, 2, ',', ' ') }} Fc</span>
        </div>
    </div>

    @if($order->notes)
        <div style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
            <strong>Notes:</strong>
            <p style="margin: 5px 0 0 0;">{{ $order->notes }}</p>
        </div>
    @endif

    <div style="margin-top: 20px;">
        <p><strong>Méthode de paiement:</strong> {{ $order->payment_method_label }}</p>
        <p><strong>Statut:</strong> 
            <span class="badge badge-{{ $order->status == 'completed' ? 'success' : 'warning' }}">
                {{ $order->status_label }}
            </span>
        </p>
    </div>

    <div class="footer">
        <p>Merci pour votre confiance !</p>
        <p>Cette facture est établie par {{ $order->etablissement->name ?? 'Mon établissement' }}</p>
        <p>{{ date('d/m/Y H:i:s') }}</p>
    </div>

</body>
</html>