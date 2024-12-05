<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{

    public function findUser($email)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            return Response::json([
                'status' => 200,
                'message' => "user exist",
                'user' => $user
            ]);
        } else {
            return Response::json([
                'status' => 200,
                'message' => "user not exist",
                'user' => null
            ]);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|min:2',
            'email' => 'required|unique:users,email',
            'mobile' => 'max:12|min:5',
            'password' => 'min:6|max:32',
            'role_id' => 'max:200',
            'image' => 'nullable|url', // URL validation
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => 2,
            'mobile' => $request->mobile,
            'username' => $request->username,
            'password' => '12345678',
            'image' => null,
        ];

        try {
            if ($request->image) {
                $imageUrl = $request->image;
                $response = Http::get($imageUrl);

                if ($response->successful()) {
                    $imageContents = $response->body();
                    $contentType = $response->header('Content-Type');
                    $extension = $this->getExtensionFromMimeType($contentType);
                    if (!$extension) {
                        throw new \Exception('Unable to determine the image extension.');
                    }

                    $timestamp = now()->timestamp;
                    $newName = 'user_' . $timestamp . '.' . $extension;

                    // Store the image
                    Storage::disk('public')->put('users/' . $newName, $imageContents);

                    // Store only the image name in the database
                    $data['image'] = $newName;
                } else {
                    throw new \Exception('Failed to fetch image from URL.');
                }
            }

            $data["password"] = Hash::make($data["password"]);
            $user = User::create($data);
            if ($user) {
                return Response::json([
                    'status' => 200,
                    'message' => 'User Registered Successfully',
                    'user' => $user
                ]);
            } else {
                return Response::json([
                    'status' => 500,
                    'message' => 'Something Went Wrong!',
                    'user' => $user
                ]);
            }
        } catch (\Throwable $th) {
            Log::info("Register Error Message: " . $th->getMessage());
            return Response::json([
                'status' => 500,
                'message' => 'Something Went Wrong!',
                "error" => $th->getMessage(),
            ]);
        }
    }

    private function getExtensionFromMimeType($mimeType)
    {
        $mimeTypeMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            // Add more MIME types as needed
        ];

        return $mimeTypeMap[$mimeType] ?? null;
    }


    public function login(LoginRequest $request)
    {

        $payload = $request->validated();

        try {
            // Find the user by email
            $user = User::where("email", $payload["email"])->first();

            if ($user) {
                // Check if the provided password is correct
                if (!Hash::check($payload["password"], $user->password)) {
                    return response()->json(["status" => 401, "message" => "Invalid credentials."]);
                }

                // Determine the token name based on the user's role

                $tokenName =  'admin';

                // Create a new token if none exists
                $token = $user->createToken($tokenName)->plainTextToken;

                // Update the expiry time for the new token
                $user->tokens()->where('name', $tokenName)->update([
                    'expires_at' => now()->addHours(24),
                ]);


                // Retrieve the newly created token with the updated expiration time
                $tokenWithExpiry = $user->tokens()->where('name', $tokenName)->first(['token', 'expires_at']);

                // Format the expires_at value to 'Y-m-d H:i:s' format
                $expiresAtFormatted = $tokenWithExpiry->expires_at ? $tokenWithExpiry->expires_at->format('Y-m-d H:i:s') : null;

                $userWithToken = array_merge(
                    $user->toArray(),
                    ["token" => $token, 'expires_at' => $expiresAtFormatted]
                );

                return response()->json(["status" => 200, "user" => $userWithToken, "message" => "Logged in successfully!"]);
            }
            return response()->json(["status" => 401, "message" => "No account found with these credentials."]);
        } catch (\Exception $err) {
            Log::info("user_login_error => " . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Something went wrong!"], 500);
        }
    }





    // login with provider



    public function loginWithMail(Request $request)
    {

        // Validate the incoming request
        $request->validate([
            'email' => 'required|email',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Determine the token name based on the user's role
            $tokenName = $user->role_id == 1 ? 'admin' : 'user';

            // Create a new token if none exists
            $token = $user->createToken($tokenName)->plainTextToken;

            // Update the expiry time for the new token
            $user->tokens()->where('name', $tokenName)->update([
                'expires_at' => now()->addHours(24),
            ]);

            // Retrieve the newly created token with the updated expiration time
            $tokenWithExpiry = $user->tokens()->where('name', $tokenName)->first(['token', 'expires_at']);

            // Format the expires_at value to 'Y-m-d H:i:s' format
            $expiresAtFormatted = $tokenWithExpiry->expires_at ? $tokenWithExpiry->expires_at->format('Y-m-d H:i:s') : null;

            $userWithToken = array_merge(
                $user->toArray(),
                [
                    "token" => $token,
                    'expires_at' => $expiresAtFormatted
                ]
            );

            // Return success response with the token
            return response()->json([
                'status' => 200,
                'user' => $userWithToken,
                'message' => 'Logged in successfully!'
            ]);
        } else {
            // Return failure response if user not found
            return response()->json([
                'status' => 404,
                'user' => null,
                'message' => 'No account found with this email.'
            ]);
        }
    }


    public function logout(Request $request)
    {

        $email = $request->input('email');

        // Find the user by ID
        $user = User::where('email', $email)->first();

        if ($user) {
            // Revoke all tokens for the user
            $user->tokens()->delete();
            return response()->json(['status' => 200, 'message' => 'User logged out successfully'], 200);
        }

        return response()->json(['message' => 'User not found'], 404);
    }


    public function checkTokenExpiration(Request $request)
    {
        $email = $request->input('email');

        // Find the user by email
        $user = User::where('email', $email)->first();

        // Check if user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Retrieve the user's current access token
        $token = $user->tokens()->latest()->first(); // Fetch the latest token (or you can modify this to get a specific one)

        if (!$token) {
            return response()->json(['message' => 'Token not found'], 404);
        }

        // Check if the token has an expiration time and if it is expired
        if ($token->expires_at && Carbon::now()->greaterThan($token->expires_at)) {
            return response()->json(['isExpired' => true], 401);
        }

        return response()->json(['isExpired' => false], 200);
    }
}
