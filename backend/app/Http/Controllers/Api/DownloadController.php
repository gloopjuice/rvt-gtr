<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
class DownloadController extends Controller
{

    public function getDownloadableFiles()
    {
        $files = [];
        $downloadDir = 'downloads';
        if (!Storage::disk('public')->exists($downloadDir)) {
            Storage::disk('public')->makeDirectory($downloadDir);
        }
       
        $allFiles = Storage::disk('public')->files($downloadDir);
       
        foreach ($allFiles as $file) {
            $name = basename($file);
            $url = url('/api/download/' . $name);
            $size = Storage::disk('public')->size($file);
           
            $files[] = [
                'name' => $name,
                'url' => $url,
                'size' => $size
            ];
        }
       
        return response()->json([
            'status' => true,
            'files' => $files
        ]);
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:50000', 
        ]);
        
        if (Auth::user()->role_id !== 1) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Only admins can upload files.'
            ], 403);
        }
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $path = $file->storeAs('downloads', $fileName, 'public');
            
            if ($path) {
                return response()->json([
                    'status' => true,
                    'message' => 'File uploaded successfully',
                    'file' => [
                        'name' => $fileName,
                        'url' => Storage::disk('public')->url($path),
                        'size' => $file->getSize()
                    ]
                ]);
            }
        }
        
        return response()->json([
            'status' => false,
            'message' => 'File upload failed'
        ], 400);
    }

    public function deleteFile(Request $request)
    {
        $request->validate([
            'url' => 'required|string',
        ]);
        
        if (Auth::user()->role_id !== 1) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Only admins can delete files.'
            ], 403);
        }
        
        $url = $request->url;
        $path = parse_url($url, PHP_URL_PATH);
        $fileName = basename($path);
        $filePath = 'downloads/' . $fileName;
        
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            return response()->json([
                'status' => true,
                'message' => 'File deleted successfully'
            ]);
        }
        
        return response()->json([
            'status' => false,
            'message' => 'File not found'
        ], 404);
    }

    public function downloadFile($filename)
    {
        $filePath = 'downloads/' . $filename;
        
        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json([
                'status' => false,
                'message' => 'File not found.'
            ], 404);
        }
        
        return Storage::disk('public')->download($filePath, $filename);
    }
}