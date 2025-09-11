<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

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
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Validation failed.',
                    'content' => null,
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!Auth::attempt($validator->validated())) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Invalid credentials.',
                    'content' => null,
                    'errors' => []
                ], 401);
            }

            if (!Auth::check()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Authentication failed.',
                    'content' => null,
                    'errors' => []
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('api_token')->plainTextToken;

            $cookie = cookie(
                'access_token',
                $token,
                60 * 24,
                '/',
                null,
                false,
                true
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful.',
                'content' => [
                    'user' => $user,
                ],
                'errors' => []
            ])->cookie($cookie);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Validation failed.',
                'content' => null,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error.',
                'content' => null,
                'errors' => [$e->getMessage()]
            ], 500);
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
    public function destroy(Request $request)
    {
        try {
            $token = $request->cookie('access_token');
            if ($token) {
                $user = PersonalAccessToken::findToken($token);
                $user?->delete();
            }
            $cookie = cookie('access_token', '', -1, '/', null, false, true);
            return response()->json([
                'status' => 'success',
                'message' => 'Logout successful.',
                'content' => null,
                'errors' => []
            ])->cookie($cookie);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error.',
                'content' => null,
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Verify the provided token.
     **/
    public function verifyToken(Request $request)
    {
        $token = $request->input('token') ?? $request->cookie('access_token');

        if (!$token) {
            return response()->json([
                'status' => 'fail',
                'content' => null,
                'message' => 'Token not provided.',
                'errors' => []
            ], 400);
        }

        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

        if ($accessToken && $accessToken->tokenable) {
            return response()->json([
                'status' => 'success',
                'content' => null,
                'message' => 'Token Verified!',
                'errors' => []
            ]);
        }

        return response()->json([
            'status' => 'fail',
            'content' => null,
            'message' => 'Invalid token.',
            'errors' => []
        ], 401);
    }
}
