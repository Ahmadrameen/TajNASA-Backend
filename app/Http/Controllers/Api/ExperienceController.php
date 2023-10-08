<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index()
    {
        $experiences = Experience::all();
        return response()->json(['message' => 'Experience fetched', 'data' => $experiences], 200);
    }

    public function show(Experience $experience)
    {
        return response()->json(['message' => 'Experience fetched', 'data' => $experience], 200);
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

        Experience::create($data);

        return response()->json(['message' => 'Experience record created'], 201);
    }

    public function update(Request $request, Experience $experience)
    {
        $data = $request->validate([
            'user_id' => 'integer',
            'name' => 'string',
            'description' => 'nullable|string',
            'from' => 'date',
            'to' => 'nullable|date',
        ]);

        $experience->update($data);

        return response()->json(['message' => 'Experience record updated']);
    }

    public function destroy(Experience $experience)
    {
        $experience->delete();

        return response()->json(['message' => 'Experience record deleted']);
    }
}
