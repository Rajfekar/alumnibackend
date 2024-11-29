<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{
    // Create a new image
    public function createImage(ImageRequest $request)
    {
        $validatedData = $request->validated();

        if ($validatedData['image']) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $timestamp = now()->timestamp;
            $extension = $image->getClientOriginalExtension();
            $newName = 'img' . '_' . $timestamp . '.' . $extension;

            $imagePath = $image->storeAs('images', $newName, 'public');
            $validatedData['name'] = $newName;
        }


        $image = Image::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Image created successfully',
            'data' => $image,
        ], 201);
    }

    // Retrieve all images
    public function getImages()
    {
        $images = Image::all();

        return response()->json([
            'success' => true,
            'data' => $images,
        ]);
    }

    // Update image details
    public function updateImage(ImageRequest $request, $id)
    {
        $validatedData = $request->validated();
        $image = Image::find($id);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found',
            ], 404);
        }

        // Handle file replacement if new file is provided
        if ($request->hasFile('image')) {
            // Delete old file if exists
            if ($image->url) {
                Storage::disk('public')->delete($image->url);
            }

            $file = $request->file('image');
            $path = $file->store('images', 'public'); // Save in 'storage/app/public/images'
            $validatedData['url'] = $path;
        }

        $image->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Image updated successfully',
            'data' => $image,
        ]);
    }

    // Delete an image
    public function deleteImage($id)
    {
        // Find the image by ID
        $image = Image::find($id);

        if (!$image) {

            return response()->json([
                'success' => false,
                'message' => 'Image not found',
            ], 404);
        }


        // Define the correct path to the image
        $imagePath = 'images/' . $image->name;

        // Use the 'public' disk to check and delete the file
        if (Storage::disk('public')->exists($imagePath)) {
            // Delete the file from storage
            Storage::disk('public')->delete($imagePath);

            // Delete the database record
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully',
            ]);
        }

        // If file doesn't exist
        return response()->json([
            'success' => false,
            'message' => 'File not found in storage',
        ], 404);
    }
}
