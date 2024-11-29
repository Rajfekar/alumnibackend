<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlumniRequest;
use App\Http\Requests\StudentRequest;
use App\Models\Student;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    // Create a new alumni (register)
    public function registerAlumni(AlumniRequest $request)
    {
        $validatedData = $request->validated();

        if ($validatedData['image']) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $timestamp = now()->timestamp;
            $extension = $image->getClientOriginalExtension();
            $newName = 'student' . '_' . $timestamp . '.' . $extension;
            $imagePath = $image->storeAs('students', $newName, 'public');
            $validatedData['image'] = $newName;
        }

        $student = Student::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Alumni registered successfully',
            'data' => $student,
        ], 201);
    }

    // Retrieve all alumni
    public function getAlumni()
    {
        $students = Student::all();

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    // Update alumni details
    public function updateAlumni(StudentRequest $request, $id)
    {
        $student = Student::find($id);

        if (!$student) {

            return response()->json([
                'success' => false,
                'message' => 'Alumni not found',
            ], 404);
        }

        $validatedData = $request->validated();

        if ($request->has("image")) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $timestamp = now()->timestamp;
            $extension = $image->getClientOriginalExtension();
            $newName = 'student' . '_' . $timestamp . '.' . $extension;
            $imagePath = $image->storeAs('students', $newName, 'public');
            $validatedData['image'] = $newName;
        }

        $student->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Alumni updated successfully',
            'data' => $student,
        ]);
    }

    // Delete an alumni
    public function deleteAlumni($id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Alumni not found',
            ], 404);
        }

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alumni deleted successfully',
        ]);
    }
}
