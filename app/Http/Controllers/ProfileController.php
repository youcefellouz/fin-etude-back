<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
     public function index()
{
    $user_id = Auth::user()->id;
    $profile = Profile::where('user_id', $user_id)->first();

    if (!$profile) {
        return response()->json(['message' => 'No profile found'], 404);
    }

    return response()->json($profile);
}
     public function store(StoreProfileRequest $request){ 
       $user_id=Auth::user()->id;
       $validateddata=$request->validated();
       $validateddata['user_id']=$user_id;
       if($request->hasFile('image')){
          $path=$request->file('image')->store('my image','public');
          $validateddata['image']=$path;
       }
       $profile=Profile::create($validateddata); 
       return response()->json($profile,201); 
 }
 public function update(UpdateProfileRequest $request, $id ){ 
       $user_id=Auth::user()->id;
       $profile=Profile::findOrFail($id);
       if($profile->user_id != $user_id)
            return response()->json(['message'=>'unuthorized'],403);
       $profile->update($request->validated()); 
       return response()->json($profile,202); 
  }
   public function destroy($id)
{
    $user_id = Auth::user()->id;
    $profile = Profile::findOrFail($id);

    if ($profile->user_id != $user_id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $profile->delete();
    return response()->json(null, 204);
}
      public function show ($id){ 
        $user_id=Auth::user()->id;
        $profile=Profile::findOrFail($id);
        if($profile->user_id != $user_id)
            return response()->json(['message'=>'unuthorized'],403);
        return response()->json($profile,200); 
    }
  
}
