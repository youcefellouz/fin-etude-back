<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateStockRequest;


class StockController extends Controller
{
    public function index()
{
    $stocks = Stock::with(['article', 'station'])->get();
    return response()->json($stocks);
}
     public function store(StoreStockRequest $request){ 
       $stock=Stock::create($request->validated()); 
       return response()->json($stock,201); 
 }
 public function update(UpdateStockRequest $request, $id ){ 
       $stock=Stock::findOrFail($id); 
       $stock->update($request->validated()); 
       return response()->json($stock,202); 
  }
   public function destroy($id){ 
       $stock=Stock::findOrFail($id);
      $stock->delete();
        return response()->json(null,204); 
    }
     public function show ($id){ 
        $stock=Stock::findOrFail($id);
        return response()->json($stock,200); 
    }
}
