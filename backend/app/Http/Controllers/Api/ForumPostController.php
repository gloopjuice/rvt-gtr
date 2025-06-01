<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ForumPost;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ForumPostController extends Controller
{
    public function index()
    {
        $forumPosts = ForumPost::with('author')->orderBy('updated_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'All forum posts retrieved successfully',
            'data' => $forumPosts,
        ]);
    }

    public function show($id)
    {
        $post = ForumPost::find($id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Forum post not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Forum post found',
            'data' => $post,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:50',
            'content' => 'required|string|max:5000',
        ]);

        $date = Carbon::now();

        $forumPost = ForumPost::create([
            'title' => $request->title,
            'content' => $request->content,
            'author_id' => $user->id,
            'date' => $date,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Forum post created successfully',
            'data' => $forumPost,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $post = ForumPost::find($id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Forum post not found',
            ], 404);
        }

        if ($user->id != $post->author_id && $user->role_id != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized to edit this post',
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:50',
            'content' => 'required|string|max:5000',
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Forum post updated successfully',
            'data' => $post,
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $post = ForumPost::find($id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Forum post not found',
            ], 404);
        }

        if ($user->id != $post->author_id && $user->role_id != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized to delete this post',
            ], 403);
        }

        $post->delete();

        return response()->json([
            'status' => true,
            'message' => 'Forum post deleted successfully',
        ]);
    }
}
