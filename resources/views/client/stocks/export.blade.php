<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des stocks - {{ $etablissement->nom }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #20252b; font-size: 10px; margin: 24px; }
        h1 { font-size: 19px; margin: 0 0 4px; }
        .meta { color: #53606b; margin-bottom: 16px; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd2d8; padding: 6px 5px; text-align: left; vertical-align: top; }
        th { background: #eaf0f4; font-size: 9px; }
        tbody tr:nth-child(even) { background: #f7f9fa; }
        .toolbar { margin-bottom: 14px; }
        @media print { body { margin: 0; } .toolbar { display: none; } }
    </style>
</head>
<body>
    @if($showPrintButton)
        <div class="toolbar"><button type="button" onclick="window.print()">Imprimer</button></div>
    @endif

    <h1>Gestion des stocks</h1>
    <div class="meta">
        <strong>Établissement :</strong> {{ $etablissement->nom }}<br>
        <strong>Date d’impression :</strong> {{ $printedAt->format('d/m/Y H:i') }}<br>
        <strong>Nombre de produits :</strong> {{ $stocks->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th><th>Produit</th><th>Code</th><th>Catégorie</th><th>Quantité</th>
                <th>Min</th><th>Prix achat</th><th>Prix vente</th><th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $stock)
                <tr>
                    <td>{{ $stock->id }}</td>
                    <td>{{ $stock->product->name ?? 'N/A' }}<br>{{ $stock->product->unit->name ?? 'N/A' }}</td>
                    <td>{{ $stock->product->code ?? 'N/A' }}</td>
                    <td>{{ $stock->product->category->nom ?? 'N/A' }}</td>
                    <td>{{ number_format($stock->quantity ?? 0) }}</td>
                    <td>{{ number_format($stock->minimum_stock ?? 0) }}</td>
                    <td>{{ number_format($stock->purchase_price ?? 0, 2, ',', ' ') }} Fc</td>
                    <td>{{ number_format($stock->selling_price ?? 0, 2, ',', ' ') }} Fc</td>
                    <td>
                        @if(($stock->quantity ?? 0) <= 0)
                            Rupture
                        @elseif(($stock->quantity ?? 0) <= ($stock->minimum_stock ?? 0))
                            Stock bas
                        @else
                            Normal
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align: center;">Aucun produit en stock pour ces filtres.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
