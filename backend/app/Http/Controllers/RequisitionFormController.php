<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserUpload;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;


class RequisitionFormController extends Controller
{
    public function tempUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'upload_type' => 'required|in:Letter,Room Setup',
        ]);
    
        $uploadedFile = Cloudinary::upload($request->file('file')->getRealPath(), [
            'folder' => 'requisition_uploads'
        ]);
    
        $uploadToken = $request->input('upload_token') ?? Str::uuid()->toString();
    
        $userFile = UserFile::create([
            'file_url' => $uploadedFile->getSecurePath(),
            'cloudinary_public_id' => $uploadedFile->getPublicId(),
            'upload_type' => $request->upload_type,
            'upload_token' => $uploadToken,
        ]);
    
        return response()->json([
            'upload_token' => $uploadToken,
            'user_file' => $userFile,
        ]);
    }
}