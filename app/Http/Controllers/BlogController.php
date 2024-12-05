<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogRequest;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{

    public function getBlogById($id)
    {
        $blog = Blog::where('id', $id)->first();

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
        $blog = Blog::with('user')->paginate(50);

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
    public function updateBlog(BlogRequest $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found',
            ], 404);
        }

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
