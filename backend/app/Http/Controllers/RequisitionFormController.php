<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;
use App\Models\UserFile;
use Illuminate\Http\Request;

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

    $userFile = UserUpload::create([
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

public function finalizeRequisition(Request $request)
{
    // Save requisition first
    $requisition = Requisition::create([
        // your form fields...
    ]);

    // Link the uploads to the new requisition
    UserUpload::where('upload_token', $request->upload_token)
        ->update([
            'requisition_id' => $requisition->id,
            'upload_token' => null,
        ]);

    return response()->json(['message' => 'Requisition created with uploads.']);
}

}
