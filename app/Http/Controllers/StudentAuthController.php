<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentLoginRequest;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class StudentAuthController extends Controller
{

    public function loginStudent(StudentLoginRequest $request)
    {
        // Validate input
        $validatedData = $request->validated();

        // Find student by email
        $student = Student::where('email', $validatedData['email'])->first();

        // Check if student exists and password is correct
        if (!$student || !Hash::check($validatedData['password'], $student->password)) {
            return response()->json([
                'login' => false,
                'message' => 'Invalid email or password.',
                'student' => null
            ], 401);
        }

        return response()->json([
            'login' => true,
            'message' => 'Login successful.',
            'student' => $student,
        ], 200);
    }
}
