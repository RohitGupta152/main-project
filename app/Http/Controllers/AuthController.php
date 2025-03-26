<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Validation\ValidationException;



class AuthController extends Controller
{
    public function registerUser(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (!$user) {
            return response()->json([
                'message' => 'User registration failed',
            ], 400);
        }

        // Mail::to($user->email)->send(new WelcomeMail($user));

        return response()->json([
            'message' => 'User registered successfully. A welcome email has been sent!',
            'user' => $user,
        ], 201);
    }

    public function loginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function logoutUser(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ], 200);
    }

    public function getUser(Request $request): JsonResponse
    {
        // Retrieve and return authenticated user's information
        return response()->json(['user' => $request->user()], 200);
    }

    public function updateUser(Request $request): JsonResponse
    {
        // Ensure the user is authenticated
        $user = $request->user();

        // Validate user input for updating profile (if needed)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'wallet_amount' => 'nullable|numeric|min:0',
        ]);

        // Update user information
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'wallet_amount' => $request->wallet_amount ?? $user->wallet_amount,
        ]);

        return response()->json([
            'message' => 'User profile updated successfully',
            'user' => $user,
        ], 200);
    }

    public function test()
    {

        $items = User::all();
        dd($items);
    }
}
