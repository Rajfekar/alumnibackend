<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function createUser(UserRequest $request)
    {
        $validatedData = $request->validated();

        if ($validatedData['image']) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $timestamp = now()->timestamp;
            $extension = $image->getClientOriginalExtension();
            $newName = 'user' . '_' . $timestamp . '.' . $extension;
            $imagePath = $image->storeAs('users', $newName, 'public');
            $validatedData['image'] = $newName;
        }

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => $user,
        ], 201);
    }

    // Retrieve all users
    public function getUser()
    {
        $users = User::all();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    // Update user details
    public function updateUser(UserRequest $request, $id)
    {

        $validatedData = $request->validated();


        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        if ($validatedData['image']) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $timestamp = now()->timestamp;
            $extension = $image->getClientOriginalExtension();
            $newName = 'user' . '_' . $timestamp . '.' . $extension;
            $imagePath = $image->storeAs('users', $newName, 'public');
            $validatedData['image'] = $newName;
        }

        $user->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
    }

    // Delete a user
    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {

            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }
}
