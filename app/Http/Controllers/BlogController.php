<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogRequest;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    // Create a new blog
    public function createBlog(BlogRequest $request)
    {
        $validatedData = $request->validated();
        if ($validatedData['image']) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $timestamp = now()->timestamp;
            $extension = $image->getClientOriginalExtension();
            $newName = 'blog' . '_' . $timestamp . '.' . $extension;
            $imagePath = $image->storeAs('blogs', $newName, 'public');
            $validatedData['image'] = $newName;
        }

        // Create the blog
        $blog = Blog::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Blog created successfully',
            'data' => $blog,
        ], 201);
    }

    // Retrieve a blog by its ID
    public function getBlog()
    {
        $blog = Blog::get();

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $blog,
        ]);
    }

    // Update an existing blog
    public function updateBlog(Request $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found',
            ], 404);
        }

        $validatedData = $request->validate([
            'image' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        // Update the blog with validated data
        $blog->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Blog updated successfully',
            'data' => $blog,
        ]);
    }

    // Delete a blog
    public function deleteBlog($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found',
            ], 404);
        }

        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog deleted successfully',
        ]);
    }
}
