<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;


class BrandController extends Controller
{
     public function index(){ 
        $brands=Brand::all();
        return response()->json($brands);
    }

    public function store(StoreBrandRequest $request){ 
       $brand=Brand::create($request->validated()); 
       return response()->json($brand,201); 
 }
    public function update(UpdateBrandRequest $request, $id ){ 
       $brand=Brand::findOrFail($id); 
       $brand->update($request->validated()); 
       return response()->json($brand,202); 
  }
    public function destroy($id){ 
       $brand=Brand::findOrFail($id);
      $brand->delete();
        return response()->json(null,204); 
    }
    public function show ($id){ 
        $brand=Brand::findOrFail($id);
        return response()->json($brand,200); 
    }
}
