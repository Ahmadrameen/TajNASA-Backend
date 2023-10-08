<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function index()
    {
        $educations = Education::all();
        return response()->json(['message' => 'Education fetched', 'data' => $educations], 200);
    }

    public function show(Education $education)
    {
        return response()->json(['message' => 'Education single fetched', 'data' => $education], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'from' => 'required|date',
            'to' => 'nullable|date',
        ]);

        Education::create($data);

        return response()->json(['message' => 'Education record created'], 201);
    }

    public function update(Request $request, Education $education)
    {
        $data = $request->validate([
            'user_id'     => 'integer',
            'name'        => 'string',
            'description' => 'nullable|string',
            'from'        => 'date',
            'to'          => 'nullable|date',
        ]);

        $education->update($data);

        return response()->json(['message' => 'Education record updated', 'data' => $education]);
    }

    public function destroy(Education $education)
    {
        $education->delete();

        return response()->json(['message' => 'Education record deleted']);
    }
}
