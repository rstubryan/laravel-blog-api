<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = (int)$request->input('per_page', 10);
            $posts = Post::paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Posts retrieved successfully.',
                'content' => [
                    'data' => $posts->items(),
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ],
                'errors' => []
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve posts.',
                'content' => null,
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Unauthenticated.',
                    'content' => null,
                    'errors' => []
                ], 401);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'author' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
            ]);

            $validated['slug'] = Post::generateUniqueSlug($validated['title']);

            $post = Post::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Post created successfully.',
                'content' => $post,
                'errors' => []
            ], 201);
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
        try {
            $post = Post::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Post retrieved successfully.',
                'content' => $post,
                'errors' => []
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found.',
                'content' => null,
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Unauthenticated.',
                    'content' => null,
                    'errors' => []
                ], 401);
            }

            $post = Post::findOrFail($id);

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'content' => 'sometimes|required|string',
                'author' => 'sometimes|required|string|max:255',
                'category_id' => 'sometimes|required|exists:categories,id',
            ]);

            if (isset($validated['title'])) {
                $validated['slug'] = Post::generateUniqueSlug($validated['title'], $post->id);
            }

            $post->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Post updated successfully.',
                'content' => $post,
                'errors' => []
            ]);
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
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Unauthenticated.',
                    'content' => null,
                    'errors' => []
                ], 401);
            }

            $post = Post::findOrFail($id);
            $post->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Post deleted successfully.',
                'content' => null,
                'errors' => []
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error.',
                'content' => null,
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
