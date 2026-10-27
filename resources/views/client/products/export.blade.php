<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des produits - {{ $etablissement->nom }}</title>
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

    <h1>Liste des produits</h1>
    <div class="meta">
        <strong>Établissement :</strong> {{ $etablissement->nom }}<br>
        <strong>Date d’impression :</strong> {{ $printedAt->format('d/m/Y H:i') }}<br>
        <strong>Nombre de produits :</strong> {{ $products->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th><th>Nom</th><th>Code</th><th>Catégorie</th><th>Unité</th>
                <th>Prix de vente</th><th>Stock</th><th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->code }}@if($product->barcode)<br>{{ $product->barcode }}@endif</td>
                    <td>{{ $product->category->nom ?? 'N/A' }}</td>
                    <td>{{ $product->unit->name ?? 'N/A' }}</td>
                    <td>{{ number_format($product->stock->selling_price_original ?? $product->stock->selling_price ?? 0, 2, ',', ' ') }} {{ $product->stock->selling_currency ?? 'CDF' }}</td>
                    <td>{{ number_format($product->stock->quantity ?? 0) }}</td>
                    <td>{{ $product->status ? 'Actif' : 'Inactif' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align: center;">Aucun produit pour ces filtres.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
