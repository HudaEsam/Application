<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Contracts\Providers\Auth;

class PostController extends Controller
{
    public function allPosts()
    {
        $posts = Post::with('user', 'category')->get();

        if ($posts->isEmpty()) {
            return Response::json([
                'message' => 'No posts found.',
            ], 404);
        }
        return PostResource::collection($posts);
        // return Response::json([
        //     'data' => $posts,
        //     'status_code' => 200,
        // ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:png,jpg,jpeg',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        try {
            $user = JWTAuth::user();

            $post = Post::create([
                'category_id' => $request->category_id,
                'title' => $request->title,
                'content' => $request->content,
                'user_id' => $user->id,
            ]);

            if ($request->hasFile('image')) {
                $imageName = Storage::putFile('posts', $request->image);
                $post->image = $imageName;
                $post->save();
            }

            $post->load('user', 'category');

            return Response::json([
                'message' => 'Post created successfully.',
                'data' => $post,
                'status_code' => 201,
            ], 201);
        }

        catch (\Tymon\JWTAuth\Exceptions\JWTException $exception) {
            return Response::json([
                'message' => 'Invalid access token or user not found.',
                'status_code' => 401,
            ], 401);
        } catch (Throwable $exception) {
            return Response::json([
                'message' => 'An error occurred while creating a post.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            $post = Post::with('user', 'category')->find($id);

            if (!$post) {
                return Response::json([
                    'message' => 'Post not found.',
                ], 404);
            }

            return new PostResource($post);

        } catch (Throwable $exception) {
            return Response::json([
                'message' => 'An error occurred while fetching the post.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
    public function update(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:png,jpg,jpeg',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $user = JWTAuth::user();
        $post = Post::where('id', $id)->where('user_id', $user->id)->first();

        if (!$post) {
            return Response::json([
                'message' => 'Unauthorized or post not found.',
                'status_code' => 403,
            ], 403);
        }

        $post->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'content' => $request->content,
        ]);


        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::delete($post->image);
            }
            $imageName = Storage::putFile('posts', $request->image);
            $post->image = $imageName;
        }

        $post->save();

        $post->load('user', 'category');

        return Response::json([
            'message' => 'Post updated successfully.',
            'data' => $post,
            'status_code' => 200,
        ], 200);
    } catch (JWTException $exception) {
        return Response::json([
            'message' => 'Invalid access token or user not found.',
            'status_code' => 401,
        ], 401);
    } catch (Throwable $exception) {
        return Response::json([
            'message' => 'An error occurred while updating the post.',
            'error' => $exception->getMessage(),
        ], 500);
    }
}
    public function delete( $id)
    {
        try {
            $user = JWTAuth::user();
            $post = Post::where('id', $id)->where('user_id', $user->id)->first();

            if (!$post) {
                return Response::json([
                    'message' => 'Unauthorized or post not found.',
                    'status_code' => 403,
                ], 403);
            }

            if ($post->image) {
                Storage::delete($post->image);
            }

            $post->delete();

            return Response::json([
                'message' => 'Post deleted successfully.',
                'status_code' => 200,
            ], 200);
        } catch (JWTException $exception) {
            return Response::json([
                'message' => 'Invalid access token or user not found.',
                'status_code' => 401,
            ], 401);
        } catch (Throwable $exception) {
            return Response::json([
                'message' => 'An error occurred while deleting the post.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
}


