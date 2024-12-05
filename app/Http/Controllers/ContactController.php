<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Models\Message;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function createContact(MessageRequest $request)
    {
        $validatedData = $request->validated();
        // Create the blog
        $message = Message::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Contact Message Send successfully',
            'data' => $message,
        ], 201);
    }

    public function getContact(Request $request)
    {
        $message = Message::paginate(50);

        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'message not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $message,
        ]);
    }

    public function deleteContact($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found',
            ], 404);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully',
        ]);
    }
}
