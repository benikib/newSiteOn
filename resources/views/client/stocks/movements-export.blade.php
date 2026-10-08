<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mouvements de stock - {{ $etablissement->nom }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #20252b; font-size: 10px; margin: 24px; }
        h1 { font-size: 19px; margin: 0 0 4px; }
        .meta { color: #53606b; margin-bottom: 16px; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd2d8; padding: 6px 5px; text-align: left; vertical-align: top; }
        th { background: #eaf0f4; font-size: 9px; }
        tbody tr:nth-child(even) { background: #f7f9fa; }
        .number { text-align: right; white-space: nowrap; }
        .toolbar { margin-bottom: 14px; }
        @media print { body { margin: 0; } .toolbar { display: none; } }
    </style>
</head>
<body>
    @if($showPrintButton)
        <div class="toolbar"><button type="button" onclick="window.print()">Imprimer</button></div>
    @endif

    <h1>Mouvements de stock</h1>
    <div class="meta">
        <strong>Établissement :</strong> {{ $etablissement->nom }}<br>
        <strong>Période :</strong> {{ $period }}<br>
        <strong>Date d’impression :</strong> {{ $printedAt->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th><th>Type</th><th>Produit</th><th>Code</th>
                <th>Quantité</th><th>Avant</th><th>Après</th><th>Variation</th>
                <th>Note</th><th>Utilisateur</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $movement)
                @php
                    $labels = [
                        'in' => 'Entrée', 'out' => 'Sortie',
                        'adjust_positive' => 'Ajustement +', 'adjust_negative' => 'Ajustement -',
                        'inventory_in' => 'Inventaire +', 'inventory_out' => 'Inventaire -',
                        'stock_in_create' => 'Création stock', 'stock_in_update' => 'Approvisionnement',
                    ];
                @endphp
                <tr>
                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $labels[$movement->type] ?? $movement->type }}</td>
                    <td>{{ $movement->product->name ?? 'N/A' }}</td>
                    <td>{{ $movement->product->code ?? 'N/A' }}</td>
                    <td class="number">{{ number_format($movement->quantity) }}</td>
                    <td class="number">{{ number_format($movement->before) }}</td>
                    <td class="number">{{ number_format($movement->after) }}</td>
                    <td class="number">{{ $movement->after > $movement->before ? '+' : '' }}{{ number_format($movement->after - $movement->before) }}</td>
                    <td>{{ $movement->note ?: '—' }}</td>
                    <td>{{ $movement->user->name ?? 'Système' }}</td>
                </tr>
            @empty
                <tr><td colspan="10" style="text-align: center;">Aucun mouvement pour ces filtres.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>