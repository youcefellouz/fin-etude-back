<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Article;
use App\Models\OrderStationStock;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()->with('articles')->get();
        return response()->json($orders);
    }

    // create order
    public function store(StoreOrderRequest $request)
    {
        $user = auth('sanctum')->user();

        // check if the stock is available
        foreach ($request->articles as $articleData) {
            $totalAvailable = Stock::where('article_id', $articleData['article_id'])
                ->sum('quantity');

            if ($totalAvailable < $articleData['quantity']) {
                $article = Article::find($articleData['article_id']);
                return response()->json([
                    'message' => "Le stock n'est pas disponible pour '{$article->name}'. Disponible : {$totalAvailable}, Requis : {$articleData['quantity']}",
                ], 400);
            }
        }

        // create order
        $order = Order::create([
            'user_id'        => $user?->id,
            'guest_name'     => $user ? null : $request->guest_name,
            'guest_phone'    => $user ? null : $request->guest_phone,
            'city'           => $request->city,
            'address'        => $request->address,
            'payment_method' => $request->payment_method,
            'status'         => 'pending',
            'global_price'   => 0,
        ]);

        $syncData = [];

        // distribute the quantity to the branches automatically for each product
        foreach ($request->articles as $articleData) {
            $article      = Article::findOrFail($articleData['article_id']);
            $remainingQty = $articleData['quantity'];

            // get the stocks sorted by quantity in descending order
            $stocks = Stock::where('article_id', $article->id)
                ->where('quantity', '>', 0)
                ->orderByDesc('quantity')
                ->get();

            foreach ($stocks as $stock) {
                if ($remainingQty <= 0) break;

                $takeQty = min($stock->quantity, $remainingQty);

                // decrement the stock
                $stock->decrement('quantity', $takeQty);

                // create order station stock
                OrderStationStock::create([
                    'order_id'   => $order->id,
                    'article_id' => $article->id,
                    'station_id' => $stock->station_id,
                    'quantity'   => $takeQty,
                ]);

                $remainingQty -= $takeQty;
            }

            $syncData[$article->id] = [
                'quantity'   => $articleData['quantity'],
                'unit_price' => $article->price_after_discount,
            ];
        }

        // link the products to the order and calculate the price
        $order->articles()->sync($syncData);
        $order->update(['global_price' => $order->calculateGlobalPrice()]);

        return response()->json($order->load('articles'), 201);
    }

    // update order
    public function update(UpdateOrderRequest $request, $id)
    {
        $order = Order::findOrFail($id);
        $user  = auth('sanctum')->user();

        if ($user && $order->user_id !== $user->id) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à modifier cette commande'], 403);
        }

        $order->update($request->only(['guest_name', 'guest_phone', 'city', 'address', 'payment_method', 'status']));

        if ($request->has('articles')) {

            // restore the stock
            $this->restoreStock($order);

            // check the stock
            foreach ($request->articles as $articleData) {
                $totalAvailable = Stock::where('article_id', $articleData['article_id'])->sum('quantity');

                if ($totalAvailable < $articleData['quantity']) {
                    $article = Article::find($articleData['article_id']);
                    return response()->json([
                        'message' => "Le stock n'est pas disponible pour '{$article->name}'. Disponible : {$totalAvailable}, Requis : {$articleData['quantity']}",
                    ], 400);
                }
            }

            // distribute the quantity to the branches automatically for each product
            $syncData = [];
            foreach ($request->articles as $articleData) {
                $article      = Article::findOrFail($articleData['article_id']);
                $remainingQty = $articleData['quantity'];

                $stocks = Stock::where('article_id', $article->id)
                    ->where('quantity', '>', 0)
                    ->orderByDesc('quantity')
                    ->get();

                foreach ($stocks as $stock) {
                    if ($remainingQty <= 0) break;

                    $takeQty = min($stock->quantity, $remainingQty);
                    $stock->decrement('quantity', $takeQty);

                    OrderStationStock::create([
                        'order_id'   => $order->id,
                        'article_id' => $article->id,
                        'station_id' => $stock->station_id,
                        'quantity'   => $takeQty,
                    ]);

                    $remainingQty -= $takeQty;
                }

                $syncData[$article->id] = [
                    'quantity'   => $articleData['quantity'],
                    'unit_price' => $article->price_after_discount,
                ];
            }

            $order->articles()->sync($syncData);
            $order->update(['global_price' => $order->calculateGlobalPrice()]);
        }

        return response()->json($order->load('articles'), 202);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $this->restoreStock($order);
        $order->delete();
        return response()->json(null, 204);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order->load('articles'), 200);
    }

    public function get_order_articles($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order->articles, 200);
    }

    public function getallorders()
    {
        $orders = Order::with('articles')->get();
        return response()->json($orders, 200);
    }

    // restore the stock
    private function restoreStock(Order $order)
    {
        $distributions = OrderStationStock::where('order_id', $order->id)->get();

        foreach ($distributions as $dist) {
            Stock::where('article_id', $dist->article_id)
                ->where('station_id', $dist->station_id)
                ->increment('quantity', $dist->quantity);
        }

        OrderStationStock::where('order_id', $order->id)->delete();
    }

    public function pay(Request $request, $id)
{
    $order = Order::findOrFail($id);
    $user  = auth('sanctum')->user();

    if ($order->user_id) {
        if (!$user || $order->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
    } else {
        if (!$request->guest_phone || $order->guest_phone !== $request->guest_phone) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
    }

    if ($order->status !== 'pending') {
        return response()->json(['message' => 'Cette commande ne peut pas être payée'], 422);
    }

    $order->update(['status' => 'confirmed']);
    $order->load('articles');

    // ← try/catch حتى لا يوقف الـ response عند فشل البريد
    try {
        $email = $user?->email ?? null;
        if ($email) {
            \Mail::to($email)->send(new \App\Mail\OrderConfirmedMail($order));
        }
    } catch (\Exception $e) {
        \Log::error('Mail error: ' . $e->getMessage());
    }

    return response()->json([
        'message' => 'Paiement effectué avec succès',
        'order'   => $order
    ], 200);
}
}