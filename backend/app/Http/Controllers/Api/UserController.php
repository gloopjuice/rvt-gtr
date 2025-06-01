<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json([
            'status' => true,
            'users' => $users,
        ]);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'user' => $user,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'User profile updated successfully',
            'data' => $user,
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User profile deleted successfully',
        ]);
    }

    public function getUserProfile(Request $request)
{
    $id = $request->query('id');
    if ($id) {
        $userData = User::find($id);
        if (!$userData) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
                'userData' => null
            ], 404);
        }
        return response()->json([
            'status' => true,
            'userData' => $userData
        ]);
    }
    $userData = Auth::user();
    return response()->json([
        'status' => true,
        'userData' => $userData
    ]);
}

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $user->update([
            'bio' => $request->input('bio'),
            'username' => $request->input('username')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
        ]);
    }

    public function deleteProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $user->delete();
        auth()->user()->token()->revoke();

        return response()->json([
            'status' => true,
            'message' => 'Profile deleted',
        ]);
    }
}
