<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PostController extends Controller
{
    // Create a new post
    public function createPost(PostRequest $request)
    {
        $validatedData = $request->validated();

        if ($validatedData['image']) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $timestamp = now()->timestamp;
            $extension = $image->getClientOriginalExtension();
            $newName = 'post' . '_' . $timestamp . '.' . $extension;
            $imagePath = $image->storeAs('posts', $newName, 'public');
            $validatedData['image'] = $newName;
        }

        // Create the post
        $post = Post::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }

    public function getPost(Request $request)
    {
        $query = Post::with('student');

        if ($request->startDate && $request->endDate) {
            $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        if ($request->student) {
            $query->where('student_id', $request->student);
        }

        if ($request->orderField && $request->order) {
            $query->orderBy($request->orderField, $request->order);
        }
        if ($request->searchTerm) {
            $query->where('title', 'like', '%' . $request->searchTerm . '%');
        }

        try {

            $posts = $query->orderBy('id', 'DESC')->paginate($request->per_page ?? 5);
            return Response::json([
                'status' => 200,
                'message' => 'Student Notice Fetch Successfully.',
                'data' => $posts
            ]);
        } catch (\Throwable $th) {

            return Response::json([
                'status' => 501,
                'message' => 'Something Went Wrong!',
                'data' => $th
            ]);
        }
    }

    // Update an existing post
    public function updatePost(PostRequest $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }

        $validatedData = $request->validated();

        if ($validatedData['image']) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $timestamp = now()->timestamp;
            $extension = $image->getClientOriginalExtension();
            $newName = 'post' . '_' . $timestamp . '.' . $extension;
            $imagePath = $image->storeAs('posts', $newName, 'public');
            $validatedData['image'] = $newName;
        }
        // Update the post with validated data
        $post->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => $post,
        ]);
    }

    // Delete a post
    public function deletePost($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
        ]);
    }
}
