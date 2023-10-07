<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\ProjectTag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage; // Import the Storage facade

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('photos')->withCount('members')->paginate('10');

        return response()->json([
            'status' => true,
            'message' => 'Projects Fetched Successfully',
            'data' => $projects
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'        => 'required|max:255',
            'description' => 'required',
            'content'     => 'required',
            'tags'        => 'required|array', // Change 'tags' validation to an array
            'tags.*'      => 'string', // Validate each tag as a string
            'photo'       => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        // Create the project
        $project = Project::create($validatedData);

        // Attach tags to the project
        $tags = $request->input('tags');

        foreach ($tags as $tag) {
            ProjectTag::create(['tag_id' => $tag, 'project_id' => $project->id]);
        }

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
            $project->photos()->create(['url' => $photoPath]);
        }

        $project->members()->create([
            'user_id' => auth()->user()->id,
            'type' => 1,
            'status' => 1,
        ]);

        return response()->json([
            'message' => 'Project created successfully',
        ], Response::HTTP_CREATED);
    }


    public function show(Project $project)
    {
        // Check if the user is an admin for the project
        $isAdmin = $project->isAdmin(auth()->user()->id)->exists();

        return response()->json([
            'status' => true,
            'message' => 'Project Fetched Successfully',
            'data' => [
                'project' => $project->load('photos'),
                'is_admin' => $isAdmin,
            ],
        ], 200);
    }

    public function update(Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'name'        => 'required|max:255',
            'description' => 'required',
            'content'     => 'required',
            'tags'        => 'required|array', // Change 'tags' validation to an array
            'tags.*'      => 'string', // Validate each tag as a string
            'photo'       => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', // Add photo validation rules
        ]);

        $project->fill($validatedData);
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

        // Attach tags to the project
        $tags = $request->input('tags');

        foreach ($tags as $tag) {
            ProjectTag::firstOrCreate(['tag_id' => $tag, 'project_id' => $project->id]);
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

    public function myProjects()
    {
        $projects = auth()->user()->projects()->with('photos')->get();

        return response()->json([
            'status' => true,
            'message' => 'Projects Fetched Successfully',
            'data' => $projects
        ], 200);
    }
}
