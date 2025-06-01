<?php
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;

class ApiController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    
        $status = Password::sendResetLink($request->only('email'));
    
        \Log::info('Password reset status: ' . $status); 
        \Log::info('Password reset requested for: ' . $request->email); 
    
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => true, 'message' => __($status)])
            : response()->json(['status' => false, 'message' => __($status)], 400);
    }
    

public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? response()->json(['status' => true, 'message' => __($status)])
        : response()->json(['status' => false, 'message' => __($status)], 400);
}

    public function register(Request $request){
        $request->validate([
            "username" => "required",
            "name"=> "required",
            "email"=> "required|email|unique:users",
            "password"=>"required|confirmed",
        ]);

        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status'=> true,
            'message'=>'User created successfully',
        ]);
    }

    public function login(Request $request){
        $request->validate([
            "email"=> "required|email",
            "password"=>"required",
        ]);

        if(Auth::attempt([
            "email"=> $request->email,
            "password"=>$request->password
        ])){

            $user = Auth::user();

            $token = $user->createToken("myToken")->accessToken;

            return response()->json([
                'status'=> true,
                'message'=>'User logged in successfully',
                'token'=> $token,
                'name'=> $user->name
            ]);

        }else{
            return response()->json([
                'status'=> false,
                'message'=>'Invalid login details',
            ]);
        }
    }

    public function profile(){
        $user = Auth::user();

        return response()->json([
            'status' => true,
            'message' => 'User profile',
            'data' => $user
        ]);
    }

    public function logout(){

        auth()->user()->token()->revoke();

        return response()->json([
            'status' => true,
            'message' => 'User logged out'
        ]);
    }

    public function uploadFile(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $isDownloadable = $request->boolean('isDownloadable', false);
        
        if ($isDownloadable) {
            $path = $request->file('file')->store('downloads', 'public');
        } else {
            $path = $request->file('file')->store('uploads', 'public');
        }

        $url = Storage::url($path);

        return response()->json([
            'status' => true,
            'message' => 'File uploaded successfully',
            'url' => $url,
        ]);
    }

    public function deleteFile(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'url' => 'required|string',
        ]);

        $urlPath = parse_url($request->url, PHP_URL_PATH);
        $relativePath = str_replace('/storage/', '', $urlPath);
        
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);

            return response()->json([
                'status' => true,
                'message' => 'File deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'File not found',
            ], 404);
        }
    }




}
