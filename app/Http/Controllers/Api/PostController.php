<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        if (isset($_GET['q'])) {
            $search_query = $_GET['q'];
            $forum_id = $_GET['f'];
            $user_id = auth()->user()->id;

            $posts = Post::where('content', 'LIKE', '%' . $search_query . '%')
                ->where('forum_id', $forum_id)
                ->whereUserIsMemberOfProject($user_id)
                ->paginate(10);
        } else {
            $posts = Post::paginate(10);
        }

        return response()->json(['message' => 'Successfully completed', 'posts' => $posts], 200);
    }

    public function show(Post $post)
    {
        return response()->json(['message' => 'Successfully completed', 'post' => $post], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => 'required',
            'user_id' => 'required',
            'forum_id' => 'required'
        ]);

        Post::create($data);
        return response()->json(['message' => 'Successfully completed'], 201);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'content' => 'required',
            'user_id' => 'required',
            'forum_id' => 'required'
        ]);

        $post->update($data);
        return response()->json(['message' => 'Successfully completed'], 200);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['message' => 'Successfully completed'], 200);
    }
}
