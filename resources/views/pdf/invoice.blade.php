<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: Arial, sans-serif; color: #0F172A; font-size: 13px; }
    .header { display: flex; justify-content: space-between; margin-bottom: 2rem; }
    h1 { color: #4F46E5; font-size: 1.4rem; margin: 0; }
    .invoice-info { text-align: right; color: #475569; font-size: .85rem; }
    table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
    th { background: #4F46E5; color: white; padding: .6rem .75rem; text-align: left; }
    td { padding: .6rem .75rem; border-bottom: 1px solid #E8ECF0; }
    .total-row { font-weight: 800; font-size: 1rem; color: #4F46E5; }
    .footer { margin-top: 3rem; padding-top: 1rem; border-top: 1px solid #E8ECF0; color: #94A3B8; font-size: .8rem; text-align: center; }
  </style>
</head>
<body>
  <div class="header">
    <div>
      <h1>Tech Shop</h1>
      <div style="color:#475569; font-size:.85rem;">+216 54 736 722</div>
    </div>
    <div class="invoice-info">
      <strong>FACTURE #{{ $order->id }}</strong><br>
      Date : {{ now()->format('d/m/Y') }}<br>
      Client : {{ $order->user?->name ?? $order->guest_name }}<br>
      Statut : Confirmée
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Article</th>
        <th>Quantité</th>
        <th>Prix unitaire</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->articles as $article)
      <tr>
        <td>{{ $article->name }}</td>
        <td>{{ $article->pivot->quantity }}</td>
        <td>{{ number_format($article->pivot->unit_price, 2) }} TND</td>
        <td>{{ number_format($article->pivot->unit_price * $article->pivot->quantity, 2) }} TND</td>
      </tr>
      @endforeach
      <tr class="total-row">
        <td colspan="3">Livraison</td>
        <td>Gratuite</td>
      </tr>
      <tr>
        <td colspan="3">Mode de paiement</td>
        <td>
          @if($order->payment_method === 'cash')
            💵 Paiement à la livraison
          @else
            💳 Carte bancaire
          @endif
        </td>
      </tr>
      <tr class="total-row">
        <td colspan="3">Total</td>
        <td>{{ number_format($order->global_price, 2) }} TND</td>
      </tr>
    </tbody>
  </table>

  <div class="footer">
    Merci pour votre confiance — Tech Shop © {{ now()->year }}
  </div>
</body>
</html>