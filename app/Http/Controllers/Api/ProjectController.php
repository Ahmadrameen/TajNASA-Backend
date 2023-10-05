<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage; // Import the Storage facade

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('photos')->get();

        return response()->json([
            'status' => true,
            'message' => 'Projects Fetched Successfully',
            'data' => $projects
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'content' => 'required',
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Add photo validation rules
        ]);

        $project = Project::create($validatedData);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
            $project->photos()->create(['url' => $photoPath]);
        }

        return response()->json([
            'message' => 'Project created successfully',
            'data' => new ProjectResource($project),
        ], Response::HTTP_CREATED);
    }

    public function show(Project $project)
    {
        return response()->json([
            'status' => true,
            'message' => 'Project Fetched Successfully',
            'data' => $project->load('photos'),
        ], 200);
    }

    public function update(Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'content' => 'required',
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Add photo validation rules
        ]);

        $project->update($validatedData);
        // Check if a new photo has been uploaded
        if ($request->hasFile('photo')) {
            // Delete the old photo file and record
            if ($project->photos()->exists()) {
                $oldPhoto = $project->photos()->first();
                Storage::disk('public')->delete($oldPhoto->url);
                $oldPhoto->delete();
            }

            // Upload the new photo
            $photoPath = $request->file('photo')->store('photos', 'public');
            $project->photos()->create(['url' => $photoPath]);
        }

        return response()->json([
            'message' => 'Project updated successfully',
            'data' => new ProjectResource($project),
        ]);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        if ($project->photos()->exists()) {
            $oldPhoto = $project->photos()->first();
            Storage::disk('public')->delete($oldPhoto->url);
            $oldPhoto->delete();
        }

        return response()->json([
            'message' => 'Project deleted successfully',
        ]);
    }
}
