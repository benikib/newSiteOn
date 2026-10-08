<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des abonnements - {{ $etablissement->nom }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; font-size: 12px; margin: 24px; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        .muted { color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #bbb; padding: 7px; text-align: left; vertical-align: top; }
        th { background: #edf1f4; }
        .toolbar { margin-bottom: 16px; }
        @media print { body { margin: 0; } .toolbar { display: none; } }
    </style>
</head>
<body>
    @if($printButton)
        <div class="toolbar"><button type="button" onclick="window.print()">Imprimer</button></div>
    @endif
    <h1>Historique des abonnements</h1>
    <div class="muted">{{ $etablissement->nom }} · {{ now()->format('d/m/Y H:i') }}</div>
    <table>
        <thead><tr><th>Créé le</th><th>Période</th><th>Opération</th><th>Statut</th><th>Motif</th><th>Montant</th><th>Créé par</th></tr></thead>
        <tbody>
            @forelse($abonnements as $abonnement)
                <tr>
                    <td>{{ $abonnement->created_at?->format('d/m/Y H:i') }}</td>
                    <td>{{ $abonnement->date_debut->format('d/m/Y') }} au {{ $abonnement->date_fin->format('d/m/Y') }}</td>
                    <td>{{ $abonnement->type_operation_label }}</td>
                    <td>{{ $abonnement->statut_label }}</td>
                    <td>{{ $abonnement->motif }}</td>
                    <td>{{ $abonnement->montant_paye !== null ? number_format($abonnement->montant_paye, 2, ',', ' ') . ' ' . $abonnement->devise : '—' }}</td>
                    <td>{{ $abonnement->createur->name ?? 'Système' }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Aucun abonnement enregistré.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>