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

        if (isset($validatedData['image'])) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $timestamp = now()->timestamp;
            $extension = $image->getClientOriginalExtension();
            $newName = 'student' . '_' . $timestamp . '.' . $extension;
            $imagePath = $image->storeAs('students', $newName, 'public');
            $validatedData['image'] = $newName;
        } else {
            $validatedData['image'] = "default.jpg";
        }

        $student = Student::create($validatedData);
        if ($student) {

            return response()->json([
                'status' => 200,
                'message' => 'Alumni registered successfully',
                'data' => $student,
            ], 201);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to register alumni',
            ], 500);
        }
    }

    // Retrieve all alumni
    public function getAlumni(Request $request)
    {
        $query = Student::query();
        if ($request->searchTerm) {
            $query->where('name', 'like', '%' . $request->searchTerm . '%');
        }
        $query->orderBy("id", 'DESC');
        return response()->json([
            'success' => true,
            'data' => $query->get(),
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
