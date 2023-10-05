<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectMemberRequest;
use App\Models\ProjectMember;
use Illuminate\Support\Facades\Validator;

class ProjectMemberController extends Controller
{
    public function index($project_id)
    {
        $projectMembers = ProjectMember::with('user')->where('project_id', $project_id)->get();

        return response()->json([
            'message' => 'Project members retrieved successfully',
            'data' => $projectMembers,
        ]);
    }

    public function store(ProjectMemberRequest $request)
    {
        $projectMember = new ProjectMember;

        $projectMember->project_id = $request->project_id;
        $projectMember->user_id = $request->user_id;
        $projectMember->type = $request->type;
        $projectMember->status = $request->status;

        $projectMember->save();

        return response()->json([
            'message' => 'Project member created successfully',
            'data' => $projectMember,
        ]);
    }

    public function update(ProjectMemberRequest $request, ProjectMember $projectMember)
    {
        $validator = Validator::make($request->all(), $request->rules(), $request->messages());

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update the project member with the validated data
        $validatedData = $request->validated();
        $projectMember->update($validatedData);

        // Return a response with the updated project member as a resource
        return response()->json([
            'message' => 'Project member updated successfully',
            'data' => $projectMember,
        ]);
    }


    public function destroy(ProjectMember $projectMember)
    {
        $projectMember->delete();

        return response()->json(['message' => 'Project member deleted']);
    }

    //make a method to get the project members by project id and project member id
    public function getMemberByProjectIdAndMemberId($project_id, $member_id)
    {
        $projectMember = ProjectMember::where('project_id', $project_id)->where('user_id', $member_id)->first();

        //create api response
        return response()->json([
            'message' => 'Project member retrieved successfully',
            'data' => $projectMember,
        ]);
    }
}
