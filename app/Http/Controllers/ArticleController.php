<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;

class ArticleController extends Controller
{
    public function index(){ 
        $articles=Article::all();
        return response()->json($articles);
    }

    public function store(StoreArticleRequest $request){ 
       $article=Article::create($request->validated());  
        if($request->hasFile('image')){
          $path=$request->file('image')->store('articles','public');
          $article['image']=$path;
       }
        return response()->json($article,201);
 }
    public function update(UpdateArticleRequest $request, $id ){ 
       $article=Article::findOrFail($id); 
       $article->update($request->validated()); 
       return response()->json($article,202); 
  }
    public function destroy($id){ 
       $article=Article::findOrFail($id);
      $article->delete();
        return response()->json(null,204); 
    }
    public function show ($id){ 
        $article=Article::findOrFail($id);
        return response()->json($article,200); 
    }
    
public function add_discount_to_article(Request $request, $id)
{
    $request->validate([
        'discount_id' => 'required|exists:discounts,id'
    ]);

    $article = Article::findOrFail($id);
    
    if ($article->discounts()->where('discount_id', $request->discount_id)->exists()) {
        return response()->json([
            'message' => 'This discount is already applied to this article'
        ], 409);
    }

    $article->discounts()->attach($request->discount_id);

    return response()->json(
        $article->load('discounts'),
        200
    );
}

    function get_article_discounts($id){
        $article=Article::findOrFail($id);
        $discounts=$article->discounts;
        return response()->json($discounts,200); 
    }
   /* function add_order_to_article(Request $request, $id){
        $article=Article::findOrFail($id);
        $article->orders()->attach($request->order_id);
        return response()->json($article,200); 
    }*/
    function get_article_orders($id){
        $article=Article::findOrFail($id);
        $orders=$article->orders;
        return response()->json($orders,200); 
    }
    function add_station_to_article(Request $request, $id) {
    $article = Article::findOrFail($id);
    $article->stations()->attach($request->station_id, [
        'quantity' => $request->quantity ?? 1
    ]);
    return response()->json($article->load('stations'), 200); 
}
    function get_article_stations($articleid){
        $article=Article::findOrFail($articleid);
        $stations=$article->stations;
        return response()->json($stations,200); 
    }
    

}
