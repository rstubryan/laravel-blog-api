<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Categories retrieved successfully.',
                'content' => $categories,
                'errors' => []
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve categories.',
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
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $validated['slug'] = Str::slug($validated['name']);
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Category::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }

            $category = Category::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Category created successfully.',
                'content' => $category,
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
            $category = Category::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Category retrieved successfully.',
                'content' => $category,
                'errors' => []
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found.',
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

            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string',
            ]);

            if (isset($validated['name'])) {
                $validated['slug'] = Str::slug($validated['name']);
                $originalSlug = $validated['slug'];
                $count = 1;
                while (Category::where('slug', $validated['slug'])->where('id', '!=', $category->id)->exists()) {
                    $validated['slug'] = $originalSlug . '-' . $count++;
                }
            }

            $category->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Category updated successfully.',
                'content' => $category,
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

            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Category deleted successfully.',
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
