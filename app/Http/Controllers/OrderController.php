<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Article;

class OrderController extends Controller
{
      public function index(){ 
        $orders=Order::all();
        return response()->json($orders);
    }
   public function store(StoreOrderRequest $request)
    {
        // إنشاء الطلب
        $order = Order::create([
            'user_id' => $request->user_id,
            'guest_name' => $request->guest_name,
            'guest_phone' => $request->guest_phone,
            'status' => $request->status ?? 'pending',
            'global_price' => 0, // سيتم تحديثه لاحقاً
        ]);

        // إضافة المقالات مع حفظ unit_price
        $syncData = [];
        foreach ($request->articles as $article) {
            $articleModel = Article::findOrFail($article['article_id']);
            
            $syncData[$article['article_id']] = [
                'quantity' => $article['quantity'] ?? 1,
                'unit_price' => $articleModel->price_after_discount, // السعر بعد الخصم
            ];
        }

        $order->articles()->sync($syncData);

        // حساب وتحديث السعر الإجمالي
        $order->update([
            'global_price' => $order->calculateGlobalPrice()
        ]);

        return response()->json(
            $order->load('articles'),
            201
        );
    }
 public function update(UpdateOrderRequest $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->update($request->only([
            'user_id', 'guest_name', 'guest_phone', 'status'
        ]));

        if ($request->has('articles')) {
            $syncData = [];
            foreach ($request->articles as $article) {
                $articleModel = Article::findOrFail($article['article_id']);
                
                $syncData[$article['article_id']] = [
                    'quantity' => $article['quantity'] ?? 1,
                    'unit_price' => $articleModel->price_after_discount,
                ];
            }
            $order->articles()->sync($syncData);
            
            $order->update([
                'global_price' => $order->calculateGlobalPrice()
            ]);
        }

        return response()->json($order->load('articles'), 202);
    }

   public function destroy($id){ 
       $order=Order::findOrFail($id);
      $order->delete();
        return response()->json(null,204); 
    }
     public function show ($id){ 
        $order=Order::findOrFail($id);
        return response()->json($order,200); 
    }

    public function add_article_to_order(Request $request, $id)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $order = Order::findOrFail($id);
        $article = Article::findOrFail($request->article_id);

        $order->articles()->syncWithoutDetaching([
            $request->article_id => [
                'quantity' => $request->quantity ?? 1,
                'unit_price' => $article->getPriceAfterDiscountAttribute, 
            ]
        ]);

        $order->update([
            'global_price' => $order->calculateGlobalPrice()
        ]);

        return response()->json($order->load('articles'), 200);
    }

     public function get_order_articles($id)
    {
        $order = Order::findOrFail($id);
        $articles = $order->articles;
        return response()->json($articles, 200);
    }
}
