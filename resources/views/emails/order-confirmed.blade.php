<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: Arial, sans-serif; color: #0F172A; background: #f5f5f7; margin: 0; padding: 0; }
    .container { max-width: 560px; margin: 40px auto; background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
    h1 { color: #4F46E5; font-size: 1.5rem; }
    .badge { background: #D1FAE5; color: #059669; padding: 4px 12px; border-radius: 20px; font-size: .85rem; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
    th { background: #EEF2FF; color: #4F46E5; padding: .75rem; text-align: left; font-size: .85rem; }
    td { padding: .75rem; border-bottom: 1px solid #E8ECF0; font-size: .875rem; }
    .total { font-size: 1.1rem; font-weight: 800; color: #4F46E5; }
    .footer { text-align: center; color: #94A3B8; font-size: .8rem; margin-top: 2rem; }
  </style>
</head>
<body>
  <div class="container">
    <h1>🎉 Commande confirmée !</h1>
    <p>Bonjour <strong>{{ $order->user?->name ?? $order->guest_name }}</strong>,</p>
    <p>Merci pour votre commande. Vous trouverez votre facture en pièce jointe.</p>

    <p>Statut : <span class="badge">Confirmée ✅</span></p>

    <table>
      <thead>
        <tr>
          <th>Article</th>
          <th>Qté</th>
          <th>Prix unitaire</th>
          <th>Sous-total</th>
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
      </tbody>
    </table>

    <p>Livraison : <strong>Gratuite 🚚</strong></p>
    <p>Paiement :
      @if($order->payment_method === 'cash')
        <span style="background:#FEF3C7;color:#D97706;padding:4px 12px;border-radius:20px;font-size:.85rem;font-weight:700;">💵 À la livraison</span>
      @else
        <span style="background:#EEF2FF;color:#4F46E5;padding:4px 12px;border-radius:20px;font-size:.85rem;font-weight:700;">💳 Carte bancaire</span>
      @endif
    </p>
    <p class="total">Total : {{ number_format($order->global_price, 2) }} TND</p>

    <div class="footer">
      Tech Shop <br>
      +216 54 736 722 | youssef.ellouze14.5@gmail.com
    </div>
  </div>
</body>
</html>