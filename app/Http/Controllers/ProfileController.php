<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;
        $profile = Profile::where('user_id', $user_id)->first();

        if (!$profile) {
            return response()->json(['message' => 'Aucun profil trouvé'], 404);
        }

        return response()->json($profile);
    }

    public function store(StoreProfileRequest $request)
    {
        $user_id = Auth::user()->id;
        $validatedData = $request->validated();
        $validatedData['user_id'] = $user_id;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profiles', 'public');
            $validatedData['image'] = $path;
        }

        $profile = Profile::create($validatedData);
        return response()->json($profile, 201);
    }

    public function update(UpdateProfileRequest $request, $id)
    {
        $user_id = Auth::user()->id;
        $profile = Profile::findOrFail($id);

        if ($profile->user_id != $user_id)
            return response()->json(['message' => 'Non autorisé'], 403);

        $data = $request->validated();

        // ── Gestion de l'image ──────────────────────
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($profile->image) {
                Storage::disk('public')->delete($profile->image);
            }
            $data['image'] = $request->file('image')->store('profiles', 'public');
        }

        $profile->update($data);
        return response()->json($profile, 200);
    }

    public function show($id)
    {
        $user_id = Auth::user()->id;
        $profile = Profile::findOrFail($id);

        if ($profile->user_id != $user_id)
            return response()->json(['message' => 'Non autorisé'], 403);

        return response()->json($profile, 200);
    }

    public function destroy($id)
    {
        $user_id = Auth::user()->id;
        $profile = Profile::findOrFail($id);

        if ($profile->user_id != $user_id)
            return response()->json(['message' => 'Non autorisé'], 403);

        if ($profile->image) {
            Storage::disk('public')->delete($profile->image);
        }

        $profile->delete();
        return response()->json(null, 204);
    }
}