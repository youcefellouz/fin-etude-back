<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;

class ProfileController extends Controller
{
     public function index(){ 
        $profiles=Profile::all();
        return response()->json($profiles);
    }
     public function store(StoreProfileRequest $request){ 
       $profile=Profile::create($request->validated()); 
       return response()->json($profile,201); 
 }
 public function update(UpdateProfileRequest $request, $id ){ 
       $profile=Profile::findOrFail($id); 
       $profile->update($request->validated()); 
       return response()->json($profile,202); 
  }
   public function destroy($id){ 
       $profile=Profile::findOrFail($id);
      $profile->delete();
        return response()->json(null,204); 
    }
     public function show ($id){ 
        $profile=Profile::findOrFail($id);
        return response()->json($profile,200); 
    }
  
}
