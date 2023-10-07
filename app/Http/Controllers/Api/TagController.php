<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();

        return response()->json([
            'status' => true,
            'message' => 'Tags Fetched Successfully',
            'data' => $tags
        ], 200);
    }

}
