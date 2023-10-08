<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage; // Import the Storage facade

class ProjectController extends Controller
{
    public function index()
    {
        if (isset($_GET['q'])) {
            $search = $_GET['q'];
            $projects = Project::where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->with('photos')
                ->withCount('members')
                ->with('tags')
                ->paginate(10);
        } else {
            $projects = Project::with('photos')->withCount('members')->with('tags')->paginate('10');
        }

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
            'tags'        => 'required',
            'tags.*'      => 'string',
            'photo'       => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $project = Project::create($validatedData);

        $tags = json_decode($request->input('tags'));

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
        $isAdmin = $project->isAdmin(auth()->user()->id)->exists();

        return response()->json([
            'status' => true,
            'message' => 'Project Fetched Successfully',
            'data' => [
                'project' => $project->load('photos')->load('tags')->load('members.user'),
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
            'tags'        => 'required',
            'tags.*'      => 'string',
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
        $tags = json_decode($request->input('tags'));

        foreach ($tags as $tag) {
            ProjectTag::firstOrCreate(['tag_id' => $tag, 'project_id' => $project->id]);
        }

        return response()->json([
            'message' => 'Project updated successfully',
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
        $user = auth()->user();
        $projects = Project::with('photos')->withCount('members')->with('tags')->whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->paginate('10');

        return response()->json([
            'status' => true,
            'message' => 'Projects Fetched Successfully',
            'data' => $projects
        ], 200);
    }
}
