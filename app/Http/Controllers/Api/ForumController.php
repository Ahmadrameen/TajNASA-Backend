<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index()
    {
        if (isset($_GET['q'])) {
            $search_query = $_GET['q'];
            $project_id = $_GET['p'];

            $user_id = auth()->user()->id;

            $forums = Forum::where('name', 'LIKE', '%' . $search_query . '%')
                ->where('project_id', $project_id)
                ->whereUserIsMember($user_id)
                ->paginate(10);
        } else {
            $forums = Forum::paginate(10);
        }

        return response()->json(['message' => 'Forums fetched!', 'data' => $forums], 200);
    }

    public function show(Forum $forum)
    {
        return response()->json(['message' => 'Forum updated!', 'data' => $forum], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required',
            'project_id' => 'required'
        ]);

        Forum::create($data);
        return response()->json(['message' => 'Forum saved!'], 201);
    }

    public function update(Request $request, Forum $forum)
    {
        $data = $request->validate([
            'name'       => 'required',
            'project_id' => 'required'
        ]);

        $forum->update($data);
        return response()->json(['message' => 'Forum updated!'], 200);
    }

    public function destroy(Forum $forum)
    {
        $forum->delete();
        return response()->json(['message' => 'Forum deleted!'], 200);
    }
}
