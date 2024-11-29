<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\Request;

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

    public function getPost()
    {
        $post = Post::get();

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $post,
        ]);
    }

    // Update an existing post
    public function updatePost(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }

        $validatedData = $request->validate([
            'image' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
            'student_id' => 'nullable|exists:students,id',
        ]);

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
