<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Stock;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders=Auth::user()->orders;
        return response()->json($orders);
    }

    public function store(StoreOrderRequest $request)
{
    $stationId = $request->station_id;
    $user = auth('sanctum')->user(); 

    foreach ($request->articles as $articleData) {
        $stock = Stock::where('article_id', $articleData['article_id'])
            ->where('station_id', $stationId)
            ->first();

        $quantity = $articleData['quantity'] ?? 1;

        if (!$stock || $stock->quantity < $quantity) {
            $article = Article::find($articleData['article_id']);
            $available = $stock ? $stock->quantity : 0;

            return response()->json([
                'message' => "Stock insuffisant pour l'article '{$article->name}'. Disponible: {$available}, Demandé: {$quantity}",
            ], 400);
        }
    }

    $order = Order::create([
        'user_id' => $user?->id,
        'station_id' => $stationId,
        'guest_name' => $user ? null : $request->guest_name,
        'guest_phone' => $user ? null : $request->guest_phone,
        'status' => 'pending',
        'global_price' => 0,
    ]);

    $syncData = [];

    foreach ($request->articles as $articleData) {
        $article = Article::findOrFail($articleData['article_id']);
        $quantity = $articleData['quantity'] ?? 1;

        $syncData[$article->id] = [
            'quantity' => $quantity,
            'unit_price' => $article->price_after_discount,
        ];

        Stock::where('article_id', $article->id)
            ->where('station_id', $stationId)
            ->decrement('quantity', $quantity);
    }

    $order->articles()->sync($syncData);

    $order->update([
        'global_price' => $order->calculateGlobalPrice()
    ]);

    return response()->json(
        $order->load('articles', 'station'),
        201
    );
}


   public function update(UpdateOrderRequest $request, $id)
{
    $order = Order::findOrFail($id);
    $user = auth('sanctum')->user();

    if ($user && $order->user_id !== $user->id) {
        return response()->json(['message' => 'Non autorisé'], 403);
    }

    $order->update($request->only([
        'station_id', 'guest_name', 'guest_phone', 'status'
    ]));

    if ($request->has('articles')) {
        $stationId = $request->station_id ?? $order->station_id;

        // نرجع الـ stock القديم
        $this->restoreStock($order);

        // نتحقق من الـ stock الجديد
        foreach ($request->articles as $articleData) {
            $articleId = $articleData['article_id'];
            $quantity = $articleData['quantity'] ?? 1;

            $stock = Stock::where('article_id', $articleId)
                ->where('station_id', $stationId)
                ->first();

            if (!$stock || $stock->quantity < $quantity) {
                $article = Article::find($articleId);
                $available = $stock ? $stock->quantity : 0;

                return response()->json([
                    'message' => "Stock insuffisant pour l'article '{$article->name}'. Disponible: {$available}, Demandé: {$quantity}",
                ], 400);
            }
        }

        // نبني الـ syncData ونحط الـ stock الجديد
        $syncData = [];
        foreach ($request->articles as $articleData) {
            $article = Article::findOrFail($articleData['article_id']);
            $quantity = $articleData['quantity'] ?? 1;

            $syncData[$article->id] = [
                'quantity' => $quantity,
                'unit_price' => $article->price_after_discount,
            ];

            Stock::where('article_id', $article->id)
                ->where('station_id', $stationId)
                ->decrement('quantity', $quantity);
        }

        $order->articles()->sync($syncData);

        $order->update([
            'global_price' => $order->calculateGlobalPrice()
        ]);
    }

    return response()->json($order->load('articles', 'station'), 202);
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
        return response()->json($order->load('articles', 'station'), 200);
    }

    public function get_order_articles($id)
    {
        $order = Order::findOrFail($id);
        $articles = $order->articles;
        return response()->json($articles, 200);
    }

    private function restoreStock(Order $order)
    {
        if (!$order->station_id) {
            return;
        }

        foreach ($order->articles as $article) {
            Stock::where('article_id', $article->id)
                ->where('station_id', $order->station_id)
                ->increment('quantity', $article->pivot->quantity);
        }
    }
    public function getallorders(){
        $orders=Order::all();
        return response()->json($orders,200);
    }
}
