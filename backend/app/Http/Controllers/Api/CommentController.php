<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with('author')->get();
        return response()->json([
            'status' => true,
            'comments' => $comments,
        ]);
    }

    public function show($id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['status' => false, 'message' => 'Comment not found'], 404);
        }

        return response()->json(['status' => true, 'comment' => $comment]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:forum_posts,id',
            'author_id' => 'required|exists:users,id',
            'content' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'post_id' => $request->post_id,
            'author_id' => $request->author_id,
            'content' => $request->content,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Comment created successfully',
            'data' => $comment,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['status' => false, 'message' => 'Comment not found'], 404);
        }

        if ($comment->author_id !== Auth::id() && Auth::user()->role_id !== 1) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $comment->content = $request->content;
        $comment->save();

        return response()->json(['status' => true, 'message' => 'Comment updated successfully']);
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['status' => false, 'message' => 'Comment not found'], 404);
        }

        if ($comment->author_id !== Auth::id() && Auth::user()->role_id !== 1) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['status' => true, 'message' => 'Comment deleted successfully']);
    }
}
