<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Models\PendingUser;
use App\Mail\VerifyEmailMail;
use Carbon\Carbon;

class UserController extends Controller
{
    public function GetUser(){
    //$User_id=Auth::user()->id;
    $userData=User::with('profile')-> get();
    return UserResource::collection($userData);
}
 public function register(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    PendingUser::where('email', $request->email)->delete();

    PendingUser::create([
        'name'              => $request->name,
        'email'             => $request->email,
        'password'          => Hash::make($request->password),
        'verification_code' => $code,
        'code_expires_at'   => Carbon::now()->addMinutes(5),
    ]);

    Mail::to($request->email)->send(new VerifyEmailMail($code));

    return response()->json([
        'message' => 'Verification code sent to your email. Please verify within 5 minutes.',
    ], 200);
}

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    $user = User::where('email', $request->email)->firstOrFail();

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'User logged in successfully',
        'user' => $user,
        'token' => $token
    ], 200);
}

public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'User logged out successfully'
    ], 200);
}
public function get_profile(Request $request) {
    $user = $request->user();
    return response()->json($user);

}

public function verifyCode(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'code'  => 'required|string|size:6',
    ]);

    $pending = PendingUser::where('email', $request->email)->first();

    if (!$pending) {
        return response()->json(['message' => 'No registration request found for this email.'], 404);
    }

    if (Carbon::now()->isAfter($pending->code_expires_at)) {
        $pending->delete();
        return response()->json(['message' => 'Verification code has expired. Please register again.'], 410);
    }

    if ($pending->verification_code !== $request->code) {
        return response()->json(['message' => 'Invalid verification code.'], 422);
    }

    $user = User::create([
        'name'     => $pending->name,
        'email'    => $pending->email,
        'password' => $pending->password,
        'role'     => 'client',
    ]);

    $pending->delete();

    Mail::to($user->email)->send(new WelcomeMail($user));

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Account created successfully!',
        'user'    => $user,
        'token'   => $token,
    ], 201);
}

public function resendCode(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $pending = PendingUser::where('email', $request->email)->first();

    if (!$pending) {
        return response()->json(['message' => 'No registration request found for this email.'], 404);
    }

    $newCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    $pending->update([
        'verification_code' => $newCode,
        'code_expires_at'   => Carbon::now()->addMinutes(5),
    ]);

    Mail::to($request->email)->send(new VerifyEmailMail($newCode));

    return response()->json([
        'message' => 'A new verification code has been sent to your email.',
    ], 200);
}

}