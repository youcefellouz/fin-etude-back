<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use App\Http\Requests\StoreDiscountRequest;
use App\Http\Requests\UpdateDiscountRequest;

class DiscountController extends Controller
{
      public function index(){ 
        $discounts=Discount::all();
        return response()->json($discounts);
    }
     public function store(StoreDiscountRequest $request){ 
       $discount=Discount::create($request->validated()); 
       return response()->json($discount,201); 
 }
 public function update(UpdateDiscountRequest $request, $id ){ 
       $discount=Discount::findOrFail($id); 
       $discount->update($request->validated()); 
       return response()->json($discount,202); 
  }
   public function destroy($id){ 
       $discount=Discount::findOrFail($id);
      $discount->delete();
        return response()->json(null,204); 
    }
     public function show ($id){ 
        $discount=Discount::findOrFail($id);
        return response()->json($discount,200); 
    }
    public function add_article_to_discount(Request $request, $id)
{
    $request->validate([
        'article_id' => 'required|exists:articles,id',
    ]);

    $discount = Discount::findOrFail($id);
    
    if ($discount->articles()->where('article_id', $request->article_id)->exists()) {
        return response()->json([
            'message' => 'Cet article est déjà lié à cette remise'
        ], 409);
    }

    $discount->articles()->attach($request->article_id);

    return response()->json($discount, 200);
}
    function get_discount_articles($id){
        $discount=Discount::findOrFail($id);
        $articles=$discount->articles;
        return response()->json($articles,200); 
    } 
}
