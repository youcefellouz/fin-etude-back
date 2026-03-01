<?php

namespace App\Http\Controllers;

use App\Models\RepairRequest;
use App\Models\Station;
use App\Http\Requests\StoreRepairRequest;
use App\Http\Requests\UpdateRepairRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepairRequestController extends Controller
{
    public function index()
    {
        $requests = RepairRequest::where('user_id', Auth::id())
            ->with(['station', 'article'])
            ->latest()
            ->get();

        return response()->json($requests);
    }

    public function store(StoreRepairRequest $request)
    {
        $station = Station::findOrFail($request->station_id);

        if ($station->type !== 'technique') {
            return response()->json([
                'message' => 'This station is not a technical center.'
            ], 422);
        }

        $repairRequest = RepairRequest::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
            'status'  => 'en_attente',
        ]);

        return response()->json($repairRequest->load(['station', 'article']), 201);
    }

    public function show($id)
    {
        $repairRequest = RepairRequest::where('user_id', Auth::id())
            ->with(['station', 'article'])
            ->findOrFail($id);

        return response()->json($repairRequest);
    }

    public function destroy($id)
    {
        $repairRequest = RepairRequest::where('user_id', Auth::id())
            ->findOrFail($id);

        if ($repairRequest->status !== 'en_attente') {
            return response()->json([
                'message' => 'Cannot cancel a request that is already in progress.'
            ], 422);
        }

        $repairRequest->delete();
        return response()->json(null, 204);
    }

    public function adminIndex()
    {
        $requests = RepairRequest::with(['user', 'station', 'article'])
            ->latest()
            ->get();

        return response()->json($requests);
    }

    public function adminUpdate(UpdateRepairRequest $request, $id)
    {
        $repairRequest = RepairRequest::findOrFail($id);
        $repairRequest->update($request->validated());

        return response()->json($repairRequest->load(['user', 'station', 'article']));
    }

    public function technicalStations()
    {
        $stations = Station::where('type', 'technique')
            ->where('status', 'active')
            ->get();

        return response()->json($stations);
    }
}