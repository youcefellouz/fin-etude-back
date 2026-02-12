<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Station;
use App\Http\Requests\StoreStationRequest;
use App\Http\Requests\UpdateStationRequest;

class StationController extends Controller
{
     public function index(){ 
        $stations=Station::all();
        return response()->json($stations);
    }
     public function store(StoreStationRequest $request){ 
       $station=Station::create($request->validated()); 
       return response()->json($station,201); 
 }
 public function update(UpdateStationRequest $request, $id ){ 
       $station=Station::findOrFail($id); 
       $station->update($request->validated()); 
       return response()->json($station,202); 
  }
   public function destroy($id){ 
       $station=Station::findOrFail($id);
      $station->delete();
        return response()->json(null,204); 
    }
     public function show ($id){ 
        $station=Station::findOrFail($id);
        return response()->json($station,200); 
    }
    
    function get_station_articles($id){
        $station=Station::findOrFail($id);
        $articles=$station->articles;
        return response()->json($articles,200); 
    }
    
}
