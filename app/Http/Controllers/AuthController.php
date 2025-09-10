<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if (!Auth::attempt($validator->validated())) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            if (!Auth::check()) {
                return response()->json(['message' => 'Authentication failed'], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('api_token')->plainTextToken;

            $cookie = cookie(
                'access_token',
                $token,
                60 * 24,
                null,
                null,
                false,
                true
            );

            return response()->json([
                'user' => $user,
                'token_type' => 'Bearer',
            ])->cookie($cookie);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
