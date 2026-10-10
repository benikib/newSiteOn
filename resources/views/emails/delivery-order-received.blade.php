<h1>Nouvelle commande à livrer</h1>
<p>Une demande vient d’être envoyée pour <strong>{{ $deliveryOrder->etablissement->nom }}</strong>.</p>

<h2>Client</h2>
<p>{{ $deliveryOrder->client_name }}<br>{{ $deliveryOrder->client_phone }}<br>{{ $deliveryOrder->delivery_address }}</p>

<h2>Articles</h2>
<ul>
    @foreach($deliveryOrder->items as $item)
        <li>{{ $item->product_name }} ({{ $item->product_code }}) × {{ $item->quantity }}</li>
    @endforeach
</ul>

<p>Total : {{ number_format($deliveryOrder->total_amount, 2, ',', ' ') }} CDF</p>
<p>Paiement choisi : {{ $deliveryOrder->payment_plan === 'one_time' ? 'En une fois' : 'En deux fois' }}</p>
@if($deliveryOrder->payment_plan === 'two_installments')
    <p>Acompte souhaité : {{ number_format($deliveryOrder->deposit_amount, 2, ',', ' ') }} CDF</p>
@endif
<p>La commande est en attente de traitement.</p>