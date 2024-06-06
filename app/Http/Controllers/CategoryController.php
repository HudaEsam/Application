<?php
namespace App\Http\Controllers;

use Throwable;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Middleware\JwtAuthenticate;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class CategoryController extends Controller
{
    public function all(){
    $categories = Category::with('posts')->get();
    if ($categories->isEmpty()) {
        return response()->json([
            'message' => 'No categories found.',
        ], 404);
    }

    return CategoryResource::collection($categories);
    }
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return Response::json([
                'error' => 'unauthorized',
                'message' => 'You are not authorized to access this resource. Please login or check your credentials.',
                'data' => [],
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255|unique:categories,name",
            "description" => "required|string",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors(),
                'status_code' => 301,
            ], 301);
        }

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Category created successfully.',
            'data' => $category,
            'status_code' => 201,
        ], 201);
    }

public function show($id)
{
    $category = Category::find($id);

    if (!$category) {
        return response()->json([
            'message' => 'Category not found.',
        ], 404);
    }

    return new CategoryResource($category);

}


    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255|unique:categories,name',
        'description' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    $category = Category::find($id);
    if (!$category) {
        return response()->json([
            'message' => 'Category not found.',
        ], 404);
    }
    $category->update($request->only('name', 'description'));
    $category->save();
     return response()->json([
        'message' => 'Category updated successfully.',
        'data' => $category,
        'status_code' => 200,
    ], 200);
}

public function delete($id)
{
    $category = Category::find($id);
    if (!$category) {
        return response()->json([
            'message' => 'Category not found.',
        ], 404);
    }

    $category->delete();
      return response()->json([
        'message' => 'Category deleted successfully.',
        'status_code' => 200,
    ], 200);
}
}
